<?php
App::uses('MerchantOwner', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantOwner Test Case
 *
 */
class MerchantOwnerTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantOwner = ClassRegistry::init('MerchantOwner');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantOwner);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers MerchantOwner::beforeValidate()
 * @return void
 */
	public function testBeforeValidateAddressRules() {
		$data = [
			'MerchantOwner' => [
				'owner_name' => 'test-name',
			],
			'Address' => [
				'address_street' => 'test-street'
			]
		];

		// Adds address rules if MerchantOwner is set
		$this->MerchantOwner->set($data);
		$addressValidator = $this->MerchantOwner->Address->validator();
		$this->assertSame(3, $addressValidator->count());
		$this->assertEmpty($addressValidator->getField('address_street'));
		$this->assertEmpty($addressValidator->getField('address_city'));
		$this->assertEmpty($addressValidator->getField('address_state'));
		$this->assertNotEmpty(Hash::get($this->MerchantOwner->data, 'MerchantOwner.owner_name'));
		$this->assertNotEmpty(Hash::get($this->MerchantOwner->data, 'Address'));

		$this->MerchantOwner->beforeValidate();
		$addressValidator = $this->MerchantOwner->Address->validator();
		$this->assertSame(6, $addressValidator->count());
		$this->assertNotEmpty($addressValidator->getField('address_street'), 'No validation rule was added for address_street');
		$this->assertNotEmpty($addressValidator->getField('address_city'), 'No validation rule was added for address_city');
		$this->assertNotEmpty($addressValidator->getField('address_state'), 'No validation rule was added for address_state');
		$merchantNoteValidator = $this->MerchantOwner->Merchant->MerchantNote->validator();
		$this->assertNotEmpty($merchantNoteValidator->getField('note'), 'No validation rule was added for note');

		$this->MerchantOwner->create();
		$this->MerchantOwner->set([]);
		$this->MerchantOwner->beforeValidate();
		$addressValidator = $this->MerchantOwner->Address->validator();
		$this->assertSame(2, $addressValidator->count());
		$this->assertEmpty($addressValidator->getField('address_street'), 'address_street rule was not removed');
		$this->assertEmpty($addressValidator->getField('address_city'), 'address_city rule was not removed');
		$this->assertEmpty($addressValidator->getField('address_state'), 'address_state rule was not removed');
		$this->assertEmpty($addressValidator->getField('address_zip'), 'address_zip rule was not removed');
		$merchantNoteValidator = $this->MerchantOwner->Merchant->MerchantNote->validator();
		$this->assertNotEmpty($merchantNoteValidator->getField('note'), 'No validation rule was added for note');
	}

/**
 * test
 *
 * @covers MerchantOwner::cleanRequestData()
 * @return void
 */
	public function testCleanRequestData() {
		$data = [
			'MerchantOwner' => [
				0 => [],
				1 => ['owner_name' => 'name1'],
				2 => [
					'owner_name' => 'name2',
					'owner_title' => 'title'
				],
				3 => ['owner_equity' => 'equity'],
				4 => ['owner_social_sec_no' => 'social'],
				5 => ['random_field' => 'random-value'],
			],
		];
		$expectedCleanData = [
			'MerchantOwner' => [
				1 => ['owner_name' => 'name1'],
				2 => [
					'owner_name' => 'name2',
					'owner_title' => 'title'
				],
				3 => ['owner_equity' => 'equity'],
				4 => ['owner_social_sec_no' => 'social'],
			],
		];
		$this->assertSame($expectedCleanData, $this->MerchantOwner->cleanRequestData($data));
	}

/**
 * test
 *
 * @covers MerchantOwner::beforeSave()
 * @return void
 */
	public function testBeforeSave() {
		$data = [
			'MerchantOwner' => [
				'owner_name' => 'Owner 1',
			]
		];
		$this->MerchantOwner->set($data);
		$this->MerchantOwner->beforeSave();
		$this->assertSame($data, $this->MerchantOwner->data);

		$data = [
			'MerchantOwner' => [
				'owner_name' => 'Owner 1',
				'owner_social_sec_no' => 'ssn-123456789'
			]
		];
		$expectedData = [
			'MerchantOwner' => [
				'owner_name' => 'Owner 1',
				'owner_social_sec_no' => 'tdGYA+LVUNxE4dHOjlpt6EDVJ0d8C5w0rmXH0ZozHTV/TgRE8t4i/7W6PJw0VijjArG5FVFXU+hQu3cpTcg3KQ==',
				'encrypt_fields' => 'owner_social_sec_no',
				'owner_social_sec_no_disp' => '6789'
			]
		];
		$this->MerchantOwner->set($data);
		$this->MerchantOwner->beforeSave();
		$this->assertSame($expectedData, $this->MerchantOwner->data);
	}

/**
 * test
 *
 * @covers MerchantOwner::getOwnersByMerchantId()
 * @return void
 */
	public function testGetOwnersByMerchantId() {
		$this->assertEmpty($this->MerchantOwner->getOwnersByMerchantId('00000000-0000-0000-0000-000000000001'));

		$result = $this->MerchantOwner->getOwnersByMerchantId('00000000-0000-0000-0000-000000000003');
		$this->assertSame('Corporation - LLC', Hash::get($result, 'Merchant.merchant_ownership_type'));
		$this->assertArrayHasPath('MerchantOwner', $result, 'Missing MerchantOwner data');
		$expectedOwners = [
			'00000000-0000-0000-0000-000000000001',
			'00000000-0000-0000-0000-000000000002'
		];
		$this->assertSame($expectedOwners, Hash::extract($result, 'MerchantOwner.{n}.id'));
		$this->assertArrayHasPath('MerchantOwner.0.Address', $result, 'Missing Address data');
		$expectedAddresses = [
			'00000000-0000-0000-0000-000000000001',
			'00000000-0000-0000-0000-000000000002'
		];
		$this->assertSame($expectedAddresses, Hash::extract($result, 'MerchantOwner.{n}.Address.id'));
	}
}
