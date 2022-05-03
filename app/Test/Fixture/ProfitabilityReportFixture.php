<?php
/**
 * ProfitabilityReport Fixture
 */
class ProfitabilityReportFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'uuid', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'uuid', 'null' => false, 'default' => null, 'length' => 36),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null),
		'month' => array('type' => 'integer', 'null' => true, 'default' => null),
		'net_sales_vol' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'net_sales_item_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'gross_sales_vol' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'gross_sales_item_count' => array('type' => 'integer', 'null' => true, 'default' => null),
		'card_brand_cogs' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'processor_cogs' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'sponsor_bank_cogs' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'ithree_monthly_cogs' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'axia_net_income' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'axia_gross_profit' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'axia_net_profit' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'cost_of_goods_sold' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'total_income' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'profitability_report_merchant_id_index' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '',
			'merchant_id' => '',
			'year' => 1,
			'month' => 1,
			'net_sales_vol' => '',
			'net_sales_item_count' => 1,
			'gross_sales_vol' => '',
			'gross_sales_item_count' => 1,
			'card_brand_cogs' => '',
			'processor_cogs' => '',
			'sponsor_bank_cogs' => '',
			'ithree_monthly_cogs' => '',
			'axia_net_income' => '',
			'axia_gross_profit' => '',
			'axia_net_profit' => '',
			'cost_of_goods_sold' => '',
			'total_income' => ''
		),
	);

}
