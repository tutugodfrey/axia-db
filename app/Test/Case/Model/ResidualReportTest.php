<?php
App::uses('ResidualReport', 'Model');
App::uses('CommissionPricing', 'Model');
App::uses('RolePermissions', 'Lib');
App::uses('User', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('SearchByUserIdBehavior', 'Model/Behavior');

/**
 * Merchant Reject Test Case
 *
 */
class ResidualReportTest extends AxiaTestCase {

	const SM_ID = '30b6ae64-efd2-4c59-a73f-535c696d8beb';

	const SM2_ID = '3a593d5c-7704-47c8-a466-0a20eaf9f873';

	const REF_ID = 'f62fdc3e-5263-466d-bc14-f0533eb636e3';

	const RES_ID = '86b2a947-d858-40ba-9c4d-bdba6b066952';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$userId = AxiaTestCase::USER_ADMIN_ID;
		$SearchByUserIdBehavior = $this->getMock('SearchByUserIdBehavior', ['_getLoggedInUser']);
		ClassRegistry::addObject('SearchByUserIdBehavior', $SearchByUserIdBehavior);

		$this->ResidualReport = $this->_mockModel('ResidualReport', ['_getCurretestGetReportDatantUser'], [
				'_getCurrentUser' => ['value' => $userId],
			]);

		$this->ResidualReport->Behaviors->attach('SearchByUserId');
		$SearchByUserIdBehavior
			->expects($this->any())
			->method('_getLoggedInUser')
			->will($this->returnValue($userId));
		//These are products that have formulas and calculations in common
		$this->commonProducts = [
			"American Express Non Dial Sales" => "dfd21bee-ccaf-47f4-b83e-356d1e1a1132",
			"American Express Settled Items" => "13c449c9-00d7-4287-bfcc-342fac9a19e5",
			"Discover Dial Sales" => "28b4aa2e-2559-4eee-b29a-b25808e16a10",
			"Discover Non Dial Sales" => "ca336477-3c6a-417b-9594-7f452c2a266d",
			"Discover Sales" => "38d6c5b3-b6ca-4be8-9998-8b78976f0767",
			"Discover Settled Items" => "64133a8f-34b5-43fb-8dcb-28f507e93e6c",
			"MasterCard Dial Sales" => "728ff26a-8209-4cc4-86ae-e79a2df21e7b",
			"MasterCard Non Dial Sales" => "fdafb816-4a0c-4e81-84df-96d794041372",
			"MasterCard Sales" => "28dd4748-9699-41b9-9391-2524d3941018",
			"MasterCard Settled Items" => "cba097b8-6af9-4835-8422-2fee99e6b15a",
			"Visa Dial Sales" => "c907b3b8-2cf5-475b-898c-53ed1b0adaf6",
			"Visa Non Dial Sales" => "624250b2-55ae-4423-b2e9-95c837943115",
			"Visa Sales" => "e6d8f040-9963-4539-ab75-3e19f679de16",
			"Visa Settled Items" => "2e298d95-345b-49e9-963e-5a95fa663355"
		];
		//Another set of products that use the same calculations among them but different from the rest
		$this->discountProds = [
			"American Express Discount" => "566f5f47-bac0-41f8-ac68-23f534627ad4",
			"American Express Discount (Converted Merchants)" => "5801936c-4498-498d-b0bc-398534627ad4",
			"Debit Discount" => "12093cfa-fecd-4f17-894f-2f3430008bb0",
			"Discover Discount" => "566f5f47-59c4-456a-9e1b-23f534627ad4",
			"EBT Discount" => "5da88a37-c3fa-4ee6-a69d-91ff643b3b54",
			"MasterCard Discount" => "3d4f7842-1cb9-4bad-b9fd-415b1d0eee30",
			"Visa Discount" => "ec5b6684-6f5c-4881-b26c-5a8233bde091"
		];
		//Another set of products that use the same calculations among them but different from the rest
		$this->commonProducts2 = [
			"Corral License Fee" => "5806a480-cdd8-4199-8d6f-319b34627ad4",
			"Debit Sales" => "72a445f3-3937-4078-8631-1f569d6a30ed",
			"EBT Sales" => "f615203f-b266-4147-9d53-039480371607",
			"Payment Fusion" => "9db324ec-8365-4ae2-9b49-1e575113d5df"
		];
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ResidualReport);
		parent::tearDown();
	}

/**
 * testConstructorAndFilterArgs
 *
 * @covers ResidualReport::__construct
 * @covers ResidualReport::filterArgs property
 * @return void
 */
	public function testConstructorAndFilterArgs() {
		$ResidualReport = $this->_mockModel('ResidualReport');
		$expected = [
			'dba_mid' => [
				'type' => 'query',
				'method' => 'orConditions',
			],
			'from_date' => [
				'type' => 'query',
				'method' => 'dateStartConditions',
				'empty' => true
			],
			'end_date' => [
				'type' => 'query',
				'method' => 'dateEndConditions',
				'empty' => true
			],
			'user_id' => [
				'type' => 'subquery',
				'method' => 'searchByUserId',
				'field' => '"ResidualReport"."user_id"',
				'searchByMerchantEntity' => true
			],
			'partners' => [
				'type' => 'value',
				'field' => 'ResidualReport.partner_id'
			],
			'products' => [
				'type' => 'query',
				'method' => 'productConditions'
			],
			'dba_mid' => [
				'type' => 'query',
				'method' => 'orConditions',
			],
			'date_type' => [
				'type' => 'value',
				'field' => '"TimelineEntry"."timeline_item_id"'
			],
			'organization_id' => [
				'type' => 'value',
				'field' => '"Merchant"."organization_id"',
			],
			'region_id' => [
				'type' => 'value',
				'field' => '"Merchant"."region_id"',
			],
			'subregion_id' => [
				'type' => 'value',
				'field' => '"Merchant"."subregion_id"',
			],		
			'location_description' => [
				'type' => 'subquery',
				'method' => 'searchByLocation',
				'field' => '"Merchant"."id"'
			],
		];

		$this->assertEquals($expected, $ResidualReport->filterArgs);
		unset($ResidualReport);
	}

/**
 * integration test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @dataProvider providerDateStartConditions
 * @return void
 */
	public function testDateStartConditions($data, $expected) {
		$this->assertEquals($expected, $this->ResidualReport->dateStartConditions($data));
	}

/**
 * Data provider for testDateStartConditions
 *
 * @return array
 */
	public function providerDateStartConditions() {
		return [
			[
				['from_date' => ['year' => '2015', 'month' => '01']],
				"make_date(ResidualReport.r_year::integer, ResidualReport.r_month::integer, 1) >= '2015-01-01'",
			],
			[
				['from_date' => ['year' => '2022', 'month' => '06']],
				"make_date(ResidualReport.r_year::integer, ResidualReport.r_month::integer, 1) >= '2022-06-01'",
			],
		];
	}

/**
 * integration test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @dataProvider providerDateEndConditions
 * @return void
 */
	public function testDateEndConditions($data, $expected) {
		$this->assertEquals($expected, $this->ResidualReport->dateEndConditions($data));
	}

/**
 * Data provider for testDateEndConditions
 *
 * @return array
 */
	public function providerDateEndConditions() {
		return [
			[
				['end_date' => ['year' => '2015', 'month' => '01']],
				"make_date(ResidualReport.r_year::integer, ResidualReport.r_month::integer, 28) <= '2015-01-31'",
			],
			[
				['end_date' => ['year' => '2022', 'month' => '02']],
				"make_date(ResidualReport.r_year::integer, ResidualReport.r_month::integer, 28) <= '2022-02-28'",
			],
		];
	}

/**
 * testGetTotalResidualsNotValidUserRole
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Invalid user role
 * @return void
 */
	public function testGetTotalResidualsNotValidUserRole() {
		$this->ResidualReport->getTotalResiduals(User::ROLE_ADMIN_I);
	}

/**
 * testGetTotalResidualsNotValidUserRole
 *
 * @param string $userRole Role to get the residuals amount
 * @param array $filterParams Parametros de filtrado
 * @param decimal $expected Expected result
 * @dataProvider providerGetTotalResiduals
 * @return void
 */
	public function testGetTotalResiduals($userRole, $filterParams, $expected) {
		$ResidualReport = $this->getMockForModel('ResidualReport', ['_getCurrentUser']);
		$ResidualReport->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));

		$result = $ResidualReport->getTotalResiduals($userRole, $filterParams);
		$this->assertEquals($expected, $result);
		unset($ResidualReport);
	}

/**
 * Data provider for testDateRangeStartConditions
 *
 * @return array
 */
	public function providerGetTotalResiduals() {
		return [
			[User::ROLE_REP, [], -25.21],
			[User::ROLE_PARTNER, [], 1.32],
			[User::ROLE_PARTNER_REP, [], -25.21],
			[User::ROLE_SM, [], -0.6480],
			[User::ROLE_SM2, [], null],
			[
				User::ROLE_REP,
				[
					'user_id' => 'user_id:' . AxiaTestCase::USER_REP_ID,
				],
				-18.91
			],
			[
				User::ROLE_REP,
				[
					'user_id' => 'user_id:' . AxiaTestCase::USER_REP_ID,
					'from_date' => ['year' => '2015', 'month' => '01'],
					'end_date' => ['year' => '2015', 'month' => '12'],
				],
				-5.41
			],
			[
				User::ROLE_REP,
				[
					'user_id' => 'user_id:' . AxiaTestCase::USER_REP_ID,
					'from_date' => ['year' => '2015', 'month' => '01'],
					'end_date' => ['year' => '2015', 'month' => '12'],
					'merchant_dba' => 'Refer Aloha, LLC',
				],
				'-1.230000'
			],
		];
	}

/**
 * Regression test Ticket #17711
 *
 * @return void
 */
	public function testGetTotalResidualsRegressionTest17711() {
		$filterParams = [
			'from_date' => [
				'year' => '2014',
				'month' => '05'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '04'
			],
			'user_id' => 'user_id:' . AxiaTestCase::USER_REP_ID
		];
		$ResidualReport = $this->getMockForModel('ResidualReport', ['_getCurrentUser']);
		$ResidualReport->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_REP_ID));

		$result = $ResidualReport->getTotalResiduals(User::ROLE_REP, $filterParams);
		$this->assertEquals(-7.5, $result);
		unset($ResidualReport);
	}

/**
 * testGetDefaultSearchValues
 *
 * @covers ResidualReport::getDefaultSearchValues
 * @return void
 */
	public function testGetDefaultSearchValues() {
		$ResidualReport = $this->getMockForModel('ResidualReport', ['_getCurrentUser', '_getNow']);
		$ResidualReport->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$ResidualReport->User = $this->getMockForModel('User', ['_getCurrentUser']);
		$ResidualReport->User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$ResidualReport->expects($this->any())
			->method('_getNow')
			->will($this->returnValue(new DateTime('2016-06-01')));

		$expected = [
			'from_date' => [
				'year' => '2016',
				'month' => '06'
			],
			'end_date' => [
				'year' => '2016',
				'month' => '06'
			],
			'user_id' => 'user_id:' . AxiaTestCase::USER_ADMIN_ID
		];

		$result = $ResidualReport->getDefaultSearchValues();
		$this->assertEquals($expected, $result);
		unset($ResidualReport);
	}

/**
 * testGetReportData
 *
 * @covers ResidualReport::getReportData
 * @return void
 */
	public function testGetReportData() {
		$User = $this->getMockForModel('User', ['_getCurrentUser']);
		$User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		ClassRegistry::init('LastDepositReport')->deleteAll(['id is not null'], false);
		$arg = [
			'from_date' => [
				'year' => '2015',
				'month' => '07'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '07'
			],
			'user_id' => 'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1',
			'products' => '566f5f47-bac0-41f8-ac68-23f534627ad4'
		];

		$expected = [
			'residualReports' => [
				[
					'merchant_mid' => 3948906204000946,
					'merchant_dba' => '16 Hands',
					'bet_name' => '5612/7612tsys',
					'products_services_description' => 'American Express Discount',
					'r_avg_ticket' => 0,
					'r_items' => 29.0,
					'r_volume' => 0,
					'm_rate_pct' => '0.000%',
					'm_rate_and_bet_pct' => '0.000%',
					'm_per_item_fee' => '$0.480',
					'm_statement_fee' => 0,
					'RepFullname' => 'Mark Weatherford',
					'r_rate_pct' => '0.000%',
					'r_per_item_fee' => '$0.350',
					'r_statement_fee' => '$5.00',
					'rep_gross_profit' => 0,
					'r_profit_pct' => '75.000%',
					'rep_pct_of_gross' => '0.000%',
					'r_profit_amount' => '$0.262',
					'SalesManagerFullname' => 'Bill McAbee',
					'manager_rate' => '0.000%',
					'manager_per_item_fee' => 0,
					'manager_statement_fee' => 0,
					'manager_gross_profit' => 0,
					'manager_profit_pct' => '0.050%',
					'manager_pct_of_gross' => '0.000%',
					'manager_profit_amount' => '($0.062)',
					'SalesManager2Fullname' => 'Mark Weatherford',
					'manager2_rate' => '0.000%',
					'manager2_per_item_fee' => 0,
					'manager2_statement_fee' => 0,
					'manager2_gross_profit' => 0,
					'manager_profit_pct_secondary' => '0.000%',
					'manager2_pct_of_gross' => '0.000%',
					'manager_profit_amount_secondary' => 0,
					'PartnerFullname' => 'Mark Weatherford',
					'partner_rate' => '0.000%',
					'partner_per_item_fee' => 0,
					'partner_statement_fee' => 0,
					'partner_gross_profit' => 0,
					'partner_profit_pct' => '0.100%',
					'partner_pct_of_gross' => '0.000%',
					'partner_profit_amount' => '$0.120',
					'RefFullname' => 'Mark Weatherford',
					'referrer_rate' => '0.000%',
					'referrer_per_item_fee' => 0,
					'referrer_statement_fee' => 0,
					'referrer_gross_profit' => 0,
					'referer_pct_of_gross' => '0.000%',
					'refer_profit_pct' => '0.000%',
					'refer_profit_amount' => 0.0,
					'ResFullname' => 'Mark Weatherford',
					'reseller_rate' => '0.000%',
					'reseller_per_item_fee' => 0,
					'reseller_statement_fee' => 0,
					'reseller_gross_profit' => 0,
					'reseller_pct_of_gross' => '0.000%',
					'res_profit_pct' => '0.000%',
					'res_profit_amount' => null,
					'address_state' => null,
					'ent_name' => null,
					'organization' => null,
					'region' => null,
					'subregion' => null,
					'location' => null,
					'last_deposit_date' => null,
					'client_id_global' => null
				]
			],
			'totals' => [
				'r_items' => 29.0,
				'r_volume' => '0.000000',
				'total_profit' => '-1.230000',
				'r_profit_amount' => '0.262000',
				'refer_profit_amount' => '0.000000',
				'res_profit_amount' => '0.000000',
				'manager_profit_amount' => '-0.061500',
				'manager_profit_amount_secondary' => '0.000000',
				'partner_profit_amount' => '0.120000'
			],
			'totalsNonReferrals' => [
				'r_volume' => 0,
				'gross_profit' => -1.23
			],
			'totalsReferrals' => [
				'r_volume' => 0,
				'gross_profit' => 0
			],
			'users' => [
				'Entity 1' => [
						'entity_id:00000000-0000-0000-0000-000000000001' => 'Entity 1 '
				],
				'Bill McAbee' => [
						'parent_user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (All in this group)',
						'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee ',
						'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'Mark Weatherford (Rep)',
				],
				'Select all' => [
						'select_all:select_all' => 'All Entities/Users '
				],
				'No Entity' => [
						'entity_id:' => 'No Entity '
				],
				'No Manager' => [
						'parent_user_id:' => 'No Manager (All in this group)',
						'user_id:00000000-0000-0000-0000-000000000010' => 'Ben Franklin (SM)',
						'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (SM)',
						'user_id:d2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'Frank Williams (SM2)',
						'user_id:113114ae-7777-7777-7777-fa0c0f25c786' => 'Mr Installer (Installer)',
						'user_id:113114ae-9999-9999-9999-fa0c0f25c786' => 'No Profile Partner Rep (PartnerRep)',
						'user_id:32165df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Slim Pickins (PartnerRep)',
						'user_id:113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep (PartnerRep)',
				],
				 'Referrers/Resellers' => [
				 		'user_id:00000000-0000-0000-0000-000000000005' => 'Bill McAbee blocked (Referrer)'
				 ]
			],
			'partners' => [],
			'products' => [
				'Current Products' => [
						'e6d8f040-9963-4539-ab75-3e19f679de16' => 'Visa Sales'
				],
				'Legacy Products' => [
						'all_legacy' => 'All Legacy Products',
						'452d4a67-0a2a-4893-a854-47f14e58f53c' => 'Bankcard'
				]
			],
			'organizations' => [
				'8d08c7ed-c61a-4311-b088-706d6d8c052c' => 'Test Org 1'
			],
			'regions' => [
				'ef3b25d3-b4e7-4137-8663-42fc83f7fc71' => 'Org 1 - Region 1'
			],
			'subregions' => [
				'9b26cfbf-4511-4c5b-a10c-59d99b97bbd2' => 'Org 1 - Region 1 - Subregion 1'
			],
		];

		$residualReports = $this->ResidualReport->find('residualReport', [
			'conditions' => $this->ResidualReport->parseCriteria($arg),
		]);

		$results = $this->ResidualReport->getReportData($residualReports, false, false);
		unset($results['labels']);
		foreach ($results['residualReports'] as $idx => $data) {
			array_walk($data, function(&$value, $key) {
				if (is_numeric($value)) {
					$value = $value * 1;
				}
			});

			$results['residualReports'][$idx] = $data;
		}

		$this->assertEquals($expected, $results);
	}

