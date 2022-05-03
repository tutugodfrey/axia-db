<?php
App::uses('CommissionPricing', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * CommissionPricing Test Case
 *
 */
class CommissionPricingTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CommissionPricing = $this->getMockForModel('CommissionPricing', array(
			'_getCurrentUser'
		));
		$this->CommissionPricing->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_SM_ID));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CommissionPricing);
		parent::tearDown();
	}

/**
 * Return a mocked CommissionPricing model
 *
 * @param string $userId User id
 * @return CommissionPricing
 */
	protected function _getComissionPricingMock($userId) {
		$CommissionPricing = $this->getMockForModel('CommissionPricing', ['_getCurrentUser']);
		$CommissionPricing->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		$CommissionPricing->User = $this->getMockForModel('User', ['_getCurrentUser']);
		$CommissionPricing->User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		return $CommissionPricing;
	}

/**
 * test
 *
 * @covers CommissionPricing::timelineStartConditions()
 * @return void
 */
	public function testDateStartConditions() {
		$result = $this->CommissionPricing->timelineStartConditions([
			'from_date' => [
				'year' => '1999',
				'month' => '04'
			],
			'report_type' => CommissionPricing::REPORT_COMMISION_MULTIPLE,
		]);
		$expected = [
			'TimelineInc.timeline_date_completed >=' => '1999-04-01'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers CommissionPricing::timelineStartConditions()
 * @return void
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid date
 */
	public function testDateStartConditionsInvalidDate() {
		$this->CommissionPricing->timelineStartConditions([
			'random_from_date_path' => [
				'year' => '1999',
				'month' => '04'
			],
			'report_type' => CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE,
		]);
	}

/**
 * test
 *
 * @covers CommissionPricing::timelineStartConditions()
 * @return void
 * @expectedException BadMethodCallException
 * @expectedExceptionMessage Missing report type
 */
	public function testDateStartConditionsMissingReportType() {
		$this->CommissionPricing->timelineStartConditions([
			'from_date' => [
				'year' => '1999',
				'month' => '04'
			],
		]);
	}

/**
 * test
 *
 * @covers CommissionPricing::timelineEndConditions()
 * @return void
 */
	public function testTimelineEndConditions() {
		$result = $this->CommissionPricing->timelineEndConditions([
			'end_date' => [
				'year' => '1999',
				'month' => '04'
			],
			'report_type' => CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE,
		]);
		$expected = [
			'TimelineSub.timeline_date_completed <=' => '1999-04-30'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers CommissionPricing::timelineEndConditions()
 * @return void
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid date
 */
	public function testTimelineEndConditionsInvalidDate() {
		$this->CommissionPricing->timelineEndConditions([
			'end_date' => [
				'year' => '1999',
			],
			'report_type' => CommissionPricing::REPORT_COMMISION_MULTIPLE,
		]);
	}

/**
 * test
 *
 * @covers CommissionPricing::timelineEndConditions()
 * @return void
 * @expectedException BadFunctionCallException
 * @expectedExceptionMessage Missing report type
 */
	public function testTimelineEndConditionsMissingReportType() {
		$this->CommissionPricing->timelineEndConditions([
			'end_date' => [
				'year' => '1999',
				'month' => '5',
			],
		]);
	}

/**
 * test
 *
 * @covers CommissionPricing::productsServicesTypeConditions()
 * @return void
 */
	public function testProductsServicesTypeConditions() {
		$result = $this->CommissionPricing->productsServicesTypeConditions([
			'products_services_type_id' => '9ac4250e-6938-4bad-a979-e9856abc6568']);
		$expected = [
			['OR' => [
					'CommissionPricing.products_services_type_id' => '9ac4250e-6938-4bad-a979-e9856abc6568',
					'Cpc.products_services_type_id' => '9ac4250e-6938-4bad-a979-e9856abc6568'
				]
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers CommissionPricing::_findCommissionMultiple()
 * @covers CommissionPricing::_formatCommissionPricingField()
 * @covers CommissionPricing::_processCommissionPricingData()
 * @covers CommissionPricing::_makeReportQuery()
 * @return void
 */
	public function testFindCommissionMultiple() {
		$commissionMultiples = $this->CommissionPricing->find('commissionMultiple');
		$this->assertCount(1, $commissionMultiples);

		$firstCommission = Hash::get($commissionMultiples, '0');
		$expectedContains = [
		'Merchant',
		'Client',
		'Bet',
		'ProductsServicesType',
		'TimelineSub',
		'TimelineApp',
		'TimelineIns',
		'TimelineSis',
		'TimelineInc',
		'CommissionPricing',
		'Rep',
		'PartnerRep',
		'Manager',
		'SecondaryManager',
		'Partner',
		'Referrer',
		'Reseller',
		'Entity',
		];

		$this->assertEquals($expectedContains, array_keys($firstCommission));

		$expectedReps = [
			'Slim Pickins',
		];
		$this->assertEquals($expectedReps, Hash::extract($commissionMultiples, '{n}.Rep.fullname'));

		$expectedFirstCommissionPricings = [
			'm_monthly_volume' => '25.0000',
			'm_avg_ticket' => '0.0000',
			'num_items' => '0.0000',
			'm_rate_pct' => '0.0300',
			'm_per_item_fee' => '0.1500',
			'm_statement_fee' => '5.0000',
			'multiple_amount' => '25',
			'multiple' => '2.5000',
			'manager_residual_gross_profit' => '-55.5',
			'secundary_manager_residual_gross_profit' => '-55.5',
			'r_rate_pct' => '0.0000',
			'r_per_item_fee' => '0.0400',
			'r_statement_fee' => '5.0000',
			'rep_gross_profit' => '100',
			'rep_pct_of_gross' => '2',
			'partner_rep_rate' => '0',
			'partner_rep_per_item_fee' => '0',
			'partner_rep_statement_fee' => '0',
			'partner_rep_gross_profit' => '50',
			'partner_rep_pct_of_gross' => '0',
			'manager_rate' => '0',
			'manager_per_item_fee' => '0',
			'manager_statement_fee' => '0',
			'manager_gross_profit' => '0',
			'manager_pct_of_gross' => '0',
			'manager2_rate' => '0',
			'manager2_per_item_fee' => '0',
			'manager2_statement_fee' => '0',
			'manager2_gross_profit' => '0',
			'manager2_pct_of_gross' => '0',
			'partner_rate' => '0',
			'partner_per_item_fee' => '0',
			'partner_statement_fee' => null,
			'partner_gross_profit' => '0',
			'partner_pct_of_gross' => '0',
			'referrer_rate' => '0',
			'referrer_per_item_fee' => '0',
			'referrer_statement_fee' => '0',
			'referrer_gross_profit' => '0',
			'referrer_pct_of_gross' => '0',
			'reseller_rate' => '0',
			'reseller_per_item_fee' => '0',
			'reseller_statement_fee' => '0',
			'reseller_gross_profit' => '55.5',
			'reseller_pct_of_gross' => '0',
			'residual_gross_profit' => '44.5',
			'r_profit_amount' => null,
			'partner_rep_profit_pct' => null,
			'partner_rep_profit_amount' => null,
			'manager_profit_pct' => null,
			'manager_profit_amount' => null,
			'manager2_profit_pct' => null,
			'manager2_profit_amount' => null,
			'partner_profit_pct' => null,
			'partner_profit_amount' => null,
			'referrer_profit_pct' => null,
			'referrer_profit_amount' => null,
			'reseller_profit_pct' => null,
			'reseller_profit_amount' => null,
			'partner_id' => null,
			'original_m_rate_pct' => null,
			'manager_multiple_amount' => '3.5',
			'manager_multiple' => null,
			'manager2_multiple_amount' => '4',
			'manager2_multiple' => null,
			'rep_product_profit_pct' => null

		];
		$this->assertEquals($expectedFirstCommissionPricings, Hash::get($firstCommission, 'CommissionPricing'));
	}

/**
 * test
 *
 * @return void
 * @covers CommissionPricing::getCommissionMultipleData()
 * @covers CommissionPricing::_processCommissionPricingData()
 * @covers CommissionPricing::_makeReportQuery()
 */
	public function testGetCommissionMultipleData() {
		$userId = AxiaTestCase::USER_ADMIN_ID;
		$CommissionPricing = $this->_getComissionPricingMock($userId);

		$result = $CommissionPricing->getCommissionMultipleData([
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2016',
				'month' => '12'
			],
			'report_type' => CommissionPricing::REPORT_COMMISION_MULTIPLE
		]);

		$expectedReportFields = [
			'mid',
			'dba',
			'bet',
			'client_id_global',
			'bus_level',
			'product',
			'sub_date',
			'app_date',
			'install_date',
			'recd_install_sheet',
			'com_month',
			'avg_tkt',
			'items',
			'volume',
			'original_merch_rate',
			'bet_extra_pct',
			'merch_rate',
			'merch_p_i',
			'merch_stmt',
			'rep',
			'rep_rate',
			'rep_p_i',
			'rep_stmt',
			'rep_gross_profit',
			'rep_pct_of_gross',
			'multiple',
			'multiple_amount',
			'manager_multiple',
			'manager_multiple_amount',
			'manager2_multiple',
			'manager2_multiple_amount',
			'ent_name',
			'partner_name',
			'partner_pct_of_gross',
			'partner_profit_pct',
		];
		$firstCommissionMultiple = Hash::get($result, 'commissionMultiples.0');
		$this->assertEquals($expectedReportFields, array_keys($firstCommissionMultiple));
		$this->assertCount(1, Hash::get($result, 'commissionMultiples'));

		$expectedMerchants = [
			'Another Merchant',
		];
		$this->assertEquals($expectedMerchants, Hash::extract($result, 'commissionMultiples.{n}.dba'));

		$expectedTotals = [
			'volume' => 0,
			'multiple_amount' => 25,
			'rep_gross_profit' => 100,
			'manager_multiple_amount' => '3.5',
			'manager2_multiple_amount' => '4',
			'items' => 0,
			'avg_tkt' => 0,
			'rep_residual_gp' => 0,
		];
		$this->assertEquals($expectedTotals, Hash::get($result, 'commissionMultipleTotals'));

		unset($CommissionPricing);
	}
/**
 * test
 * Test retrieving data intended fro Gross Profit Analysis report
 * 
 * @return void
 */
	public function testGetDataGpAnalysisParams() {
		$userId = AxiaTestCase::USER_ADMIN_ID;
		$CommissionPricing = $this->_getComissionPricingMock($userId);

		$result = $CommissionPricing->getCommissionMultipleData([
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2016',
				'month' => '12'
			],
			'report_type' => CommissionPricing::REPORT_GP_ANALYSIS,
		]);

		$expectedReportFields = [
			'mid',
			'dba',
			'bet',
			'client_id_global',
			'bus_level',
			'product',
			'sub_date',
			'app_date',
			'install_date',
			'recd_install_sheet',
			'avg_tkt',
			'items',
			'volume',
			'original_merch_rate',
			'bet_extra_pct',
			'merch_rate',
			'merch_p_i',
			'merch_stmt',
			'rep',
			'rep_rate',
			'rep_p_i',
			'rep_stmt',
			'rep_gross_profit',
			'partner_profit_amount',
			'rep_residual_gp',
			'multiple',
			'manager_multiple',
			'manager_multiple_amount',
			'manager2_multiple',
			'manager2_multiple_amount',
			'ent_name',
			'partner_name',
			'partner_pct_of_gross',
			'partner_profit_pct',
			'organization',
			'region',
			'subregion',
			'location'
		];
		$firstCommissionMultiple = Hash::get($result, 'commissionMultiples.0');

		$this->assertEquals($expectedReportFields, array_keys($firstCommissionMultiple));
		$this->assertCount(2, Hash::get($result, 'commissionMultiples'));

		$expectedMerchants = [
			'Another Merchant',
			'16 Hands'
		];
		$this->assertEquals($expectedMerchants, Hash::extract($result, 'commissionMultiples.{n}.dba'));

		$expectedTotals = [
			'volume' => 75.0,
			'multiple_amount' => 30,
			'rep_gross_profit' => 100,
			'manager_multiple_amount' => 0,
			'manager2_multiple_amount' => 0,
			'items' => 0.0,
			'avg_tkt' => 0,
			'rep_residual_gp' => 100.0
		];

		$this->assertEquals($expectedTotals, Hash::get($result, 'commissionMultipleTotals'));
		//Test with install date type
		$result = $CommissionPricing->getCommissionMultipleData([
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2016',
				'month' => '12'
			],
			'report_type' => CommissionPricing::REPORT_GP_ANALYSIS,
			'date_type' => TimelineItem::GO_LIVE_DATE
		]);
		$firstCommissionMultiple = Hash::get($result, 'commissionMultiples.0');
		$this->assertEquals($expectedReportFields, array_keys($firstCommissionMultiple));
		$this->assertCount(2, Hash::get($result, 'commissionMultiples'));
		//update expected totals
		$expectedTotals['volume'] = 75.0;
		$expectedTotals['multiple_amount'] = 30;
		unset($CommissionPricing);
	}

/**
 * test
 *
 * @covers CommissionPricing::_findGrossProfitEstimate()
 * @covers CommissionPricing::_formatCommissionPricingField()
 * @covers CommissionPricing::_processCommissionPricingData()
 * @covers CommissionPricing::_makeReportQuery()
 * @return void
 */
	public function testFindGrossProfitEstimate() {
		$expectedReportFields = [
			'mid',
			'dba',
			'bet',
			'client_id_global',
			'bus_level',
			'product',
			'sub_date',
			'days_to_approved',
			'app_date',
			'days_to_installed',
			'days_app_to_inst',
			'install_date',
			'recd_install_sheet',
			'com_month',
			'avg_tkt',
			'items',
			'volume',
			'original_merch_rate',
			'bet_extra_pct',
			'merch_rate',
			'merch_p_i',
			'merch_stmt',
			'rep',
			'rep_rate',
			'rep_p_i',
			'rep_stmt',
			'rep_gross_profit',
			'rep_pct_of_gross',
			'multiple',
			'multiple_amount',
			'manager_multiple',
			'manager_multiple_amount',
			'ent_name',
			'partner_name',
			'organization',
			'region',
			'subregion',
			'location',
		];

		// -- SM User
		$result = $this->CommissionPricing->find('grossProfitEstimate', ['roll_up_data' => false]);
		$expectedVariables = [
			'grossProfitEstimates',
			'grossProfitEstimateTotals'
		];

		$this->assertSame($expectedVariables, array_keys($result));
		$this->assertCount(2, Hash::get($result, 'grossProfitEstimates'));
		$firstEstimate = Hash::get($result, 'grossProfitEstimates.0');
		$this->assertEquals($expectedReportFields, array_keys($firstEstimate));

		// -- Admin User
		$CommissionPricing = $this->_getComissionPricingMock(AxiaTestCase::USER_ADMIN_ID);
		$result = $CommissionPricing->find('grossProfitEstimate', ['roll_up_data' => false]);
		$expectedReportFields = [
			'mid',
			'dba',
			'bet',
			'client_id_global',
			'bus_level',
			'product',
			'sub_date',
			'days_to_approved',
			'app_date',
			'days_to_installed',
			'days_app_to_inst',
			'install_date',
			'recd_install_sheet',
			'com_month',
			'avg_tkt',
			'items',
			'volume',
			'original_merch_rate',
			'bet_extra_pct',
			'merch_rate',
			'merch_p_i',
			'merch_stmt',
			'rep',
			'rep_rate',
			'rep_p_i',
			'rep_stmt',
			'rep_gross_profit',
			'rep_pct_of_gross',
			'multiple',
			'multiple_amount',
			'manager_multiple',
			'manager_multiple_amount',
			'manager2_multiple',
			'manager2_multiple_amount',
			'ent_name',
			'partner_name',
			'partner_pct_of_gross',
			'partner_profit_pct',
			'organization',
			'region',
			'subregion',
			'location',
		];

		$this->assertCount(2, Hash::get($result, 'grossProfitEstimates'));
		$firstEstimate = Hash::get($result, 'grossProfitEstimates.0');
		$this->assertEquals($expectedReportFields, array_keys($firstEstimate));

		$expectedMerchants = [
			'Another Merchant',
			'16 Hands',
		];
		$this->assertEquals($expectedMerchants, Hash::extract($result, 'grossProfitEstimates.{n}.dba'));

		$expectedTotals = [
			'volume' => 0,
			'multiple_amount' => 30,
			'manager_multiple_amount' => 0,
			'manager2_multiple_amount' => 0,
			'rep_gross_profit' => 100,
			'items' => 0,
			'avg_tkt' => 0,
			'rep_residual_gp' => 0,
		];
		$this->assertEquals($expectedTotals, Hash::get($result, 'grossProfitEstimateTotals'));

		unset($CommissionPricing);
	}

/**
 * test
 *
 * @covers CommissionPricing::getDefaultSearchValues
 * @return void
 */
	public function testGetDefaultSearchValues() {
		$year = date('Y');
		$month = date('m');
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
		$this->assertEquals($expected, $this->CommissionPricing->getDefaultSearchValues());
	}

/**
 * testGetTotalMultipleAmnt
 *
 * @covers CommissionPricing::getTotalMultipleAmnt
 * @return void
 */
	public function testGetTotalMultipleAmnt() {
		$TimelineEntry = ClassRegistry::init('TimelineEntry');
		ClassRegistry::init('TimelineItem');
		//Arbitrary users
		$userId = CakeText::uuid();
		$smUserId = CakeText::uuid();
		$sm2UserId = CakeText::uuid();
		$tstCommData = [
			[
				'merchant_id' => '00000000-0000-0000-0000-000000000003',
				'user_id' => $userId,
				'manager_id' => $smUserId,
				'secondary_manager_id' => $sm2UserId,
				'manager_multiple_amount' => 10,
				'manager_multiple' => 100,
				'manager2_multiple' => 100,
				'c_year' => 2020,
				'c_month' => 12
			],
			[
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'user_id' => $userId,
				'manager_id' => $smUserId,
				'secondary_manager_id' => $sm2UserId,
				'manager_multiple_amount' => 30,
				'manager2_multiple_amount' => 50,
				'manager_multiple' => 100,
				'manager2_multiple' => 100,
				'c_year' => 2020,
				'c_month' => 12
			]
		];
		$this->CommissionPricing->saveMany($tstCommData, ['validate' => false]);
		$timeEntries = [
			[
				'merchant_id' => '00000000-0000-0000-0000-000000000003',
				'timeline_item_id' => TimelineItem::INSTALL_COMMISSIONED,
				'timeline_date_completed' => '2020-12-01'
			],
			[
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'timeline_item_id' => TimelineItem::INSTALL_COMMISSIONED,
				'timeline_date_completed' => '2020-12-01'
			],
		];
		$TimelineEntry->saveMany($timeEntries, ['validate' => false]);

		$filterParams = [
				'merchant_dba' => '',
				'from_date' => [
					'month' => '12',
					'year' => '2020'
				],
				'end_date' => [
					'month' => '12',
					'year' => '2020'
				],
				'user_id' => 'user_id:' . $smUserId,
				'report_type' => CommissionPricing::REPORT_COMMISION_MULTIPLE
			];
		$actual = $this->CommissionPricing->getTotalMultipleAmnt(User::ROLE_SM, $filterParams);
		$expected = $tstCommData[0]['manager_multiple_amount'] + $tstCommData[1]['manager_multiple_amount'];
		$this->assertEquals($expected, $actual);

		$filterParams = [
				'merchant_dba' => '',
				'from_date' => [
					'month' => '12',
					'year' => '2020'
				],
				'end_date' => [
					'month' => '12',
					'year' => '2020'
				],
				'user_id' => 'user_id:' . $sm2UserId,
				'report_type' => CommissionPricing::REPORT_COMMISION_MULTIPLE
			];
		$actual = $this->CommissionPricing->getTotalMultipleAmnt(User::ROLE_SM2, $filterParams);
		$expected = $tstCommData[1]['manager2_multiple_amount'];
		$this->assertEquals($expected, $actual);

		$filterParams = [
				'merchant_dba' => '',
				'from_date' => [
					'month' => '12',
					'year' => '2020'
				],
				'end_date' => [
					'month' => '12',
					'year' => '2020'
				],
				'user_id' => 'parent_user_id:', //(no parent user should return all non zero multiple amounts)
				'report_type' => CommissionPricing::REPORT_COMMISION_MULTIPLE
			];
		$actual = $this->CommissionPricing->getTotalMultipleAmnt(User::ROLE_SM, $filterParams);
		$expected = $tstCommData[0]['manager_multiple_amount'] + $tstCommData[1]['manager_multiple_amount'];
		$this->assertEquals($expected, $actual);

		$actual = $this->CommissionPricing->getTotalMultipleAmnt(User::ROLE_SM2, $filterParams);
		$expected = $tstCommData[1]['manager2_multiple_amount'];
		$this->assertEquals($expected, $actual);
	}

}
