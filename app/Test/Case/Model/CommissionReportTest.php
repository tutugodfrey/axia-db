<?php
App::uses('CommissionReport', 'Model');
App::uses('ResidualParameterType', 'Model');
App::uses('User', 'Model');
App::uses('ResidualReport', 'Model');
App::uses('SearchByUserIdBehavior', 'Model/Behavior');
App::uses('Adjustment', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('RolePermissions', 'Lib');

/**
 * CommissionReport Test Case
 *
 */
class CommissionReportTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

	protected function _getSimpleCommissionReportMock() {
		$userId = AxiaTestCase::USER_SM_ID;
		return $this->_mockModel('CommissionReport', ['_getCurrentUser'], [
				'_getCurrentUser' => ['value' => $userId],
			]);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CommissionReport);
		parent::tearDown();
	}

/**
 * testGetDefaultSearchValues
 *
 * @covers CommissionReport::getDefaultSearchValues
 * @test
 * @return void
 */
	public function testGetDefaultSearchValues() {
		// Current date fixed in the mocked model
		$CommissionReport = $this->_getComissionReportMock(AxiaTestCase::USER_SM_ID);
		$year = '2015';
		$month = '01';
		$expected = array(
			'from_date' => array(
					'year' => $year,
					'month' => $month,
			),
			'end_date' => array(
					'year' => $year,
					'month' => $month,
			),
			'user_id' => 'user_id:' . AxiaTestCase::USER_SM_ID,
		);
		$this->assertEquals($expected, $CommissionReport->getDefaultSearchValues());
	}

/**
 * testDateStartConditions
 *
 * @test
 * @covers SearchByMonthYearBehavior::dateStartConditions()
 * @return void
 */
	public function testDateStartConditions() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		$result = $CommissionReport->dateStartConditions(array(
			'from_date' => array(
				'year' => '1950',
				'month' => '04'
			)
		));
		$expected = "make_date(CommissionReport.c_year::integer, CommissionReport.c_month::integer, 1) >= '1950-04-01'";
		$this->assertEquals($expected, $result);
	}

/**
 * testDateEndConditions
 *
 * @test
 * @covers SearchByMonthYearBehavior::dateEndConditions()
 * @return void
 */
	public function testDateEndConditions() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		$result = $CommissionReport->dateEndConditions(array(
			'end_date' => array(
				'year' => '1999',
				'month' => '04'
			)
		));
		$expected = "make_date(CommissionReport.c_year::integer, CommissionReport.c_month::integer, 28) <= '1999-04-30'";
		$this->assertEquals($expected, $result);
	}

/**
 * testBuild
 *
 * @return void
 */
	public function testBuild() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		// records won't exist for this date
		$year = '2016';
		$month = '01';
		$email = 'test@example.com';

		$expected = array(
			'job_name' => 'Commission Report Admin',
			'result' => true,
			'recordsAdded' => true,
			'start_time' => '',
			'end_time' => '',
			'log' => [
				'products' => [],
				'errors' => [],
				'optional_msgs' => ['Selected month/year: 01/2016'],
			]
		);
		$response = $CommissionReport->build($year, $month, $email);

		$expected['start_time'] = $response['start_time'];
		$expected['end_time'] = $response['end_time'];

		$this->assertEquals($expected, $response);

		// records exist for this date
		$year = '2015';
		$month = '12';

		$expected = array(
			'job_name' => 'Commission Report Admin',
			'result' => true,
			'recordsAdded' => true,
			'start_time' => '',
			'end_time' => '',
			'log' => [
				'products' => [],
				'errors' => [],
				'optional_msgs' => ['Selected month/year: 12/2015'],
			]
		);

		$response = $CommissionReport->build($year, $month, $email);
		$expected['start_time'] = $response['start_time'];
		$expected['end_time'] = $response['end_time'];
		$this->assertEquals($expected, $response);

		// check expected commission values
		$results = $CommissionReport->find('all', [
			'conditions' => [
				'CommissionReport.c_month' => 12,
				'CommissionReport.c_year' => 2015,
			]
		]);

		$this->assertEquals(-316.88, $results[0]['CommissionReport']['c_business']);
		$this->assertEquals(40, $results[0]['CommissionReport']['tax_amount']);
		$this->assertEquals(16.88, $results[1]['CommissionReport']['c_shipping']);
		$this->assertEquals(40, $results[1]['CommissionReport']['tax_amount']);
		$this->assertEquals(516.88, $results[3]['CommissionReport']['c_business']);
	}