/**
 * testGetGpaReportData
 *
 * @covers ResidualReport::getReportData
 * @return void
 */
	public function testGetGpaReportData() {
		$User = $this->getMockForModel('User', ['_getCurrentUser']);
		$User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->ResidualReport->updateAll(
			[	'rep_gross_profit' => '500',
				'partner_gross_profit' => '100',
				'r_avg_ticket' => 15
			],
			['r_year' => 2015, 'r_month' => 7, 'products_services_type_id' => '566f5f47-bac0-41f8-ac68-23f534627ad4', 'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1']
		);
		$arg = [
			'from_date' => [
				'year' => '2015',
				'month' => '07'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '07'
			],
			'res_months' => 1,
			'user_id' => 'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1',
			'products' => '566f5f47-bac0-41f8-ac68-23f534627ad4',
			'date_type' => TimelineItem::INSTALL_COMMISSIONED
		];
		$projectedData = [
			'commissionMultiples' => [
				[
					'mid' => '3948906204000946',
					'product' => 'American Express Discount',
					'rep_pct_of_gross' => 50,
					'rep_product_profit_pct' => 50,
					'rep_gross_profit' => 100,
				]
			],
			'commissionMultipleTotals' => [
				'multiple_amount' => 100,
				'rep_residual_gp' => 10
			],
		];
		$expected = [
			'actualResiduals' => [
				[
					'active_merchant' => 1,
					'merchant_mid' => '3948906204000946',
					'merchant_dba' => '16 Hands',
					'RepFullname' => 'Mark Weatherford',
					'products_services_description' => 'American Express Discount',
					'r_avg_ticket' => '$15.00',
					'r_items' => '29.0000',
					'avg_volume' => '0',
					'rep_avg_gross_profit' => '$500.00',
					'rep_avg_residual_gp' => '$499.88',
					'gp_amount_diff' => '$499.88',
					'gp_pct_diff' => '100.00%',
					'organization' => null,
					'region' => null,
					'subregion' => null,
					'location' => null,
					'avg_partner_profit_amount' => '$0.12',
					'PartnerFullname' => 'Mark Weatherford',
					'client_id_global' => null
				]
			],
		];

		$gpaResiduals = $this->ResidualReport->find('gpaResiduals', [
			'conditions' => $this->ResidualReport->parseCriteria($arg),
		]);

		$results = $this->ResidualReport->getGpaReportData($gpaResiduals, $projectedData, $arg['res_months'], CommissionPricing::REPORT_GP_ANALYSIS);

		$this->assertEquals($expected['actualResiduals'][0]['rep_avg_gross_profit'], $results['actualResiduals'][0]['rep_avg_gross_profit']);
		$this->assertEquals($expected['actualResiduals'][0], $results['actualResiduals'][0]);
		$results = $this->ResidualReport->getGpaReportData($gpaResiduals, $projectedData, $arg['res_months'], CommissionPricing::REPORT_CM_ANALYSIS);

		$expected['actualResiduals'][0]['avg_volume'] = '0';
		$expected['actualResiduals'][0]['rep_avg_gross_profit'] = '$500.00';
		$expected['actualResiduals'][0]['rep_avg_residual_gp'] = '$499.88';
		$expected['actualResiduals'][0]['rep_total_residual_gp'] = '$499.88';
		$expected['actualResiduals'][0]['rep_projected_gp'] = '$100.00';
		$expected['actualResiduals'][0]['actual_multiple'] = '0';
		$expected['actualResiduals'][0]['commissionable'] = false;
		$this->assertEquals($expected['actualResiduals'][0]['rep_avg_gross_profit'], $results['actualResiduals'][0]['rep_avg_gross_profit']);
		$this->assertEquals($expected['actualResiduals'][0]['rep_total_residual_gp'], $results['actualResiduals'][0]['rep_total_residual_gp']);
		$this->assertEquals($expected['actualResiduals'][0]['rep_avg_residual_gp'], $results['actualResiduals'][0]['rep_avg_residual_gp']);
		$this->assertEquals($expected['actualResiduals'][0]['actual_multiple'], $results['actualResiduals'][0]['actual_multiple']);
	}

/**
 * testFindResidualReport
 *
 * @covers ResidualReport::_findResidualReport
 * @return void
 */
	public function testFindResidualReport() {
		ClassRegistry::init('LastDepositReport')->deleteAll(['id is not null'], false);
		$residualReports = $this->ResidualReport->find('residualReport', [
			'order' => ['Merchant.merchant_mid ASC']
		]);
		$this->assertCount(6, $residualReports);

		$firstResidualReport = Hash::get($residualReports, '0');

		$expectedContains = [
			'Merchant',
			'Client',
			'ResidualReport',
			'Bet',
			'ProductsServicesType',
			'ProductCategory',
			'Rep',
			'PartnerRep',
			'SalesManager',
			'SalesManager2',
			'Ref',
			'Res',
			'Address',
			'Entity',
			'Organization',
			'Region',
			'Subregion',
			'LastDepositReport',
			'Partner'
		];

		$this->assertEquals($expectedContains, array_keys($firstResidualReport));

		$expectedReps = [
			'Bill McAbee',
			'Mark Weatherford',
			'Mark Weatherford',
			'Mark Weatherford',
			'Mark Weatherford',
			'Mark Weatherford',
		];
		$actualReps = Hash::extract($residualReports, '{n}.Rep.RepFullname');
		sort($actualReps, SORT_STRING);
		$this->assertEquals($expectedReps, $actualReps);

		$expectedFirstResidualReportPricing = [
			'partner_exclude_volume' => false,
			'total_profit' => '-1.2300',
			'r_avg_ticket' => '0.00000000000000000000',
			'r_items' => '29.0000',
			'r_volume' => '0.0000',
			'm_rate_pct' => '0.00000000000000000000',
			'm_per_item_fee' => '0.48000000000000000000',
			'm_statement_fee' => '0.00000000000000000000',
			'r_rate_pct' => '0.00000000000000000000',
			'r_per_item_fee' => '0.35000000000000000000',
			'r_statement_fee' => '5.0000000000000000',
			'rep_gross_profit' => '0.0000',
			'r_profit_pct' => '75.0000000000000000',
			'rep_pct_of_gross' => '0.00000000000000000000',
			'r_profit_amount' => '0.2620',
			'partner_rep_rate' => '0.00000000000000000000',
			'partner_rep_per_item_fee' => '0.00000000000000000000',
			'partner_rep_statement_fee' => '0.00000000000000000000',
			'partner_rep_gross_profit' => '0.0000',
			'partner_rep_profit_pct' => '1.00000000000000000000',
			'partner_rep_pct_of_gross' => '0.00000000000000000000',
			'manager_rate' => '0.00000000000000000000',
			'manager_per_item_fee' => '0.00000000000000000000',
			'manager_statement_fee' => '0.00000000000000000000',
			'manager_gross_profit' => '0.0000',
			'manager_profit_pct' => '0.05000000000000000000',
			'manager_pct_of_gross' => '0.00000000000000000000',
			'manager_profit_amount' => '-0.0615',
			'manager2_rate' => '0.00000000000000000000',
			'manager2_per_item_fee' => '0.00000000000000000000',
			'manager2_statement_fee' => '0.00000000000000000000',
			'manager2_gross_profit' => '0.0000',
			'manager_profit_pct_secondary' => null,
			'manager2_pct_of_gross' => '0.00000000000000000000',
			'manager_profit_amount_secondary' => null,
			'partner_rate' => '0.00000000000000000000',
			'partner_per_item_fee' => '0.00000000000000000000',
			'partner_statement_fee' => '0.00000000000000000000',
			'partner_gross_profit' => '0.0000',
			'partner_profit_pct' => '0.10000000000000000000',
			'partner_pct_of_gross' => '0.00000000000000000000',
			'partner_profit_amount' => '0.1200',
			'referrer_rate' => '0.00000000000000000000',
			'referrer_per_item_fee' => '0.00000000000000000000',
			'referrer_statement_fee' => '0.00000000000000000000',
			'referrer_gross_profit' => '0.0000',
			'referer_pct_of_gross' => null,
			'refer_profit_pct' => null,
			'refer_profit_amount' => '0.0000',
			'reseller_rate' => '0.00000000000000000000',
			'reseller_per_item_fee' => '0.00000000000000000000',
			'reseller_statement_fee' => '0.00000000000000000000',
			'reseller_gross_profit' => '0.0000',
			'reseller_pct_of_gross' => '0.00000000000000000000',
			'res_profit_pct' => null,
			'res_profit_amount' => null,
			'processing_rate' => null,
		];

		$this->assertEquals($expectedFirstResidualReportPricing, Hash::get($firstResidualReport, 'ResidualReport'));
	}

/**
 * testProcessResidualReportData
 *
 * @covers ResidualReport::_processResidualReportData
 * @return void
 */
	public function testProcessResidualReportData() {
		$expectedFirstReport = [
			'merchant_mid' => '3948906204000946',
			'merchant_dba' => '16 Hands',
			'bet_name' => '5612/7612tsys',
			'products_services_description' => 'American Express Discount',
			'r_avg_ticket' => '0',
			'r_items' => '29.0000',
			'r_volume' => '0',
			'm_rate_pct' => '0.000%',
			'm_rate_and_bet_pct' => '0.000%',
			'm_per_item_fee' => '$0.480',
			'm_statement_fee' => '0',
			'RepFullname' => 'Mark Weatherford',
			'r_rate_pct' => '0.000%',
			'r_per_item_fee' => '$0.350',
			'r_statement_fee' => '$5.00',
			'rep_gross_profit' => '0',
			'r_profit_pct' => '75.000%',
			'rep_pct_of_gross' => '0.000%',
			'r_profit_amount' => '$0.262',
			'SalesManagerFullname' => 'Bill McAbee',
			'manager_rate' => '0.000%',
			'manager_per_item_fee' => '0',
			'manager_statement_fee' => '0',
			'manager_gross_profit' => '0',
			'manager_profit_pct' => '0.050%',
			'manager_pct_of_gross' => '0.000%',
			'manager_profit_amount' => '($0.062)',
			'SalesManager2Fullname' => 'Mark Weatherford',
			'manager2_rate' => '0.000%',
			'manager2_per_item_fee' => '0',
			'manager2_statement_fee' => '0',
			'manager2_gross_profit' => '0',
			'manager_profit_pct_secondary' => '0.000%',
			'manager2_pct_of_gross' => '0.000%',
			'manager_profit_amount_secondary' => '0',
			'PartnerFullname' => 'Mark Weatherford',
			'partner_rate' => '0.000%',
			'partner_per_item_fee' => '0',
			'partner_statement_fee' => '0',
			'partner_gross_profit' => '0',
			'partner_profit_pct' => '0.100%',
			'partner_pct_of_gross' => '0.000%',
			'partner_profit_amount' => '$0.120',
			'RefFullname' => 'Mark Weatherford',
			'referrer_rate' => '0.000%',
			'referrer_per_item_fee' => '0',
			'referrer_statement_fee' => '0',
			'referrer_gross_profit' => '0',
			'referer_pct_of_gross' => '0.000%',
			'refer_profit_pct' => '0.000%',
			'refer_profit_amount' => '0.0000',
			'ResFullname' => 'Mark Weatherford',
			'reseller_rate' => '0.000%',
			'reseller_per_item_fee' => '0',
			'reseller_statement_fee' => '0',
			'reseller_gross_profit' => '0',
			'reseller_pct_of_gross' => '0.000%',
			'res_profit_pct' => '0.000%',
			'res_profit_amount' => null,
			'address_state' => null,
			'ent_name' => null,
			'organization' => null,
			'region' => null,
			'subregion' => null,
			'location' => null,
			'last_deposit_date' => null,
			'client_id_global' => null,
		];
		$expectedTotals = [
			'r_items' => 29.0,
			'r_volume' => '0.000000',
			'total_profit' => '-1.230000',
			'r_profit_amount' => '0.262000',
			'refer_profit_amount' => '0.000000',
			'res_profit_amount' => '0.000000',
			'manager_profit_amount' => '-0.061500',
			'manager_profit_amount_secondary' => '0.000000',
			'partner_profit_amount' => '0.120000'

		];
		$expectedTotalsNonReferrals = [
			'r_volume' => 0,
			'gross_profit' => -1.23

		];
		$expectedTotalsReferrals = [
			'r_volume' => 0,
			'gross_profit' => 0

		];
		ClassRegistry::init('LastDepositReport')->deleteAll(['id is not null'], false);
		$residualReports = $this->ResidualReport->find('residualReport', ['conditions' => ['Merchant.id' => '00000000-0000-0000-0000-000000000004']]);
		$reflection = $this->__buildResidualReportReflection('_processResidualReportData');
		$result = $reflection->invokeArgs($this->ResidualReport, [$residualReports, false]);

		$this->assertEquals($expectedFirstReport, Hash::get($result, 'residualReports.0'));
		$this->assertEquals($expectedTotals, Hash::get($result, 'totals'));
		$this->assertEquals($expectedTotalsNonReferrals, Hash::get($result, 'totalsNonReferrals'));
		$this->assertEquals($expectedTotalsReferrals, Hash::get($result, 'totalsReferrals'));

		//Test that when a non admin user is viewing the report no data is returned when the Do Not Display option is set for
		//the rep in the report data
		$userId = AxiaTestCase::USER_REP_ID;
		$this->ResidualReport = $this->_mockModel('ResidualReport', ['_getCurretestGetReportDatantUser'], [
				'_getCurrentUser' => ['value' => $userId],
			]);
		$SearchByUserIdBehavior = $this->getMock('SearchByUserIdBehavior', ['_getLoggedInUser']);
		$this->ResidualReport->Behaviors->attach('SearchByUserId');
		$SearchByUserIdBehavior
			->expects($this->any())
			->method('_getLoggedInUser')
			->will($this->returnValue($userId));
		$this->UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');

		$tstData = [
			'UserCompensationProfile' => [
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1', //As shown in above assertion Mark Weatherford has Residual report data
				'partner_user_id' => null
			],
			'UserParameter' => [
				[
					'user_parameter_type_id' => 'd78ed6bf-739f-43b9-a262-dd343d3bcb5b',
					'products_services_type_id' => '566f5f47-bac0-41f8-ac68-23f534627ad4', //Amex Discount
					'user_parameter_type_id' => UserParameterType::DONT_DISPLAY,
					'value' => 1 //do not display this product in residual report
				],
			]
		];
		$existsingUcpId = $this->UserCompensationProfile->field('id', ['user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1', 'is_default' => true]);
		if (!empty($existsingUcpId)) {
			$tstData['UserCompensationProfile']['id'] = $existsingUcpId;
		}

		$this->UserCompensationProfile->saveAssociated($tstData);
		$arg = [
			'from_date' => [
				'year' => '2015',
				'month' => '07'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '07'
			],
			'user_id' => 'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1',
			'products' => '566f5f47-bac0-41f8-ac68-23f534627ad4'
		];
		//Try to get the same data again
		$residualReports = $this->ResidualReport->find('residualReport', [
			'conditions' => $this->ResidualReport->parseCriteria($arg),
		]);

		$reflection = $this->__buildResidualReportReflection('_processResidualReportData');
		$result = $reflection->invokeArgs($this->ResidualReport, [$residualReports, false]);
		//No data is to be returned
		$this->assertEmpty(Hash::get($result, 'residualReports'));
	}

