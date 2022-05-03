<?php
App::uses('AppController', 'Controller');
/**
 * GatewayCostStructures Controller
 *
 * @property GatewayCostStructure $GatewayCostStructure
 * @property PaginatorComponent $Paginator
 */
class GatewayCostStructuresController extends AppController {

/**
 * editMany
 *
 * Edit many ach rep costs records belongin to a single user
 *
 * @param type $userId a user id
 * @param type $compensationId compensation profile id
 * @param type $partnerUserId user id of type partner
 * @throws NotFoundException
 * @return void
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->GatewayCostStructure->UserCompensationProfile->id = $compensationId;
		if (!$this->GatewayCostStructure->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->GatewayCostStructure->saveMany(Hash::extract($this->request->data, '{n}.GatewayCostStructure'))) {
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
		$user = $this->GatewayCostStructure->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$gateways = ClassRegistry::init('Gateway')->getList();
		$this->set(compact('user', 'userId', 'partnerUserId', 'gateways', 'compensationId'));
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
		$gwrepCost['GatewayCostStructure'] = $this->_getData($compensationId);
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/GatewayRepCosts/viewContent', compact('gwrepCost', 'compensationId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->GatewayCostStructure->find('byCompProfile', array(
			'conditions' => array('GatewayCostStructure.user_compensation_profile_id' => $compensationId)
		));
		return $data;
	}
}
