<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');
App::uses('AccessControlFactory', 'Rbac.Auth');
App::uses('AccessControl', 'Plugin/Rbac/Lib/Auth');
App::uses('DigestAuthenticate', 'Controller/Component/Auth');
App::uses('ApiAuthValueTracker', 'Model');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'OwnerAccessControl',
		'Session',
		'Flash',
		'Paginator' => array(
			'className' => 'AppPaginator',
			'limit' => 100, //App-wide default index limit
		),
		'DebugKit.Toolbar' => array('panels' => array('history' => false)), //Turn of history to avoid serialization of closure error
		'Search.Prg',
		'RequestHandler',
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'scope' => array('User.active' => 1)
				),
			),
			'flash' => array(
				'element' => 'alert',
				'key' => 'auth',
				'params' => array(
					'class' => 'alert-error',
					'close' => false,
				)
			)
		)
	);

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = [
		'Html' => [
			'className' => 'AxiaHtml'
		],
		'Form' => [
			'className' => 'AxiaCakeForm'
		],
		'Time' => [
			'className' => 'AxiaTime'
		],
		'Session',
		'Paginator' => ['className' => 'BoostCake.BoostCakePaginator'],
		'Rbac.Rbac' => ['actionPath' => 'app/actions'],
		'Number',
		'Js',
		'AssetCompress.AssetCompress',
		'Csv',
	];


/**
 * beforeRender callback
 *
 * @return void
 */
	public function beforeRender() {
	     $this->response->header('X-Frame-Options', 'DENY');
	     parent::beforeRender();
	}

/**
 * BeforeFilter callback
 * 'Form' => array(
 * 'className' => 'AxiaCakeForm'
 * ),
 *
 * @return void
 */
	public function afterFilter() {
		parent::afterFilter();
 		
 		//Check if request is api and user session was created
		//then extract nonce from check digest authorization 
		if ((Hash::get($this->request->params, 'api') || $this->params['ext'] == 'json')) {
			$allHeaders = getallheaders();
			if (!empty(Hash::get($allHeaders, 'Authorization'))) {
				$DigestAuthenticate = new DigestAuthenticate(new ComponentCollection(), []);
				//parse digest auth values
				$DigestAuthorizationHeaderValues = $DigestAuthenticate->parseAuthData(Hash::get($allHeaders, 'Authorization'));
				//extract client-supplied nonce value
				$nonce = Hash::get($DigestAuthorizationHeaderValues, 'nonce');
				//Check nonce is a valid, known and unused value.
				if ($this->_validateNonce($nonce)) {
					$this->_upsertNonce($nonce, true);
				}  else {
					$this->response->body = null;
					$this->response->statusCode(401);
					$this->set('name', 'Unauthorized: invalid authorization parameters.');
					$this->set('url', $this->request->here);
					return $this->render('/Errors/errorCust');
				}
			}
		}
	}

