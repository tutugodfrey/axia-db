<?php

App::uses('AppController', 'Controller');

class AchRepCostsController extends AppController {

/**
 * editMany
 *
 * Edit many ach rep costs records belongin to a single user
 *
 * @param string $userId user id
 * @param string $compensationId compensation profile id
 * @param string $partnerUserId another user id melonging to a partner
 * @return void
 * @throws NotFoundException
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->AchRepCost->UserCompensationProfile->id = $compensationId;
		if (!$this->AchRepCost->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User compensation profile')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AchRepCost->saveMany(Hash::extract($this->request->data, '{n}.AchRepCost'))) {
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
 * @param string $userId user id
 * @param string $compensationId compensation profile id
 * @param string $partnerUserId another user id melonging to a partner
 * @return void
 */
	protected function _setViewData($userId, $compensationId, $partnerUserId) {
		$user = $this->AchRepCost->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$achProviers = ClassRegistry::init('AchProvider')->find('list');
		$this->set(compact('user', 'userId', 'partnerUserId', 'compensationId', 'achProviers'));
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
		$achRepCosts['AchRepCost'] = $this->_getData($compensationId);
		$achProviers = ClassRegistry::init('AchProvider')->find('list');
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/AchRepCosts/viewContent', compact('achRepCosts', 'compensationId', 'achProviers'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->AchRepCost->getByCompProfile($compensationId);
		return $data;
	}

}
