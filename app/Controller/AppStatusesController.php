<?php

App::uses('AppController', 'Controller');

/**
 * Controller
 *
 * @property AppStatus $AppStatus
 */
class AppStatusesController extends AppController {

/**
 * editMany, edit a grid of the model related to a given userId
 *
 * @param string $userId a user id
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @throws NotFoundException
 * @return void
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->AppStatus->UserCompensationProfile->id = $compensationId;
		if (!$this->AppStatus->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User Compensation Profile')));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AppStatus->saveMany($this->request->data['AppStatus'])) {
				$redirectUserId = (empty($partnerUserId))? $userId : $partnerUserId;
				$this->_success(null, array(
						'controller' => 'users',
						'action' => 'view',
						$redirectUserId
				));
			} else {
				$this->_failure();
			}
		} else {
			$this->request->data = $this->_getData($compensationId);
		}
		$merchantAchAppStatuses = ClassRegistry::init('MerchantAchAppStatus')->find('enabled');
		$this->set(compact('partnerUserId', 'merchantAchAppStatuses'));
	}

/**
 * ajaxView, handles ajax request for viewContent ajax view
 *
 * @param string $compensationId a compensation profile id
 * @param string $partnerUserId a user id with partner role
 * @return void
 */
	public function ajaxView($compensationId, $partnerUserId = null) {
		$merchantAchAppStatuses = ClassRegistry::init('MerchantAchAppStatus')->find('enabled');
		$appStatuses = $this->_getData($compensationId);
		$this->_ajaxView('/Elements/AppStatuses/viewContent', compact('appStatuses', 'compensationId', 'partnerUserId', 'merchantAchAppStatuses'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$options = array('contain' => array('AppStatus'));
		$data = $this->AppStatus->UserCompensationProfile->getCompData($compensationId, $options);
		return $data;
	}

}