/**
 * testBuildCommissionPricing
 * Check that commission_pricing data is accurately saved during the CommissionReport->build() process
 *
 * @return void
 */
	public function testBuildCommissionPricing() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		$CommissionPricing = ClassRegistry::init('CommissionPricing');
		$AxiaCalculate = ClassRegistry::init('AxiaCalculate');

		$year = '2016';
		$month = '01';
		$email = 'test@example.com';

		$expected = array(
			'job_name' => 'Commission Report Admin',
			'result' => true,
			'recordsAdded' => true,
			'start_time' => '',
			'end_time' => '',
			'log' => [
				'products' => [],
				'errors' => [],
				'optional_msgs' => ['Selected month/year: 01/2016'],
			]
		);
		//Save test data
		$this->__saveTestDataForBuild();
		$response = $CommissionReport->build($year, $month, $email);
		$expected['start_time'] = $response['start_time'];
		$expected['end_time'] = $response['end_time'];
		$this->assertEquals($expected, $response);

		$response = $CommissionReport->build($year, $month, $email);
		$expected['start_time'] = $response['start_time'];
		$expected['end_time'] = $response['end_time'];
		$this->assertEquals($expected, $response);

		// check expected commission pricing values
		$results = $CommissionPricing->find('all', [
			'conditions' => [
				'CommissionPricing.merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'CommissionPricing.c_month' => 1,
				'CommissionPricing.c_year' => 5000,
			]
		]);

		foreach ($results as $result) {
			//check known test data
			$this->assertEquals(5, $result['CommissionPricing']['multiple']);
			$this->assertEquals(60, $result['CommissionPricing']['manager_multiple']);
			$this->assertEquals(0.1, $result['CommissionPricing']['r_per_item_fee']);
			$this->assertEquals(500, $result['CommissionPricing']['m_avg_ticket']);
			$this->assertEquals(1000, $result['CommissionPricing']['m_monthly_volume']);
			$this->assertEquals(2, $result['CommissionPricing']['num_items']);
			$this->assertEquals(5, $result['CommissionPricing']['rep_product_profit_pct']);
			$expected = $result['CommissionPricing']['num_items'] * ($result['CommissionPricing']['m_per_item_fee'] - $result['CommissionPricing']['r_per_item_fee']);
			$this->assertEquals($expected, $result['CommissionPricing']['rep_gross_profit']);
			$expected = $result['CommissionPricing']['num_items'] * ($result['CommissionPricing']['m_per_item_fee'] - $result['CommissionPricing']['manager_per_item_fee']);
			$this->assertEquals($expected, $result['CommissionPricing']['manager_gross_profit']);
			$this->assertEquals(50, $result['CommissionPricing']['rep_pct_of_gross']);
			$this->assertEquals(20, $result['CommissionPricing']['manager_pct_of_gross']);
			$expected = $AxiaCalculate->multipleAmnt([
				"rep_gross_prft" => $result['CommissionPricing']['rep_gross_profit'],
				"p_profit_amount" => 0.00,
				"ref_profit_amount" => 0.00,
				"res_profit_amount" => 0.00,
				"rep_pct_of_gross" => $result['CommissionPricing']['rep_pct_of_gross'],
				"multiple" => $result['CommissionPricing']['multiple']
			]);
			$this->assertEquals($expected, $result['CommissionPricing']['multiple_amount']);

			$expected = $AxiaCalculate->multipleAmnt([
				"rep_gross_prft" => $result['CommissionPricing']['manager_gross_profit'],
				"p_profit_amount" => 0.00,
				"ref_profit_amount" => 0.00,
				"res_profit_amount" => 0.00,
				"rep_pct_of_gross" => $result['CommissionPricing']['manager_pct_of_gross'],
				"multiple" => $result['CommissionPricing']['manager_multiple']
			]);
			$this->assertEquals($expected, $result['CommissionPricing']['manager_multiple_amount']);
		}
	}

