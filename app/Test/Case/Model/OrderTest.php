<?php
App::uses('Order', 'Model');
App::uses('AxiaTestCase', 'Test');

class OrderTestCase extends AxiaTestCase {

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
		$this->Order = ClassRegistry::init('Order');
		$this->Merchant = ClassRegistry::init('Merchant');
		$this->_mockSystemTransactionListener($this->Order);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Order);
		ClassRegistry::flush();

		parent::tearDown();
	}

/**
 * testConstructorAndFilterArgs
 *
 * @return void
 */
	public function testConstructorAndFilterArgs() {
		$Order = $this->_mockModel('Order');

		$expected = [
			'equipment_item_id' => [
				'type' => 'value',
				'field' => 'Orderitem.equipment_item_id'
			],
			'dba_mid' => [
				'type' => 'query',
				'method' => 'merchantOrCndtn',
				'field' => 'Merchant.id'
			],
			'orderitem_type_id' => [
				'type' => 'value',
				'field' => 'Orderitem.orderitem_type_id'
			],
			'hardware_sn' => [
				'type' => 'value',
				'field' => 'Orderitem.hardware_sn'
			],
			'hardware_replacement_for' => [
				'type' => 'value',
				'field' => 'Orderitem.hardware_replacement_for'
			],
			'commission_month' => [
				'type' => 'query',
				'field' => 'Order.commission_month',
				'method' => 'commMonthCondition'
			],
			'invoice_number' => [
				'type' => 'value'
			],
			'status' => [
				'type' => 'query',
				'method' => 'orderStatusOrCndtn'
			],
			'date_ordered_b_month' => [
				'type' => 'query',
				'method' => 'orderDateRangeCondition'
			],
			'date_ordered_b_year' => [
				'type' => 'query',
				'method' => 'orderDateRangeCondition'
			],
			'date_ordered_e_month' => [
				'type' => 'query',
				'method' => 'orderDateRangeCondition'
			],
			'date_ordered_e_year' => [
				'type' => 'query',
				'method' => 'orderDateRangeCondition'
			]
		];
		$this->assertEquals($expected, $Order->filterArgs);
	}

/**
 * OrderTest::testMerchEqmntPaginatorSetting
 *
 * @return void
 */
	public function testMerchEqmntPaginatorSetting() {
		$merchantId = '47cf6834-1ac0-4c50-b4c8-d851335897ab';
		$expected = [
			'limit' => 9999,
			'conditions' => [
				'Orderitem.merchant_id' => $merchantId,
				'Order.status !=' => 'DEL'
			],
			'contain' => [
				'Order' => [
					'Vendor',
					'ShippingType.shipping_type_description'
				],
				'OrderitemType.orderitem_type_description'
			]
		];

		$actual = $this->Order->merchEqmntPaginatorSettings($merchantId);
		$this->assertEquals($actual, $expected);
	}

/**
 * OrderTest::testGroupOrderItemsByOrder
 *
 * @return void
 */
	public function testGroupOrderItemsByOrder() {
		$merchantId = '47cf6834-1ac0-4c50-b4c8-d851335897ab';
		$settings = $this->Order->merchEqmntPaginatorSettings($merchantId);
		$data = $this->Order->Orderitem->find('all', $settings);

		$actual = $this->Order->groupOrderItemsByOrder($data);

		//Orderitem should not be at the same dimension as Order
		$this->assertFalse(Hash::check($actual, '0.Orderitem'));

		//All Orderitems should now be grouped within its corresponding Order
		$this->assertTrue(count($actual[0]['Order']['Orderitem']) === 4);
		$this->assertTrue(array_key_exists('Orderitem', $actual[0]['Order']));
		$this->assertTrue(Hash::check($actual, '0.Order.Orderitem'));
	}

/**
 * test
 *
 * @covers Order::groupOrderItemsByOrder()
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage groupOrderItemsByOrder function expects an indexed array containing Orders and Orderitems data on its second dimention.
 * @return void
 */
	public function testGroupOrderItemsByOrderInvalidArgumentException() {
		$this->Order->groupOrderItemsByOrder([
			[
				'Order' => [
					'id' => 'random-id',
				]
			]
		]);
	}

