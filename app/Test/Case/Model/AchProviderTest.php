<?php
App::uses('AchProvider', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AchProvider Test Case
 *
 */
class AchProviderTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AchProvider = ClassRegistry::init('AchProvider');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AchProvider);
		parent::tearDown();
	}

/**
 * testGetList
 *
 * @return void
 */
	public function testGetList() {
		$expected = [
			'5501ec0e-6b8c-47c8-b96f-2f2034627ad4' => 'Check Gateway',
			'5501ec0e-abe4-474e-a5d0-2f2034627ad4' => 'Sage Payments',
			'55bbcdd0-ed3c-4745-918a-1f3634627ad4' => 'Vericheck'
		];
		$actual = $this->AchProvider->getList();
		$this->assertEquals($expected, $actual);
	}
}
