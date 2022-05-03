<?php
App::uses('Warranty', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Warranty Test Case
 *
 */
class WarrantyTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Warranty = ClassRegistry::init('Warranty');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Warranty);
		parent::tearDown();
	}

/**
 * testGetWarrantysList
 *
 * @covers Warranty::getWarrantiesList
 * testGetWarrantysList method
 *
 * @return void
 */
	public function testGetWarrantysList() {
		$result = $this->Warranty->getWarrantiesList();
		$expected = [
			'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor sit amet',
		];

		$this->assertEquals($expected, $result);
	}
}