/**
 * OrderTest::testGetOrderByOrderId
 *
 * @return void
 */
	public function testGetOrderByOrderId() {
		$id = '07afa03c-7a54-4732-8d21-2aab3abddc1b';
		$expected = [
			'Order' => [
				'id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
				'order_id_old' => null,
				'status' => 'PAID',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'user_id_old' => null,
				'date_ordered' => '2004-03-08',
				'date_paid' => '2004-08-24',
				'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
				'merchant_id_old' => null,
				'shipping_cost' => '25.0700',
				'tax' => '16.7600',
				'ship_to' => 'Matt Sakauye',
				'tracking_number' => null,
				'notes' => null,
				'invoice_number' => 683097,
				'shipping_type_id' => '00000000-0000-0000-0000-000000000001',
				'shipping_type_old' => null,
				'display_id' => '00000000-0000-0000-0000-000000000001',
				'merchant_ach_id' => '83e96265-539b-41a1-a16d-cc08a64082e2',
				'ach_seq_number_old' => null,
				'commission_month' => null,
				'commission_year' => null,
				'vendor_id' => '00000000-0000-0000-0000-000000000001',
				'vendor_id_old' => null,
				'add_item_tax' => 0,
				'commission_month_nocharge' => 0,
				'total_true_cost' => '425.58',
				'total_rep_cost' => '455.83',
				'depricated_commission_month' => null
			],
			'User' => [
				'id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
			],
			'Vendor' => [
				'id' => '00000000-0000-0000-0000-000000000001',
				'vendor_id_old' => null,
				'vendor_description' => 'vendor-1',
				'rank' => 1
			],
			'ShippingType' => [
				'id' => '00000000-0000-0000-0000-000000000001',
				'shipping_type_old' => 1,
				'shipping_type_description' => 'shipping-type-1'
			]
		];
		$actual = $this->Order->getOrderByOrderId($id);

		$this->assertEquals($expected, $actual);
	}

