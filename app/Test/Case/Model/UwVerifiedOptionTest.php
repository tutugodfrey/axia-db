<?php
App::uses('UwVerifiedOption', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * UwVerifiedOption Test Case
 *
 */
class UwVerifiedOptionTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UwVerifiedOption = ClassRegistry::init('UwVerifiedOption');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UwVerifiedOption);
		parent::tearDown();
	}

/**
 * testGetUwVerifiedOptions
 *
 * @covers UwVerifiedOption::getUwVerifiedOptions
 * testGetUwVerifiedOptions method
 *
 * @return void
 */
	public function testGetUwVerifiedOptions() {
		$result = $this->UwVerifiedOption->getUwVerifiedOptions();
		$expected = [
			'yn' => [
				'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor sit amet'
			]
		];

		$this->assertEquals($expected, $result);
	}
}