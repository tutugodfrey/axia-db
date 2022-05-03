<?php
class CreateNewApiConfigurationsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_new_api_configurations_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'api_configurations' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary'
					),
					'configuration_name' => array(
						'type' => 'string',
						'length' => '150',
						'null' => false,
					),
					'auth_type' => array(
						'type' => 'string',
						'length' => '150',
					),
					'instance_url' => array(
						'type' => 'string',
						'length' => '250',
					),
					'authorization_url' => array(
						'type' => 'string',
						'length' => '250',
					),
					'access_token_url' => array(
						'type' => 'string',
						'length' => '250',
					),
					'redirect_url' => array(
						'type' => 'string',
						'length' => '250',
					),
					'client_id' => array(
						'type' => 'string',
						'length' => '250',
					),
					'client_secret' => array(
						'type' => 'string',
						'length' => '250',
					),
					'access_token' => array(
						'type' => 'string',
						'length' => '250',
					),
					'access_token_lifetime_hours' => array(
						'type' => 'integer',
					),
					'refresh_token' => array(
						'type' => 'string',
						'length' => '250',
					),
					'issued_at' => array(
						'type' => 'string',
						'length' => '100',
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'API_CONFIG_NAME' => array(
							'column' => 'configuration_name',
							'unique' => 1
						),
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'api_configurations'
			)
		),
	);
}
