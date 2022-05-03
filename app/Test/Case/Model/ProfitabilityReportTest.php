<?php
App::uses('ProfitabilityReport', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('File', 'Utility');

/**
 * ProfitabilityReport Test Case
 */
class ProfitabilityReportTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProfitabilityReport = ClassRegistry::init('ProfitabilityReport');
		$this->ResidualReport = ClassRegistry::init('ResidualReport');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProfitabilityReport);
		unset($this->ResidualReport);

		parent::tearDown();
	}

/**
 * testGetReportDataExceptionThrown method
 *
 * @covers ProfitabilityReport::getReportData
 * @expectedException Exception
 * @expectedExceptionMessage "ProfitabilityReport" data must be an array
 * @return void
 */
	public function testGetReportDataExceptionThrown() {
		$this->ProfitabilityReport->getReportData('invalid argument');
	}

/**
 * testGetReportData method
 *
 * @covers ProfitabilityReport::getReportData
 * @return void
 */
	public function testGetReportData() {
		$profitReports = [
			[
				'Merchant' => [
					'merchant_mid' => '3948000030002785',
					'merchant_dba' => 'Square One Internet Solutions',
				],
				'ProfitabilityReport' => [
					'year' => '2050',
					'month' => '1',
					'net_sales_item_count' => '10',
					'net_sales_vol' => '100',
					'gross_sales_item_count' => '10',
					'gross_sales_vol' => '100',
					'total_income' => '1000',
					'card_brand_cogs' => '5',
					'processor_cogs' => '5',
					'sponsor_bank_cogs' => '5',
					'ithree_monthly_cogs' => '10',
					'axia_net_income' => '100',
					'cost_of_goods_sold' => '10',
					'axia_gross_profit' => '120',
					'total_residual_comp' => '10',
					'axia_net_profit' => '300',
				],
				'ResidualReport' => [
					'pr_total_rep_gp' => '',
					'pr_total_partner_rep_gp' => '',
					'pr_total_partner_gp' => '',
					'pr_total_sm_gp' => '',
					'pr_total_sm2_gp' => '',
					'pr_total_referrer_gp' => '',
					'pr_total_reseller_gp' => '',
				]
			]
		];

		$actual = $this->ProfitabilityReport->getReportData($profitReports);
		$expectedTotals = [
			'total_income' => '1000.000',
			'axia_net_income' => '100.000',
			'cost_of_goods_sold' => '10.000',
			'axia_gross_profit' => '120.000',
			'total_residual_comp' => '10.000',
			'axia_net_profit' => '300.000'
		];
		$this->assertSame($expectedTotals, $actual['totals']);
	}

/**
 * testImportData_DataAlreadyExistsException method
 *
 * @covers ProfitabilityReport::importData
 * @expectedException Exception
 * @expectedExceptionMessage Import failed! Data for Jan 2100 already exists.
 * @return void
 */
	public function testImportData_DataAlreadyExistsException() {
		$requestData = [
			'ProfitabilityReport' => [
				'year' => ['year' => 2100],
				'month' => ['month' => 1],
				'serverside_processing' => false,
				'profitabilityReportFile' => [
					'tmp_name' => '',
					'name' => ''
				]
			]
		];
		$this->ProfitabilityReport->create();
		$this->ProfitabilityReport->save(['year' => 2100, 'month' => 1, 'merchant_id' => CakeText::uuid()], ['validate' => false]);
		$this->ProfitabilityReport->importData($requestData, false);
	}

/**
 * testImportData_FileUploadFailedException method
 *
 * @covers ProfitabilityReport::importData
 * @expectedException Exception
 * @expectedExceptionMessage File upload failed! Upload method is not allowed.
 * @return void
 */
	public function testImportData_FileUploadFailedException() {
		$requestData = [
			'ProfitabilityReport' => [
				'year' => ['year' => null],
				'month' => ['month' => null],
				'serverside_processing' => false,
				'profitabilityReportFile' => [
					'tmp_name' => '',
					'name' => ''
				]
			]
		];
		$this->ProfitabilityReport->importData($requestData, false);
	}

