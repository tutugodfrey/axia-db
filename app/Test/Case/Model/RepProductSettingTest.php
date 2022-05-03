<?php
App::uses('RepProductSetting', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * RepProductSetting Test Case
 *
 */
class RepProductSettingTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RepProductSetting = ClassRegistry::init('RepProductSetting');
	}

/**
 * testGetByCompProfile method
 *
 * @return void
 */
	public function testFindByCompProfile() {
		$data = $this->RepProductSetting->find('byCompProfile', array(
			'conditions' => array(
				'RepProductSetting.user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4'
			)
		));

		$this->assertArrayHasKey('RepProductSetting', $data[0]);
		$this->assertArrayHasKey('ProductsServicesType', $data[0]);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RepProductSetting);
		parent::tearDown();
	}

}
