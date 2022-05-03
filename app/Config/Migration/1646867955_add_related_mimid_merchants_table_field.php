<?php
class AddRelatedMimidMerchantsTableField extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_related_mimid_merchants_table_field';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'create_field' => array(
                'merchants' => array(
                    'related_acquiring_mid' => array('type' => 'string', 'length' => 20, 'default' => null)
                )
            )
		),
		'down' => array(
            'drop_field' => array(
                'merchants' => array(
                    'related_acquiring_mid' 
                )
            )
		),
	);
}
