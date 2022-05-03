<?php

App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 */
 /**
 * @OA\Tag(name="Users", description="Operation and data about Users")
 *
 * @OA\Schema(
 *	   schema="Users",
 *     description="Users database table schema",
 *     title="Users",
 *     @OA\Property(
 *			description="User id",
 *			property="id",
 *			type="string/uuid"
 *     ),
 *     @OA\Property(
 *			description="User First name",
 *			property="user_first_name",
 *			type="string"
 *     ),
 *     @OA\Property(
 *			description="User last name",
 *			property="user_last_name",
 *			type="string"
 *     ),
 *     @OA\Property(
 *			description="Active =1, Inactive = 0",
 *			property="active",
 *			type="integer"
 *     ),
 * )
 */
class UsersController extends AppController {

	public $components = array(
		'Security',
		'Search.Prg',
		'Flash',
		'Flash',
		'RequestHandler' => [
			'viewClassMap' => ['csv' => 'CsvView.Csv']
		]
	);

/**
 * BeforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array(
			'request_pw_reset',
			'change_pw',
			'login',
			'logout',
		));
		$this->Security->unlockedActions= array(
			'view',
			'AutoCompleteSuggestions',
			'edit',
			'add',
			'editAssociated',
			'ajaxAssociatedView',
			'removeAssociated',
			'setMainAssociated',
			'getUsersByRole',
			'getPermissionLevelByRole',
			'assignRoles',
			'checkDecrypPassword',
			'api_get_partners',
			'api_get_reps');
	}

/**
 * request_pw_reset
 * Handles requests to reset the user's password.
 * Generates a unique psudo-random md5 hash for one-time use as URI parameter to reset the user password.
 * This md5 hash is saved in the user's record and deleted after the password is reset, efectively expiring the URL that was sent to the user to reset the password.
 * 
 * @param boolean $hasExpired whether the request to reset password is because it hasExpired
 * @param string $id a user id
 * @param boolean $setRandPw whether to assign a randomized password to the user
 * @return void
 */
	public function request_pw_reset($hasExpired = false, $id = null, $setRandPw = false) {
		$this->layout = 'loginlayout';
		if ($this->request->is('post')) {
			if (!empty($id)) {
				$conditions = ['id' => $id];
			} else {
				$conditions = ['username' => $this->request->data('User.username')];
			}
			$user = $this->User->find('first', [
				'conditions' => $conditions,
				'fields' => ['id', 'user_email']
			]);

			if (!empty($user['User']['id']) && !empty($user['User']['user_email'])) {
				$data['pw_reset_hash'] = $pwResetHash = $this->User->getRandHash();
				if ($setRandPw) {
					$data['password'] = $this->User->generateRandPw();
					//Set expriation date to sometime in the past to prevent user from logging in without updating the temporary password
					$data['pw_expiry_date'] = '1999-01-01';
				}
				$this->User->id = $user['User']['id'];
				if (!$this->User->save($data, ['validate' => false])) {
					$this->Session->setFlash(__('Error: Failed to persist temporary password reset hash parameter. Try again later.'), 'Flash/alert', ['class' => 'alert-danger']);
				} else {
					$tmpPwMsg = '';
					if ($setRandPw) {
						$tmpPwMsg = "and the following temporary password has been assigned to your account: {$data['password']}\n";
					}
					$msg = "A request to reset/renew your password has been submitted\n";
					$msg .= $tmpPwMsg;
					$msg .= "Follow the link below to change your password:\n";
					$msg .= Router::url(['controller' => 'Users', 'action' => 'change_pw', (int)$hasExpired, $pwResetHash], true) . "\n";
					$this->User->emailUser($user['User']['user_email'], $msg);
				}
				$this->Session->setFlash(__('A password reset link has been emailed to the user.'), 'Flash/alert', ['class' => 'alert-success']);
				if ($setRandPw) {
					$this->redirect($this->referer());
				} else {
					$this->redirect(array('action' => 'login'));
				}
			} else {
				$errMsg = (!empty($id) && empty($user['User']['user_email']))?__("Failed to reset password, user does not have an email address.") : __('That is not a registered username.');
				$this->Session->setFlash($errMsg, 'Flash/alert', ['class' => 'alert-danger']);
			}
		}
	}

/**
 * Users change_pw method implements first time login functionality
 *
 * @param boolean $renew whether this request is to renew password
 * @param string $pwResetHash the md5 hash for one-time use as URI parameter to reset the user password 
 * @return void
 */
	public function change_pw($renew, $pwResetHash) {
		$this->layout = 'loginlayout';
		$this->User->setPwFieldsValidation($renew);
		$id = $this->User->field('id', ['pw_reset_hash' => $pwResetHash]);

		if (empty($id) || !isset($renew) || !isset($pwResetHash)) {
			$this->set('name', 'ERROR 404: Page Not Found Or Has Expired.');
			$this->set('url', Router::url(['controller' => 'Users', 'action' => 'change_pw', $renew, $pwResetHash], true));
			$this->render('/Errors/errorCust');
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($renew) {
				$newPW = $this->request->data['User']['password'];
				$currentPW = $this->request->data['User']['cur_password'];
				if ($this->User->pwIsValid($this->request->data['User']['id'], $currentPW) === false) {
					$this->Session->setFlash(__('Current password does not match our records.'), 'Flash/alert', ['class' => 'alert-danger']);
					$this->redirect(['action' => 'change_pw', $renew, $pwResetHash]);
				}
				if ($newPW === $currentPW) {
					$this->Session->setFlash(__('New password must be different from current.'), 'Flash/alert', ['class' => 'alert-danger']);
					$this->redirect(['action' => 'change_pw', $renew, $pwResetHash]);
				}
			}
			//save pw_reset_hash as null
			$this->request->data['User']['pw_reset_hash'] = null;
			$updatedData = $this->request->data['User'];
			$updatedData['is_blocked'] = false;
			$updatedData['wrong_log_in_count'] = 0;
			if ($this->User->save($updatedData)) {
				$this->Session->setFlash(__('Password updated!'), 'Flash/alert', ['class' => 'alert-success']);
				$this->redirect(['action' => 'login']);
			} else {
				$this->Session->setFlash(__('Failed to update password.'), 'Flash/alert', ['class' => 'alert-danger']);
			}

		}
		$this->request->data['User']['id'] = $id;
		$this->set('id', $id);
		$this->set('renewingPw', $renew);
	}

/**
 * Displays secret code and QRcode for the user account.
 * Will create a secret, if not set in users table
 *
 * Also handles removal of secret from database with Form->postLink(), to allow normal login again.
 *
 * @param string $renew specify renew to generate a new secres token
 * @return void
 */
	public function secret($renew = null) {
		$id = $this->Auth->user('id');
		if ($this->request->is('post')) {
			$this->User->save(['id' => $id, 'secret' =>  null, 'opt_out_2fa' => false], ['validate' => false]);
			$this->redirect(array(
				'controller' => 'dashboards',
				'action' => 'home',
				'plugin' => false,
				'admin' => false,)
			);
		}

		App::uses('GoogleAuthenticator', 'GoogleAuthenticate.Lib');
		$Google = new GoogleAuthenticator();
		$secret = $this->User->field('secret', ['id' => $id]);
		if (empty($secret) || $renew == 'renew') {
			$secret = $Google->generateSecret();
			$this->User->save(['id' => $id, 'secret' =>  $secret, 'opt_out_2fa' => false], ['validate' => false]);
		}

		$url = $Google->getUrl($secret, 'AxiaMed_Database('.$this->Auth->user('user_email').')');

		$this->set(compact('secret', 'url'));
		if ($renew == 'renew') {
			//redirect without param to avoid accidental renewal on page refresh
			$this->redirect(['action' => 'secret']);
		}
	}

/**
 * Users login method implements first time login functionality
 *
 * @return void
 */
	public function login() {
		$this->layout = 'loginlayout';
		$this->response->disableCache();
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$userId = $this->Auth->user('id');
				$passwordValidDays = $this->User->getDaysTillPwExpires($userId);
				//Do not allow login if user enters code but user is not setup for 2FA
				if (!empty($this->request->data('User.code')) && empty($this->Auth->user('secret'))) {
					$this->Session->destroy();
					//Display intentionally vage error message. We don't want to give any hints to potential attackers.
					$this->Flash->warning(__('2-Factor Authentication code invalid!'), ['key' => 'loginError']);
					$this->redirect(array('action' => 'login'));
				}
				if ($passwordValidDays < 0) {
					$this->Session->destroy();
					$this->Flash->warning(__('Password expired! Please renew your password.'), [
						'key' => 'loginError'
					]);
					$this->redirect(array('action' => 'login'));
				}
				if ($this->User->needsPasswordRehash($userId)) {
					$this->User->rehashPassword($userId, $this->request->data('User.password'));
				}
				if ($passwordValidDays <= 7) {
					$msg = ($passwordValidDays === 0)? "You password expires today!" : "Your password will expire in $passwordValidDays day" . (($passwordValidDays > 1)? 's' : '');
					$msg .= " Please renew your password from login page.";
					$this->Session->setFlash(__($msg), 'Flash/alert', ['class' => 'alert-danger strong text-center']);
				}
				if ($this->User->UsersRole->hasAny(['user_id' => $userId]) === false) {
					$this->Session->destroy();
					$this->Flash->warning(__('Unable to log you in! Your account has not been assigned a role. Please inform administrator.'), [
						'key' => 'loginError'
					]);
					$this->redirect(array('action' => 'login'));
				}
				$loginRedirect = array(
					'controller' => 'dashboards',
					'action' => 'home',
					'plugin' => false,
					'admin' => false,
				);
				if ($this->User->roleIs($userId, User::ROLE_API)) {
					$loginRedirect['controller'] = 'Users';
					$loginRedirect['action'] = 'view';
					$loginRedirect[] = $userId;
				}
				//reset any previous failed attempts to login after a successful one
				$this->User->trackIncorrectLogIn($this->request->data('User.username'), true);
				return $this->redirect($loginRedirect);
			} else {
				$errMsg = 'Invalid information!';
				$attemptCount = $this->User->trackIncorrectLogIn($this->request->data('User.username'));
				if ($attemptCount >= 6) {
					$errMsg .= " Account is now locked. Notification will be sent to the registered user's email.";
				}
				$this->Flash->warning(__($errMsg), ['key' => 'loginError']);
			}
		}
	}

