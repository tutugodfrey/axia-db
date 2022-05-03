<?php
App::uses('Address', 'Model');
App::uses('AddressType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Address Test Case
 *
 */
class AddressTest extends AxiaTestCase {

	const MERCHANT_WITH_ADDRESS = '00000000-0000-0000-0000-000000000003';
	const MERCHANT_WITH_NO_ADDRESS = '00000000-0000-0000-0000-000000000005';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Address = ClassRegistry::init('Address');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Address);
		parent::tearDown();
	}

/**
 * testGetBusinessAddressByMerchantId
 *
 * @return void
 */
	public function testGetBusinessAddressByMerchantId() {
		$this->assertEmpty($this->Address->getBusinessAddressByMerchantId(self::MERCHANT_WITH_NO_ADDRESS));

		$merchantId = self::MERCHANT_WITH_ADDRESS;
		$result = $this->Address->getBusinessAddressByMerchantId($merchantId);
		$this->assertEquals('00000000-0000-0000-0000-000000000001', Hash::get($result, 'Address.id'));
		$this->assertEquals($merchantId, Hash::get($result, 'Address.merchant_id'));
		$this->assertEquals(AddressType::BUSINESS_ADDRESS, Hash::get($result, 'Address.address_type_id'));
		$expectedContain = ['Address'];
		$this->assertEquals($expectedContain, array_keys($result));
	}

/**
 * testGetSortedLocaleInfoByMerchantId
 *
 * @return void
 */
	public function testGetSortedLocaleInfoByMerchantId() {
		$expectedContain = ['Merchant', 'Address'];

		$merchantId = self::MERCHANT_WITH_NO_ADDRESS;
		$result = $this->Address->getSortedLocaleInfoByMerchantId($merchantId);
		$this->assertEquals($expectedContain, array_keys($result));
		$this->assertEquals($merchantId, Hash::get($result, 'Merchant.id'));
		$this->assertEmpty(Hash::get($result, 'Address'));

		$merchantId = self::MERCHANT_WITH_ADDRESS;
		$result = $this->Address->getSortedLocaleInfoByMerchantId($merchantId);
		$this->assertEquals($expectedContain, array_keys($result));
		$this->assertEquals($merchantId, Hash::get($result, 'Merchant.id'));
		$this->assertEquals(AddressType::CORP_ADDRESS, Hash::get($result, 'Address.0.address_type_id'));
		$this->assertEquals(AddressType::BUSINESS_ADDRESS, Hash::get($result, 'Address.1.address_type_id'));
		$this->assertEquals(AddressType::MAIL_ADDRESS, Hash::get($result, 'Address.2.address_type_id'));
	}

/**
 * testGetBankAddressByMerchantId
 *
 * @return void
 */
	public function testGetBankAddressByMerchantId() {
		$this->assertEmpty($this->Address->getBankAddressByMerchantId(self::MERCHANT_WITH_NO_ADDRESS));

		$merchantId = self::MERCHANT_WITH_ADDRESS;
		$result = $this->Address->getBankAddressByMerchantId($merchantId);
		$this->assertEquals('00000000-0000-0000-0000-000000000002', Hash::get($result, 'Address.id'));
		$this->assertEquals($merchantId, Hash::get($result, 'Address.merchant_id'));
		$this->assertEquals(AddressType::BANK_ADDRESS, Hash::get($result, 'Address.address_type_id'));
		$expectedContain = ['Address'];
		$this->assertEquals($expectedContain, array_keys($result));
	}

/**
 * testGetMerchantBusinessData
 *
 * @return void
 */
	public function testGetMerchantBusinessData() {
		$expectedContain = ['Merchant', 'Address', 'MerchantReference'];

		$merchantId = self::MERCHANT_WITH_NO_ADDRESS;
		$result = $this->Address->getMerchantBusinessData($merchantId);
		$this->assertEquals($expectedContain, array_keys($result));
		$this->assertEquals($merchantId, Hash::get($result, 'Merchant.id'));
		$this->assertEmpty(Hash::get($result, 'Address'));

		$merchantId = self::MERCHANT_WITH_ADDRESS;
		$result = $this->Address->getMerchantBusinessData($merchantId);
		$this->assertEquals($expectedContain, array_keys($result));
		$this->assertEquals($merchantId, Hash::get($result, 'Merchant.id'));
		$this->assertCount(4, Hash::get($result, 'Address'));
		$this->assertNotEmpty(Hash::get($result, 'Address.0.AddressType'));
	}

