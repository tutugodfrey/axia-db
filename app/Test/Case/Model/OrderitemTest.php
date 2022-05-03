<?php
App::uses('Orderitem', 'Model');
App::uses('AxiaTestCase', 'Test');

class OrderitemTestCase extends AxiaTestCase {

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array();

/**
 * Start Test callback
 *
 * @param string $method Method name
 * @return void
 */
	public function startTest($method) {
		parent::startTest($method);
		$this->Orderitem = ClassRegistry::init('Orderitem');
	}

/**
 * End Test callback
 *
 * @param string $method Method name
 * @return void
 */
	public function endTest($method) {
		parent::endTest($method);
		unset($this->Orderitem);
		ClassRegistry::flush();
	}

/**
 * testGetAllOrderitemsByOrderId
 *
 * @return void
 */
	public function testGetAllOrderitemsByOrderId() {
		$orderId = '07afa03c-7a54-4732-8d21-2aab3abddc1b';

		$result = $this->Orderitem->getAllOrderitemsByOrderId($orderId);
		$this->assertCount(4, Hash::get($result, 'Orderitem'));
		$this->assertCount(4, Hash::extract($result, "Orderitem.{n}[order_id={$orderId}]"));

		$expectedEquipmentDescriptions = [
			'Nurit 2085 Ref',
			'Micr Mini Ref',
			'Tax',
			'Shipping'
		];
		$this->assertEquals($expectedEquipmentDescriptions, Hash::extract($result, "Orderitem.{n}.equipment_item_description"));

		$expectedContains = [
			'Merchant',
			'EquipmentItem',
			'EquipmentType',
			'Warranty',
			'ShippingTypeItem',
			'OrderitemType',
			'OrderitemsReplacement',
		];
		$this->assertArrayHasPaths($expectedContains, Hash::get($result, "Orderitem.0"));
	}

/**
 * testGetAllOrderitemsByOrderId
 *
 * @return void
 */
	public function testGetAllOrderitemsByOrderIdEmpty() {
		$result = $this->Orderitem->getAllOrderitemsByOrderId('00000000-9999-0000-0000-000000000001');
		$this->assertEmpty(Hash::get($result, 'Orderitems'));
	}

/**
 * testGetItemTaxAmount
 *
 * @covers Orderitem::getItemTaxAmount
 * @test
 * @return void
 */
	public function testGetItemTaxAmount() {
		//Get sales tax rate from API
		$taxRate = json_decode($this->Orderitem->requestAPITaxRate('93101', 'CA', 'Santa Barbara'))->results[0]->taxSales;
		$expected = bcmul(10, $taxRate, 2);
		$actual = $this->Orderitem->getItemTaxAmount(10);
		$this->assertSame($actual, $expected);
	}

/**
 * test
 *
 * @covers Orderitem::getItemTaxAmount()
 * @return void
 */
	public function testGetItemTaxAmountEmpty() {
		$this->assertSame(0, $this->Orderitem->getItemTaxAmount(null));
		$this->assertSame(0, $this->Orderitem->getItemTaxAmount(0));
		$this->assertSame(0, $this->Orderitem->getItemTaxAmount(''));
	}

/**
 * test
 *
 * @covers Orderitem::getItemTaxAmount()
 * @expectedException HttpException
 * @expectedExceptionMessage TAX API responded with error: 101
 * @return void
 */
	public function testGetItemTaxAmountApiError() {
		$httpSocket = new HttpSocket();
		$OrderItemModel = $this->getMockForModel('Orderitem', ['requestAPITaxRate']);
		$OrderItemModel->expects($this->once())
			->method('requestAPITaxRate')
			->will($this->returnValue($httpSocket->get('http://api.zip-tax.com/invalid-end-point')));

		$OrderItemModel->getItemTaxAmount(100);
	}

/**
 * test
 *
 * @covers Orderitem::getSearchSettings()
 * @return void
 */
	public function testGetSearchSettings() {
		$orderId = '17afa03c-7a54-4732-8d21-2aab3abddc1b';

		$expectedSettings = [
			'limit' => 9999,
			'conditions' => [
				'Orderitem.order_id' => $orderId
			],
			'contain' => [
				'Merchant' => [
					'fields' => [
						'Merchant.id',
						'Merchant.merchant_mid',
						'Merchant.merchant_dba'
					]
				],
				'EquipmentItem',
				'EquipmentType',
				'Warranty',
				'ShippingTypeItem',
				'OrderitemType',
				'OrderitemsReplacement' => [
					'AxiaToMerchantShippingType',
					'MerchantToVendorShippingType',
					'VendorToAxiaShippingType'
				]
			]
		];
		$settings = $this->Orderitem->getSearchSettings($orderId);
		$this->assertEquals($expectedSettings, $settings);
	}

/**
 * test
 *
 * @param string $uuid uuid value
 * @covers Orderitem::isValidMerchantUUID()
 * @dataProvider providerIsValidMerchantUUID
 * @return void
 */
	public function testIsValidMerchantUUID($uuid) {
		$this->assertTrue($this->Orderitem->isValidMerchantUUID(['merchant_id' => $uuid]));
	}

/**
 * Provider for testIsValidMerchantUUID
 *
 * @return array
 */
	public function providerIsValidMerchantUUID() {
		return [
			['534bdba9-e8a8-4a75-9406-2ce134627ad4'],
			['00ccf87a-4564-4b95-96e5-e90df32c46c1'],
			['871e3266-4855-4b71-80f2-efeaa6892469'],
		];
	}

/**
 * test
 *
 * @param string $uuid uuid value
 * @covers Orderitem::isValidMerchantUUID()
 * @dataProvider providerIsValidMerchantUUIDNotValid
 * @return void
 */
	public function testIsValidMerchantUUIDNotValid($uuid) {
		$this->assertFalse($this->Orderitem->isValidMerchantUUID(['merchant_id' => $uuid]));
	}

/**
 * Provider for testIsValidMerchantUUIDNotValid
 *
 * @return array
 */
	public function providerIsValidMerchantUUIDNotValid() {
		return [
			[556],
			['not-valid-uui'],
			['871e3266-48555555-4b71-80f2-efeaa6892469'],
			['871e3266-4855-4b71-80f22222-efeaa6892469'],
		];
	}

/**
 * test
 *
 * @covers Orderitem::isValidMerchantUUID()
 * @expectedException BadFunctionCallException
 * @expectedExceptionMessage Missing 'merchant_id' from the uuidCheck data
 * @return void
 */
	public function testIsValidMerchantUUIDBadFunctionCall() {
		$this->Orderitem->isValidMerchantUUID('871e3266-4855-4b71-80f2-efeaa6892469');
	}

/**
 * test
 *
 * @covers Orderitem::beforeValidate()
 * @return void
 */
	public function testBeforeValidateCheckMerchantIdRule() {
		$merchant = $this->Orderitem->Merchant->read(null, '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040');

		// Adds merchant_id rule if merchant_mid is set
		$this->Orderitem->set([
			'Merchant' => [
				'merchant_mid' => $merchant['Merchant']['merchant_mid'],
			],
		]);
		$validator = $this->Orderitem->validator();
		$this->assertSame(6, $validator->count());
		$this->assertEmpty($validator->getField('merchant_id'));
		$this->assertNotEmpty(Hash::get($this->Orderitem->data, 'Merchant.merchant_mid'));

		$this->Orderitem->beforeValidate();
		$validator = $this->Orderitem->validator();
		$this->assertSame(7, $validator->count(), 'No validation rule was added');
		$this->assertNotEmpty($validator->getField('merchant_id'), 'Missing merchant_id validation rules');
		$this->assertEmpty(Hash::get($this->Orderitem->data, 'Merchant'), 'Merchant data was not removed');
		$this->assertSame(
			$merchant['Merchant']['id'],
			Hash::get($this->Orderitem->data, 'Orderitem.merchant_id'),
			'Merchant id was not added to Orderitem data'
		);

		// Removed merchant_id rule if merchant_mid is not set
		$this->Orderitem->set([
			'OrderItem' => [
				'merchant_id' => $merchant['Merchant']['id'],
			],
			'Merchant' => [
				'id' => $merchant['Merchant']['id']
			]
		]);
		$validator = $this->Orderitem->validator();
		$this->assertSame(7, $validator->count());
		$this->assertNotEmpty($validator->getField('merchant_id'));
		$this->assertEmpty(Hash::get($this->Orderitem->data, 'Merchant.merchant_mid'));

		$this->Orderitem->beforeValidate();
		$validator = $this->Orderitem->validator();
		$this->assertSame(6, $validator->count(), 'No validation rule was removed');
		$this->assertEmpty($validator->getField('merchant_id'), 'Merchant_id validation was not removed');
		$this->assertEmpty(Hash::get($this->Orderitem->data, 'Merchant'), 'Merchant data was not removed');
		$this->assertEmpty(Hash::get($this->Orderitem->data, 'Orderitem.merchant_id'), 'Merchant id was not removed from Orderitem data');
	}

/**
 * test
 *
 * @covers Orderitem::beforeValidate()
 * @return void
 */
	public function testBeforeValidateCheckOrderitemType() {
		$findOptions = [
			'conditions' => [
				'Orderitem.id' => '760ae5da-a785-454c-a5be-613086cf8a9d',
			],
			'contain' => ['OrderitemsReplacement']
		];
		$data = $this->Orderitem->find('first', $findOptions);
		$this->assertNotNull(Hash::get($data, 'OrderitemsReplacement.id'), 'The orderitem does not have an associated Replacement');

		// Change order item type from 'replacement' to 'refurbished'
		$data['Orderitem']['orderitem_type_id'] = '0d16d8b7-e155-45d3-891e-90383f9d5a3f';
		$this->Orderitem->set($data);
		$this->Orderitem->beforeValidate();
		$this->assertEmpty(Hash::get($this->Orderitem->data, 'OrderitemsReplacement'));
		$data = $this->Orderitem->find('first', $findOptions);
		$this->assertNull(Hash::get($data, 'OrderitemsReplacement.id'), 'The OrderitemsReplacement was not removed');
	}
}
