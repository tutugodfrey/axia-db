<?php
App::uses('Bet', 'Model');
App::uses('User', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Bet Test Case
 *
 */
class BetTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Bet = ClassRegistry::init('Bet');
		$this->UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Bet);
		parent::tearDown();
	}

/**
 * testGetUserBets
 *
 * @covers Bet::getUserBets()
 * @return void
 */
	public function testGetUserBets() {
		$expected = $this->Bet->find('all', array(
			'conditions' => array(
				"user_compensation_profile_id" => '5570e7fe-5a64-4a93-8724-337a34627ad4',
			),
		));
		$actual = $this->Bet->getUserBets('5570e7fe-5a64-4a93-8724-337a34627ad4');
		$this->assertSame($expected, $actual);
	}

/**
 * testValidPercentage
 *
 * @covers Bet::validPercentage()
 * @return void
 */
	public function testValidPercentage() {
		$this->assertFalse($this->Bet->validPercentage([]));
		$this->assertSame($this->Bet->validPercentage(['too_big' => 999]), 'Must be a valid percentage value with a maximum precision of 4');
		$this->assertSame($this->Bet->validPercentage(['too_small' => -9]), 'Must be a valid percentage value with a maximum precision of 4');
		$this->assertSame($this->Bet->validPercentage(['NAN' => true]), 'Must be a valid percentage value with a maximum precision of 4');
		$this->assertTrue($this->Bet->validPercentage(['good' => 100]));
		
	}
/**
 * test
 *
 * @return void
 */
	public function testGetFilteredBets() {
		$result = $this->Bet->getFilteredBets(
			'e9580d94-29ce-4de4-9fd4-c81d1afefbf4',
			'521df34a-0a90-4035-b90e-461534627ad4',
			'00000000-9999-0000-0000-000000000001'
		);
		$this->assertEmpty($result);

		$result = $this->Bet->getFilteredBets(
			'e9580d94-29ce-4de4-9fd4-c81d1afefbf4',
			'521df34a-0a90-4035-b90e-461534627ad4',
			'5570e7fe-5a64-4a93-8724-337a34627ad4'
		);
		$this->assertCount(1, $result);
		$this->assertEquals('00000000-0000-0000-0000-000000000001', Hash::get($result, '0.Bet.id'));
	}

