<?php
/**
 * ResidualTimeParameterFixture
 *
 */
class ResidualTimeParameterFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'residual_parameter_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'associated_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'type' => array('type' => 'string', 'null' => true, 'default' => null),
		'value' => array('type' => 'decimal', 'null' => true, 'default' => null),
		'is_multiple' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'tier' => array('type' => 'integer', 'null' => false, 'default' => '1'),
		'user_compensation_profile_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'residual_time_parameters_associated_user_id_idx' => array('unique' => false, 'column' => 'associated_user_id'),
			'residual_time_parameters_products_services_type_id_idx' => array('unique' => false, 'column' => 'products_services_type_id'),
			'residual_time_parameters_residual_parameter_type_id_idx' => array('unique' => false, 'column' => 'residual_parameter_type_id')
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
			'id' => '5612b540-6d04-4108-a936-0d73c0a81cd6',
			'residual_parameter_type_id' => '1',
			'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
			'associated_user_id' => '',
			'type' => null,
			'value' => '5',
			'is_multiple' => 0,
			'created' => '2015-10-05 10:37:04',
			'modified' => '2015-10-06 14:24:02',
			'tier' => 4,
			'user_compensation_profile_id' => '55db8591-1bc8-4336-b478-130b34627ad4'
		),
		array(
			'id' => '5612b540-8df0-410c-ba33-0d73c0a81cd6',
			'residual_parameter_type_id' => '2',
			'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
			'associated_user_id' => '',
			'type' => null,
			'value' => '5',
			'is_multiple' => 1,
			'created' => '2015-12-18 11:28:09',
			'modified' => '2015-12-18 11:28:09',
			'tier' => 1,
			'user_compensation_profile_id' => '55db8591-1bc8-4336-b478-130b34627ad4'
		),
		array(
			'id' => '5612b540-bf7c-47f5-bb31-0d73c0a81cd6',
			'residual_parameter_type_id' => '2',
			'products_services_type_id' => '551038c4-3370-47c3-bb4c-1a3f34627ad4',
			'associated_user_id' => '',
			'type' => null,
			'value' => '0',
			'is_multiple' => 1,
			'created' => '2015-10-05 10:37:04',
			'modified' => '2015-10-06 14:24:02',
			'tier' => 1,
			'user_compensation_profile_id' => '55db8591-1bc8-4336-b478-130b34627ad4'
		),
	);

}
