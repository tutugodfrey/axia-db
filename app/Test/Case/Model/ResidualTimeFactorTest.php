<?php
App::uses('ResidualTimeFactor', 'Model');
App::uses('AxiaTestCase', 'Test');

class ResidualTimeFactorTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ResidualTimeFactor = ClassRegistry::init('ResidualTimeFactor');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ResidualTimeFactor);

		parent::tearDown();
	}

/**
 * test checkBeginMonth method
 *
 * @param array $data Data to validate
 * @param bool $expectedValidates Validation result
 * @param array $expectedErrors Validation errors
 * @dataProvider providerCheckBeginMonth
 * @covers ResidualTimeFactor::checkBeginMonth()
 * @covers ResidualTimeFactor::checkEndMonth()
 * @return void
 */
	public function testCheckTierMonthRanges($data, $expectedValidates, $expectedErrors) {
		$this->ResidualTimeFactor->set($data);
		$this->assertSame($expectedValidates, $this->ResidualTimeFactor->validates());
		$this->assertSame($expectedErrors, $this->ResidualTimeFactor->validationErrors);
	}

/**
 * Povider for TestCheckBeginMonth
 *
 * @return array
 */
	public function providerCheckBeginMonth() {
		return [
			[
				['tier1_begin_month' => null, 'tier1_end_month' => null],
				true,
				[]
			],
			[
				['tier1_begin_month' => 1, 'tier1_end_month' => null],
				false,
				[
					'tier1_end_month' => [
						0 => 'The end month must be greater or equal than the begin month'
					]
				]
			],
			[
				['tier1_begin_month' => null, 'tier1_end_month' => 1],
				true,
				[]
			],
			[
				['tier1_begin_month' => 1, 'tier1_end_month' => 1],
				true,
				[]
			],
			[
				['tier1_begin_month' => 1, 'tier1_end_month' => 10],
				true,
				[]
			],
			[
				['tier1_begin_month' => 11, 'tier1_end_month' => 10],
				false,
				[
					'tier1_begin_month' => [
						0 => 'The begin month must be less or equal than the end month'
					],
					'tier1_end_month' => [
						0 => 'The end month must be greater or equal than the begin month'
					],
				]
			],
			[
				[
					'tier1_end_month' => 3,
					'tier2_begin_month' => 3,
					'tier2_end_month' => 6
				],
				false,
				[
					'tier1_end_month' => [
						0 => 'The end month must be less than the next tier begin month'
					],
					'tier2_begin_month' => [
						0 => 'The begin month must be greater than the previous tier end month'
					],
				]
			],
			[
				[
					'tier1_end_month' => 3,
					'tier2_begin_month' => 4,
					'tier2_end_month' => 6
				],
				true,
				[]
			],
			[
				[
					'tier1_end_month' => 3,
					'tier2_begin_month' => 4,
					'tier2_end_month' => 6,
					'tier3_begin_month' => 5,
				],
				false,
				[
					'tier2_end_month' => [
						0 => 'The end month must be less than the next tier begin month'
					],
					'tier3_begin_month' => [
						0 => 'The begin month must be greater than the previous tier end month'
					],
				]
			],
			[
				[
					'tier1_end_month' => 'not-valid',
					'tier2_begin_month' => 4,
					'tier2_end_month' => 6,
					'tier3_begin_month' => 'not-valid',
				],
				false,
				[
					'tier3_begin_month' => [
						0 => 'The begin month must be greater than the previous tier end month'
					]
				]
			],
		];
	}
}
