<?php
class CreateProfitabilityReportTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_profitability_report_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'profitability_reports' => array(
					'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
					'merchant_id' => array('type' => 'uuid', 'null' => false),
					'year' => array('type' => 'integer'),
					'month' => array('type' => 'integer'),
					'net_sales_vol' => array('type' => 'number'),
					'net_sales_item_count' => array('type' => 'integer'),
					'gross_sales_vol' => array('type' => 'number'),
					'gross_sales_item_count' => array('type' => 'integer'),
					'card_brand_cogs' => array('type' => 'number'),
					'processor_cogs' => array('type' => 'number'),
					'sponsor_bank_cogs' => array('type' => 'number'),
					'ithree_monthly_cogs' => array('type' => 'number'),
					'axia_net_income' => array('type' => 'number'),
					'axia_gross_profit' => array('type' => 'number'),
					'axia_net_profit' => array('type' => 'number'),
					'cost_of_goods_sold' => array('type' => 'number'),
					'total_income' => array('type' => 'number'),
					'total_residual_comp' => array('type' => 'number'),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'PROFITABILITY_REPORT_MERCHANT_ID_INDEX' => array(
							'column' => 'merchant_id',
						)
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'profitability_reports'
			)
		),
	);
}
