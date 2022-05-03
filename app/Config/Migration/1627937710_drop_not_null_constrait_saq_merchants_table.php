<?php
class DropNotNullConstraitSaqMerchantsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'drop_not_null_constrait_saq_merchants_table';

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
        $SaqMerchant = ClassRegistry::init('SaqMerchant');
        $SaqMerchant->query('alter table saq_merchants alter COLUMN password drop not null');
		return true;
	}
}
