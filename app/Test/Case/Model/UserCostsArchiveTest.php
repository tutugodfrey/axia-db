<?php
App::uses('UserCostsArchive', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * UserCostsArchive Test Case
 *
 */
class UserCostsArchiveTest extends AxiaTestCase {

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array('testGetUCAFieldData');

/**
 * Start Test callback
 *
 * @param string $method Method name
 * @return void
 */
	public function startTest($method) {
		parent::startTest($method);
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserParameter = ClassRegistry::init('UserParameter');
		$this->User = ClassRegistry::init('User');
		//Use object instantiation notation to call the constructor
		$this->UserCostsArchive = new UserCostsArchive(false, null, null, true);
		$this->Bet = ClassRegistry::init('Bet');
		$this->RepProductSetting = ClassRegistry::init('RepProductSetting');
		$this->PaymentFusion = ClassRegistry::init('PaymentFusion');
		$this->PaymentFusionRepCost = ClassRegistry::init('PaymentFusionRepCost');
		$this->ProductSetting = ClassRegistry::init('ProductSetting');
		$this->AchRepCost = ClassRegistry::init('AchRepCost');
		$this->Ach = ClassRegistry::init('Ach');
		$this->UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$this->ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$this->ProductsAndService = ClassRegistry::init('ProductsAndService');
		$this->MerchantPricing = ClassRegistry::init('MerchantPricing');
		$this->Merchant = ClassRegistry::init('Merchant');
		$this->activeProds = ClassRegistry::init('ProductsServicesType')->getList();
		$this->mId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
		$this->tstGatewayId = '9898ae02-3a93-484f-b41b-9261cdfe1db4';
		//create arbitrary user data
		$this->assocUsers = [
			'rep' => [
				'user_id' => $this->UUIDv4(),
				'ucp_id' => $this->UUIDv4()
				],
			'sm' => [
				'user_id' => $this->UUIDv4(),
				'ucp_id' => $this->UUIDv4(),
				'role_id' => $this->User->Role->field('id', ['name' => User::ROLE_SM])
				],
			'sm2' => [
				'user_id' => $this->UUIDv4(),
				'ucp_id' => $this->UUIDv4(),
				'role_id' => $this->User->Role->field('id', ['name' => User::ROLE_SM2])
				],
			'partner' => [
				'user_id' => $this->UUIDv4(),
				'ucp_id' => $this->UUIDv4()
				],
			'referrer' => [
				'user_id' => $this->UUIDv4(),
				'ucp_id' => $this->UUIDv4()
				],
			'reseller' => [
				'user_id' => $this->UUIDv4(),
				'ucp_id' => $this->UUIDv4()
				],
		];
		//bet tables
		$this->betTables = [
			'visa_bet_table_id' => '558ade3c-d180-423c-a50d-20ba34627ad4',
			'mc_bet_table_id' => '558ade3b-0a28-4c31-82a1-20ba34627ad4',
			'ds_bet_table_id' => 'fb37224a-5d68-499c-abc2-a1c892e0fde7',
			'db_bet_table_id' => '558ade3c-2d94-45ea-8ba1-20ba34627ad4',
			'ebt_bet_table_id' => '558ade3c-3b38-492f-adb4-20ba34627ad4',
			'amex_bet_table_id' => '558ade3b-9990-4f05-bfed-20ba34627ad4',
		];
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserCostsArchive);
		unset($this->ProductsAndService);
		unset($this->ProductsServicesType);
		unset($this->MerchantPricing);
		unset($this->Merchant);
		unset($this->UserCostsArchive);
		unset($this->RepProductSetting);
		unset($this->ProductSetting);

		parent::tearDown();
	}
/**
 * testSetUserCostArchiveDataCaluclationsWithAllUsers method
 * This method tests whether Model aliases that are generated automatically for
 * user related join queries match the aliases in conditions.
 * in the join.
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testUCostArchiveDataPfAndCorralAllUsers() {
		// Products that require calculation for user per item costs:
		$pNames = [
			'Corral License Fee',
			'Payment Fusion',
		];
		//Create test user compensation profiles
		$this->__createTestUcp();

		//Set test data
		$this->__saveTstData(true, true, false, true, true);

		//Create test RepProductSettings
		$this->__createRepProductSettings();

		//Create test PaymentFusionRepCost
		$this->__createPaymentFusionRepCost();

		//Create list of roles without Partner based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->__setRoleListWithout('Partner');

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		//Create Merchant ProductSetting
		$tstData = [
			[
				'merchant_id' => $this->mId,
				'products_services_type_id' => '5806a480-cdd8-4199-8d6f-319b34627ad4', //Corral
				'gral_fee_multiplier' => 2
			],
		];
		$this->ProductSetting->saveMany($tstData);
		//Create Merchant's Payment Fusion product data
		$tstData = [
			[
				'merchant_id' => $this->mId,
				'generic_product_mid' => '123456',
				'standard_num_devices' => 2,
				'standard_device_fee' => 15
			],
		];
		$this->PaymentFusion->save($tstData);
		$data = $this->__getData($pNames);

		//Calculate data
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				if (array_key_exists($prefx . "RepProductSetting", $data["Raw $pName"][0])) {
					$piCost = Hash::get($data["$pName"][$idx], 'per_item_cost');
					$this->assertNotEmpty($piCost);
					$this->assertEquals(0.250, $piCost);
				}
				if (array_key_exists($prefx . "PaymentFusionRepCost", $data["Raw $pName"][0])) {
					$piCost = Hash::get($data["$pName"][$idx], 'per_item_cost');
					$this->assertNotEmpty($piCost);
					$this->assertEquals(0.50, $piCost);
					$moCost = Hash::get($data["$pName"][$idx], 'monthly_statement_cost');
					$this->assertNotEmpty($moCost);
					$this->assertEquals(15, $moCost);
				}

				if (array_key_exists('ProductSetting', $data["Raw $pName"][0])) {
					$moCost = $data["Raw $pName"][0][$prefx . 'RepProductSetting']['rep_monthly_cost'];
					$multiplier = $data["Raw $pName"][0]["ProductSetting"]['gral_fee_multiplier'];
					$multiplicand = $data["Raw $pName"][0][$prefx . 'RepProductSetting']['provider_device_cost'];
					$expected = $moCost + ($multiplier * $multiplicand);
					$actual = Hash::get($data["$pName"][$idx], 'monthly_statement_cost');
					$this->assertEquals($expected, $actual);
				}
			}
		}
	}

/**
 * testUCostArchiveDataGateway1 method
 * This method tests whether some Gateway 1 product archive values are set correctly
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testUCostArchiveDataGateway1() {
		// Products that require calculation for user per item costs:
		$pNames = [
			'Gateway 1',
		];
		//Create test user compensation profiles
		$this->__createTestUcp();

		//Set test data
		$this->__saveTstData(true, true, false, true, true);

		//Create list of roles without Partner based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->__setRoleListWithout('Partner');

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		
		$this->UserCostsArchive->Merchant->Gateway1->updateAll(
				["gateway_id" => "'{$this->tstGatewayId}'"],
				["merchant_id = '{$this->mId}'"]
		);
		foreach ($this->assocUsers as $uDat) {
			$gatewayCostsData[] = [
				'user_compensation_profile_id' => $uDat['ucp_id'],
				'gateway_id' => $this->tstGatewayId,
				'rep_monthly_cost' => 10,
			];
		}
		$this->UserCostsArchive->User->UserCompensationProfile->GatewayCostStructure->saveMany($gatewayCostsData);

		$data = $this->__getData($pNames);
		foreach ($roles as $idx => $prefx) {
			// all monthly costs should be GatewayCostStructure.rep_monthly_cost + Gateway1.addl_rep_statement_cost = 15
			//Value for Gateway1.addl_rep_statement_cost is already stored in the DB
			$this->assertEqual(15, Hash::get($data["Gateway 1"][$idx], 'monthly_statement_cost'));
		}
	}

/**
 * testUCostArchiveDataAchRepCostsVericheck method
 * This method tests whether the corect Ach product data is retrieved
 * Also it verifies that the correct AchRepCosts are retrieved from the correct UCPs
 * The ACH product does not require an formulas or calculations for the archive process
 * Tesing with ach provider = Vericheck
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testUCostArchiveDataAchRepCostsVericheck() {
		// Products to save:
		$pNames = [
			'ACH',
		];
		//Create test user compensation profiles
		$this->__createTestUcp();

		//Set test data
		$this->__saveTstData(true, true, false, true, true);

		//Create test RepProductSettings
		$this->__createAchRepCosts();

		//Create list of roles without Partner based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->__setRoleListWithout('Partner');

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);

		//Create test Ach product data with provider vericheck
		$this->__createTestAch(
				['ach_provider_id' => '55bbcdd0-ed3c-4745-918a-1f3634627ad4']
			);
		$data = $this->__getData($pNames);
		//The ACH product does not require an formulas or calculations for the archive process
		//Check that data being archived is unchanged.
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$this->assertTrue(array_key_exists($prefx . "AchRepCost", $data["Raw $pName"][0]));
				//Check expected per item cost
				$piCost = Hash::get($data["$pName"][$idx], 'per_item_cost');
				$this->assertNotEmpty($piCost);
				$this->assertEquals(0.250, $piCost);

				//Check expected cost percent
				$costPct = Hash::get($data["$pName"][$idx], 'cost_pct');
				$this->assertNotEmpty($costPct);
				$this->assertEquals(10, $costPct);

				//Check expected monthly cost
				$moCost = Hash::get($data["$pName"][$idx], 'monthly_statement_cost');
				$this->assertNotEmpty($moCost);
				$this->assertEquals(5, $moCost);
			}
		}
	}

/**
 * testUCostArchiveDataAchRepCostsSagePayments method
 * This method tests whether the corect Ach product data is retrieved
 * Also it verifies that the correct AchRepCosts are retrieved from the correct UCPs
 * The ACH product does not require an formulas or calculations for the archive process
 * Tesing with ach provider = Sage Payments
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testUCostArchiveDataAchRepCostsSagePayments() {
		// Products to save:
		$pNames = [
			'ACH',
		];
		//Create test user compensation profiles
		$this->__createTestUcp();

		//Set test data
		$this->__saveTstData(true, true, false, true, true);

		//Create test RepProductSettings
		$this->__createAchRepCosts();

		//Create list of roles without Partner based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->__setRoleListWithout('Partner');

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);

		//Create test Ach product data with provider Sage Payments
		$this->__createTestAch(
				['ach_provider_id' => '5501ec0e-abe4-474e-a5d0-2f2034627ad4']
			);
		$data = $this->__getData($pNames);
		//The ACH product does not require an formulas or calculations for the archive process
		//Check that data being archived is unchanged.
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$this->assertTrue(array_key_exists($prefx . "AchRepCost", $data["Raw $pName"][0]));
				//Check expected per item cost
				$piCost = Hash::get($data["$pName"][$idx], 'per_item_cost');
				$this->assertNotEmpty($piCost);
				$this->assertEquals(0.20, $piCost);

				//Check expected cost percent
				$costPct = Hash::get($data["$pName"][$idx], 'cost_pct');
				$this->assertNotEmpty($costPct);
				$this->assertEquals(9, $costPct);

				//Check expected monthly cost
				$moCost = Hash::get($data["$pName"][$idx], 'monthly_statement_cost');
				$this->assertNotEmpty($moCost);
				$this->assertEquals(4, $moCost);
			}
		}
	}

/**
 * testSetJoinsVerifyModelAliaseMatch method
 * This method tests whether Model aliases that are generated automatically for
 * user related join queries match the aliases in conditions.
 * in the join.
 *
 * @covers UserCostsArchive::setJoins
 * @return void
 */
	public function testSetJoinsVerifyModelAliaseMatch() {
		foreach ($this->activeProds as $pId => $val) {
			if (!in_array($val, Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}"))) {
				//skip products for which field maps might not exist
				continue;
			}
			$joins = $this->UserCostsArchive->setJoins($pId, 'UserCostArchiveFieldMap');

			$matchNotFound = false;
			foreach ($joins as $join) {
				//Check for user related join aliases and continue if not present
				if (strpos($join['alias'], 'Rep') === false && strpos($join['alias'], 'Manager') === false &&
					strpos($join['alias'], 'Manager2') === false && strpos($join['alias'], 'Partner') === false &&
					strpos($join['alias'], 'Ref') === false && strpos($join['alias'], 'Res') === false) {
					continue;
				}
					$condStr = join($join['conditions'], ' ');
					$this->assertContains($join['alias'], $condStr);
			}
		}
	}

/**
 * testSetUserCostArchiveDataCaluclationsWithoutPartner method
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testSetUserCostArchiveDataCaluclationsWithoutPartner() {
		// Products that require calculation for user per item costs:
		$pNames = [
			'Visa Sales',
			'Visa Dial Sales',
			'Visa Non Dial Sales',
			'MasterCard Sales',
			'MasterCard Dial Sales',
			'MasterCard Non Dial Sales',
			//Users do not have Bet's data in BetFixture for the products below
			//However this test should still succeed
			'Discover Sales',
			'Discover Dial Sales',
			'Discover Non Dial Sales',
			'American Express Sales',
			'American Express Dial Sales',
			'American Express Non Dial Sales',
		];
		//Set test data without partner
		$this->__saveTstData(true, true, false, true, true);
		//Create test user compensation profiles
		$this->__createTestUcp();
		//Create test user bets
		$this->__createTestBets();

		//Create list of roles without Partner based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->__setRoleListWithout('Partner');

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		//Calculate data
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$piCost = 0;
				$varCost = 0;
				$wlessCost = Hash::get($data["Raw $pName"][0], "MerchantPricing.wireless_auth_cost");
				if (array_key_exists('pi_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$piCost = $data["Raw $pName"][0][$prefx . 'Bet']['pi_cost'];
				}

				if (array_key_exists('sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['sales_cost'];
				}
				if (array_key_exists('dial_sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['dial_sales_cost'];
				}
				if (array_key_exists('non_dial_sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['non_dial_sales_cost'];
				}
				if (array_key_exists('auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['auth_cost'];
				}
				if (array_key_exists('dial_auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['dial_auth_cost'];
				}
				if (array_key_exists('non_dial_auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['non_dial_auth_cost'];
				}
				$expected = $wlessCost + $piCost + $varCost;
				$actual = Hash::get($data["$pName"][$idx], 'per_item_cost');
				$this->assertEquals($expected, $actual);
				if ($prefx === 'Ref' || $prefx === 'Res') {
					if ($prefx === 'Ref') {
						//assert referrer data not null but null for reseler
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'ref_p_type'));
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'ref_p_value'));
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'ref_p_pct'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'res_p_type'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'res_p_value'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'res_p_pct'));
					} else { //vice-versa
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'res_p_type'));
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'res_p_value'));
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'res_p_pct'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'ref_p_type'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'ref_p_value'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'ref_p_pct'));
					}

				} else {
					//assert all null
					$this->assertNull(Hash::get($data["$pName"][$idx], 'ref_p_type'));
					$this->assertNull(Hash::get($data["$pName"][$idx], 'ref_p_value'));
					$this->assertNull(Hash::get($data["$pName"][$idx], 'ref_p_pct'));
					$this->assertNull(Hash::get($data["$pName"][$idx], 'res_p_type'));
					$this->assertNull(Hash::get($data["$pName"][$idx], 'res_p_value'));
					$this->assertNull(Hash::get($data["$pName"][$idx], 'res_p_pct'));
				}
			}
		}
	}

/**
 * testSetUserCostArchiveDataCaluclationsWithAllUsers method
 * This method tests whether Model aliases that are generated automatically for
 * user related join queries match the aliases in conditions.
 * in the join.
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testSetUserCostArchiveDataCaluclationsWithAllUsers() {
		// Products that require calculation for user per item costs:
		$pNames = [
			'Visa Sales',
			'Visa Dial Sales',
			'Visa Non Dial Sales',
			'MasterCard Sales',
			'MasterCard Dial Sales',
			'MasterCard Non Dial Sales',
			//Users do not have Bet's data in BetFixture for the products below
			//However this test should still succeed
			'Discover Sales',
			'Discover Dial Sales',
			'Discover Non Dial Sales',
			'American Express Sales',
			'American Express Dial Sales',
			'American Express Non Dial Sales',
		];
		//Create test user compensation profiles
		$this->__createTestUcp(true);

		//Set test data
		$this->__saveTstData(true, true, true, true, true);

		//Create test user bets
		$this->__createTestBets();

		//Create list of roles based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->UserCostsArchive->_joinRolePrefixes;

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		//Calculate data
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$piCost = 0;
				$varCost = 0;
				$wlessCost = Hash::get($data["Raw $pName"][0], "MerchantPricing.wireless_auth_cost");
				if (array_key_exists('pi_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$piCost = $data["Raw $pName"][0][$prefx . 'Bet']['pi_cost'];
				}

				if (array_key_exists('sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['sales_cost'];
				}
				if (array_key_exists('dial_sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['dial_sales_cost'];
				}
				if (array_key_exists('non_dial_sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['non_dial_sales_cost'];
				}
				if (array_key_exists('auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['auth_cost'];
				}
				if (array_key_exists('dial_auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['dial_auth_cost'];
				}
				if (array_key_exists('non_dial_auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['non_dial_auth_cost'];
				}
				$expected = $wlessCost + $piCost + $varCost;
				$this->assertNotEmpty($expected);
				$actual = Hash::get($data["$pName"][$idx], 'per_item_cost');
				$this->assertEquals($expected, $actual);
			}
		}
	}

/**
 * testSetUserCostArchiveDataCaluclationsWithAllUsers method
 * This method tests whether Model aliases that are generated automatically for
 * user related join queries match the aliases in conditions.
 * in the join.
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testSetUserCostArchiveDataCaluclationsWithRepAndMgr() {
		// Products that require calculation for user per item costs:
		$pNames = [
			'Visa Sales',
			'Visa Dial Sales',
			'Visa Non Dial Sales',
			'MasterCard Sales',
			'MasterCard Dial Sales',
			'MasterCard Non Dial Sales',
			//Users do not have Bet's data in BetFixture for the products below
			//However this test should still succeed
			'Discover Sales',
			'Discover Dial Sales',
			'Discover Non Dial Sales',
			'American Express Sales',
			'American Express Dial Sales',
			'American Express Non Dial Sales',
		];
		//Create test user compensation profiles
		$this->__createTestUcp();
		//Set test data
		$this->__saveTstData(true, false, false, false, false);
		//Create test user bets
		$this->__createTestBets();

		//Create list of roles with only rep and mgr
		$roles = ['Rep', 'Manager'];

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		//Calculate data
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$piCost = 0;
				$varCost = 0;
				$wlessCost = Hash::get($data["Raw $pName"][0], "MerchantPricing.wireless_auth_cost");
				if (array_key_exists('pi_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$piCost = $data["Raw $pName"][0][$prefx . 'Bet']['pi_cost'];
				}

				if (array_key_exists('sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['sales_cost'];
				}
				if (array_key_exists('dial_sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['dial_sales_cost'];
				}
				if (array_key_exists('non_dial_sales_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['non_dial_sales_cost'];
				}
				if (array_key_exists('auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['auth_cost'];
				}
				if (array_key_exists('dial_auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['dial_auth_cost'];
				}
				if (array_key_exists('non_dial_auth_cost', $data["Raw $pName"][0][$prefx . "Bet"])) {
					$varCost = $data["Raw $pName"][0][$prefx . 'Bet']['non_dial_auth_cost'];
				}
				$expected = $wlessCost + $piCost + $varCost;
				$this->assertNotEmpty($expected);
				$actual = Hash::get($data["$pName"][$idx], 'per_item_cost');
				$this->assertEquals($expected, $actual);
			}
		}
	}

/**
 * testSetUserCostArchiveDataCaluclationsWithAllUsers method
 * This method tests whether Model aliases that are generated automatically for
 * user related join queries match the aliases in conditions.
 * in the join.
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testSetUserCostArchiveSalesAndDiscountDataNoPartner() {
		$pNames = [
			'Visa Discount',
			'MasterCard Discount',
			'American Express Discount',
			'Discover Discount',
			'Debit Sales',
			'Debit Discount',
			'EBT Sales',
			'EBT Discount',
		];

		//Add Test records to UserProductsRisk
		$pNamesIds = $this->ProductsServicesType->find('list', ['conditions' => ['products_services_description' => $pNames], 'fields' => ['products_services_description', 'id']]);
		foreach ($pNamesIds as $pId) {
			$riskDat[] = [
					'merchant_id' => $this->mId,
					'user_id' => $this->assocUsers['rep']['user_id'],
					'products_services_type_id' => $pId,
					'risk_assmnt_pct' => .99,
				];

			$riskDat[] = [
					'merchant_id' => $this->mId,
					'user_id' => $this->assocUsers['sm']['user_id'],
					'products_services_type_id' => $pId,
					'risk_assmnt_pct' => .59,
				];
		}
		$this->UserCostsArchive->User->UsersProductsRisk->saveMany($riskDat);

		//Create test user compensation profiles
		$this->__createTestUcp();

		//Create test user bets
		$this->__createTestBets();

		//Create test monthly costs data
		foreach ($this->assocUsers as $udat) {
			$monthlyCostsData[] = [
				'user_compensation_profile_id' => $udat['ucp_id'],
				'bet_network_id' => '55104e48-7c4c-44da-bf83-225e34627ad4',
				'credit_cost' => 10,
				'debit_cost' => 11,
				'ebt_cost' => 12,
			];
		}

		$this->UserCostsArchive->User->UserCompensationProfile->RepMonthlyCost->saveMany($monthlyCostsData);

		//Set test data
		$this->__saveTstData(true, false, false, false, false);

		//Create list of roles with only rep
		$roles = ['Rep', 'Manager'];

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$piCost = 0;
				$varCost = 0;
				$this->assertNull(Hash::get($data["Raw $pName"][0], "MerchantPricing.wireless_auth_cost"));

				//Check that UserCostArchive.cost_pct for all Discount products is NOT calculated/modified from original Raw data
				if (strpos($pName, 'Discount') !== false) {
					$expected = $data["Raw $pName"][0][$prefx . 'Bet']['pct_cost'];
					$actual = $data["$pName"][$idx]['cost_pct'];
					$this->assertNotEmpty($expected);
					$this->assertEquals($expected, $actual);

					//Debit/EBT in this subset does not archive per_item_cost, only check all others
					if (strpos($pName, 'Debit') === false && strpos($pName, 'EBT') === false) {
						$expected = $data["Raw $pName"][0][$prefx . 'Bet']['pi_cost'];
						$actual = $data["$pName"][$idx]['per_item_cost'];
						$this->assertNotEmpty($expected);
						$this->assertEquals($expected, $actual);
					}
				}

				//Check that debit and ebt in this subset of products do not contain per_item_cost
				if (strpos($pName, 'Debit') !== false || strpos($pName, 'EBT') !== false) {
					$this->assertNull( $data["$pName"][$idx]['per_item_cost']);
				}

				//Check risk assessment data is present for some Discount products but not all as spec indicates
				if (strpos($pName, 'Debit') === false && strpos($pName, 'EBT') === false && strpos($pName, 'Credit') === false) {
					$expected = Hash::get($data["Raw $pName"][0]['RepProductsRisk'], 'risk_assmnt_pct');
					$this->assertEquals($expected, .99);

					$expected = Hash::get($data["Raw $pName"][0]['ManagerProductsRisk'], 'risk_assmnt_pct');
					$this->assertEquals($expected, .59);
				}
			}
		}
	}

/**
 * testSetUserCostArchiveDataCaluclationsWithAllUsers method
 * This method tests whether Model aliases that are generated automatically for
 * user related join queries match the aliases in conditions.
 * in the join.
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testSetUserCostArchiveMonthlyData() {
		$pNames = [
			'Credit Monthly',
			'Debit Monthly',
			'EBT Monthly',
		];

		//Add Test records to UserProductsRisk
		$pNamesIds = $this->ProductsServicesType->find('list', ['conditions' => ['products_services_description' => $pNames], 'fields' => ['products_services_description', 'id']]);

		//Create test bet data
		foreach ($this->assocUsers as $uDat) {
			$monthlyCostsData[] = [
				'user_compensation_profile_id' => $uDat['ucp_id'],
				'bet_network_id' => '55104e48-7c4c-44da-bf83-225e34627ad4',
				'credit_cost' => 10,
				'debit_cost' => 11,
				'ebt_cost' => 12,
			];
			$gatewayCostsData[] = [
				'user_compensation_profile_id' => $uDat['ucp_id'],
				'gateway_id' => $this->tstGatewayId,
				'rep_monthly_cost' => 10,
			];
		}

		$this->UserCostsArchive->User->UserCompensationProfile->RepMonthlyCost->saveMany($monthlyCostsData);
		$this->UserCostsArchive->User->UserCompensationProfile->GatewayCostStructure->saveMany($gatewayCostsData);

		//Set test data
		$this->__saveTstData(true, false, false, false, false);

		//Create test user compensation profiles
		$this->__createTestUcp();

		//Create test user bets
		$this->__createTestBets();
		//Create list of roles with only rep
		$roles = ['Rep', 'Manager'];

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			$this->assertNull(Hash::get($data["Raw $pName"][0], "MerchantPricing.wireless_auth_cost"));

			foreach ($roles as $idx => $prefx) {
				//Check that debit, ebt and credit in this subset of products contain monthly statement cost is calculated correctly
				$expected = Hash::get($data["Raw $pName"][0][$prefx . 'RepMonthlyCost'], 'credit_cost');
				$totalGwMnthlyCost = Hash::get($data, "Raw $pName.0." . $prefx . "GatewayCostStructure.rep_monthly_cost");
				if (!empty($expected)) {
					$totalWirelessCost = $data["Raw $pName"][0]['MerchantPricing']['total_wireless_term_cost'];
					//This value should match what was set in __saveTstsData for this product
					$this->assertEqual(10, Hash::get($data["Raw $pName"][0], "MerchantPricing.total_wireless_term_cost"));
					$this->assertEquals($expected, 10);
					$expected = $expected + $totalGwMnthlyCost + $totalWirelessCost;
					$actual = $data["$pName"][$idx]['monthly_statement_cost'];
					$this->assertEquals($expected, $actual);
				}

				$expected = Hash::get($data["Raw $pName"][0][$prefx . 'RepMonthlyCost'], 'debit_cost');
				if (!empty($expected)) {
					$this->assertEquals($expected, 11);
					$actual = $data["$pName"][$idx]['monthly_statement_cost'];
					$this->assertEquals($expected, $actual);
				}

				$expected = Hash::get($data["Raw $pName"][0][$prefx . 'RepMonthlyCost'], 'ebt_cost');
				if (!empty($expected)) {
					$this->assertEquals($expected, 12);
					$actual = $data["$pName"][$idx]['monthly_statement_cost'];
					$this->assertEquals($expected, $actual);
				}
			}
		}
	}

/**
 * testSetUserCostArchiveSpecialCaseDiscoverUserNotPaid method
 *
 * this test makes sure the special case for discover discount products works. The expectation is that if merchant_pricings.ds_user_not_paid = true the following should happen
 * 	-data always archives for axia house
 *	- If rep manager or manager 2 do not archive data
 *  - for all other users check if UserParameter.'user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP and  'value' => 1 and if so do not archive otherwise if 'value' => 0 then do archive
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 * @return void
 */
	public function testSetUserCostArchiveSpecialCaseDiscoverUserNotPaid() {
		$pNames = [
			'Discover Discount',
		];
		//Create test UserParameters data
		$this->__createTestUParamsData();

		//Create test user compensation profiles
		$this->__createTestUcp(true);

		//Create test user bets
		$this->__createTestBets();

		//Set test data with do not pay on discover set to true
		$this->__saveTstData(true, true, true, true, true, true);

		//Create list of roles without Partner based from $this->UserCostsArchive->_joinRolePrefixes
		$roles = $this->UserCostsArchive->_joinRolePrefixes;

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		//All data that is supposed to be archived should be present in this iteration except for rep, manager manager2
		foreach ($pNames as $pName) {
			$this->assertCount(count($roles), Hash::extract($data["$pName"], '{n}'));
			foreach ($roles as $idx => $prefx) {
				$piCost = 0;
				$varCost = 0;
				//Since we are not paying discover nothing should archive for rep manager and manager 2
				if ($prefx === 'Rep' || $prefx === 'Manager' || $prefx === 'Manager2') {
					$this->assertEmpty(Hash::get($data["$pName"][$idx], 'cost_pct'));
					$this->assertEmpty(Hash::get($data["$pName"][$idx], 'per_item_cost'));
				} else {

					if ($pName === 'Discover Discount') {
						//No amounts should be rpesent since we are not paying for discover
						$this->assertNull(Hash::get($data["$pName"][$idx], 'cost_pct'));
						$this->assertNull(Hash::get($data["$pName"][$idx], 'per_item_cost'));
					} else {
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'per_item_cost'));
						$this->assertEquals($data["Raw $pName"][0][$prefx . 'Bet']['settlement_cost'], $data["$pName"][$idx]['per_item_cost']);
					}
				}
			}
		}
		unset($data);

		//Modfy saved data to create a test case where partner ref res data >>is NOT<< present
		$this->UserParameter->updateAll(
				['UserParameter.value' => 1],
				['UserParameter.user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP]
			);
		$data = $this->__getData($pNames);

		//In this case nothing should be archived since none of the users are getting paid on these products
		foreach ($pNames as $pName) {
				$this->assertEmpty(Hash::get($data["$pName"], 'cost_pct'));
				$this->assertEmpty(Hash::get($data["$pName"], 'per_item_cost'));
				$this->assertEmpty(Hash::get($data["$pName"], 'monthly_statement_cost'));
				$this->assertEmpty(Hash::get($data["$pName"], 'risk_assmnt_pct'));
				$this->assertEmpty(Hash::get($data["$pName"], 'risk_assmnt_per_item'));
				$this->assertEmpty(Hash::get($data["$pName"], 'ref_p_type'));
				$this->assertEmpty(Hash::get($data["$pName"], 'ref_p_value'));
				$this->assertEmpty(Hash::get($data["$pName"], 'ref_p_pct'));
				$this->assertEmpty(Hash::get($data["$pName"], 'res_p_type'));
				$this->assertEmpty(Hash::get($data["$pName"], 'res_p_value'));
				$this->assertEmpty(Hash::get($data["$pName"], 'res_p_pct'));
		}
	}

