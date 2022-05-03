<?php
App::uses('UwInfodoc', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * UwInfodoc Test Case
 *
 */
class UwInfodocTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UwInfodoc = ClassRegistry::init('UwInfodoc');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UwInfodoc);
		parent::tearDown();
	}

/**
 * testFindByRequired
 *
 * @covers UwInfodoc::_findByRequired
 * testFindByRequired method
 *
 * @return void
 */
	public function testFindByRequired() {
		$reflection = new ReflectionClass('UwInfodoc');
		$method = $reflection->getMethod('_findByRequired');
		$method->setAccessible(true);

		$query = [
			'required' => true,
			'fields' => null,
			'joins' => [],
			'limit' => null,
			'offset' => null,
			'order' => null,
			'page' => 1,
			'group' => null,
			'callbacks' => true
		];

		$state = 'before';
		$result = $method->invokeArgs($this->UwInfodoc, [$state, $query]);
		$expected = [
			'required' => true,
			'conditions' => [
				'UwInfodoc.required' => true
			],
			'fields' => null,
			'joins' => [],
			'limit' => null,
			'offset' => null,
			'order' => [
				'UwInfodoc.priority' => 'ASC'
			],
			'page' => 1,
			'group' => null,
			'callbacks' => true
		];

		$this->assertEquals($expected, $result);

		$state = 'after';
		$result = $method->invokeArgs($this->UwInfodoc, [$state, $query]);
		$expected = [];

		$this->assertEquals($expected, $result);
	}
}