/**
 * __saveTestDataForBuild
 *
 * @return void
 */
	private function __saveTestDataForBuild() {
		$Merchant = ClassRegistry::init('Merchant');
		$UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$MerchantUwVolume = ClassRegistry::init('MerchantUwVolume');
		$productIds[0] = 'e6d8f040-9963-4539-ab75-3e19f679de16'; //Visa sales
		$productIds[1] = '72a445f3-3937-4078-8631-1f569d6a30ed'; //Debit sales
		$mAcquirerIds[0] = '71646a03-93e9-4275-9f15-5f79b4a64179';
		$mAcquirerIds[1] = '68b66eb9-364b-42a3-880d-68fadd73909e';
		$userId = '32165df7-6b97-4f86-9e6f-8638eb30cd9e';
		$smUserId = $this->UUIDv4(); //arbitrary manager id
		//Save test data
		foreach ($productIds as $key => $productId) {
			$tstData = [
				'UserCompensationProfile' => [
					'id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
					'user_id' => $userId,
					'is_profile_option_1' => 1,
					'is_profile_option_2' => 0
				],
				'UserParameter' => [
					[
						'merchant_acquirer_id' => $mAcquirerIds[$key],
						'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
						'associated_user_id' => $userId, //% of Gross Profit
						'user_parameter_type_id' => 'd78ed6bf-739f-43b9-a262-dd343d3bcb5b', //% of Gross Profit
						'products_services_type_id' => $productId,
						'is_multiple' => 0,
						'value' => 50
					],
					[	//Associated Manager
						'merchant_acquirer_id' => $mAcquirerIds[$key],
						'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
						'associated_user_id' => $smUserId, //% of Gross Profit
						'user_parameter_type_id' => 'd78ed6bf-739f-43b9-a262-dd343d3bcb5b', //% of Gross Profit
						'products_services_type_id' => $productId,
						'is_multiple' => 0,
						'value' => 20
					]
				],
				'ResidualParameter' => [
					[
						'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
						'residual_parameter_type_id' => ResidualParameterType::R_MULTIPLE, //rep multiple type
						'products_services_type_id' => $productId,
						'is_multiple' => 1,
						'tier' => 0,
						'value' => 5
					],
					[
						'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
						'residual_parameter_type_id' => ResidualParameterType::MGR_MULTIPLE, //manager multiple type
						'associated_user_id' => $smUserId,
						'products_services_type_id' => $productId,
						'is_multiple' => 1,
						'tier' => 0,
						'value' => 60
					],
				],
				'ResidualTimeFactor' => [
					[
						'id' => '5fd3ec5d-a415-4821-a44c-9bf26cb77cfc',
						'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
						'tier1_begin_month' => 0,
						'tier1_end_month' => 1,
						'tier2_begin_month' => 2,
						'tier2_end_month' => 3,
						'tier3_begin_month' => 4,
						'tier3_end_month' => 5,
						'tier4_begin_month' => 6,
						'tier4_end_month' => 7,
					]
				],
				'ResidualTimeParameter' => [
					[
						'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
						'residual_parameter_type_id' => ResidualParameterType::R_MULTIPLE, //rep multiple type
						'products_services_type_id' => $productId,
						'is_multiple' => 1,
						'tier' => 1,
						'value' => 6
					]
				],

			];

			$UserCompensationProfile->saveAll($tstData, ['deep' => true]);
		}
		$MerchantUwVolume->save([
			'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
			'average_ticket' => 500,
			'mo_volume' => 1000,
			'visa_volume' => 1000,
			'sales' => 2
		]);
		//add manager to the merchant
		$smData = ['id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', 'sm_user_id' => $smUserId];
		$Merchant->save($smData, ['validate' => false]);
	}
