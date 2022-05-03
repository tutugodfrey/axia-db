<?php
class AddFailedLoginInCountFieldUsersTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_failed_login_in_count_field_users_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'create_field' => array(
                'users' => array(
                    'wrong_log_in_count' => array('type' => 'integer', 'default' => 0)
                )
            )
		),
		'down' => array(
            'drop_field' => array(
                'users' => array(
                    'wrong_log_in_count'
                )
            )
		),
	);

}
