<?php
App::uses('AppController', 'Controller');
/**
 * Controller
 *
 * @property EquipmentCosts $EquipmentCosts
 */
class EquipmentCostsController extends AppController {

/**
 * editMany, edit a grid of the model related to a given userId
 *
 * @param string $userId a user id
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 * @throws NotFoundException
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->EquipmentCost->UserCompensationProfile->id = $compensationId;
		if (!$this->EquipmentCost->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User Compensation Profile')));
		}

		$isPost = ($this->request->is('post') || $this->request->is('put'));
		if ($isPost) {
			if ($this->EquipmentCost->saveMany($this->request->data['EquipmentCost'])) {
				$redirectUserId = (empty($partnerUserId)) ? $userId: $partnerUserId;
				$this->_success(null, array(
					'controller' => 'users',
					'action' => 'view',
					$redirectUserId
				));
			} else {
				$this->_failure();
			}
		}

		$userEquipment = $this->_getData($compensationId);
		if (!$isPost) {
			$this->request->data = $userEquipment;
		}

		$this->set(compact('userEquipment', 'partnerUserId'));
	}

/**
 * ajaxView, handles ajax request for viewContent ajax view
 *
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 */
	public function ajaxView($compensationId, $partnerUserId = null) {
		$userEquipment = $this->_getData($compensationId);
		$this->_ajaxView('/Elements/EquipmentCosts/viewContent', compact('userEquipment', 'compensationId', 'partnerUserId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$options = array('contain', array(
				'User'
		));
		$data = $this->EquipmentCost->UserCompensationProfile->getCompData($compensationId, $options);
		$data['EquipmentCost'] = $this->EquipmentCost->getByCompProfile($compensationId, $data['PartnerUser']['id']);
		return $data;
	}

}
