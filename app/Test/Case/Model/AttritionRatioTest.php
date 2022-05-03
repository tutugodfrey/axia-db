<?php
App::uses('AttritionRatio', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AttritionRatio Test Case
 *
 */
class AttritionRatioTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AttritionRatio = ClassRegistry::init('AttritionRatio');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AttritionRatio);
		parent::tearDown();
	}

/**
 * testDefaultData
 *
 * @return void
 */
	public function testDefaultData() {
		$userId = CakeText::uuid();
		$expected = [
			[
				'AttritionRatio' => [
					'associated_user_id' => $userId,
					'percentage' => '0'
				]
			]
		];
		$actual = $this->AttritionRatio->defaultData($userId, []);
		
		$this->assertSame($expected, $actual);
	}

/**
 * testfindByCompProfile
 *
 * @return void
 */
	public function testfindByCompProfile() {
		$id = $this->AttritionRatio->field('id');
		$ucpId = CakeText::uuid();
		$this->AttritionRatio->save(['id' => $id, 'user_compensation_profile_id' => $ucpId]);
		$actual = $this->AttritionRatio->find('byCompProfile', ['conditions' => ['AttritionRatio.user_compensation_profile_id' => $ucpId]]);

		$this->assertSame($id, $actual[0]['AttritionRatio']['id']);
		$this->assertSame($ucpId, $actual[0]['AttritionRatio']['user_compensation_profile_id']);
	}
}
