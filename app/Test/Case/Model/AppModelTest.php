<?php
App::uses('AppModel', 'Model');
App::uses('HttpSocket', 'Network/Http');
App::uses('AxiaTestCase', 'Test');

/**
 * AppModel Test Case
 *
 * @property AppModel $AppModel
 */
class AppModelTest extends AxiaTestCase {

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array();

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->httpSocket =	$this
				->getMockBuilder('HttpSocket')
				->setMethods(array('requestAPITaxRate'))
				->getMock();
		$this->AppModel = $this->getMockForModel('AppModel', array('_getHttpSocket', '_getCurrentUser'));
		$this->AppModel
			->expects($this->any())
			->method('_getHttpSocket')
			->will($this->returnValue($this->httpSocket));

		$this->AppModel->useTable = false;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AppModel);
		parent::tearDown();
	}

/**
 * Test for testIsEncrypted method.
 *
 * @covers AppModel::isEncrypted
 * @return void
 */
	public function testIsEncrypted() {
		$val = '123456789102';
		$result = $this->AppModel->isEncrypted($val);
		$this->assertFalse($result);
		//Encrypt
		$val = $this->AppModel->encrypt($val, Configure::read('Security.OpenSSL.key'));
		$result = $this->AppModel->isEncrypted($val);
		$this->assertTrue($result);

		// //Encrypt
		$val = $this->AppModel->encrypt($val, Configure::read('Security.OpenSSL.key'));
		$result = $this->AppModel->isEncrypted($val);
		$this->assertTrue($result);

		$val = 'tdGYA+LVUNxE4dHOjlpt6LlhS9YJVrKkOApoykUlmOTjBn7BLlOYXWjDnRZW4iFJdTZ50Ee+Cq0qw1mEqQuHdQ==';
		$result = $this->AppModel->isEncrypted($val);
		$this->assertTrue($result);
	}
/**
 * Test for `getNumOfMonthsSince` method.
 *
 * @param DateTime|string $date DateTime or compatible-string.
 * @param int $expected The number of months that have passed.
 * @param DateTime|null $mockNow Current date (used in tests).
 * @covers AppModel::getNumOfMonthsSince
 * @dataProvider providesTestGetNumOfMonthsSince
 * @return void
 */
	public function testGetNumOfMonthsSince($date, $expected, $mockNow = null) {
		$this->assertEquals($expected, $this->AppModel->getNumOfMonthsSince($date, $mockNow));
	}

/**
 * Data provider for `testGetNumOfMonthsSince` test.
 *
 * @return array[]
 */
	public function providesTestGetNumOfMonthsSince() {
		$testDate = '2016-07-01';
		return [
			'null' => [
				'date' => null,
				'expected' => 0,
			],
			'0' => [
				'date' => 0,
				'expected' => 0,
			],
			'< 1 month' => [
				'date' => (new Datetime($testDate))->modify('-29 days'),
				'expected' => 0,
				'mockNow' => new DateTime($testDate),
			],
			'= 1 month' => [
				'date' => (new Datetime($testDate))->modify('-30 days'),
				'expected' => 1,
				'mockNow' => new DateTime($testDate)
			],
			'> 1 month' => [
				'date' => (new Datetime($testDate))->modify('-31 days'),
				'expected' => 1,
				'mockNow' => new DateTime($testDate),
			],
			'< 1 month crossing year' => [
				'date' => new DateTime('2015-12-25 00:00:00'),
				'expected' => 0,
				'mockNow' => new DateTime('2016-01-01 00:00:00'),
			],
			'= 1 month crossing year' => [
				'date' => new DateTime('2015-12-25 00:00:00'),
				'expected' => 1,
				'mockNow' => new DateTime('2016-02-01 00:00:00'),
			],
			'> 1 month crossing year' => [
				'date' => new DateTime('2015-12-25 00:00:00'),
				'expected' => 2,
				'mockNow' => new DateTime('2016-03-01 00:00:00'),
			],
			'= 1 year' => [
				'date' => new DateTime('2015-12-25 00:00:00'),
				'expected' => 12,
				'mockNow' => new DateTime('2016-12-25 00:00:00'),
			],
		];
	}

