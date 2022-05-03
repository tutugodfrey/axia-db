<?php
App::uses('MerchantUwFinalStatus', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantUwFinalStatus Test Case
 *
 */
class MerchantUwFinalStatusTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantUwFinalStatus = ClassRegistry::init('MerchantUwFinalStatus');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantUwFinalStatus);
		parent::tearDown();
	}

/**
 * testGetUwFinalStatusesList
 *
 * @return void
 */
	public function testGetUwFinalStatusesList() {
		$result = $this->MerchantUwFinalStatus->getUwFinalStatusesList();
		$expected = [
			'2fb6e87b-bf77-4d3c-96db-f12e9147fa5a' => 'Approved',
        	'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor s'
		];
		$this->assertSame($expected, $result);
	}
}
