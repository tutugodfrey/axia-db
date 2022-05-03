<?php
App::uses('ShippingTypeItem', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ShippingTypeItem Test Case
 *
 */
class ShippingTypeItemTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ShippingTypeItem = ClassRegistry::init('ShippingTypeItem');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ShippingTypeItem);
		parent::tearDown();
	}

/**
 * testGetShippingTypesList
 *
 * @covers ShippingTypeItem::getShippingTypesList()
 * @return void
 */
	public function testGetShippingTypesList() {
		$actual = $this->ShippingTypeItem->getShippingTypesList();
		$expected = ['00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor sit amet'];
		$this->assertSame($expected, $actual);
	}
}
