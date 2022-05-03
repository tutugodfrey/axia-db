<?php
App::uses('Merchant', 'Model');
App::uses('MerchantReject', 'Model');
App::uses('MerchantRejectStatus', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Merchant Reject Test Case
 *
 */
class MerchantRejectTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantReject = ClassRegistry::init('MerchantReject');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantReject);
		parent::tearDown();
	}

/**
 * testListCodes
 *
 * @return void
 */
	public function testListCodes() {
		$expected = ['Lorem i' => 'Lorem i'];
		$actual = $this->MerchantReject->listCodes();
		$this->assertEqual($actual, $expected, 'Actual and Expected data does not match for inactive merchant');
	}

/**
 * Test beforeImport
 *
 * @return void
 */
	public function testBeforeImport() {
		$expected = [
			'MerchantReject' => [
				'merchant_id' => '00000000-0000-0000-0000-000000000003',
				'reject_date' => '2015-03-19',
				'amount' => -141.38,
				'trace' => '8.10E+13',
				'code' => 'R01',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
			],
			'MerchantRejectLine' => [
				[
					'merchant_reject_status_id' => 'a4961985-c384-43df-b03d-b269f560d1f5',
					'status_date' => date('Y-m-d')
				]
			]
		];

		$data = [
			'MerchantReject' => [
				'Individual ID' => '3948000030003049',
				'Date' => '3/19/2015',
				'Name' => 'MOREY\'S MARKET',
				'Amount' => '($141.38)',
				'TC' => '26',
				'Trace Number' => '8.10E+13',
				'Account Number' => '1339123075',
				'Ret Code' => 'R01',
				'Ret Reason' => 'Insufficient Funds',
				'Company ID' => '9000007641',
				'Merrick DDA' => '9113'
			]
		];
		$result = $this->MerchantReject->beforeImport($data);
		$this->assertEquals($expected, $result);
		$result = $this->MerchantReject->save($result);
		$this->assertEmpty($this->MerchantReject->validationErrors);

		$expected = [
			'MerchantReject' => [
				'merchant_id' => '00000000-0000-0000-0000-000000000003',
				'reject_date' => '2015-03-19',
				'amount' => 18.00,
				'trace' => '9.10E+13',
				'code' => 'R01',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
			],
			'MerchantRejectLine' => [
				[
					'merchant_reject_status_id' => 'a4961985-c384-43df-b03d-b269f560d1f5',
					'status_date' => date('Y-m-d')
				]
			]
		];
		$data = [
			'MerchantReject' => [
				'Individual ID' => '3948000030003049',
				'Date' => '3/19/2015',
				'Name' => 'WEBCONSORT',
				'Amount' => '$18.00 ',
				'TC' => '26',
				'Trace Number' => '9.10E+13',
				'Account Number' => '291887610',
				'Ret Code' => 'R01',
				'Ret Reason' => 'Insufficient Funds',
				'Company ID' => '9000007641',
				'Merrick DDA' => '9113'
			]
		];

		$result = $this->MerchantReject->beforeImport($data);
		$this->assertEquals($expected, $result);
		$result = $this->MerchantReject->saveAll($result);
		$this->assertEmpty($this->MerchantReject->validationErrors);
	}