/**
 * testFindIndex
 *
 * @covers CommissionReport::_findIndex()
 * @return void
 */
	public function testFindIndex() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		$expected = [
			'c_month' => 2,
			'c_year' => 2015,
			'status' => 'A'
		];

		$response = $CommissionReport->find('index', ['order' => 'CommissionReport.c_month ASC']);

		$this->assertEquals($expected, $response[0]['CommissionReport']);
	}

/**
 * testGetDefaultContaind
 *
 * @covers CommissionReport::getDefaultContain()
 * @return void
 */
	public function testGetDefaultContain() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		$expected = [
			'User',
			'Order',
			'Merchant',
			'Partner',
			'ShippingType',
		];
		$this->assertEquals($expected, $CommissionReport->getDefaultContain());
	}

/**
 * testFindCommissionReport
 *
 * @covers CommissionReport::_findCommissionReport()
 * @return void
 */
	public function testFindCommissionReport() {
		$CommissionReport = $this->_getSimpleCommissionReportMock();
		$result = $CommissionReport->find('commissionReport');
		$matchContain = [
			'CommissionReport',
			'Merchant',
			'Client',
			'Address',
			'MerchantUws',
			'Entity',
			'Organization',
			'Region',
			'Subregion',
			'Partner'
		];

		$this->assertEquals($matchContain, array_keys(Hash::get($result, '0')));
		$this->assertEquals(1.5, Hash::get($result, '0.CommissionReport.tax_amount'));
		$this->assertEquals(1.7, Hash::get($result, '1.CommissionReport.tax_amount'));

		$matchOrder = [
			'00000000-0000-0000-0000-000000000001',
			'00000000-0000-0000-0000-000000000003',
		];
		$this->assertEquals($matchOrder, Hash::extract($result, '{n}.CommissionReport.id'));

		// Check virtual field
		$this->assertEquals('Bill McAbee', Hash::get($result, '0.CommissionReport.rep'));
	}

/**
 * Regression test Ticket #17711
 *
 * @covers CommissionReport::_findCommissionReport()
 * @return void
 */
	public function testFindCommissionReportRegressionTest17711() {
		$SearchByUserIdBehavior = $this->getMock('SearchByUserIdBehavior', ['_getLoggedInUser']);
		ClassRegistry::addObject('SearchByUserIdBehavior', $SearchByUserIdBehavior);
		$CommissionReport = $CommissionReport = $this->_getSimpleCommissionReportMock();
		$CommissionReport->Behaviors->attach('SearchByUserId');
		$SearchByUserIdBehavior
			->expects($this->any())
			->method('_getLoggedInUser')
			->will($this->returnValue(AxiaTestCase::USER_SM_ID));
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
		$results = $CommissionReport->find('commissionReport', [
			'conditions' => $CommissionReport->parseCriteria($filterParams)
		]);
		$this->assertCount(1, $results);
		$this->assertSame('00000000-0000-0000-0000-000000000003', Hash::get($results, '0.CommissionReport.id'));
	}

/**
 * Return a mocked CommissionReport model
 *
 * @param string $userId User id
 * @return CommissionReport
 */
	protected function _getComissionReportMock($userId) {
		$SearchByUserIdBehavior = $this->getMock('SearchByUserIdBehavior', ['_getLoggedInUser']);
		ClassRegistry::addObject('SearchByUserIdBehavior', $SearchByUserIdBehavior);
		$CommissionReport = $this->_mockModel('CommissionReport', ['_getCurrentUser'], [
				'_getCurrentUser' => ['value' => $userId],
			]);
		$CommissionReport->Behaviors->attach('SearchByUserId');
		$SearchByUserIdBehavior
			->expects($this->any())
			->method('_getLoggedInUser')
			->will($this->returnValue($userId));

		$CommissionReport->User = $this->getMockForModel('User', ['_getCurrentUser']);
		$CommissionReport->User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		$CommissionReport->User->ResidualReport = $this->getMockForModel('ResidualReport', ['_getCurrentUser']);
		$CommissionReport->User->ResidualReport->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		$CommissionReport->User->Adjustment = $this->getMockForModel('Adjustment', ['_getCurrentUser']);
		$CommissionReport->User->Adjustment->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		$CommissionReport->RolePermissions = $this->getMockBuilder('RolePermissions')
			->setMethods(['_getCurrentUser'])
			->getMock();

		return $CommissionReport;
	}

