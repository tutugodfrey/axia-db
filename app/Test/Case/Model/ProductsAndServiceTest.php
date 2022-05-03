<?php
App::uses('ProductsAndService', 'Model');
App::uses('ProductsServicesType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ProductsAndService Test Case
 *
 */
class ProductsAndServiceTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductsAndService = ClassRegistry::init('ProductsAndService');
		$this->MerchantPricing = ClassRegistry::init('MerchantPricing');
		$this->Ach = ClassRegistry::init('Ach');
		$this->WebBasedAch = ClassRegistry::init('WebBasedAch');
		$this->CheckGuarantee = ClassRegistry::init('CheckGuarantee');
		$this->Gateway1 = ClassRegistry::init('Gateway1');
		$this->GiftCard = ClassRegistry::init('GiftCard');
		$this->PaymentFusion = ClassRegistry::init('PaymentFusion');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductsAndService);
		parent::tearDown();
	}

/**
 * testGetReportViewData
 *
 * @covers ProductsAndService::getReportViewData()
 * @return void
 */
	public function testGetReportViewData(){
		$actual = $this->ProductsAndService->getReportViewData();
		$this->assertContains('Visa Sales', $actual['productsServicesTypes']['Current Products']);
		$this->assertContains('Bankcard', $actual['productsServicesTypes']['Legacy Products']);
		$this->assertContains('Entity 1', $actual['entities']);
	}

/**
 * test_validationErrorsString
 *
 * @covers ProductsAndService::_validationErrorsString()
 * @return void
 */
	public function test_validationErrorsString(){
		$reflection = new ReflectionClass('ProductsAndService');
		$method = $reflection->getMethod('_validationErrorsString');
		$method->setAccessible(true);
		//provoke a validation error
		$this->PaymentFusion->save(['id' => CakeText::uuid(), 'merchant_id' => '']);

		$result = $method->invokeArgs($this->ProductsAndService, [$this->PaymentFusion]);
		$this->assertEquals('Merchant id cannot be blank. Product id is required', $result);
		
		$this->PaymentFusion->clear();
		$result = $method->invokeArgs($this->ProductsAndService, [$this->PaymentFusion]);
		$this->assertEmpty($result);
	}

/**
 * testProductConditions
 *
 * @covers ProductsAndService::productConditions()
 * @return void
 */
	public function testProductConditions(){
		$fiterArgs = [
				'products_services_type_id' => ProductsServicesType::ALL_LEGACY_OPTN
		];
		$actual = $this->ProductsAndService->productConditions($fiterArgs);
		$expected = ["ProductsAndService.products_services_type_id IN (SELECT id from products_services_types where is_legacy = true)"];
		$this->assertSame($expected, $actual);

		$fiterArgs = ['products_services_type_id' => CakeText::uuid()];
		$actual = $this->ProductsAndService->productConditions($fiterArgs);
		$expected = ["ProductsAndService.products_services_type_id" => $fiterArgs['products_services_type_id']];;
		$this->assertSame($expected, $actual);
	}

/**
 * testOrConditions
 *
 * @covers ProductsAndService::orConditions
 * @return void
 */
	public function testOrConditions(){
		$fiterArgs = ['dba_mid' => 'Healing Clinic'];
		$actual = $this->ProductsAndService->orConditions($fiterArgs);
		$expected = [
			'OR' => [
				'Merchant.merchant_mid ILIKE' => '%Healing Clinic%',
                'Merchant.merchant_dba ILIKE' => '%Healing Clinic%'
			]
		];
		$this->assertSame($expected, $actual);

		$fiterArgs = ['dba_mid' => '1239005599999999'];
		$actual = $this->ProductsAndService->orConditions($fiterArgs);
		$expected = [
			'OR' => [
				'Merchant.merchant_mid ILIKE' => '%1239005599999999%',
                'Merchant.merchant_dba ILIKE' => '%1239005599999999%'
			]
		];
		$this->assertSame($expected, $actual);

	}

/**
 * test
 *
 * @covers ProductsAndService::add()
 * @return void
 */
	public function testAdd() {
		//Test product without model.
		//The merchant id doesn't have to match any existing ones
		$merchantId = $this->UUIDv4();
		//Test add a product that does not have it's own model and/or table
		$actual = $this->ProductsAndService->add($merchantId, '952e7099-42b7-48d0-909b-6cb09d4d6706');
		$expected = "/MerchantPricings/edit/" . $this->MerchantPricing->id;
		$this->assertEqual($actual, $expected);
		//Test add a second product that does not have it's own model and/or table
		$actual = $this->ProductsAndService->add($merchantId, '0582831b-4aaf-404e-8d92-b6aaf89ac3f3');
		$expected = "/MerchantPricings/products_and_services/" . $merchantId;
		$this->assertEqual($actual, $expected);

		//Test add ACH product with model.
		$actual = $this->ProductsAndService->add($merchantId, 'e8fa66a0-790f-4710-b7de-ef79be75a1c7');
		$expected = "/Aches/edit/" . $this->Ach->id;
		$this->assertEqual($actual, $expected);

		//Test add WebBasedAch product with model.
		$actual = $this->ProductsAndService->add($merchantId, '551038c4-3370-47c3-bb4c-1a3f34627ad4');
		$expected = "/WebBasedAches/edit/" . $this->WebBasedAch->id;
		$this->assertEqual($actual, $expected);

		//Test add WebBasedAch product with model.
		$actual = $this->ProductsAndService->add($merchantId, '762137fb-a8ef-457f-83b1-6591a1fd7596');
		$expected = "/CheckGuarantees/edit/" . $this->CheckGuarantee->id;
		$this->assertEqual($actual, $expected);

		//Test add WebBasedAch product with model.
		$actual = $this->ProductsAndService->add($merchantId, 'f446b74f-ae19-4505-82c7-67cf93fcfe3d');
		$expected = "/Gateway1s/edit/" . $this->Gateway1->id;
		$this->assertEqual($actual, $expected);

		//Test add WebBasedAch product with model.
		$actual = $this->ProductsAndService->add($merchantId, 'ada3799b-1671-4239-b79b-4cabe2de2bbc');
		$expected = "/GiftCards/edit/" . $this->GiftCard->id;
		$this->assertEqual($actual, $expected);
	}

