<?php
class SetMerchantAchesCompleteDate extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'set_merchant_aches_complete_date';

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
			$MerchantAch = $this->generateModel('MerchantAch');
			$MerchantAch->updateAll(
				['date_completed' => 'ach_date'],
				['status' => 'COMP', 'date_completed IS NULL']
			);
		}
		return true;
	}
}