/**
 * testAxiaHouseUserCostsArchiveDiscoverCase metod
 * Test that merchant.user_id = axia house with merchan.pricing.ds_user_not_paid = true should archive data for axia house always but not partner, etc.
 *
 * @covers UserCostsArchive::setUserCostArchiveData
 *	@return void
 */

	public function testAxiaHouseUserCostsArchiveDiscoverCase() {
		$pNames = [
			'Discover Discount',
		];
		//Make the arbitrary rep the axia house rep
		$axiaUser = [
			'id' => $this->assocUsers['rep']['user_id'],
			'user_first_name' => UserCostsArchive::AXIA_USER,
			'user_last_name' => 'House',
		];
		$this->User->create();
		$this->User->save($axiaUser, false);

		//Create test UserParameters data
		$this->__createTestUParamsData();

		//Create test user compensation profiles
		$this->__createTestUcp(true);

		//Create test user bets
		$this->__createTestBets();

		//Set test data with do not pay on discover set to true
		$this->__saveTstData(true, true, true, true, true, true);

		//Assign all active products $this->mId
		$this->__saveProdsForTst($this->mId);
		$data = $this->__getData($pNames);

		//Modfy saved data to create a test case where partner ref res data >>is NOT<< present
		$this->UserParameter->updateAll(
				['UserParameter.value' => 1],
				['UserParameter.user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP]
			);

		//get new data
		$data = $this->__getData($pNames);

		$roles = $this->UserCostsArchive->_joinRolePrefixes;
		//In this case nothing should be archived since none of the users are getting paid on these products >>EXCEPT AXIA HOUSE<<
		foreach ($pNames as $pName) {
			foreach ($roles as $idx => $prefx) {
				if ($prefx === 'Rep') {//Axia house is the rep in this scenareo
					if ($pName === 'Discover Discount') {
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'cost_pct'));
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'per_item_cost'));
						$this->assertEquals($data["Raw $pName"][0][$prefx . 'Bet']['pct_cost'], $data["$pName"][$idx]['cost_pct']);
						$this->assertEquals($data["Raw $pName"][0][$prefx . 'Bet']['pi_cost'], $data["$pName"][$idx]['per_item_cost']);
					} else {
						$this->assertNotNull(Hash::get($data["$pName"][$idx], 'per_item_cost'));
						$this->assertEquals($data["Raw $pName"][0][$prefx . 'Bet']['settlement_cost'], $data["$pName"][$idx]['per_item_cost']);
					}
					$this->assertFalse(Hash::get($data["$pName"][$idx], 'is_hidden'));
				}
			}
		}
	}