/**
 * test
 *
 * @covers ProductsAndService::getProductsAndServicesByMID()
 * @return void
 */
	public function testGetProductsAndServicesByMID() {
		$this->assertEmpty($this->ProductsAndService->getProductsAndServicesByMID('3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'));

		$expectedProducts = [
			[
				'ProductsServicesType' => [
					'id' => '952e7099-42b7-48d0-909b-6cb09d4d6706',
					'products_services_description' => 'American Express',
					'is_active' => false
				]
			],
			[
				'ProductsServicesType' => [
					'id' => '72a445f3-3937-4078-8631-1f569d6a30ed',
					'products_services_description' => 'Debit Sales',
					'is_active' => true
				]
			],
			[
				'ProductsServicesType' => [
					'id' => 'ad536ee9-1ecc-48ca-8461-3e4525c4727b',
					'products_services_description' => 'USA ePay',
					'is_active' => false
				]
			]
		];

		$this->assertSame($expectedProducts, $this->ProductsAndService->getProductsAndServicesByMID('00000000-0000-0000-0000-000000000003'));
	}

/**
 * test
 *
 * @covers ProductsAndService::hasProduct()
 * @return void
 */
	public function testHasProduct() {
		//Arbitrary Ids
		$merchantId = $this->UUIDv4();
		$productId = $this->UUIDv4();
		$result = $this->ProductsAndService->hasProduct($merchantId, $productId);
		$this->assertFalse($result);

		$this->ProductsAndService->create();
		$this->ProductsAndService->save(array('merchant_id' => $merchantId, 'products_services_type_id' => $productId));
		$result = $this->ProductsAndService->hasProduct($merchantId, $productId);
		$this->assertTrue($result);

		$testParams = [
			'id' => '5b807318-2b94-4e90-8bcc-1d7d0a0101f3',
			'PaymentFusion' => '9db324ec-8365-4ae2-9b49-1e575113d5df',
			'PaymentFusion-merchant_id' => '5b807318-d720-4816-af11-1d7d0a0101f3'
		];
		//test with product that has its own model and its current out of sync state will change and become synced with ProductsAndService
		$this->PaymentFusion->create();
		$this->PaymentFusion->save(['merchant_id' => $testParams['PaymentFusion-merchant_id']], ['validate' => false]);
		
		$result = $this->ProductsAndService->hasProduct($testParams['PaymentFusion-merchant_id'], $testParams['PaymentFusion']);
		$this->assertTrue($result);
	}

/**
 * testMerchantProductsCustomSearch
 *
 * @return void
 */
	public function testMerchantProductsCustomSearch() {
		$expected = array(
			array(
				'Merchant' => array(
						'merchant_mid' => '3948000030003049',
						'merchant_dba' => 'Another Merchant'
				),
				'Client' => array(
		            'client_id_global' => null
		        ),
				'user' => array(
						'full_name' => 'Slim Pickins'
				),
				'UwStatusMerchantXref' => array(
						'datetime' => null
				),
				'MerchantPricing' => array(
						'ds_user_not_paid' => false
				),
				'VisaProduct' => array(
						'names' => null
				),
				'MasterCardProduct' => array(
						'names' => null
				),
				'DiscoverProduct' => array(
						'names' => null
				),
				'AmexProduct' => array(
						'names' => null
				),
				'DebitProduct' => array(
						'names' => 'Debit Sales'
				),
				'EbtProduct' => array(
						'names' => null
				),
				'OtherProduct' => array(
						'names' => null
				),
				'VisaBet' => array(
						'name' => null
				),
				'MasterCardBet' => array(
						'name' => null
				),
				'DiscoverBet' => array(
						'name' => null
				),
				'AmexBet' => array(
						'name' => null
				),
				'DebitBet' => array(
						'name' => null
				),
				'EbtBet' => array(
						'name' => null
				),
				'Entity' => array(
						'entity_acronym' => null
				)
			)
		);
		$actual = $this->ProductsAndService->find('merchantProducts', ['conditions' => ['Merchant.merchant_mid' => '3948000030003049']]);
		$this->assertSame($expected, $actual);
	}
}