/**
 * testProcessResidualReportDataWithPartner
 *
 * @covers ResidualReport::_processResidualReportData
 * @return void
 */
	public function testProcessResidualReportDataWithPartner() {
		ClassRegistry::init('LastDepositReport')->deleteAll(['id is not null'], false);
		$residualReports = $residualReports = $this->ResidualReport->find('residualReport', ['conditions' => ['Merchant.id' => '00000000-0000-0000-0000-000000000004']]);
		$reflection = $this->__buildResidualReportReflection('_processResidualReportData');
		$residualReports[0]['ResidualReport']['partner_exclude_volume'] = true;
		$result = $reflection->invokeArgs($this->ResidualReport, [$residualReports, false]);

		$expectedFirstReport = [
			'merchant_mid' => '3948906204000946',
			'merchant_dba' => '16 Hands',
			'bet_name' => '5612/7612tsys',
			'products_services_description' => 'American Express Discount',
			'r_avg_ticket' => '0',
			'r_items' => '29.0000',
			'r_volume' => '0',
			'm_rate_pct' => '0.000%',
			'm_rate_and_bet_pct' => '0.000%',
			'm_per_item_fee' => '$0.480',
			'm_statement_fee' => '0',
			'RepFullname' => 'Mark Weatherford',
			'r_rate_pct' => '0.000%',
			'r_per_item_fee' => '$0.350',
			'r_statement_fee' => '$5.00',
			'rep_gross_profit' => '0',
			'r_profit_pct' => '75.000%',
			'rep_pct_of_gross' => '0.000%',
			'r_profit_amount' => '$0.262',
			'SalesManagerFullname' => 'Bill McAbee',
			'manager_rate' => '0.000%',
			'manager_per_item_fee' => '0',
			'manager_statement_fee' => '0',
			'manager_gross_profit' => '0',
			'manager_profit_pct' => '0.050%',
			'manager_pct_of_gross' => '0.000%',
			'manager_profit_amount' => '($0.062)',
			'SalesManager2Fullname' => 'Mark Weatherford',
			'manager2_rate' => '0.000%',
			'manager2_per_item_fee' => '0',
			'manager2_statement_fee' => '0',
			'manager2_gross_profit' => '0',
			'manager_profit_pct_secondary' => '0.000%',
			'manager2_pct_of_gross' => '0.000%',
			'manager_profit_amount_secondary' => '0',
			'PartnerFullname' => 'Mark Weatherford',
			'partner_rate' => '0.000%',
			'partner_per_item_fee' => '0',
			'partner_statement_fee' => '0',
			'partner_gross_profit' => '0',
			'partner_profit_pct' => '0.100%',
			'partner_pct_of_gross' => '0.000%',
			'partner_profit_amount' => '$0.120',
			'RefFullname' => 'Mark Weatherford',
			'referrer_rate' => '0.000%',
			'referrer_per_item_fee' => '0',
			'referrer_statement_fee' => '0',
			'referrer_gross_profit' => '0',
			'referer_pct_of_gross' => '0.000%',
			'refer_profit_pct' => '0.000%',
			'refer_profit_amount' => '0.0000',
			'ResFullname' => 'Mark Weatherford',
			'reseller_rate' => '0.000%',
			'reseller_per_item_fee' => '0',
			'reseller_statement_fee' => '0',
			'reseller_gross_profit' => '0',
			'reseller_pct_of_gross' => '0.000%',
			'res_profit_pct' => '0.000%',
			'res_profit_amount' => null,
			'address_state' => null,
			'ent_name' => null,
			'organization' => null,
			'region' => null,
			'subregion' => null,
			'location' => null,
			'last_deposit_date' => null,
			'client_id_global' => null,
			
		];
		$expectedTotals = [
			'r_items' => 29.0,
			'r_volume' => '0.000000',
			'total_profit' => '-1.230000',
			'r_profit_amount' => '0.262000',
			'refer_profit_amount' => '0.000000',
			'res_profit_amount' => '0.000000',
			'manager_profit_amount' => '-0.061500',
			'manager_profit_amount_secondary' => '0.000000',
			'partner_profit_amount' => '0.120000'
		];
		$expectedTotalsNonReferrals = [
			'r_volume' => 0,
			'gross_profit' => 0
		];
		$expectedTotalsReferrals = [
			'r_volume' => 0,
			'gross_profit' => -1.23
		];

		$this->assertEquals($expectedFirstReport, Hash::get($result, 'residualReports.0'));
		$this->assertEquals($expectedTotals, Hash::get($result, 'totals'));
		$this->assertEquals($expectedTotalsNonReferrals, Hash::get($result, 'totalsNonReferrals'));
		$this->assertEquals($expectedTotalsReferrals, Hash::get($result, 'totalsReferrals'));
	}

/**
 * _percentError()
 * Calculatesd the error percantage between an aproximate value and the exact value
 * 
 * @param float $aproxVal the value to evaluate which approximates the exact value
 * @param float $exactVal the exact amount expected
 * @return float The percent error of how much the approximate value was off from the exact value
 */
	protected function _percentError($aproxVal, $exactVal) {
		if ($exactVal == 0) {
			return 0;
		}
		//Minute differences are not important
		if (abs($exactVal - $aproxVal) === .01 || abs($exactVal - $aproxVal) === .001 || abs($exactVal - $aproxVal) === .0001) {
			return 0;
		}
		return (abs($exactVal - $aproxVal) / abs($exactVal)) * 100;
	}

/**
 * _isAcceptableErrorPercent()
 * Detrmines whether the error percantage between an aproximate value and the exact value are
 * within the acceptance percent margin of error provided.
 * 
 * @param float $aproxVal the value to evaluate which approximates the exact value
 * @param float $exactVal the exact amount expected
 * @param float $acceptancePercent a percent value for acceptable error in percentage form
 * @return boolean false when the error percent excedes the acceptable margin of error
 */
	protected function _isAcceptableErrorPercent($aproxVal, $exactVal, $acceptancePercent) {
		$percentError = $this->_percentError($aproxVal, $exactVal);
		return $percentError <= $acceptancePercent;
	}
/**
 * _saveAsApproved
 * Saves merchant as approved in merchant_uws
 *
 * @param string $merchantId a merchant id
 * @return void
 */
	protected function _saveAsApproved($merchantId) {
		$UwStatusMerchantXref = ClassRegistry::init('UwStatusMerchantXref');
		$id = $UwStatusMerchantXref->field('id', ['merchant_id' => $merchantId]);
		$data = [
			'merchant_id' => $merchantId,
			'uw_status_id' => '00000000-0000-0000-0000-000000000002',
			'datetime' => '2010-01-01 00:00:00'
		];
		if (!empty($id)) {
			$data['id'] = $id;
		}
		$UwStatusMerchantXref->clear();
		$UwStatusMerchantXref->save($data);
	}

/**
 * testApprovalCheckAdd
 * Tests the scenario where a merchant has archived data for its products but merchant has not been approved
 * Then tests the same scenario with the merchant approved
 * 
 * @covers ResidualReport::_findIndex
 * @return void
 */
	public function testApprovalCheckAdd() {
		$year = '2016';
		$month = '01';
		$product = [
		'ProductsServicesType' => [
				'id' => 'e6d8f040-9963-4539-ab75-3e19f679de16',
				'products_services_description' => 'Visa Sales'
			]
		];
		$email = 'test@example.com';

		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';

		copy($file, $datafile);
		$this->__saveTestArchivedData('archiveRepOnly', 'e6d8f040-9963-4539-ab75-3e19f679de16');
		$result = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
		$this->assertFalse($result['recordsAdded']);

		//now approve the merchant
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		copy($file, $datafile);
		$result = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
		$this->assertTrue($result['recordsAdded']);
	}

/**
 * testAddWithPartner
 *
 * @return void
 */
	public function testAddWithPartner() {
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$product = [
		'ProductsServicesType' => [
				'id' => 'e6d8f040-9963-4539-ab75-3e19f679de16',
				'products_services_description' => 'Visa Sales'
			]
		];
		$email = 'test@example.com';

		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';

		copy($file, $datafile);

		$expected = [
			'job_name' => 'Residual Report Admin',
			'result' => true,
			'recordsAdded' => true,
			'start_time' => '',
			'end_time' => '',
			'log' => [
				'products' => ['Visa Sales'],
				'errors' => [],
				'optional_msgs' => ['Selected month/year: 01/2016'],
			]
		];

		$Merchant->save(
			[
				'Merchant' => [
					'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'partner_id' => '19b2c1da-943e-4761-829a-4ca54008dd58',
				],
			], ['validate' => false]
		);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		copy($file, $datafile);

		//save dataset that emoulates an archive for a merchant with partner
		$this->__saveTestArchivedData('archiveRepPartner', $product['ProductsServicesType']['id']);
		$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
		$expected['start_time'] = $response['start_time'];
		$expected['end_time'] = $response['end_time'];
		$this->assertEquals($expected, $response);

		// check expected residual report values
		$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month]]);

		$response = $response[0]['ResidualReport'];

		$this->assertEquals(1, $response['r_month']);
		$this->assertEquals(2016, $response['r_year']);
		$this->assertEquals(120, $response['r_avg_ticket']);
		$this->assertEquals(250, $response['r_items']);
		$this->assertEquals(30000, $response['r_volume']);

		$this->assertEquals(0.21, $response['m_rate_pct']);
		$this->assertEquals(.15, $response['m_per_item_fee']);
		$this->assertEquals(25, $response['m_statement_fee']);

		$this->assertEquals(2.99, $response['partner_rate']);
		$this->assertEquals(.6, $response['partner_per_item_fee']);
		$this->assertEquals(16, $response['partner_statement_fee']);
		//gross_profit calculation applies to the Sales products
		$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['partner_per_item_fee']));
		$this->assertEquals($expected, $response['partner_gross_profit']);
		$this->assertEquals('0', $response['partner_profit_pct']);
		$this->assertEquals('0', $response['partner_profit_amount']);
		$this->assertEquals('0', $response['partner_pct_of_gross']);

		$this->assertEquals('0', $response['referrer_rate']);
		$this->assertEquals('0', $response['referrer_per_item_fee']);
		$this->assertEquals('0', $response['referrer_statement_fee']);
		$this->assertEquals('0', $response['referrer_gross_profit']);
		$this->assertEquals(0, $response['refer_profit_pct']);
		$this->assertEquals(0, $response['refer_profit_amount']);
		$this->assertEquals('0', $response['referer_pct_of_gross']);

		$this->assertEquals('0', $response['reseller_rate']);
		$this->assertEquals('0', $response['reseller_per_item_fee']);
		$this->assertEquals('0', $response['reseller_statement_fee']);
		$this->assertEquals('0', $response['reseller_gross_profit']);
		$this->assertEquals(0, $response['res_profit_pct']);
		$this->assertEquals(0, $response['res_profit_amount']);
		$this->assertEquals('0', $response['reseller_pct_of_gross']);

		$this->assertEquals(0, $response['r_rate_pct']);
		$this->assertEquals(0, $response['r_per_item_fee']);
		$this->assertEquals(0, $response['r_statement_fee']);
		$this->assertEquals(0, $response['rep_gross_profit']);
		$this->assertEquals(0, $response['r_profit_pct']);
		$this->assertEquals(0, $response['r_profit_amount']);
		$this->assertEquals('0', $response['rep_pct_of_gross']);

		$this->assertEquals(1.99, $response['partner_rep_rate']);
		$this->assertEquals(.5, $response['partner_rep_per_item_fee']);
		$this->assertEquals(15, $response['partner_rep_statement_fee']);
		//rep_gross_profit calculation applies to the Sales products
		$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['partner_rep_per_item_fee']));
		$this->assertEquals($expected, $response['partner_rep_gross_profit']);
		$this->assertEquals(0, $response['partner_rep_profit_pct']);
		$this->assertEquals(0, $response['partner_rep_profit_amount']);
		$this->assertEquals(12, $response['partner_rep_pct_of_gross']);

		$this->assertEquals('0', $response['manager_rate']);
		$this->assertEquals('0', $response['manager_per_item_fee']);
		$this->assertEquals('0', $response['manager_statement_fee']);
		$this->assertEquals('0', $response['manager_gross_profit']);
		$this->assertEquals(0, $response['manager_profit_pct']);
		$this->assertEquals(0, $response['manager_profit_amount']);
		$this->assertEquals(0, $response['manager_pct_of_gross']);

		$this->assertEquals('0', $response['manager2_rate']);
		$this->assertEquals('0', $response['manager2_per_item_fee']);
		$this->assertEquals('0', $response['manager2_statement_fee']);
		$this->assertEquals('0', $response['manager2_gross_profit']);
		$this->assertEquals(0, $response['manager_profit_pct_secondary']);
		$this->assertEquals(0, $response['manager_profit_amount_secondary']);
		$this->assertEquals('0', $response['manager2_pct_of_gross']);

		$expected = $response['r_profit_amount'] + $response['partner_rep_profit_amount'];
		//cannot always check if they are equal due to small unpredictable rounding error
		//Instead check that they do not differ by more than this
		if (abs($response['total_profit']) - abs($expected) != 0) {
			$this->assertLessThan(.01, abs($response['total_profit'] - $expected));
		} else {
			$this->assertEquals($expected, $response['total_profit']);
		}

		$this->assertEquals(null, $response['merchant_state']);
	}

/**
 * testAddCommonProducts
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 * The subset of products being tested here all use the same formulas and logic.
 *
 * @return void
 */
	public function testAddCommonProducts() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		foreach ($this->commonProducts as $key => $pId) {
			$product = [
			'ProductsServicesType' => [
					'id' => $pId,
					'products_services_description' => $key
				]
			];
			//Add product to merchant if doesn't have it
			if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
				$ProductsAndService->save([
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'products_services_type_id' => $pId,
				]);
			}
			//create a pseudo-random profit percent amount
			$randPct = rand(0, 100);

			//Add user's residual parameter value
			$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);

			//Add user's user parameter value
			$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

			copy($file, $datafile);

			$expected = [
				'job_name' => 'Residual Report Admin',
				'result' => true,
				'recordsAdded' => true,
				'start_time' => '',
				'end_time' => '',
				'log' => [
					'products' => [$key],
					'errors' => [],
					'optional_msgs' => ['Selected month/year: 01/2016'],
				]
			];
			$Merchant->save(
				[
					'Merchant' => [
						'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
						'partner_id' => null,
						'sm_user_id' => null,
						'sm2_user_id' => null,
						'referer_id' => null,
						'reseller_id' => null,
					]
				], ['validate' => false]
			);

			$this->__saveTestArchivedData('archiveRepOnly', $pId);
			$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
			$expected['start_time'] = $response['start_time'];
			$expected['end_time'] = $response['end_time'];
			$this->assertEquals($expected, $response);

			// check expected residual report values
			$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

			//One record per add() method execution for month/year/product

			$response = $response[0]['ResidualReport'];

			$this->assertEquals(1, $response['r_month']);
			$this->assertEquals(2016, $response['r_year']);
			$this->assertEquals(120, $response['r_avg_ticket']);
			$this->assertEquals(250, $response['r_items']);
			$this->assertEquals(30000, $response['r_volume']);

			$this->assertEquals(0.21, $response['m_rate_pct']);
			$this->assertEquals(.15, $response['m_per_item_fee']);
			$this->assertEquals(25, $response['m_statement_fee']);

			$this->assertEquals('0', $response['partner_rate']);
			$this->assertEquals('0', $response['partner_per_item_fee']);
			$this->assertEquals('0', $response['partner_statement_fee']);
			$this->assertEquals('0', $response['partner_gross_profit']);
			$this->assertEquals('0', $response['partner_profit_pct']);
			$this->assertEquals('0', $response['partner_profit_amount']);
			$this->assertEquals('0', $response['partner_pct_of_gross']);

			$this->assertEquals('0', $response['referrer_rate']);
			$this->assertEquals('0', $response['referrer_per_item_fee']);
			$this->assertEquals('0', $response['referrer_statement_fee']);
			$this->assertEquals('0', $response['referrer_gross_profit']);
			$this->assertEquals(0, $response['refer_profit_pct']);
			$this->assertEquals(0, $response['refer_profit_amount']);
			$this->assertEquals('0', $response['referer_pct_of_gross']);

			$this->assertEquals('0', $response['reseller_rate']);
			$this->assertEquals('0', $response['reseller_per_item_fee']);
			$this->assertEquals('0', $response['reseller_statement_fee']);
			$this->assertEquals('0', $response['reseller_gross_profit']);
			$this->assertEquals(0, $response['res_profit_pct']);
			$this->assertEquals(0, $response['res_profit_amount']);
			$this->assertEquals('0', $response['reseller_pct_of_gross']);

			$this->assertEquals(1.49, $response['r_rate_pct']);
			$this->assertEquals('0.1800', $response['r_per_item_fee']);
			$this->assertEquals('4.7500', $response['r_statement_fee']);

			//rep_gross_profit calculation applies to all products in this test
			$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['r_per_item_fee']));
			$this->assertEquals($expected, $response['rep_gross_profit']);
			$this->assertEquals($randPct, $response['r_profit_pct']);
			$this->assertEquals($randPct, $response['rep_pct_of_gross']);

			//Calculation for r_profit_amount applies to all products in this test
			$expected = ($response['rep_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['rep_pct_of_gross'] / 100 * ($response['r_profit_pct'] / 100);
			$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .9));

			$this->assertEquals('0', $response['partner_rep_rate']);
			$this->assertEquals('0', $response['partner_rep_per_item_fee']);
			$this->assertEquals('0', $response['partner_rep_statement_fee']);
			$this->assertEquals('0', $response['partner_rep_gross_profit']);
			$this->assertEquals('0', $response['partner_rep_profit_pct']);
			$this->assertEquals('0', $response['partner_rep_profit_amount']);
			$this->assertEquals('0', $response['partner_rep_pct_of_gross']);

			$this->assertEquals('0', $response['manager_rate']);
			$this->assertEquals('0', $response['manager_per_item_fee']);
			$this->assertEquals('0', $response['manager_statement_fee']);
			$this->assertEquals('0', $response['manager_gross_profit']);
			$this->assertEquals(0, $response['manager_profit_pct']);
			$this->assertEquals(0, $response['manager_profit_amount']);
			$this->assertEquals(0, $response['manager_pct_of_gross']);

			$this->assertEquals('0', $response['manager2_rate']);
			$this->assertEquals('0', $response['manager2_per_item_fee']);
			$this->assertEquals('0', $response['manager2_statement_fee']);
			$this->assertEquals('0', $response['manager2_gross_profit']);
			$this->assertEquals(0, $response['manager_profit_pct_secondary']);
			$this->assertEquals(0, $response['manager_profit_amount_secondary']);
			$this->assertEquals('0', $response['manager2_pct_of_gross']);

			$expected = $response['r_profit_amount'] + $response['partner_rep_profit_amount'];
			//cannot always check if they are equal due to small unpredictable rounding error
			//Instead check that they do not differ by more than this
			if (abs($response['total_profit']) - abs($expected) != 0) {
				$this->assertLessThan(.01, abs($response['total_profit'] - $expected));
			} else {
				$this->assertEquals($expected, $response['total_profit']);
			}

			$this->assertEquals(null, $response['merchant_state']);
		}
	}
