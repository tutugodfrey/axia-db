<?php
App::uses('AddlAmexRepCost', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AddlAmexRepCost Test Case
 *
 */
class AddlAmexRepCostTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AddlAmexRepCost = ClassRegistry::init('AddlAmexRepCost');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AddlAmexRepCost);
		parent::tearDown();
	}

/**
 * testGetByCompProfile
 *
 * @return void
 */
	public function testGetByCompProfile() {
		$expected = [
			'AddlAmexRepCost' => [
				'user_compensation_profile_id' => '88888888-5a64-4a93-8724-888888888888'
			]
		];
		$actual = $this->AddlAmexRepCost->getByCompProfile('88888888-5a64-4a93-8724-888888888888');
		$this->assertEquals($expected, $actual);

		$expected = [
			'AddlAmexRepCost' => [
				'id' => '55b9f412-0ac0-4a85-9931-139734627ad4',
				'user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4',
				'conversion_fee' => '0.1',
				'sys_processing_fee' => '0.11'
			]
		];
		$actual = $this->AddlAmexRepCost->getByCompProfile('5570e7fe-5a64-4a93-8724-337a34627ad4');
		$this->assertEquals($expected, $actual);
	}
}
