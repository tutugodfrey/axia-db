<?php
/* UsersProductsRisk Fixture generated on: 2015-06-08 16:06:46 : 1433805226 */
class UsersProductsRiskFixture extends CakeTestFixture {

/**
 * Name
 *
 * @var string
 * @access public
 */
	public $name = 'UsersProductsRisk';

/**
 * Fields
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'risk_assmnt_pct' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'risk_assmnt_per_item' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_risk_assessment_merchant_index' => array('unique' => false, 'column' => 'merchant_id'),
			'user_risk_assessment_user_id_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 * @access public
 */
	public $records = array(
		array(
			'id' => '557621a9-ef88-4905-b24d-2a8134627ad4',
			'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
			'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
			'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
			'risk_assmnt_pct' => .1,
			'risk_assmnt_per_item' => .5
		),
		array(
			'id' => '557621a9-ef88-4906-b22d-2a8134627bc1',
			'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
			'user_id' => '02eb04ab-1f70-4f80-bc83-e16f9a86e764',
			'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
			'risk_assmnt_pct' => .1,
			'risk_assmnt_per_item' => .5
		),
		array(
			'id' => '557621a9-0b44-476d-a082-2a8134627ad4',
			'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
			'user_id' => '02eb04ab-1f70-4f80-bc83-e16f9a86e764',
			'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
			'risk_assmnt_pct' => .1,
			'risk_assmnt_per_item' => .5
		),
	);

}