/**
 * testAddDiscountProducts
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 * The subset of products being tested here all use the same formulas and logic.
 *
 * @return void
 */
	public function testAddDiscountProducts() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$partnerRepUcpId = '8bbe2d12-2975-4466-a309-fcf4e721d468';
		$partnerUcpId = 'a8c5607c-cf2e-4b5b-a2f9-fda5a95a3950';
		$partnerUserId = '19b2c1da-943e-4761-829a-4ca54008dd58';
		$testScenarios = [
			'rep' => [
				'arhive_name' => 'archiveRepOnly',
				'assoc_users' => []
			],
			'rep_and_mgrs' => [
				'arhive_name' => 'archiveWithManagers',
				'assoc_users' => [
					'sm_user_id' => self::SM_ID,
					'sm2_user_id' => self::SM2_ID
				]
			],
			'rep_ref_res' => [
				'arhive_name' => 'archiveWithRefRes',
				'assoc_users' => [
					'referer_id' => self::REF_ID,
					'reseller_id' => self::RES_ID
				],
				'modifiers' => [
					'ref_p_pct' => 60,
					'res_p_pct' => 70,
					'res_p_value' => 20,
					'ref_p_value' => 30,
					'res_p_type' => 'percentage',
					'ref_p_type' => 'percentage',
				]
			],
			'rep_partner' => [
				'arhive_name' => 'archiveRepPartner',
				'assoc_users' => [
					//partner id
					'partner_id' => $partnerUserId
				]
			]
		];

		//create comp profiles for all users except partner which already has one in test_db
		$this->_createUCP(self::SM_ID);
		$this->_createUCP(self::SM2_ID);
		$refUcpId = $this->_createUCP(self::REF_ID);
		$resUcpId = $this->_createUCP(self::RES_ID);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		foreach ($testScenarios as $tstCaseData) {
			foreach ($this->discountProds as $prodNameKey => $pId) {
				$product = [
					'ProductsServicesType' => [
						'id' => $pId,
						'products_services_description' => $prodNameKey
					]
				];
				//Add product to merchant if doesn't have it
				if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
					$ProductsAndService->save([
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'products_services_type_id' => $pId,
					]);
				}
				//create a pseudo-random profit percent amount
				$randPct = rand(0, 100);

				//Add user's residual parameter value
				$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);
				//Add user's user parameter value
				$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

				//Create Residual Parameters for PartnerRep, none is defined in test_db
				$this->_createResidualParameter($randPct, $partnerRepUcpId, $pId);
				//Create User Parameters for PartnerRep, none is defined in test_db
				$this->_createUserParameter($randPct, $partnerRepUcpId, '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

				//add parameter values for associated users
				if (!empty($tstCaseData['assoc_users'])) {
					foreach ($tstCaseData['assoc_users'] as $caseKey => $assocUserId) {
						if ($caseKey === 'partner_id') {
							$this->_createResidualParameter($randPct, $partnerUcpId, $pId);
							//partner is associated to itself in user parameters (3dr function param)
							$this->_createUserParameter($randPct, $partnerUcpId, $partnerUserId, $pId);
						} elseif ($caseKey === 'sm_user_id' || $caseKey === 'sm2_user_id') {
							//managers are associated to the rep UCP
							$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId, $assocUserId);
							$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $assocUserId, $pId);
						} elseif ($caseKey === 'referer_id') {
							$this->_createResidualParameter($randPct, $refUcpId, $pId);
							$this->_createUserParameter($randPct, $refUcpId, self::REF_ID, $pId);
						} elseif ($caseKey === 'reseller_id') {
							$this->_createResidualParameter($randPct, $resUcpId, $pId);
							$this->_createUserParameter($randPct, $resUcpId, self::RES_ID, $pId);
						}
					}
				}
				copy($file, $datafile);

				$expected = [
					'job_name' => 'Residual Report Admin',
					'result' => true,
					'recordsAdded' => true,
					'start_time' => '',
					'end_time' => '',
					'log' => [
						'products' => [$prodNameKey],
						'errors' => [],
						'optional_msgs' => ['Selected month/year: 01/2016'],
					]
				];
				$Merchant->save(
					[
						'Merchant' => [
							'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
							'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
							'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
							'partner_id' => Hash::get($tstCaseData['assoc_users'], 'partner_id'),
							'sm_user_id' => Hash::get($tstCaseData['assoc_users'], 'sm_user_id'),
							'sm2_user_id' => Hash::get($tstCaseData['assoc_users'], 'sm2_user_id'),
							'referer_id' => Hash::get($tstCaseData['assoc_users'], 'referer_id'),
							'reseller_id' => Hash::get($tstCaseData['assoc_users'], 'reseller_id'),
						]
					], ['validate' => false]
				);
				$this->__saveTestArchivedData($tstCaseData['arhive_name'], $pId, Hash::extract($tstCaseData, 'modifiers'));
				$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
				$expected['start_time'] = $response['start_time'];
				$expected['end_time'] = $response['end_time'];
				$this->assertEquals($expected, $response);

				// check expected residual report values
				$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

				//One record per add() method execution for month/year/product
				$response = $response[0]['ResidualReport'];

				//merchant discount item fee is not stored by itself in residual report only in mp archive
				$response['m_discount_item_fee'] = 55;
				$this->assertEquals(1, $response['r_month']);
				$this->assertEquals(2016, $response['r_year']);
				$this->assertEquals(120, $response['r_avg_ticket']);
				$this->assertEquals(250, $response['r_items']);
				$this->assertEquals(30000, $response['r_volume']);
				$this->assertEquals(0.21, $response['m_rate_pct']);
				$this->assertEquals(.15, $response['m_per_item_fee']);
				$this->assertEquals(25, $response['m_statement_fee']);

				if (!empty(Hash::get($tstCaseData['assoc_users'], 'partner_id'))) {
					if (in_array($prodNameKey, ["Debit Discount", "EBT Discount"])) {
						//Gross profit calculation for these products is
						$expected = $response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['partner_rate'] / 100));
					} else {
						$expected = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['partner_rate'] / 100) + (Hash::get($response, 'partner_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
					}

					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_gross_profit'], $expected, .5));
					$this->assertEquals(2.99, $response['partner_rate']);
					$this->assertEquals(.6, $response['partner_per_item_fee']);
					$this->assertEquals(16, $response['partner_statement_fee']);
					$this->assertEquals($randPct, $response['partner_profit_pct']);
					$expected = $response['partner_gross_profit'] * ($response['partner_pct_of_gross'] / 100) * ($response['partner_profit_pct'] / 100);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_profit_amount'], $expected, .5));
					$this->assertEquals($randPct, $response['partner_pct_of_gross']);

					if (in_array($prodNameKey, ["Debit Discount", "EBT Discount"])) {
						//Gross profit calculation for these products is
						$expected = $response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['partner_rep_rate'] / 100));
					} else {
						$expected = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['partner_rep_rate'] / 100) + (Hash::get($response, 'partner_rep_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
					}

					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_gross_profit'], $expected, .5));
					$this->assertEquals(1.99, $response['partner_rep_rate']);
					$this->assertEquals(.5, $response['partner_rep_per_item_fee']);
					$this->assertEquals(15, $response['partner_rep_statement_fee']);
					$this->assertEquals($randPct, $response['partner_rep_profit_pct']);
					$this->assertEquals($randPct, $response['partner_rep_pct_of_gross']);
					$expected = ($response['partner_rep_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['partner_rep_pct_of_gross'] / 100 * ($response['partner_rep_profit_pct'] / 100);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_profit_amount'], $expected, .5));
				} else {
					if (in_array($prodNameKey, ["Debit Discount", "EBT Discount"])) {
						// this rep_gross_profit calculation applies only to these products in this if block
						$expected = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['r_rate_pct'] / 100)));
					} else {
						$expected = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['r_rate_pct'] / 100) + (Hash::get($response, 'r_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
					}

					$this->assertEquals(1.49, $response['r_rate_pct']);
					$this->assertEquals('0.1800', $response['r_per_item_fee']);
					$this->assertEquals('4.7500', $response['r_statement_fee']);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['rep_gross_profit'], $expected, .5));
					$this->assertEquals($randPct, $response['r_profit_pct']);
					$this->assertEquals($randPct, $response['rep_pct_of_gross']);

					//Calculation for r_profit_amount applies to all products in this test
					//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
					$expected = ($response['rep_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['rep_pct_of_gross'] / 100 * ($response['r_profit_pct'] / 100);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .5));
				}

				if (!empty(Hash::get($tstCaseData['assoc_users'], 'referer_id'))) {
					if (in_array($prodNameKey, ["Debit Discount", "EBT Discount"])) {
						$refExpectedGp = $response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['referrer_rate'] / 100));
						$resExpectedGp = $response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['reseller_rate'] / 100));
					} else {
						$refExpectedGp = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['referrer_rate'] / 100) + (Hash::get($response, 'referrer_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
						$resExpectedGp = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['reseller_rate'] / 100) + (Hash::get($response, 'reseller_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
					}

					$this->assertTrue($this->_isAcceptableErrorPercent($response['referrer_gross_profit'], $refExpectedGp, .5));
					$this->assertTrue($this->_isAcceptableErrorPercent($response['reseller_gross_profit'], $resExpectedGp, .5));
					$this->assertEquals($randPct, $response['refer_profit_pct']);
					$this->assertEquals($tstCaseData['modifiers']['ref_p_pct'], $response['referer_pct_of_gross']);
					$this->assertEquals($randPct, $response['res_profit_pct']);
					$this->assertEquals($tstCaseData['modifiers']['res_p_pct'], $response['reseller_pct_of_gross']);

					//for this test ref_p_type = res_p_type = 'percentage' for profit amount wich uses this formula
					$expected = ($response['referrer_gross_profit'] * ($response['referer_pct_of_gross'] / 100) * ($response['ref_p_value'] / 100));
					$this->assertTrue($this->_isAcceptableErrorPercent($response['refer_profit_amount'], $expected, .5));
					$expected = ($response['reseller_gross_profit'] * ($response['reseller_pct_of_gross'] / 100) * ($response['res_p_value'] / 100));
					$this->assertTrue($this->_isAcceptableErrorPercent($response['res_profit_amount'], $expected, .5));

					$this->assertEquals(2, $response['referrer_rate']);
					$this->assertEquals(.5, $response['referrer_per_item_fee']);
					$this->assertEquals(7, $response['referrer_statement_fee']);

					$this->assertEquals(3, $response['reseller_rate']);
					$this->assertEquals(.6, $response['reseller_per_item_fee']);
					$this->assertEquals(8, $response['reseller_statement_fee']);
				}

				if (!empty(Hash::get($tstCaseData['assoc_users'], 'sm_user_id'))) {
					if (in_array($prodNameKey, ["Debit Discount", "EBT Discount"])) {
						// this [managers]_gross_profit calculation applies only to these products in this if block
						$expectedSmGp = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['manager_rate'] / 100)));
						$expectedSm2Gp = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - ($response['manager2_rate'] / 100)));
					} else {
						$expectedSmGp = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['manager_rate'] / 100) + (Hash::get($response, 'manager_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
						$expectedSm2Gp = ($response['r_volume'] * ($response['m_rate_pct'] / 100 - (($response['manager2_rate'] / 100) + (Hash::get($response, 'manager2_risk_pct') / 100)))) + ($response['r_items'] * $response['m_discount_item_fee']);
					}
					$this->assertEquals(2, $response['manager_rate']);
					$this->assertEquals(.5, $response['manager_per_item_fee']);
					$this->assertEquals(7, $response['manager_statement_fee']);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_gross_profit'], $expectedSmGp, .5));
					$this->assertEquals($randPct, $response['manager_profit_pct']);
					$this->assertEquals($randPct, $response['manager_pct_of_gross']);
					//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
					$expected = ($response['manager_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['manager_pct_of_gross'] / 100 * ($response['manager_profit_pct'] / 100);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount'], $expected, .5));

					$this->assertEquals(3, $response['manager2_rate']);
					$this->assertEquals(.6, $response['manager2_per_item_fee']);
					$this->assertEquals(8, $response['manager2_statement_fee']);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager2_gross_profit'], $expectedSm2Gp, .5));
					$this->assertEquals($randPct, $response['manager_profit_pct_secondary']);
					$this->assertEquals($randPct, $response['manager2_pct_of_gross']);
					//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
					$expected = ($response['manager2_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['manager2_pct_of_gross'] / 100 * ($response['manager_profit_pct_secondary'] / 100);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount_secondary'], $expected, .5));
				}

				$expected = $response['r_profit_amount'] + $response['partner_rep_profit_amount'];

				$this->assertTrue($this->_isAcceptableErrorPercent($response['total_profit'], $expected, 1));
				$this->assertEquals(null, $response['merchant_state']);
			}
			//Delete data before next test scenario
			$this->ResidualReport->deleteAll(['r_year' => $year, 'r_month' => $month]);
		}
	}

/**
 * testAddarchiveWithManagers
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 * The subset of products being tested here all use the same formulas and logic.
 *
 * @return void
 */
	public function testAddarchiveWithManagers() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$this->_createUCP(self::SM_ID);
		$this->_createUCP(self::SM2_ID);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		foreach ($this->commonProducts as $key => $pId) {
			$product = [
			'ProductsServicesType' => [
					'id' => $pId,
					'products_services_description' => $key
				]
			];
			//Add product to merchant if doesn't have it
			if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
				$ProductsAndService->save([
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'products_services_type_id' => $pId,
				]);
			}
			//create a pseudo-random profit percent amount
			$randPct = rand(0, 100);

			//Add user's residual parameter value for rep
			$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);
			//Add residual parameter value for the rep's manager
			$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId, self::SM_ID);
			//Add residual parameter value for rep's manager2
			$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId, self::SM2_ID);

			//Add user's user parameter value
			$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);
			//Add user parameter value for the rep's manager
			$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', self::SM_ID, $pId);
			//Add user parameter value for the rep's manager2
			$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', self::SM2_ID, $pId);

			copy($file, $datafile);

			$expected = [
				'job_name' => 'Residual Report Admin',
				'result' => true,
				'recordsAdded' => true,
				'start_time' => '',
				'end_time' => '',
				'log' => [
					'products' => [$key],
					'errors' => [],
					'optional_msgs' => ['Selected month/year: 01/2016'],
				]
			];
			//save merchant with managers
			$Merchant->save(
				[
					'Merchant' => [
						'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
						'partner_id' => null,
						'sm_user_id' => self::SM_ID,
						'sm2_user_id' => self::SM2_ID,
						'referer_id' => null,
						'reseller_id' => null,
					]
				], ['validate' => false]
			);
			$this->__saveTestArchivedData('archiveWithManagers', $pId);

			$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
			$expected['start_time'] = $response['start_time'];
			$expected['end_time'] = $response['end_time'];
			$this->assertEquals($expected, $response);

			// check expected residual report values
			$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

			//One record per add() method execution for month/year/product

			$response = $response[0]['ResidualReport'];

			$this->assertEquals(1, $response['r_month']);
			$this->assertEquals(2016, $response['r_year']);
			$this->assertEquals(120, $response['r_avg_ticket']);
			$this->assertEquals(250, $response['r_items']);
			$this->assertEquals(30000, $response['r_volume']);

			$this->assertEquals(0.21, $response['m_rate_pct']);
			$this->assertEquals(.15, $response['m_per_item_fee']);
			$this->assertEquals(25, $response['m_statement_fee']);

			$this->assertEquals('0', $response['partner_rate']);
			$this->assertEquals('0', $response['partner_per_item_fee']);
			$this->assertEquals('0', $response['partner_statement_fee']);
			$this->assertEquals('0', $response['partner_gross_profit']);
			$this->assertEquals('0', $response['partner_profit_pct']);
			$this->assertEquals('0', $response['partner_profit_amount']);
			$this->assertEquals('0', $response['partner_pct_of_gross']);

			$this->assertEquals('0', $response['referrer_rate']);
			$this->assertEquals('0', $response['referrer_per_item_fee']);
			$this->assertEquals('0', $response['referrer_statement_fee']);
			$this->assertEquals('0', $response['referrer_gross_profit']);
			$this->assertEquals(0, $response['refer_profit_pct']);
			$this->assertEquals(0, $response['refer_profit_amount']);
			$this->assertEquals('0', $response['referer_pct_of_gross']);

			$this->assertEquals('0', $response['reseller_rate']);
			$this->assertEquals('0', $response['reseller_per_item_fee']);
			$this->assertEquals('0', $response['reseller_statement_fee']);
			$this->assertEquals('0', $response['reseller_gross_profit']);
			$this->assertEquals(0, $response['res_profit_pct']);
			$this->assertEquals(0, $response['res_profit_amount']);
			$this->assertEquals('0', $response['reseller_pct_of_gross']);

			$this->assertEquals(1.49, $response['r_rate_pct']);
			$this->assertEquals('0.1800', $response['r_per_item_fee']);
			$this->assertEquals('4.7500', $response['r_statement_fee']);

			//rep_gross_profit calculation applies to all products in this test
			$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['r_per_item_fee']));
			$this->assertEquals($expected, $response['rep_gross_profit']);
			$this->assertEquals($randPct, $response['r_profit_pct']);
			$this->assertEquals($randPct, $response['rep_pct_of_gross']);

			//Calculation for r_profit_amount applies to all products in this test
			$expected = ($response['rep_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['rep_pct_of_gross'] / 100 * ($response['r_profit_pct'] / 100);

			$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .9));

			$this->assertEquals('0', $response['partner_rep_rate']);
			$this->assertEquals('0', $response['partner_rep_per_item_fee']);
			$this->assertEquals('0', $response['partner_rep_statement_fee']);
			$this->assertEquals('0', $response['partner_rep_gross_profit']);
			$this->assertEquals('0', $response['partner_rep_profit_pct']);
			$this->assertEquals('0', $response['partner_rep_profit_amount']);
			$this->assertEquals('0', $response['partner_rep_pct_of_gross']);

			$this->assertEquals(2, $response['manager_rate']);
			$this->assertEquals(.5, $response['manager_per_item_fee']);
			$this->assertEquals(7, $response['manager_statement_fee']);
			//expected manager goss profit calculation
			$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['manager_per_item_fee']));
			$this->assertEquals($expected, $response['manager_gross_profit']);
			$this->assertEquals($randPct, $response['manager_profit_pct']);

			//Manager 2 profit amount
			$expected = ($response['manager_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['manager_pct_of_gross'] / 100 * ($response['manager_profit_pct'] / 100);
			$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount'], $expected, .9));
			$this->assertEquals($randPct, $response['manager_pct_of_gross']);

			$this->assertEquals(3, $response['manager2_rate']);
			$this->assertEquals(.6, $response['manager2_per_item_fee']);
			$this->assertEquals(8, $response['manager2_statement_fee']);
			//expected manager 2 goss profit calculation
			$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['manager2_per_item_fee']));
			$this->assertEquals($expected, $response['manager2_gross_profit']);
			$this->assertEquals($randPct, $response['manager_profit_pct_secondary']);

			//expected manager 2 profit amount calculation
			$expected = ($response['manager2_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['manager2_pct_of_gross'] / 100 * ($response['manager_profit_pct_secondary'] / 100);

			$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount_secondary'], $expected, .5));
			$this->assertEquals($randPct, $response['manager2_pct_of_gross']);

			$this->assertEquals(null, $response['merchant_state']);
		}
	}