/**
 * testfindProfitability method
 *
 * @covers ProfitabilityReport::_findProfitability
 * @return void
 */
	public function testfindProfitability() {
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$tstData= [
			'merchant_id' => $merchantId,
			'year' => 2030,
			'month' => 1,
		];
		$this->ProfitabilityReport->create();
		$this->ProfitabilityReport->save($tstData);
		$actual = $this->ProfitabilityReport->find('profitability', ['conditions' => ['year' => 2030]]);
		$expected = [
			[
				'ProfitabilityReport' => [
					'id' => '5db20a98-e470-4df9-9566-488934627ad4',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'year' => 2030,
					'month' => 1,
				],
				'Merchant' => [
					'merchant_mid' => '3948000030003045',
					'merchant_dba' => 'Refer Aloha, LLC'
				],
			]
		];
		$this->assertSame($expected[0]['ProfitabilityReport']['merchant_id'], $actual[0]['ProfitabilityReport']['merchant_id']);
		$this->assertSame($expected[0]['ProfitabilityReport']['year'], $actual[0]['ProfitabilityReport']['year']);
		$this->assertSame($expected[0]['ProfitabilityReport']['month'], $actual[0]['ProfitabilityReport']['month']);
		$this->assertSame($expected[0]['Merchant']['merchant_mid'], $actual[0]['Merchant']['merchant_mid']);
	}

/**
 * testIsValidCsvContent method
 *
 * @covers ProfitabilityReport::isValidCsvContent
 * @return void
 */
	public function testIsValidCsvContent() {
		$filePath = APP."tmp/validFile.csv";
		$fp = @fopen($filePath, 'w');
		fclose($fp);
		$actual = $this->ProfitabilityReport->isValidCsvContent($filePath);
		$this->assertTrue($actual);
		unlink($filePath);

		$filePath = APP."tmp/invalidFile.csv";
		$fp2 = @fopen($filePath, 'w');
		$content = "MID, DBA\n123456,Fake DBA";
		@fwrite($fp2, $content);
		fclose($fp2);
		$actual = $this->ProfitabilityReport->isValidCsvContent($filePath);
		$this->assertFalse($actual);

	}

/**
 * testOrConditions method
 *
 * @covers ProfitabilityReport::orConditions
 * @return void
 */
	public function testOrConditions() {
		$data = ['dba_mid' => 'Some MID'];
		$actual = $this->ProfitabilityReport->orConditions($data);
		$expected = [
			'OR' => [
				'Merchant.merchant_mid ILIKE' => "%Some MID%",
				'Merchant.merchant_dba ILIKE' => "%Some MID%",
			]
		];
	}


/**
 * testGetExistingList method
 *
 * @return void
 */
	public function testGetExistingList() {
		$tstData = [
			['merchant_id' => CakeText::uuid(), 'year' => 2018, 'month' => 1],
			['merchant_id' => CakeText::uuid(), 'year' => 2018, 'month' => 1],
			['merchant_id' => CakeText::uuid(), 'year' => 2018, 'month' => 2],
			['merchant_id' => CakeText::uuid(), 'year' => 2018, 'month' => 2],
		];
		$this->ProfitabilityReport->saveMany($tstData);
		$result = $this->ProfitabilityReport->getExistingList(2018, null);
		$this->assertCount(2, $result);
		$this->assertSame([1,2], Hash::extract($result, '{n}.ProfitabilityReport.month'));
		$this->assertSame([2018], array_unique(Hash::extract($result, '{n}.ProfitabilityReport.year')));
		$this->ProfitabilityReport->query('TRUNCATE profitability_reports');

		$tstData[] = ['merchant_id' => CakeText::uuid(), 'year' => 2019, 'month' => 2];
		$this->ProfitabilityReport->saveMany($tstData);
		$result = $this->ProfitabilityReport->getExistingList(2019, null);
		$this->assertSame([2], Hash::extract($result, '{n}.ProfitabilityReport.month'));
		$this->assertSame([2019], array_unique(Hash::extract($result, '{n}.ProfitabilityReport.year')));
	}

