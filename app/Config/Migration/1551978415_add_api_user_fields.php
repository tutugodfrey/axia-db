<?php
class AddApiUserFields extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_api_user_fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => [
					'access_token' => array('type' => 'string', 'length' => 40),
					'api_password' => array('type' => 'string', 'length' => 200),
					'api_last_request' => array('type' => 'datetime'),
				]
			)
		),
		'down' => array(
			'drop_field' => array(
				'users' => [
					'access_token',
					'api_password',
					'api_last_request',
				]
			)
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
