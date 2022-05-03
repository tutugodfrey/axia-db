<?php
App::uses('GatewayCostStructure', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * GatewayCostStructure Test Case
 *
 */
class GatewayCostStructureTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->GatewayCostStructure = ClassRegistry::init('GatewayCostStructure');
	}

/**
 * testGetByCompProfile method
 *
 * @return void
 */
	public function testFindByCompProfile() {
		$data = $this->GatewayCostStructure->find('byCompProfile', array(
			'conditions' => array(
				'GatewayCostStructure.user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4'
			)
		));

		$this->assertArrayHasKey('GatewayCostStructure', $data[0]);
		$this->assertArrayHasKey('Gateway', $data[0]);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->GatewayCostStructure);
		parent::tearDown();
	}

}
