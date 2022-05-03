<?php

App::uses('AppController', 'Controller');

class RepMonthlyCostsController extends AppController {

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
		$this->RepMonthlyCost->UserCompensationProfile->id = $compensationId;
		if (!$this->RepMonthlyCost->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->RepMonthlyCost->saveMany(Hash::extract($this->request->data, '{n}.RepMonthlyCost'))) {
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
		$user = $this->RepMonthlyCost->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$betNetworks = ClassRegistry::init('BetNetwork')->getList();
		$this->set(compact('user', 'userId', 'betNetworks', 'partnerUserId', 'compensationId'));
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
		$repMonthlyCosts['RepMonthlyCost'] = $this->_getData($compensationId);
		$betNetworks = ClassRegistry::init('BetNetwork')->getList();
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/RepBetNetworkMonthlyCosts/viewContent', compact('repMonthlyCosts', 'compensationId', 'betNetworks'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->RepMonthlyCost->getByCompProfile($compensationId);
		return $data;
	}
}