/**
 * testValidPercentage
 *
 * @covers AppModel::validPercentage
 * @return void
 */
	public function testValidPercentage() {
		$result = $this->AppModel->validPercentage(array());
		$this->assertFalse($result);
	}

/**
 * testValidPercentageInvalidPercentage
 *
 * @param array $value percentage values as array
 * @covers AppModel::validPercentage
 * @dataProvider provideValidPercentageInvalidPercentage
 * @return void
 */
	public function testValidPercentageInvalidPercentage($value) {
		$expected = 'Must be a valid percentage value with a maximum precision of 3';
		$result = $this->AppModel->validPercentage($value);
		$this->assertEquals($expected, $result);
	}

/**
 * provideValidPercentageInvalidPercentage this method will provide values to testValidPercentageInvalidPercentage
 *
 * @return array
 */
	public function provideValidPercentageInvalidPercentage() {
		return array(
			array(array(100.001)),
			array(array(100.01)),
			array(array(100.1)),
			array(array(-1)),
			array(array('string')),
			array(array(true)),
			array(array(false)),
			array(array('23.43fg')),
		);
	}

/**
 * testValidPercentageValidPercentage
 *
 * @param array $value percentage values as array
 * @covers AppModel::validPercentage
 * @dataProvider providerValidPercentageValidPercentage
 * @return void
 */
	public function testValidPercentageValidPercentage($value) {
		$result = $this->AppModel->validPercentage($value);
		$this->assertTrue($result);
	}

/**
 * providerValidPercentageValidPercentage this method will provide values to testValidPercentageInvalidPercentage
 *
 * @return array
 */
	public function providerValidPercentageValidPercentage() {
		return array(
			array(array(100.000)),
			array(array(100)),
			array(array(99.99)),
			array(array(99.999)),
			array(array(99)),
			array(array(0)),
			array(array(0.00)),
			array(array(0.001)),
			array(array(0.01)),
			array(array(0.1)),
			array(array('1.14')),
		);
	}

/**
 * testIsValidUUIDWithValidUUID
 *
 * @param string $uuid uuid value
 * @covers AppModel::isValidUUID
 * @dataProvider providerIsValidUUIDWithValidUUID
 * @return void
 */
	public function testIsValidUUIDWithValidUUID($uuid) {
		$this->assertTrue($this->AppModel->isValidUUID($uuid));
	}

/**
 * providerIsValidUUIDWithValidUUID this method will provide values to testIsValidUUIDWithValidUUID
 *
 * @return array
 */
	public function providerIsValidUUIDWithValidUUID() {
		return array(
			array('534bdba9-e8a8-4a75-9406-2ce134627ad4'),
			array('00ccf87a-4564-4b95-96e5-e90df32c46c1'),
			array('871e3266-4855-4b71-80f2-efeaa6892469'),
			array('5f36be3e-3211-4b40-94e4-a46c1081e7e3'),
			array('871e3266-4855-4b71-80f2-efeaa6892469'),
		);
	}

/**
 * testIsValidUUIDWithInvalidUUID
 *
 * @param string $uuid uuid value
 * @covers AppModel::isValidUUID
 * @dataProvider providerIsValidUUIDWithInvalidUUID
 * @return void
 */
	public function testIsValidUUIDWithInvalidUUID($uuid) {
		$this->assertFalse($this->AppModel->isValidUUID($uuid));
	}

/**
 * providerIsValidUUIDWithInvalidUUID this method will provide values to testIsValidUUIDWithInvalidUUID
 *
 * @return array
 */
	public function providerIsValidUUIDWithInvalidUUID() {
		return array(
			array('534bdba-e8a88-4a75-9406-2ce134627ad4'),
			array('534bdba-e8a8-4a755-9406-2ce134627adas4'),
			array('534bdba-e8a8-4a75-94066-2ce134627adas4'),
			array('534bdba-e8a8-4a75-9406-22ce134627adas4'),
			array('534bdba-e8a84a75-9406-2ce134627adas4'),
		);
	}