/**
 * testIsValidZip
 * Test custom data validation method
 * 
 * @covers Address::isValidZip
 * @return void
 */
	public function testIsValidZip() {
		$validUSASample = [
			'val1' => "35801",
			'val2' => "99501",
			'val3' => "85001",
			'val4' => "72201",
			'val5' => "94203",
			'val6' => "94209",
			'val7' => "90001",
			'val8' => "90089",
			'val9' => "90209",
			'val10' => "90213",
			'val11' => "70112",
			'val12' => "70119",
			'val13' => "04032",
			'val14' => "04034",
			'val15' => "21201",
			'val16' => "21237",
			'val17' => "02101",
			'val18' => "02137"
		];
		$inValidSample = [
			'val1' => "1",
			'val2' => "Junk1",
			'val3' => "nowhere",
			'val4' => "notown",
			'val5' => "lost",
			'val6' => "where?",
			'val7' => "space",
			'val8' => "tatoine",
			'val9' => "valhala",
			'val10' => "mars4",
			'val11' => "moon2",
			'val12' => "earth3",
			'val13' => "mercury",
			'val14' => "venus2",
			'val15' => "jupiter",
			'val16' => "saturn",
			'val17' => "neptune",
			'val18' => "1111111111toolong"
		];
		$validCanadaSample = [
			'val1' => "G4A 2P1",
			'val2' => "K9V 2V0",
			'val3' => "J3V 1P0",
			'val4' => "H9K 2A5",
			'val5' => "J0X 3G1",
			'val6' => "G0T 0V6",
			'val7' => "N0L 0S3",
			'val8' => "E5V 3E3",
			'val9' => "B3V 2R8",
			'val10' => "B1G 1X0",
			'val11' => "H3N 1B4",
			'val12' => "T9N 0Z7",
			'val13' => "E4T 1Y5",
			'val14' => "G0Y 2E7",
			'val15' => "E1B 3B9",
			'val16' => "E9G 1J9",
			'val17' => "L7J 3X2",
			'val18' => "K6T 2G9",

		];
		//emulate setting the data for a save operation 
		$this->Address->set(
			[
				'address_title' => 'test',
				'address_street' => 'test',
				'address_city' => 'test',
				'address_state' => 'test',
			]
		);

		foreach ($validUSASample as $key => $validZip) {
			$this->assertTrue($this->Address->isValidZip(['address_zip' => $validZip]));
			$this->assertTrue($this->Address->isValidZip(['address_zip' => $validCanadaSample[$key]]));
			$this->assertFalse($this->Address->isValidZip(['address_zip' => $inValidSample[$key]]));
		}
		$expected = 'You have entered an address with a missing ZIP code. Please enter zip code.';
		$actual = $this->Address->isValidZip(['address_zip' => null]);
		$this->assertSame($expected, $actual);

		$this->Address->clear();
		$this->Address->set([
				'Address' => [
					'address_type_id' => AddressType::BANK_ADDRESS,
				]
			]
		);
		$this->assertTrue($this->Address->isValidZip(['address_zip' => null]));

		$this->Address->clear();
		$this->Address->set([
				'Address' => [
					'address_type_id' => AddressType::MAIL_ADDRESS,
				]
			]
		);
		$this->assertTrue($this->Address->isValidZip(['address_zip' => null]));

		$this->Address->clear();
		$this->Address->set([
				'Address' => [
					'address_title' => null,
					'address_street' => null,
					'address_city' => null,
					'address_state' => null,
				]
			]
		);
		$this->assertTrue($this->Address->isValidZip(['address_zip' => '93101']));
	}

/**
 * testGetStreetByMerchantIds
 * 
 * @covers Address::getStreetByMerchantIds
 * @return void
 */
	public function testGetStreetByMerchantIds() {
		$merchantIds = $this->Address->find('all', ['fields' => ['DISTINCT merchant_id']]);
		$actual = $this->Address->getStreetByMerchantIds(Hash::extract($merchantIds, '{n}.Address.merchant_id'));
		$expected = ['street-1' => 'street-1'];
		$this->assertSame($expected, $actual);

		$actual = $this->Address->getStreetByMerchantIds([]);
		$expected = ['street-1' => 'street-1'];
		$this->assertSame($expected, $actual);

	}

/**
 * testAfterSave
 * 
 * @covers Address::afterSave
 * @return void
 */
	public function testAfterSave() {
		$data = [
			'Address' => [
				'address_type_id' => AddressType::BANK_ADDRESS,
				'merchant_id' => '00000000-0000-0000-0000-000000000001',
				'address_title' => 'Test update Bank Name in after Address save'
			]
		];
		$nameBefore = $this->Address->Merchant->MerchantBank->field('bank_name');
		$this->Address->set($data);
		$this->Address->afterSave(false, []);
		$nameAfter = $this->Address->Merchant->MerchantBank->field('bank_name');
		$this->assertFalse(($nameBefore === $nameAfter));
		$this->assertSame($nameAfter, $data['Address']['address_title']);
	}
/**
 * testSetEditViewVars
 * 
 * @covers Address::setEditViewVars
 * @return void
 */
	public function testSetEditViewVars() {
		$MerchantChangeModel = $this->getMockForModel('MerchantChange', ['_getCurrentUser']);
		$MerchantChangeModel->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$testData = [
			'Merchant' => ['id' => '00000000-0000-0000-0000-000000000003'],
			'MerchantNote' => [['loggable_log_id' => '565d863d-13c8-44a8-bb72-03fbc0a8ad65']],
		];
		$actual = $this->Address->setEditViewVars($testData);

		$expected = [
			'merchant' => [
				'Merchant' => [
					'merchant_dba' => 'Another Merchant',
					'merchant_mid' => '3948000030003049',
					'id' => '00000000-0000-0000-0000-000000000003',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e'

				],
				'User' => [
					'user_first_name' => 'Slim',
					'user_last_name' => 'Pickins',
					'id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e'
				],
			],
			'isEditLog' => true,
			'userCanApproveChanges' => true
		];
		$this->assertSame($expected, $actual);
	}
}
