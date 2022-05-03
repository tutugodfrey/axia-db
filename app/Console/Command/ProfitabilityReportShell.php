<?php

App::uses('AppShell', 'Console/Command');

/**
 * ProfitabilityReport Shell
 *
 * @package app.Console.Command
 */
class ProfitabilityReportShell extends AppShell {

	public $uses = array('ProfitabilityReport');

/**
 * beginImport
 *
 * @return void
 * @throws InvalidArgumentException
 * @throws Exception
 */
	public function beginImport() {
		//Set args as vars
		$year = $this->args[0];
		$month = $this->args[1];
		$datafile = $this->args[2];
		$userEmail = $this->args[3];
		$result = [
			'job_name' => 'Profitability Data Import',
			'start_time' => date('Y-m-d H:i:s'),
			'result' => false,
		];
		// check the arguments passed
		if (empty($year) || empty($month)) {
			$result['log']['errors'][] = "Missing argument(s) in beginImport()";
			if (!empty($userEmail)) {
				$this->ProfitabilityReport->sendCompletionStatusEmail($result, $userEmail);
			}
			throw new InvalidArgumentException('Missing argument(s) in beginImport()');
		}

		// check if data already exists
		// this is necessary in case a job is accidentally initiated by the user twice (or more) for the same date and product
		// the first will be run while subsequent queued duplicates will be checked and execution will be avoided
		$reportExists = $this->ProfitabilityReport->hasAny(['year' => $year, 'month' => $month]);

		if ($reportExists) {
			$error = "Profitability Report data already exists for $month $year.";
			$result['log']['errors'][] = $error;
			$this->ProfitabilityReport->sendCompletionStatusEmail($result, $userEmail);
			unlink($datafile);
			throw new Exception($error);
		}

		$result = $this->ProfitabilityReport->processDataFile($year, $month, $datafile);
		$this->ProfitabilityReport->sendCompletionStatusEmail($result, $userEmail);
	}
}
