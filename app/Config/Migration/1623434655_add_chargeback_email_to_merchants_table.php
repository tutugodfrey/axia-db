<?php
class AddChargebackEmailToMerchantsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_chargeback_email_to_merchants_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchants' => array(
					'chargebacks_email' => array(
						'type' => 'string',
						'length' => 50,
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchants' => array(
					'chargebacks_email'
				)
			)
		),
	);
}