/**
 * BeforeFilter callback
 * 
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if ($this->RequestHandler->isSSL() && (Hash::get($this->request->params, 'api') || $this->params['ext'] == 'json')) {
			$this->autoRender = false;
			$this->layout = false;
			AuthComponent::$sessionKey = false;
			$this->Auth->authenticate = array(
				'Digest' => array(
					'realm' => env('SERVER_NAME'),
					'fields' => array('username' => 'access_token','password' => 'api_password'),
					'scope' => array('User.active' => 1),
					'recursive' => -1
				)
			);

			$this->Auth->authorize = array('Controller');
			$this->Auth->unauthorizedRedirect = false;
			AuthComponent::$sessionKey = false;
			// Store newly generated nonce values to enforce single use
			if (array_key_exists('WWW-Authenticate', $this->response->header())) {
				$headers = $this->response->header();
				preg_match_all('/(\w+)=([\'"]?)([a-zA-Z0-9\:\#\%\?\&@=\.\/_-]+)\2/', $headers['WWW-Authenticate'], $match, PREG_SET_ORDER);
				foreach ($match as $i) {
					if ($i[1] === 'nonce') {
						$nonce = $i[3];
						break;
					}
				}
				$this->_upsertNonce($nonce, false);
			}

		} else {
			//disable cache - no back button navigation on session termination
			$this->response->disableCache();

			$this->_setupAuthorize();
			//Check Ownership permissions
			$this->OwnerAccessControl->isAuthorizedOwner();
			if ($this->Security) {
				$this->Security->csrfExpires = Configure::read('Security.csrfExpires');
				$this->Security->csrfUseOnce = false;
			}
		}
	}
/**
 * validateNonce
 * Checks whther api digest nonce value is a valid 13 charavter hexadecimal value and whether
 * it aexists in the pool of prevously generated values and that has not been used before.
 *  
 * @param  string $nonce      a nonce value up to 50 characters long
 * @return boolean true|false 
 */
	protected function _validateNonce($nonce) {
		if (strlen($nonce) === 13 && ctype_xdigit($nonce)) {
			$ApiAuthValueTracker = ClassRegistry::init('ApiAuthValueTracker');
			$values = $ApiAuthValueTracker->find('first', array(
				'recursive' => -1,
				'conditions' => array('nonce_value' => $nonce, 'nonce_value_used' => false)
			));
			if (!empty(Hash::get($values, 'ApiAuthValueTracker.id'))) {
				return true;
			}
		}
		return false;
	}
/**
 * _upsertNonce
 * The nonce value may not be used more than once and in order to enforce this
 * nonce values generated by Digest authenticate are remembered and marked as used/unused.
 * Exception trown must be handled by caller
 * 
 * @param  string $nonce      a nonce value up to 50 characters long
 * @param  boolean $markAsUsed 
 * @return void
 * @throws InvalidArgumentException
 */
	protected function _upsertNonce($nonce, $markAsUsed) {
		if (empty($nonce) || strlen($nonce) >50) {
			throw new InvalidArgumentException('Argument 1 is invalid!');
		}
		$ApiAuthValueTracker = ClassRegistry::init('ApiAuthValueTracker');
		$values = $ApiAuthValueTracker->find('first', array(
			'recursive' => -1,
			'conditions' => array('nonce_value' => $nonce)
		));
		if (empty($values['ApiAuthValueTracker']['id'])) {
			$values = array();
			$ApiAuthValueTracker->create();
		}
		$values['ApiAuthValueTracker']['nonce_value'] = $nonce;
		$values['ApiAuthValueTracker']['nonce_value_used'] = $markAsUsed;
		$ApiAuthValueTracker->save($values);
	}
/**
 * isAuthorized method
 * This method uses RBAC to authorize/deny API consumers access to existing API resources
 *
 * @return boolean
 */
	public function isAuthorized() {
		$User = ClassRegistry::init('User');
		if ($this->request->params['api'] || $this->params['ext'] == 'json'){
			$toApiResource = 'app/actions/'. $this->request->params['controller'] . '/'. $this->request->params['action'];
			$accessControl = new AccessControl($this->Auth->user());
			$User->updateLastApiRequest($this->Auth->user('id'));
			return $accessControl->isPermitted($toApiResource);
		}

		// Default deny
		return false;
	}
