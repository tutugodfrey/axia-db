<?php
App::uses('TimelineItem', 'Model');
class CopyApprovalDateFromTimeEntryToUw extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'copy_approval_date_from_time_entry_to_uw';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'up') {
			$UwStatus = $this->generateModel('UwStatus');
			$TimelineEntry = $this->generateModel('TimelineEntry');
			$UwStatusMerchantXref = $this->generateModel('UwStatusMerchantXref');
			$tiApprovedId = TimelineItem::APPROVED;
			$uwStatusApprovedId = $UwStatus->field('id', ['name' => 'Approved']);

			//list of merchants that need the approval date copied from timeline entry to uw_status_merchant_xrefs
			$list = $TimelineEntry->find('all', [
				'fields' => [
					'TimelineEntry.merchant_id',
					'TimelineEntry.timeline_date_completed',
				],
				'conditions' => [
					'TimelineEntry.timeline_item_id' => $tiApprovedId,
					'UwStatusMerchantXref.datetime IS NULL'
				],
				'joins' => [
					[
						'table' => 'uw_status_merchant_xrefs',
						'alias' => 'UwStatusMerchantXref',
						'type' => 'LEFT',
						'conditions' => [
							'TimelineEntry.merchant_id = UwStatusMerchantXref.merchant_id',
							"UwStatusMerchantXref.uw_status_id = '$uwStatusApprovedId'"
						]
					]
				]
			]);
			$data = [];
			if (!empty($list)) {
				foreach ($list as $timeEntry) {
					$data[] = [
						'merchant_id' => $timeEntry['TimelineEntry']['merchant_id'],
						'uw_status_id' => $uwStatusApprovedId,
						'datetime' => $timeEntry['TimelineEntry']['timeline_date_completed'],
						'notes' => 'Date copied from Timeline in a Mass update done on ' . date("M jS Y"),
					];
				}
				$UwStatusMerchantXref->useTable = 'uw_status_merchant_xrefs';
				if (!$UwStatusMerchantXref->saveMany($data, ['validate' => false])) {
					echo "Failed to save updates!\n";
					return false;
				}
			}
		}
		return true;
	}
}
