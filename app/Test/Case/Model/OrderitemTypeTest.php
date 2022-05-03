<?php
App::uses('OrderitemType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * OrderitemType Test Case
 *
 * @coversDefaultClass OrderitemType
 */
class OrderitemTypeTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var OrderitemType
 */
	public $OrderitemType;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->OrderitemType = ClassRegistry::init('OrderitemType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->OrderitemType);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers OrderitemType::getItemTypesList()
 * @return void
 */
	public function testGetItemTypesList() {
		$result = $this->OrderitemType->getItemTypesList();
		$expected = [
			'24614a81-bde0-49a5-a234-2a9dabdfcf66' => 'New Order',
			'4d165bc9-71bb-4ebc-98b5-d805589a7a74' => 'Replacement',
			'0d16d8b7-e155-45d3-891e-90383f9d5a3f' => 'Refurbished',
		];
		$this->assertEquals($expected, $result);
	}
}
