<?php
class AddFieldToGateway1Table extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_field_to_gateway1_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'gateway1s' => array(
					'addl_rep_statement_cost' => array(
						'type' => 'number'
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'gateway1s' => array(
					'addl_rep_statement_cost'
				)
			)
		),
	);
}
