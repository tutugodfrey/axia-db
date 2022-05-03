<?php
App::uses('EquipmentCost', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * EquipmentCost Test Case
 *
 */
class EquipmentCostTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EquipmentCost = ClassRegistry::init('EquipmentCost');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EquipmentCost);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers EquipmentCost::__construct
 * @covers EquipmentCost::validate property
 * @return void
 */
	public function testConstructorAndValidateDefinition() {
		$EquipmentCost = $this->_mockModel('EquipmentCost');

		$expected = [
			'equipment_item_id',
			'user_compensation_profile_id',
			'true_cost',
			'rep_cost',
		];
		$this->assertEquals($expected, array_keys($EquipmentCost->validate));
		unset($EquipmentCost);
	}

/**
 * test
 *
 * @return void
 */
	public function testAdd() {
		$this->assertCount(17, $this->EquipmentCost->find('list'));

		$data = [];
		$this->assertFalse($this->EquipmentCost->add($data));
		$this->assertCount(17, $this->EquipmentCost->find('list'));

		$data = [
			'EquipmentCost' => [
				'equipment_item_id' => '00000000-0000-0000-0000-000000000001',
				'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000001',
			],
		];
		$this->assertTrue($this->EquipmentCost->add($data));
		$this->assertCount(18, $this->EquipmentCost->find('list'));
		$newRecord = $this->EquipmentCost->read(array_keys($data['EquipmentCost']), $this->EquipmentCost->id);
		$this->assertEquals($data, $newRecord);
	}

/**
 * test
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Could not save the equipmentCost, please check your inputs.
 * @return void
 */
	public function testAddValidationError() {
		$data = [
			'EquipmentCost' => [
				'equipment_item_id' => 'lorem ipsum',
				'user_compensation_profile_id' => 'dolor sit amet',
				'rep_cost' => 'not-a-valid-number'
			],
		];
		$this->EquipmentCost->add($data);
	}

/**
 * test
 *
 * @return void
 */
	public function testEdit() {
		$firstRecord = $this->EquipmentCost->find('first');
		$this->assertNotEmpty($firstRecord);

		$recordId = $firstRecord['EquipmentCost']['id'];
		$this->assertEquals($firstRecord, $this->EquipmentCost->edit($recordId, []));
		$data = [
			'EquipmentCost' => [
				'id' => $recordId,
				'user_compensation_profile_id' => $firstRecord['EquipmentCost']['user_compensation_profile_id'],
				'rep_cost' => $firstRecord['EquipmentCost']['rep_cost'] + 0.05,
			]
		];
		$this->assertCount(17, $this->EquipmentCost->find('list'));
		$this->assertTrue($this->EquipmentCost->edit($recordId, $data));
		$this->assertCount(17, $this->EquipmentCost->find('list'));

		$editedRecord = $this->EquipmentCost->read(array_keys($data['EquipmentCost']), $recordId);
		$this->assertEquals($data, $editedRecord);
	}

/**
 * test
 *
 * @return void
 */
	public function testEditValidationError() {
		$firstRecord = $this->EquipmentCost->find('first');
		$this->assertNotEmpty($firstRecord);

		$recordId = $firstRecord['EquipmentCost']['id'];
		$data = [
			'EquipmentCost' => [
				'id' => $recordId,
				'user_compensation_profile_id' => 'lorem ipsum',
				'rep_cost' => 'not-a-valid-number'
			],
		];
		$result = $this->EquipmentCost->edit($recordId, $data);
		$this->assertEquals($data, $result);
		$expected = [
			'rep_cost' => [
				'Please enter a numeric Rep Cost'
			]
		];
		$this->assertEquals($expected, $this->EquipmentCost->validationErrors);
	}

/**
 * test
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid Equipment Cost
 * @return void
 */
	public function testAddNotValid() {
		$this->EquipmentCost->edit('00000000-9999-0000-0000-000000000001', []);
	}

/**
 * test
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid Equipment Cost
 * @return void
 */
	public function testValidateAndDeleteNotValid() {
		$this->EquipmentCost->validateAndDelete('00000000-9999-0000-0000-000000000001', []);
	}

