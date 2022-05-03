<?php

App::uses('AppController', 'Controller');
App::uses('AxiaTestCase', 'Test');

/**
 * UserParameters Controller
 *
 * @property UserParameter $UserParameter
 */
class UserParametersController extends AppController {

/**
 * editMany, edit a grid of USer Parameters related to a given userId
 *
 * @param string $userId user id
 * @param string $compensationId user compensation profile id
 * @param string $partnerUserId a partner user id
 * @throws NotFoundException
 * @return void
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->UserParameter->UserCompensationProfile->id = $compensationId;
		if (!$this->UserParameter->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User Compensation')));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->UserParameter->saveMany($this->request->data('UserParameter'))) {
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
			$this->_setViewData($userId, $compensationId, $partnerUserId);
			$this->request->data = $this->_getData($compensationId);
		}
	}

/**
 * Utility function to set the view data
 *
 * @param string $userId user id
 * @param string $compensationId user compensation profile id
 * @param string $partnerUserId a partner user id
 * @return void
 */
	protected function _setViewData($userId, $compensationId, $partnerUserId = null) {
		$userParameterHeaders = $this->UserParameter->UserParameterType->getHeaders($userId, $compensationId);
		$merchantAcquirers = ClassRegistry::init('MerchantAcquirer')->find('list', array('conditions' => array('reference_only' => false)));
		$ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$productsServicesTypes = $ProductsServicesType->find('active');
		$prodsWithSettings = $ProductsServicesType->find('active', ['conditions' => ['class_identifier' => Configure::read('App.productClasses.p_set.classId')]]);
		$this->set(compact('prodsWithSettings', 'userParameterHeaders', 'merchantAcquirers', 'productsServicesTypes', 'partnerUserId'));
	}

/**
 * ajaxView, handles ajax request for viewContent ajax view
 *
 * @param string $userId user id
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 */
	public function ajaxView($userId, $compensationId, $partnerUserId = null) {
		$user = $this->_getData($compensationId);
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/UserParameters/viewContent', compact('user', 'userId', 'compensationId', 'partnerUserId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$options = array(
				'contain' => array(
					'UserParameter' => array(
						'UserCompensationAssociation' => array(
							'UserAssociated' => array(
								'fields' => array('id', 'fullname'))
						),
					)));
		$data = $this->UserParameter->UserCompensationProfile->getCompData($compensationId, $options);
		return $data;
	}
}