/**
 * test
 *
 * @param string $userId User id
 * @param array $filterParams Parameters to filter the report
 * @param int $expectedReports Expected number of reports
 * @param array $expectedIncome Expected net income
 * @return void
 * @dataProvider provideCommissionReportData
 * @covers CommissionReport::getCommissionReportData()
 */
	public function testGetCommissionReportData($userId, $filterParams, $expectedReports, $expectedIncome) {
		$CommissionReport = $this->_getComissionReportMock($userId);
		$result = $CommissionReport->getCommissionReportData($filterParams);

		$expectedVaribles = [
			'commissionReports',
			'commissionReportTotals',
			'shippingTypes',
			'income',
			'repAdjustments',
			'netIncome',
			'organizations',
			'regions',
			'subregions',
			'partners',
		];
		$this->assertEquals($expectedVaribles, array_keys($result));
		$this->assertCount($expectedReports, Hash::get($result, 'commissionReports'));
		$this->assertEquals($expectedIncome, Hash::get($result, 'netIncome.netIncome'));

		unset($CommissionReport);
	}

/**
 * Provider for testGetCommissionReportData
 *
 * @return array
 */
	public function provideCommissionReportData() {
		return [
			[
				AxiaTestCase::USER_ADMIN_ID,
				[
					'user_id' => 'select_all:select_all',
					'from_date' => ['year' => '2015', 'month' => '01'],
					'end_date' => ['year' => '2015', 'month' => '12'],
				],
				3,
				-12.05,
			],
			[
				AxiaTestCase::USER_SM_ID,
				[
					'user_id' => 'parent_user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6',
					'from_date' => ['year' => '2015', 'month' => '01'],
					'end_date' => ['year' => '2015', 'month' => '12'],
				],
				2,
				null,
			],
			[
				AxiaTestCase::USER_REP_ID,
				[
					'user_id' => 'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1',
					'from_date' => ['year' => '2015', 'month' => '01'],
					'end_date' => ['year' => '2015', 'month' => '12'],
				],
				1,
				null,
			],
		];
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport::getCommissionReportData()
 * @expectedException ForbiddenException
 * @expectedExceptionMessage This user can not access the commission report
 */
	public function testGetCommissionReportDataNotAccess() {
		$userId = AxiaTestCase::USER_INSTALLER_ID;
		$CommissionReport = $this->_getComissionReportMock($userId);
		$CommissionReport->User = $this->getMockForModel('User', ['_getCurrentUser', 'roleIs']);
		$CommissionReport->User->expects($this->any())
			->method('roleIs')
			->will($this->returnValue(false));

		$CommissionReport->getCommissionReportData([
			'from_date' => ['year' => '2015', 'month' => '01'],
			'end_date' => ['year' => '2015', 'month' => '12']
		]);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport::_processIncomeData()
 */
	public function testProcessIncomeData() {
		$userId = AxiaTestCase::USER_REP_ID;
		$CommissionReport = $this->_getComissionReportMock($userId);

		$filterParams = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '12'
			],
			'user_id' => 'user_id:' . $userId
		];
		$result = $CommissionReport->getCommissionReportData($filterParams);

		$this->assertEmpty(Hash::get($result, 'income'));

		unset($CommissionReport);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport::_processRepAdjustmentsData()
 *
 */
	public function testProcessRepAdjustmentsData() {
		$userId = AxiaTestCase::USER_REP_ID;
		$CommissionReport = $this->_getComissionReportMock($userId);

		$filterParams = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '12'
			],
			'user_id' => 'user_id:' . $userId
		];
		$result = $CommissionReport->getCommissionReportData($filterParams);

		$this->assertEmpty(Hash::get($result, 'repAdjustments'));

		unset($CommissionReport);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport::_processCommissionReportData()
 */
	public function testProcessCommissionReportData() {
		$userId = AxiaTestCase::USER_REP_ID;
		$CommissionReport = $this->_getComissionReportMock($userId);

		$filterParams = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '12'
			],
			'user_id' => 'user_id:' . $userId
		];
		$result = $CommissionReport->getCommissionReportData($filterParams);
		$this->assertCount(1, Hash::get($result, 'commissionReports'));

		$expectedReportFields = [
			'mid',
			'dba',
			'client_id_global',
			'axia_invoice',
			'rep',
			'description',
			'retail',
			'rep_cost',
			'partner_cost',
			'shipping_type',
			'shipping_cost',
			'expedited',
			'rep_profit',
			'partner_profit',
			'state',
			'organization',
			'region',
			'subregion',
			'location',
			'partner_name'
		];
		$firstCommissionReport = Hash::get($result, 'commissionReports.0');
		$this->assertEquals($expectedReportFields, array_keys($firstCommissionReport));
		$this->assertEquals(1.5, $firstCommissionReport['rep_profit']);

		$expectedTotalsfields = [
			'rep_profit',
			'partner_profit'
		];
		$totals = Hash::get($result, 'commissionReportTotals');
		$this->assertEquals($expectedTotalsfields, array_keys($totals));
		$this->assertEquals(1.5, $totals['rep_profit']);

		unset($CommissionReport);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport:: __dbCommissionReportGetSplitCommissions()
 */
	public function test__DbCommissionReportGetSplitCommissions() {
		$userId = AxiaTestCase::USER_REP_ID;
		
		$reflection = new ReflectionClass('CommissionReport');
		$method = $reflection->getMethod('__dbCommissionReportGetSplitCommissions');
		$method->setAccessible(true);
		$actual = $method->invokeArgs(ClassRegistry::init('CommissionReport'), [$userId]);
		$this->assertFalse($actual);
		 
	}