/**
 * Users logout method
 *
 * @return void
 */
	public function logout() {
		$this->Session->destroy();
		$this->Flash->warning(__('Good-Bye'), [
			'key' => 'goodbye'
		]);
		return $this->redirect($this->Auth->logout());
	}

/**
 * Users index method
 *
 * @return void
 */
	public function index() {
		$this->Prg->commonProcess();
		$this->Paginator->settings = $this->User->getUserIndexInfo();
		if (!empty($this->passedArgs['sort'])) {
			if ($this->passedArgs['sort'] == 'Role.name') {
				$this->Paginator->settings['group'][] = 'Role.name';
			}
		}

		$this->Paginator->settings['conditions'] = $this->User->parseCriteria($this->passedArgs);
		$this->set('users', $this->paginate());
	}

/**
 * Export the Users index data to csv
 *
 * @return void
 */
	public function exportUsers() {
		$this->Prg->commonProcess();
		$this->Paginator->settings = $this->User->getUserIndexInfo();
		if (!empty($this->passedArgs['sort'])) {
			if ($this->passedArgs['sort'] == 'Role.name') {
				$this->Paginator->settings['group'][] = 'Role.name';
			}
		}

		$this->Paginator->settings['conditions'] = $this->User->parseCriteria($this->passedArgs);
		//we want all users
		$userCount = $this->User->find('count');
		$this->Paginator->settings['limit'] = $userCount;
		$this->Paginator->settings['maxLimit'] = $userCount;
		$data = $this->paginate();
		$users = [];
		if (!empty($data)) {
			$users = $this->User->setExportStructure($data);
		}

		$this->viewClass = 'CsvView.Csv';
		$this->set('users', $users);
	}

