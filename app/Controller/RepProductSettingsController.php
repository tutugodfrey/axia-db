<?php
App::uses('AppController', 'Controller');
/**
 * RepProductSettings Controller
 *
 * @property RepProductSetting $RepProductSetting
 * @property PaginatorComponent $Paginator
 */
class RepProductSettingsController extends AppController {

/**
 * editMany
 *
 * Edit many RepProductSetting records belongin to a single user
 *
 * @param type $userId a user id
 * @param type $compensationId compensation profile id
 * @param type $partnerUserId user id of type partner
 * @throws NotFoundException
 * @return void
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->RepProductSetting->UserCompensationProfile->id = $compensationId;
		if (!$this->RepProductSetting->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->RepProductSetting->saveMany(Hash::extract($this->request->data, '{n}.RepProductSetting'))) {
				$redirectUserId = (empty($partnerUserId))? $userId : $partnerUserId;
				$this->_success(null, array(
						'controller' => 'users',
						'action' => 'view',
						$redirectUserId
				));
			} else {
				$this->_setViewData($userId, $compensationId);
				$this->_failure();
			}
		} else {
			$this->request->data = $this->_getData($compensationId);
			$this->_setViewData($userId, $compensationId, $partnerUserId);
		}
	}

/**
 * Utility function to set the view data
 *
 * @param type $userId a user if
 * @param type $compensationId compensation profile id
 * @param type $partnerUserId user id of type partner
 * @return void
 */
	protected function _setViewData($userId, $compensationId, $partnerUserId) {
		$prodsWithSettings = ClassRegistry::init('ProductsServicesType')->find('active', ['conditions' => ['class_identifier' => Configure::read('App.productClasses.p_set.classId')]]);
		$user = $this->RepProductSetting->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$this->set(compact('user', 'userId', 'partnerUserId', 'prodsWithSettings', 'compensationId'));
	}

/**
 * ajaxView, handles ajax request for viewContent ajax view
 *
 * @param string $userId a user id
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 */
	public function ajaxView($userId, $compensationId, $partnerUserId = null) {
		$repProdSettings['RepProductSetting'] = $this->_getData($compensationId);
		$prodsWithSettings = ClassRegistry::init('ProductsServicesType')->find('active', ['conditions' => ['class_identifier' => Configure::read('App.productClasses.p_set.classId')]]);
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/RepProductSettings/viewContent', compact('repProdSettings', 'compensationId', 'prodsWithSettings'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->RepProductSetting->find('byCompProfile', array(
				'conditions' => array('RepProductSetting.user_compensation_profile_id' => $compensationId)
			));
		return $data;
	}
}