/**
 * testIsValidUUIDwithNonValidUUID
 *
 * @covers AppModel::isValidUUID
 * @return void
 */
	public function testIsValidUUIDwithNonValidUUID() {
		$this->assertFalse($this->AppModel->isValidUUID('534bdba-e8a84a75-9406-2ce134627adas4'));
	}

/**
 * testValidateFieldsEqual
 *
 * @covers AppModel::validateFieldsEqual
 * @return void
 */
	public function testValidateFieldsEqual() {
		$this->AppModel->create();
		$check = [];
		$this->assertFalse($this->AppModel->validateFieldsEqual($check, 'field1', 'field1'));
		$this->assertFalse($this->AppModel->validateFieldsEqual($check, 'field1', 'field2'));
		$this->AppModel->set(['field1' => '1']);
		$this->assertTrue($this->AppModel->validateFieldsEqual($check, 'field1', 'field1'));
		$this->assertFalse($this->AppModel->validateFieldsEqual($check, 'field1', 'field2'));
		$this->assertFalse($this->AppModel->validateFieldsEqual($check, 'field2', 'field1'));
		$this->AppModel->set([
			'field1' => '1',
			'field2' => 1,
		]);
		$this->assertFalse($this->AppModel->validateFieldsEqual($check, 'field1', 'field2'));
		$this->assertFalse($this->AppModel->validateFieldsEqual($check, 'field2', 'field1'));
		$this->AppModel->set([
			'field1' => 1,
			'field2' => 1,
		]);
		$this->assertTrue($this->AppModel->validateFieldsEqual($check, 'field1', 'field2'));
		$this->assertTrue($this->AppModel->validateFieldsEqual($check, 'field2', 'field1'));
	}

/**
 * testValidAmount
 *
 * @param array $amount amount value
 * @covers AppModel::validAmount
 * @dataProvider providerValidAmount
 * @return void
 */
	public function testValidAmount($amount) {
		$result = $this->AppModel->validAmount($amount);
		$this->assertTrue($result);
	}

/**
 * providerValidAmount this method will provide values to testValidAmount
 *
 * @return array
 */
	public function providerValidAmount() {
		return [
			[['0.0001']],
			[['99999999999.9999']],
			[['99']]
		];
	}

/**
 * testInvalidAmount
 *
 * @param array $amount amount value
 * @covers AppModel::validAmount
 * @dataProvider providerInvalidAmount
 * @return void
 */
	public function testInvalidAmount($amount) {
		$result = $this->AppModel->validAmount($amount);
		$this->assertFalse($result);
	}

/**
 * providerInvalidAmount this method will provide values to testInvalidAmount
 *
 * @return array
 */
	public function providerInvalidAmount() {
		return [
			[null],
			[[]],
			[['not number']],
			['0'],
			[['999999999991.9999']]
		];
	}

/**
 * testView
 *
 * @covers AppModel::view
 * @return void
 */
	public function testView() {
		$this->AppModel->useTable = 'users';
		//existing user in Fixtures
		$result = $this->AppModel->view(AxiaTestCase::USER_REP_ID, array());
		$this->assertEquals('mweatherford', Hash::get($result, 'AppModel.username'));
	}

/**
 * testViewOptions
 *
 * @covers AppModel::view
 * @return void
 */
	public function testViewOptions() {
		$this->AppModel->useTable = 'users';
		//existing user in Fixtures
		$result = $this->AppModel->view(AxiaTestCase::USER_REP_ID, array(
			'fields' => array('id', 'username')
		));
		$expected = array(
			'AppModel' => array(
				'id' => AxiaTestCase::USER_REP_ID,
				'username' => 'mweatherford'
			)
		);

		$this->assertEquals($expected, $result);
	}