/**
 *	__createTestUParamsData metod
 *	create test data using specific product 'Discover Discount'
 *
 *	@return void
 */
	private function __createTestUParamsData() {
		foreach ($this->assocUsers as $uDat) {
			$usrParams[] = [
					'user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP,
					'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
					'products_services_type_id' => '566f5f47-59c4-456a-9e1b-23f534627ad4',
					'associated_user_id' => $uDat['user_id'], //self associated in UserParameter
					'user_compensation_profile_id' => $uDat['ucp_id'],
					'type' => 'Lorem ipsum dolor sit amet',
					'value' => 0,
					'created' => '2014-04-14 14:59:21',
					'modified' => '2014-04-14 14:59:21',
					'is_multiple' => 0
			];
			$usrParams[] = [
					'user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP,
					'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
					'products_services_type_id' => '566f5f47-5b44-4096-8d66-23f534627ad4',
					'associated_user_id' => $uDat['user_id'], //self associated in UserParameter
					'user_compensation_profile_id' => $uDat['ucp_id'],
					'type' => 'Lorem ipsum dolor sit amet',
					'value' => 0,
					'created' => '2014-04-14 14:59:21',
					'modified' => '2014-04-14 14:59:21',
					'is_multiple' => 0
			];
		}

		$this->UserParameter->saveMany($usrParams);
	}

