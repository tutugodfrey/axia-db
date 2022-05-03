<?php
App::uses('AxiaTestCase', 'Test');

/**
 * LastDepositReport Test Case
 *
 */
class LastDepositReportTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->LastDepositReport = ClassRegistry::init('LastDepositReport');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->LastDepositReport);
		parent::tearDown();
	}

/**
 * testImportFromCsvUpload method
 *
 * @return void
 */
	public function testImportFromCsvUpload() {
		$data['LastDepositReport'] = [
			'file' => [
				'tmp_name' => APP . 'tmp' . DS . 'sage1.csv',
				'error' => false,
				'name' => 'sage1.csv',
			]
		];
		$merchantData = [
			'Merchant' => [
				'id' => 'id',
				'merchant_dba' => 'dba',
			],
			'LastDepositReport' => [
				'id' => 'lastReportId'
			]
		];
		$this->LastDepositReport = $this->getMockForModel('LastDepositReport', ['_getCurrentUser', 'set', 'importCsv']);
		$this->LastDepositReport->expects($this->once())
				->method('importCsv')
				->with($data['LastDepositReport']['file']['tmp_name'], [], true)
				->will($this->returnValue(true));
		$this->LastDepositReport->Merchant = $this->_mockMerchant($merchantData);
		$this->LastDepositReport->data = $data;
		$result = $this->LastDepositReport->importFromCsvUpload($data, '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6');
		$this->assertTrue($result);
	}

