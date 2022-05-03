<?php

App::uses('AppShell', 'Console/Command');

/**
 * Ucp Shell (UserCompensationProfile shell)
 *
 * @package app.Console.Command
 */
class UcpShell extends AppShell {

	public $uses = array('UserCompensationProfile');

/**
 * processCopyManyJob
 *
 * @return void
 * @throws InvalidArgumentException
 */
	public function processCopyManyJob() {
		$targetUserId = $this->args[0];
		$sourceUcpIds = $this->args[1];
		$newPartnerIdForNewCopy = $this->args[2];
		$userEmail = $this->args[3];

		$result = [
			'job_name' => 'User Compensation Copy Creation',
			'start_time' => date('Y-m-d H:i:s'),
			'result' => false,
		];
		// check the arguments passed
		if (empty($targetUserId) || empty($sourceUcpIds)) {
			$result['log']['errors'][] = "Missing argument(s) in processCopyManyJob()";
			if (!empty($userEmail)) {
				$result['end_time'] = date('Y-m-d H:i:s');
				$this->UserCompensationProfile->sendCompletionStatusEmail($result, $userEmail);
			}
			throw new InvalidArgumentException('Missing argument(s) in processCopyManyJob()');
		}
		try {
			$processResult = $this->UserCompensationProfile->copyMany($targetUserId, $sourceUcpIds, $newPartnerIdForNewCopy, true);
		} catch (Exception $e) {
			$processResult = false;
			$result['log']['errors'][] = $e->getMessage();
		}
		$targetUserName = $this->UserCompensationProfile->User->field('fullname', ['id' => $targetUserId]);
		$result['result'] = $processResult;
		$result['recordsAdded'] = $processResult;
		if ($processResult) {
			$result['log']['optional_msgs'][] = "Successfully created " . count($sourceUcpIds) . " User Compensation Profile copies for $targetUserName.";
		} else {
			$result['log']['optional_msgs'][] = "Failed to create " . count($sourceUcpIds) . " User Compensation Profile copies for $targetUserName.";
		}
		$result['end_time'] = date('Y-m-d H:i:s');

		$this->UserCompensationProfile->sendCompletionStatusEmail($result, $userEmail);
	}
}
