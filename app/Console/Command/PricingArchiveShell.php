<?php
/**
 * PricingArchiveShell file
 *
 * @author Oscar Mota <omota@axiapayments.com>
 */

App::uses('AppShell', 'Console/Command');

/**
 * PricingArchive Shell
 *
 * This shell is used to background lengthy MerchantPricingArchive jobs
 *
 * @package       app.Console.Command
 */
class PricingArchiveShell extends AppShell {

	public $uses = array('MerchantPricingArchive', 'BackgroundJob');

/**
 * Main Archive job
 *
 * @return void
 * @throws \InvalidArgumentException
 * @throws \Exception
 */
	public function processArchiveJob() {
		// Check the arguments passed
		$producsToArchive = Hash::get($this->args, '0');
		$month = Hash::get($this->args, '1');
		$year = Hash::get($this->args, '2');
		$email = Hash::get($this->args, '3');
		$jobTrackerId = Hash::get($this->args, '4');
		$result = [
			'job_name' => 'Merchant Pricing Archive',
			'start_time' => date('Y-m-d H:i:s'),
			'result' => false,
		];
		// when queuing the job via $this->args
		if ((empty($producsToArchive) || empty($month) || empty($year)) || (!is_numeric($year) || !is_numeric($month))) {
			$result['log']['errors'][] = 'Missing or invalid argument type in archiveMerchantProductsPricingData.';
			$this->MerchantPricingArchive->sendCompletionSatusEmail($result, $email);
			$this->BackgroundJob->finished($jobTrackerId, false);
			throw new InvalidArgumentException('Missing or invalid argument type in archiveMerchantProductsPricingData.');
		}
		/* This is necessary in case a job is accidentally initiated by the user twice (or more) for the same archive date.
		 * The first will be run while subsequent queued duplicates will be checked and execution will be avoided.
		*/
		if ($this->MerchantPricingArchive->archiveExists($year, $month, $producsToArchive)) {
			$result['log']['errors'][] = "Archive already exists for date $month/$year and Product Id(s): " . implode(', ', $this->args[0]);
			$this->MerchantPricingArchive->sendCompletionSatusEmail($result, $email);
			$this->BackgroundJob->finished($jobTrackerId, false);
			throw new Exception("Archive already exists for Product Id: {$this->args[0]} and date {$month}/{$year}.");
		}
		$this->BackgroundJob->inProgress($jobTrackerId);
		$this->MerchantPricingArchive->saveNewArchives($this->args[0], $month, $year, $email, $jobTrackerId);
	}
}
