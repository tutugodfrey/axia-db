<?php
App::uses('MerchantUwFinalApproved', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * NonTaxableReason Test Case
 *
 */
class NonTaxableReasonTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->NonTaxableReason = ClassRegistry::init('NonTaxableReason');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->NonTaxableReason);
		parent::tearDown();
	}

/**
 * testGetList
 *
 * @covers NonTaxableReason::getList()
 * @return void
 */
	public function testGetList() {
		$expected = [
			'5cf17a0f-0ad0-4126-bc5a-6d5634627ad4' => 'Service/Non Equipment',
			'5cf17a0f-68d0-48d3-9222-6d5634627ad4' => 'Reseller',
			'5cf17a0f-9598-41c3-a88f-6d5634627ad4' => 'Out of State',
			'5cf17a0f-a960-4514-b953-6d5634627ad4' => 'Exact Shipping'
		];

		$actual = $this->NonTaxableReason->getList();

		$this->assertEquals($expected, $actual);
	}
}
