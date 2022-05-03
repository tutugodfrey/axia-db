<?php
class AddAccountingFieldsToMerchantAches extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_accounting_fields_to_merchant_aches';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchant_aches' => array(
					'acctg_month' => array(
						'type' => 'integer',
					),
					'acctg_year' => array(
						'type' => 'integer',
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchant_aches' => array(
					'acctg_month',
					'acctg_year'
				)
			)
		),
	);
}
