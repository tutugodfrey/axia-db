<?php
App::uses('ImportedDataCollection', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ImportedDataCollection Test Case
 *
 */
class ImportedDataCollectionTest extends AxiaTestCase {
	/**
	 * This test file will be created and populated
	 * on the fly with test data.
	 */
	const TST_CSV_FILE = 'IDC_uploadTst.csv';
	const TST_CSV_PATH = APP . 'Test' . DS . 'Tmp' . DS;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ImportedDataCollection = ClassRegistry::init('ImportedDataCollection');
		//Setup initial CSV file for testing
		$testData = array (
		 	array('headerA'),
		 	array('789'),
		);
		$this->__saveAsCsv($testData);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ImportedDataCollection);
		try {
			unlink(self::TST_CSV_PATH . self::TST_CSV_FILE);
		} catch(Exception $e){}
		parent::tearDown();
	}

/**
 * testGetFieldMap
 *
 * @covers ImportedDataCollection::getFieldMap()
 * @return void
 */
	public function testGetFieldMap(){
		//Test 1 - check returned map is matches the contents of the emulated csvheaders param
		$csvHeaders = ['Business Type', 'This Header is not recognized', 'USAePay'];
		$expected = ['Business Type' => 'merchant_bustype', 'This Header is not recognized' => null, 'USAePay' => 'is_usa_epay'];
		$actual = $this->ImportedDataCollection->getFieldMap($csvHeaders);
		$this->assertSame($expected, $actual);

		//Test 2 - full map returned when no parameters are pased
		foreach ($this->ImportedDataCollection->csvFieldMap as $arr) {
			$expected = array_merge($expected, $arr);
		}
		$actual = $this->ImportedDataCollection->getFieldMap();
		$this->assertSame(sort($expected, SORT_STRING), sort($actual, SORT_STRING));
		$this->assertSame(count(Hash::extract($this->ImportedDataCollection->csvFieldMap, '{s}.{s}')), count($actual));
	}

/**
 * testValidUploadFile
 *
 * @covers ImportedDataCollection::validUploadFile()
 * @return void
 */
	public function testValidUploadFile(){
		//Test error handling
		$actual = $this->ImportedDataCollection->validUploadFile('Invalid file', 'Invalid file');
		$this->assertSame('Invalid file! Verify file extension is .csv', $actual['errors'][0]);

		//Inaccessible file error
		$actual = $this->ImportedDataCollection->validUploadFile('not_real', 'not_real.csv');
		$this->assertSame('ERROR: Unable to open file!', $actual['errors'][0]);

		//Ivalid file Content Error missing MID
		$tstFile = self::TST_CSV_PATH . self::TST_CSV_FILE;
		$testData = array (
		 	array('headerA'),
		 	array('789'),
		);
		$this->__saveAsCsv($testData);
		$actual = $this->ImportedDataCollection->validUploadFile($tstFile, self::TST_CSV_FILE);
		$this->assertSame('The "MID" column is missing from this file!', $actual['errors'][0]);

		//Ivalid file Content Error Missing Expected Headers
		$testData = array (
		 	array('MID', 'headerA'),
		 	array('789', 'Blah'),
		);
		$this->__saveAsCsv($testData);
		$actual = $this->ImportedDataCollection->validUploadFile($tstFile, self::TST_CSV_FILE);
		$this->assertSame('Invalid file content! The headers do not match any of the expected headers.', $actual['errors'][0]);
		
		//Valid file no errors
		$testData = array (
		 	array('MID', 'Reseller/Direct'),
		 	array('123901239', 'Magic'),
		);
		$this->__saveAsCsv($testData);
		$actual = $this->ImportedDataCollection->validUploadFile($tstFile, self::TST_CSV_FILE);
		$this->assertSame(2, $actual['row_count']);
		$this->assertEmpty($actual['errors']);
	}