/**
 * OrderTest::testOrderitemsPaginatorSetting
 *
 * @return void
 */
	public function testOrderitemsPaginatorSetting() {
		$orderId = '07afa03c-7a54-4732-8d21-2aab3abddc1b';
		$expected = [
			'limit' => 9999,
			'conditions' => [
				'Orderitem.order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b'
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
		$actual = $this->Order->orderitemsPaginatorSettings($orderId);
		$this->assertSame($actual, $expected);
	}

/**
 * OrderTest::testOrderStatusOrCndtnWithSingleStatus
 *
 * @return void
 */
	public function testOrderStatusOrCndtnWithSingleStatus() {
		$expectedCondition = ['Order.status =' => 'PAID'];
		$actual = $this->Order->orderStatusOrCndtn(["status" => "PAID"]);
		$this->assertSame($actual, $expectedCondition);
	}

/**
 * OrderTest::testOrderStatusOrCndtnWithDoubleStatus
 *
 * @return void
 */
	public function testOrderStatusOrCndtnWithDoubleStatus() {
		$expectedCondition = ['OR' => [
				['Order.status =' => 'PEND'],
				['Order.status =' => 'INV']
		]];
		$actual = $this->Order->orderStatusOrCndtn(["status" => "PEND,INV"]);
		$this->assertSame($actual, $expectedCondition);
	}

/**
 * OrderTest::testMerchantOrCndtn
 *
 * @return void
 */
	public function testMerchantOrCndtn() {
		$expectedCondition = ['OR' => [
				'Merchant.merchant_dba ILIKE' => '%Some Merchant DBA%',
				'Merchant.merchant_mid LIKE' => '%Some Merchant DBA%']];
		$actual = $this->Order->merchantOrCndtn(['dba_mid' => 'Some Merchant DBA']);
		$this->assertSame($actual, $expectedCondition);
	}

/**
 * OrderTest::testOrderDateRangeCondition
 *
 * @return void
 */
	public function testOrderDateRangeCondition() {
		$srchParams = ['date_ordered_b_month' => '08', 'date_ordered_b_year' => '2014',
			'date_ordered_e_month' => '01', 'date_ordered_e_year' => '2015'];

		$expectedDateRangeCondition = ['Order.date_ordered >=' => '2014-08-01',
			'Order.date_ordered <=' => '2015-01-31'];
		$actual = $this->Order->orderDateRangeCondition($srchParams);
		$this->assertSame($actual, $expectedDateRangeCondition);
	}

/**
 * OrderTest::testCleanupRequestDatum
 *
 * @return void
 */
	public function testCleanupRequestDatum() {
		$messyData = [
			'Save' => '',
			'Order' => [
				'id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
				'user_id' => '27082cdc-5f99-43ac-86d5-9636eaeb14c9',
				'vendor_id' => 'a0ab5e38-b91f-4478-899c-64d85c74f697',
				'invoice_number' => '683097',
				'display_id' => '00000000-0000-0000-0000-000000000001',
				'date_ordered' => [
					'month' => '03',
					'day' => '08',
					'year' => '2004'
				],
				'date_paid' => [
					'month' => '08',
					'day' => '24',
					'year' => '2004'
				],
				'commission_month' => '',
				'tax' => '16.7600',
				'shipping_cost' => '25.0700',
				'add_item_tax' => '1',
				'status' => 'PAID',
				'shipping_type_id' => 'c84b184a-31c7-4d22-8689-b8c67cdf45f4',
				'ship_to' => 'Matt Sakauye',
				'tracking_number' => '',
				'notes' => ''
			],
			'Orderitem' => [
				0 => [
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '13571745-28e4-4d1e-bb62-4a6d7546e841',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'warranty_id' => '26d1b51d-0f9c-4234-9da4-068e596c1765',
					'quantity' => '1',
					'hardware_sn' => '79U034144816',
					'hardware_replacement_for' => '',
					'equipment_item_true_price' => '248.7500',
					'equipment_item_rep_price' => '259.0000',
					'item_tax' => null,
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				1 => [
					'equipment_item_id' => '00000000-0000-0000-0000-000000000002',
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '760ae5da-a785-454c-a5be-613086cf8a9d',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'warranty_id' => '26d1b51d-0f9c-4234-9da4-068e596c1765',
					'quantity' => '1',
					'hardware_sn' => 'a012tyd',
					'hardware_replacement_for' => '',
					'equipment_item_true_price' => '20.0000',
					'equipment_item_rep_price' => '10.0000',
					'item_tax' => '0.0000',
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				4 => [
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'equipment_item_id' => '',
					'equipment_item_description' => '',
					'item_commission_month' => '',
					'warranty_id' => '',
					'quantity' => '',
					'hardware_sn' => '',
					'hardware_replacement_for' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				5 => [
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'equipment_item_id' => '',
					'equipment_item_description' => '',
					'item_commission_month' => '',
					'warranty_id' => '',
					'quantity' => '',
					'hardware_sn' => '',
					'hardware_replacement_for' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				6 => [
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'equipment_item_id' => '',
					'equipment_item_description' => '',
					'item_commission_month' => '',
					'warranty_id' => '',
					'quantity' => '',
					'hardware_sn' => '',
					'hardware_replacement_for' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				7 => [
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'equipment_item_id' => '',
					'equipment_item_description' => '',
					'item_commission_month' => '',
					'warranty_id' => '',
					'quantity' => '',
					'hardware_sn' => '',
					'hardware_replacement_for' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				2 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '26695264-22c6-4fc2-b2cc-62818d0971f7',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'equipment_item_description' => 'Tax',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'quantity' => '1',
					'equipment_item_true_price' => '16.7600',
					'equipment_item_rep_price' => '16.7600',
					'item_tax' => '0.0000',
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					]
				],
				3 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => 'e570c50e-b367-4f4d-9dc8-f0ec010e2cb6',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'equipment_item_description' => 'Shipping',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'quantity' => '1',
					'equipment_item_true_price' => '25.0700',
					'equipment_item_rep_price' => '25.0700',
					'item_tax' => '0.0000',
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					]
				],
				8 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '',
					'equipment_item_description' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '',
					'quantity' => '',
					'equipment_item_true_price' => '',
					'equipment_item_rep_price' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					]
				],
				9 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '',
					'equipment_item_description' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '',
					'quantity' => '',
					'equipment_item_true_price' => '',
					'equipment_item_rep_price' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					]
				],
				10 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '',
					'equipment_item_description' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '',
					'quantity' => '',
					'equipment_item_true_price' => '',
					'equipment_item_rep_price' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					]
				],
				11 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '',
					'equipment_item_description' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '',
					'quantity' => '',
					'equipment_item_true_price' => '',
					'equipment_item_rep_price' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					]
				],
				12 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '',
					'equipment_item_description' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '',
					'quantity' => '',
					'equipment_item_true_price' => '',
					'equipment_item_rep_price' => '',
					'item_tax' => '',
					'shipping_type_items_id' => '',
					'Merchant' => [
						'merchant_mid' => ''
					]
				]
			]
		];
		$expected = [
			'Save' => '',
			'Order' => [
				'id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
				'user_id' => '27082cdc-5f99-43ac-86d5-9636eaeb14c9',
				'vendor_id' => 'a0ab5e38-b91f-4478-899c-64d85c74f697',
				'invoice_number' => '683097',
				'display_id' => '00000000-0000-0000-0000-000000000001',
				'date_ordered' => [
					'month' => '03',
					'day' => '08',
					'year' => '2004'
				],
				'date_paid' => [
					'month' => '08',
					'day' => '24',
					'year' => '2004'
				],
				'commission_month' => '',
				'tax' => '16.7600',
				'shipping_cost' => '25.0700',
				'add_item_tax' => '1',
				'status' => 'PAID',
				'shipping_type_id' => 'c84b184a-31c7-4d22-8689-b8c67cdf45f4',
				'ship_to' => 'Matt Sakauye',
				'tracking_number' => '',
				'notes' => ''
			],
			'Orderitem' => [
				0 => [
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '13571745-28e4-4d1e-bb62-4a6d7546e841',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'warranty_id' => '26d1b51d-0f9c-4234-9da4-068e596c1765',
					'quantity' => '1',
					'hardware_sn' => '79U034144816',
					'hardware_replacement_for' => '',
					'equipment_item_true_price' => '248.7500',
					'equipment_item_rep_price' => '259.0000',
					'item_tax' => null,
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				1 => [
					'equipment_item_id' => '00000000-0000-0000-0000-000000000002',
					'equipment_type_id' => '1dd22698-efa4-43ee-80cc-5ca62e732708',
					'id' => '760ae5da-a785-454c-a5be-613086cf8a9d',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'warranty_id' => '26d1b51d-0f9c-4234-9da4-068e596c1765',
					'quantity' => '1',
					'hardware_sn' => 'a012tyd',
					'hardware_replacement_for' => '',
					'equipment_item_true_price' => '20.0000',
					'equipment_item_rep_price' => '10.0000',
					'item_tax' => '0.0000',
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					],
					'OrderitemsReplacement' => [
						'id' => '',
						'ax_to_merch_shipping_type_id' => '',
						'shipping_axia_to_merchant_cost' => '',
						'ra_num' => '',
						'merch_to_vend_shipping_type_id' => '',
						'shipping_merchant_to_vendor_cos' => '',
						'date_shipped_to_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'vend_to_ax_shipping_type_id' => '',
						'shipping_vendor_to_axia_cost' => '',
						'date_arrived_from_vendor' => [
							'month' => '',
							'day' => '',
							'year' => ''
						],
						'amount_billed_to_merchant' => '',
						'tracking_num' => ''
					]
				],
				2 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => '26695264-22c6-4fc2-b2cc-62818d0971f7',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'equipment_item_description' => 'Tax',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'quantity' => '1',
					'equipment_item_true_price' => '16.7600',
					'equipment_item_rep_price' => '16.7600',
					'item_tax' => '0.0000',
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					]
				],
				3 => [
					'equipment_type_id' => '4f15efcf-4df8-47c8-b7b3-e1268f5e7923',
					'id' => 'e570c50e-b367-4f4d-9dc8-f0ec010e2cb6',
					'order_id' => '07afa03c-7a54-4732-8d21-2aab3abddc1b',
					'equipment_item_description' => 'Shipping',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'quantity' => '1',
					'equipment_item_true_price' => '25.0700',
					'equipment_item_rep_price' => '25.0700',
					'item_tax' => '0.0000',
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '3948000031002259'
					]
				]
			]
		];

		$actual = $this->Order->cleanupRequestData($messyData);
		$this->assertSame($actual, $expected);
	}

