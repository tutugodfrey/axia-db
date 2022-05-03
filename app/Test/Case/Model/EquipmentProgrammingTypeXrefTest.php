<?php
App::uses('EquipmentProgrammingTypeXref', 'Model');
App::uses('AxiaTestCase', 'Test');

class EquipmentProgrammingTypeXrefTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EquipmentProgrammingTypeXref = ClassRegistry::init('EquipmentProgrammingTypeXref');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EquipmentProgrammingTypeXref);
		parent::tearDown();
	}

/**
 * testCheckRemoveProgrammingTypesXref
 *
 * @return void
 */
	public function testCheckRemoveProgrammingTypesXref() {
		$data = ['random-data'];
		$actual = $this->EquipmentProgrammingTypeXref->checkRemoveProgrammingTypesXref($data);
		$this->assertEquals($data, $actual);

		//Test that one of many EquipmentProgrammingTypeXref is removed
		$data = array(
			'EquipmentProgrammingTypeXref' => array(
				array(
					'id' => '53c2cb71-23d8-4628-93fb-4ed9fb281ef5',
					'equipment_programming_id' => '1815616b-2203-40e4-b1f7-b5bd70525a91',
					'programming_type' => 'AVS'
				),
				array(
					/*This record should be removed from the array and deleted since it lacks a programming_type*/
					'id' => '5e1929b7-80ee-47f6-b39a-75ee5bd34f82',
					'equipment_programming_id' => '1815616b-2203-40e4-b1f7-b5bd70525a91',
				),
				array(
					'id' => '348c7881-2afd-477b-831f-8b27a390e89e',
					'equipment_programming_id' => '1815616b-2203-40e4-b1f7-b5bd70525a91',
					'programming_type' => 'TIP'
				)
			)
		);
		$actual = $this->EquipmentProgrammingTypeXref->checkRemoveProgrammingTypesXref($data);
		unset($data['EquipmentProgrammingTypeXref'][1]);
		$expected = $data;
		$this->assertSame($actual, $expected);
		$this->assertEmpty(
			$this->EquipmentProgrammingTypeXref->find('first', array(
				'conditions' => array(
					'EquipmentProgrammingTypeXref.id' => '5e1929b7-80ee-47f6-b39a-75ee5bd34f82'
				)
			)
		));
	}

/**
 * testGetProgrammingTypes
 *
 * @return void
 */
	public function testGetProgrammingTypes() {
		$result = $this->EquipmentProgrammingTypeXref->getProgrammingTypes();
		$this->assertCount(15, $result);
		$this->assertEquals('9', Hash::get($result, '9'));
		$this->assertEquals('Split Dial', Hash::get($result, 'SD'));
		$this->assertEquals('WIRELESS', Hash::get($result, 'WIRELESS'));
	}

/**
 * testGetHasManyProgrammingTypesList
 *
 * @return void
 */
	public function testGetHasManyProgrammingTypesList() {
		$expected = [
			'EquipmentProgrammingTypeXref' => [
				['PMNTFUSION' => 'Payment Fusion'],
				['VP2PE' => 'VP2PE'],
				['AVS' => 'AVS'],
				['INV' => 'Invoice'],
				['SD' => 'Split Dial'],
				['TIP' => 'Tips'],
				['SRV' => 'Server #s'],
				['NPB' => 'No PABX'],
				['CVV' => 'CVV'],
				['8' => '8'],
				['9' => '9'],
				['IP' => 'IP'],
				['DIAL' => 'DIAL'],
				['SSL' => 'SSL'],
				['WIRELESS' => 'WIRELESS'],
			],
		];
		$result = $this->EquipmentProgrammingTypeXref->getHasManyProgrammingTypesList();
		$this->assertEquals($expected, $result);
	}
}