/**
 * test
 *
 * @return void
 */
	public function testFixMerchantMerchantIdNotNullShouldReturnData() {
		$data = [
			'MerchantReject' => [
				'reject_date' => '2015-03-19',
				'amount' => 18.00,
				'trace' => '9.10E+13',
				'code' => 'R01',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
				'merchant_id' => '3948000030003049'
			],
		];
		$result = $this->MerchantReject->fixMerchant($data);
		//Fixing should replace the mid with the merchant_id UUID
		$data['MerchantReject']['merchant_id'] = '00000000-0000-0000-0000-000000000003';
		$this->assertEquals($data, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testFixMerchantMerchantIdNullOldIdNullShouldReturnData() {
		$data = [
			'MerchantReject' => [
				'reject_date' => '2015-03-19',
				'amount' => 18.00,
				'trace' => '9.10E+13',
				'code' => 'R01',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
			],
		];
		$result = $this->MerchantReject->fixMerchant($data);
		$data['MerchantReject']['merchant_id'] = null;
		$this->assertEquals($data, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testFixMerchantMerchantIdNullOldIdNotNullShouldFixMerchantIdFound() {
		$data = [
			'MerchantReject' => [
				'reject_date' => '2015-03-19',
				'amount' => 18.00,
				'trace' => '9.10E+13',
				'code' => 'R01',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
				'merchant_id' => '3948000030003049'
			],
		];
		$expected = $data;
		$expected['MerchantReject']['merchant_id'] = '00000000-0000-0000-0000-000000000003';

		$result = $this->MerchantReject->fixMerchant($data);
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testFixMerchantMerchantIdNullOldIdNotNullShouldFixMerchantIdNotFound() {
		$data = [
			'MerchantReject' => [
				'reject_date' => '2015-03-19',
				'amount' => 18.00,
				'trace' => '9.10E+13',
				'code' => 'R01',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
				'merchant_id' => 'not-found'
			],
		];
		$expected = $data;
		$expected['MerchantReject']['merchant_id'] = null;

		$result = $this->MerchantReject->fixMerchant($data);
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testGetMerchantIdNotFound() {
		$result = $this->MerchantReject->getMerchantId('not-found');
		$this->assertEmpty($result);
	}

/**
 * test
 *
 * @return void
 */
	public function testGetMerchantIdFound() {
		$result = $this->MerchantReject->getMerchantId('3948000030003049');
		$expected = '00000000-0000-0000-0000-000000000003';
		$this->assertEquals($expected, $result);
	}

/**
 * testConstructorAndFilterArgs
 *
 * @return void
 */
	public function testConstructorAndFilterArgs() {
		$MerchantReject = $this->_mockModel('MerchantReject');

		$expected = [
			'merchant_reject_type_id' => [
				'type' => 'value'
			],
			'merchant_dba' => [
				'type' => 'like',
				'field' => 'Merchant.merchant_dba'
			],
			'from_date' => [
				'type' => 'query',
				'method' => 'dateStartConditions',
				'empty' => true,
				'defaultValue' => [
					'year' => '2015',
					'month' => '01'
				]
			],
			'end_date' => [
				'type' => 'query',
				'method' => 'dateEndConditions',
				'empty' => true,
				'defaultValue' => [
					'year' => '2015',
					'month' => '01'
				]
			],
			'open' => [
				'type' => 'value'
			],
			'user_id' => [
				'type' => 'subquery',
				'method' => 'searchByUserId',
				'field' => '"Merchant"."user_id"',
				'searchByMerchantEntity' => true
			],
			'merchant_reject_status_id' => [
				'type' => 'query',
				'method' => 'searchByStatus'
			]
		];
		$this->assertEquals($expected, $MerchantReject->filterArgs);
	}

/**
 * testDateRangeStartConditions
 *
 * @return void
 */
	public function testDateStartConditions() {
		$data = ['from_date' => [
			'year' => '2000',
			'month' => '01'
		]];
		$result = $this->MerchantReject->dateStartConditions($data);
		$expected = [
			'MerchantReject.reject_date >=' => '2000-01-01'
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testDateRangeEndConditions
 *
 * @return void
 */
	public function testDateEndConditions() {
		$data = ['end_date' => [
			'year' => '2000',
			'month' => '01'
		]];
		$result = $this->MerchantReject->dateEndConditions($data);
		$expected = [
			'MerchantReject.reject_date <=' => '2000-01-31'
		];

		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testAfterFind() {
		$results = $this->MerchantReject->find('all', [
			'contain' => [
				'CurrentMerchantRejectLine',
				'MerchantRejectLine'
			],
			'conditions' => [
				'MerchantReject.id' => '00000000-0000-0000-0000-000000000001',
			]
		]);

		$amount = 100;
		$currentMerchantRejectLineFee = 100.5000;
		$feesMerchantRejectLine = [100.5, 200.75];
		$currentMerchantRejectSubmittedAmount = $amount + $currentMerchantRejectLineFee;
		$submittedAmounts = array_map(function ($element) use ($amount) {
			return $element + $amount;
		}, $feesMerchantRejectLine);
		$this->assertEquals($amount, Hash::get($results, '0.MerchantReject.amount'));
		$this->assertEquals($currentMerchantRejectLineFee, Hash::get($results, '0.CurrentMerchantRejectLine.fee'));
		$this->assertEquals($currentMerchantRejectSubmittedAmount, Hash::get($results, '0.CurrentMerchantRejectLine.submitted_amount'));
		$this->assertEquals($submittedAmounts, Hash::extract($results, '0.MerchantRejectLine.{n}.submitted_amount'));
	}

/**
 * test
 *
 * @return void
 */
	public function testFindMerchantRejects() {
		$result = $this->MerchantReject->find('merchantRejects');
		$matchContains = [
			'MerchantReject',
			'Merchant',
			'MerchantRejectType',
			'MerchantRejectRecurrance',
			'FirstMerchantRejectLine',
			'CurrentMerchantRejectLine',
			'MerchantRejectLine'
		];

		$this->assertEquals($matchContains, array_keys(Hash::get($result, '0')));

		$matchOrder = [
			'00000000-0000-0000-0000-000000000002',
			'00000000-0000-0000-0000-000000000001',
		];
		$this->assertEquals($matchOrder, Hash::extract($result, '{n}.MerchantReject.id'));
	}

/**
 * test
 *
 * @return void
 */
	public function testContain() {
		$result = $this->MerchantReject->contain();
		$expected = [
			'MerchantRejectType' =>
			[
				'fields' =>
				[
					0 => 'id',
					1 => 'name',
				],
			],
			'MerchantRejectLine' =>
			[
				'fields' =>
				[
					0 => 'id',
					1 => 'fee',
					2 => 'status_date',
					3 => 'notes',
				],
				'order' =>
				[
					0 => 'MerchantRejectLine.status_date DESC NULLS LAST',
				],
				'MerchantRejectStatus' =>
				[
					'fields' =>
					[
						0 => 'id',
						1 => 'name',
					],
				],
			],
			'FirstMerchantRejectLine' =>
			[
				'order' =>
				[
					'FirstMerchantRejectLine.status_date' => 'ASC NULLS FIRST',
				],
				'MerchantRejectStatus' =>
				[
					'fields' =>
					[
						0 => 'id',
						1 => 'name',
					],
				],
			],
			'CurrentMerchantRejectLine' =>
			[
				'order' =>
				[
					'CurrentMerchantRejectLine.status_date' => 'DESC NULLS LAST',
				],
				'MerchantRejectStatus' =>
				[
					'fields' =>
					[
						0 => 'id',
						1 => 'name',
					],
				],
			],
			'Merchant' =>
			[
				'fields' =>
				[
					0 => 'id',
					1 => 'user_id',
					2 => 'merchant_mid',
					3 => 'merchant_dba',
				],
				'User' =>
				[
					'fields' =>
					[
						0 => 'id',
						1 => 'initials',
						2 => 'user_first_name',
						3 => 'user_last_name',
					],
				],
			],
			'MerchantRejectRecurrance' =>
			[
				'fields' =>
				[
					'id',
					'name'
				]
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testRowContain
 *
 * @return void
 */
	public function testRowContain() {
		$result = $this->MerchantReject->rowContain();
		$expected = [
			'MerchantRejectType',
			'FirstMerchantRejectLine',
			'CurrentMerchantRejectLine',
			'Merchant',
			'MerchantRejectRecurrance'
		];

		$this->assertEquals($expected, array_keys($result));
	}

/**
 * test
 *
 * @return void
 */
	public function testGetDefaultValues() {
		$expected = [
			'MerchantReject' => [
				'merchant_reject_type_id' => '8b48f7ef-6115-41c0-9bbe-bcd34f786506',
				'merchant_reject_recurrance_id' => '4aa0cdb2-afd4-42f2-beae-df9d39fc786b',
				'open' => 1
			],
			'MerchantRejectLine' => [
				'merchant_reject_status_id' => 'a4961985-c384-43df-b03d-b269f560d1f5'
			]
		];

		$this->assertEquals($expected, $this->MerchantReject->getDefaultValues());
	}

/**
 * testSearchByStatus
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Merchant reject status is required
 * @return void
 */
	public function testSearchByStatusNotValid() {
		$this->MerchantReject->searchByStatus([]);
	}

/**
 * testSearchByStatus
 *
 * @param array $data Search data
 * @param array $expected Expected result
 * @dataProvider providerSearchByStatus
 * @return void
 */
	public function testSearchByStatus($data, $expected) {
		$result = $this->MerchantReject->searchByStatus($data);
		$this->assertEquals($expected, $result);
	}

/**
 * Data provider for testSearchByStatus
 *
 * @dataProvider
 * @return void
 */
	public function providerSearchByStatus() {
		return [
			[
				['merchant_reject_status_id' => strval(MerchantRejectStatus::STATUS_NOT_COLLECTED_GENERAL)],
				[
					'CurrentMerchantRejectLine.merchant_reject_status_id' => [
						0 => '79d18238-e403-4596-a37b-d7d4468795df',
						1 => 'afa257c3-9dcb-4661-8946-91805171510c',
						2 => '87828851-cde7-4ddc-b0b9-213435caf2e8',
						3 => '505e6e00-eda4-4b0e-b48e-00f1827655ec',
					],
				],
			],
			[
				['merchant_reject_status_id' => strval(MerchantRejectStatus::STATUS_COLLECTED_GENERAL)],
				[
					'CurrentMerchantRejectLine.merchant_reject_status_id' => [
						0 => '03fcd6ef-726b-40c4-8942-764d8bf6bc46',
						1 => 'dd2123a3-aa3a-4747-bcfb-9b7641e48bc7',
						2 => 'c109f205-9d85-445d-8010-c0013b8f6141',
					],
				],
			],
			[
				['merchant_reject_status_id' => strval(MerchantRejectStatus::STATUS_WRITTEN_OFF)],
				['CurrentMerchantRejectLine.merchant_reject_status_id' => '505e6e00-eda4-4b0e-b48e-00f1827655ec'],
			],
		];
	}

/**
 * testGetItemsToSet
 *
 * @return void
 */
	public function testGetItemsToSet() {
		$this->MerchantReject->Merchant->User = $this->getMockForModel('User', ['_getCurrentUser']);
		$this->MerchantReject->Merchant->User->expects($this->any())
				->method('_getCurrentUser')
				->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));

		$expected = [
			'merchantRejectStatuses' => [
					'a4961985-c384-43df-b03d-b269f560d1f5' => 'Received',
					'4c82df9f-403b-4a8d-83e0-f85ed4b074d5' => 'Re-Submitted',
					'e683ee7e-97d2-4882-a5d5-bd79d5246a9e' => 'Additional Reject',
					'c0a5e7f2-5634-4d8d-81a2-ff43a33abe03' => 'Updated Banking (NOC)',
					'COLLECTED' => [
						1 => 'Select this to search all COLLECTED',
						'03fcd6ef-726b-40c4-8942-764d8bf6bc46' => 'Re-submitted - Confirmed',
						'dd2123a3-aa3a-4747-bcfb-9b7641e48bc7' => 'Collected - Reserve',
						'c109f205-9d85-445d-8010-c0013b8f6141' => 'Collected - Other'
					],
					'NOT COLLECTED' => [
						0 => 'Select this to search all NOT COLLECTED',
						'79d18238-e403-4596-a37b-d7d4468795df' => 'On Reserve',
						'afa257c3-9dcb-4661-8946-91805171510c' => 'Re-Rejected',
						'87828851-cde7-4ddc-b0b9-213435caf2e8' => 'Not Collected',
						'505e6e00-eda4-4b0e-b48e-00f1827655ec' => 'Written Off'
					]
			],
			'merchantRejectRecurrances' => [
				'4aa0cdb2-afd4-42f2-beae-df9d39fc786b' => '1st Reject',
				'b1e9eaea-1a03-4335-a6ac-e59d049ad451' => 'Additional Reject'
			],
			'merchantRejectTypes' => [
				'8b48f7ef-6115-41c0-9bbe-bcd34f786506' => 'Reject',
				'c70ac383-0e29-4394-9b75-9ca6c01200f7' => 'Update Banking'
			],
			'merchantRejectCodes' => [
				'Lorem i' => 'Lorem i'
			]
		];

		$this->assertEquals($expected, $this->MerchantReject->getItemsToSet());
	}

/**
 * testImportFromCsvUpload
 *
 * @return void
 */
	public function testImportFromCsvUpload() {
		$this->MerchantReject->Behaviors->load('CsvImportCheck');
		$fileName = "rejectTestDat.csv";
		$data = [
			'MerchantReject' => [
				'file' => [
					'name' => $fileName,
					'type' => 'application/vnd.ms-excel',
					'tmp_name' => APP . 'Test' . DS . 'Tmp' . DS . 'rejectTestDat.csv',
					'error' => 0,
				]
			]
		];

		$result = $this->MerchantReject->importFromCsvUpload($data);
		$this->assertEqual([], $result);
	}
}
