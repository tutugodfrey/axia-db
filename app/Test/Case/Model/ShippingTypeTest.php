<?php
App::uses('ShippingType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ShippingType Test Case
 *
 */
class ShippingTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ShippingType = ClassRegistry::init('ShippingType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ShippingType);
		parent::tearDown();
	}

/**
 * testGetShippingTypesList
 *
 * @return void
 */
	public function testGetShippingTypesList() {
		$expected = [
			'00000000-0000-0000-0000-000000000001' => 'shipping-type-1',
			'00000000-0000-0000-0000-000000000002' => 'shipping-type-2',
			'00000000-0000-0000-0000-000000000003' => 'shipping-type-3',
		];
		$this->assertEquals($expected, $this->ShippingType->getShippingTypesList());
	}
}
