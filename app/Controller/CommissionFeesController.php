<?php

App::uses('AppController', 'Controller');

/**
 * CommissionFees Controller
 *
 */
class CommissionFeesController extends AppController {

/**
 * editMany, edit a grid of the model related to a given userId
 *
 * @throws NotFoundException
 * @param string $userId
 * @return void
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->CommissionFee->UserCompensationProfile->id = $compensationId;
		if (!$this->CommissionFee->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User Compensation Profile')));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->CommissionFee->saveMany($this->request->data['CommissionFee'])) {
				$redirectUserId = (empty($partnerUserId))? $userId: $partnerUserId;
				$this->_success(null, array(
					'controller' => 'users',
					'action' => 'view',
					$redirectUserId
				));
			} else {
				$this->_failure();
			}
		}
		$options = array('contain' => array(
			'CommissionFee',
			'UserCompensationAssociation' => array(
					'UserAssociated' => array(
						'fields' => array('id', 'fullname')),
			),
		));
		$commissionFees = $this->_getData($compensationId);
		$user = $this->CommissionFee->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$this->set(compact('partnerUserId', 'user', 'commissionFees'));
	}

/**
 * ajaxView, handles ajax request for viewContent ajax view
 *
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 */
	public function ajaxView($compensationId, $partnerUserId = null) {
		$commissionFees = $this->_getData($compensationId);
		$this->_ajaxView('/Elements/CommissionFees/viewContent', compact('commissionFees', 'compensationId', 'partnerUserId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$options = array('contain' => array(
			'CommissionFee',
			'UserCompensationAssociation' => array(
					'UserAssociated' => array(
						'fields' => array('id', 'fullname')),
			),
		));
		$data = $this->CommissionFee->UserCompensationProfile->getCompData($compensationId, $options);
		return $data;
	}

}
