<?php
class CreateClientsTableAndFields extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_clients_table_and_fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'clients' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary',
					),
					'client_id_global' => array(
						'type' => 'integer',
						'null' => false,
					),
					'client_name_global' => array(
						'type' => 'string',
						'length' => '100',
						'null' => false,
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'UNIQUE_GLOBAL_CLIENT_ID_IDX' => array(
							'column' => 'client_id_global',
							'unique' => true
						)
					)
				)
			),
			'create_field' => array(
				'merchants' => array(
					'client_id' => array(
						'type' => 'uuid',
						'null' => true,
					),
				),
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchants' => array(
					'client_id',
				),				
			),
			'drop_table' => array(
				'clients'
			)
		),
	);

}