/**
 * testAddarchiveWithRefRes
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 * The subset of products being tested here all use the same formulas and logic.
 *
 * @return void
 */
	public function testAddarchiveWithRefRes() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$refUcpId = $this->_createUCP(self::REF_ID);
		$resUcpId = $this->_createUCP(self::RES_ID);
		$modifiers = [];
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		//perform entire test twise, first without res_p_pct, ref_p_pct and second with those values set
		for ($x = 0; $x < 2; $x ++) {
			if ($x === 1) {
				$modifiers['res_p_pct'] = '80';
				$modifiers['ref_p_pct'] = '60';
			}
			foreach ($this->commonProducts as $key => $pId) {
				$product = [
				'ProductsServicesType' => [
						'id' => $pId,
						'products_services_description' => $key
					]
				];
				//Add product to merchant if doesn't have it
				if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
					$ProductsAndService->save([
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'products_services_type_id' => $pId,
					]);
				}

				//create a pseudo-random profit percent amount
				$randPct = rand(0, 100);

				//Add user's residual parameter value for rep
				$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);
				//Add residual parameter value for referrer
				$this->_createResidualParameter($randPct, $refUcpId, $pId);
				//Add residual parameter value for reseller
				$this->_createResidualParameter($randPct, $resUcpId, $pId);

				//Add user's user parameter value
				$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);
				//Add user parameter value for the referrer
				$this->_createUserParameter($randPct, $refUcpId, self::REF_ID, $pId);
				//Add user parameter value for the reseller
				$this->_createUserParameter($randPct, $resUcpId, self::RES_ID, $pId);

				copy($file, $datafile);

				$expected = [
					'job_name' => 'Residual Report Admin',
					'result' => true,
					'recordsAdded' => true,
					'start_time' => '',
					'end_time' => '',
					'log' => [
						'products' => [$key],
						'errors' => [],
						'optional_msgs' => ['Selected month/year: 01/2016'],
					]
				];
				//save merchant with managers
				$Merchant->save(
					[
						'Merchant' => [
							'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
							'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
							'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
							'partner_id' => null,
							'sm_user_id' => null,
							'sm2_user_id' => null,
							'referer_id' => self::REF_ID,
							'reseller_id' => self::RES_ID,
						]
					], ['validate' => false]
				);
				$this->__saveTestArchivedData('archiveWithRefRes', $pId, $modifiers);

				$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
				$expected['start_time'] = $response['start_time'];
				$expected['end_time'] = $response['end_time'];
				$this->assertEquals($expected, $response);

				// check expected residual report values
				$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

				//One record per add() method execution for month/year/product

				$response = $response[0]['ResidualReport'];

				$this->assertEquals(1, $response['r_month']);
				$this->assertEquals(2016, $response['r_year']);
				$this->assertEquals(120, $response['r_avg_ticket']);
				$this->assertEquals(250, $response['r_items']);
				$this->assertEquals(30000, $response['r_volume']);

				$this->assertEquals(0.21, $response['m_rate_pct']);
				$this->assertEquals(.15, $response['m_per_item_fee']);
				$this->assertEquals(25, $response['m_statement_fee']);

				$this->assertEquals('0', $response['partner_rate']);
				$this->assertEquals('0', $response['partner_per_item_fee']);
				$this->assertEquals('0', $response['partner_statement_fee']);
				$this->assertEquals('0', $response['partner_gross_profit']);
				$this->assertEquals('0', $response['partner_profit_pct']);
				$this->assertEquals('0', $response['partner_profit_amount']);
				$this->assertEquals('0', $response['partner_pct_of_gross']);

				$this->assertEquals(2, $response['referrer_rate']);
				$this->assertEquals(.5, $response['referrer_per_item_fee']);
				$this->assertEquals(7, $response['referrer_statement_fee']);
				//Expected calculation of referrer gross profit
				$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['referrer_per_item_fee']));
				$this->assertEquals($expected, $response['referrer_gross_profit']);
				$this->assertEquals($randPct, $response['refer_profit_pct']);

				if (!empty($modifiers)) {
					$this->assertEquals($modifiers['ref_p_pct'], $response['referer_pct_of_gross']);
				} else {
					$this->assertEquals($randPct, $response['referer_pct_of_gross']);
				}
				//Expected calculation of referrer profit amount, dividing by 100 onlyto conver the percent into decimal form
				$expected = $response['referrer_gross_profit'] * ($response['referer_pct_of_gross'] / 100) * ($response['refer_profit_pct'] / 100);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['refer_profit_amount'], CakeNumber::precision($expected, 4), .9));

				$this->assertEquals(3, $response['reseller_rate']);
				$this->assertEquals(.6, $response['reseller_per_item_fee']);
				$this->assertEquals(8, $response['reseller_statement_fee']);
				//Expected calculation of reseller gross profit
				$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['reseller_per_item_fee']));
				$this->assertEquals($expected, $response['reseller_gross_profit']);
				$this->assertEquals($randPct, $response['res_profit_pct']);
				if (!empty($modifiers)) {
					$this->assertEquals($modifiers['res_p_pct'], $response['reseller_pct_of_gross']);
				} else {
					$this->assertEquals($randPct, $response['reseller_pct_of_gross']);
				}
				//Expected calculation of reseller profit amount, dividing by 100 onlyto conver the percent into decimal form
				$expected = $response['reseller_gross_profit'] * ($response['reseller_pct_of_gross'] / 100) * ($response['res_profit_pct'] / 100);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['res_profit_amount'], CakeNumber::precision($expected, 4), .9));
				$this->assertEquals(1.49, $response['r_rate_pct']);
				$this->assertEquals('0.1800', $response['r_per_item_fee']);
				$this->assertEquals('4.7500', $response['r_statement_fee']);

				//rep_gross_profit calculation applies to all products in this test
				$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['r_per_item_fee']));
				$this->assertEquals($expected, $response['rep_gross_profit']);
				$this->assertEquals($randPct, $response['r_profit_pct']);
				$this->assertEquals($randPct, $response['rep_pct_of_gross']);

				//Calculation for r_profit_amount applies to all products in this test.
				//For now we dont want to subtract referer and reseller profit hence multiplication by zero to prevent substracting those amounts.
				$expected = ($response['rep_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['rep_pct_of_gross'] / 100 * ($response['r_profit_pct'] / 100);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .9));

				$this->assertEquals('0', $response['partner_rep_rate']);
				$this->assertEquals('0', $response['partner_rep_per_item_fee']);
				$this->assertEquals('0', $response['partner_rep_statement_fee']);
				$this->assertEquals('0', $response['partner_rep_gross_profit']);
				$this->assertEquals('0', $response['partner_rep_profit_pct']);
				$this->assertEquals('0', $response['partner_rep_profit_amount']);
				$this->assertEquals('0', $response['partner_rep_pct_of_gross']);

				$this->assertEquals('0', $response['manager_rate']);
				$this->assertEquals('0', $response['manager_per_item_fee']);
				$this->assertEquals('0', $response['manager_statement_fee']);
				$this->assertEquals('0', $response['manager_gross_profit']);
				$this->assertEquals(0, $response['manager_profit_pct']);
				$this->assertEquals(0, $response['manager_profit_amount']);
				$this->assertEquals(0, $response['manager_pct_of_gross']);

				$this->assertEquals('0', $response['manager2_rate']);
				$this->assertEquals('0', $response['manager2_per_item_fee']);
				$this->assertEquals('0', $response['manager2_statement_fee']);
				$this->assertEquals('0', $response['manager2_gross_profit']);
				$this->assertEquals(0, $response['manager_profit_pct_secondary']);
				$this->assertEquals(0, $response['manager_profit_amount_secondary']);
				$this->assertEquals('0', $response['manager2_pct_of_gross']);

				$expected = $response['r_profit_amount'] + $response['partner_rep_profit_amount'];
				//cannot always check if they are equal due to small unpredictable rounding error
				//Instead check that they do not differ by more than this
				if (abs($response['total_profit']) - abs($expected) != 0) {
					$this->assertLessThan(.01, abs($response['total_profit'] - $expected));
				} else {
					$this->assertEquals($expected, $response['total_profit']);
				}

				$this->assertEquals(null, $response['merchant_state']);
			}
			//Delete data to do next test iteration
			$this->ResidualReport->deleteAll(['r_year' => $year, 'r_month' => $month]);
		}
	}
