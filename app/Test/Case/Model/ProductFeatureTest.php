<?php
App::uses('MerchantUwFinalApproved', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ProductFeature Test Case
 *
 */
class ProductFeatureTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductFeature = ClassRegistry::init('ProductFeature');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductFeature);
		parent::tearDown();
	}

/**
 * testGetList
 *
 * @covers ProductFeature::getList()
 * @return void
 */
	public function testGetList() {
		$expected = [
			'5818d29f-bf44-4588-b0e8-6cf234627ad4' => 'Corral',
			'5818d29f-2ed8-4767-b27f-6cf234627ad4' => 'Corral Premium',
			'5818d29f-d5d0-4828-9fdb-6cf234627ad4' => 'Starter',
			'791b9e07-5d6c-4198-a8d7-0ce26f50e379' => 'VP2PE'
		];

		$actual = $this->ProductFeature->getList();

		$this->assertEquals($expected, $actual);
	}
}
