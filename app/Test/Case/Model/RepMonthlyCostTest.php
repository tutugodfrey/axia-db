<?php
App::uses('RepMonthlyCost', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * RepMonthlyCost Test Case
 *
 */
class RepMonthlyCostTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RepMonthlyCost = ClassRegistry::init('RepMonthlyCost');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RepMonthlyCost);
		parent::tearDown();
	}

/**
 * testGetByCompProfile
 *
 * @covers RepMonthlyCost::getByCompProfile()
 * @return void
 */
	public function testGetByCompProfile() {
		$ucpId = $this->RepMonthlyCost->field('user_compensation_profile_id');
		$actual = $this->RepMonthlyCost->getByCompProfile($ucpId);

		$expected = [
			[
				'RepMonthlyCost' => [
					'id' => '557b1019-7024-4dd5-b7d7-1f8734627ad4',
					'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000001',
					'bet_network_id' => '00000000-0000-0000-0000-000000000001',
					'credit_cost' => '0',
					'debit_cost' => '0',
					'ebt_cost' => '0'
				],
				'BetNetwork' => [
					'id' => null,
					'name' => null,
					'is_active' => null
				]
			]
		];
		$this->assertSame($expected, $actual);

		$actual = $this->RepMonthlyCost->getByCompProfile(CakeText::uuid());
		$this->assertNotEmpty($actual);
		$betNets = $this->RepMonthlyCost->BetNetwork->find('list', ['fields' => ['id', 'name']]);
		foreach($actual as $record) {
			$this->assertNotEmpty($betNets[$record['RepMonthlyCost']['bet_network_id']]);
			$this->assertSame($betNets[$record['RepMonthlyCost']['bet_network_id']], $record['BetNetwork']['name']);
		}
		
	}
}