/**
 * view method
 *
 * @param string $id a user id
 * @param string $partnerUserId a user id belonging to a user with role of Partner
 * @return void
 */
	public function view($id = null, $partnerUserId = null, $createApiPw = false) {
		/*****Get/Set Compensation data on Ajax request only*/
		if ($this->request->is('ajax')) {
			if ($this->Session->check('Auth.User.id')) {
				$this->autoRender = false;

				//Get compensation data for given $id
				//During an Ajax call the $id param being passed is not the user id, but the compensation profile id
				$options = array('contain' => array(
					'User' => array(
					),
				));

				$user = $this->User->UserCompensationProfile->getCompData($id, $options);
				//Check if this is a manager UCP
				$isManagerUcp = $this->User->Role->hasAny([
					'id' => Hash::get($user, 'UserCompensationProfile.role_id'),
					'name' => [User::ROLE_SM, User::ROLE_SM2]
				]);
				//Re-assign the $id as the user id
				$compId = $id;
				$id = $user['User']['id'];

				//data for bet tables
				$betTables = ClassRegistry::init('BetTable')->getAllGroupedByCardType();

				$this->set(compact(
					'user',
					'isManagerUcp',
					'compId',
					'betTables',
					'partnerUserId'
				));
				$this->render('/Elements/AjaxElements/user_compensation', 'ajax');
			} else {
				//Session expired, redirect user to login page with Forbidden status 403
				$this->response->statusCode(403);
			}
		} else {
			//Check Ownership permissions
			$this->OwnerAccessControl->isAuthorizedOwner();
			$user = $this->User->view($id);
			$isPartnerRep = $this->User->UserCompensationProfile->hasAny(array('user_id' => $id, 'is_partner_rep' => true));
			$isPartner = $this->User->roleIs($id, Configure::read('AssociatedUserRoles.Partner.roles.0'));
			$isManager = $this->User->roleIs($id, Configure::read('AssociatedUserRoles.SalesManager.roles'));
			$isApiConsumer = $this->User->roleIs($id, User::ROLE_API);
			$compProfiles = $this->User->UserCompensationProfile->getAssociatedCompList($id, $isPartnerRep, $isManager);
			$ucpCount = $this->User->UserCompensationProfile->find('count', ['conditions' => ['user_id' => $id]]);
			$managerRoles = [];
			if ($isManager) {
				$managerRoles = $this->User->UserCompensationProfile->getMgrRoleCompProfileList($user['User']['id']);
			}
			if ($isPartner) {
				$partnerReps = $this->User->getPartnerReps($id);
				$this->set('partnerReps', Hash::sort($partnerReps, '{s}', 'asc'));
			}

			if ($this->request->is('post') && $createApiPw) {
				$this->_create_api_pw($id);
			}
			$roleList = ClassRegistry::init('Rbac.Role')->generateTreeList();
			$this->set(compact('user', 'roleList', 'compProfiles', 'isPartner', 'isManager', 'managerRoles', 'ucpCount', 'isApiConsumer'));
		}
	}