/**
 *	__saveProdsForTst metod
 *
 *	@param string $mId a merchant id
 *	@return void
 */
	private function __saveProdsForTst($mId) {
		foreach ($this->activeProds as $pId => $val) {
			if ($this->__mapExists($val)) {
				$tstData[] = [
						'merchant_id' => $mId,
						'products_services_type_id' => $pId,
				];
			}
		}
		$this->ProductsAndService->saveMany($tstData);
	}
/**
 * __mapExists metod
 * returns true of false depending on wether a field map has been defined in Configure::read("ArchiveFieldMap") for the given product name
 *
 *	@param string $productName a product name
 *	@return bool
 */
	private function __mapExists($productName) {
		return in_array($productName, Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}"));
	}

/**
 * __getData
 *
 * @param array $pNames product names
 * @return array
 */
	private function __getData($pNames) {
		$pNamesIds = $this->ProductsServicesType->find('list', ['conditions' => ['products_services_description' => $pNames], 'fields' => ['products_services_description', 'id']]);

		foreach ($pNamesIds as $name => $id) {
			//Raw data is data brefore calculations staight from the database/fixtures
			$dat["Raw $name"] = $this->UserCostsArchive->getUserCostArchiveData($this->mId, $id);
			$dat[$name] = $this->UserCostsArchive->setUserCostArchiveData($this->mId, $id, 'UserCostArchiveFieldMap');
		}
		return $dat;
	}