/**
 * testViewNotFound
 *
 * @covers AppModel::view
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testViewNotFound() {
		$this->AppModel->useTable = 'users';
		//existing user in Fixtures
		$this->AppModel->view('00000000-9999-0000-0000-000000000001', array());
	}

/**
 * testGet
 *
 * @covers AppModel::get
 * @return void
 */
	public function testGet() {
		$this->AppModel->useTable = 'users';
		$result = $this->AppModel->get(AxiaTestCase::USER_REP_ID);
		$this->assertNotEmpty($result);
		$this->assertEquals('mweatherford', Hash::get($result, 'AppModel.username'));
	}

/**
 * testGetOptions
 *
 * @covers AppModel::get
 * @return void
 */
	public function testGetOptions() {
		$this->AppModel->useTable = 'users';
		$result = $this->AppModel->get(AxiaTestCase::USER_REP_ID, array(
			'fields' => array('id', 'username')
		));

		$expected = array(
			'AppModel' => array(
				'id' => AxiaTestCase::USER_REP_ID,
				'username' => 'mweatherford'
			)
		);

		$this->assertEquals($expected, $result);
	}

/**
 * testGetNotFound
 *
 * @covers AppModel::get()
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testGetNotFound() {
		$this->AppModel->useTable = 'users';
		$this->AppModel->get('00000000-9999-0000-0000-000000000001', array());
	}

/**
 * testGetList
 *
 * @covers AppModel::getList()
 * @return void
 */
	public function testGetList() {
		$this->AppModel->useTable = 'users';
		$expected = AxiaTestCase::USER_REP_ID;
		$result = $this->AppModel->getList();
		$this->assertNotEmpty($result);
		$this->assertEquals($expected, Hash::get($result, AxiaTestCase::USER_REP_ID));
	}

/**
 * testCsvPrepString
 *
 * @covers AppModel::csvPrepString()
 * @return void
 */
	public function testCsvPrepString() {
		$string = 'csv,prep,string';
		$expected = 'csv prep string';
		$result = $this->AppModel->csvPrepString($string);

		$this->assertEquals($expected, $result);
	}

/**
 * testRequestAPITaxRateWithValidZipCode
 *
 * @covers AppModel::requestAPITaxRate()
 * @return void
 */
	public function testRequestAPITaxRateWithValidZipCode() {
		$zipCode = "83254";
		$expected = '"MONTPELIER"';

		$this->httpSocket
			->expects($this->any())
			->method('get')
			->will($this->returnValue('"MONTPELIER"'));

		$result = $this->AppModel->requestAPITaxRate($zipCode);
		$this->assertTextContains($expected, $result->body);
	}

/**
 * testRequestAPITaxRateWithNonValidZipCode
 *
 * @covers AppModel::requestAPITaxRate()
 * @return void
 */
	public function testRequestAPITaxRateWithNonValidZipCode() {
		$zipCode = "invalid";
		$expected = '{}';

		$this->httpSocket
			->expects($this->any())
			->method('get')
			->will($this->returnValue('NO_CITY'));

		$result = $this->AppModel->requestAPITaxRate($zipCode);
		$this->assertEquals($expected, $result);
	}

/**
 * testEncryptAndDecrypt
 *
 * @covers AppModel::encrypt() decrypt()
 * @return void
 */
	public function testEncryptAndDecrypt() {
		$expected = "axia";
		$result = $this->AppModel->encrypt($expected, Configure::read('Security.OpenSSL.key'));
		$result = $this->AppModel->decrypt($result, Configure::read('Security.OpenSSL.key'));
		$this->assertEquals($expected, $result);
	}

/**
 * testRbacIsPermitted
 *
 * @covers AppModel::RbacIsPermitted()
 * @return void
 */
	public function testRbacIsPermitted() {
		$this->AppModel = $this->_mockModel('AppModel', [], [
			'_getCurrentUser' => [
				'matcher' => $this->once(),
				'value' => self::USER_REP_ID,
			]
		]);
		$this->assertFalse($this->AppModel->rbacIsPermitted('random/path'));
	}