/**
 * AutoCompleteSuggestions method
 *
 * @param string $activeStatus status
 * @param string $searchData username
 * @return void
 */
	public function AutoCompleteSuggestions($activeStatus, $searchData = null) {
		/* This function handles only ajax rerquests */
		$this->autoRender = false;

		if ($this->request->is('ajax')) {
			/* Check if session is still active by checking currently logged in user id */
			if ($this->Session->check('Auth.User.id')) {
				$userUUID = $this->User->findUserByUsername($searchData, $activeStatus);

				if (!empty($userUUID)) {
					echo json_encode($userUUID);
				} else {
					/* if no merchants are found */
					$this->response->statusCode(404);
				}
			} else {
				/* Upon session expiration, we cannot use $this->redirect(...) dutring an ajax requests in process.
				 * Therefore return a Forbidden status 403 responce and handle this responce on the client side with ajax. */
				$this->response->statusCode(403);
			}
		}
	}

/**
 * _roleChangeAllowed() method
 * Checks if current user is allowed to change roles for others and for self
 *
 * @throws NotFoundException
 * @return boolean true when allowed false when not allowed to change role
 */
	protected function _roleChangeAllowed() {
		return !($this->Session->read('Auth.User.id') === $this->request->data('User.id') &&
							$this->User->rbacIsPermitted('app/actions/Users/view/module/changeRoleSelfModule', true) === false);
	}
