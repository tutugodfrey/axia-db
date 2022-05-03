<?php
class UpdateMerchantAchBillingOptionsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_merchant_ach_billing_options_table';

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
		$AchBillOptns = $this->generateModel('MerchantAchBillingOption');
		if ($direction === 'up') {
			$id = $AchBillOptns->field('id', ['billing_option_description' => 'Bill Rep']);
			$data = [
				[
					'billing_option_description' => 'Client',
				],
				[
					'billing_option_description' => 'Partner',
				],
			];
			if (!$AchBillOptns->save(['id' => $id, 'billing_option_description' => 'Rep'])) {
				throw new Exception('Failed to save!');
			}
			if (!$AchBillOptns->saveMany($data)) {
				throw new Exception('Failed to saveMany');
			}
		} else {
			$id = $AchBillOptns->field('id', ['billing_option_description' => 'Rep']);
			$AchBillOptns->deleteAll(['billing_option_description' => 'Client'], false);
			$AchBillOptns->deleteAll(['billing_option_description' => 'Partner'], false);
			if (!$AchBillOptns->save(['id' => $id, 'billing_option_description' => 'Bill Rep'])) {
				throw new Exception('Failed to save!');
			}
		}
		
		return true;
	}
}
