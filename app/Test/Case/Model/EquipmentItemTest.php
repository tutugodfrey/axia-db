<?php
App::uses('EquipmentItem', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * EquipmentItem Test Case
 *
 */
class EquipmentItemTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EquipmentItem = ClassRegistry::init('EquipmentItem');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EquipmentItem);
		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testGetEquipmentType() {
		$this->assertEquals('00000000-0000-0000-0000-000000000001', $this->EquipmentItem->getEquipmentType());
	}

/**
 * test
 *
 * @return void
 */
	public function testAfterSaveCreate() {
		$equipmentCostCount = $this->EquipmentItem->EquipmentCost->find('count');
		$profilesCount = $this->EquipmentItem->EquipmentCost->UserCompensationProfile->find('count');
		$data = [
			'EquipmentItem' => [
				'equipment_type_id' => '00000000-0000-0000-0000-000000000001',
				'equipment_item_description' => 'New equipment',
				'equipment_item_true_price' => 999.99,
				'equipment_item_rep_price' => 1000,
			],
		];

		$this->assertEquals(14, $profilesCount);
		$this->assertEquals(17, $equipmentCostCount);
		$this->EquipmentItem->create();
		$this->assertTrue($this->EquipmentItem->save($data));
		$this->assertEquals($equipmentCostCount + $profilesCount, $this->EquipmentItem->EquipmentCost->find('count'));
		$equipmentItem = $this->EquipmentItem->find('first', [
			'conditions' => ['EquipmentItem.id' => $this->EquipmentItem->id],
			'contain' => ['EquipmentCost']
		]);
		$this->assertCount($profilesCount, Hash::get($equipmentItem, 'EquipmentCost'));
		$this->assertCount($profilesCount, Hash::extract($equipmentItem, 'EquipmentCost.{n}[rep_cost=1000]'));
	}

/**
 * test
 *
 * @return void
 */
	public function testAfterSaveUpdate() {
		$equipmentItemId = '00000000-0000-0000-0000-000000000001';
		$data = [
			'EquipmentItem' => [
				'id' => $equipmentItemId,
				'equipment_type_id' => '00000000-0000-0000-0000-000000000001',
				'equipment_item_description' => 'Edited equipment',
				'equipment_item_true_price' => 999.99,
				'equipment_item_rep_price' => 1000,
			],
		];

		$actual = $this->EquipmentItem->field('equipment_item_description', ['id' => $equipmentItemId]);
		$this->assertEquals('Equipment item 1', $actual);
		$this->assertEquals(17, $this->EquipmentItem->EquipmentCost->find('count'));

		$this->assertTrue($this->EquipmentItem->save($data));
		$actual = $this->EquipmentItem->field('equipment_item_description', ['id' => $equipmentItemId]);
		$this->assertEquals('Edited equipment', $actual);
		$this->assertEquals(17, $this->EquipmentItem->EquipmentCost->find('count'));
	}

/**
 * test
 *
 * @expectedException InvalidArgumentException
 * @return void
 */
	public function testGetEquipmentListInvalidArgument() {
		$this->EquipmentItem->getEquipmentList('false');
	}

/**
 * test
 *
 * @return void
 */
	public function testGetEquipmentList() {
		$actual = $this->EquipmentItem->getEquipmentList();
		$expected = [
			'00000000-0000-0000-0000-000000000001' => 'Equipment item 1',
			'00000000-0000-0000-0000-000000000002' => 'Equipment item 2',
			'00000000-0000-0000-0000-000000000003' => 'Equipment item 3',
			'00000000-0000-0000-0000-000000000004' => 'Equipment item 4'
		];
		$this->assertEquals($expected, $actual);

		$result = $this->EquipmentItem->getEquipmentList(true);
		$this->assertCount(3, $result);
		$result = $this->EquipmentItem->getEquipmentList(false);
		$this->assertCount(1, $result);
	}
}