/**
 * testGetNow
 *
 * @covers AppModel::_getNow()
 * @return void
 */
	public function testGetNow() {
		$method = $this->getReflectionClass('_getNow');

		$AppModel = new AppModel();

		$expected = new Datetime();
		$result = $method->invoke($AppModel);

		$this->assertEquals($expected->format('Y-m-d H:i:s'), $result->format('Y-m-d H:i:s'));
	}

/**
 * testGetHttpSocket
 *
 * @covers AppModel::_getHttpSocket()
 * @return void
 */
	public function testGetHttpSocket() {
		$method = $this->getReflectionClass('_getHttpSocket');

		$AppModel = new AppModel();

		$expected = new HttpSocket();
		$result = $method->invoke($AppModel);

		$this->assertEquals($expected, $result);
	}

/**
 * getReflectionClass method this method will return AppModal object to test their protected methods
 *
 * @param string $name method name that will be invoked
 * @return AppModel
 */
	public function getReflectionClass($name) {
		$reflection = new ReflectionClass('AppModel');

		$method = $reflection->getMethod($name);
		$method->setAccessible(true);

		return $method;
	}

/**
 * testEncryptFields
 *
 * @covers AppModel::encryptFields()
 * @return void
 */
	public function testEncryptFields() {
		$fields = array(
			'axia',
			'name'
		);

		$modelData = array(
			'axia' => 'axia',
			'name' => 'name'
		);

		$expected = array(
			'axia' => 'tdGYA+LVUNxE4dHOjlpt6N3QLxARqScB4LsWFHDBUk8z9F29qB4c7cln6IxuinPmZMdmx6obmUyN6hmZNJ8OQQ==',
			'name' => 'tdGYA+LVUNxE4dHOjlpt6ER3gNQnFAFBSkTN/GWsSZlJ3bXq8GfmxZo2wgNDpYp2WU5w07NsDATADBc0Du4GBw=='
		);

		$result = $this->AppModel->encryptFields($fields, $modelData);

		$this->assertEquals($expected, $result);
	}

/**
 * testEncryptFieldsInvalidArguments
 *
 * @covers AppModel::encryptFields()
 * @expectedException InvalidArgumentException
 * @return void
 */
	public function testEncryptFieldsInvalidArguments() {
		$fields = null;
		$modelData = null;

		$result = $this->AppModel->encryptFields($fields, $modelData);
	}

/**
 * testDecryptFields
 *
 * @covers AppModel::decryptFields()
 * @return void
 */
	public function testDecryptFields() {
		$modelData = array(
			'non_encrypted_field_1' => 'Lorem impsum dolor sit amet',
			'merchant_tin' => 'tdGYA+LVUNxE4dHOjlpt6CpFdQhTq9SVpyRP8rAmYIrZ95mTAAFlGDQtjf1BEQcqvIERswBGPrdJRT1sPGy2Xg==',
			'non_encrypted_field_2' => 'Lorem impsum dolor sit amet',
			'te_amex_number' => 'tdGYA+LVUNxE4dHOjlpt6JNTBFDdMtEgQJbGaKGseqkKnUZPG0jLKXXrCPaCwu3R4owy5mycYi6UkfFj9UTHeg=='
		);

		$expected = array(
			'non_encrypted_field_1' => 'Lorem impsum dolor sit amet',
			'merchant_tin' => '123456',
			'non_encrypted_field_2' => 'Lorem impsum dolor sit amet',
			'te_amex_number' => '987654321',
			'encrypt_fields' => 'merchant_tin,te_amex_number'
		);

		$result = $this->AppModel->decryptFields($modelData);

		$this->assertEquals($expected, $result);
	}

/**
 * testDecryptFieldsInvalidArguments
 *
 * @covers AppModel::decryptFields()
 * @expectedException InvalidArgumentException
 * @return void
 */
	public function testDecryptFieldsInvalidArguments() {
		$modelData = null;

		$result = $this->AppModel->decryptFields($modelData);
	}