/**
 * test
 *
 * @covers Order::setItemRepPartnerCost()
 * @return void
 */
	public function testsetItemRepPartnerCost() {
		$ucpId = '00000000-0000-0000-0000-000000000001';
		$userId = '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6';
		$this->Merchant->create();
		//save test data valiation not needed
		$this->Merchant->save(['user_id' => $userId, 'merchant_mid' => '1234567890'], ['validate' => false]);

		$tstData = [
			'Save' => '',
			'Order' => [
				'user_id' => '27082cdc-5f99-43ac-86d5-9636eaeb14c9',
				'vendor_id' => 'a0ab5e38-b91f-4478-899c-64d85c74f697',
				'invoice_number' => '683097',
				'display_id' => '',
				'date_ordered' => [
					'month' => '03',
					'day' => '08',
					'year' => '2004'
				],
				'date_paid' => [
					'month' => '08',
					'day' => '24',
					'year' => '2004'
				],
				'commission_month' => '',
				'tax' => null,
				'shipping_cost' => '25.0700',
				'add_item_tax' => '1',
				'status' => 'PAID',
				'shipping_type_id' => 'c84b184a-31c7-4d22-8689-b8c67cdf45f4',
				'ship_to' => 'Matt Sakauye',
				'tracking_number' => '',
				'notes' => ''
			],
			'Orderitem' => [
				0 => [
					'equipment_item_id' => '00000000-0000-0000-0000-000000000001',
					'equipment_type_id' => '',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'warranty_id' => '26d1b51d-0f9c-4234-9da4-068e596c1765',
					'quantity' => '1',
					'hardware_sn' => '79U034144816',
					'hardware_replacement_for' => '',
					'equipment_item_true_price' => null,
					'equipment_item_rep_price' => null,
					'item_tax' => null,
					'shipping_type_items_id' => '',
					'merchant_id' => '47cf6834-1ac0-4c50-b4c8-d851335897ab',
					'Merchant' => [
						'merchant_mid' => '1234567890'
					],
				]
			]
		];
		$result = $this->Order->setItemRepPartnerCost($tstData);

		$actual = $result['Orderitem'][0]['equipment_item_rep_price'];
		$expected = ClassRegistry::init('EquipmentCost')->field('rep_cost', ["user_compensation_profile_id" => $ucpId]);
		$this->assertEquals($expected, $actual);
	}