/**
 * __setRoleListWithout()
 *
 * @param string $roleName role name
 * @return array
 * @throws \Exception array
 */
	private function __setRoleListWithout($roleName) {
		$roles = $this->UserCostsArchive->_joinRolePrefixes;
		$idxFound = array_search('Partner', $roles);
		if ($idxFound === false) {
			throw new Exception("Role $roleName not found in UserCostsArchive::_joinRolePrefixes array.");
		}
		unset($roles[$idxFound]);
		//Reindex $roles by merging into an empty array
		$roles = array_merge(array(), $roles);
		return $roles;
	}
/**
 * __saveTstData()
 *
 * @param bool $addMgr whether to include this user in the test data as a user associated with the merchant
 * @param bool $addMgr2 whether to include this user in the test data as a user associated with the merchant
 * @param bool $addPartner whether to include this user in the test data as a user associated with the merchant
 * @param bool $addRef whether to include this user in the test data as a user associated with the merchant
 * @param bool $addRes whether to include this user in the test data as a user associated with the merchant
 * @param bool $noDiscover whether to pay users on discover discount/settled items
 * @return void
 */
	private function __saveTstData($addMgr, $addMgr2, $addPartner, $addRef, $addRes, $noDiscover = false) {
		//Update specific Merchant with arbitrary associated users
		$merchTstData = [
				'id' => $this->mId,
				'user_id' => $this->assocUsers['rep']['user_id'],
				'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
				'bet_network_id' => '55104e48-7c4c-44da-bf83-225e34627ad4',
				'sm_user_id' => ($addMgr)? $this->assocUsers['sm']['user_id'] : null,
				'sm2_user_id' => ($addMgr2)? $this->assocUsers['sm2']['user_id'] : null,
				'partner_id' => ($addPartner)? $this->assocUsers['partner']['user_id'] : null,
				'referer_id' => ($addRef)? $this->assocUsers['referrer']['user_id'] : null,
				'reseller_id' => ($addRes)? $this->assocUsers['reseller']['user_id'] : null,
				'ref_p_type' => 'arbitrary_type',
				'ref_p_value' => 20,
				'res_p_type' => 'arbitrary_type',
				'res_p_value' => 15,
				'ref_p_pct' => 80,
				'res_p_pct' => 100,
		];

		$this->Merchant->save($merchTstData, ['validate' => false]);

		//check if there is already test data for this merchant
		$mpId = $this->MerchantPricing->field('id', ['merchant_id' => $this->mId]);
		$mpId = empty($mpId)? $this->UUIDv4 : $mpId;
		//Create MerchantPricing test data
		$tstData = [
				'id' => $mpId,
				'merchant_id' => $this->mId,
				'gateway_id' => $this->tstGatewayId,
				'visa_bet_table_id' => $this->betTables['visa_bet_table_id'],
				'mc_bet_table_id' => $this->betTables['mc_bet_table_id'],
				'ds_bet_table_id' => $this->betTables['ds_bet_table_id'],
				'db_bet_table_id' => $this->betTables['db_bet_table_id'],
				'ebt_bet_table_id' => $this->betTables['ebt_bet_table_id'],
				'amex_bet_table_id' => $this->betTables['amex_bet_table_id'],
				'total_wireless_term_cost' => 10,
				'ds_user_not_paid' => $noDiscover,
				'debit_access_fee' => 25,
				'ebt_access_fee' => 20,
		];
		$this->MerchantPricing->save($tstData);
	}