/**
 * testBeforeImport method
 *
 * @return void
 */
	public function testBeforeImportSageExistingLastDeposit() {
		$oldMid = '\'oldMerchId';
		$data['LastDepositReport'] = array(
			'MerchID' => $oldMid,
			'LastDepositDate' => '29/01/2015',
			'InitialMonthlyVolume' => '7000',
			'SomeOtherFieldNotUsed' => 'not used',
		);
		$merchantData = array(
			'Merchant' => array(
				'id' => 'id',
				'merchant_dba' => 'dba',
			),
			'LastDepositReport' => array(
				'id' => 'lastReportId'
			)
		);
		$this->LastDepositReport = $this->getMockForModel('LastDepositReport', array('_getCurrentUser'));
		$this->LastDepositReport->expects($this->any())
				->method('_getCurrentUser')
				->will($this->returnValue('currentUserValue'));
		$this->LastDepositReport->Merchant = $this->_mockMerchant($merchantData);
		$result = $this->LastDepositReport->beforeImport($data);
		$expected = array(
			'LastDepositReport' => array(
				'merchant_id' => 'id',
				'merchant_id_old' => 'oldMerchId',
				'merchant_dba' => 'dba',
				'id' => 'lastReportId',
				'last_deposit_date' => '29/01/2015',
				'user_id' => 'currentUserValue',
				'monthly_volume' => '7000'
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testBeforeImport method
 *
 * @return void
 */
	public function testBeforeImportSageNewLastDeposit() {
		$oldMid = '\'oldMerchId';
		$data['LastDepositReport'] = array(
			'MerchID' => $oldMid,
			'LastDepositDate' => '29/01/2015',
			'InitialMonthlyVolume' => '7000',
			'SomeOtherFieldNotUsed' => 'not used',
		);
		$merchantData = array(
			'Merchant' => array(
				'id' => 'id',
				'merchant_dba' => 'dba',
			),
		);
		$this->LastDepositReport = $this->getMockForModel('LastDepositReport', array('_getCurrentUser'));
		$this->LastDepositReport->expects($this->any())
				->method('_getCurrentUser')
				->will($this->returnValue('currentUserValue'));
		$this->LastDepositReport->Merchant = $this->_mockMerchant($merchantData);
		$result = $this->LastDepositReport->beforeImport($data);
		$expected = array(
			'LastDepositReport' => array(
				'merchant_id' => 'id',
				'merchant_id_old' => 'oldMerchId',
				'merchant_dba' => 'dba',
				'id' => null,
				'last_deposit_date' => '29/01/2015',
				'user_id' => 'currentUserValue',
				'monthly_volume' => '7000'
			)
		);
		$this->assertEquals($expected, $result);
	}
/**
 * testBeforeImportUsaEpayNewLastDeposit method
 *
 * @return void
 */
	public function testBeforeImportUsaEpayNewLastDeposit() {
		$oldMid = '\'oldMerchId';
		$data['LastDepositReport'] = array(
			'ID' => $oldMid,
			'Sales' => 125,
			'Amount' => '12300',
			'SomeOtherFieldNotUsed' => 'not used',
		);
		$merchantData = array(
			'Merchant' => array(
				'id' => 'id',
				'merchant_dba' => 'dba',
			),
		);
		$this->LastDepositReport = $this->getMockForModel('LastDepositReport', array('_getCurrentUser'));
		$this->LastDepositReport->expects($this->any())
				->method('_getCurrentUser')
				->will($this->returnValue('currentUserValue'));
		$this->LastDepositReport->Merchant = $this->_mockMerchant($merchantData);
		$result = $this->LastDepositReport->beforeImport($data);
		$expected = array(
			'LastDepositReport' => array(
				'merchant_id' => 'id',
				'merchant_id_old' => 'oldMerchId',
				'merchant_dba' => 'dba',
				'id' => null,
				'last_deposit_date' => date('Y-m-d', strtotime('-1 day')),
				'user_id' => 'currentUserValue',
				'monthly_volume' => '12300',
				'sales_num' => 125
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * Return a merchant model mock
 *
 * @param array $merchantData merchant data
 * @return Merchant
 */
	protected function _mockMerchant($merchantData) {
		$mock = $this->getMockForModel('Merchant', array('find'));
		$mock->expects($this->any())
				->method('find')
				->with('first', array(
					'fields' => array(
						'Merchant.id',
						'Merchant.merchant_dba',
						'LastDepositReport.id'
					),
					'conditions' => array(
						'Merchant.merchant_mid' => 'oldMerchId',
					),
					'contain' => array(
						'LastDepositReport'
					)
				))
				->will($this->returnValue($merchantData));
		return $mock;
	}

/**
 * testBeforeImport method
 *
 * @return void
 */
	public function testBeforeImportTsysExistingLastDeposit() {
		$oldMid = 'oldMerchId';
		$data['LastDepositReport'] = array(
			'Merchant ID' => $oldMid,
			'Date of Last Deposit' => '29/01/2015',
			'SomeOtherFieldNotUsed' => 'not used',
		);
		$merchantData = array(
			'Merchant' => array(
				'id' => 'id',
				'merchant_dba' => 'dba',
			),
			'LastDepositReport' => array(
				'id' => 'lastReportId'
			)
		);
		$this->LastDepositReport = $this->getMockForModel('LastDepositReport', array('_getCurrentUser'));
		$this->LastDepositReport->expects($this->any())
				->method('_getCurrentUser')
				->will($this->returnValue('currentUserValue'));
		$this->LastDepositReport->Merchant = $this->_mockMerchant($merchantData);
		$result = $this->LastDepositReport->beforeImport($data);
		$expected = array(
			'LastDepositReport' => array(
				'merchant_id' => 'id',
				'merchant_id' => 'id',
				'merchant_id_old' => 'oldMerchId',
				'merchant_dba' => 'dba',
				'id' => 'lastReportId',
				'last_deposit_date' => '29/01/2015',
				'user_id' => 'currentUserValue',
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testSkipRowImport method
 *
 * @return void
 */
	public function testSkipRowImport() {
		$data = array();
		$result = $this->LastDepositReport->skipRowImport($data);
		$this->assertTrue($result);

		$data = array('LastDepositReport' => array('ID' => false, 'Sales' => 0));
		$result = $this->LastDepositReport->skipRowImport($data);
		$this->assertEquals('Merchant ID is blank in CSV file', $result);

		$data = array('LastDepositReport' => array('ID' => 'not empty'));
		$result = $this->LastDepositReport->skipRowImport($data);
		$this->assertEquals('Merchant not empty does not exist', $result);

		$data = array('LastDepositReport' => array('ID' => '3948000030002785', 'Sales' => 0));
		$result = $this->LastDepositReport->skipRowImport($data);
		$this->assertEquals('Merchant 3948000030002785 had no sales activity and was skipped', $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testSearchByMerchant() {
		$expectedConditions = [
			'OR' => [
				'Merchant.merchant_dba iLIKE' => '%%',
				'Merchant.merchant_mid iLIKE' => '%%'
			]
		];

		$conditions = $this->LastDepositReport->searchByMerchant();
		$this->assertEquals($expectedConditions, $conditions);

		$conditions = $this->LastDepositReport->searchByMerchant([
			'random-key-1' => 'random-value-1',
			'random-key-2' => 'random-value-2'
		]);
		$this->assertEquals($expectedConditions, $conditions);

		$expectedConditions = [
			'OR' => [
				'Merchant.merchant_dba iLIKE' => '%hand%',
				'Merchant.merchant_mid iLIKE' => '%hand%'
			]
		];
		$conditions = $this->LastDepositReport->searchByMerchant([
			'merchant' => 'hand',
		]);
		$this->assertEquals($expectedConditions, $conditions);
	}

/**
 * test
 *
 * @return void
 */
	public function testFindLastDepositReports() {
		$matchContain = [
			'LastDepositReport',
			'Client',
			'InstalledTimeLineEntry',
			'ApprovedUwStatus',
			'Merchant',
		];
		$matchDepositFields = [
			'last_deposit_date',
			'sales_num',
			'monthly_volume'
		];

		$result = $this->LastDepositReport->find('lastDepositReports');

		$this->assertCount(3, $result);
		$this->assertEquals($matchContain, array_keys(Hash::get($result, '0')));
		$this->assertEquals($matchDepositFields, array_keys(Hash::get($result, '0.LastDepositReport')));

		$result = $this->LastDepositReport->find('lastDepositReports', [
			'conditions' => [
				'LastDepositReport.merchant_id' => '00000000-0000-0000-0000-000000000004',
			]
		]);
		$this->assertCount(2, $result);
		$this->assertEquals($matchContain, array_keys(Hash::get($result, '0')));
		$this->assertEquals($matchDepositFields, array_keys(Hash::get($result, '0.LastDepositReport')));
		$expectedIds = [
			'00000000-0000-0000-0000-000000000004',
			'00000000-0000-0000-0000-000000000004'
		];
		$this->assertEquals($expectedIds, Hash::extract($result, '{n}.Merchant.id'));
	}
/**
 * testBeforeSave
 *
 * @return void
 */
	public function testBeforeSave() {
		//Emilate date belonging to a non acquiring merchant that already has install
		//and insall commissioned dates in timeline_entries
		$merchantId = '00000000-0000-0000-0000-000000000004';
		$this->LastDepositReport->Merchant->id = $merchantId;
		$this->LastDepositReport->Merchant->saveField('merchant_mid', '12345');
		$data = [
			'merchant_id' => $merchantId,
			'sales_num' => 10,
			'monthly_volume' => 101
		];
		$conditions = [
			'merchant_id' => $merchantId,
			'timeline_date_completed IS NOT NULL',
			'timeline_item_id' => TimelineItem::GO_LIVE_DATE
		];
		$this->LastDepositReport->set($data);
		$expected = $this->LastDepositReport->Merchant->TimelineEntry->find('count', ['conditions' =>  $conditions]);
		$this->LastDepositReport->beforeSave();
		$actual = $this->LastDepositReport->Merchant->TimelineEntry->find('count', ['conditions' =>  $conditions]);
		//No duplicates should have been created
		$this->assertEquals($expected, $actual);

		//Remove install commissioned  timeline entry to emulate merchant had only one
		$this->LastDepositReport->Merchant->TimelineEntry->deleteAll(['merchant_id' => $merchantId, 'timeline_item_id' => TimelineItem::INSTALL_COMMISSIONED]);
		$this->LastDepositReport->beforeSave();
		$actual = $this->LastDepositReport->Merchant->TimelineEntry->find('count', ['conditions' =>  $conditions]);
		$this->assertEquals($expected, $actual);

		//Remove all  timeline entries to emulate merchant had none
		$this->LastDepositReport->Merchant->TimelineEntry->deleteAll($conditions);
		$this->LastDepositReport->beforeSave();
		$actual = $this->LastDepositReport->Merchant->TimelineEntry->find('count', ['conditions' =>  $conditions]);
		$this->assertEquals($expected, $actual);
		
	}
}