/**
 * test
 *
 * @expectedException Exception
 * @expectedExceptionMessage You need to confirm to delete this Equipment Cost
 * @return void
 */
	public function testValidateAndDeleteNotConfirm() {
		$this->EquipmentCost->validateAndDelete('5509dd2d-4730-4a42-942d-0e200a0100c1', ['EquipmentCost' => ['confirm' => 0]]);
	}

/**
 * test
 *
 * @return void
 */
	public function testValidateAndDelete() {
		$equipmentCostId = '5509dd2d-4730-4a42-942d-0e200a0100c1';
		$result = $this->EquipmentCost->validateAndDelete($equipmentCostId, []);
		$this->assertFalse($result);

		$data = ['EquipmentCost' => ['confirm' => 1]];
		$this->assertCount(17, $this->EquipmentCost->find('list'));
		$result = $this->EquipmentCost->validateAndDelete($equipmentCostId, $data);
		$this->assertTrue($result);
		$this->assertCount(16, $this->EquipmentCost->find('list'));
		$this->assertFalse($this->EquipmentCost->read(null, $equipmentCostId));
	}

/**
 * test
 *
 * @return void
 */
	public function testGetByCompProfile() {
		$expectedTrueCost = [10, 20, 30];
		$expectedRepCost = [0, 10, 25];
		$expectedPartnerCost = [null, null, null];
		$expectedDescription = ['Equipment item 1', 'Equipment item 2', 'Equipment item 3'];

		$result = $this->EquipmentCost->getByCompProfile('00000000-0000-0000-0000-000000000001');
		$this->assertCount(3, $result);
		$this->assertEquals($expectedTrueCost, Hash::extract($result, '{n}.EquipmentItem.equipment_item_true_price'));
		$this->assertEquals($expectedRepCost, Hash::extract($result, '{n}.rep_cost'));
		$this->assertEquals($expectedPartnerCost, Hash::extract($result, '{n}.partner_cost'));
		$this->assertEquals($expectedDescription, Hash::extract($result, '{n}.EquipmentItem.equipment_item_description'));

		$expectedPartnerCost = [0, 10, 25];
		$result = $this->EquipmentCost->getByCompProfile('00000000-0000-0000-0000-000000000001', AxiaTestCase::USER_SM_ID);
		$this->assertEquals($expectedTrueCost, Hash::extract($result, '{n}.EquipmentItem.equipment_item_true_price'));
		$this->assertEquals($expectedPartnerCost, Hash::extract($result, '{n}.partner_cost'));
		$this->assertEquals($expectedDescription, Hash::extract($result, '{n}.EquipmentItem.equipment_item_description'));
	}

/**
 * test
 *
 * @return void
 */
	public function testGetRepCost() {
		$equipmentId = '00000000-0000-0000-0000-000000000002';
		$profileWithCosts = '00000000-0000-0000-0000-000000000001';
		$actual = $this->EquipmentCost->getRepCost($equipmentId, $profileWithCosts);
		$expected = $this->EquipmentCost->field('rep_cost', ['equipment_item_id' => $equipmentId, 'user_compensation_profile_id' => $profileWithCosts]);
		$this->assertNotEmpty($actual);
		$this->assertEqual($expected, $actual);

		$profileWithNoCosts = '88888888-5a64-4a93-8724-888888888888';
		$actual = $this->EquipmentCost->getRepCost($equipmentId, $profileWithNoCosts);
		$this->assertFalse($actual);
	}

/**
 * test
 *
 * @return void
 */
	public function testGenerateCompData() {
		$profileWithCosts = '00000000-0000-0000-0000-000000000001';
		$equipmentCosts = $this->EquipmentCost->find('list', [
			'conditions' => ['EquipmentCost.user_compensation_profile_id' => $profileWithCosts]
		]);
		$this->assertNotEmpty($equipmentCosts);
		$this->assertNull($this->EquipmentCost->generateCompData($profileWithCosts));

		$profileWithNoCosts = '88888888-5a64-4a93-8724-888888888888';
		$equipmentCosts = $this->EquipmentCost->find('list', [
			'conditions' => ['EquipmentCost.user_compensation_profile_id' => $profileWithNoCosts]
		]);
		$this->assertEmpty($equipmentCosts);
		$this->assertTrue($this->EquipmentCost->generateCompData($profileWithNoCosts));
	}
}
