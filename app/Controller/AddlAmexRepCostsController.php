<?php
App::uses('AppController', 'Controller');
/**
 * AddlAmexRepCosts Controller
 *
 * @property AddlAmexRepCost $AddlAmexRepCost
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class AddlAmexRepCostsController extends AppController {

/**
 * edit
 *
 * Edit Additional Amex rep costs records belonging to a single user and compensation profile
 *
 * @param type $userId user id 
 * @param type $compensationId user compensation id
 * @param type $partnerUserId  user id of a user w/role partner
 * @return void
 * @throws NotFoundException
 */
	public function edit($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->AddlAmexRepCost->UserCompensationProfile->id = $compensationId;
		if (!$this->AddlAmexRepCost->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User Compensation Profile')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AddlAmexRepCost->save($this->request->data)) {
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
 * @param type $compensationId user compensation id
 * @return void
 */
	protected function _setViewData($userId, $compensationId) {
		$user = $this->AddlAmexRepCost->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
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
		$amexRepCosts = $this->_getData($compensationId);
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/AddlAmexRepCosts/viewContent', compact('amexRepCosts', 'compensationId', 'partnerUserId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->AddlAmexRepCost->getByCompProfile($compensationId);
		return $data;
	}
}