/**
 * testProcessDataFile method
 *
 * @dataProvider providerTestProcessDataFile
 * @return void
 */
	public function testProcessDataFile($csvData, $residualData) {
		//Save all test residualData
		$this->ResidualReport->saveMany($residualData);
		$merchantId = $residualData[0]['merchant_id'];
		$year = $residualData[0]['r_year'];
		$month = $residualData[0]['r_month'];

		//save test CSV data in APP . "tmp/tests" folder
		$fileName = 'test_profit_data';
		$savePath = APP . "tmp/tests/" . $fileName;
		$fp = fopen($savePath, 'w+');
		fwrite($fp, $csvData);
		fclose($fp);
		//process test csvData
		//This procedure also deletes test CSV data in tmp folder
		$response = $this->ProfitabilityReport->processDataFile($year, $month, $savePath);
		$this->assertTrue($response['recordsAdded']);
		$this->assertEmpty(Hash::get($response, 'errors'));

		$actualData = $this->ProfitabilityReport->find('all', ['conditions' => ['merchant_id' => $merchantId]]);
		//Only one record should be generated per merchant regardless of how many residual report records
		//where used to generate the ProfitabilityReport data
		$this->assertCount(1, $actualData);
		//Check 2020/01 expected total_residual_comp sum for 3948000030002785
		if ($merchantId === '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040') {
			//the sum of all users residual gross profit ecluding non-credit products
			$this->assertEquals(14, $actualData[0]['ProfitabilityReport']['total_residual_comp']);
		} else {
			//Check 2020/02 expected total_residual_comp sum for 4541619030000153
			//the sum of all users residual gross profit ecluding non-credit products
			$this->assertEquals(70, $actualData[0]['ProfitabilityReport']['total_residual_comp']);
		}

		//===========================Check all other calculations=============================//

		//axia_net_income =  total_income - card_brand_cogs
		$expectedAxNetIncome = bcsub(441.24, 345.20, 3);
		$actual = $actualData[0]['ProfitabilityReport']['axia_net_income'];
		$this->assertEquals($expectedAxNetIncome, $actual);
		
		//cost_of_goods_sold =  total_income - card_brand_cogs
		$expectedCOGS = bcadd(bcadd(2.06, 1.65, 3), 2.50, 3);
		$actual = $actualData[0]['ProfitabilityReport']['cost_of_goods_sold'];
		$this->assertEquals($expectedCOGS, $actual);

		//axia_gross_profit =  axia_net_income - cost_of_goods_sold - ResidualReport.partner_gross_profit<TOTAL FOR ALL CREDIT PRODUCTS PRODUCTS>
		$expectedAxGp = bcsub(bcsub($expectedAxNetIncome, $expectedCOGS, 3), $residualData[0]['partner_gross_profit'] + $residualData[1]['partner_gross_profit'], 3);
		$actual = $actualData[0]['ProfitabilityReport']['axia_gross_profit'];
		$this->assertEquals($expectedAxGp, $actual);
		
		//expectedAxGp =  axia_net_income - cost_of_goods_sold 
		$expectedAxGp = bcsub($expectedAxGp, $actualData[0]['ProfitabilityReport']['total_residual_comp'], 3);
		$actual = $actualData[0]['ProfitabilityReport']['axia_net_profit'];
		$this->assertEquals($expectedAxGp, $actual);

	}
