<?php
class AddColumnLastDepostReportTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_last_depost_report_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'last_deposit_reports' => array(
					'sales_num' => array(
						'type' => 'integer',
						'defalut' => null,
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'last_deposit_reports' => array(
					'sales_num'
				)
			)
		),
	);
}
