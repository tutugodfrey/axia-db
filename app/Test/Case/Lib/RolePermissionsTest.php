<?php
App::uses('AxiaTestCase', 'Test');
App::uses('RolePermissions', 'Lib');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * Merchant Test Case
 *
 */
class RolePermissionsTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RolePermissions = $this->getMockBuilder('RolePermissions')
			->setMethods(['_getCurrentUser'])
			->getMock();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RolePermissions);
		parent::tearDown();
	}

/**
 * testGetRepFilterConditionsInvalidArguments
 *
 * @param string $user User id
 * @param string $exception Exception class name
 * @param string $message Exception error message
 * @dataProvider filterConditionsDataInvalidArguments
 * @return void
 */
	public function testGetRepFilterConditionsInvalidArguments($user, $exception, $message) {
		$expected = 'var-not-modified';
		try {
			$expected = $this->RolePermissions->getRepFilterConditions($user);
		} catch (Exception $e) {
			$this->assertEquals($exception, get_class($e));
			$this->assertEquals($message, $e->getMessage());
		}
		$this->assertEquals('var-not-modified', $expected);
	}

/**
 * Gets the data provider for testGetRepFilterConditionsInvalidArguments
 *
 * @return array
 */
	public function filterConditionsDataInvalidArguments() {
		return [
			[1, 'InvalidArgumentException', 'Invalid user id'],
			[['array'], 'InvalidArgumentException', 'Invalid user id'],
			['00000000-9999-0000-0000-000000000001', 'NotFoundException', 'User not found'],
		];
	}

/**
 * testGetRepFilterConditionsAsAdmin
 *
 * @param string $userId User id
 * @param mixed $expected Expected output
 * @test
 * @dataProvider filterConditionsDataAsAdmin
 * @return void
 */
	public function testGetRepFilterConditionsAsAdmin($userId, $expected) {
		$this->RolePermissions->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));

		$result = $this->RolePermissions->getRepFilterConditions($userId);
		$this->assertEquals($expected, $result);
	}

/**
 * Gets the data provider for testGetRepFilterConditionsAsAdmin
 *
 * @return array
 */
	public function filterConditionsDataAsAdmin() {
		return [
			[null, []],
			[AxiaTestCase::USER_REP_ID, ['User.id' => AxiaTestCase::USER_REP_ID]],
			[AxiaTestCase::USER_SM_ID, ['User.id' => AxiaTestCase::USER_SM_ID]],
			[
				GUIbuilderComponent::GRP_PREFIX . AxiaTestCase::USER_SM_ID,
				[
					[
						'AND' => [
							[
								'OR' => [
									['User.id' => AxiaTestCase::USER_SM_ID],
									['AssociatedUser.associated_user_id' => AxiaTestCase::USER_SM_ID]
								]
							]
						]
					]
				]
			],
		];
	}

/**
 * testGetRepFilterConditionsAsSm
 *
 * @param string $userId User id
 * @param mixed $expected Expected output
 * @test
 * @dataProvider filterConditionsDataAsSm
 * @return void
 */
	public function testGetRepFilterConditionsAsSm($userId, $expected) {
		$this->RolePermissions->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_SM_ID));

		$result = $this->RolePermissions->getRepFilterConditions($userId);
		$this->assertEquals($expected, $result);
	}

/**
 * Gets the data provider for testGetRepFilterConditionsAsSm
 *
 * @return array
 */
	public function filterConditionsDataAsSm() {
		$assosiatedUserCondition = [
			'AND' => [
				[
					'OR' => [
						['User.id' => AxiaTestCase::USER_SM_ID],
						['AssociatedUser.associated_user_id' => AxiaTestCase::USER_SM_ID]
					]
				]
			]
		];

		return [
			[
				null,
				[
					'User.id' => AxiaTestCase::USER_SM_ID,
					$assosiatedUserCondition
				]
			],
			[
				AxiaTestCase::USER_REP_ID,
				[
					'User.id' => AxiaTestCase::USER_REP_ID,
					$assosiatedUserCondition
				]
			],
			[
				AxiaTestCase::USER_SM_ID,
				[
					'User.id' => AxiaTestCase::USER_SM_ID,
					$assosiatedUserCondition
				]
			],
			[
				GUIbuilderComponent::GRP_PREFIX . AxiaTestCase::USER_SM_ID,
				[
					[
						'AND' => [
							[
								'OR' => [
									['User.id' => AxiaTestCase::USER_SM_ID],
									['AssociatedUser.associated_user_id' => AxiaTestCase::USER_SM_ID]
								]
							],
							[
								'OR' => [
									['User.id' => AxiaTestCase::USER_SM_ID],
									['AssociatedUser.associated_user_id' => AxiaTestCase::USER_SM_ID]
								]
							]
						]
					],
				],
			],
		];
	}

