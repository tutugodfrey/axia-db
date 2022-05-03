<?php
App::uses('MerchantAchReason', 'Model');
class AddColumnToMerchantAchReasons extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_to_merchant_ach_reasons';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchant_ach_reasons' => array(
					'accounting_report_col_alias' => array('type' => 'string', 'length' => 100)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchant_ach_reasons' => array(
					'accounting_report_col_alias'
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
			$acSetup = $MerchantAchReason->find('first', ['fields'=> ['id'], 'conditions' => ['reason' => 'Account Setup Fee']]);
			$replShipFee = $MerchantAchReason->find('first', ['fields'=> ['id'], 'conditions' => ['reason' => 'Replacement Shipping']]);
			$expedite = $MerchantAchReason->find('first', ['fields'=> ['id'], 'conditions' => ['reason' => 'Expedite Fee']]);
			if (empty($acSetup['MerchantAchReason']['id']) || empty($replShipFee['MerchantAchReason']['id']) || empty($expedite['MerchantAchReason']['id'])) {
				throw new Exception ('Unable to find Merchant AchReson');
			}
			$map = [
				'ach_setups' => [MerchantAchReason::ACH_SETUP],
				'app_fees' => [MerchantAchReason::APP_FEE],
				'equip_repair_fees' => [MerchantAchReason::REINJECT_FEE],
				'equip_sales' => [
					MerchantAchReason::EQ_CA,
					MerchantAchReason::EQ_FEE,
					MerchantAchReason::SUP_CA,
					MerchantAchReason::SUP_OUT
				],
				'replace_fees' => [MerchantAchReason::REPLACEMENT],
				'expedite_fees' => [$expedite['MerchantAchReason']['id']],
				'software_setup_fees' => [MerchantAchReason::SOFTWARE],
				'licence_setup_fees' => [MerchantAchReason::PF_TERM_SETUP_FEE],
				'account_setup_fees' => [$acSetup['MerchantAchReason']['id']],
				'client_implement_fees' => [MerchantAchReason::INSTALL],
				'termination_fees' => [MerchantAchReason::CNL],
				'shipping_fees' => [MerchantAchReason::EXACT_SHIP],
				'replacement_shipping_fees' => [$replShipFee['MerchantAchReason']['id']],
				'reject_fees' => [MerchantAchReason::REJECT],
				'rental_fees' => [MerchantAchReason::RENTALS_CA, MerchantAchReason::RENTALS_OUT],
				'misc_fees' => [MerchantAchReason::REASON_OTHER],
			];
			foreach($map as $colAlias => $ids) {
				if(!$MerchantAchReason->updateAll(['accounting_report_col_alias' => "'$colAlias'"], ['id' => $ids])) {
					throw new Exception ('Unexpected Error! Update failed');
				}
			}

		}
		return true;
	}
}