/**
 * edit method
 *
 * @param string $id user id
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			//Since the role form field becomes disabled when user is not allowed to change the role,
			//the role data will not be included in the post request, but we still want to save
			if (!empty($this->data['Role']['Role'][0]) || $this->_roleChangeAllowed() === false) {
				$this->User->set(array_merge($this->request->data, ['User' => array_merge($this->request->data['User'], $this->request->data['Role'])]));

				if ($this->User->save($this->request->data)) {
					return $this->_success(null, array('action' => 'view', $id));
				} else {
					$this->_failure();
				}
			} else {
				$this->_failure("Role is required!");
			}
		}

		$this->request->data = $this->User->view($id);
		if (!empty($this->request->data['UsersRole'])) {
			$index = 0;
			foreach ($this->request->data['UsersRole'] as $role) {
				$this->request->data['Role']['Role'][$index] = $role['role_id'];
				$index++;
			}
		}

		$roles = $this->User->UsersRole->Role->find('list');
		$entities = $this->User->Entity->find('list');
		$this->set(compact('roles', 'entities'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			if (!empty($this->data['Role']['Role'][0])) {
				$this->User->create();
				if ($user = $this->User->save($this->request->data)) {
					$this->_success(__('The User has been saved'));
					$this->redirect(array('action' => 'view', $user['User']['id']));
				} else {
					$this->_failure();
				}
			} else {
				$this->_failure("Role is required!");
			}
		}

		$newUser = true;
		$roles = $this->User->UsersRole->Role->find('list');
		$entities = $this->User->Entity->find('list');
		$this->set(compact('newUser', 'roles', 'entities'));
	}

/**
 * edit associated users
 *
 * @param string $id user id
 * @param string $compensationId compensation profile id
 * @param string $partnerUserId partner User Id
 * @throws NotFoundException
 * @return void
 */
	public function editAssociated($id = null, $compensationId = null, $partnerUserId = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->AssociatedUser->save($this->request->data)) {
				$this->_success(null, array('action' => 'editAssociated', $id, $compensationId));
			} else {
				$this->_failure();
			}
		}

		$this->request->data = $this->_getAssocData($compensationId);
		$this->set('roleNames', $this->User->getRoleNames());
		$this->set('partnerUserId', $partnerUserId);
	}

/**
 * ajaxAssociatedView, handles ajax request for ajax view
 *
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 */
	public function ajaxAssociatedView($compensationId, $partnerUserId = null) {
		$assocUsers = $this->_getAssocData($compensationId);
		$this->_ajaxView('/Elements/Users/associated_view_content', compact('assocUsers', 'compensationId', 'partnerUserId'));
	}

/**
 * _getAssocData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getAssocData($compensationId) {
		$options = array(
			'contain' => array(
				'UserCompensationAssociation' => array(
					'order' => 'UserCompensationAssociation.role DESC',
							'UserAssociated' => array(
								'fields' => array('id', 'fullname')),
				)
			),
		);
		$data = $this->User->UserCompensationProfile->getCompData($compensationId, $options);
		return $data;
	}

/**
 * Remove the association with another user
 *
 * @param string $associatedUserId user id
 * @param string $compensationId compensation profile id
 * @throws NotFoundException
 * @return void
 */
	public function removeAssociated($associatedUserId = null, $compensationId = null) {
		$this->User->AssociatedUser->id = $associatedUserId;
		if (!$this->User->AssociatedUser->exists()) {
			throw new NotFoundException(__('Invalid AssociatedUser'));
		}

		$userId = $this->User->AssociatedUser->field('user_id');
		$idOfAssociatedUser = $this->User->AssociatedUser->field('associated_user_id');
		if ($this->User->AssociatedUser->deleteAllAssocData($associatedUserId, $idOfAssociatedUser, $compensationId)) {
			$this->_success(__('AssociatedUser removed'), array('action' => 'editAssociated', $userId, $compensationId));
		}
		$this->_failure(__('AssociatedUser was not removed'), array('action' => 'editAssociated', $userId, $compensationId));
	}
