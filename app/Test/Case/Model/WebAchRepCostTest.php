<?php
/* WebAchRepCost Test cases generated on: 2017-11-13 09:11:53 : 1510593173*/
App::uses('WebAchRepCost', 'Model');
App::uses('AxiaTestCase', 'Test');
class WebAchRepCostTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->WebAchRepCost = ClassRegistry::init('WebAchRepCost');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->WebAchRepCost);
		parent::tearDown();
	}

/**
 * testGetByCompProfile
 * Test getting WebAchRepCost data
 *
 * @covers WebAchRepCost::getByCompProfile()
 * @return void
 * @access public
 */
	public function testGetByCompProfile() {
		//test existing data
		$actual = $this->WebAchRepCost->getByCompProfile('5612bc94-4864-4080-b2a9-114fc0a81cd6');
		$expected = [
			'WebAchRepCost' => [
				'id' => '56143469-a7dc-4c49-a600-67e3c0a81cd6',
				'user_compensation_profile_id' => '5612bc94-4864-4080-b2a9-114fc0a81cd6',
				'rep_rate_pct' => '10',
				'rep_per_item' => '10',
				'rep_monthly_cost' => '10'
			]
		];
		$this->assertSame($expected, $actual);

		//test default data structure return when data does not exist
		$actual = $this->WebAchRepCost->getByCompProfile('00000000-0000-0000-0000-000000000001');
		$expected = [
			'WebAchRepCost' => [
				'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000001'
			]
		];
		$this->assertSame($expected, $actual);
	}

}
