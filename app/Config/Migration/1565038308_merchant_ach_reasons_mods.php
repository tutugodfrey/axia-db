<?php
App::uses('MerchantAchReason', 'Model');
class MerchantAchReasonsMods extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'merchant_ach_reasons_mods';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchant_ach_reasons' => array(
					'non_taxable_reason_id' => array('type' => 'uuid')
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchant_ach_reasons' => array(
					'non_taxable_reason_id'
				)
			)
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
			$MerchantAchReason = $this->generateModel('MerchantAchReason');
			$MerchantAch = $this->generateModel('MerchantAch');
			$InvoiceItem = $this->generateModel('InvoiceItem');
			$MerchantAchReason->query("ALTER TABLE merchant_ach_reasons DROP COLUMN IF EXISTS taxable");

			$InvoiceItem->updateAll(
				['merchant_ach_reason_id' => "'" . MerchantAchReason::EQ_FEE . "'"],
				['merchant_ach_reason_id' => MerchantAchReason::EQ_CA]
			);
			$MerchantAchReason->delete(MerchantAchReason::EQ_CA, false);
		}
		return true;
	}
}