/**
 * testGetAssociatedModelData
 *
 * @covers ImportedDataCollection::getAssociatedModelData()
 * @return void
 */
	public function testGetAssociatedModelData(){
		//Test 1- Verify returned data contains only Merchant and ImportedDataCollection data
		//and verify data types and foreing keys are properly detected and set
		$mid = '3948000030003045';
		$csvFieldMap = [
			'MID' => 'id',
			'Reseller/Direct' => 'source_of_sale',
			'Gateway 1' => 'gw_n1_id',
			'Gateway 1 Item Count' => 'gw_n1_item_count',
			'Gateway 1 Volume' => 'gw_n1_vol',
			'Gateway 2' => 'gw_n2_id',
			'PF Only' => 'is_pf_only',
			'Closed/Won Date' => 'sf_closed_date',
			SalesForce::OPPTY_ID => 'value',
			SalesForce::OPPTY_NAME => 'value'
		];
		$csvData = [
			$mid,
			'Reseller',
			'USA ePay', //foreing key
			'30', // integer type
			'$250.99', // decimal type with unacceptable character dollar sign
			'TGate', // foreing key
			'true', //boolean
			'01/01/2022', //Date type
			'OPPTID123456ABCDEF',
			'Test Salesforce Opportinity uploaded'
		];
		$tmpRefParam = [];
		$actual = $this->ImportedDataCollection->getAssociatedModelData($mid, $csvData, $csvFieldMap, 1, 2022, $tmpRefParam);

		$Gateway = ClassRegistry::init('Gateway');
		//Check Foreing keys are set
		$this->assertTrue($Gateway->exists($actual['ImportedDataCollection']['gw_n1_id']));
		$this->assertTrue($Gateway->exists($actual['ImportedDataCollection']['gw_n2_id']));
		$this->assertTrue($this->ImportedDataCollection->Merchant->exists($actual['Merchant']['id']));

		//Check data types
		$this->assertTrue(is_numeric($actual['ImportedDataCollection']['gw_n1_item_count']));
		$this->assertTrue(is_numeric($actual['ImportedDataCollection']['gw_n1_vol']));
		$this->assertTrue(gettype($actual['Merchant']['is_pf_only']), 'boolean');
		//special case date field indicates whether opportunity was won or lost
		//In this case the Closed/Won Date was set in the csv file which implies a won
		//Check sf_opportunity_won is set to true
		$this->assertSame($actual['ImportedDataCollection']['sf_closed_date'], '2022-01-01');
		$this->assertTrue($actual['ImportedDataCollection']['sf_opportunity_won']);
		$this->assertNotEmpty($actual['ExternalRecordField']);
		$this->assertCount(2, $actual['ExternalRecordField']);
		$actualExtRec = [$actual['ExternalRecordField'][0]['value'], $actual['ExternalRecordField'][1]['value']];
		$this->assertContains('OPPTID123456ABCDEF', $actualExtRec);
		$this->assertContains('Test Salesforce Opportinity uploaded', $actualExtRec);
	}

/**
 * testUpsertUpload
 *
 * @covers ImportedDataCollection::upsertUpload()
 * @return void
 */
	public function testUpsertUpload(){
		$testData = [
			[
				'MID',
				'Reseller/Direct',
				'Gateway 1',
				'Gateway 1 Item Count',
				'Gateway 1 Volume',
				'Gateway 2',
				'PF Only',
				'Closed/Won Date',
				SalesForce::OPPTY_ID,
				SalesForce::OPPTY_NAME,
			],
			[
				'3948000030003045',
				'Reseller',
				'USA ePay', //foreing key
				'30', // integer type
				'$250.99', // decimal type with unacceptable character dollar sign
				'TGate', // foreing key
				'true', //boolean
				'01/01/2022', //Date type
				'OPPTID123456ABCDEF',
				'Test Salesforce Opportinity uploaded'
			]
		];
		$this->__saveAsCsv($testData);
		$params = [
			'month' => 1,
			'year' => 2022,
			'file_path' => self::TST_CSV_PATH . self::TST_CSV_FILE,
			'is_queued_job' => false
		];
		$actual = $this->ImportedDataCollection->upsertUpload($params);

		$this->assertTrue($actual['result']);
		$this->assertTrue($actual['recordsAdded']);
		$actual = $this->ImportedDataCollection->getExistingData('3948000030003045', $params['month'], $params['year']);

		$Gateway = ClassRegistry::init('Gateway');
		//Check Foreing keys are set
		$this->assertTrue($Gateway->exists($actual['ImportedDataCollection']['gw_n1_id']));
		$this->assertTrue($Gateway->exists($actual['ImportedDataCollection']['gw_n2_id']));

		//Check data types
		$this->assertEquals(30, $actual['ImportedDataCollection']['gw_n1_item_count']);
		$this->assertEquals(250.99, $actual['ImportedDataCollection']['gw_n1_vol']);
		$this->assertTrue($actual['Merchant']['is_pf_only']);
		//special case date field indicates whether opportunity was won or lost
		//In this case the Closed/Won Date was set in the csv file which implies a won
		//Check sf_opportunity_won is set to true
		$this->assertSame($actual['ImportedDataCollection']['sf_closed_date'], '2022-01-01');
		$this->assertNotEmpty($actual['ExternalRecordField']);
		$this->assertCount(2, $actual['ExternalRecordField']);
		$actualExtRec = [$actual['ExternalRecordField'][0]['value'], $actual['ExternalRecordField'][1]['value']];
		$this->assertContains('OPPTID123456ABCDEF', $actualExtRec);
		$this->assertContains('Test Salesforce Opportinity uploaded', $actualExtRec);
	}

