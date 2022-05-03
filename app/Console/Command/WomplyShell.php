<?php
/**
 * WomplyShell file
 *
 * @author Oscar Mota <omota@axiapayments.com>
 */

App::uses('AppShell', 'Console/Command');

/**
 * WomplyShell Shell
 *
 * This shell is used to background Womply jobs
 *
 * @package       app.Console.Command
 */
class WomplyShell extends AppShell {

	public $uses = array('Womply');

/**
 * Womply send daily Merchat Action Report MAR
 *
 * @return void
 * @throws \InvalidArgumentException
 * @throws \Exception
 */
	public function sendMARJob() {
		try {
			$this->Womply->sendDailyMAR();
		} catch (Exception $e) {
			$emailSubject = "Axia DB Fatal Error - Automated Womply MAR Process Failed!";
			$emailBody = "The following error ocurred wile attempting to send Womply Merchant Action Report:\n";
			$emailBody .= $e->getMessage() . ".\n";
			$emailBody .= "If error was due to a connecton issue you may chose to wait until the next attempt which will ocurr in " . Configure::read('Womply.MAR.sendFrequency') . " hours from now.\n";
			$emailBody .= "Otherwise, investigation and resolution this issue will be necessary.\n";
			$this->Womply->sendWomplyEmailAlert($emailBody, $emailSubject);
		}
	}

/**
 * getParMonthlyJob
 * Get monthly Product Action Report PAR
 *
 * @return void
 * @throws \InvalidArgumentException
 * @throws \Exception
 */
	public function getParMonthlyJob() {
		$result = $this->Womply->getEmailReadyPARdata();
		$this->Womply->sendWomplyEmailAlert(
			$result['body'],
			'Womply PAR ' . date('Y-m-d'),
			$result['email'],
			$result['attachment']
		);
	}
}
