<?php
App::uses('Vendor', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Vendor Test Case
 *
 */
class VendorTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Vendor = ClassRegistry::init('Vendor');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Vendor);
		parent::tearDown();
	}

/**
 * testGetVendorsList
 *
 * @covers Vendor::getVendorsList
 * testGetVendorsList method
 *
 * @return void
 */
	public function testGetVendorsList() {
		$result = $this->Vendor->getVendorsList();
		$expected = [
			'00000000-0000-0000-0000-000000000001' => 'vendor-1',
			'55cc6447-2418-4924-8f8b-13d834627ad4' => 'vendor-2',
			'55cc6447-2d14-442b-95c0-13d834627ad4' => 'vendor-3',
			'55cc6447-3548-4d42-b428-13d834627ad4' => 'vendor-4',
			'55cc6447-3d7c-41d6-9044-13d834627ad4' => 'vendor-5',
			'55cc6447-454c-4d24-8ca0-13d834627ad4' => 'vendor-6',
			'55cc6447-4d1c-47ff-8cca-13d834627ad4' => 'vendor-7',
			'55cc6447-5550-4eb0-bea4-13d834627ad4' => 'vendor-8',
			'55cc6447-5d20-439b-b075-13d834627ad4' => 'vendor-9',
			'55cc6447-6554-41b5-b0b5-13d834627ad4' => 'vendor-10'
		];

		$this->assertEquals($expected, $result);
	}
}