/**
 * __createTestAch
 * Create test data for the ACH product for users
 *
 * @return void
 */
	private function __createTestAch($data = []) {
		//Create test Ach product  data
		$tstData = [
				'id' => '3187bdf4-5dcd-43d6-b3e2-8cdbe2ca0430', //existing record
				'merchant_id' => $this->mId,
		];
		if (!empty($data)) {
			$tstData = array_merge($tstData, $data);
		}
		$this->Ach->save($tstData);
	}
/**
 * __createTestBetTables
 * Create test bets for users
 *
 * @return void
 */
	private function __createTestBets() {
		foreach ($this->assocUsers as $user) {
			foreach ($this->betTables as $btId) {
				$uBetDat[] = [
					'bet_table_id' => $btId,
					'card_type_id' => $this->UUIDv4(),
					'bet_network_id' => '55104e48-7c4c-44da-bf83-225e34627ad4',
					'pct_cost' => .1,
					'pi_cost' => .2,
					'additional_pct' => .3,
					'sales_cost' => .4,
					'auth_cost' => 10,
					'dial_auth_cost' => 5,
					'non_dial_auth_cost' => 4,
					'settlement_cost' => 15,
					'user_compensation_profile_id' => $user['ucp_id'],
					'non_dial_sales_cost' => 8,
					'dial_sales_cost' => 9,
				];
			}
		}

		$this->Bet->saveMany($uBetDat);
	}
