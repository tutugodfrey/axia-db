<?php

App::uses('AppController', 'Controller');

class WebAchRepCostsController extends AppController {

/**
 * edit
 *
 * Edit web ach rep costs records belonging to a single user and compensation profile
 *
 * @param type $userId user id
 * @param type $compensationId compensation profile id
 * @param type $partnerUserId user id of a user with role of partner 
 * @return void
 * @throws NotFoundException
 */
	public function edit($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->WebAchRepCost->UserCompensationProfile->id = $compensationId;
		if (!$this->WebAchRepCost->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->WebAchRepCost->save($this->request->data)) {
				$redirectUserId = (empty($partnerUserId))? $userId: $partnerUserId;
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
			$this->_setViewData($userId, $compensationId);
		}
	}

/**
 * Utility function to set the view data
 *
 * @param type $userId user id
 * @param type $compensationId compensation profile id
 * @return void
 */
	protected function _setViewData($userId, $compensationId) {
		$user = $this->WebAchRepCost->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$this->set(compact('user', 'userId'));
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
		$webAchCosts = $this->_getData($compensationId);
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/WebAchRepCosts/viewContent', compact('webAchCosts', 'compensationId', 'partnerUserId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->WebAchRepCost->getByCompProfile($compensationId);
		return $data;
	}
}
