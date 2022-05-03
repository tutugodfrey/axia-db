<?php

App::uses('AppModel', 'Model');
App::uses('ControlScan', 'Model');
App::uses('SaqControlScanUnboarded', 'Model');

/**
 * MerchantPci Model
 *
 * @property Merchant $Merchant
 */
class MerchantPci extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true
			),
		),
		'compliance_level' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => true
			)
		),
		'saq_completed_date' => array(
			'date' => array(
				'rule' => array('date'),
				'allowEmpty' => true
			)
		),
		'last_security_scan' => array(
			'date' => array(
				'rule' => array('date'),
				'allowEmpty' => true
			)
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['scanning_company'])) {
            $this->data[$this->alias]['scanning_company'] = $this->removeAnyMarkUp($this->data[$this->alias]['scanning_company']);
        }
        if (!empty($this->data[$this->alias]['saq_type'])) {
            $this->data[$this->alias]['saq_type'] = $this->removeAnyMarkUp($this->data[$this->alias]['saq_type']);
        }
	}
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant'
	);

/**
 * Compilance levels
 * 
 * @return array
 */
	protected $_compilanceLevel = array(
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4
	);

/**
 * Retrive a lsit of compilance level
 * 
 * @return array
 */
	public function getListOfCompilanceLevel() {
		return $this->_compilanceLevel;
	}

/**
 * listPendedCsBoardedGuids
 * Returns a list of ControlScan GUIDs that have not been marked as controlscan_boarded = true
 * This list can be used to check the boarding status of these merchants with ControlScan's API
 * 
 * @return array with MerchantPci.id as keys and MerchantPci.controlscan_boarded_guid as value
 */
	public function listPendedCsBoardedGuids() {
		return $this->find('list', array(
			'fields' => array('id', 'cs_board_request_guid'),
			'conditions' => array(
				'cs_board_request_guid IS NOT NULL',
			)
		));
	}

/**
 * listPendedCsCancelledGuids
 * Returns a list of ControlScan GUIDs that have not been marked as cancelled_controlscan = true
 * This list can be used to check the cancel request status of these merchants with ControlScan's API
 * 
 * @return array with MerchantPci.id as keys and MerchantPci.cs_cancel_request_guid as value
 */
	public function listPendedCsCancelledGuids() {
		return $this->find('list', array(
			'fields' => array('id', 'cs_cancel_request_guid'),
			'conditions' => array(
				'cs_cancel_request_guid IS NOT NULL',
				'cancelled_controlscan' => false,
			)
		));
	}

/**
 * updateAsBoarded
 * Updates MerchantPci record associated the merchant_id parameter as successfully boarded to cotntrol scan
 * 
 * @param  string $id a MerchantPci.id
 * @return void
 */
	public function updateAsBoarded($id) {
		$this->_setAsBoardedOrCancelled($id, true);
	}
/**
 * updateAsCancelled
 * Updates MerchantPci record associated the merchant_id parameter as successfully cancelled from cotntrol scan
 * 
 * @param  string $id a MerchantPci.id
 * @return void
 */
	public function updateAsCancelled($id) {
		$this->_setAsBoardedOrCancelled($id, false);
	}

/**
 * _setAsBoardedOrCancelled
 * Updates MerchantPci record associated the merchant_id parameter as successfully boarded to cotntrol scan
 * or successfully cancelled from controlscan depending on the $asBoarded parameter.
 * This function marks newly boarded merchants as boarded but also toggles previously boarded merchants
 * from boarded to cancelled but not the opposite
 *
 * @param  string $id a MerchantPci.id
 * @param boolean $asBoarded updates merchant as controlscan boarded if true otherwise as cancelled
 * @return void
 */
	protected function _setAsBoardedOrCancelled($id, $asBoarded) {
		if (!empty($id)) {
			if ($asBoarded == true && $this->hasAny(['id' => $id, 'cancelled_controlscan' => true])) {
				return;
			}
			$data = [
				'id' => $id,
				'controlscan_boarded' => $asBoarded,
				'cancelled_controlscan' => !$asBoarded
			];
			if ($asBoarded) {
				$data['cs_board_request_guid'] = null; //remove cs_board_request_guid
			} else {
				$data['cs_cancel_request_guid'] = null; //remove cs_cancel_request_guid
				$data['cancelled_controlscan_date'] = date('Y-m-d'); //remove cs_cancel_request_guid
				$merchantId = $this->field('merchant_id', ['id' => $id]);
				$SaqControlScanUnboarded = ClassRegistry::init('SaqControlScanUnboarded');
				$unboardedId = $SaqControlScanUnboarded->field('id', ['merchant_id' => $merchantId]);
				$unboardedData = [];
				if (!empty($unboardedId)) {
					$unboardedId['id'] = $unboardedId;
				}
				$unboardedData['merchant_id'] = $merchantId;
				$unboardedData['date_unboarded'] = date('Y-m-d');
				$SaqControlScanUnboarded->save($unboardedData);
			}
			$this->save($data, ['validate' => false]);
		}
	}