/**
 * setMainAssociated
 * 
 * @param string $associatedUserId user id
 * @param string $compensationId compensation profile id
 * @throws NotFoundException
 * @return void
 */
	public function setMainAssociated($associatedUserId = null, $compensationId = null) {
		$this->User->AssociatedUser->id = $associatedUserId;
		if (!$this->User->AssociatedUser->exists()) {
			throw new NotFoundException(__('Invalid AssociatedUser'));
		}
		//Get the user id of the user who wil become the main associated user
		$userId = $this->User->AssociatedUser->field('user_id');
		$assocUserId = $this->User->AssociatedUser->field('associated_user_id');
		if ($this->User->AssociatedUser->toggleMainAssocUser($assocUserId, $compensationId)) {
			$this->_success(__('Main/Default user association succesfully assigned'), array('action' => 'editAssociated', $userId, $compensationId));
		}
		$this->_failure(__('Failed to assign a Main/Default user association!'), array('action' => 'editAssociated', $userId, $compensationId));
	}

/**
 * Get users by role
 *
 * @return void
 */

	public function getUsersByRole() {
		/*Prevent self association by removing the user that owns the profile from the results*/
		$users = Hash::remove($this->User->getByRole($this->request->data['AssociatedUser']['role']), $this->request->data['AssociatedUser']['user_id']);
		$this->set(compact('users'));
	}

/**
 * Get permission level by role
 *
 * @return void
 */

	public function getPermissionLevelByRole() {
		$permissions = $this->User->getPermissionsByRole($this->request->data['AssociatedUser']['role']);
		$this->set(compact('permissions'));
	}
/**
 * Change the user roles
 *
 * @param string $id user id
 * @throws NotFoundException
 * @return void
 */
	public function assignRoles($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}

		$redirectUrl = array('action' => 'view', $id, '#' => 'permissions-section');
		if ($this->request->is('post') || $this->request->is('put')) {
			ClassRegistry::init('Rbac.Role')->saveRolesToUser($id, $this->request->data['Role']);
			$this->_success(__('The new roles has been assigned'), $redirectUrl);
		}
		$this->_failure(__('The new roles could not be assigned'), $redirectUrl);
	}

/**
 * checkPassword method
 *
 * @param string $id user id
 * @param string $ajaxRedirectController string name of a controller to return in the response if a inmediate consecutive request needs to be made to a different controller
 * @param string $ajaxRedirectAction string name of a the action that corresponds to the $ajaxRedirectController
 * @param array $params parameters to pass to the action
 * @return void
 */
	public function checkDecrypPassword($id = null, $ajaxRedirectController = null, $ajaxRedirectAction = null, $params = null) {
		/* This function handles only ajax requests */
		$this->autoRender = false;
		if ($this->request->is('ajax')) {

			/* Check if session is still active by checking currently logged in user id */
			if ($this->Session->check('Auth.User.id')) {
				if (!empty($this->request->data['User']['password'])) {
					$grantAccess = $this->User->checkDecrypPassword($this->request->data['User']['password']);

					if ($grantAccess) {
						//The name of the controller and action that will be used to render contents of whatever access was granted to
						//must be passed back to the response
						echo json_encode($this->request->data['User']);
					} else {
						/* Return status 401: Unathorized */
						$this->response->statusCode(401);
					}
				} else {
					// Set the id to tell our element whether there is an existing MerchantUwVolume.id
					$this->request->data['User']['id'] = $id;
					$this->request->data['User']['redirectController'] = $ajaxRedirectController;
					$this->request->data['User']['redirectAction'] = $ajaxRedirectAction;
					$this->request->data['User']['redirectActionParams'] = $params;
					$this->render('/Elements/AjaxElements/modalPasswordDialog', 'ajax');
				}
			} else {
				/* Upon session expiration, we cannot use $this->redirect(...) dutring an ajax requests in process.
				 * Therefore return a Forbidden status 403 responce and handle this responce on the client side with ajax. */
				$this->response->statusCode(403);
			}
		}
	}

/**
 * block method
 *
 * @param string $id user id
 * @throws NotFoundException
 * @return void
 */
	public function toggleActive($id = null) {
		$this->User->id = $id;

		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}
		$status = (int)!(bool)$this->User->field('active');
		$this->User->saveField('active', $status);
		$alertMsg = 'User status updated. ';
		if ($status === 0 && $this->User->roleIs($id, User::ROLE_API)) {
			$this->User->revokeApiAccess($id);
			$alertMsg .= 'API access revoked.';
		}

		$this->_success(__($alertMsg), ['action' => 'view', $id]);
	}

