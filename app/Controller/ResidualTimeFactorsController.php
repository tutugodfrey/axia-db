<?php
App::uses('AppController', 'Controller');

class ResidualTimeFactorsController extends AppController {

/**
 * Edit the ResidualTimeFactor and ResidualTimeParameter models of an user
 *
 * @throws NotFoundException
 * @param string $userId
 * @return void
 */
	public function editResidualTimeGrid($userId = null, $compensationId = null, $partnerUserId = null) {
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ResidualTimeFactor->UserCompensationProfile->saveAssociated($this->request->data)) {
				$redirectUserId = (empty($partnerUserId))? $userId: $partnerUserId;
				$redirectUrl = array(
					'controller' => 'Users',
					'action' => 'view',
					$redirectUserId
				);
				$this->_success(__('The Residual Time grid has been saved'), $redirectUrl);
			} else {
				$this->_failure(__('The Residual Time grid could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->_getData($compensationId);
			//required field if there is still no ResidualTimeFactor associated a user compensation profile
			if ($this->request->data['ResidualTimeFactor']['id'] === null) {
				$this->request->data['ResidualTimeFactor']['user_compensation_profile_id'] = $compensationId;
			}
		}
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
		$residualTimeParameterHeaders = ClassRegistry::init('ResidualParameterType')->getHeaders($userId, $compensationId, 'ResidualTimeParameter');
		$this->set(compact('userId', 'productsServicesTypes', 'residualTimeParameterHeaders', 'partnerUserId'));
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
		$residualTimeFact = $this->_getData($compensationId);
		$user = $this->ResidualTimeFactor->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$this->set(compact('user'));
		$this->_setViewData($userId, $compensationId, $partnerUserId);
		$this->_ajaxView('/Elements/ResidualTimeFactors/viewContent', compact('residualTimeFact', 'userId', 'compensationId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$options = array('contain' => array(
				'ResidualTimeFactor',
				'ResidualTimeParameter' => array(
					'UserCompensationAssociation' => array(
						'UserAssociated' => array(
							'fields' => array('id', 'fullname'))
					),
				),
			));
		$data = $this->ResidualTimeFactor->UserCompensationProfile->getCompData($compensationId, $options);
		return $data;
	}
}
