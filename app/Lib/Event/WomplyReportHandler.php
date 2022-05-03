<?php

App::uses('CakeEventListener', 'Event');

class WomplyReportHandler extends CakeObject implements CakeEventListener {

/**
 * MAR_SUBJECT
 *
 * @var MAR_SUBJECT
 */
	const MAR_SUBJECT = 'MerchantChange';

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Model.MerchantChange.editChange' => 'initMAR',
		);
	}

/**
 * initMAR
 * Initiates Merchant Action Report Background Job IFF last report was created within the last 24hours as requied by Womply.
 *
 * The $event->subject is the class triggering events about itself
 *
 * @param object $event Event
 * @return bool
 */
	public function initMAR($event) {
		if ($event->subject === self::MAR_SUBJECT && $event->name() !== 'Model.editChange') {
			return false; //unknown/unimplemented event trigger
		}

		$WomplyMerchantLog = ClassRegistry::init('WomplyMerchantLog');

		//We can infer when the last report was sent based on the last womply log date.
		$lastCreation = $WomplyMerchantLog->find('all', [
			'fields' => ['WomplyMerchantLog.created'],
			'order' => ['WomplyMerchantLog.created DESC'],
			'limit' => 1
		]);

		$now = date_create(date('Y-m-d H:i:s'));
		$lastCreation = date_create(Hash::get($lastCreation, '0.WomplyMerchantLog.created'));
		//Use difference as negative
		$interval = $now->diff($lastCreation);
		$daysFromNow = (int)$interval->format("%R%a");
		$hoursFromNow = (int)$interval->format("%R%H");

		$frequency = -1 * Configure::read('Womply.MAR.sendFrequency');
		//if last log creation was -12 hours from now or or -1 day from now
		if ($daysFromNow <= -1 || $hoursFromNow <= $frequency) {
			//initiate job
			CakeResque::enqueue(
				'womplyQueue',
				'WomplyShell',
				array('sendMARJob')
			);
		}
	}

}
