<?php
App::uses('EquipmentProgramming', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * EquipmentProgramming Test Case
 *
 */
class EquipmentProgrammingTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EquipmentProgramming = ClassRegistry::init('EquipmentProgramming');
		$this->_mockSystemTransactionListener($this->EquipmentProgramming);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EquipmentProgramming);
		parent::tearDown();
	}

/**
 * testGetAllEquipmentProgrammingByMerchantId method
 *
 * @return void
 */
	public function testGetAllEquipmentProgrammingByMerchantId() {
		$actual = $this->EquipmentProgramming->getAllEquipmentProgrammingByMerchantId('00000000-9999-0000-0000-000000000001');
		$this->assertEmpty($actual);
		$merchantId = '00000000-0000-0000-0000-000000000003';
		$actual = $this->EquipmentProgramming->getAllEquipmentProgrammingByMerchantId($merchantId);
		$this->assertCount(2, $actual);
		$this->assertCount(2, Hash::extract($actual, "{n}.EquipmentProgramming[merchant_id={$merchantId}]"));
		$expected = [
			'00000000-0000-0000-0000-000000000002',
			'00000000-0000-0000-0000-000000000003',
		];
		$this->assertEquals($expected, Hash::extract($actual, '{n}.EquipmentProgramming.id'));
	}

/**
 * testFindEquipmentProgrammingById method
 *
 * @return void
 */
	public function testFindEquipmentProgrammingById() {
		$actual = $this->EquipmentProgramming->findEquipmentProgrammingById('00000000-9999-0000-0000-000000000001');
		$this->assertFalse($actual);
		$actual = $this->EquipmentProgramming->findEquipmentProgrammingById('00000000-0000-0000-0000-000000000001');
		$this->assertEquals('00000000-0000-0000-0000-000000000001', Hash::get($actual, 'EquipmentProgramming.id'));
		$this->assertCount(2, Hash::extract($actual, 'EquipmentProgrammingTypeXref'));
	}
}