/**
 * testUpdateMany method
 *
 * @param array $params options array that emulates items selected from the mass update form on the client side
 * @param $expecter 
 * @covers Bet::updateMany()
 * @dataProvider providerTestUpdateMany()
 * @return void
 */
	public function testUpdateMany($params, $expected) {
		
		$preCount = $this->Bet->find('count');
		$this->Bet->updateMany($params);
		$postCount = $this->Bet->find('count');
		$this->assertEqual($postCount, $preCount + $expected['total_bet_records_added']);

		$ucpCond = [];
		if (empty($params['UserCompensationProfile']['ucp_ids'])) {
			$ucpCond[] = ($params['UserCompensationProfile']['default_ucps_ckbx'] > 0)? 'UserCompensationProfile.is_default = true' : null;
			$ucpCond[] = ($params['UserCompensationProfile']['partner_reps_ucps_ckbx'] > 0)? 'UserCompensationProfile.partner_user_id IS NOT NULL' : null;
			$ucpCond[] = ($params['UserCompensationProfile']['manager_ucps_ckbx'] > 0)? 'UserCompensationProfile.role_id = (SELECT id from roles where name = ' . "'" . User::ROLE_SM . "')" : null;
			$ucpCond[] = ($params['UserCompensationProfile']['manager2_ucps_ckbx'] > 0)? 'UserCompensationProfile.role_id = (SELECT id from roles where name = ' . "'" . User::ROLE_SM2 . "')" : null;
			$ucpCond['OR'] = Hash::filter($ucpCond);
			$compIds = $this->UserCompensationProfile->find('list', ['conditions' => $ucpCond, 'fields' => ['id']]);
		} else {
			$compIds = $params['UserCompensationProfile']['ucp_ids'];
		}

		foreach ($compIds as $ucpId) {
			foreach($params['BetTable']['bet_ids'] as $betTableId) {
				foreach ($params['Bet'] as $betParam) {
					//compare post update actuals vs test data
					$actual = $this->Bet->find('all', [
							'conditions' => [
								'bet_network_id' => $betParam['bet_network_id'],
								'bet_table_id' => $betTableId,
								'card_type_id' => $this->Bet->BetTable->field('card_type_id', ['id' => $betTableId]),
								'user_compensation_profile_id' => $ucpId,
							]
						]
					);

					$this->assertCount(1, $actual); //check for uniqueness 
					$this->assertEqual($betParam['bet_network_id'], $actual[0]['Bet']['bet_network_id']);
					$this->assertEqual($betParam['pct_cost'], $actual[0]['Bet']['pct_cost']);
					$this->assertEqual($betParam['pi_cost'], $actual[0]['Bet']['pi_cost']);
					$this->assertEqual($betParam['additional_pct'], $actual[0]['Bet']['additional_pct']);
					$this->assertEqual($betParam['sales_cost'], $actual[0]['Bet']['sales_cost']);
					$this->assertEqual($betParam['dial_sales_cost'], $actual[0]['Bet']['dial_sales_cost']);
					$this->assertEqual($betParam['non_dial_sales_cost'], $actual[0]['Bet']['non_dial_sales_cost']);
					$this->assertEqual($betParam['auth_cost'], $actual[0]['Bet']['auth_cost']);
					$this->assertEqual($betParam['dial_auth_cost'], $actual[0]['Bet']['dial_auth_cost']);
					$this->assertEqual($betParam['non_dial_auth_cost'], $actual[0]['Bet']['non_dial_auth_cost']);
					$this->assertEqual($betParam['settlement_cost'], $actual[0]['Bet']['settlement_cost']);
				}
			}
		}
	}

