<?php

App::uses('AppShell', 'Console/Command');

/**
 * CommissionAdmin Shell
 *
 * @package app.Console.Command
 */
class CommissionAdminShell extends AppShell {

	public $uses = array('CommissionReport', 'BackgroundJob');

/**
 * processCommissionAdminJob
 *
 * @return void
 * @throws InvalidArgumentException
 * @throws Exception
 */
	public function processCommissionAdminJob() {
		$year = $this->args[0];
		$month = $this->args[1];
		$userEmail = $this->args[2];
		$jobTrackerId = $this->args[3];
		$result = [
			'job_name' => 'Commission Report Admin',
			'start_time' => date('Y-m-d H:i:s'),
			'log' => [
				'errors' => [],
				'optional_msgs' => ["Selected month/year: $month/$year" ],
			]
		];
		// check the arguments passed
		if (empty($year) || empty($month)) {
			$result['log']['errors'][] = "Missing argument(s) in processCommissionAdminJob()";
			if (!empty($userEmail)) {
				$this->BackgroundJob->finished($jobTrackerId, false);
				$this->CommissionReport->sendCompletionStatusEmail($result, $userEmail);
			}
			throw new InvalidArgumentException('Missing argument(s) in processCommissionAdminJob()');
		}

		// check if data already exists
		// this is necessary in case a job is accidentally initiated by the user twice (or more) for the same date
		// the first will be run while subsequent queued duplicates will be checked and execution will be avoided
		$reportExists = $this->CommissionReport->hasAny(array(
					'CommissionReport.c_year' => $year,
					'CommissionReport.c_month' => $month
				)
			);

		if ($reportExists) {
			$this->BackgroundJob->finished($jobTrackerId, false);
			$result['log']['errors'][] = "Commission Report data already exists for date {$month}/{$year}.";
			$this->CommissionReport->sendCompletionStatusEmail($result, $userEmail);
			throw new Exception("Commission Report data already exists for date {$month}/{$year}.");
		}
		$this->BackgroundJob->inProgress($jobTrackerId);
		$result = $this->CommissionReport->build($year, $month, $userEmail);
		$this->CommissionReport->sendCompletionStatusEmail($result, $userEmail);
		$successful = (empty(Hash::get($result, 'log.errors')) && $result['result']);
		$this->BackgroundJob->finished($jobTrackerId, $successful);
	}
}
