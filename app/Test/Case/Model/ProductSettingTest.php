<?php
App::uses('ProductSetting', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ProductSetting Test Case
 *
 */
class ProductSettingTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductSetting = ClassRegistry::init('ProductSetting');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductSetting);
		parent::tearDown();
	}

/**
 * testValidateCalculation
 *
 * @return void
 */
	public function testValidateCalculation() {
		$data = [
			'gral_fee' => 2,
			'gral_fee_multiplier' => 6,
			'monthly_fee' => 3
		];
		$check = ['total' => 15];
		$this->ProductSetting->set($data);
		$actual = $this->ProductSetting->validateCalculation($check);
		$this->assertTrue($actual);

		$check = ['total' => 10];
		$actual = $this->ProductSetting->validateCalculation($check);
		$this->assertFalse($actual);
	}
}
