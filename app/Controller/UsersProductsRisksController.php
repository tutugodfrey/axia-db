<?php

App::uses('AppController', 'Controller');

class UsersProductsRisksController extends AppController {

/**
 * editMany, edit a grid of Users Products Risk assessmetnts
 *
 * @param string $merchantId a merchant id
 * @throws NotFoundException
 * @return void
 */
	public function editMany($merchantId) {
		$this->UsersProductsRisk->Merchant->id = $merchantId;
		if (!$this->UsersProductsRisk->Merchant->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Merchant')));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data = $this->UsersProductsRisk->cleanSaveData($this->request->data);
			if ($this->UsersProductsRisk->saveMany($this->request->data)) {
				$this->_success(null, array(
					'controller' => 'MerchantUws',
					'action' => 'view',
					$merchantId
				));
			} else {
				$errors = array_unique(Hash::extract($this->UsersProductsRisk->validationErrors, '{n}.{s}.{n}'));

				$errors = join($errors, '<br/>');
				$this->_setViewData($merchantId);
				$this->_failure($errors);
			}
		} else {
			$this->_setViewData($merchantId);
		}
	}

/**
 * Utility function to set the view data
 *
 * @param string $merchantId a merchant id
 * @return void
 */
	protected function _setViewData($merchantId) {
		$riskData = $this->UsersProductsRisk->getByMerchant($merchantId, true);
		$this->request->data['UsersProductsRisk'] = Hash::extract($riskData, '{s}.{n}');
		$merchant = ClassRegistry::init('Merchant')->find('first', array(
			'recursive' => -1,
			'conditions' => array("Merchant.id" => $merchantId),
			'contain' => array('User')));
		$this->set(compact('merchant', 'riskData'));
	}

}