/**
 * testAddACHProduct
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 *
 * @return void
 */
	public function testAddACHProduct() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$AxiaCalculate = ClassRegistry::init('AxiaCalculate');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$partnerRepUcpId = '8bbe2d12-2975-4466-a309-fcf4e721d468';
		$partnerUcpId = 'a8c5607c-cf2e-4b5b-a2f9-fda5a95a3950';
		$partnerUserId = '19b2c1da-943e-4761-829a-4ca54008dd58';
		$testScenarios = [
			'rep' => [
				'arhive_name' => 'archiveRepOnly',
				'assoc_users' => []
			],
			'rep_and_mgrs' => [
				'arhive_name' => 'archiveWithManagers',
				'assoc_users' => [
					'sm_user_id' => self::SM_ID,
					'sm2_user_id' => self::SM2_ID
				]
			],
			'rep_ref_res' => [
				'arhive_name' => 'archiveWithRefRes',
				'assoc_users' => [
					'referer_id' => self::REF_ID,
					'reseller_id' => self::RES_ID
				],
				'modifiers' => [
					'ref_p_pct' => 60,
					'res_p_pct' => 70,
					'res_p_value' => 20,
					'ref_p_value' => 30,
					'res_p_type' => 'points',
					'ref_p_type' => 'points',
				]
			],
			'rep_partner' => [
				'arhive_name' => 'archiveRepPartner',
				'assoc_users' => [
					//partner id
					'partner_id' => $partnerUserId
				]
			]
		];
		$pId = 'e8fa66a0-790f-4710-b7de-ef79be75a1c7';
		$prodNameKey = 'ACH';
		$product = [
			'ProductsServicesType' => [
				'id' => $pId,
				'products_services_description' => $prodNameKey
			]
		];
		//Add product to merchant if doesn't have it
		if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
			$ProductsAndService->save([
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'products_services_type_id' => $pId,
			]);
		}
		//create comp profiles for all users except partner which already has one in test_db
		$this->_createUCP(self::SM_ID);
		$this->_createUCP(self::SM2_ID);
		$refUcpId = $this->_createUCP(self::REF_ID);
		$resUcpId = $this->_createUCP(self::RES_ID);
		//create a pseudo-random profit percent amount
		$randPct = rand(0, 100);
		//Add user's residual parameter value
		$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);
		//Add user's user parameter value
		$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

		//Create Residual Parameters for PartnerRep, none is defined in test_db
		$this->_createResidualParameter($randPct, $partnerRepUcpId, $pId);
		//Create User Parameters for PartnerRep, none is defined in test_db
		$this->_createUserParameter($randPct, $partnerRepUcpId, '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		foreach ($testScenarios as $tstCaseData) {
			//add parameter values for associated users
			if (!empty($tstCaseData['assoc_users'])) {
				foreach ($tstCaseData['assoc_users'] as $caseKey => $assocUserId) {
					if ($caseKey === 'partner_id') {
						$this->_createResidualParameter($randPct, $partnerUcpId, $pId);
						//partner is associated to itself in user parameters (3dr function param)
						$this->_createUserParameter($randPct, $partnerUcpId, $partnerUserId, $pId);
					} elseif ($caseKey === 'sm_user_id' || $caseKey === 'sm2_user_id') {
						//managers are associated to the rep UCP
						$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId, $assocUserId);
						$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $assocUserId, $pId);
					} elseif ($caseKey === 'referer_id') {
						$this->_createResidualParameter($randPct, $refUcpId, $pId);
						$this->_createUserParameter($randPct, $refUcpId, self::REF_ID, $pId);
					} elseif ($caseKey === 'reseller_id') {
						$this->_createResidualParameter($randPct, $resUcpId, $pId);
						$this->_createUserParameter($randPct, $resUcpId, self::RES_ID, $pId);
					}
				}
			}
			copy($file, $datafile);

			$expected = [
				'job_name' => 'Residual Report Admin',
				'result' => true,
				'recordsAdded' => true,
				'start_time' => '',
				'end_time' => '',
				'log' => [
					'products' => [$prodNameKey],
					'errors' => [],
					'optional_msgs' => ['Selected month/year: 01/2016'],
				]
			];
			$Merchant->save(
				[
					'Merchant' => [
						'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
						'partner_id' => Hash::get($tstCaseData['assoc_users'], 'partner_id'),
						'sm_user_id' => Hash::get($tstCaseData['assoc_users'], 'sm_user_id'),
						'sm2_user_id' => Hash::get($tstCaseData['assoc_users'], 'sm2_user_id'),
						'referer_id' => Hash::get($tstCaseData['assoc_users'], 'referer_id'),
						'reseller_id' => Hash::get($tstCaseData['assoc_users'], 'reseller_id'),
					]
				], ['validate' => false]
			);
			$this->__saveTestArchivedData($tstCaseData['arhive_name'], $pId, Hash::extract($tstCaseData, 'modifiers'));
			$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
			$expected['start_time'] = $response['start_time'];
			$expected['end_time'] = $response['end_time'];
			$this->assertEquals($expected, $response);

			// check expected residual report values
			$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

			//One record per add() method execution for month/year/product
			$response = $response[0]['ResidualReport'];
			//merchant discount item fee is not stored by itself in residual report only in mp archive
			$this->assertEquals(1, $response['r_month']);
			$this->assertEquals(2016, $response['r_year']);
			$this->assertEquals(120, $response['r_avg_ticket']);
			$this->assertEquals(250, $response['r_items']);
			$this->assertEquals(30000, $response['r_volume']);
			$this->assertEquals(0.21, $response['m_rate_pct']);
			$this->assertEquals(.15, $response['m_per_item_fee']);
			$this->assertEquals(25, $response['m_statement_fee']);
			//set merchant amounts for calculations
			$amounts['volume'] = $response['r_volume'];
			$amounts['num_items'] = $response['r_items'];
			$amounts['m_rate'] = ($response['m_rate_pct']);
			$amounts['m_monthly'] = $response['m_statement_fee'];
			$amounts['m_item_fee'] = $response['m_per_item_fee'];
			if (!empty(Hash::get($tstCaseData['assoc_users'], 'partner_id'))) {
				$amounts['user_pi'] = $response['partner_per_item_fee'];
				$amounts['u_monthly'] = $response['partner_statement_fee'];
				$expected = $AxiaCalculate->uGrossPrftType4($amounts);

				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_gross_profit'], $expected, .5));
				$this->assertEquals(2.99, $response['partner_rate']);
				$this->assertEquals(.6, $response['partner_per_item_fee']);
				$this->assertEquals(16, $response['partner_statement_fee']);
				$this->assertEquals($randPct, $response['partner_profit_pct']);
				$expected = $response['partner_gross_profit'] * ($response['partner_pct_of_gross'] / 100) * ($response['partner_profit_pct'] / 100);

				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_profit_amount'], $expected, .5));
				$this->assertEquals($randPct, $response['partner_pct_of_gross']);

				$amounts['user_pi'] = $response['partner_rep_per_item_fee'];
				$amounts['u_monthly'] = $response['partner_rep_statement_fee'];
				$expected = $AxiaCalculate->uGrossPrftType4($amounts);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_gross_profit'], $expected, .5));

				$this->assertEquals(1.99, $response['partner_rep_rate']);
				$this->assertEquals(.5, $response['partner_rep_per_item_fee']);
				$this->assertEquals(15, $response['partner_rep_statement_fee']);
				$this->assertEquals($randPct, $response['partner_rep_profit_pct']);
				$this->assertEquals($randPct, $response['partner_rep_pct_of_gross']);
				$expected = ($response['partner_rep_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['partner_rep_pct_of_gross'] / 100 * ($response['partner_rep_profit_pct'] / 100);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_profit_amount'], $expected, .5));
			} else {
				$amounts['user_pi'] = $response['r_per_item_fee'];
				$amounts['u_monthly'] = $response['r_statement_fee'];
				$expected = $AxiaCalculate->uGrossPrftType4($amounts);

				$this->assertEquals(1.49, $response['r_rate_pct']);
				$this->assertEquals('0.1800', $response['r_per_item_fee']);
				$this->assertEquals('4.7500', $response['r_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['rep_gross_profit'], $expected, .5));

				$this->assertEquals($randPct, $response['r_profit_pct']);
				$this->assertEquals($randPct, $response['rep_pct_of_gross']);

				//Calculation for r_profit_amount applies to all products in this test
				//ref and res profit amounts should be subtracted for this test case.
				$expected = ($response['rep_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['rep_pct_of_gross'] / 100 * ($response['r_profit_pct'] / 100);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .5));
			}

			if (!empty(Hash::get($tstCaseData['assoc_users'], 'referer_id'))) {
				$amounts['user_pi'] = $response['referrer_per_item_fee'];
				$amounts['u_monthly'] = $response['referrer_statement_fee'];
				$referrerGrossProfit = $AxiaCalculate->uGrossPrftType4($amounts);
				$amounts['user_pi'] = $response['reseller_per_item_fee'];
				$amounts['u_monthly'] = $response['reseller_statement_fee'];
				$resellerGrossProfit = $AxiaCalculate->uGrossPrftType4($amounts);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['referrer_gross_profit'], $referrerGrossProfit, .5));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['reseller_gross_profit'], $resellerGrossProfit, .5));
				$this->assertEquals($randPct, $response['refer_profit_pct']);
				$this->assertEquals($tstCaseData['modifiers']['ref_p_pct'], $response['referer_pct_of_gross']);
				$this->assertEquals($randPct, $response['res_profit_pct']);
				$this->assertEquals($tstCaseData['modifiers']['res_p_pct'], $response['reseller_pct_of_gross']);

				//for this test ref_p_type = res_p_type = 'points' for profit amount wich uses this formula
				$expected = ($response['r_volume'] * ($response['referer_pct_of_gross'] / 100) * ($response['ref_p_value'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['refer_profit_amount'], $expected, .5));
				$expected = ($response['r_volume'] * ($response['reseller_pct_of_gross'] / 100) * ($response['res_p_value'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['res_profit_amount'], $expected, .5));

				$this->assertEquals(2, $response['referrer_rate']);
				$this->assertEquals(.5, $response['referrer_per_item_fee']);
				$this->assertEquals(7, $response['referrer_statement_fee']);

				$this->assertEquals(3, $response['reseller_rate']);
				$this->assertEquals(.6, $response['reseller_per_item_fee']);
				$this->assertEquals(8, $response['reseller_statement_fee']);
			}

			if (!empty(Hash::get($tstCaseData['assoc_users'], 'sm_user_id'))) {
				$amounts['user_pi'] = $response['manager_per_item_fee'];
				$amounts['u_monthly'] = $response['manager_statement_fee'];
				$expectedSmGp = $AxiaCalculate->uGrossPrftType4($amounts);

				$amounts['user_pi'] = $response['manager2_per_item_fee'];
				$amounts['u_monthly'] = $response['manager2_statement_fee'];
				$expectedSm2Gp = $AxiaCalculate->uGrossPrftType4($amounts);

				$this->assertEquals(2, $response['manager_rate']);
				$this->assertEquals(.5, $response['manager_per_item_fee']);
				$this->assertEquals(7, $response['manager_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_gross_profit'], $expectedSmGp, .5));

				$this->assertEquals($randPct, $response['manager_profit_pct']);
				$this->assertEquals($randPct, $response['manager_pct_of_gross']);
				//ref and res profit amounts should not be subtracted for this test case.
				$expected = ($response['manager_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['manager_pct_of_gross'] / 100 * ($response['manager_profit_pct'] / 100);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount'], $expected, .5));

				$this->assertEquals(3, $response['manager2_rate']);
				$this->assertEquals(.6, $response['manager2_per_item_fee']);
				$this->assertEquals(8, $response['manager2_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager2_gross_profit'], $expectedSm2Gp, .5));
				$this->assertEquals($randPct, $response['manager_profit_pct_secondary']);
				$this->assertEquals($randPct, $response['manager2_pct_of_gross']);
				//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
				$expected = ($response['manager2_gross_profit'] - $response['partner_profit_amount'] - $response['refer_profit_amount'] - $response['res_profit_amount']) * $response['manager2_pct_of_gross'] / 100 * ($response['manager_profit_pct_secondary'] / 100);

				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount_secondary'], $expected, .5));
			}

			$expected = $response['r_profit_amount'] + $response['partner_rep_profit_amount'];
			$this->assertTrue($this->_isAcceptableErrorPercent($response['total_profit'], $expected, 1));
			$this->assertEquals(null, $response['merchant_state']);

			//Delete data before next test scenario
			$this->ResidualReport->deleteAll(['r_year' => $year, 'r_month' => $month]);
		}
	}