/**
 * __createRepProductSettings
 * Create test data
 *
 * @return void
 */
	private function __createRepProductSettings() {
		foreach ($this->assocUsers as $user) {
				$rPSetDat[] = [
					'user_compensation_profile_id' => $user['ucp_id'],
					'products_services_type_id' => '5806a480-cdd8-4199-8d6f-319b34627ad4', //Corral
					'rep_monthly_cost' => 10,
					'rep_per_item' => .25,
					'provider_device_cost' => 5,
				];
		}
		$this->RepProductSetting->saveMany($rPSetDat);
	}
/**
 * __createPaymentFusionRepCost
 * Create test data
 *
 * @return void
 */
	private function __createPaymentFusionRepCost() {
		foreach ($this->assocUsers as $user) {
				$rPfData[] = [
					'user_compensation_profile_id' => $user['ucp_id'],
					'rep_monthly_cost' => 15,
					'rep_per_item' => .5,
					'standard_device_cost' => 1,
					'vp2pe_device_cost' => 2,
					'pfcc_device_cost' => 3,
					'vp2pe_pfcc_device_cost' => 4,
				];
		}
		$this->PaymentFusionRepCost->saveMany($rPfData);
	}
/**
 * __createAchRepCosts
 * Create test data
 *
 * @return void
 */
	private function __createAchRepCosts() {
		foreach ($this->assocUsers as $user) {
				$achRcDat[] = [
					'user_compensation_profile_id' => $user['ucp_id'],
					'ach_provider_id' => '55bbcdd0-ed3c-4745-918a-1f3634627ad4', //Vericheck Ach provider
					'rep_rate_pct' => 10,
					'rep_per_item' => .25,
					'rep_monthly_cost' => 5,
				];
				$achRcDat[] = [
					'user_compensation_profile_id' => $user['ucp_id'],
					'ach_provider_id' => '5501ec0e-abe4-474e-a5d0-2f2034627ad4', //Sage Payments Ach provider
					'rep_rate_pct' => 9,
					'rep_per_item' => .20,
					'rep_monthly_cost' => 4,
				];
		}
		$this->AchRepCost->saveMany($achRcDat);
	}