/**
 * AuthComponent setup goes here
 *
 * @return void
 */
	protected function _setupAuthorize() {
		$User = ClassRegistry::init('User');
		//Configure AuthComponent
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');

		$this->Auth->loginAction = array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'dashboards', 'action' => 'home');
		$notAutorizedRedirect = array('plugin' => false, 'admin' => false, 'controller' => 'dashboards', 'action' => 'home');
		$authUser = $this->Auth->user();
		$curUserId = null;
		if (!empty($authUser['User']['id'])) {
			$curUserId = $authUser['User']['id'];
		} elseif (!empty($authUser['id'])) {
			$curUserId = $authUser['id'];
		}
		if (!empty($curUserId) && $User->roleIs($curUserId, User::ROLE_API)) {
			$notAutorizedRedirect['controller'] = 'Users';
			$notAutorizedRedirect['action'] = 'view';
			$notAutorizedRedirect[] = $this->Auth->user('id');
		}
		$this->Auth->unauthorizedRedirect = $notAutorizedRedirect;
		$this->Auth->authError = '<h3><span class="label label-danger">You don\'t have permissions to access that.</span></h3>';
		//Don't display auth errors to users who are just logging in
		if ($this->Auth->redirect = '/' && !$this->Auth->user()) {
			unset($this->Auth->authError);
			$this->Session->delete('Message.auth');
		}

		//define a custom authorization
		$this->Auth->authenticate = array(
			'GoogleAuthenticate.Google' => array(
				'fields' => array(
					'username' => 'username',
					'password' => 'password',
				),
				'recursive' => -1,
				'contain' => array(),
                'userFields' =>  null,
				'passwordHasher' => array(
					'className' => 'Shim.Fallback',
					'hashers' => array(
						'Shim.Modern',
						'Simple' => array(
							'className' => 'Simple',
							'hashType' => 'sha1',
						),
					),
				),
				'userModel' => 'User',
				'scope' => array( 'User.active' => 1, 'User.is_blocked' => false)
			)
		);
		$this->Auth->authorize = array(
			'Custom' => array('actionPath' => '/app/actions')
		);

		//Checks if a user is currently logged in, which implies a session has been started.
		if ($this->Auth->user()) {
			$this->Session->write('Auth.User.loggedInUser', $this->Auth->user('fullname'));
		} elseif ($this->request->is('ajax')) {
			echo '<script type="text/javascript">location.reload();</script>';
			$this->Flash->warning(__('Session expired, login and try your last action again.'), [
				'key' => 'sessionExpiration'
			]);
		}
	}

/**
 * Display a success message
 *
 * @param string $message Message
 * @param mixed $redirectUrl string|array url
 * @return mixed
 */
	protected function _success($message = null, $redirectUrl = null, $settings = array()) {
		if (empty($message)) {
			$message = __('%s successfully saved', $this->modelClass);
		}
		$default = array('class' => 'success');
		$settings = array_merge($default, $settings);
		$this->Session->setFlash(h($message), 'default', $settings);
		if (!empty($redirectUrl)) {
			return $this->redirect($redirectUrl);
		}

		return true;
	}

/**
 * Display a failure message
 *
 * @param string $message Message
 * @param mixed $redirectUrl string|array Url
 * @return mixed
 */
	protected function _failure($message = null, $redirectUrl = null, $settings = array()) {
		if (empty($message)) {
			$message = __('%s could not be saved', $this->modelClass);
		}
		$this->Session->setFlash(h($message), 'default', $settings);
		if (!empty($redirectUrl)) {
			return $this->redirect($redirectUrl);
		}

		return true;
	}

/**
 * _hasPendChanges method
 *
 * @param string $merchantId  a merchant id
 * @param sting $logId a MerchantNote.loggable_log_id or LoggableLog.id
 * @return boolean
 */
	protected function _hasPendChanges($merchantId, $logId) {
		return (ClassRegistry::init('MerchantChange')->isPending($merchantId) && empty($logId));
	}

/**
 * _ajaxView
 *
 * @param string $elementPath the path where the view element is located with the form '/Elements/[Path]'
 * @param array $viewVars variables to pass to the ajax view
 * @return void
 */
	protected function _ajaxView($elementPath, array $viewVars) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			if ($this->Session->check('Auth.User.id')) {
				$this->set($viewVars);
				$this->render($elementPath, 'ajax');
			} else {
				//Session expired, redirect user to login page with Forbidden status 403
				$this->response->statusCode(403);
			}
		}
	}
}
