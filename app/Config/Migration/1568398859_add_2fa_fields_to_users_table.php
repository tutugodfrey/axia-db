<?php
class Add2FAFieldsToUsersTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_2FA_fields_to_users_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'secret' => array('type' => 'string', 'length' => '100'),
					'opt_out_2fa' => array('type' => 'boolean', 'default' => false)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'users' => array(
					'secret',
					'opt_out_2fa'
				)
			)
		),
	);
}
