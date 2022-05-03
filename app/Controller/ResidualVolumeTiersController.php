<?php

App::uses('AppController', 'Controller');

class ResidualVolumeTiersController extends AppController {

/**
 * Edit the ResidualVolumeTier and ResidualParameter models of an user
 *
 * @param string $userId a user id
 * @param string $compensationId a user compensation profile id
 * @param string $partnerUserId a partner user id
 * @return void
 * @throws NotFoundException
 */
	public function editResidualGrid($userId = null, $compensationId = null, $partnerUserId = null) {
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ResidualVolumeTier->UserCompensationProfile->saveAssociated($this->request->data)) {
				$redirectUserId = (empty($partnerUserId))? $userId: $partnerUserId;
				$redirectUrl = array(
					'controller' => 'Users',
					'action' => 'view',
					$redirectUserId
				);
				$this->_success(__('The Residual Volume grid has been saved'), $redirectUrl);
			} else {
				$this->_failure(__('The Residual Volume grid could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->_getData($compensationId);
			//required field if there is still no ResidualVolumeTier associated to the user
			if ($this->request->data['ResidualVolumeTier']['id'] === null) {
				$this->request->data['ResidualVolumeTier']['user_compensation_profile_id'] = $compensationId;
			}
		}
		// data for residual parameters
		$this->_setViewData($userId, $compensationId, $partnerUserId);
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
		$productsServicesTypes = ClassRegistry::init('ProductsServicesType')->find('active');
		$residualParameterHeaders = ClassRegistry::init('ResidualParameterType')->getHeaders($userId, $compensationId);
		$this->set(compact('userId', 'productsServicesTypes', 'residualParameterHeaders', 'partnerUserId'));
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
		$residualVolTiers = $this->_getData($compensationId);
		$user = $this->ResidualVolumeTier->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$this->set(compact('user'));
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/ResidualVolumeTiers/viewContent', compact('residualVolTiers', 'userId', 'compensationId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$options = array('contain' => array(
				'ResidualVolumeTier',
				'ResidualParameter' => array(
					'UserCompensationAssociation' => array(
						'UserAssociated' => array(
							'fields' => array('id', 'fullname')),
					),
				),
			));
		$data = $this->ResidualVolumeTier->UserCompensationProfile->getCompData($compensationId, $options);
		return $data;
	}

}
