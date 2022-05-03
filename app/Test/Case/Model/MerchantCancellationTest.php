<?php
App::uses('MerchantCancellation', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * MerchantCancellation Test Case
 *
 */
class MerchantCancellationTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantCancellation = $this->getMockForModel('MerchantCancellation', [
			'_getCurrentDay'
		]);
		$this->MerchantCancellation->expects($this->any())
			->method('_getCurrentDay')
			->will($this->returnValue('10'));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantCancellation);
		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testGetCancellation() {
		$this->assertEmpty( $this->MerchantCancellation->getCancellation('00000000-9999-0000-0000-000000000001'));

		$merchantId = '00000000-0000-0000-0000-000000000005';
		$result = $this->MerchantCancellation->getCancellation($merchantId);

		$expectedContain = [
			'Merchant',
			'User',
			'MerchantCancellation',
			'MerchantUwVolume',
			'CancellationsHistory',
		];
		$this->assertEquals($expectedContain, array_keys($result));
		$this->assertEquals($merchantId, Hash::get($result, 'Merchant.id'));
		$this->assertEquals('Bill McAbee inactive', Hash::get($result, 'User.fullname'));
	}

/**
 * test
 *
 * @param array $params Filter options
 * @param array $expectedConditions Expected filter conditions
 * @dataProvider providerSetPaginatorSettings
 * @return void
 */
	public function testSetPaginatorSettings($params, $expectedConditions) {
		$expectedJoins = ['Client', 'User', 'Partner', 'Entity'];
		$expectedFields = [
			'MerchantCancellation.*',
			'MerchantCancellationSubreason.*',
			'Client.client_id_global',
			'User.user_first_name',
			'User.user_last_name',
			'((trim(BOTH FROM "Partner"."user_first_name" || \' \' || "Partner"."user_last_name"))) as "Partner__fullname"',
			'Entity.entity_name',
		];

		$result = $this->MerchantCancellation->setPaginatorSettings($params);
		$this->assertEquals($expectedJoins, Hash::extract($result, 'joins.{n}.alias'));
		$this->assertEquals('MerchantCancellationSubreason', Hash::get($result, 'contain.0'));
		$this->assertNotEmpty(Hash::get($result, 'contain.Merchant'));
		$this->assertEquals($expectedFields, Hash::get($result, 'fields'));
		$this->assertEquals($expectedConditions, Hash::get($result, 'conditions'));
	}

/**
 * Data provider for testSetPaginatorSettings
 *
 * @return array
 */
	public function providerSetPaginatorSettings() {
		return [
			[[], ['MerchantCancellation.status' => GUIbuilderComponent::STATUS_COMPLETED]],
			[
				['fromDate' => '2015-01-01'],
				[
					'MerchantCancellation.status' => GUIbuilderComponent::STATUS_COMPLETED,
					'MerchantCancellation.date_submitted >=' => '2015-01-01',
				],
			],
			[
				['toDate' => '2015-12-01'],
				[
					'MerchantCancellation.status' => GUIbuilderComponent::STATUS_COMPLETED,
					'MerchantCancellation.date_submitted <=' => '2015-12-01',
				],
			],
			[
				[
					'fromDate' => '2015-01-01',
					'toDate' => '2015-12-01',
					'random-key' => 'random-value',
				],
				[
					'MerchantCancellation.status' => GUIbuilderComponent::STATUS_COMPLETED,
					'MerchantCancellation.date_submitted >=' => '2015-01-01',
					'MerchantCancellation.date_submitted <=' => '2015-12-01',
				],
			],
		];
	}

/**
 * test
 *
 * @return void
 */
	public function testGetDateRangeByMonthYear() {
		$result = $this->MerchantCancellation->getDateRangeByMonthYear('07', '2013', 4);
		$expected = [
			'fromDate' => '2013-03-01',
			'toDate' => '2013-07-31',
		];
		$this->assertEquals($expected, $result);

		$result = $this->MerchantCancellation->getDateRangeByMonthYear('07', '2013', 0);
		$expected = [
			'fromDate' => '2013-07-01',
			'toDate' => '2013-07-31',
		];
		$this->assertEquals($expected, $result);

		$result = $this->MerchantCancellation->getDateRangeByMonthYear('01', '2016', 2);
		$expected = [
			'fromDate' => '2015-11-01',
			'toDate' => '2016-01-31',
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @param array $filterData Filter data
 * @param array $expected Expected result
 * @dataProvider providerCalculateAttritionData
 * @return void
 */
	public function testCalculateAttritionData($filterData, $expected) {
		$result = $this->MerchantCancellation->calculateAttritionData($filterData);
		$this->assertEquals($expected, $result);
	}

/**
 * provider for testCalculateAttritionData
 *
 * @return array
 */
	public function providerCalculateAttritionData() {
		return [
			[
				[
					'ammountOfMonths' => 6,
					'month' => ['month' => 6],
					'year' => ['year' => 2015],
				],
				[
					'fromDate' => '2014-12-01',
					'toDate' => '2015-06-30',
					'amountOfmonths' => 6,
					'activeMerchants' => 2,
					'user_id' => null
				]
			],
			[
				[
					'ammountOfMonths' => '',
					'month' => ['month' => 6],
					'year' => ['year' => 2015],
				],
				[
					'fromDate' => '2014-06-01',
					'toDate' => '2015-06-30',
					'amountOfmonths' => 12,
					'activeMerchants' => 2,
					'user_id' => null
				]
			],
		];
	}

/**
 * test
 *
 * @return void
 */
	public function testMakeCsvString() {
		$settings = $this->MerchantCancellation->setPaginatorSettings([]);
		$filterData = [
			'fromDate' => '2014-06-10',
			'toDate' => '2015-06-10',
			'amountOfmonths' => 12,
			'activeMerchants' => 0,
		];
		$result = $this->MerchantCancellation->makeCsvString($settings, $filterData);

		$expected = 'Cancellation Report | 12 months from June 2014 to June 2015';
		$this->assertContains($expected, $result);
		$expected = 'MID,DBA,Client ID,Rep,Volume,Axia Inv,Date Approved,Date Submitted,Date Completed,Date Inactive,Fee Charged,Reason,Details,Status';
		$this->assertContains($expected, $result);
		$expected = '="3948906204000946","16 Hands",,Mark Weatherford,,,05/16/2015,02/10/2004,02/23/2004,,$200.00,No Reason at all,,Completed';
		$this->assertContains($expected, $result);
		

		$filterData = [
			'fromDate' => '2014-06-10',
			'toDate' => '2015-06-10',
			'amountOfmonths' => 12,
			'activeMerchants' => 2,
			'userAttritionRate' => 0.5,
		];
		$result = $this->MerchantCancellation->makeCsvString($settings, $filterData);
		$expected = 'Attrition Ratio Report | 12 months from June 2014 to June 2015';
		$this->assertContains($expected, $result);
		$expected = '="3948906204000946","16 Hands",,Mark Weatherford,,,05/16/2015,02/10/2004,02/23/2004,,$200.00,No Reason at all,,Completed';
		$this->assertContains($expected, $result);
		$expected = 'Number of Open Merchants:,2';
		$this->assertContains($expected, $result);
		$expected = 'Plus/Minus Allowance:,0.50%';
		$this->assertContains($expected, $result);
	}
}
