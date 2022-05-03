<?php
App::uses('UwReceived', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * UwReceived Test Case
 *
 */
class UwReceivedTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UwReceived = ClassRegistry::init('UwReceived');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UwReceived);
		parent::tearDown();
	}

/**
 * testFindPrioritized
 *
 * @covers UwReceived::_findPrioritized()
 * @return void
 */
	public function testFindPrioritized() {
		$expected = [
			'00000000-0000-0000-0000-000000000001' => 'Lorem ips'
		];
		$actual = $this->UwReceived->find('prioritized');
		$this->assertsame($expected, $actual);
	}
}
