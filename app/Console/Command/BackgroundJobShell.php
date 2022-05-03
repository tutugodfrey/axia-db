<?php

App::uses('AppShell', 'Console/Command');

/**
 * BackgroundJobShell Shell
 *
 * @package app.Console.Command
 */
class BackgroundJobShell extends AppShell {

	public $uses = array(
		'LastDepositReport',
		'Bet',
		'User',
		'MerchantPci',
		'BackgroundJob',
		'ImportedDataCollection'
	);

/**
 * lastDepositCsvDataImport
 * This shell method is used to background LastDepositReport's CAV data import process
 *
 * @return void
 * @throws InvalidArgumentException
 * @throws Exception
 */
	public function lastDepositCsvDataImport() {
		$response = array();

		$requestData = $this->args[0];
		$userId = $this->args[1];
		$userEmail = $this->LastDepositReport->User->field('user_email', ['id' => $userId]);
		$result = [
			'job_name' => 'Last Activity CSV Upload',
			'start_time' => date('Y-m-d H:i:s'),
			'result' => false,
		];
		$emailSettings = [
			'template' => Configure::read('App.bgJobEmailTemplate'),
			'from' => Configure::read('App.defaultSender'),
			'to' => $userEmail,
			'subject' => 'Finished Last Activity CSV Upload Process',
			'emailBody' => $result
		];
		// check the arguments passed
		if (empty($requestData) || empty($userId)) {
			$result['log']['errors'][] = "Missing argument(s) in lastDepositCsvDataImport()";
			if (!empty($userEmail)) {
				$this->LastDepositReport->User->emailUser($userEmail, $result, $emailSettings);
			}
			throw new InvalidArgumentException('Missing argument(s) in lastDepositCsvDataImport()');
		}

		$result = $this->LastDepositReport->importFromCsvUpload($requestData, $userId);
		$emailSettings['emailBody'] = $result;
		$this->LastDepositReport->User->emailUser($userEmail, $result, $emailSettings);
	}
/**
 * betMassUpdateJob
 * This shell method is used to background Mass Updating User BET Costs process
 *
 * @return void
 * @throws InvalidArgumentException
 * @throws Exception
 */
	public function betMassUpdateJob() {
		$response = array();

		$requestData = $this->args[0];
		$jobTrackerId = $this->args[1];
		$userId = $this->args[2];
		$userEmail = $this->User->field('user_email', ['id' => $userId]);
		$result = [
			'job_name' => 'User BETs Cost Update',
			'start_time' => date('Y-m-d H:i:s'),
			'result' => false,
		];
		$emailSettings = [
			'template' => Configure::read('App.bgJobEmailTemplate'),
			'from' => Configure::read('App.defaultSender'),
			'to' => $userEmail,
			'subject' => 'Finished User BETs Cost Mass Update Process',
			'emailBody' => $result
		];
		// check the arguments passed
		if (empty($requestData) || empty($userId)) {
			$result['log']['errors'][] = "Missing argument(s) in betMassUpdateJob()";
			if (!empty($userEmail)) {
				$this->User->emailUser($userEmail, $result, $emailSettings);
			}
			//update background job tracker
			$this->BackgroundJob->finished($jobTrackerId, false);
			throw new InvalidArgumentException('Missing argument(s) in lastDepositCsvDataImport()');
		}
		//update background job tracker
		$this->BackgroundJob->inProgress($jobTrackerId);

		$result = $this->Bet->updateMany($requestData);
		$successful = (empty(Hash::get($result, 'log.errors')) && $result['result']);
		$emailSettings['emailBody'] = $result;
		$this->User->emailUser($userEmail, $result, $emailSettings);
		// update background job tracker
		$this->BackgroundJob->finished($jobTrackerId, $successful);
	}

/**
 * processControlScanBoardAndCancelRequests
 * Requests to Board or Cancel merchant in ControlScan are done asynchronously by ControlScan.
 * An additional API call must be made to determine the processing status of merchant board/cancel requests that
 * were previously submitted. This method performs such task and also updates those that have been processed successfuly 
 * 
 * Only one argument is required at zeroth index position:
 * $this->args[0] = name of the callable function to use to process/finalize the original request 
 *  - processPendingBoardings 
 *   OR
 *  - processPendingCancellations
 *
 *
 * @return void
 */
	public function processControlScanBoardAndCancelRequests() {
		$func = $this->args[0];
		if ($func === 'processPendingBoardings' || $func === 'processPendingCancellations') {
			call_user_func(array($this->MerchantPci, $func));
		}
	}
/**
 * importedDataCollectionUpsert
 * This shell method is used to background large CSV dataset uploads using the ImportedDataCollection.upsertUpload() method
 *
 * @return void
 */
	public function importedDataCollectionUpsert() {
		$params = $this->args[0];
		$this->ImportedDataCollection->upsertUpload($params);
	}
}