/**
 * block method
 *
 * @param string $id user id
 * @throws NotFoundException
 * @return void
 */
	public function block($id = null) {
		$this->User->id = $id;

		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}

		$this->User->toggleBlockUser($id, true);
		$alertMsg = 'User is now blocked. ';
		if ($this->User->roleIs($id, User::ROLE_API)) {
			$alertMsg .= 'API access revoked.';
		}
		$this->_success(__($alertMsg), ['action' => 'view', $id]);
	}

/**
 * unblock method
 *
 * @param string $id user id
 * @throws NotFoundException
 * @return void
 */
	public function unblock($id = null) {
		$this->User->id = $id;

		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}

		$this->User->toggleBlockUser($id, false);
		$this->_success(__('User no longer blocked.'), ['action' => 'view', $id]);
	}

/**
 * unblock method
 *
 * @param string $id user id
 * @throws NotFoundException
 * @return void
 */
	public function turn_off_2FA($id = null, $optOut = false) {
		if ($this->request->is('post')) {
			$this->User->save(['id' => $id, 'secret' =>  null, 'opt_out_2fa' => (bool)$optOut], ['validate' => false]);
			$this->_success(__('2-Factor Authentication has been disabled for this user!'), $this->referer(), ['class' => 'alert alert-success']);
		}
	}

/**
 * createApiToken methiod
 *
 * @param string $id user id
 * @return void
 */
	public function create_api_token($id = null) {
		$this->User->id = $id;

		if (!$this->User->exists() || $this->User->roleIs($id, User::ROLE_API) === false) {
			$this->_failure(__('Failed to create token! User was not found or does not have the required role.'), ['action' => 'view', $id], array('alert alert-danger'));
		}

		try {
			$this->User->create_token($id);
		} catch (Exception $e) {
			$this->_failure(__($e->getMessage()), ['action' => 'view', $id], array('alert alert-danger'));
		}

		$this->_success(__('API access token created!'), ['action' => 'view', $id], array('class' => 'alert alert-success'));
	}

/**
 * createApiPw methiod
 * Utility action to creates API password. The password is sent to the view unhashed so the user can copy it and use it.
 *
 * @param string $id user id
 * @return void
 */
	protected function _create_api_pw($id) {
		$this->User->id = $id;
		if (!$this->User->exists() || $this->User->roleIs($id, User::ROLE_API) === false) {
			$this->_failure(__('Failed to create password! User was not found or does not have the required role.'), null, array('class' => 'alert alert-danger'));
			return;
		}

		try {
			$apiPass = $this->User->create_api_password($id);
			$this->set('oneTimeCopyPasswordStr', $apiPass);
			$this->_success(__('API password created!'), null, array('class' => 'alert alert-success'));
		} catch (Exception $e) {
			$this->_failure(__($e->getMessage()), null, array('class' => 'alert alert-danger'));
		}
	}

/**
 *
 * Handles API GET requests for a list of partners.
 * Accepts request query data containig a partner name string to search for.
 * The user_name value can be emtpty and a full list of partners will be returned.
 *
 * @OA\Get(
 *   path="/api/Users/get_partners?user_name={FirstName+LastName}",
 *	 tags={"Users"},
 *   summary="list partners ",
 *	 @OA\Parameter(
 *		   name="user_name",
 *		   description="Parner user full name to search for (optional). Empty parameter value is allowed but key 'user_name=' must be preset in query URI. Empty value will return a full list of all partners.
 *	 			Example 1: Jane Doe
 *	 			Example 2: Great Healthcare Solutions",
 *         in="query",
 *         @OA\Schema(type="string")
 *   ),
 *	 @OA\Response(
 *     response=200,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *		   example={"id": "5a3875b2-b170-4ab0-841f-510834627ad4", "first_name": "Jane", "last_name": "Doherty", "status": "active"},
 *         @OA\Schema(
 *	   	   	 ref="#/components/schemas/Users",
 *         )
 *     ),
 *     description="
 * 			status=success JSON list of Users (empty when no users found).
 *			status=failed Missing key user_name in uri",
 *   ),
 *   @OA\Response(
 *     response=405,
 *     description="HTTP method not allowed when request method is not GET"
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Invalid or missing query parameters"
 *   )
 * )
 *
 * @return void
 */
	public function api_get_partners() {
		$this->autoRender = false;
		$response = array('status' => 'failed', 'messages' => 'HTTP method not allowed');
		if ($this->request->is('get')) {
			$this->__setApiGetUsersRequestData(User::ROLE_PARTNER, $response);
		} else {
			$this->response->statusCode(405); //405 Method Not Allowed
		}
		$this->response->type('application/json');
		$this->response->body(json_encode($response));
	}

