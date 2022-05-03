<?php

App::uses('AppController', 'Controller');

class UserCompensationProfilesController extends AppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'UserCompensationProfiles';

/**
 * addCompProfile
 *
 * @param string $userId user_id
 * @param bool $isDefault is_default
 * @param string $partnerUserId partner_user_id
 * @param string $roleId the user role id
 * @param bool $isManager whether the user is a manager
 *
 * @return null
 */
	public function addCompProfile($userId, $isDefault, $partnerUserId = null, $roleId = null, $isManager = false) {
		$this->_checkActionParams($userId);

		if ($this->request->is('post') || $this->request->is('put')) {

			$data['UserCompensationProfile'] = array('user_id' => $userId);
			$data['UserCompensationProfile']['is_default'] = $isDefault;
			if ($isManager) {
				//the role id helps idetify the hierarchical manager level
				$data['UserCompensationProfile']['role_id'] = $roleId;
			}
			if (!empty($partnerUserId)) {
				$this->_checkActionParams($partnerUserId);
				$data['UserCompensationProfile']['partner_user_id'] = $partnerUserId;
				$data['UserCompensationProfile']['is_partner_rep'] = true;
			}

			if ($this->UserCompensationProfile->save($data)) {
				$success = $this->UserCompensationProfile->createInitialCompData($this->UserCompensationProfile->id, $userId);
				if ($success) {
					return $this->_success(null, $this->referer());
				} else {
					$this->_failure(null, $this->referer());
				}
			} else {
				$this->_failure(null, $this->referer());
			}
		}
	}

/**
 * copyMany
 *
 * @param string $userId user_id
 * @return void
 */
	public function copyMany($userId, $newPartnerIdForNewCopy = null) {
		try {
			$this->_checkActionParams($userId);
		} catch (NotFoundException $e) {
			$this->_failure($e->getMessage(), $this->referer());
		}
		if ($this->request->is('ajax')) {
			if ($this->Session->check('Auth.User.id')) {
				$this->autoRender = false;
				$users = $this->UserCompensationProfile->getUsersWithComps($userId);
				$targetUserName = $this->UserCompensationProfile->User->field('fullname', ['id' => $userId]);
				$this->set('users', $users);
				$this->set('targetUserName', $targetUserName);
				$this->set('newPartnerIdForNewCopy', $newPartnerIdForNewCopy);
				$this->set('targetUserId', $userId);
				$this->render('copyMany');
			} else {
				//Session expired status 403
				$this->response->statusCode(403);
			}
		} elseif ($this->request->is('post')) {
			if (empty($this->request->data('UserCompensationProfile.id'))) {
				$this->_failure('Copy failed! Nothing was selected...', ['controller' => 'Users', 'action' => 'view', $this->request->data('UserCompensationProfile.target_user_id')]);
			}

			$copyCount = count($this->request->data('UserCompensationProfile.id'));
			if ($copyCount >= 2) {
				CakeResque::enqueue(
					'genericAxiaDbQueue',
					'UcpShell',
					array(
						'processCopyManyJob',
						//Args:
						$this->request->data('UserCompensationProfile.target_user_id'),
						$this->request->data('UserCompensationProfile.id'),
						$this->request->data('UserCompensationProfile.new_partner_id_for_new_copy'),
						$this->Auth->user('user_email'))
				);
				$this->Session->setFlash(
					__("Copying $copyCount UCPs will take approx. " . $copyCount * 15 . " seconds, therefore process will continue on the server. An email will be sent to you when done."),
					'Flash/bgProcessMsg', ['class' => 'alert alert-warning']);
			} else {
				$copySuccess = false;
				$error = '';
				try {
					$copySuccess = $this->UserCompensationProfile->copyMany(
						$this->request->data('UserCompensationProfile.target_user_id'),
						$this->request->data('UserCompensationProfile.id'),
						$this->request->data('UserCompensationProfile.new_partner_id_for_new_copy'));
				} catch (Exception $e) {
					$error = $e->getMessage();
				}
				if ($copySuccess) {
					$this->Session->setFlash(__("Successfully copied $copyCount compensation profile(s)"), 'default', ['class' => 'alert alert-success']);
				} else {
					$this->Session->setFlash(__("Error - Copy Failed!" . h($error)), 'default', ['class' => 'alert alert-danger strong']);
				}
			}
			$this->redirect(['controller' => 'Users', 'action' => 'view', $this->request->data('UserCompensationProfile.target_user_id')]);
		}
	}

/**
 * getUpdatedUcpList dispatches AJAX requests only
 * Sets a unique list of user compensations catered for the target user 
 *
 * @param string $sourceUserId source user_id the user whiom the list will be based from
 * @param string $targetUserId user_id the user receiving the resulting copies
 * @return void
 */
	public function getUpdatedUcpList($sourceUserId, $targetUserId) {
		$this->autoRender = false;
		if ($this->Session->check('Auth.User.id')) {
			$ucpList = $this->UserCompensationProfile->buildUcpList($sourceUserId, $targetUserId);
			$this->set('ucpList', $ucpList);
			$this->render('/Elements/AjaxElements/ucpListFormMenu');
		} else {
			//Session expired status 403
			$this->response->statusCode(403);
		}
	}

/**
 * Delete for user compensation profile.
 *
 * @param string $id user compensation profile id
 * @access public
 *
 * @return null
 */
	public function delete($id = null) {
		if ($this->request->is('ajax')) {
			if ($this->Session->check('Auth.User.id')) {
				$this->autoRender = false;
				if ($this->UserCompensationProfile->deleteAll(['UserCompensationProfile.id' => $id], true) === false) {
					$this->response->statusCode(500);
				}
			} else {
				$this->response->statusCode(403);
			}
		}
	}

/**
 * Update profile option 1 and option 2
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @throws InternalErrorException
 * @return void
 */
	public function updateProfileOptions() {
		$this->layout = null;
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			throw new MethodNotAllowedException();
		}

		$id = Hash::get($this->request->data, 'UserCompensationProfile.id');
		if (!$this->UserCompensationProfile->exists($id)) {
			throw new NotFoundException(__('Invalid User Compensation Profiles'));
		}
		if (!$this->UserCompensationProfile->save($this->request->data)) {
			throw new InternalErrorException(__('User profile options could not be updated'));
		}
	}

/**
 * Check if exists for the requested action
 *
 * @param string $userId user_id
 *
 * @throws NotFoundException
 * @return null
 */
	protected function _checkActionParams($userId = null) {
		if (!$this->UserCompensationProfile->User->exists($userId)) {
			throw new NotFoundException(__('Invalid User'));
		}
	}

}
