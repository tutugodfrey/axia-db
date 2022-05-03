<?php

App::uses('AppShell', 'Console/Command');

/**
 * ResidualAdmin Shell
 *
 * @package app.Console.Command
 */
class ResidualAdminShell extends AppShell {

	public $uses = array('ResidualReport', 'BackgroundJob');

/**
 * processResidualAdminJob
 *
 * @return void
 * @throws InvalidArgumentException
 * @throws Exception
 */
	public function processResidualAdminJob() {
		$response = array();

		$year = $this->args[0];
		$month = $this->args[1];
		$product = $this->args[2];
		$datafile = $this->args[3];
		$userEmail = $this->args[4];
		$jobTrackerId = $this->args[5];
		$result = [
			'job_name' => 'Residual Report Admin',
			'start_time' => date('Y-m-d H:i:s'),
			'result' => false,
		];
		// check the arguments passed
		if (empty($year) || empty($month) || empty($product) || empty($datafile)) {
			$result['log']['errors'][] = "Missing argument(s) in processResidualAdminJob()";
			if (!empty($userEmail)) {
				$this->ResidualReport->sendCompletionStatusEmail($result, $userEmail);
				$this->BackgroundJob->finished($jobTrackerId, false);
			}
			throw new InvalidArgumentException('Missing argument(s) in processResidualAdminJob()');
		}

		// check if data already exists
		// this is necessary in case a job is accidentally initiated by the user twice (or more) for the same date and product
		// the first will be run while subsequent queued duplicates will be checked and execution will be avoided
		$reportExists = $this->ResidualReport->dataExists($datafile, $year, $month, $product['ProductsServicesType']['id']);

		if ($reportExists) {
			$error = "Residual Report data already exists for some or all merchants in the provided CSV file, and the date {$month}/{$year} and product {$product['ProductsServicesType']['products_services_description']}.";
			$result['log']['errors'][] = $error;
			$this->ResidualReport->sendCompletionStatusEmail($result, $userEmail);
			$this->BackgroundJob->finished($jobTrackerId, false);
			throw new Exception($error);
		}
		$this->BackgroundJob->inProgress($jobTrackerId);
		$result = $this->ResidualReport->add($year, $month, $product, $datafile, $userEmail);
		$this->ResidualReport->sendCompletionStatusEmail($result, $userEmail);
		$successful = (empty(Hash::get($result, 'log.errors')) && $result['result']);
		$this->BackgroundJob->finished($jobTrackerId, $successful);
	}
}