/**
 * test
 *
 * @return void
 * @covers CommissionReport::searchByLocation()
 */
	public function testSearchByLocation() {
		$userId = AxiaTestCase::USER_SM_ID;
		$data = ['location_description' => 'street-1'];
		$CommissionReport = $this->_getComissionReportMock($userId);
		$actual = $CommissionReport->searchByLocation($data);
		$expected = 'SELECT "Address"."merchant_id" AS "Address__merchant_id" FROM "public"."addresses" AS "Address"   WHERE "Address"."address_type_id" = \'795f442e-04ab-43d1-a7f8-f9ba685b90ac\' AND "Address"."address_street" = \'street-1\'';
		$this->assertSame($expected, $actual);
	}
/**
 * test
 *
 * @return void
 * @covers CommissionReport::searchByUserAndManager()
 */
	public function testSearchByUserAndManager() {
		$userId = AxiaTestCase::USER_SM_ID;
		$CommissionReport = $this->_getComissionReportMock($userId);

		$filterParams = ['user_id' => 'parent_user_id:' . $userId];
		$result = $CommissionReport->searchByUserAndManager($filterParams);
		$expected = ["CommissionReport.user_id = (select distinct user_id from associated_users where user_id = '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' AND associated_user_id = '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' AND (permission_level = 'SM' OR permission_level = 'SM2'))"];
		$this->assertSame($expected, $result);

		$userId = '00000000-0000-0000-0000-000000000005';//user with referrer role
		$CommissionReport = $this->_getComissionReportMock($userId);

		$filterParams = ['user_id' => 'user_id:' . $userId];
		$result = $CommissionReport->searchByUserAndManager($filterParams);
		$expected = [
			"CommissionReport.user_id IN (select distinct user_id from associated_users where associated_user_id = '00000000-0000-0000-0000-000000000005' AND (permission_level = 'SM' OR permission_level = 'SM2'))",
			'OR' => [
				"CommissionReport.partner_id = (select distinct associated_user_id from associated_users where user_id = '00000000-0000-0000-0000-000000000005' AND associated_user_id = '00000000-0000-0000-0000-000000000005' AND permission_level = 'Partner')",
				"CommissionReport.referer_id = (select distinct associated_user_id from associated_users where user_id = '00000000-0000-0000-0000-000000000005' AND associated_user_id = '00000000-0000-0000-0000-000000000005' AND permission_level = 'Referrer')",
				"CommissionReport.reseller_id = (select distinct associated_user_id from associated_users where user_id = '00000000-0000-0000-0000-000000000005' AND associated_user_id = '00000000-0000-0000-0000-000000000005' AND permission_level = 'Reseller')"
			]
		];
		$this->assertSame($expected, $result);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport:: _setMultipleAmounts()
 */
	public function test_SetMultipleAmounts() {
		$AxiaCalculate = $this->getMockForModel('AxiaCalculate', ['thirdPartyProfitAmnt']);
		$record = [
			'partner_gross_profit' => '5',
			'partner_pct_of_gross' => '100',
			'partner_profit_pct' => '5',
			'referrer_gross_profit' => '5',
			'referrer_pct_of_gross' => '100',
			'reseller_gross_profit' => '5',
			'reseller_pct_of_gross' => '10',
			'multiple' => '10',
			'rep_gross_profit' => '20',
			'partner_rep_gross_profit' => '30',
			'rep_pct_of_gross' => '40',
			'partner_rep_pct_of_gross' => '50',
			'multiple_amount' => null,
			'manager_multiple' => '10',
			'manager_gross_profit' => '20',
			'manager_pct_of_gross' => '30',
			'manager_multiple_amount' => null,
			'manager2_multiple' => '10',
			'manager2_gross_profit' => '20',
			'manager2_pct_of_gross' => '30',
			'manager2_multiple_amount' => null
		];
		$merchant = [
			'Merchant' => [
				//referrer usr
				'referer_id' => '00000000-0000-0000-0000-000000000005',
				'reseller_id' => null,
				'partner_id' => '55144783-77a0-43a6-8474-554534627ad4'
			]
		];
		$productId = '566f5f47-59c4-456a-9e1b-23f534627ad4';


		$userId = AxiaTestCase::USER_REP_ID;
		
		$reflection = new ReflectionClass('CommissionReport');
		$method = $reflection->getMethod('_setMultipleAmounts');
		$method->setAccessible(true);
		$OriginalInstance = ClassRegistry::init('CommissionReport');
		$OriginalInstance->AxiaCalculate = ClassRegistry::init('AxiaCalculate');
		$actual = $method->invokeArgs($OriginalInstance, [&$record, &$merchant, $productId]);
		$this->assertEqual(1.5 ,$record['multiple_amount']);
		$this->assertEqual(0.6 ,$record['manager_multiple_amount']);
		$this->assertEqual(0.6 ,$record['manager2_multiple_amount']);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport:: __addCommissionReportRecord()
 */
	public function test__AddCommissionReportRecord() {
		$userId = CakeText::uuid();
		$merchantId = CakeText::uuid();
		$record = [
			'user_id' => $userId ,
			'merchant_id' => $merchantId,
			'split_commissions' => false
		];
		
		$reflection = new ReflectionClass('CommissionReport');
		$method = $reflection->getMethod('__addCommissionReportRecord');
		$method->setAccessible(true);
		$actual = $method->invokeArgs(ClassRegistry::init('CommissionReport'), [$record]);
		$this->assertNotEmpty($record, $actual['CommissionReport']['id']);
		unset($actual['CommissionReport']['id']);
		$this->assertSame($record, $actual['CommissionReport']);
	}

/**
 * test
 *
 * @return void
 * @covers CommissionReport::getIndexData()
 */
	public function testGetIndexData() {
		$userId = AxiaTestCase::USER_REP_ID;
		$CommissionReport = $this->_getComissionReportMock($userId);
		$actual = $CommissionReport->getIndexData([]);
		$expected = [
			[
                'c_year' => 2015,
                'c_month' => 2,
                'status' => 'A'
			],
			[
                'c_year' => 2015,
                'c_month' => 7,
                'status' => 'A'
			],
			[
                'c_year' => 2015,
                'c_month' => 11,
                'status' => 'I'
			],
		];
		$this->assertSame($expected, $actual);
	}

/**
 * testSetReportVars
 *
 * @return void
 * @covers CommissionReport::setReportVars()
 */
	public function testSetReportVars() {
		$userId = AxiaTestCase::USER_ADMIN_ID;
		$filterParams = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '11'
			],
			'user_id' => 'user_id:' . AxiaTestCase::USER_SM_ID
		];
		$data = [
			'commissionMultipleTotals' => []
		];

		$CommissionReport = $this->_getComissionReportMock($userId);
		$actual = $CommissionReport->setReportVars($data, $filterParams);

		$this->assertTrue(array_key_exists('commissionReports', $actual));
		$this->assertTrue(array_key_exists('commissionReportTotals', $actual));
		$this->assertTrue(array_key_exists('shippingTypes', $actual));
		$this->assertTrue(array_key_exists('income', $actual));
		$this->assertTrue(array_key_exists('repAdjustments', $actual));
		$this->assertTrue(array_key_exists('netIncome', $actual));
		$this->assertTrue(array_key_exists('organizations', $actual));
		$this->assertTrue(array_key_exists('regions', $actual));
		$this->assertTrue(array_key_exists('subregions', $actual));
		$this->assertTrue(array_key_exists('partners', $actual));
	}

