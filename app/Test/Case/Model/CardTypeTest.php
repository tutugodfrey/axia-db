<?php
App::uses('CardType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * CardType Test Case
 *
 */
class CardTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CardType = ClassRegistry::init('CardType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CardType);
		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testGetList() {
		$actual = $this->CardType->getList();
		$expected = [
			'e9580d94-29ce-4de4-9fd4-c81d1afefbf4' => 'Visa',
			'd3216cb3-ee71-40c6-bede-ac9818e24f3a' => 'Mastercard',
			'ddea093f-ab15-44f6-87ba-fb4c4235ced1' => 'American Express',
			'd82f4282-f816-4880-975c-53d42a7f02bc' => 'Discover',
			'd0ba718a-9296-4cdd-a6be-1d6d52e9c66c' => "Diner's Club",
			'95a6b6af-c084-4ad9-bfc1-f406f0df1601' => 'JCB',
			'5581dfa8-8004-4b13-8448-093534627ad4' => 'Debit',
			'5581dfa8-7788-4db5-b1c5-093534627ad4' => 'EBT'
		];
		$this->assertEquals($expected, $actual);
	}

/**
 * test
 *
 * @expectedException NotFoundException
 * @expectedExceptionMessage Invalid Card Type!
 * @return void
 */
	public function testViewNotFound() {
		$this->CardType->view('00000000-9999-0000-0000-000000000001');
	}

/**
 * test
 *
 * @return void
 */
	public function testView() {
		$actual = $this->CardType->view('d3216cb3-ee71-40c6-bede-ac9818e24f3a');
		$expected = [
			'CardType' => [
				'id' => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a',
				'card_type_old' => null,
				'card_type_description' => 'Mastercard',
			],
		];
		$this->assertEquals($expected, $actual);
	}
}