/**
 * testGetAllowedReportStatusesNotValid
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Invalid user Id
 * @return void
 */
	public function testGetAllowedReportStatusesNotValid() {
		RolePermissions::getAllowedReportStatuses(null);
	}

/**
 * testGetAllowedReportStatuses
 *
 * @param string $user User id
 * @param mixed $expected Expected output
 * @dataProvider getAllowedReportStatusesData
 * @return void
 */
	public function testGetAllowedReportStatuses($user, $expected) {
		$actual = RolePermissions::getAllowedReportStatuses($user);
		$this->assertEquals($expected, $actual);
	}

/**
 * Gets the data provider for testGetAllowedReportStatuses
 *
 * @return array
 */
	public function getAllowedReportStatusesData() {
		return [
			[
				AxiaTestCase::USER_ADMIN_ID,
				[
					RolePermissions::REPORT_STATUS_ACTIVE,
					RolePermissions::REPORT_STATUS_INACTIVE,
				]
			],
			[AxiaTestCase::USER_SM_ID, [RolePermissions::REPORT_STATUS_ACTIVE]],
			[AxiaTestCase::USER_REP_ID, [RolePermissions::REPORT_STATUS_ACTIVE]],
		];
	}

/**
 * testCommissionReportMatrixNotValid
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Invalid user Id
 * @return void
 */
	public function testCommissionReportMatrixNotValid() {
		RolePermissions::reportPermissionsMatrix(null);
	}

/**
 * testCommissionReportMatrixInstaller
 *
 * @return void
 */
	public function testCommissionReportMatrixInstaller() {
		$actual = RolePermissions::reportPermissionsMatrix(AxiaTestCase::USER_INSTALLER_ID);
		$expected = [
			'commissionMultiple' => [
				'mid',
				'dba',
				'client_id_global',
				'bet',
				'product',
				'install_date',
				'recd_install_sheet',
				'com_month',
				'volume',
				'avg_tkt',
				'items',
				'original_merch_rate',
				'bet_extra_pct',
				'merch_rate',
				'merch_p_i',
				'merch_stmt',
				'rep',
			]
		];
		$this->assertSame($expected, $actual);
	}

/**
 * testCommissionReportMatrixAdminI
 *
 * @return void
 */
	public function testCommissionReportMatrixAdminI() {
		$permissions = RolePermissions::reportPermissionsMatrix(AxiaTestCase::USER_ADMIN_ID);
		$expected = [
			'rep_residuals',
			'sm_residuals',
			'sm2_residuals',
			'partner_residuals',
			'commission',
			'multiple_amount',
			'manager_multiple_amount',
			'manager2_multiple_amount',
			'partner_commission',
			'gross_income',
		];
		$this->assertEquals($expected, Hash::get($permissions, 'income'));
		$this->assertNotEmpty(Hash::get($permissions, 'repAdjustments'));
		$this->assertNotEmpty(Hash::get($permissions, 'commissionReports'));
	}

/**
 * testCommissionReportMatrixAdminI
 *
 * @return void
 */
	public function testCommissionReportMatrixSM() {
		$permissions = RolePermissions::reportPermissionsMatrix(AxiaTestCase::USER_SM_ID);
		$expected = [
			'rep_residuals',
			'sm_residuals',
			'sm2_residuals',
			'partner_residuals',
			'commission',
			'multiple_amount',
			'manager_multiple_amount',
			'gross_income',
		];
		$this->assertEquals($expected, Hash::get($permissions, 'income'));
		$this->assertNotEmpty(Hash::get($permissions, 'netIncome'));
		$this->assertNotEmpty(Hash::get($permissions, 'commissionReports'));
	}

/**
 * testCommissionReportMatrixAdminI
 *
 * @return void
 */
	public function testCommissionReportMatrixRep() {
		$permissions = RolePermissions::reportPermissionsMatrix(AxiaTestCase::USER_REP_ID);
		$expected = [
			'rep_residuals',
			'partner_residuals',
			'commission',
			'multiple_amount',
			'gross_income',
		];
		$this->assertEquals($expected, Hash::get($permissions, 'income'));
		$this->assertNotEmpty(Hash::get($permissions, 'repAdjustments'));
		$this->assertNotEmpty(Hash::get($permissions, 'commissionReports'));
	}

/**
 * testResidualReportMatrixAdminI
 *
 * @return void
 */
	public function testResidualReportMatrixAdminI() {
		$permissions = RolePermissions::reportPermissionsMatrix(AxiaTestCase::USER_ADMIN_ID, ResidualReport::SECTION_RESIDUAL_REPORTS);
		$config = Configure::read('ResidualReportPermissions');
		$expected = $config['Admin I']['residualReports'];
		$this->assertEquals($expected, Hash::get($permissions, 'residualReports'));
	}
/**
 * testResidualReportMatrixAdminII
 *
 * @return void
 */
	public function testResidualReportMatrixRep() {}

