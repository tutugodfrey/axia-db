<?php
class CreateUserBankingFields extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_user_banking_fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'bank_name' => array(
						'type' => 'string',
						'length' => 60
					),
					'routing_number' => array(
						'type' => 'string',
						'length' => 255
					),
					'account_number' => array(
						'type' => 'string',
						'length' => 255
					),
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'users' => array(
					'bank_name',
					'routing_number',
					'account_number',
				)
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
		if($direction === 'up') {
			$User = ClassRegistry::init('User');
			$User->query("update users set user_last_name = btrim(regexp_replace(user_last_name, '\(partner\)', '','i'), ' ') where user_last_name ilike '%partner%'");
			$User->query("update users set user_last_name = btrim(regexp_replace(user_last_name, 'partner', '','i'), ' ') where user_last_name ilike '%partner'");
		}
		return true;
	}
}
