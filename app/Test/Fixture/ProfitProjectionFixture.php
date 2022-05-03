<?php
/**
 * ProfitProjection Fixture
 */
class ProfitProjectionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'uuid', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'uuid', 'null' => false, 'default' => null, 'length' => 36),
		'products_services_type_id' => array('type' => 'uuid', 'null' => false, 'default' => null, 'length' => 36),
		'rep_gross_profit' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'rep_profit_amount' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'axia_profit_amount' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'profit_projections_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'profit_projections_product_id_idx' => array('unique' => false, 'column' => 'products_services_type_id')
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
			'products_services_type_id' => '',
			'rep_gross_profit' => '',
			'rep_profit_amount' => '',
			'axia_profit_amount' => ''
		),
	);

}
