<?php
App::uses('UsersProductsRisk', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * UsersProductsRisk Test Case
 *
 */
class UsersProductsRiskTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UsersProductsRisk = ClassRegistry::init('UsersProductsRisk');

		$tmpPstHolder = ClassRegistry::init('ProductsServicesType')->find('list', [
						'fields' => [
							'ProductsServicesType.products_services_description',
							'ProductsServicesType.id'
						],
						'conditions' => [
							'ProductsServicesType.is_active' => true
						]
					]);
		$this->expectedKeys = array_keys($tmpPstHolder);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UsersProductsRisk);
		parent::tearDown();
	}

/**
 * testGetByMerchant
 *
 * @covers UsersProductsRisk::getByMerchant
 * testGetByMerchant method
 *
 * @return void
 */
	public function testGetByMerchant() {
		$merchantId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
		$result = $this->UsersProductsRisk->getByMerchant($merchantId);
		$this->assertEquals($this->expectedKeys, array_keys($result));

		$expectedAch = [
			0 => [
				'id' => '557621a9-ef88-4905-b24d-2a8134627ad4',
				'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
				'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
				'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
				'risk_assmnt_pct' => '0.1',
				'risk_assmnt_per_item' => '0.5'
			],
			1 => null,
			2 => null,
			3 => [
				'id' => '557621a9-ef88-4905-b24d-2a8134627ad4',
				'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
				'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
				'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
				'risk_assmnt_pct' => '0.1',
				'risk_assmnt_per_item' => '0.5'
			],
			4 => null,
			5 => null
		];

		$this->assertEquals($expectedAch, Hash::get($result, 'ACH'));

		$expectedDebitSales = [
			114 => null,
			115 => null,
			116 => null,
			117 => null,
			118 => null,
			119 => null
		];

		$this->assertEquals($expectedDebitSales, Hash::get($result, 'Debit Sales'));

		$expectedVisaVolume = [
			330 => null,
			331 => null,
			332 => null,
			333 => null,
			334 => null,
			335 => null,
		];

	}

/**
 * testSortRiskDataByProductsAndUsers
 *
 * @covers UsersProductsRisk::_sortRiskDataByProductsAndUsers
 * testSortRiskDataByProductsAndUsers method
 *
 * @return void
 */
	public function testSortRiskDataByProductsAndUsers() {
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$data = [
			'UsersProductsRisk' => [
				'id' => '557621a9-0b44-476d-a082-2a8134627ad4',
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'user_id' => '02eb04ab-1f70-4f80-bc83-e16f9a86e764',
				'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
				'risk_assmnt_pct' => '0.1',
				'risk_assmnt_per_item' => '0.5'
			]
		];

		$reflection = new ReflectionClass('UsersProductsRisk');

		$method = $reflection->getMethod('_sortRiskDataByProductsAndUsers');
		$method->setAccessible(true);
		$result = $method->invokeArgs($this->UsersProductsRisk, [$data, $merchantId]);
		$this->assertEquals($this->expectedKeys, array_keys($result));

		$expectedAch = [
			0 => null,
			1 => null,
			2 => null,
			3 => null,
			4 => null,
			5 => null
		];
		$this->assertEquals($expectedAch, Hash::get($result, 'ACH'));

		$expectedDebitSales = [
			114 => null,
			115 => null,
			116 => null,
			117 => null,
			118 => null,
			119 => null
		];
		$this->assertEquals($expectedDebitSales, Hash::get($result, 'Debit Sales'));

		$expectedVisaVolume = [
			402 => null,
			403 => null,
			404 => null,
			405 => null,
			406 => null,
			407 => null,
		];
	}

/**
 * testSortRiskDataByProductsAndUsersIncluded
 *
 * @covers UsersProductsRisk::_sortRiskDataByProductsAndUsers
 * testSortRiskDataByProductsAndUsersIncluded method
 *
 * @return void
 */
	public function testSortRiskDataByProductsAndUsersIncluded() {
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$data = [
			'UsersProductsRisk' => [
				'id' => '557621a9-0b44-476d-a082-2a8134627ad4',
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'user_id' => '02eb04ab-1f70-4f80-bc83-e16f9a86e764',
				'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
				'risk_assmnt_pct' => '0.1',
				'risk_assmnt_per_item' => '0.5'
			]
		];

		$reflection = new ReflectionClass('UsersProductsRisk');

		$method = $reflection->getMethod('_sortRiskDataByProductsAndUsers');
		$method->setAccessible(true);
		$result = $method->invokeArgs($this->UsersProductsRisk, [$data, $merchantId, true]);
		$this->assertEquals($this->expectedKeys, array_keys($result));

		$expectedAch = [
			0 => [
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
				'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7'
			],
			1 => null,
			2 => null,
			3 => null,
			4 => null,
			5 => null
		];
		$this->assertEquals($expectedAch, Hash::get($result, 'ACH'));

		$expectedDebitSales = [
			114 => [
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
				'products_services_type_id' => '72a445f3-3937-4078-8631-1f569d6a30ed'
			],
			115 => null,
			116 => null,
			117 => null,
			118 => null,
			119 => null,
		];
		$this->assertEquals($expectedDebitSales, Hash::get($result, 'Debit Sales'));

		$expectedVisaVolume = [
			402 => [
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
				'products_services_type_id' => '551038c2-0ed8-48c1-9161-1a3f34627ad4'
			],
			403 => null,
			404 => null,
			405 => null,
			406 => null,
			407 => null,
		];
	}

/**
 * testSortRiskDataByProductsAndUsersException
 *
 * @covers UsersProductsRisk::_sortRiskDataByProductsAndUsers
 * @expectedException InvalidArgumentException
 * testSortRiskDataByProductsAndUsersException method
 *
 * @return void
 */
	public function testSortRiskDataByProductsAndUsersException() {
		$merchantId = 'invalid';
		$data = [];

		$reflection = new ReflectionClass('UsersProductsRisk');

		$method = $reflection->getMethod('_sortRiskDataByProductsAndUsers');
		$method->setAccessible(true);
		$result = $method->invokeArgs($this->UsersProductsRisk, [$data, $merchantId, true]);
	}

/**
 * cleanSaveData
 *
 * @covers UsersProductsRisk::cleanSaveData
 * cleanSaveData method
 *
 * @return void
 */
	public function testCleanSaveData() {
		$data = [
			'UsersProductsRisk' => [
				[
					'id' => '557621a9-0b44-476d-a082-2a8134627ad4',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'user_id' => '02eb04ab-1f70-4f80-bc83-e16f9a86e764',
					'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
					'risk_assmnt_pct' => '0.1',
					'risk_assmnt_per_item' => '0.5'
				]
			]
		];

		$result = $this->UsersProductsRisk->cleanSaveData($data);
		$expected = [
			0 => [
				'id' => '557621a9-0b44-476d-a082-2a8134627ad4',
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
				'user_id' => '02eb04ab-1f70-4f80-bc83-e16f9a86e764',
				'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
				'risk_assmnt_pct' => '0.1',
				'risk_assmnt_per_item' => '0.5'
			]
		];

		$this->assertEquals($expected, $result);
	}
}