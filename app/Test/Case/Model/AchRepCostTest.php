<?php
/* AchRepCost Test cases generated on: 2017-11-13 10:11:00 : 1510598820*/
App::uses('AchRepCost', 'Model');
App::uses('AxiaTestCase', 'Test');
class AchRepCostTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AchRepCost = ClassRegistry::init('AchRepCost');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AchRepCost);
		parent::tearDown();
	}

/**
 * testGetByCompProfile
 * Test getting AchRepCost data
 *
 * @covers AchRepCost::getByCompProfile()
 * @return void
 * @access public
 */
	public function testGetByCompProfile() {
		//test existing data
		$actual = $this->AchRepCost->getByCompProfile('5570e7ec-38a8-4fc2-afc8-337a34627ad4');
		
		$expected = [
			[
				'AchRepCost' => [
					'id' => '5328acfa-5c18-45c7-85cc-2b1434627ad4',
					'user_compensation_profile_id' => '5570e7ec-38a8-4fc2-afc8-337a34627ad4',
					'ach_provider_id' => '550d554f-7fa8-4a5a-86c3-0d8c34627ad4',
					'rep_rate_pct' => '0.11',
					'rep_per_item' => '0.11',
					'rep_monthly_cost' => '11'
				],
				'AchProvider' => [
					'id' => null,
					'provider_name' => null
				]
			],
			[
				'AchRepCost' => [
					'id' => '5328acfa-b65c-4536-9152-2b1434627ad4',
					'user_compensation_profile_id' => '5570e7ec-38a8-4fc2-afc8-337a34627ad4',
					'ach_provider_id' => '550d554f-8694-4a03-bb75-0d8c34627ad4',
					'rep_rate_pct' => '0.10',
					'rep_per_item' => '0.10',
					'rep_monthly_cost' => '10'
				],
				'AchProvider' => [
					'id' => null,
					'provider_name' => null
				]
			],
		];
		$this->assertSame($expected, $actual);

		//test default data structure return when data does not exist
		$actual = $this->AchRepCost->getByCompProfile('00000000-0000-0000-0000-000000000001');

		$expected = [
			[
				'AchRepCost' => [
						'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000001',
						'ach_provider_id' => '5501ec0e-6b8c-47c8-b96f-2f2034627ad4'
				],
				'AchProvider' => [
						'provider_name' => 'Check Gateway'
				]
			],
			[
				'AchRepCost' => [
						'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000001',
						'ach_provider_id' => '5501ec0e-abe4-474e-a5d0-2f2034627ad4'
				],
				'AchProvider' => [
						'provider_name' => 'Sage Payments'
				]
			],
			[
				'AchRepCost' => [
						'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000001',
						'ach_provider_id' => '55bbcdd0-ed3c-4745-918a-1f3634627ad4'
				],
				'AchProvider' => [
						'provider_name' => 'Vericheck'
				]
			]
		];
		$this->assertSame($expected, $actual);
	}

}
