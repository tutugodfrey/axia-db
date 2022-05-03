<?php
App::uses('BetTable', 'Model');
App::uses('CardType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * BetTable Test Case
 *
 */
class BetTableTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BetTable = ClassRegistry::init('BetTable');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BetTable);
		parent::tearDown();
	}

/**
 * testGetAllGroupedByCardType
 *
 * @return void
 */
	public function testGetAllGroupedByCardType() {
		$result = $this->BetTable->getAllGroupedByCardType();
		$expectedGroups = [
			'Visa',
			'Mastercard',
			'EBT',
			'Discover',
			'Debit',
			'American Express'
		];
		$this->assertEquals($expectedGroups, array_keys($result));
		foreach ($expectedGroups as $ctName) {
			$expected = $this->BetTable->find('count', [
						'conditions' => [
							'card_type_id' => $this->BetTable->CardType->field('id', ['card_type_description' => $ctName]),
							'is_enabled' => true
						]
					]
				);
			$this->assertCount($expected, Hash::get($result, $ctName));
		}

		$expectedFields = ['id', 'name', 'card_type_id'];
		$this->assertEquals($expectedFields, array_keys(Hash::get($result, 'Visa.0')));
	}


/**
 * testGetListGroupedByCardType
 *
 * @return void
 */
	public function testGetListGroupedByCardType() {
		$actual = $this->BetTable->getListGroupedByCardType();
		$this->assertNotEmpty($actual['Visa']);
		$this->assertNotEmpty($actual['Mastercard']);
		$this->assertNotEmpty($actual['Discover']);
		$this->assertNotEmpty($actual['American Express']);
	}

}