/**
 * Provider for testUpdateMany
 *
 * @dataProvider for testUpdateMany()
 * @return void
 */
	public function providerTestUpdateMany() {
		$currUcpCount = ClassRegistry::init('UserCompensationProfile')->find('count');
		$currDefaultUcpCount = ClassRegistry::init('UserCompensationProfile')->find('count', ['conditions' => ['is_default' => true]]);
		$currPartnerRepUcpCount = ClassRegistry::init('UserCompensationProfile')->find('count', ['conditions' => ['UserCompensationProfile.partner_user_id IS NOT NULL']]);
		$data = [
			//update existing data and create new
			[	//params array will match to existing data
				[
					'BetTable' => [
						'bet_ids' => ['521df34a-0a90-4035-b90e-461534627ad4', '521df34a-d678-4b4d-ab58-485934627ad4']
					],
					'UserCompensationProfile' => [
						'default_ucps_ckbx' => '0',
						'partner_reps_ucps_ckbx' => '0',
						'manager_ucps_ckbx' => '0',
						'manager2_ucps_ckbx' => '0',
						'ucp_ids' => [
							// only update this UCP
							'5570e7fe-5a64-4a93-8724-337a34627ad4',
						]
					],
					'Bet' => [
						[	//only non-null values will be used for update
							'bet_network_id' => '55cc6d99-d534-45f1-acc7-184d34627ad4',
							'pct_cost' => null,
							'pi_cost' => 2,
							'additional_pct' => 3,
							'sales_cost' => 4,
							'dial_sales_cost' => 0,
							'non_dial_sales_cost' => 0,
							'auth_cost' => null,
							'dial_auth_cost' => null,
							'non_dial_auth_cost' => 7,
							'settlement_cost' => 10,
						],
						[
							'bet_network_id' => '55cc6d99-0220-47c7-a89a-184d34627ad4',
							'pct_cost' => null,
							'pi_cost' => 9,
							'additional_pct' => 10,
							'sales_cost' => 11,
							'dial_sales_cost' => 0,
							'non_dial_sales_cost' => 0,
							'auth_cost' => 12,
							'dial_auth_cost' => 13,
							'non_dial_auth_cost' => 14,
							'settlement_cost' => 15, 
						],
					],
				],
				[
					'total_bet_records_added' => 3,
				]
			],
			//update one create none
			[	//params array will match to existing data
				[
					'BetTable' => [
						'bet_ids' => ['521df34a-0a90-4035-b90e-461534627ad4']
					],
					'UserCompensationProfile' => [
						'default_ucps_ckbx' => '0',
						'partner_reps_ucps_ckbx' => '0',
						'manager_ucps_ckbx' => '0',
						'manager2_ucps_ckbx' => '0',
						'ucp_ids' => [
							// only update this UCP
							'5570e7fe-5a64-4a93-8724-337a34627ad4',
						]
					],
					'Bet' => [
						[	//only non-null values will be used for update
							'bet_network_id' => '55cc6d99-d534-45f1-acc7-184d34627ad4',
							'pct_cost' => null,
							'pi_cost' => 2,
							'additional_pct' => 3,
							'sales_cost' => 4,
							'dial_sales_cost' => 0,
							'non_dial_sales_cost' => 0,
							'auth_cost' => null,
							'dial_auth_cost' => null,
							'non_dial_auth_cost' => 7,
							'settlement_cost' => 10,
						],
					],
				],
				[
					'total_bet_records_added' => 0,
				]
			],
			//create one for all compensation profiles by using parameters that no user Bet has
			[	//params array will match to existing data
				[
					'BetTable' => [
						'bet_ids' => ['558ade3b-f5ec-43e1-bd89-20ba34627ad4'] 
					],
					'UserCompensationProfile' => [
						'default_ucps_ckbx' => '1',
						'partner_reps_ucps_ckbx' => '1',
						'manager_ucps_ckbx' => '1',
						'manager2_ucps_ckbx' => '1',
						'ucp_ids' => ''
					],
					'Bet' => [
						[	//only non-null values will be used for update
							'bet_network_id' => CakeText::uuid(),//arbitrary bet network id 
							'pct_cost' => null,
							'pi_cost' => 2,
							'additional_pct' => 3,
							'sales_cost' => 4,
							'dial_sales_cost' => 0,
							'non_dial_sales_cost' => 0,
							'auth_cost' => null,
							'dial_auth_cost' => null,
							'non_dial_auth_cost' => 7,
							'settlement_cost' => 10,
						],
					],
				],
				[
					//total records added is the total number of UCPs since none have this BET cost
					'total_bet_records_added' => $currUcpCount, 
				]
			],
			//create one for all default UCPs using parameters that no user Bet has
			[	//params array will match to existing data
				[
					'BetTable' => [
						'bet_ids' => ['558ade3b-f5ec-43e1-bd89-20ba34627ad4'] 
					],
					'UserCompensationProfile' => [
						'default_ucps_ckbx' => '1',
						'partner_reps_ucps_ckbx' => '0',
						'manager_ucps_ckbx' => '0',
						'manager2_ucps_ckbx' => '0',
						'ucp_ids' => ''
					],
					'Bet' => [
						[	//only non-null values will be used for update
							'bet_network_id' => CakeText::uuid(),//arbitrary bet network id 
							'pct_cost' => null,
							'pi_cost' => 2,
							'additional_pct' => 3,
							'sales_cost' => 4,
							'dial_sales_cost' => 0,
							'non_dial_sales_cost' => 0,
							'auth_cost' => null,
							'dial_auth_cost' => null,
							'non_dial_auth_cost' => 7,
							'settlement_cost' => 10,
						],
					],
				],
				[
					//total records added is the total number of default UCPs since none have this BET cost
					'total_bet_records_added' => $currDefaultUcpCount, 
				]
			],
			//create one for all parter-rep UCPs using parameters that no user Bet has
			[	//params array will match to existing data
				[
					'BetTable' => [
						'bet_ids' => ['558ade3b-f5ec-43e1-bd89-20ba34627ad4'] 
					],
					'UserCompensationProfile' => [
						'default_ucps_ckbx' => '0',
						'partner_reps_ucps_ckbx' => '1',
						'manager_ucps_ckbx' => '0',
						'manager2_ucps_ckbx' => '0',
						'ucp_ids' => ''
					],
					'Bet' => [
						[	//only non-null values will be used for update
							'bet_network_id' => CakeText::uuid(),//arbitrary bet network id 
							'pct_cost' => null,
							'pi_cost' => 2,
							'additional_pct' => 3,
							'sales_cost' => 4,
							'dial_sales_cost' => 0,
							'non_dial_sales_cost' => 0,
							'auth_cost' => null,
							'dial_auth_cost' => null,
							'non_dial_auth_cost' => 7,
							'settlement_cost' => 10,
						],
					],
				],
				[
					//total records added is the total number of partner rep UCPs since none have this BET cost
					'total_bet_records_added' => $currPartnerRepUcpCount, 
				]
			],
		];
		return $data;
	}

