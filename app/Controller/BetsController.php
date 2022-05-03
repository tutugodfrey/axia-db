<?php

App::uses('AppController', 'Controller');

/**
 * Bets Controller
 *
 * @property Bet $Bet
 */
class BetsController extends AppController {

/**
	* Show bets related with a CardType, BetTable and User
	* @param string cardTypeId
	* @param string betTableId
	* @param string userId
	* @return void
	*/
	public function view($cardTypeId = null, $betTableId = null, $userId = null, $compensationId = null, $partnerUserId = null) {
		$this->_checkActionParams($cardTypeId, $betTableId, $compensationId);
		$this->_setActionInfo($cardTypeId, $betTableId, $userId, $compensationId, $partnerUserId);

		if ($this->request->is('ajax')) {
			$this->layout = null;
		}
	}

/**
	* Show bets related with a CardType, BetTable and User
	* @param string cardTypeId
	* @param string betTableId
	* @param string userId
	* @return void
	*/
	public function editMany($cardTypeId = null, $betTableId = null, $userId = null, $compensationId = null, $partnerUserId = null) {
		$this->_checkActionParams($cardTypeId, $betTableId, $compensationId);

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Bet->saveMany($this->request->data['Bet'])) {
				$redirectUserId = (empty($partnerUserId))? $userId: $partnerUserId ;
				$this->_success(null, array(
					'controller' => 'users',
					'action' => 'view',
					$redirectUserId
				));
			} else {
				$this->_failure();
			}
		}

		$this->_setActionInfo($cardTypeId, $betTableId, $userId, $compensationId, $partnerUserId);
	}

/**
	* Check if exists the CardType, BetTable and User for the requested action
	* @param string cardTypeId
	* @param string betTableId
	* @param string compensationId
	*
	* @throws NotFoundException
	*/
	protected function _checkActionParams($cardTypeId = null, $betTableId = null, $compensationId = null) {
		if (!$this->Bet->CardType->exists($cardTypeId)) {
			throw new NotFoundException(__('Invalid CardType'));
		}
		if (!$this->Bet->BetTable->exists($betTableId)) {
			throw new NotFoundException(__('Invalid BetTable'));
		}
		if (!$this->Bet->UserCompensationProfile->exists($compensationId)) {
			throw new NotFoundException(__('Invalid User Compensation Profile'));
		}
	}

/**
	* Set to the view the information needed for the action
	* @param string cardTypeId
	* @param string betTableId
	* @param string userId
	*/
	protected function _setActionInfo($cardTypeId = null, $betTableId = null, $userId = null, $compensationId = null, $partnerUserId = null) {
		$bets = $this->Bet->getFilteredBets($cardTypeId, $betTableId, $compensationId);
		$betNetworks = $this->Bet->BetNetwork->getList();
		$cardType = $this->Bet->CardType->get($cardTypeId );
		$betTable = $this->Bet->BetTable->get($betTableId);
		$user = $this->Bet->UserCompensationProfile->User->get($userId);
		$userCompensation = $this->Bet->UserCompensationProfile->get($compensationId);

		$this->set(compact('bets', 'betNetworks', 'cardType', 'betTable', 'user', 'userCompensation','partnerUserId'));
	}

/**
 * Perform a mass update of many bets for many UCPs
 *
 * @param string cardTypeId
 * @param string betTableId
 * @param string userId
 * @return void
 */
	public function mass_update() {
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Bet->validateMassUpdate($this->request->data)) {
				//initiate BG process tracker
				$BackgroundJob = ClassRegistry::init('BackgroundJob');
				$jobTrackerId = $BackgroundJob->addToQueue('Updating Users BET Costs');
				if ($jobTrackerId === false) {
					$this->_failure(__('Failed to create a Background Job Tracker for this process! Try again later.'), ['action' => 'mass_update']);
				}
				// this can be a very long process, so queue it and notify the user when done
				CakeResque::enqueue(
					'genericAxiaDbQueue',
					'BackgroundJobShell',
					['betMassUpdateJob', $this->request->data, $jobTrackerId, $this->Auth->user('id')]
				);
				$msg = 'Updates will begin shortly on the server. When finished a notification will be sent to ' . $this->Auth->user('user_email');
				$this->_success(__($msg), null, ['class' => 'alert alert-warning strong']);
			} else {
				//falure
				$this->_failure(__('Update failed! Fix any validation errors and try again.'), null, ['class' => 'alert alert-danger strong']);
			}
		}
		$betTable = $this->Bet->BetTable->getListGroupedByCardType();
		$betNetworks = $this->Bet->BetNetwork->getList();
		$usersList = $this->Bet->UserCompensationProfile->getUsersWithComps(null, true);
		$this->set(compact('betTable', 'betNetworks', 'usersList'));
		
	}

/**
 * getListGroupedByUser
 * Wrapper method to handle ajax request for list of user compensation profiles.
 * Response will include a JSON encoded array of users and corresponding UCPs
 *
 * @access public
 * @return void
 */
	public function getListGroupedByUser() {
		if ($this->request->is('ajax')) {
			if ($this->Session->check('Auth.User.id')) {
				$this->autoRender = false;
				if (!empty($this->request->data)) {
					$ucpList = $this->Bet->UserCompensationProfile->getListGroupedByUser($this->request->data('User.ids'));
					return json_encode($ucpList);
				} else {
					return json_encode([]);
				}
			} else {
				$this->response->statusCode(403);
			}
		}
	}
}
