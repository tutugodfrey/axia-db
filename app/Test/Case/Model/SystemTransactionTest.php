<?php
App::uses('SystemTransaction', 'Model');
App::uses('TransactionType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * SystemTransaction Test Case
 *
 */
class SystemTransactionTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SystemTransaction = ClassRegistry::init('SystemTransaction');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SystemTransaction);
		parent::tearDown();
	}

/**
 * testConstructorFilterArgsVirtualFields
 *
 * @covers SystemTransaction::__construct
 * @covers SystemTransaction::filterArgs property
 * @covers SystemTransaction::virtualFields property
 * @return void
 */
	public function testConstructorFilterArgsVirtualFields() {
		$SystemTransactionModel = $this->_mockModel('SystemTransaction');
		$expected = [
			'user_id' => [
				'type' => 'subquery',
				'method' => 'searchByUserId',
				'field' => '"SystemTransaction"."user_id"'
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
			]
		];
		$this->assertEquals($expected, $SystemTransactionModel->filterArgs);

		$expected = ['transaction_description'];
		$this->assertEquals($expected, array_keys($SystemTransactionModel->virtualFields));
		unset($SystemTransactionModel);
	}

/**
 * testTypeExists
 *
 * @return void
 */
	public function testTypeExists() {
		$this->assertFalse($this->SystemTransaction->typeExists('00000000-9999-0000-0000-000000000001'));
		$this->assertFalse($this->SystemTransaction->typeExists(['00000000-9999-0000-0000-000000000001']));
		$this->assertTrue($this->SystemTransaction->typeExists('00000000-0000-0000-0000-000000000001'));
		$this->assertTrue($this->SystemTransaction->typeExists(['00000000-0000-0000-0000-000000000002']));
	}

/**
 * testGetDefaultSearchValues
 *
 * @return void
 */
	public function testGetDefaultSearchValues() {
		$SystemTransactionModel = $this->_mockModel('SystemTransaction');
		$SystemTransactionModel->User = ClassRegistry::init('User');
		$expected = [
			'from_date' => [
				'year' => '2015',
				'month' => '01',
			],
			'end_date' => [
				'year' => '2015',
				'month' => '01',
			],
			'row_limit' => 50,
		];
		$this->assertEquals($expected, $SystemTransactionModel->getDefaultSearchValues());
		unset($SystemTransactionModel);
	}

/**
 * integration test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @dataProvider dateStartConditionsData
 * @return void
 */
	public function testDateStartConditions($data, $expected) {
		$this->assertEquals($expected, $this->SystemTransaction->dateStartConditions($data));
	}

/**
 * Data provider for testDateStartConditions
 *
 * @return array
 */
	public function dateStartConditionsData() {
		return [
			[
				['from_date' => ['year' => '2015', 'month' => '01']],
				['SystemTransaction.system_transaction_date >=' => '2015-01-01']
			],
			[
				['from_date' => ['year' => '2022', 'month' => '06']],
				['SystemTransaction.system_transaction_date >=' => '2022-06-01']
			],
		];
	}

/**
 * integration test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @dataProvider dateEndConditionsData
 * @return void
 */
	public function testDateEndConditions($data, $expected) {
		$this->assertEquals($expected, $this->SystemTransaction->dateEndConditions($data));
	}

/**
 * Data provider for testDateStartConditions
 *
 * @return array
 */
	public function dateEndConditionsData() {
		return [
			[
				['end_date' => ['year' => '2015', 'month' => '01']],
				['SystemTransaction.system_transaction_date <=' => '2015-01-31']
			],
			[
				['end_date' => ['year' => '2022', 'month' => '02']],
				['SystemTransaction.system_transaction_date <=' => '2022-02-28']
			],
		];
	}

/**
 * test
 *
 * @return void
 */
	public function testUserActivity() {
		$result = $this->SystemTransaction->find('userActivity', [
			'conditions' => [
				'SystemTransaction.system_transaction_date >=' => '2015-01-01',
				'SystemTransaction.system_transaction_date <=' => '2015-12-31',
			],
		]);
		$matchFields = [
			'id',
			'user_id',
			'transaction_type_id',
			'system_transaction_date',
			'system_transaction_time',
			'login_date',
			'client_address',
			'merchant_id',
			'merchant_note_id',
			'merchant_change_id',
			'merchant_ach_id',
			'order_id',
			'programming_id',
			'user_fullname'
		];

		$this->assertEquals($matchFields, array_keys(Hash::get($result, '0.SystemTransaction')));

		$matchOrder = [
			'00000000-0000-0000-0000-000000000002',
			'00000000-0000-0000-0000-000000000001',
		];
		$this->assertEquals($matchOrder, Hash::extract($result, '{n}.SystemTransaction.id'));
	}
}
