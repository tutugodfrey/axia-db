<?php
class AddExternalForeignIdField extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_external_foreign_id_field';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'associated_external_records' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary'
					),
					'external_system_name' => array('type' => 'string', 'length' => 200, 'null' => false),
					'external_id' => array('type' => 'string', 'length' => 36, 'null' => false),
					'merchant_id' => array('type' => 'uuid', 'null' => false),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'ASSOC_EXT_REC_MERCHANT_ID' => array(
							'column' => 'merchant_id',
						),
						'ASSOC_EXT_REC_EXTERNAL_ID' => array(
							'column' => 'external_id',
							'unique' => 1
						),
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'associated_external_records'
			)
		),
	);
}
