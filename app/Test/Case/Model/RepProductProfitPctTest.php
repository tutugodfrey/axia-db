<?php
App::uses('OrderitemType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * RepProductProfitPct Test Case
 *
 * @coversDefaultClass RepProductProfitPct
 */
class RepProductProfitPctTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var RepProductProfitPct
 */
	public $RepProductProfitPct;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RepProductProfitPct = ClassRegistry::init('RepProductProfitPct');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RepProductProfitPct);
		parent::tearDown();
	}

/**
 * testConstructorAndValidates method
 *
 * @covers RepProductProfitPct::__construct
 * @covers RepProductProfitPct::validate property
 *
 * @return void
 */
	public function testConstructorAndValidates() {
		$RepProductProfitPct = $this->getMockBuilder('RepProductProfitPct')
			->disableOriginalConstructor()
			->getMock();
		$RepProductProfitPct->__construct();
		$expected = [
			'user_id' => [
				'uuid' => [
					'rule' => [
						0 => 'uuid'
					],
					'required' => true,
					'allowEmpty' => false,
					'message' => 'Please enter a User'
				]
			],
			'products_services_type_id' => [
				'uuid' => [
					'rule' => [
						0 => 'uuid'
					],
					'required' => true,
					'allowEmpty' => false,
					'message' => 'Please enter a Products Services Type'
				]
			]
		];

		$this->assertEquals($expected, $RepProductProfitPct->validate);
	}

/**
 * testAdd method
 *
 * @covers RepProductProfitPct::add()
 * @return void
 */
	public function testAdd() {
		$data = [
			'RepProductProfitPct' => [
				'id' => '9f3c9149-6301-4b2b-873a-0d077999497d',
				'user_id' => AxiaTestCase::USER_SM_ID,
				'products_services_type_id' => '7da107ab-efda-4a39-8520-b61991bbe48a',
				'pct' => 50,
				'multiple' => 0,
				'pct_gross' => 100,
				'do_not_display' => false
			]
		];

		$result = $this->RepProductProfitPct->add($data);
		$this->assertTrue($result);

		$data = [];

		$result = $this->RepProductProfitPct->add($data);
		$this->assertFalse($result);
	}

/**
 * testAdd method
 *
 * @covers RepProductProfitPct::add()
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testAddException() {
		$data = [
			'RepProductProfitPct' => []
		];

		$this->RepProductProfitPct->add($data);
	}

/**
 * testAdd method
 *
 * @covers RepProductProfitPct::edit()
 * @return void
 */
	public function testEdit() {
		$repId = 'c88e1da7-d185-42c8-bc11-22d724448387';

		$result = $this->RepProductProfitPct->edit($repId);
		$expected = [
			'RepProductProfitPct' => [
				'id' => 'c88e1da7-d185-42c8-bc11-22d724448387',
				'user_id' => '18788e0a-58b7-42e6-a222-563abfb2ceb9',
				'user_id_old' => null,
				'products_services_type_id' => 'd207d63a-9613-4ace-9374-897f750f3fba',
				'products_services_type_id_old' => null,
				'pct' => '50.0000',
				'multiple' => '0.0000',
				'pct_gross' => '80.0000',
				'do_not_display' => false
			]
		];

		$this->assertEquals($expected, $result);

		$data = [
			'RepProductProfitPct' => [
				'id' => 'c88e1da7-d185-42c8-bc11-22d724448387',
				'user_id' => '18788e0a-58b7-42e6-a222-563abfb2ceb9',
				'user_id_old' => null,
				'products_services_type_id' => 'd207d63a-9613-4ace-9374-897f750f3fba',
				'products_services_type_id_old' => null,
				'pct' => '50.0000',
				'multiple' => '0.0000',
				'pct_gross' => '80.0000',
				'do_not_display' => false
			]
		];

		$repId = 'f9c4ae60-7752-4f62-a4af-29381e38a74f';
		$result = $this->RepProductProfitPct->edit($repId, $data);

		$this->assertTrue($result);

		$data = [
			'RepProductProfitPct' => [
				'user_id' => 'invalid-user',
				'products_services_type_id' => 'd207d63a-9613-4ace-9374-897f750f3fba',
				'pct' => '50.0000',
				'multiple' => '0.0000',
				'pct_gross' => '80.0000',
				'do_not_display' => false
			]
		];

		$repId = 'c88e1da7-d185-42c8-bc11-22d724448387';
		$result = $this->RepProductProfitPct->edit($repId, $data);
		$expected = [
			'RepProductProfitPct' => [
				'user_id' => 'invalid-user',
				'products_services_type_id' => 'd207d63a-9613-4ace-9374-897f750f3fba',
				'pct' => '50.0000',
				'multiple' => '0.0000',
				'pct_gross' => '80.0000',
				'do_not_display' => false
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testAdd method
 *
 * @covers RepProductProfitPct::edit()
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testEditException() {
		$repId = '00000000-9999-0000-0000-000000000001';
		$this->RepProductProfitPct->edit($repId);
	}

/**
 * testValidateAndDelete method
 *
 * @covers RepProductProfitPct::validateAndDelete()
 * @return void
 */
	public function testValidateAndDelete() {
		$repId = '9f3c9149-6301-4b2b-873a-0d077999497d';
		$data = [
			'RepProductProfitPct' => [
				'id' => $repId,
				'user_id' => AxiaTestCase::USER_SM_ID,
				'products_services_type_id' => '7da107ab-efda-4a39-8520-b61991bbe48a',
				'pct' => 50,
				'multiple' => 0,
				'pct_gross' => 100,
				'do_not_display' => false
			]
		];

		$result = $this->RepProductProfitPct->add($data);
		$this->assertTrue($result);

		$result = $this->RepProductProfitPct->validateAndDelete($repId, $data);
		$this->assertTrue($result);
	}

/**
 * testValidateAndDeleteException method
 *
 * @covers RepProductProfitPct::validateAndDelete()
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testValidateAndDeleteException() {
		$repId = '00000000-9999-0000-0000-000000000001';
		$this->RepProductProfitPct->validateAndDelete($repId);
	}
}