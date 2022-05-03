<?php
class CreateProfitProjectionsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_profit_projections_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'profit_projections' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary'
					),
					'merchant_id' => array(
						'type' => 'uuid',
						'null' => false,
					),
					'products_services_type_id' => array(
						'type' => 'uuid',
						'null' => false,
					),
					'rep_gross_profit' => array(
						'type' => 'number',
					),
					'rep_profit_amount' => array(
						'type' => 'number',
					),
					'axia_profit_amount' => array(
						'type' => 'number',
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'PROFIT_PROJECTIONS_MERCHANT_ID_IDX' => array(
							'column' => 'merchant_id',
						),
						'PROFIT_PROJECTIONS_PRODUCT_ID_IDX' => array(
							'column' => 'products_services_type_id',
						),
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'profit_projections'
			)
		),
	);
}