/**
 * test
 *
 * @covers Order::setItemRepPartnerCost()
 * @return void
 */
	public function testSetItemRepPartnerCostWithPartner() {
		$parterRepUcpId = '8bbe2d12-2975-4466-a309-fcf4e721d468';
		$PartnerUcpId = 'a8c5607c-cf2e-4b5b-a2f9-fda5a95a3950';
		$userId = '32165df7-6b97-4f86-9e6f-8638eb30cd9e';
		$partnerId = '19b2c1da-943e-4761-829a-4ca54008dd58';

		$this->Order->Orderitem->EquipmentItem->EquipmentCost->create();
		$this->Order->Orderitem->EquipmentItem->EquipmentCost->save([
				'equipment_item_id' => '00000000-0000-0000-0000-000000000001',
				'rep_cost' => 123,
				'user_compensation_profile_id' => $PartnerUcpId,
			]);
		$this->Order->Orderitem->EquipmentItem->EquipmentCost->create();
		$this->Order->Orderitem->EquipmentItem->EquipmentCost->save([
				'equipment_item_id' => '00000000-0000-0000-0000-000000000001',
				'rep_cost' => 1000,
				'user_compensation_profile_id' => $parterRepUcpId,
			]);
		$this->Merchant->create();
		//save test data valiation not needed
		$this->Merchant->save(['user_id' => $userId, 'partner_id' => $partnerId, 'merchant_mid' => '1234567890'], ['validate' => false]);

		$tstData = [
			'Save' => '',
			'Order' => [
				'user_id' => '27082cdc-5f99-43ac-86d5-9636eaeb14c9',
				'vendor_id' => 'a0ab5e38-b91f-4478-899c-64d85c74f697',
				'invoice_number' => '683097',
				'display_id' => '',
				'date_ordered' => [
					'month' => '03',
					'day' => '08',
					'year' => '2004'
				],
				'date_paid' => [
					'month' => '08',
					'day' => '24',
					'year' => '2004'
				],
				'commission_month' => '',
				'tax' => null,
				'shipping_cost' => '25.0700',
				'add_item_tax' => '1',
				'status' => 'PAID',
				'shipping_type_id' => 'c84b184a-31c7-4d22-8689-b8c67cdf45f4',
				'ship_to' => 'Matt Sakauye',
				'tracking_number' => '',
				'notes' => ''
			],
			'Orderitem' => [
				0 => [
					'equipment_item_id' => '00000000-0000-0000-0000-000000000001',
					'equipment_type_id' => '',
					'orderitem_type_id' => '',
					'item_date_ordered' => [
						'month' => '',
						'day' => '',
						'year' => ''
					],
					'item_commission_month' => '3:2004',
					'warranty_id' => '26d1b51d-0f9c-4234-9da4-068e596c1765',
					'quantity' => '1',
					'hardware_sn' => '79U034144816',
					'hardware_replacement_for' => '',
					'equipment_item_true_price' => null,
					'equipment_item_rep_price' => null,
					'equipment_item_partner_price' => null,
					'item_tax' => null,
					'shipping_type_items_id' => '',
					'merchant_id' => '',
					'Merchant' => [
						'merchant_mid' => '1234567890'
					],
				]
			]
		];
		$result = $this->Order->setItemRepPartnerCost($tstData);

		$actual = $result['Orderitem'][0]['equipment_item_rep_price'];
		$this->assertEquals(1000, $actual);
		$actual = $result['Orderitem'][0]['equipment_item_partner_price'];
		$this->assertEquals(123, $actual);
	}