/**
 * testResidualReportMatrixAdminII
 *
 * @return void
 */
	public function testResidualReportMatrixSM() {}

/**
 * testResidualReportMatrixAdminII
 *
 * @return void
 */
	public function testResidualReportMatrixSM2() {}

/**
 * testResidualReportMatrixAdminII
 *
 * @return void
 */
	public function testResidualReportMatrixPartner() {}

/**
 * testResidualReportMatrixAdminII
 *
 * @return void
 */
	public function testResidualReportMatrixReseller() {}

/**
 * testResidualReportMatrixAdminII
 *
 * @return void
 */
	public function testResidualReportMatrixReferrer() {}

/**
 * test
 *
 * @covers RolePermissions::filterReportData()
 * @covers RolePermissions::_filterRow()
 * @return void
 */
	public function testFilterReportDataIncome() {
		$reportData = [
			'rep_residuals' => null,
			'sm_residuals' => null,
			'sm2_residuals' => null,
			'partner_residuals' => null,
			'commission' => 450.95,
			'partner_commission' => 0,
			'gross_income' => 450.95
		];
		$result = RolePermissions::filterReportData(CommissionReport::SECTION_INCOME, AxiaTestCase::USER_ADMIN_ID, $reportData);
		$this->assertSame($reportData, $result);

		$expected = [
			'rep_residuals' => null,
			'partner_residuals' => null,
			'commission' => 450.95,
			'gross_income' => 450.95
		];
		$result = RolePermissions::filterReportData(CommissionReport::SECTION_INCOME, AxiaTestCase::USER_REP_ID, $reportData);
		$this->assertSame($expected, $result);
	}

/**
 * test
 *
 * @covers RolePermissions::filterReportData()
 * @covers RolePermissions::_filterRow()
 * @return void
 */
	public function testFilterReportDataCommissionMultiple() {
		$reportData = [
			'commissionMultiples' => [
				[
					'mid' => '76411100',
					'dba' => 'Test-dba-1',
					'bet' => 'bet-table-1',
					'bus_level' => 'MOTO',
					'product' => 'Bankcard',
					'sub_date' => '2011-06-01',
					'app_date' => '2011-06-02',
					'install_date' => '2011-06-13',
					'recd_install_sheet' => '2011-06-15',
					'com_month' => '2011-06-15',
					'volume' => '21000.0000',
					'avg_tkt' => '93.0000',
					'items' => '0.0000',
					'merch_rate' => '0.2500',
					'merch_p_i' => '0.0800',
					'merch_stmt' => '0.0000',
					'multiple' => '0.0800',
					'multiple_amount' => '0',
					'resid_gross_profit' => -50.5,
					'rep_rate' => '0.0000',
					'rep_p_i' => '0.0650',
					'rep_stmt' => '4.7500',
					'rep_pct_of_gross' => '0',
					'rep_gross_profit' => '0',
					'rep_residual_gross_profit' => -124,
					'rep_profit_pct' => null,
					'rep_profit' => null
				],
				[
					'mid' => '42402895',
					'dba' => 'Test-dba-2',
					'bet' => 'bet-table-2',
					'bus_level' => 'Restaurant',
					'product' => 'Bankcard',
					'sub_date' => '2011-06-15',
					'app_date' => '2011-06-15',
					'install_date' => '2011-06-25',
					'recd_install_sheet' => '2011-06-24',
					'com_month' => '2011-06-25',
					'volume' => '39000.0000',
					'avg_tkt' => '34.0000',
					'items' => '0.0000',
					'merch_rate' => '2.1500',
					'merch_p_i' => '0.0700',
					'merch_stmt' => '0.0000',
					'multiple' => '0.0700',
					'multiple_amount' => '0',
					'resid_gross_profit' => 44.44,
					'rep_rate' => '1.5600',
					'rep_p_i' => '0.1650',
					'rep_stmt' => '4.7500',
					'rep_pct_of_gross' => '0',
					'rep_gross_profit' => '0',
					'rep_residual_gross_profit' => 66.66,
					'rep_profit_pct' => null,
					'rep_profit' => null
				],
			],
			'commissionMultipleTotals' => [
				'volume' => 600000,
				'multiple_amount' => 0,
				'resid_gross_profit' => 44.44,
				'rep_gross_profit' => 0,
				'rep_residual_gross_profit' => -57.34,
				'rep_profit' => 0,
			]
		];
		$result = RolePermissions::filterReportData(CommissionPricing::REPORT_COMMISION_MULTIPLE, AxiaTestCase::USER_REP_ID, $reportData['commissionMultiples']);

		$this->assertSame($reportData['commissionMultiples'], $result);

		$result = RolePermissions::filterReportData(CommissionPricing::REPORT_COMMISION_MULTIPLE, AxiaTestCase::USER_REP_ID, $reportData['commissionMultipleTotals']);
		$this->assertSame($reportData['commissionMultipleTotals'], $result);
	}
}
