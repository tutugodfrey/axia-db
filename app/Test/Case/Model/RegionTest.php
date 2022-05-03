<?php
App::uses('Region', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Region Test Case
 *
 */
class RegionTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Region = ClassRegistry::init('Region');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Region);
		parent::tearDown();
	}

/**
 * testGetByOrganization
 *
 * @covers Region::getByOrganization()
 * @return void
 */
	public function testGetByOrganization() {
		$expected = ['ef3b25d3-b4e7-4137-8663-42fc83f7fc71' => 'Org 1 - Region 1'];
		$actual = $this->Region->getByOrganization('8d08c7ed-c61a-4311-b088-706d6d8c052c');		
		$this->assertSame($expected, $actual);
	}
}
