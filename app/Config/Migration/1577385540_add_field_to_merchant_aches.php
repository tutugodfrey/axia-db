<?php
class AddFieldToMerchantAches extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_field_to_merchant_aches';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchant_aches' => array(
					'related_foreign_inv_number' => array(
						'type' => 'string',
						'length' => '100',
						'default' => null,
						'null' => true
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchant_aches' => array(
					'related_foreign_inv_number'
				)
			)
		),
	);

}