/**
 * testGetData
 *
 * @covers ImportedDataCollection::getData()
 * @return void
 */
	public function testGetData() {
		$organizations = $this->ImportedDataCollection->Merchant->Organization->find('list');
		$regions = $this->ImportedDataCollection->Merchant->Region->find('list');
		$subregions = $this->ImportedDataCollection->Merchant->Subregion->find('list');
		$partners = $this->ImportedDataCollection->Merchant->User->getByRole(User::ROLE_PARTNER);
		//use roll up layout when this is true
		$rollUpLayout = false;
		$headersMeta = [];
		$subsetIndices = null;
		$reportData = [];
		$expected = compact(
			'rollUpLayout',
			'partners',
			'organizations',
			'regions',
			'subregions',
			'reportData',
			'subsetIndices'
		);
		$filterParams = [];
		$actual = $this->ImportedDataCollection->getData($reportData, $filterParams, false);
		unset($actual['headersMeta']);
		$this->assertSame($expected, $actual);
	}

/**
 * testGetOrderedSameDataSubsetIndexes
 *
 * @covers ImportedDataCollection::getOrderedSameDataSubsetIndexes()
 * @return void
 */
	public function testGetOrderedSameDataSubsetIndexes() {
		$mockedReportData = [
			['Merchant' => ['merchant_mid' => 123]],
			['Merchant' => ['merchant_mid' => 987]],
			['Merchant' => ['merchant_mid' => 123]],
			['Merchant' => ['merchant_mid' => 987]],
			['Merchant' => ['merchant_mid' => 654]],
		];
		$expected = [0, 2, '<END>', 1, 3, '<END>', 4, '<END>'];
		$actual = $this->ImportedDataCollection->getOrderedSameDataSubsetIndexes($mockedReportData);
		$this->assertSame($expected, $actual);

		$mockedReportData = [
			['Merchant' => ['merchant_mid' => 999]],
			['Merchant' => ['merchant_mid' => 123]],
			['Merchant' => ['merchant_mid' => 123]],
			['Merchant' => ['merchant_mid' => 123]],
		];
		$expected = [0, '<END>', 1, 2, 3, '<END>'];
		$actual = $this->ImportedDataCollection->getOrderedSameDataSubsetIndexes($mockedReportData);
		$this->assertSame($expected, $actual);

		$mockedReportData = [
			['Merchant' => ['merchant_mid' => 123]],
			['Merchant' => ['merchant_mid' => 456]],
			['Merchant' => ['merchant_mid' => 789]],
			['Merchant' => ['merchant_mid' => 111]],
		];
		$expected = [0, '<END>', 1, '<END>',  2, '<END>', 3, '<END>'];
		$actual = $this->ImportedDataCollection->getOrderedSameDataSubsetIndexes($mockedReportData);
		$this->assertSame($expected, $actual);

	}

/**
 * _saveAsCsv 
 * Saves array of data as CSV file where each sub-array entry becomes a CSV row
 * 
 * @param  array  $testData multidim array matching this struncture:
 * $testData = array (
		array('headerA', 'headerB', 'HeaderC', <...>),
		array('123', '456', '789', <...>),
	);
 * @return void
 */
	private function __saveAsCsv(array $testData) {
		$tstFile = self::TST_CSV_PATH . self::TST_CSV_FILE;
		$fp = fopen($tstFile, 'w');

		foreach ($testData as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);
	}
}