/**
 * test_ProcessIncomeData
 *
 * @return void
 * @covers CommissionReport::_processIncomeData()
 */
	public function test_ProcessIncomeData(){
		$userId = AxiaTestCase::USER_ADMIN_ID;
		$filterParams = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '11'
			],
			'user_id' => 'user_id:' . AxiaTestCase::USER_SM_ID
		];
		$data = [
			'commissionMultipleTotals' => []
		];

		$reflection = new ReflectionClass('CommissionReport');
		$method = $reflection->getMethod('_processIncomeData');
		$method->setAccessible(true);
		$actual = $method->invokeArgs($this->_getComissionReportMock($userId), [$data, $filterParams]);
		$expected = [
			'rep_residuals' => -5.0,
			'sm_residuals' => -0.64,
			'sm2_residuals' => 0.0,
			'partner_residuals' => 0.0,
			'commission' => 0.0,
			'partner_commission' => 0.0,
			'gross_income' => -5.64
		];

		$this->assertSame($expected, $actual);
	}

/**
 * test_ProcessRepAdjustmentsData
 *
 * @return void
 * @covers CommissionReport::_processRepAdjustmentsData()
 */
	public function test_ProcessRepAdjustmentsData() {
		$userId = AxiaTestCase::USER_ADMIN_ID;
		$filterParams = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '11'
			],
			'user_id' => 'user_id:' . AxiaTestCase::USER_REP_ID
		];
		$data = [
			'commissionMultipleTotals' => []
		];

		$reflection = new ReflectionClass('CommissionReport');
		$method = $reflection->getMethod('_processRepAdjustmentsData');
		$method->setAccessible(true);
		$actual = $method->invokeArgs($this->_getComissionReportMock($userId), [$filterParams]);

		$expected = [
			'adjustments' => [
				[
                    'adj_amount' => '1.0000',
                    'adj_description' => 'Adjustment 1'
                ]
	        ],
	        'gross_adjustments' => 1.0
		];

		$this->assertSame($expected, $actual);
	}
}