/**
 * testExtractEncryptedFieldsKeys
 *
 * @covers AppModel::extractEncryptedFieldsKeys()
 * @return void
 */
	public function testExtractEncryptedFieldsKeys() {
		$modelData = array(
			'ach_mi_w_dsb_routing_number' => 'Lorem impsum dolor sit amet',
			'non_encrypted_field_1' => 'Lorem impsum dolor sit amet',
			'merchant_tin' => 'Lorem impsum dolor sit amet',
			'non_encrypted_field_2' => 'Lorem impsum dolor sit amet',
			'bank_routing_number' => 'Lorem impsum dolor sit amet',
			'non_encrypted_field_3' => 'Lorem impsum dolor sit amet',
			'owner_social_sec_no' => 'Lorem impsum dolor sit amet',
			'non_encrypted_field_4' => 'Lorem impsum dolor sit amet',
			'te_amex_number' => 'Lorem impsum dolor sit amet'
		);

		$expected = array(
			'ach_mi_w_dsb_routing_number',
			'merchant_tin',
			'bank_routing_number',
			'owner_social_sec_no',
			'te_amex_number'
		);

		$result = $this->AppModel->extractEncryptedFieldsKeys($modelData);

		$this->assertEquals($expected, $result);
	}

/**
 * testExtractEncryptedFieldsKeysInvalidArguments
 *
 * @covers AppModel::extractEncryptedFieldsKeys()
 * @expectedException InvalidArgumentException
 * @return void
 */
	public function testExtractEncryptedFieldsKeysInvalidArguments() {
		$modelData = null;

		$result = $this->AppModel->extractEncryptedFieldsKeys($modelData);
	}

/**
 * testModelExists
 *
 * @return void
 */
	public function testModelExists() {
		$this->assertTrue($this->AppModel->modelExists('Merchant'));
		$this->assertTrue($this->AppModel->modelExists('User'));
		$this->assertFalse($this->AppModel->modelExists('i-dont-exist'));
	}

/**
 * arrayIsNumeric
 *
 * @return void
 */
	public function testArrayIsNumeric() {
		for ($n = 0; $n < 1000000; $n++) {
			$vals[] = $n;
		}
		$this->assertTrue($this->AppModel->arrayIsNumeric($vals));
		$vals[] = "not-a-number";
		$this->assertFalse($this->AppModel->arrayIsNumeric($vals));
	}

/**
 * testToPercentageAmountChange
 *
 * @return void
 */
	public function testToPercentage() {
		for ($x = 100; $x > 0.01; $x -= .01) {
			$percent = CakeNumber::precision($x, 2);
			$expected = $percent / 100;
			$actual = $this->AppModel->percentToDec($percent);
			$this->assertSame($expected, $actual);
		}
	}


/**
 * testSetDecimalsNoRounding
 *
 * @covers AppModel::setDecimalsNoRounding()
 * @return void
 */
	public function testSetDecimalsNoRounding() {
		$this->assertSame(1.99, $this->AppModel->setDecimalsNoRounding(1.99999));
		$this->assertSame(1.955, $this->AppModel->setDecimalsNoRounding(1.95599, 3));
		$this->assertSame(1.9876, $this->AppModel->setDecimalsNoRounding(1.987699, 4));
	}

/**
 * testSetDecimalsNoRoundingExceptionThrown
 *
 * @covers AppModel::setDecimalsNoRounding()
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Invalid argument! Must be a number greater than or equal to zero
 * @return void
 */
	public function testSetDecimalsNoRoundingExceptionThrown() {
		$this->AppModel->setDecimalsNoRounding(1.99999, -1);
	}

/**
 * testGetRandHash
 *
 * @covers AppModel::getRandHash()
 * @return void
 */
	public function testGetRandHash() {
		$result = $this->AppModel->getRandHash();
		$this->assertNotEmpty($result);
		$this->assertSame(32, strlen($result));
		$this->assertRegExp('/\d/', $result);
		$this->assertRegExp('/\w/', $result);
	}
}