/**
 * processPendingBoardings
 * This function iterates through a list of MerchantPci records, this list contains records
 * belonging to merchants that were sent to ControlScan for boarding and boarding are still pending.
 * All records of pending request that have been processed will be updated.
 * IMPORTANT: This method should be called from a background CakeResque job since it waits 10 secods to allow
 * time for original API calls to be processed.
 * 
 * @return void
 */
	public function processPendingBoardings() {
		$pendList = $this->listPendedCsBoardedGuids();
		if (empty($pendList)) {
			return;
		}
		$hasError = false;
		$errMsg = '';
		foreach ($pendList as $id => $guid) {
			sleep(10); //wait between API calls
			try {
				$status = $this->checkCsRequestStatus($id, 'board', $guid);
				$status = strtolower($status);//normalize

				//Capture all errors in the same errMsg var
				if ($status == 'error') {
					$hasError =  true;
					$errMsg .= "\nError status returned from ContolScan API check Boarding status for:\n";
					$errMsg .= "GUID: '$guid'\nMerchantPci record id: '$id'\n";
				}
				if ($status == 'success') {
					$this->_setAsBoardedOrCancelled($id, true);
				}
			} catch (Exception $e) {
				$hasError = true;
				$errMsg .= "\nUnexpected Exception ocurred while checking Boarding status:\n";
				$errMsg .= $e->getMessage();
				$errMsg .= "\nGUID: '$guid'\nMerchantPci record id: '$id'\n";
			}
		}
		if ($hasError) {
			//Send falure notification
			$email = array_keys(Configure::read('App.defaultSender'));
			$this->Merchant->User->emailUser($email[0], $errMsg);
		}
	}

/**
 * processPendingCancellations
 * This function iterates through a list of MerchantPci records, this list contains records
 * belonging to merchants that were sent to ControlScan for a cancel request and are still pending. 
 * All records of pending request that have been processed will be updated.
 * IMPORTANT: This method should be called from a background CakeResque job since it waits 10 secods to allow
 * time for original API calls to be processed. 
 * 
 * @return void
 */
	public function processPendingCancellations() {
		$pendList = $this->listPendedCsCancelledGuids();
		if (empty($pendList)) {
			return;
		}
		$hasError = false;
		$errMsg = '';
		foreach ($pendList as $id => $guid) {
			sleep(10); //wait between API calls
			try {
				$status = $this->checkCsRequestStatus($id, 'cancel', $guid);
				$status = strtolower($status);//normalize
				
				//Capture all errors in the same errMsg var
				if ($status == 'error') {
					$hasError =  true;
					$errMsg .= "\nError status returned from ContolScan API check Canceling status for:\n";
					$errMsg .= "GUID: '$guid'\nMerchantPci record id: '$id'\n";
				}
				if ($status == 'success') {
					$this->_setAsBoardedOrCancelled($id, false);
				}
			} catch (Exception $e) {
				$hasError = true;
				$errMsg .= "\nUnexpected Exception ocurred while checking cancellation status:\n";
				$errMsg .= $e->getMessage();
				$errMsg .= "\nGUID: '$guid'\nMerchantPci record id: '$id'\n";
			}
		}
		if ($hasError) {
			//Send falure notification
			$email = array_keys(Configure::read('App.defaultSender'));
			$this->Merchant->User->emailUser($email[0], $errMsg);
		}
	}

/**
 * checkCsRequestStatus
 * Checks status of asynchronous Merchat Boarding And Cancelling requests made previously to ControlScan.
 * The $csGuid parameter must contain the ControlScan guid returned when the origial API request was made.
 *
 * @param string $requestType should be either "board" or "cancel"
 * @param string $requestType board or cancel request to check the status
 * @param string $csGuid the ControlScan GUID originally returned when the origial API request was made
 * @return string
 * @throws Exception
 */
	public function checkCsRequestStatus($id, $requestType, $csGuid) {
		$ControlScan = ClassRegistry::init('ControlScan');
		$status = null;
		if ($requestType === 'board') {
			$status = $ControlScan->getBoardingStatus($csGuid);
		} elseif ($requestType === 'cancel') {
			$status = $ControlScan->getCancellingStatus($csGuid);
		}
		unset($ControlScan);
		ClassRegistry::flush();
		return $status;
	}
}