/**
 *
 * Handles API GET request for a list of reps.
 * Accepts request query data containig a rep name string to search for
 * The user_name value can be emtpty and a full list of reps will be returned along with a list of partners that the rep is associated with.
 *
 * @OA\Get(
 *   path="/api/Users/get_reps?user_name={FirstName+LastName}",
 *	 tags={"Users"},
 *   summary="list rep users",
 *	 @OA\Parameter(
 *		   name="user_name",
 *		   description="Rep user full name to search for (optional). Empty parameter value is allowed but key 'user_name=' must be preset in query URI. Empty value will return a full list of all reps.
 *	 			Example 1: John Doe",
 *         in="query",
 *         @OA\Schema(type="string")
 *   ),
 *	 @OA\Response(
 *     response=200,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *		   example={"id": "5a3875b2-b170-5ba1-321f-510834627ad4", "first_name": "John", "last_name": "Davis", "status": "active",
 *				"assoc_partners":{"588094ef-d50a-4def-a17b-20f0d95d2c4b": "A Partner Name", "08c37527-9abd-4c95-ba19-7bd30c1b54ac": "Great Healthcare Solutions"}
 *				},
 *         @OA\Schema(
 *	   	   	 ref="#/components/schemas/Users",
 *         )
 *     ),
 *     description="
 * 			status=success JSON list of Users (empty when no users found).
 *			status=failed Missing key user_name in uri",
 *   ),
 *   @OA\Response(
 *     response=405,
 *     description="HTTP method not allowed when request method is not GET"
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Invalid or missing query parameters"
 *   )
 * )
 *
 * @return void
 */
	public function api_get_reps() {
		$this->autoRender = false;
		$response = array('status' => 'failed', 'messages' => 'HTTP method not allowed');
		if ($this->request->is('get')) {
			$this->__setApiGetUsersRequestData([User::ROLE_REP, User::ROLE_PARTNER_REP], $response);
		} else {
			$this->response->statusCode(405); //405 Method Not Allowed
		}
		$this->response->type('application/json');
		$this->response->body(json_encode($response));
	}

/**
 * __setApiGetUsersRequestData()
 * Utility function to set request data for api_get_partners() and api_get_reps()
 * 
 * @param mixed string|array $roles the name of the role(s) of the list of users being requested
 * @param array &$response the API response array reference
 * @return void
 */
	private function __setApiGetUsersRequestData($roles, &$response) {
		//get data from request query
		$data = $this->request->query;
		if (array_key_exists('user_name', $data)) {
			$users = $this->User->getSimilarNames($roles, $data['user_name']);
			if (!empty($users)) {
				foreach($users as $idx => $user) {
					$response['data'][$idx] = [
						'user_id' => $user['User']['id'],
						'first_name' => $user['User']['user_first_name'],
						'last_name' => $user['User']['user_last_name'],
						'status' => ($user['User']['active'])? 'active' : 'inactive',
					];
					if ($roles === User::ROLE_PARTNER_REP || $roles === User::ROLE_REP || 
						(is_array($roles) && (in_array(User::ROLE_REP, $roles) || in_array(User::ROLE_PARTNER_REP, $roles)))
						) {
						$assocPartners = $this->User->UserCompensationProfile->getAssociatedPartners($user['User']['id']);
						$response['data'][$idx]['assoc_partners'] = $assocPartners;
					}
				}
				$response['messages'] = '';
			} else {
				$response['messages'] = 'No users found with a name similar to ' . $data['user_name'];
			}
			$response['status'] = 'success';
		} else {
			$this->response->statusCode(400);
			$response['messages'] = 'Invalid or missing query parameters';
		}
	}
}
