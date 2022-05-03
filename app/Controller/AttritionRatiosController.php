<?php
App::uses('AppController', 'Controller');
/**
 * AttritionRatios Controller
 *
 * @property AttritionRatio $AttritionRatio
 */
class AttritionRatiosController extends AppController {

/**
 * editMany, edit a grid of attrition ratios related to a given userId
 *
 * @param string $userId a user id
 * @param string $compensationId a user compensation id
 * @param string $partnerUserId a partner user id
 * @throws NotFoundException
 * @return void
 */
	public function editMany($userId = null, $compensationId = null, $partnerUserId = null) {
		$this->AttritionRatio->UserCompensationProfile->id = $compensationId;
		if (!$this->AttritionRatio->UserCompensationProfile->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		$associatedUsers = ClassRegistry::init('AssociatedUser')->getAssociatedUsers($userId, $compensationId, true);
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AttritionRatio->saveMany($this->request->data)) {
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
			if (empty($this->request->data)) {
				$this->request->data = $this->AttritionRatio->defaultData($userId, $associatedUsers);
			}
		}
		$user = $this->AttritionRatio->UserCompensationProfile->User->getBreadcrumbUserInfo($userId);
		$this->set(compact('user', 'compensationId', 'associatedUsers', 'partnerUserId'));
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
		$attritionRatios = $this->_getData($compensationId);
		$associatedUsers = array();
		$associatedUsers = $this->AttritionRatio->UserAssociated->AssociatedUser->getAssociatedUsers($userId, $compensationId, true);
		$this->_ajaxView('/Elements/AttritionRatios/viewContent', compact('userId', 'associatedUsers', 'attritionRatios', 'compensationId', 'partnerUserId'));
	}

/**
 * _getData
 *
 * @param string $compensationId a compensation profile id
 * @return array
 */
	protected function _getData($compensationId) {
		$data = $this->AttritionRatio->find('byCompProfile', array(
			'conditions' => array('AttritionRatio.user_compensation_profile_id' => $compensationId)
		));
		return $data;
	}

}