/**
 * test
 *
 * @covers Order::indexPaginatorSettings()
 * @return void
 */
	public function testIndexPaginatorSettings() {
		$expected = [
			'order' => [
				'date_ordered' => 'desc'
			],
			'limit' => 100,
		];
		$result = $this->Order->indexPaginatorSettings();
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers Order::getFilterOptions()
 * @return void
 */
	public function testGetFilterOptions() {
		$expected = [
			'EquipmentItem' => [
				'00000000-0000-0000-0000-000000000001' => 'Equipment item 1',
				'00000000-0000-0000-0000-000000000002' => 'Equipment item 2',
				'00000000-0000-0000-0000-000000000003' => 'Equipment item 3',
				'00000000-0000-0000-0000-000000000004' => 'Equipment item 4'
			],
			'OrderStatuses' => [
				'PEND' => 'Pending',
				'INV' => 'Invoiced',
				'PAID' => 'Paid',
				'PEND,INV' => 'Pending & Invoiced'
			],
			'OrderitemType' => [
				'24614a81-bde0-49a5-a234-2a9dabdfcf66' => 'New Order',
				'4d165bc9-71bb-4ebc-98b5-d805589a7a74' => 'Replacement',
				'0d16d8b7-e155-45d3-891e-90383f9d5a3f' => 'Refurbished'
			]
		];
		$result = $this->Order->getFilterOptions();
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers Order::getOrderFormMenuData()
 * @return void
 */
	public function testGetOrderFormMenuData() {
		$expected = [
			'Vendor' => [
				'00000000-0000-0000-0000-000000000001' => 'vendor-1',
				'55cc6447-2418-4924-8f8b-13d834627ad4' => 'vendor-2',
				'55cc6447-2d14-442b-95c0-13d834627ad4' => 'vendor-3',
				'55cc6447-3548-4d42-b428-13d834627ad4' => 'vendor-4',
				'55cc6447-3d7c-41d6-9044-13d834627ad4' => 'vendor-5',
				'55cc6447-454c-4d24-8ca0-13d834627ad4' => 'vendor-6',
				'55cc6447-4d1c-47ff-8cca-13d834627ad4' => 'vendor-7',
				'55cc6447-5550-4eb0-bea4-13d834627ad4' => 'vendor-8',
				'55cc6447-5d20-439b-b075-13d834627ad4' => 'vendor-9',
				'55cc6447-6554-41b5-b0b5-13d834627ad4' => 'vendor-10',
			],
			'ShippingType' => [
				'00000000-0000-0000-0000-000000000001' => 'shipping-type-1',
				'00000000-0000-0000-0000-000000000002' => 'shipping-type-2',
				'00000000-0000-0000-0000-000000000003' => 'shipping-type-3'
			],
			'ShippingTypeItem' => [
				'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor sit amet'
			],
			'OrderitemType' => [
				'24614a81-bde0-49a5-a234-2a9dabdfcf66' => 'New Order',
				'4d165bc9-71bb-4ebc-98b5-d805589a7a74' => 'Replacement',
				'0d16d8b7-e155-45d3-891e-90383f9d5a3f' => 'Refurbished'
			],
			'EquipmentType' => [
				'Equipment type HW' => '00000000-0000-0000-0000-000000000001',
				'Equipment type 2' => '00000000-0000-0000-0000-000000000002',
				'Equipment type 3' => '00000000-0000-0000-0000-000000000003'
			],
			'EquipmentItem' => [
				'00000000-0000-0000-0000-000000000001' => 'Equipment item 1',
				'00000000-0000-0000-0000-000000000002' => 'Equipment item 2',
				'00000000-0000-0000-0000-000000000003' => 'Equipment item 3'
			],
			'Warranty' => [
				'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor sit amet'
			]
		];
		$result = $this->Order->getOrderFormMenuData();
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers Order::makeCsvString()
 * @return void
 */
	public function testMakeCsvString() {
		$data = [
			0 => [
				'Order' => [
					'id' => '27afa03c-7a54-4732-8d21-2aab3abddc1b',
					'order_id_old' => '787',
					'status' => 'PAID',
					'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'user_id_old' => null,
					'date_ordered' => '2015-12-25',
					'date_paid' => '2015-12-25',
					'merchant_id' => null,
					'merchant_id_old' => null,
					'shipping_cost' => '1.0000',
					'tax' => null,
					'ship_to' => null,
					'tracking_number' => null,
					'notes' => null,
					'invoice_number' => null,
					'shipping_type_id' => null,
					'shipping_type_old' => 3,
					'display_id' => null,
					'merchant_ach_id' => null,
					'ach_seq_number_old' => null,
					'commission_month' => null,
					'vendor_id' => '00000000-0000-0000-0000-000000000001',
					'vendor_id_old' => null,
					'add_item_tax' => 0,
					'commission_month_nocharge' => 0,
				],
				'Orderitem' => [
					0 => [
						'id' => 'g570c50e-b367-4f4d-9dc8-f0ec010e2cb6',
						'order_id' => '27afa03c-7a54-4732-8d21-2aab3abddc1b',
						'equipment_type_id' => '00000000-0000-0000-0000-000000000003',
						'equipment_item_description' => 'Test description 2',
						'quantity' => 1,
						'equipment_item_true_price' => null,
						'equipment_item_rep_price' => '100',
						'equipment_item_id' => null,
						'hardware_sn' => null,
						'hardware_replacement_for' => null,
						'item_tax' => '40',
						'item_ship_cost' => '16.88',
						'item_commission_month' => '12:2015',
						'item_date_ordered' => null,
						'orderitem_id' => null,
						'warranty' => null,
						'warranty_id' => null,
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'item_merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'item_ship_type' => null,
						'shipping_type_item_id' => null,
						'item_ach_seq' => null,
						'orderitem_type_id' => null,
						'type_id' => null,
						'Merchant' => [
								'id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
								'merchant_dba' => 'Square One Internet Solutions'
						],
						'EquipmentItem' => [
								'equipment_item_description' => null
						]
					]
				],
				'Vendor' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'vendor_id_old' => null,
					'vendor_description' => 'vendor-1',
					'rank' => 1
				]
			],
			1 => [
				'Order' => [
					'id' => '37afa03c-7a54-4732-8d21-2aab3abddc1b',
					'order_id_old' => '887',
					'status' => 'PAID',
					'user_id' => '43265df7-6b97-4f86-9e6f-8638eb30cd9e',
					'user_id_old' => null,
					'date_ordered' => '2015-12-25',
					'date_paid' => '2015-12-25',
					'merchant_id' => '5f4587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'merchant_id_old' => null,
					'shipping_cost' => '16.8800',
					'tax' => null,
					'ship_to' => null,
					'tracking_number' => null,
					'notes' => null,
					'invoice_number' => null,
					'shipping_type_id' => null,
					'shipping_type_old' => 3,
					'display_id' => null,
					'merchant_ach_id' => null,
					'ach_seq_number_old' => null,
					'commission_month' => null,
					'vendor_id' => '00000000-0000-0000-0000-000000000001',
					'vendor_id_old' => null,
					'add_item_tax' => 0,
					'commission_month_nocharge' => 0,
				],
				'Orderitem' => [
					0 => [
						'id' => 'h570c50e-b367-4f4d-9dc8-f0ec010e2cb6',
						'order_id' => '37afa03c-7a54-4732-8d21-2aab3abddc1b',
						'equipment_type_id' => '00000000-0000-0000-0000-000000000003',
						'equipment_item_description' => 'Test description 3',
						'quantity' => 1,
						'equipment_item_true_price' => null,
						'equipment_item_rep_price' => '250',
						'equipment_item_id' => '7d5e6da1-47bd-4f06-ace1-04be3a83f445',
						'hardware_sn' => null,
						'hardware_replacement_for' => null,
						'item_tax' => '40',
						'item_ship_cost' => '16.88',
						'item_commission_month' => '12:2015',
						'item_date_ordered' => null,
						'orderitem_id' => null,
						'warranty' => null,
						'warranty_id' => null,
						'merchant_id' => '5f4587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'item_merchant_id' => '5f4587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'item_ship_type' => null,
						'shipping_type_item_id' => null,
						'item_ach_seq' => null,
						'orderitem_type_id' => null,
						'type_id' => null,
						'Merchant' => [
								'id' => null,
								'merchant_dba' => null
						],
						'EquipmentItem' => [
							'equipment_item_description' => null
						]
					]
				],
				'Vendor' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'vendor_id_old' => null,
					'vendor_description' => 'vendor-1',
					'rank' => 1
				]
			]
		];
		$OrderModel = $this->getMockForModel('Order', [
			'_getCurrentUser'
		]);
		$OrderModel->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));

		$result = $OrderModel->makeCsvString($data);

		$expectedHeaders = 'Order Date,Item,Item Date,Merchant,Qty,New S/N,Broken S/N,Invoice #,Rep Cost,True Cost,Tax,Shipping,Warranty,Order Total,Vendor,Status';
		$this->assertContains($expectedHeaders, $result);
		$orderData1 = ',Test description 2,,Square One Internet Solutions,1,="",="",,,$1.00,Paid';
		$this->assertContains($orderData1, $result);
		$orderData2 = ',Test description 3,,,1,="",="",,,$16.88,Paid';
		$this->assertContains($orderData2, $result);
	}
}
