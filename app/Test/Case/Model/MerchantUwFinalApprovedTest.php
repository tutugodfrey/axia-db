<?php
App::uses('MerchantUwFinalApproved', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantUwFinalApproved Test Case
 *
 */
class MerchantUwFinalApprovedTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantUwFinalApproved = ClassRegistry::init('MerchantUwFinalApproved');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantUwFinalApproved);
		parent::tearDown();
	}

/**
 * testGetActivePlus
 *
 * @covers MerchantUwFinalApproved::getActivePlus()
 * @return void
 */
	public function testGetActivePlus() {
		$includeId = CakeText::uuid();
		$expected = [
			'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor s',
			$includeId => 'inactive',
		];
		$this->MerchantUwFinalApproved->create();
		$this->MerchantUwFinalApproved->save(['id' => $includeId, 'name' => 'inactive', 'active' => false]);
		$actual = $this->MerchantUwFinalApproved->getActivePlus($includeId);
		$this->assertEquals($expected, $actual);
	}
}