/**
 * testAddGateway1Product
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 *
 * @return void
 */
	public function testAddGateway1Product() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$AxiaCalculate = ClassRegistry::init('AxiaCalculate');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$partnerRepUcpId = '8bbe2d12-2975-4466-a309-fcf4e721d468';
		$partnerUcpId = 'a8c5607c-cf2e-4b5b-a2f9-fda5a95a3950';
		$partnerUserId = '19b2c1da-943e-4761-829a-4ca54008dd58';
		$testScenarios = [
			'rep' => [
				'arhive_name' => 'archiveRepOnly',
				'assoc_users' => []
			],
			'rep_and_mgrs' => [
				'arhive_name' => 'archiveWithManagers',
				'assoc_users' => [
					'sm_user_id' => self::SM_ID,
					'sm2_user_id' => self::SM2_ID
				]
			],
			'rep_ref_res' => [
				'arhive_name' => 'archiveWithRefRes',
				'assoc_users' => [
					'referer_id' => self::REF_ID,
					'reseller_id' => self::RES_ID
				],
				'modifiers' => [
					'ref_p_pct' => 60,
					'res_p_pct' => 70,
					'res_p_value' => 20,
					'ref_p_value' => 30,
					'res_p_type' => 'points',
					'ref_p_type' => 'points',
					'gateway_mid' => '3948000030003045',
				]
			],
			'rep_partner' => [
				'arhive_name' => 'archiveRepPartner',
				'assoc_users' => [
					//partner id
					'partner_id' => $partnerUserId
				]
			]
		];
		$pId = 'f446b74f-ae19-4505-82c7-67cf93fcfe3d';
		$prodNameKey = 'Gateway 1';
		$product = [
			'ProductsServicesType' => [
				'id' => $pId,
				'products_services_description' => $prodNameKey
			]
		];
		//Add product to merchant if doesn't have it
		if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
			$ProductsAndService->save([
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'products_services_type_id' => $pId,
			]);
		}
		//create comp profiles for all users except partner which already has one in test_db
		$this->_createUCP(self::SM_ID);
		$this->_createUCP(self::SM2_ID);
		$refUcpId = $this->_createUCP(self::REF_ID);
		$resUcpId = $this->_createUCP(self::RES_ID);
		//create a pseudo-random profit percent amount
		$randPct = rand(0, 100);
		//Add user's residual parameter value
		$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);
		//Add user's user parameter value
		$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

		//Create Residual Parameters for PartnerRep, none is defined in test_db
		$this->_createResidualParameter($randPct, $partnerRepUcpId, $pId);
		//Create User Parameters for PartnerRep, none is defined in test_db
		$this->_createUserParameter($randPct, $partnerRepUcpId, '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');

		foreach ($testScenarios as $key => $tstCaseData) {
			//add parameter values for associated users
			if (!empty($tstCaseData['assoc_users'])) {
				foreach ($tstCaseData['assoc_users'] as $caseKey => $assocUserId) {
					if ($caseKey === 'partner_id') {
						$this->_createResidualParameter($randPct, $partnerUcpId, $pId);
						//partner is associated to itself in user parameters (3dr function param)
						$this->_createUserParameter($randPct, $partnerUcpId, $partnerUserId, $pId);
					} elseif ($caseKey === 'sm_user_id' || $caseKey === 'sm2_user_id') {
						//managers are associated to the rep UCP
						$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId, $assocUserId);
						$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $assocUserId, $pId);
					} elseif ($caseKey === 'referer_id') {
						$this->_createResidualParameter($randPct, $refUcpId, $pId);
						$this->_createUserParameter($randPct, $refUcpId, self::REF_ID, $pId);
					} elseif ($caseKey === 'reseller_id') {
						$this->_createResidualParameter($randPct, $resUcpId, $pId);
						$this->_createUserParameter($randPct, $resUcpId, self::RES_ID, $pId);
					}
				}
			}
			copy($file, $datafile);

			$expected = [
				'job_name' => 'Residual Report Admin',
				'result' => true,
				'recordsAdded' => true,
				'start_time' => '',
				'end_time' => '',
				'log' => [
					'products' => [$prodNameKey],
					'errors' => [],
					'optional_msgs' => ['Selected month/year: 01/2016'],
				]
			];
			$Merchant->save(
				[
					'Merchant' => [
						'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
						'partner_id' => Hash::get($tstCaseData['assoc_users'], 'partner_id'),
						'sm_user_id' => Hash::get($tstCaseData['assoc_users'], 'sm_user_id'),
						'sm2_user_id' => Hash::get($tstCaseData['assoc_users'], 'sm2_user_id'),
						'referer_id' => Hash::get($tstCaseData['assoc_users'], 'referer_id'),
						'reseller_id' => Hash::get($tstCaseData['assoc_users'], 'reseller_id'),
					]
				], ['validate' => false]
			);

			$this->__saveTestArchivedData($tstCaseData['arhive_name'], $pId, Hash::extract($tstCaseData, 'modifiers'));
			$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
			$expected['start_time'] = $response['start_time'];
			$expected['end_time'] = $response['end_time'];
			$this->assertEquals($expected, $response);

			// check expected residual report values
			$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

			//One record per add() method execution for month/year/product
			$response = $response[0]['ResidualReport'];
			//merchant discount item fee is not stored by itself in residual report only in mp archive
			$this->assertEquals(1, $response['r_month']);
			$this->assertEquals(2016, $response['r_year']);
			$this->assertEquals(120, $response['r_avg_ticket']);
			$this->assertEquals(250, $response['r_items']);
			$this->assertEquals(30000, $response['r_volume']);
			$this->assertEquals(0.21, $response['m_rate_pct']);
			$this->assertEquals(.15, $response['m_per_item_fee']);
			$this->assertEquals(25, $response['m_statement_fee']);
			//set merchant amounts for calculations
			$amounts['volume'] = $response['r_volume'];
			$amounts['num_items'] = $response['r_items'];
			$amounts['m_rate'] = ($response['m_rate_pct']);
			$amounts['m_item_fee'] = $response['m_per_item_fee'];
			if (!empty(Hash::get($tstCaseData['assoc_users'], 'partner_id'))) {
				$amounts['user_rate'] = $response['partner_rate'];
				$amounts['user_risk_pct'] = Hash::get($response, 'partner_risk_pct');
				$amounts['user_pi'] = $response['partner_per_item_fee'];

				$expected = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['partner_statement_fee']);

				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_gross_profit'], $expected, .5));
				$this->assertEquals(2.99, $response['partner_rate']);
				$this->assertEquals(.6, $response['partner_per_item_fee']);
				$this->assertEquals(16, $response['partner_statement_fee']);
				$this->assertEquals($randPct, $response['partner_profit_pct']);
				$expected = $response['partner_gross_profit'] * ($response['partner_pct_of_gross'] / 100) * ($response['partner_profit_pct'] / 100);

				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_profit_amount'], $expected, .5));
				$this->assertEquals($randPct, $response['partner_pct_of_gross']);

				$amounts['user_rate'] = $response['partner_rep_rate'];
				$amounts['user_risk_pct'] = Hash::get($response, 'partner_rep_risk_pct');
				$amounts['user_pi'] = $response['partner_rep_per_item_fee'];

				$expected = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['partner_rep_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_gross_profit'], $expected, .5));

				$this->assertEquals(1.99, $response['partner_rep_rate']);
				$this->assertEquals(.5, $response['partner_rep_per_item_fee']);
				$this->assertEquals(15, $response['partner_rep_statement_fee']);
				$this->assertEquals($randPct, $response['partner_rep_profit_pct']);
				$this->assertEquals($randPct, $response['partner_rep_pct_of_gross']);
				$expected = ($response['partner_rep_gross_profit'] * ($response['partner_rep_pct_of_gross'] / 100) * ($response['partner_rep_profit_pct'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_profit_amount'], $expected, .5));
			} else {
				$amounts['user_rate'] = $response['r_rate_pct'];
				$amounts['user_risk_pct'] = Hash::get($response, 'r_risk_pct');
				$amounts['user_pi'] = $response['r_per_item_fee'];

				$expected = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['r_statement_fee']);
				$this->assertEquals(1.49, $response['r_rate_pct']);
				$this->assertEquals('0.1800', $response['r_per_item_fee']);
				$this->assertEquals('4.7500', $response['r_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['rep_gross_profit'], $expected, .5));

				$this->assertEquals($randPct, $response['r_profit_pct']);
				$this->assertEquals($randPct, $response['rep_pct_of_gross']);

				//Calculation for r_profit_amount applies to all products in this test
				//ref and res profit amounts should be subtracted for this test case.
				$expected = ($response['rep_gross_profit'] * ($response['rep_pct_of_gross'] / 100) * ($response['r_profit_pct'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .5));
			}

			if (!empty(Hash::get($tstCaseData['assoc_users'], 'referer_id'))) {
				$amounts['user_rate'] = $response['referrer_rate'];
				$amounts['user_risk_pct'] = Hash::get($response, 'referrer_risk_pct');
				$amounts['user_pi'] = $response['referrer_per_item_fee'];

				$referrerGrossProfit = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['referrer_statement_fee']);
				$amounts['user_rate'] = $response['reseller_rate'];
				$amounts['user_risk_pct'] = Hash::get($response, 'reseller_risk_pct');
				$amounts['user_pi'] = $response['reseller_per_item_fee'];

				$resellerGrossProfit = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['reseller_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['referrer_gross_profit'], $referrerGrossProfit, .5));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['reseller_gross_profit'], $resellerGrossProfit, .5));
				$this->assertEquals($randPct, $response['refer_profit_pct']);
				$this->assertEquals($tstCaseData['modifiers']['ref_p_pct'], $response['referer_pct_of_gross']);
				$this->assertEquals($randPct, $response['res_profit_pct']);
				$this->assertEquals($tstCaseData['modifiers']['res_p_pct'], $response['reseller_pct_of_gross']);

				//for this test ref_p_type = res_p_type = 'points' for profit amount wich uses this formula
				$expected = ($response['referrer_gross_profit'] * ($response['referer_pct_of_gross'] / 100) * ($response['refer_profit_pct'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['refer_profit_amount'], $expected, .5));
				$expected = ($response['reseller_gross_profit'] * ($response['reseller_pct_of_gross'] / 100) * ($response['res_profit_pct'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['res_profit_amount'], $expected, .5));

				$this->assertEquals(2, $response['referrer_rate']);
				$this->assertEquals(.5, $response['referrer_per_item_fee']);
				$this->assertEquals(7, $response['referrer_statement_fee']);

				$this->assertEquals(3, $response['reseller_rate']);
				$this->assertEquals(.6, $response['reseller_per_item_fee']);
				$this->assertEquals(8, $response['reseller_statement_fee']);
			}

			if (!empty(Hash::get($tstCaseData['assoc_users'], 'sm_user_id'))) {
				$amounts['user_rate'] = $response['manager_rate'];
				$amounts['user_risk_pct'] = Hash::get($response, 'manager_risk_pct');
				$amounts['user_pi'] = $response['manager_per_item_fee'];

				$expectedSmGp = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['manager_statement_fee']);

				$amounts['user_rate'] = $response['manager2_rate'];
				$amounts['user_risk_pct'] = Hash::get($response, 'manager2_risk_pct');
				$amounts['user_pi'] = $response['manager2_per_item_fee'];

				$expectedSm2Gp = $AxiaCalculate->uGrossPrft($amounts) + ($response['m_statement_fee'] - $response['manager2_statement_fee']);

				$this->assertEquals(2, $response['manager_rate']);
				$this->assertEquals(.5, $response['manager_per_item_fee']);
				$this->assertEquals(7, $response['manager_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_gross_profit'], $expectedSmGp, .5));

				$this->assertEquals($randPct, $response['manager_profit_pct']);
				$this->assertEquals($randPct, $response['manager_pct_of_gross']);
				//ref and res profit amounts should not be subtracted for this test case.
				$expected = ($response['manager_gross_profit'] * ($response['manager_pct_of_gross'] / 100) * ($response['manager_profit_pct'] / 100));
				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount'], $expected, .5));

				$this->assertEquals(3, $response['manager2_rate']);
				$this->assertEquals(.6, $response['manager2_per_item_fee']);
				$this->assertEquals(8, $response['manager2_statement_fee']);
				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager2_gross_profit'], $expectedSm2Gp, .5));
				$this->assertEquals($randPct, $response['manager_profit_pct_secondary']);
				$this->assertEquals($randPct, $response['manager2_pct_of_gross']);
				//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
				$expected = ($response['manager2_gross_profit'] * ($response['manager2_pct_of_gross'] / 100) * ($response['manager_profit_pct_secondary'] / 100));

				$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount_secondary'], $expected, .5));
			}

			$expected = $response['r_profit_amount'] + $response['partner_rep_profit_amount'];
			$this->assertTrue($this->_isAcceptableErrorPercent($response['total_profit'], $expected, 1));
			$this->assertEquals(null, $response['merchant_state']);

			//Delete data before next test scenario
			$this->ResidualReport->deleteAll(['r_year' => $year, 'r_month' => $month]);
		}
	}

/**
 * testAddSecondCommonProducts
 * This test checks that the calculations and formulas evaluated during the add() procedure are always correct
 * The subset of products being tested here ($this->commonProducts2) all use the same formulas and logic.
 *
 * @return void
 */
	public function testAddSecondCommonProducts() {
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$Merchant = ClassRegistry::init('Merchant');
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';
		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';
		$partnerRepUcpId = '8bbe2d12-2975-4466-a309-fcf4e721d468';
		$partnerUcpId = 'a8c5607c-cf2e-4b5b-a2f9-fda5a95a3950';
		$partnerUserId = '19b2c1da-943e-4761-829a-4ca54008dd58';
		$testScenarios = [
			'rep' => [
				'arhive_name' => 'archiveRepOnly',
				'assoc_users' => []
			],
			'rep_and_mgrs' => [
				'arhive_name' => 'archiveWithManagers',
				'assoc_users' => [
					'sm_user_id' => self::SM_ID,
					'sm2_user_id' => self::SM2_ID
				]
			],
			'rep_ref_res' => [
				'arhive_name' => 'archiveWithRefRes',
				'assoc_users' => [
					'referer_id' => self::REF_ID,
					'reseller_id' => self::RES_ID
				],
				'modifiers' => [
					//none for this test
				]
			],
			'rep_partner' => [
				'arhive_name' => 'archiveRepPartner',
				'assoc_users' => [
					//partner id
					'partner_id' => $partnerUserId
				]
			]
		];
		// Payment fusion fees and costs
		$userPfStdDeviceCost = 5;
		$merchPfStdDevices = 1;
		$merchPfStdDeviceFee = 29;
		$merchPfAcctFee = 10;
		$expectedPfStdDeviceCalc = $merchPfStdDevices * ($merchPfStdDeviceFee - $userPfStdDeviceCost);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		//Save payment fusion data
		$Merchant->PaymentFusion->save([
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'generic_product_mid' => '3948000030003045',
					'account_fee' => $merchPfAcctFee,
					'rate' => .5,
					'monthly_total' => 125,
					'per_item_fee' => .30,
					'standard_num_devices' => $merchPfStdDevices,
					'standard_device_fee' => $merchPfStdDeviceFee
				]);

		//create comp profiles for all users except partner which already has one in test_db
		$smUcpId = $this->_createUCP(self::SM_ID, null, '5536d1cf-47b0-401c-b52b-103134627ad4');
		$sm2UcpId = $this->_createUCP(self::SM2_ID, null, '5536d1cf-c50c-47d7-a03b-103134627ad4');
		$refUcpId = $this->_createUCP(self::REF_ID);
		$resUcpId = $this->_createUCP(self::RES_ID);
		$ucpIdsArr = [$partnerRepUcpId, $partnerUcpId, $smUcpId, $sm2UcpId, $refUcpId, $resUcpId];

		//create payment fusion rep costs
		foreach ($ucpIdsArr as $userCompId) {
			$pfRepCosts[] = [
				'user_compensation_profile_id' => $userCompId,
				'standard_device_cost' => $userPfStdDeviceCost
			];
		}
		ClassRegistry::init('PaymentFusionRepCost')->saveMany($pfRepCosts);

		$currentTestData = Hash::extract($testScenarios, '{s}');
		$scCount = count($currentTestData);
		for ($x = 0; $x < $scCount; $x++) {
			foreach ($this->commonProducts2 as $prodNameKey => $pId) {
				$product = [
					'ProductsServicesType' => [
						'id' => $pId,
						'products_services_description' => $prodNameKey
					]
				];
				//Add product to merchant if doesn't have it
				if (!$ProductsAndService->hasAny(['merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'products_services_type_id' => $pId])) {
					$ProductsAndService->save([
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'products_services_type_id' => $pId,
					]);
				}
				//create a pseudo-random profit percent amount
				$randPct = rand(0, 100);

				//Add user's residual parameter value
				$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId);
				//Add user's user parameter value
				$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

				//Create Residual Parameters for PartnerRep, none is defined in test_db
				$this->_createResidualParameter($randPct, $partnerRepUcpId, $pId);
				//Create User Parameters for PartnerRep, none is defined in test_db
				$this->_createUserParameter($randPct, $partnerRepUcpId, '32165df7-6b97-4f86-9e6f-8638eb30cd9e', $pId);

				//add parameter values for associated users
				if (!empty($currentTestData[$x]['assoc_users'])) {
					$assocUsers = $currentTestData[$x]['assoc_users'];
					foreach ($assocUsers as $caseKey => $assocUserId) {
						if ($caseKey === 'partner_id') {
							$this->_createResidualParameter($randPct, $partnerUcpId, $pId);
							//partner is associated to itself in user parameters (3dr function param)
							$this->_createUserParameter($randPct, $partnerUcpId, $partnerUserId, $pId);
						} elseif ($caseKey === 'sm_user_id' || $caseKey === 'sm2_user_id') {
							//managers are associated to the rep UCP
							$this->_createResidualParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $pId, $assocUserId);
							$this->_createUserParameter($randPct, '6570e7dc-a4fc-444c-8929-337a34627ad4', $assocUserId, $pId);
						} elseif ($caseKey === 'referer_id') {
							$this->_createResidualParameter($randPct, $refUcpId, $pId);
							$this->_createUserParameter($randPct, $refUcpId, self::REF_ID, $pId);
						} elseif ($caseKey === 'reseller_id') {
							$this->_createResidualParameter($randPct, $resUcpId, $pId);
							$this->_createUserParameter($randPct, $resUcpId, self::RES_ID, $pId);
						}
					}
				}
				copy($file, $datafile);

				$expected = [
					'job_name' => 'Residual Report Admin',
					'result' => true,
					'recordsAdded' => true,
					'start_time' => '',
					'end_time' => '',
					'log' => [
						'products' => [$prodNameKey],
						'errors' => [],
						'optional_msgs' => ['Selected month/year: 01/2016'],
					]
				];

				$Merchant->save(
					[
						'Merchant' => [
							'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
							'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
							'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
							'partner_id' => Hash::get($testScenarios, 'rep_partner.assoc_users.partner_id'),
							'sm_user_id' => Hash::get($testScenarios, 'rep_and_mgrs.assoc_users.sm_user_id'),
							'sm2_user_id' => Hash::get($testScenarios, 'rep_and_mgrs.assoc_users.sm2_user_id'),
							'referer_id' => Hash::get($testScenarios, 'rep_ref_res.assoc_users.referer_id'),
							'reseller_id' => Hash::get($testScenarios, 'rep_ref_res.assoc_users.reseller_id'),
						]
					], ['validate' => false]
				);

				$this->__saveTestArchivedData($currentTestData[$x]['arhive_name'], $pId, Hash::extract($currentTestData[$x], 'modifiers'));
				$response = $this->ResidualReport->add($year, $month, $product, $datafile, $email);
				$expected['start_time'] = $response['start_time'];
				$expected['end_time'] = $response['end_time'];

				$this->assertEquals($expected, $response);

				// check expected residual report values
				$response = $this->ResidualReport->find('all', ['conditions' => ['r_year' => $year, 'r_month' => $month, 'products_services_type_id' => $product['ProductsServicesType']['id']]]);

				//One record per add() method execution for month/year/product
				$response = $response[0]['ResidualReport'];
				//merchant discount item fee is not stored by itself in residual report only in mp archive
				$this->assertEquals(1, $response['r_month']);
				$this->assertEquals(2016, $response['r_year']);
				$this->assertEquals(120, $response['r_avg_ticket']);
				$this->assertEquals(250, $response['r_items']);
				$this->assertEquals(30000, $response['r_volume']);
				$this->assertEquals(0.21, $response['m_rate_pct']);
				$this->assertEquals(.15, $response['m_per_item_fee']);
				$this->assertEquals(25, $response['m_statement_fee']);
				$isCorral = ($prodNameKey == 'Corral License Fee');
				$isPayFusion = ($prodNameKey == 'Payment Fusion');
				if (!empty(Hash::get($currentTestData[$x]['assoc_users'], 'partner_id'))) {
					if ($isCorral) {
						$expected = $response['m_statement_fee'];
					} elseif ($isPayFusion) {
						$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['partner_per_item_fee'])) + $merchPfAcctFee + $expectedPfStdDeviceCalc;
					} else {
						$expected = $response['r_items'] * ($response['m_per_item_fee'] - $response['partner_per_item_fee']);
					}

					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_gross_profit'], $expected, .5));
					$this->assertEquals(2.99, $response['partner_rate']);
					$this->assertEquals(.6, $response['partner_per_item_fee']);
					$this->assertEquals(16, $response['partner_statement_fee']);
					$this->assertEquals($randPct, $response['partner_profit_pct']);
					$expected = $response['partner_gross_profit'] * ($response['partner_pct_of_gross'] / 100) * ($response['partner_profit_pct'] / 100);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_profit_amount'], $expected, .5));
					$this->assertEquals($randPct, $response['partner_pct_of_gross']);

					if ($isCorral) {
						$expected = $response['m_statement_fee'];
					} elseif ($isPayFusion) {
						$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['partner_rep_per_item_fee'])) + $merchPfAcctFee + $expectedPfStdDeviceCalc;
					} else {
						$expected = $response['r_items'] * ($response['m_per_item_fee'] - $response['partner_rep_per_item_fee']);
					}
					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_gross_profit'], $expected, .5));
					$this->assertEquals(1.99, $response['partner_rep_rate']);
					$this->assertEquals(.5, $response['partner_rep_per_item_fee']);
					$this->assertEquals(15, $response['partner_rep_statement_fee']);
					$this->assertEquals($randPct, $response['partner_rep_profit_pct']);
					$this->assertEquals($randPct, $response['partner_rep_pct_of_gross']);
					if ($isPayFusion) {
						$expected = $response['partner_rep_gross_profit'] * $response['partner_rep_pct_of_gross'] / 100 * ($response['partner_rep_profit_pct'] / 100);
					} else {
						$expected = ($response['partner_rep_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['partner_rep_pct_of_gross'] / 100 * ($response['partner_rep_profit_pct'] / 100);
					}

					$this->assertTrue($this->_isAcceptableErrorPercent($response['partner_rep_profit_amount'], $expected, .9));
				} else {
					if ($isCorral) {
						$expected = $response['m_statement_fee'];
					} elseif ($isPayFusion) {
						$expected = ($response['r_items'] * ($response['m_per_item_fee'] - $response['r_per_item_fee'])) + ($response['m_statement_fee'] - $response['r_statement_fee']);
					} else {
						$expected = $response['r_items'] * ($response['m_per_item_fee'] - $response['r_per_item_fee']);
					}

					$this->assertEquals(0, $response['r_rate_pct']);
					$this->assertEquals(0, $response['r_per_item_fee']);
					$this->assertEquals(0, $response['r_statement_fee']);
					$this->assertEquals(0, $response['r_profit_pct']);
					$this->assertEquals(0, $response['rep_pct_of_gross']);

					//Calculation for r_profit_amount applies to all products in this test
					//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
					$expected = ($response['rep_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['rep_pct_of_gross'] / 100 * ($response['r_profit_pct'] / 100);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['r_profit_amount'], CakeNumber::precision($expected, 4), .9));
				}

				if (!empty(Hash::get($currentTestData[$x]['assoc_users'], 'referer_id'))) {
					if ($isCorral) {
						$refExpectedGp = $response['m_statement_fee'];
						$resExpectedGp = $response['m_statement_fee'];
					} elseif ($isPayFusion) {
						$refExpectedGp = ($response['r_items'] * ($response['m_per_item_fee'] - $response['referrer_per_item_fee'])) + $merchPfAcctFee + $expectedPfStdDeviceCalc;
						$resExpectedGp = ($response['r_items'] * ($response['m_per_item_fee'] - $response['reseller_per_item_fee'])) + $merchPfAcctFee + $expectedPfStdDeviceCalc;
					} else {
						$refExpectedGp = $response['r_items'] * ($response['m_per_item_fee'] - $response['referrer_per_item_fee']);
						$resExpectedGp = $response['r_items'] * ($response['m_per_item_fee'] - $response['reseller_per_item_fee']);
					}
					$this->assertTrue($this->_isAcceptableErrorPercent($response['referrer_gross_profit'], $refExpectedGp, .5));
					$this->assertTrue($this->_isAcceptableErrorPercent($response['reseller_gross_profit'], $resExpectedGp, .5));
					$this->assertEquals($randPct, $response['refer_profit_pct']);
					$this->assertEquals($randPct, $response['referer_pct_of_gross']);
					$this->assertEquals($randPct, $response['res_profit_pct']);
					$this->assertEquals($randPct, $response['reseller_pct_of_gross']);

					//for this test ref_p_type = res_p_type = 'percentage' for profit amount wich uses this formula
					$expected = ($response['referrer_gross_profit'] * ($response['referer_pct_of_gross'] / 100) * ($response['refer_profit_pct'] / 100));
					$this->assertTrue($this->_isAcceptableErrorPercent($response['refer_profit_amount'], $expected, .9));
					$expected = ($response['reseller_gross_profit'] * ($response['reseller_pct_of_gross'] / 100) * ($response['res_profit_pct'] / 100));
					$this->assertTrue($this->_isAcceptableErrorPercent($response['res_profit_amount'], $expected, .5));

					$this->assertEquals(2, $response['referrer_rate']);
					$this->assertEquals(.5, $response['referrer_per_item_fee']);
					$this->assertEquals(7, $response['referrer_statement_fee']);

					$this->assertEquals(3, $response['reseller_rate']);
					$this->assertEquals(.6, $response['reseller_per_item_fee']);
					$this->assertEquals(8, $response['reseller_statement_fee']);
				}

				if (!empty(Hash::get($currentTestData[$x]['assoc_users'], 'sm_user_id'))) {
					if ($isCorral) {
						$expectedSmGp = $response['m_statement_fee'];
						$expectedSm2Gp = $response['m_statement_fee'];
					} elseif ($isPayFusion) {
						$expectedSmGp = ($response['r_items'] * ($response['m_per_item_fee'] - $response['manager_per_item_fee'])) + $merchPfAcctFee + $expectedPfStdDeviceCalc;
						$expectedSm2Gp = ($response['r_items'] * ($response['m_per_item_fee'] - $response['manager2_per_item_fee'])) + $merchPfAcctFee + $expectedPfStdDeviceCalc;
					} else {
						$expectedSmGp = $response['r_items'] * ($response['m_per_item_fee'] - $response['manager_per_item_fee']);
						$expectedSm2Gp = $response['r_items'] * ($response['m_per_item_fee'] - $response['manager2_per_item_fee']);
					}
					$this->assertEquals(2, $response['manager_rate']);
					$this->assertEquals(.5, $response['manager_per_item_fee']);
					$this->assertEquals(7, $response['manager_statement_fee']);

					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_gross_profit'], $expectedSmGp, .5));
					$this->assertEquals(0, $response['manager_profit_pct']);
					$this->assertEquals(0, $response['manager_pct_of_gross']);
					//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
					$expected = ($response['manager_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['manager_pct_of_gross'] / 100 * ($response['manager_profit_pct'] / 100);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount'], $expected, .9));

					$this->assertEquals(3, $response['manager2_rate']);
					$this->assertEquals(.6, $response['manager2_per_item_fee']);
					$this->assertEquals(8, $response['manager2_statement_fee']);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager2_gross_profit'], $expectedSm2Gp, .5));
					$this->assertEquals(0, $response['manager_profit_pct_secondary']);
					$this->assertEquals(0, $response['manager2_pct_of_gross']);
					//ref and res profit amounts should not be subtracted for this test case. The logic for this is already in place in the ResidualReport model
					$expected = ($response['manager2_gross_profit'] - $response['partner_profit_amount'] - ($response['refer_profit_amount'] * 0) - ($response['res_profit_amount'] * 0)) * $response['manager2_pct_of_gross'] / 100 * ($response['manager_profit_pct_secondary'] / 100);
					$this->assertTrue($this->_isAcceptableErrorPercent($response['manager_profit_amount_secondary'], $expected, .5));
				}
				$this->assertEquals(null, $response['merchant_state']);
			}
			//Delete data before next test scenario
			$this->ResidualReport->deleteAll(['r_year' => $year, 'r_month' => $month]);
		}
	}

/**
 * __saveTestArchivedData
 *
 * @param string $datasetName the name of the test dataset to save
 * @param string $productId a product id
 * @param array $valueModifiers array with values to set with the test data. Array keys must match the keys of the archive array elements that are being modified
 * @return void
 */
	private function __saveTestArchivedData($datasetName, $productId = '', $valueModifiers = []) {
		$data = [
			//Dataset commonProducts without a partner user involved
			'archiveRepOnly' => [
				'MerchantPricingArchive' => [
					'id' => '59c44fd4-d260-4896-ac5f-371534627ad4',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'acquirer_id' => '71646a03-93e9-4275-9f15-5f79b4a64179',
					'provider_id' => null,
					'products_services_type_id' => $productId,
					'month' => 1,
					'year' => 2016,
					'm_rate_pct' => 0.21,
					'm_per_item_fee' => .15,
					'm_discount_item_fee' => 55,
					'm_statement_fee' => 25,
					'bet_table_id' => '59b87c49-5c84-4cc4-8df8-1c7f34627ad4',
					'months_processing' => 22,
					'interchange_expense' => null,
					'product_profit' => null,
					'gateway_mid' => '3948000030003045',
					'generic_product_mid' => '3948000030003045'
				],
				'UserCostsArchive' => [
					[
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'cost_pct' => 1.49,
						'per_item_cost' => .18,
						'monthly_statement_cost' => 4.75,
						'is_hidden' => false
					]
				]
			],
			//Dataset archiveWithManagers without a partner user involved
			'archiveWithManagers' => [
				'MerchantPricingArchive' => [
					'id' => '59c44fd4-d260-4896-ac5f-371534627ad4',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'acquirer_id' => '71646a03-93e9-4275-9f15-5f79b4a64179',
					'provider_id' => null,
					'products_services_type_id' => $productId,
					'month' => 1,
					'year' => 2016,
					'm_rate_pct' => 0.21,
					'm_per_item_fee' => .15,
					'm_discount_item_fee' => 55,
					'm_statement_fee' => 25,
					'bet_table_id' => '59b87c49-5c84-4cc4-8df8-1c7f34627ad4',
					'months_processing' => 22,
					'interchange_expense' => null,
					'product_profit' => null,
					'gateway_mid' => '3948000030003045',
					'generic_product_mid' => '3948000030003045'
				],
				'UserCostsArchive' => [
					[
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'cost_pct' => 1.49,
						'per_item_cost' => .18,
						'monthly_statement_cost' => 4.75,
						'is_hidden' => false
					],
					[	//Manager1
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => self::SM_ID,
						'cost_pct' => 2,
						'per_item_cost' => .5,
						'monthly_statement_cost' => 7,
						'is_hidden' => false
					],
					[
						//Manager2
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => self::SM2_ID,
						'cost_pct' => 3,
						'per_item_cost' => .6,
						'monthly_statement_cost' => 8,
						'is_hidden' => false
					],
				]
			],
			//Dataset archiveWithRefRes without a partner user involved
			'archiveWithRefRes' => [
				'MerchantPricingArchive' => [
					'id' => '59c44fd4-d260-4896-ac5f-371534627ad4',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'acquirer_id' => '71646a03-93e9-4275-9f15-5f79b4a64179',
					'provider_id' => null,
					'products_services_type_id' => $productId,
					'month' => 1,
					'year' => 2016,
					'm_rate_pct' => 0.21,
					'm_per_item_fee' => .15,
					'm_discount_item_fee' => 55,
					'm_statement_fee' => 25,
					'bet_table_id' => '59b87c49-5c84-4cc4-8df8-1c7f34627ad4',
					'months_processing' => 22,
					'interchange_expense' => null,
					'product_profit' => null,
					'gateway_mid' => '3948000030003045',
					'generic_product_mid' => '3948000030003045'
				],
				'UserCostsArchive' => [
					[	//rep costs archive
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'cost_pct' => 1.49,
						'per_item_cost' => .18,
						'monthly_statement_cost' => 4.75,
						'is_hidden' => false
					],
					[	//Referrer costs archive
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => self::REF_ID,
						'cost_pct' => 2,
						'per_item_cost' => .5,
						'monthly_statement_cost' => 7,
						'is_hidden' => false,
						'ref_p_pct' => (int)Hash::get($valueModifiers, 'ref_p_pct'),
						'ref_p_type' => Hash::get($valueModifiers, 'ref_p_type'),
						'ref_p_value' => (int)Hash::get($valueModifiers, 'ref_p_value'),
					],
					[
						//Reseller costs archive
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => self::RES_ID,
						'cost_pct' => 3,
						'per_item_cost' => .6,
						'monthly_statement_cost' => 8,
						'is_hidden' => false,
						'res_p_pct' => (int)Hash::get($valueModifiers, 'res_p_pct'),
						'res_p_type' => Hash::get($valueModifiers, 'res_p_type'),
						'res_p_value' => (int)Hash::get($valueModifiers, 'res_p_value'),
					],
				]
			],
			//Dataset with a partner user involved
			'archiveRepPartner' => [
				'MerchantPricingArchive' => [
					'id' => '59c44fd4-d260-4896-ac5f-371534627ad4',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'acquirer_id' => '71646a03-93e9-4275-9f15-5f79b4a64179',
					'provider_id' => null,
					'products_services_type_id' => $productId, // Visa Sales
					'month' => 1,
					'year' => 2016,
					'm_rate_pct' => 0.21,
					'm_per_item_fee' => .15,
					'm_discount_item_fee' => 55,
					'm_statement_fee' => 25,
					'bet_table_id' => '59b87c49-5c84-4cc4-8df8-1c7f34627ad4',
					'months_processing' => 22,
					'interchange_expense' => null,
					'product_profit' => null,
					'gateway_mid' => '3948000030003045',
					'generic_product_mid' => '3948000030003045'
				],
				'UserCostsArchive' => [
					[
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
						'cost_pct' => 1.99,
						'per_item_cost' => .5,
						'monthly_statement_cost' => 15,
						'is_hidden' => false
					],
					[
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'user_id' => '19b2c1da-943e-4761-829a-4ca54008dd58',
						'cost_pct' => 2.99,
						'per_item_cost' => .6,
						'monthly_statement_cost' => 16,
						'is_hidden' => false
					],
				]
			]
		];

		$MerchantPricingArchive = ClassRegistry::init('MerchantPricingArchive');
		$MerchantPricingArchive->query('TRUNCATE merchant_pricing_archives CASCADE');
		$MerchantPricingArchive->saveAll($data[$datasetName], ['validate' => false, 'deep' => true]);
	}

/**
 * _createResidualParameter
 * Create Rep % and/or associated Mgr %
 *
 * @param float $value the test value to assign as the Rep % 
 * @param string $uCompId a user compensation profile id
 * @param string $prodId a product id
 * @param string $assocMgrUserId the user id such as a mgr mgr2 with which to associated the rep's UCP.
 * @return void
 */
	protected function _createResidualParameter($value, $uCompId, $prodId, $assocMgrUserId = '') {
		$ResidualParameter = ClassRegistry::init('ResidualParameter');
		$ResidualParameterType = ClassRegistry::init('ResidualParameterType');
		$conditions = ['user_compensation_profile_id' => $uCompId, 'products_services_type_id' => $prodId];

		if (!empty($assocMgrUserId)) {
			$paramType = $ResidualParameterType->field('id', ['name' => 'Mgr %']);
			$conditions['associated_user_id'] = $assocMgrUserId;
		} else {
			$paramType = $ResidualParameterType->field('id', ['name' => 'Rep %']);
		}
		$conditions['residual_parameter_type_id'] = $paramType;
		$rParamId = $ResidualParameter->field('id', $conditions);
		$residualParam = [
			'residual_parameter_type_id' => $paramType,
			'products_services_type_id' => $prodId,
			'associated_user_id' => ($assocMgrUserId)? : null,
			'value' => $value,
			'is_multiple' => '0',
			'tier' => '0',
			'user_compensation_profile_id' => $uCompId
		];
		if (!empty($rParamId)) {
			$residualParam['id'] = $rParamId;
		}

		$ResidualParameter->create();
		$ResidualParameter->save($residualParam);
		unset($ResidualParameter);
		unset($ResidualParameterType);
	}

/**
 * _createUCP
 * Create user % of Gross Profit
 *
 * @param string $userId the user associated with the rep i.e.: manager/manager 2
 * @param string $partnerId the id of a partner user
 * @param string $roleId a role id
 * @return string id of the UCP record created
 */
	protected function _createUCP($userId, $partnerId = '', $roleId = null) {
		$UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$data = [
			'user_id' => $userId,
			'partner_user_id' => $partnerId? : null,
			'is_partner_rep' => (bool)$partnerId,
			'is_default' => !(bool)$partnerId,
			'role_id' => $roleId,
			'is_profile_option_1' => 1,
		];
		$UserCompensationProfile->create();
		$UserCompensationProfile->save($data);
		return $UserCompensationProfile->id;
	}
/**
 * _createUserParameter
 * Create user % of Gross Profit
 *
 * @param float $value an arbitrary percentage to set as a test value
 * @param string $uCompId a user compensation profile id
 * @param string $userId the user associated with the rep i.e.: manager/manager 2
 * @param string $prodId a product id
 * @return void
 */
	protected function _createUserParameter($value, $uCompId, $userId, $prodId) {
		$UserParameter = ClassRegistry::init('UserParameter');
		$UserParameterType = ClassRegistry::init('UserParameterType');
		$paramType = $UserParameterType->field('id', ['name' => '% of Gross Profit']);
		$conditions = ['user_compensation_profile_id' => $uCompId, 'products_services_type_id' => $prodId, 'user_parameter_type_id' => $paramType, 'associated_user_id' => $userId];

		$userParamId = $UserParameter->field('id', $conditions);
		$userParam = [
			'user_parameter_type_id' => $paramType,
			'products_services_type_id' => $prodId,
			'associated_user_id' => $userId,
			'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
			'value' => $value,
			'is_multiple' => '0',
			'tier' => '0',
			'user_compensation_profile_id' => $uCompId
		];
		if (!empty($userParamId)) {
			$userParam['id'] = $userParamId;
		}
		$UserParameter->create();
		$UserParameter->save($userParam);
		unset($UserParameter);
	}

/**
 * testSendCompletionStatusEmail
 *
 * @return void
 */
	public function testSendCompletionStatusEmail() {
		// check that the event in sendCompletionStatusEmail() is working as expected
		$model = $this->ResidualReport;
		$dispatched = false;

		$model->getEventManager()->attach(
			function (CakeEvent $event) use ($model, &$dispatched) {
				$this->assertSame($model, $event->subject());
				$this->assertEquals(
					[
						'template' => 'bgJobEmailTemplate',
						'from' => [
							'webmaster@axiatech.com' => 'Axia Database Website'
						],
						'to' => 'test@example.com',
						'subject' => 'Residual Admin Notice',
						'emailBody' => [
							'result' => 1,
							'recordsAdded' => 1
						]
					],
					$event->data
				);
				$dispatched = true;
			},
			'App.Model.readyForEmail'
		);

		$response = [];
		$response['result'] = true;
		$response['recordsAdded'] = true;

		$this->ResidualReport->sendCompletionStatusEmail($response, 'test@example.com');
		$this->assertTrue($dispatched);
	}

/**
 * testFindIndex
 *
 * @covers ResidualReport::_findIndex
 * @return void
 */
	public function testFindIndex() {
		$year = '2016';
		$month = '01';
		$product = [
		'ProductsServicesType' => [
				'id' => 'e6d8f040-9963-4539-ab75-3e19f679de16',
				'products_services_description' => 'Visa Sales'
			]
		];
		$email = 'test@example.com';

		$file = APP . 'Test' . DS . 'Tmp' . DS . 'residual_report_test_data_visa_sales';
		$datafile = $file . '_tmp';

		copy($file, $datafile);
		$this->_saveAsApproved('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		$this->__saveTestArchivedData('archiveRepOnly', 'e6d8f040-9963-4539-ab75-3e19f679de16');
		$this->ResidualReport->add($year, $month, $product, $datafile, $email);
		$expected = array(
			'ResidualReport' => array(
				'r_year' => '2016.0000',
				'r_month' => '1.0000',
				'status' => 'I'
			),
			'ProductsServicesType' => array(
				'products_services_description' => 'Visa Sales'
			)
		);

		$response = $this->ResidualReport->find('index', ['conditions' => ['ResidualReport.r_year' => $year, 'ResidualReport.r_month' => $month]]);

		$test = [];
		foreach ($response as $r) {
			if ($r['ProductsServicesType']['products_services_description'] == 'Visa Sales') {
				$test = $r;
				break;
			}
		}

		$expected['ProductsServicesType']['id'] = $test['ProductsServicesType']['id'];

		$this->assertEquals($expected, $test);
	}

/**
 * testProcessResidualReportDataException
 *
 * @covers ResidualReport::_processResidualReportData
 * @expectedException InvalidArgumentException
 * @return void
 */
	public function testProcessResidualReportDataException() {
		$reflection = $this->__buildResidualReportReflection('_processResidualReportData');
		$result = $reflection->invokeArgs($this->ResidualReport, ['invalid', false]);
	}

/**
 * __buildResidualReportReflection
 *
 * @param string $name method name
 * @return Object
 */
	private function __buildResidualReportReflection($name) {
		$reflection = new ReflectionMethod('ResidualReport', $name);
		$reflection->setAccessible(true);
		return $reflection;
	}

/**
 * testDeleteManyInvalidArgumentException()
 * 
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Missing or invalid entry in conditions array parameter
 * @return void
 */
	public function testDeleteManyInvalidArgumentExceptionA() {
		$conditions = [
			'year' => 2020,
			'months_products' => 'this should be array',
		];
		$this->ResidualReport->deleteMany($conditions);
	}
/**
 * testDeleteManyInvalidArgumentExceptionB()
 * 
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Missing or invalid entry in conditions array parameter
 * @return void
 */
	public function testDeleteManyInvalidArgumentExceptionB() {
		$conditions = [
			'months_products' => [1 => ['UUID']],
		];
		$this->ResidualReport->deleteMany($conditions);
	}
/**
 * testDeleteMany()
 * 
 * @covers ResidualReport::deleteMany()
 * @return void
 */
	public function testDeleteMany() {
		//Generate 10 records
		for ($n = 1; $n <= 10; $n++) {
			$mId = $this->UUIDv4(); //fictitious merchant id
			$pId = $this->UUIDv4(); //fictitious product id
			$productIds[] = $pId;
			$id = $this->UUIDv4();
			$idsCollection[] = $id;
			$data = [
				'id' => $id,
				'merchant_id' => $mId,
				'user_id' => $this->UUIDv4(),
				'products_services_type_id' => $pId,
				'r_month' => 1,
				'r_year' => 2020
			];
			//save test data
			$this->ResidualReport->create();
			$this->ResidualReport->save($data);
		}

		$conditions = [
			'year' => 2020,
			'months_products' => [1 => $productIds],
		];

		$this->assertTrue($this->ResidualReport->hasAny(['id' => $idsCollection]));
		$this->assertSame(10, $this->ResidualReport->find('count', ['conditions' => ['id' => $idsCollection]]));
		$this->assertTrue($this->ResidualReport->deleteMany($conditions));
		$this->assertFalse($this->ResidualReport->hasAny(['id' => $idsCollection]));
	}
}
