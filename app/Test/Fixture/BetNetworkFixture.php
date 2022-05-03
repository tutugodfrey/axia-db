<?php
/**
 * BetNetworkFixture
 *
 */
class BetNetworkFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'is_active' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
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
			'id' => '55104e48-942c-44e4-8aec-225e34627ad4',
			'name' => 'TSYS',
			'is_active' => 1
		),
		array(
			'id' => '55104e48-4590-42e2-8cc4-225e34627ad4',
			'name' => 'JetPay',
			'is_active' => 1
		),
		array(
			'id' => '55104e48-fb74-4bdf-8412-225e34627ad4',
			'name' => 'TSYS JetPay',
			'is_active' => 1
		),
		array(
			'id' => '55104e48-ab7c-407a-a0c5-225e34627ad4',
			'name' => 'TSYS (JetPay Legacy)',
			'is_active' => 1
		),
		array(
			'id' => '55104e48-4e3c-4799-b54a-225e34627ad4',
			'name' => 'TSYS Sage',
			'is_active' => 1
		),
		array(
			'id' => '55104e48-e5a8-4359-bc09-225e34627ad4',
			'name' => 'Sage PTI',
			'is_active' => 1
		),
		array(
			'id' => '55104e48-7c4c-44da-bf83-225e34627ad4',
			'name' => 'Direct Connect',
			'is_active' => 1
		),
		array(
			'id' => '55104e4a-1714-4bb5-bdf2-225e34627ad4',
			'name' => 'TSYS (Sage Legacy)',
			'is_active' => 1
		),
		array(
			'id' => '55104e4a-f8b8-47be-9488-225e34627ad4',
			'name' => 'Pivotal',
			'is_active' => 1
		),
	);

}