/**
 * Provider for testProcessDataFile
 *
 * @dataProvider
 * @return void
 */
	public function providerTestProcessDataFile() {
		return [
			[
				//*******Data param
				"Header,Header,Header,Header,Header,Header,Header,Header,Header,Header,Header\n" .
				"3948000030002785,Some Merchant MID,2,15000.00,2,15000, 441.24 ,345.20,2.06, 1.65 , 2.50\n",
				// Residual report test data for merchant 3948000030002785 = 3bc3ac07-fa2d-4ddc-a7e5-680035ec1040
				[
					[
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'user_id' => CakeText::uuid(), //arbitrary user id
						//MasterCard sales is considered a credit card product
						'products_services_type_id' => '28dd4748-9699-41b9-9391-2524d3941018',
						'r_year' => 2020,
						'r_month' => 1,
						'rep_gross_profit' => 1,
						'partner_gross_profit' => 1,
						'partner_rep_gross_profit' => 1,
						'manager_gross_profit' => 1,
						'manager2_gross_profit' => 1,
						'referrer_gross_profit' => 1,
						'reseller_gross_profit' => 1
					],
					[
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'user_id' => CakeText::uuid(), //arbitrary user id
						//Visa sales is considered a credit card product
						'products_services_type_id' => 'e6d8f040-9963-4539-ab75-3e19f679de16',
						'r_year' => 2020,
						'r_month' => 1,
						'rep_gross_profit' => 1,
						'partner_gross_profit' => 1,
						'partner_rep_gross_profit' => 1,
						'manager_gross_profit' => 1,
						'manager2_gross_profit' => 1,
						'referrer_gross_profit' => 1,
						'reseller_gross_profit' => 1
					],
				],
			],
			[
				//*******Data param
				"Header,Header,Header,Header,Header,Header,Header,Header,Header,Header,Header\n" .
				"4541619030000153,Some Merchant MID,2,15000.00,2,15000, 441.24 ,345.20,2.06, 1.65 , 2.50",
				// Residual report test data for merchant 4541619030000153 = 00000000-0000-0000-0000-000000000005
				[
					[
						'merchant_id' => '00000000-0000-0000-0000-000000000005',
						'user_id' => CakeText::uuid(), //arbitrary user id
						//MasterCard sales is considered a credit card product
						'products_services_type_id' => '28dd4748-9699-41b9-9391-2524d3941018',
						'r_year' => 2020,
						'r_month' => 2,
						'rep_gross_profit' => 5,
						'partner_gross_profit' => 5,
						'partner_rep_gross_profit' => 5,
						'manager_gross_profit' => 5,
						'manager2_gross_profit' => 5,
						'referrer_gross_profit' => 5,
						'reseller_gross_profit' => 5
					],
					[
						'merchant_id' => '00000000-0000-0000-0000-000000000005',
						'user_id' => CakeText::uuid(), //arbitrary user id
						//Visa sales is considered a credit card product
						'products_services_type_id' => 'e6d8f040-9963-4539-ab75-3e19f679de16',
						'r_year' => 2020,
						'r_month' => 2,
						'rep_gross_profit' => 5,
						'partner_gross_profit' => 5,
						'partner_rep_gross_profit' => 5,
						'manager_gross_profit' => 5,
						'manager2_gross_profit' => 5,
						'referrer_gross_profit' => 5,
						'reseller_gross_profit' => 5
					],
					[
						'merchant_id' => '00000000-0000-0000-0000-000000000005',
						'user_id' => CakeText::uuid(), //arbitrary user id
						//Arbitrary product NOT considered a credit card product and should not archive
						'products_services_type_id' => CakeText::uuid(),
						'r_year' => 2020,
						'r_month' => 2,
						'rep_gross_profit' => 5,
						'partner_gross_profit' => 5,
						'partner_rep_gross_profit' => 5,
						'manager_gross_profit' => 5,
						'manager2_gross_profit' => 5,
						'referrer_gross_profit' => 5,
						'reseller_gross_profit' => 5
					],
					[
						'merchant_id' => '00000000-0000-0000-0000-000000000005',
						'user_id' => CakeText::uuid(), //arbitrary user id
						//Arbitrary product NOT considered a credit card product and should not archive
						'products_services_type_id' => CakeText::uuid(),
						'r_year' => 2020,
						'r_month' => 2,
						'rep_gross_profit' => 5,
						'partner_gross_profit' => 5,
						'partner_rep_gross_profit' => 5,
						'manager_gross_profit' => 5,
						'manager2_gross_profit' => 5,
						'referrer_gross_profit' => 5,
						'reseller_gross_profit' => 5
					],
				],
			],
		];
	}

}