/**
 * testValidateMassUpdate method
 *
 * @param array $params options array that emulates items selected from the mass update form on the client side
 * @param $expecter 
 * @covers Bet::validateMassUpdate()
 * @return void
 */
	public function testValidateMassUpdate() {
		$data = [
			'BetTable' => [
				'bet_ids' => ['521df34a-0a90-4035-b90e-461534627ad4', '521df34a-d678-4b4d-ab58-485934627ad4']
			],
			'UserCompensationProfile' => [
				'default_ucps_ckbx' => '0',
				'partner_reps_ucps_ckbx' => '0',
				'manager_ucps_ckbx' => '0',
				'manager2_ucps_ckbx' => '0',
				'ucp_ids' => [
					// only update this UCP
					'5570e7fe-5a64-4a93-8724-337a34627ad4',
				]
			],
			'Bet' => [
				[	//only non-null values will be used for update
					'bet_network_id' => '55cc6d99-d534-45f1-acc7-184d34627ad4',
					'pct_cost' => null,
					'pi_cost' => 2,
					'additional_pct' => 3,
					'sales_cost' => 4,
					'dial_sales_cost' => 0,
					'non_dial_sales_cost' => 0,
					'auth_cost' => null,
					'dial_auth_cost' => null,
					'non_dial_auth_cost' => 7,
					'settlement_cost' => 10,
				],
				[
					'bet_network_id' => '55cc6d99-0220-47c7-a89a-184d34627ad4',
					'pct_cost' => null,
					'pi_cost' => 9,
					'additional_pct' => 10,
					'sales_cost' => 11,
					'dial_sales_cost' => 0,
					'non_dial_sales_cost' => 0,
					'auth_cost' => 12,
					'dial_auth_cost' => 13,
					'non_dial_auth_cost' => 14,
					'settlement_cost' => 15, 
				],
			],
		];

		$this->assertTrue($this->Bet->validateMassUpdate($data));
		$tmp = $data;
		$tmp['UserCompensationProfile']['ucp_ids'] = null;

		$this->assertFalse($this->Bet->validateMassUpdate($tmp));
		$expectedErrors = [
			'UserCompensationProfile' => [
					'ucp_ids' => [
						'You must select user(s) and at least one User Compensation Profile from this list'
					],
					'default_ucps_ckbx' => [
						'At least one UCP type must be checked'
					],
					'partner_reps_ucps_ckbx' => [
						'At least one UCP type must be checked'
					],
					'manager_ucps_ckbx' => [
						'At least one UCP type must be checked'
					],
					'manager2_ucps_ckbx' => [
						'At least one UCP type must be checked'
					]
				]
			];

		$this->assertEqual($expectedErrors, $this->Bet->validationErrors);
		$tmp = $data;
		$tmp['Bet'] = [];
		$this->assertFalse($this->Bet->validateMassUpdate($tmp));
		unset($expectedErrors['UserCompensationProfile']['ucp_ids']);

		$this->assertEqual($expectedErrors, $this->Bet->validationErrors);
	}
}
