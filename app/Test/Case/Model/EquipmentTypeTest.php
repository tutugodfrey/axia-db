<?php
App::uses('EquipmentType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * EquipmentType Test Case
 *
 */
class EquipmentTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EquipmentType = ClassRegistry::init('EquipmentType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EquipmentType);
		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testAfterSaveCreate() {
		$actual = $this->EquipmentType->getEquipmentTypesIDsList();
		$expected = [
			'Equipment type HW' => '00000000-0000-0000-0000-000000000001',
			'Equipment type 2' => '00000000-0000-0000-0000-000000000002',
			'Equipment type 3' => '00000000-0000-0000-0000-000000000003'
		];
		$this->assertEquals($expected, $actual);
	}
}
