<?php
App::uses('AddressType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AddressType Test Case
 *
 */
class AddressTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AddressType = ClassRegistry::init('AddressType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AddressType);
		parent::tearDown();
	}

/**
 * testGetAllAddressTypeIds
 *
 * @return void
 */
	public function testGetAllAddressTypeIds() {
		$expected = [
			'business_address' => '795f442e-04ab-43d1-a7f8-f9ba685b90ac',
			'bank_address' => '63494506-9aed-4d7c-b83c-898e6254ff20',
			'corp_address' => '1c7c0709-86df-4643-8f7f-78bd28259008',
			'mail_address' => 'edf125da-8592-42e3-bd26-ff0614bd27ba',
			'owner_address' => '31e277ff-423a-4af8-9042-8310d7c320df'
		];
		$actual = $this->AddressType->getAllAddressTypeIds();
		$this->assertEquals($expected, $actual);
	}
}