/**
 * __createTestUcp
 *
 * @param bool $makePartnerRep set true to save rep UCP as partner-rep otherwise just as rep
 * @return void
 */
	private function __createTestUcp($makePartnerRep = false) {
		foreach ($this->assocUsers as $key => $user) {
			$ucpData = [
				'id' => $user['ucp_id'],
				'user_id' => $user['user_id'],
				'role_id' => Hash::get($user, 'role_id')
			];
			if ($makePartnerRep && $key === 'rep') {
				$ucpData['is_default'] = false;
				$ucpData['is_partner_rep'] = true;
				$ucpData['partner_user_id'] = $this->assocUsers['partner']['user_id'];
			}
			$this->UserCompensationProfile->create();
			$this->UserCompensationProfile->save($ucpData);
		}
	}

/**
 * testGetUCAFieldData method
 * 
 * @covers UserCostsArchive::getUCAFieldData
 * @return void
 */
	public function testGetUCAFieldData() {
		//create test data for 6 fictitious users
		for ($x = 1; $x <= 6; $x++) {
			$data[] = [
				'merchant_id' => 'merch123',
				'user_id' => 'User' . $x,
				'cost_pct' => 'User' . $x . 'Stuff',
				'monthly_statement_cost' => $x * 100
			];
		}

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User1", 'monthly_statement_cost');
		$this->assertEquals(100, $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User1", 'cost_pct');
		$this->assertEquals('User1Stuff', $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User2", 'cost_pct');
		$this->assertEquals('User2Stuff', $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User2", 'monthly_statement_cost');
		$this->assertEquals(200, $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User3", 'cost_pct');
		$this->assertEquals('User3Stuff', $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User3", 'monthly_statement_cost');
		$this->assertEquals(300, $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User4", 'cost_pct');
		$this->assertEquals('User4Stuff', $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User4", 'monthly_statement_cost');
		$this->assertEquals(400, $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User5", 'cost_pct');
		$this->assertEquals('User5Stuff', $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User5", 'monthly_statement_cost');
		$this->assertEquals(500, $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User6", 'cost_pct');
		$this->assertEquals('User6Stuff', $actual);

		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User6", 'monthly_statement_cost');
		$this->assertEquals(600, $actual);

		//non existing field
		$actual = $this->UserCostsArchive->getUCAFieldData($data, "User1", 'non_existing_field');
		$this->assertNull($actual);
		//empty user id
		$actual = $this->UserCostsArchive->getUCAFieldData($data, null, 'cost_pct');
		$this->assertNull($actual);
		//non matching user id
		$actual = $this->UserCostsArchive->getUCAFieldData($data, 'non_matching_user_id', 'cost_pct');
		$this->assertNull($actual);
	}

/**
 * testInitQueryMap method
 * 
 * @covers UserCostsArchive::initQueryMap
 * @return void
 */
	public function testInitQueryMap() {
		$products = $this->UserCostsArchive->MerchantPricingArchive->getArchivableProducts();
		foreach ($products as $id => $name) {
			$fields = $this->UserCostsArchive->getMappedQuery('fields', $id);
			$joins = $this->UserCostsArchive->getMappedQuery('joins', $id);
			$this->assertNotEmpty($fields);
			$this->assertNotEmpty($joins);
		}
	}

/**
 * testCheckMerchantUserFields method
 * 
 * @covers UserCostsArchive::checkMerchantUserFields
 * @return void
 */
	public function testCheckMerchantUserFields() {
		//Test error returned with bogus data
		$data = ['user_id' => CakeText::uuid()];
		$modelData['UserCostsArchive']['merchant_id'] = CakeText::uuid();
		$this->UserCostsArchive->set($modelData);
		
		$actual = $this->UserCostsArchive->checkMerchantUserFields($data);
		$expected = ' is not assigned to this merchant! You must first assign that user to this merchant from the Overview page then try again';
		$this->assertSame($expected, $actual);

		//test with existing data
		$data = ['user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e'];
		$modelData['UserCostsArchive']['merchant_id'] = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
		$this->UserCostsArchive->set($modelData);
		
		$actual = $this->UserCostsArchive->checkMerchantUserFields($data);
		$this->assertTrue($actual);
	}
}
