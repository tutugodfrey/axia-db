<?php
App::uses('MerchantPricingArchive', 'Model');
App::uses('AxiaTestCase', 'Test');

class MerchantPricingArchiveTestCase extends AxiaTestCase {

/**
 * Start Test callback
 *
 * @param string $method no documentation available
 * @return void
 * @access public
 */
	public function startTest($method) {
		parent::startTest($method);
		$this->User = ClassRegistry::init('User');
		$this->MerchantPricing = ClassRegistry::init('MerchantPricing');
		$this->MerchantPricingArchive = ClassRegistry::init('MerchantPricingArchive');
		$this->UserCostsArchive = ClassRegistry::init('UserCostsArchive');
		$this->ProductsAndService = ClassRegistry::init('ProductsAndService');
		$this->ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$this->ProductSetting = ClassRegistry::init('ProductSetting');
		$this->PaymentFusion = ClassRegistry::init('PaymentFusion');
		$this->Ach = ClassRegistry::init('Ach');
		$this->activeProds = $this->MerchantPricingArchive->getArchivableProducts();
		$this->conditions = array(
				'month' => 01,
				'year' => 2016
			);
		$this->mId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';

		//Assign all active products to this merchant
		$this->__saveProdsForTst($this->mId);
	}

/**
 * testGetArchivableProducts
 *
 * @covers MerchantPricingArchive::getArchivableProducts()
 * @return void
 */
	public function testGetArchivableProducts() {
		$actual = $this->MerchantPricingArchive->getArchivableProducts();

		$expected = $this->ProductsServicesType->find('list', [
			'conditions' => [
				'products_services_description' => Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}"),
				'is_active' => true,
			],
		]);
		asort($expected);
		$this->assertSame($expected, $actual);
	}

/**
 * testCreateArchives
 *
 * Tests function that invokes all archive creation methods
 *
 * @covers MerchantPricingArchive::saveNewArchives()
 * @return void
 */
	public function testSaveNewArchives() {
		$mappedOutProducts = $this->MerchantPricingArchive->getArchivableProducts();
		$mappedOutProducts = array_keys($mappedOutProducts);
		$initialAmount = $this->MerchantPricingArchive->find('count', [
			'conditions' => [
				'merchant_id' => $this->mId
			]
		]);
		$this->MerchantPricingArchive->saveNewArchives($mappedOutProducts, 01, 2016, '');

		//The number archives saved should match must equal number of mapped out products sinde $this->mId is supposed to have all those products
		$actualCreated = $this->MerchantPricingArchive->find('count', [
			'conditions' => [
				'merchant_id' => $this->mId
			]
		]);

		$this->assertGreaterThan($initialAmount, $actualCreated);
	}

/**
 * testArchiveExists
 *
 * @covers MerchantPricingArchive::archiveExists()
 * @return void
 */
	public function testArchiveExists() {
		$productId = 'e8fa66a0-790f-4710-b7de-ef79be75a1c7';
		//arbitraty data
		$tstData = [
			'merchant_id' => $this->UUIDv4(),
			'user_id' => $this->UUIDv4(),
			'products_services_type_id' => $productId,
			'month' => 1,
			'year' => 2020,
		];
		$this->MerchantPricingArchive->create();
		$this->MerchantPricingArchive->save($tstData, array('validate' => false));
		$result = $this->MerchantPricingArchive->archiveExists('2020', '01', [$productId]);
		$this->assertTrue($result);
	}

/**
 * testSetMpArchiveDataForProductsWithNoCalculations
 *
 * @covers MerchantPricingArchive::setMpArchiveData()
 * @return void
 */
	public function testSetMpArchiveDataForProductsWithNoCalculations() {
		$pNames = [
			'Visa Sales',
			'Visa Dial Sales',
			'Visa Non Dial Sales',
			'MasterCard Sales',
			'MasterCard Dial Sales',
			'MasterCard Non Dial Sales',
			'Discover Sales',
			'Discover Dial Sales',
			'Discover Non Dial Sales',
			'American Express Sales',
			'American Express Dial Sales',
			'American Express Non Dial Sales',
			'Debit Sales',
			'Debit Discount',
			'EBT Sales',
			'EBT Discount',
			'Payment Fusion',
			'Corral License Fee',
			'ACH'
		];
		//Create test Ach product data with provider vericheck
		$achTstDat = ['ach_provider_id' => '55bbcdd0-ed3c-4745-918a-1f3634627ad4'];
		$this->__createTestAch($achTstDat);

		$dat = $this->__getData($pNames);

		//For this subset of products no calculations should have been done, check that original raw data matches final data
		//Final data should not have been modified at all
		foreach ($pNames as $pName) {
			if ($pName === 'ACH') {
				$this->assertSame($achTstDat['ach_provider_id'], Hash::get($dat, "Raw_$pName.NewArchiveData.provider_id"));
			} else {
				$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.provider_id"));
			}
			$pName = str_replace(' ', '_', $pName);

			$this->assertNotNull(Hash::get($dat, "Raw_$pName.NewArchiveData"));
			//Merchant Rate %
			$this->assertEquals(Hash::get($dat, "Raw_$pName.NewArchiveData.m_rate_pct"), Hash::get($dat, "$pName.MerchantPricingArchive.m_rate_pct"));
			//Merchant Per Item Fee
			$this->assertEquals(Hash::get($dat, "Raw_$pName.NewArchiveData.m_per_item_fee"), Hash::get($dat, "$pName.MerchantPricingArchive.m_per_item_fee"));
			//Merchant Statement Fee
			$this->assertEquals(Hash::get($dat, "Raw_$pName.NewArchiveData.m_statement_fee"), Hash::get($dat, "$pName.MerchantPricingArchive.m_statement_fee"));

			//No other values should archive
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.m_discount_item_fee"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.interchange_expense"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.product_profit"));

		}
	}

/**
 * testSetMpArchiveDataCalculationsForProducts
 *
 * @covers MerchantPricingArchive::setMpArchiveData()
 * @return void
 */
	public function testSetMpArchiveDataCalculationsForMonthlyProducts() {
		$mPricing = $this->MerchantPricing->find('first', ['conditions' => ['merchant_id' => $this->mId]]);
		$pNames = [
			'Credit Monthly',
			'Debit Monthly',
			'EBT Monthly',
		];

		//For this subset of products the only data being archived and calculated is the following
		foreach ($pNames as $pName) {
			if ($pName === 'Credit Monthly') {
				$expectedCalculation = $mPricing['MerchantPricing']['statement_fee'] + $mPricing['MerchantPricing']['wireless_access_fee'] + $mPricing['MerchantPricing']['gateway_access_fee'];
			} elseif ($pName === 'Debit Monthly') {
				$expectedCalculation = $mPricing['MerchantPricing']['debit_access_fee'];
			} else {
				$expectedCalculation = $mPricing['MerchantPricing']['ebt_access_fee'];
			}
			$dat = $this->__getData($pNames);

			$pName = str_replace(' ', '_', $pName);
			//Merchant Statement Fee is the same regardless of the product (it only differes when archiving UserCostsArchive data which will not be tested here)
			$this->assertEquals($expectedCalculation, Hash::get($dat, "$pName.MerchantPricingArchive.m_statement_fee"));
			//No other values should archive
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.m_rate_pct"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.m_per_item_fee"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.m_discount_item_fee"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.interchange_expense"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.product_profit"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.provider_id"));
		}
	}

/**
 * testSetMpArchiveDataCalculationsForDiscountProducts
 *
 * @covers MerchantPricingArchive::setMpArchiveData()
 * @return void
 */
	public function testSetMpArchiveDataCalculationsForDiscountProducts() {
		$mPricing = $this->MerchantPricing->find('first', ['conditions' => ['merchant_id' => $this->mId]]);
		//Note: Debit and EBT Discount do not require any calculations sare being tested in testSetMpArchiveDataForProductsWithNoCalculations()
		$pNames = [
			'American Express Sales',
			'Discover Sales',
			'MasterCard Sales',
			'Visa Sales'
		];

		$dat = $this->__getData($pNames);
		//For this subset of products the only data being archived and calculated is the following
		foreach ($pNames as $pName) {
			//We should always a value set for this
			$pName = str_replace(' ', '_', $pName);

			$this->assertNotNull(Hash::get($dat, "Raw_$pName.NewArchiveData.m_per_item_fee"));
			$expectedCalculation = Hash::get($dat, "Raw_$pName.NewArchiveData.dscnt_proc_rate") + Hash::get($dat, "Raw_$pName.NewArchiveData.bet_extra_pct");
			//Merchant Rate %
			$this->assertEquals($expectedCalculation, Hash::get($dat, "$pName.MerchantPricingArchive.m_rate_pct"));

			//Merchant Discount P/I should not change from original raw data
			$this->assertEquals(Hash::get($dat, "Raw_$pName.NewArchiveData.m_discount_item_fee"), Hash::get($dat, "$pName.MerchantPricingArchive.m_discount_item_fee"));

			//No other values should archive
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.m_rate_pct"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.m_statement_fee"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.interchange_expense"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.product_profit"));
			$this->assertNull( Hash::get($dat, "$pName.MerchantPricingArchive.provider_id"));
		}
	}

/**
 * __getData
 *
 * @param array $pNames product names
 * @return array
 */
	private function __getData($pNames) {
		$pNamesIds = $this->ProductsServicesType->find('list', [
			'conditions' => ['products_services_description' => $pNames],
			'fields' => ['products_services_description', 'id']
		]);

		$dat = array();
		foreach ($pNamesIds as $name => $id) {
			$name = str_replace(' ', '_', $name);
			//Raw data is data brefore calculations staight from the database/fixture
			$dat["Raw_$name"] = $this->MerchantPricingArchive->getPricingArchiveData($this->mId, $id, 'fakeEmail@nowhere.com', 'PricingArchiveFieldMap');
			$dat[$name] = $this->MerchantPricingArchive->setMpArchiveData($this->mId, $id, 01, 2020, 'fakeEmail@nowhere.com');
		}
		return $dat;
	}

/**
 * testCheckExistingArchiveByMonthYear
 *
 * @covers MerchantPricingArchive::checkExistingArchiveByMonthYear()
 * @return void
 */
	public function testCheckExistingArchiveByMonthYear() {
		$productId = 'e8fa66a0-790f-4710-b7de-ef79be75a1c7';
		//arbitraty data
		$tstData = [
			'merchant_id' => $this->UUIDv4(),
			'user_id' => $this->UUIDv4(),
			'products_services_type_id' => $productId,
			'month' => 1,
			'year' => 2020,
		];
		$this->MerchantPricingArchive->create();
		$this->MerchantPricingArchive->save($tstData, array('validate' => false));
		$expected = array(
						array(
							array(
								'month' => 1
							),
							'ProductsServicesType' => array(
									'products_services_description' => 'ACH',
									'id' => $productId
							)
						)
				);

		$actual = $this->MerchantPricingArchive->checkExistingArchiveByMonthYear('2020', '01');
		$this->assertEquals($expected, $actual);
	}

/**
 * testGetArchiveFieldMapExceptionThrown
 * 
 * @covers MerchantPricingArchive::getArchiveFieldMap()
 * @return void
 */
	public function testGetArchiveFieldMapExceptionThrown() {
		try
		{
				$this->MerchantPricingArchive->getArchiveFieldMap('Product Name Wothout Map', 'PricingArchiveFieldMap');//if this method not throw exception it must be fail too.
				$this->fail("Expected exception");
		}
		catch(Exception $e) {
				$this->assertNotEmpty($e->getMessage());
				$this->assertContains("Product Name Wothout Map", $e->getMessage());
		}
	}

/**
 * testGetArchiveFieldMapExceptionThrown
 *
 * @covers MerchantPricingArchive::getArchiveFieldMap()
 * @return void
 */
	public function testGetArchiveFieldMapFieldMaoDoesNotExistExceptionThrown() {
		try
		{
				$this->MerchantPricingArchive->getArchiveFieldMap('Visa Sales', 'Non-existing Field Map Name');//if this method not throw exception it must be fail too.
				$this->fail("Expected exception");
		}
		catch(Exception $e) {
				$this->assertNotEmpty($e->getMessage());
				$this->assertContains("Non-existing Field Map Name", $e->getMessage());
		}
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$this->MerchantPricingArchive->deleteAll($this->conditions, false);
		unset($this->MerchantPricingArchive);
		unset($this->MerchantPricing);
		unset($this->activeProds);
		unset($this->mId);
		unset($this->conditions);
		unset($this->ProductsAndService);
		ClassRegistry::flush();
	}

/**
 *	__saveProdsForTst metod
 *
 *	@param string $mId a merchant id
 *	@return void
 */
	private function __saveProdsForTst($mId) {
		foreach ($this->activeProds as $pId => $val) {
				$tstData[] = [
						'merchant_id' => $mId,
						'products_services_type_id' => $pId,
				];
		}

		$this->ProductsAndService->saveMany($tstData);
		$tstData = [
			[
				'merchant_id' => $mId,
				'products_services_type_id' => '5806a480-cdd8-4199-8d6f-319b34627ad4', // corral
				'rate' => .10,
				'monthly_fee' => 10,
				'per_item_fee' => .25,
				'gral_fee' => 10,
				'gral_fee_multiplier' => 3,
			],
		];
		$this->ProductSetting->saveMany($tstData);

		$tstData = [
			'merchant_id' => $mId,
			'monthly_total' => 25,
			'per_item_fee' => 1.5,
			'generic_product_mid' => 123456
		];
		$this->PaymentFusion->save($tstData);
	}

/**
 * __createTestAch
 * Create test data for the ACH product for users
 *
 * @return void
 */
	private function __createTestAch($data = []) {
		//Create test Ach product  data
		$tstData = [
				'id' => '3187bdf4-5dcd-43d6-b3e2-8cdbe2ca0430', //existing record
				'merchant_id' => $this->mId,
				'ach_statement_fee' => 99
		];
		if (!empty($data)) {
			$tstData = array_merge($tstData, $data);
		}
		$this->Ach->save($tstData);
	}

/**
 * __mapExists metod
 * returns true of false depending on wether a field map has been defined in Configure::read("ArchiveFieldMap") for the given product name
 *
 *	@param string $productName a product name
 *	@return bool
 */
	private function __mapExists($productName) {
		return in_array($productName, Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}"));
	}

/**
 * testSetEditModeValidation()
 *
 * @covers MerchantPricingArchive::setEditModeValidation()
 * @return void
 */
	public function testSetEditModeValidation() {
		$this->MerchantPricingArchive->setEditModeValidation();
		$actual = $this->MerchantPricingArchive->validator()->getField('m_per_item_fee')->getRule('validAmount')->rule;
		$this->assertEquals('validAmount', $actual);
		$actual = $this->MerchantPricingArchive->validator()->getField('m_statement_fee')->getRule('validAmount')->rule;
		$this->assertEquals('validAmount', $actual);
		$actual = $this->MerchantPricingArchive->validator()->getField('m_discount_item_fee')->getRule('validAmount')->rule;
		$this->assertEquals('validAmount', $actual);
	}

/**
 * testSetFormData()
 * 
 * @covers MerchantPricingArchive::setFormData()
 * @return void
 */
	public function testSetFormData() {
		$id = $this->UUIDv4();
		$repId = '32165df7-6b97-4f86-9e6f-8638eb30cd9e';
		$partnerId = '00000000-0000-0000-0000-000000000005';
		$mId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
		$pId = $this->UUIDv4(); //fictitious product id
		$data = [
			'MerchantPricingArchive' => [
				'id' => $id,
				'merchant_id' => $mId,
				'user_id' => $repId,
				'products_services_type_id' => $pId,
				'month' => 1,
				'year' => 2020
			],
			'UserCostsArchive' => [
				[
					'merchant_id' => $mId,
					'user_id' => $repId
				],
				[
					'merchant_id' => $mId,
					'user_id' => $partnerId //partner user
				]
			],
		];
		$this->MerchantPricingArchive->setEditModeValidation();
		$this->MerchantPricingArchive->saveAssociated($data, ['validate' => false]);
		$data = $this->MerchantPricingArchive->getDataById($id);

		$actual = $this->MerchantPricingArchive->setFormData($data);
		$ucaActual = Hash::extract($actual, 'UserCostsArchive');

		//Check data contains all associated users and it is sorted hierarchically
		//Not all actual users will be associated but there should still be initial default data set for every possible user
		//The rep
		$this->assertEquals($repId, Hash::get($ucaActual, '0.user_id'));

		//Then SM (there is no SM assigned but there should be initial data for it)
		$this->assertEquals('SM', Hash::get($ucaActual, '1.data_for_role'));
		$this->assertEquals($mId, Hash::get($ucaActual, '1.merchant_id'));

		//Then SM2 (there is no SM2 assigned but there should be initial data for it)
		$this->assertEquals('SM2', Hash::get($ucaActual, '2.data_for_role'));
		$this->assertEquals($mId, Hash::get($ucaActual, '2.merchant_id'));

		//Then Partner: Partner is assigned above and should be at this index position
		$this->assertEquals('Partner', Hash::get($ucaActual, '3.data_for_role'));
		$this->assertEquals($mId, Hash::get($ucaActual, '3.merchant_id'));

		//Then Referrer (there is no Referrer assigned but there should be initial data for it)
		$this->assertEquals('Referrer', Hash::get($ucaActual, '4.data_for_role'));
		$this->assertEquals($mId, Hash::get($ucaActual, '4.merchant_id'));

		//Then Reseller (there is no Reseller assigned but there should be initial data for it)
		$this->assertEquals('Reseller', Hash::get($ucaActual, '5.data_for_role'));
		$this->assertEquals($mId, Hash::get($ucaActual, '5.merchant_id'));

		//Check that data is structured properly when the manager assigned to the merchant has more than one role
		$id = $this->UUIDv4();
		$managerId = '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6';
		$repId = '32165df7-6b97-4f86-9e6f-8638eb30cd9e';
		$mId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
		$pId = $this->UUIDv4(); //fictitious product id
		$data = [
			'MerchantPricingArchive' => [
				'id' => $id,
				'merchant_id' => $mId,
				'user_id' => $repId,
				'products_services_type_id' => $pId,
				'month' => 2,
				'year' => 2020
			],
			'UserCostsArchive' => [
				[
					'merchant_id' => $mId,
					'user_id' => $repId
				],
				[
					'merchant_id' => $mId,
					'user_id' => $managerId //manager user
				]
			],
		];
		//Assign manager to merchant
		$this->MerchantPricingArchive->Merchant->id = $mId;
		$this->MerchantPricingArchive->Merchant->saveField('sm_user_id', $managerId);
		$this->MerchantPricingArchive->setEditModeValidation();
		$this->MerchantPricingArchive->saveAssociated($data, ['validate' => false]);
		$data = $this->MerchantPricingArchive->getDataById($id);
		$actual = $this->MerchantPricingArchive->setFormData($data);
		$ucaActual = Hash::extract($actual, 'UserCostsArchive');

		//The SM should be at the second array position right after the rep
		$this->assertEquals($managerId, Hash::get($ucaActual, '1.user_id'));
		$this->assertEquals('SM', Hash::get($ucaActual, '1.data_for_role'));
		$this->assertEquals($mId, Hash::get($ucaActual, '1.merchant_id'));
	}

/**
 * testFilterData()
 * 
 * @return void
 */
	public function testFilterData() {
		$originalData = array(
			'UserCostsArchive' => array(
					array(
						'merchant_pricing_archive_id' => '64f301fd-08b7-4c2c-812a-f227d9e5ee7b',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					),
					array(
						'data_for_role' => 'SM',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'
					),
					array(
						'data_for_role' => 'SM2',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'
					),
					array(
						'data_for_role' => 'Partner',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'
					),
					array(
						'data_for_role' => 'Referrer',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'
					),
					array(
						'data_for_role' => 'Reseller',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'
					)
				)
			);
		$expected = array(
			'UserCostsArchive' => array(
					array(
						'merchant_pricing_archive_id' => '64f301fd-08b7-4c2c-812a-f227d9e5ee7b',
						'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					)
				)
			);
		$requestData = $originalData;
		$this->MerchantPricingArchive->filterData($requestData);
		$this->assertEquals($expected, $requestData);
		unset($requestData);

		$originalData['UserCostsArchive'][3] = $originalData['UserCostsArchive'][0];
		$expected['UserCostsArchive'][3] = $originalData['UserCostsArchive'][0];
		$requestData = $originalData;
		$this->MerchantPricingArchive->filterData($requestData);
		$this->assertEquals($expected, $requestData);
	}

/**
 * testDeleteManyInvalidArgumentException()
 * 
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Missing or invalid entry in conditions array parameter
 * @return void
 */
	public function testDeleteManyInvalidArgumentExceptionA() {
		$conditions = [
			'year' => 2020,
			'months_products' => 'this should be array',
		];
		$this->MerchantPricingArchive->deleteMany($conditions);
	}
/**
 * testDeleteManyInvalidArgumentExceptionB()
 * 
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Missing or invalid entry in conditions array parameter
 * @return void
 */
	public function testDeleteManyInvalidArgumentExceptionB() {
		$conditions = [
			'months' => [1],
			'months_products' => null,
		];
		$this->MerchantPricingArchive->deleteMany($conditions);
	}
/**
 * testDeleteMany()
 * 
 * @covers MerchantPricingArchive::deleteMany()
 * @return void
 */
	public function testDeleteMany() {
		$this->MerchantPricingArchive->setEditModeValidation();
		//Generate 10 MerchantPricingArchive records and 30 associated records
		for ($n = 1; $n <= 10; $n++) {
			$mId = $this->UUIDv4(); //fictitious merchant id
			$pId = $this->UUIDv4(); //fictitious product id
			$productIds[] = $pId;
			$id = $this->UUIDv4();
			$idsCollection[] = $id;
			$data = [
				'MerchantPricingArchive' => [
					'id' => $id,
					'merchant_id' => $mId,
					'user_id' => $this->UUIDv4(),
					'products_services_type_id' => $pId,
					'month' => 1,
					'year' => 2020
				],
				'UserCostsArchive' => [
					[
						'merchant_id' => $mId,
						'user_id' => $this->UUIDv4()
					],
					[
						'merchant_id' => $mId,
						'user_id' => $this->UUIDv4()
					],
					[
						'merchant_id' => $mId,
						'user_id' => $this->UUIDv4()
					],
				],
			];
			//save test data
			$this->MerchantPricingArchive->saveAssociated($data, ['validate' => false]);
		}

		$conditions = [
			'year' => 2020,
			'months_products' => [
				1 => $productIds
			],
		];

		$this->assertTrue($this->MerchantPricingArchive->hasAny(['id' => $idsCollection]));
		$this->assertSame(30, $this->MerchantPricingArchive->UserCostsArchive->find('count', ['conditions' => ['merchant_pricing_archive_id' => $idsCollection]]));
		$this->assertTrue($this->MerchantPricingArchive->deleteMany($conditions));
		$this->assertSame(0, $this->MerchantPricingArchive->UserCostsArchive->find('count', ['conditions' => ['merchant_pricing_archive_id' => $idsCollection]]));
	}

/**
 * testGetCreateManyViewData()
 * 
 * @covers MerchantPricingArchive::getCreateManyViewData()
 * @return void
 */
	public function testGetCreateManyViewData() {
		//Emulate request data submitted by client
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$requestData = [
			'MerchantPricingArchive' => [
				'merchant_id' => $merchantId, //existing merchant
				'archive_month' => 12,
				'archive_year' => 2018
			]
		];

		$result = $this->MerchantPricingArchive->getCreateManyViewData($requestData);
		//Merchant has Visa Sales as an archivable product
		$this->assertSame('Visa Sales', $result[0]['ProductsServicesType']['products_services_description']);
		//Merchant does not have any existing archive for the specified date for Visa Sales
		$this->assertEmpty($result[0]['MerchantPricingArchive']['id']);
		//For this case creation of new archives is allowed
		$this->assertTrue($result['allow_create']);

		//Emulate request data for a merchant that does have existing pricing archives
		$requestData = [
			'MerchantPricingArchive' => [
				'merchant_id' => '00000000-0000-0000-0000-000000000003', //existing merchant
				'archive_month' => 3,
				'archive_year' => 2015
			]
		];
		$this->MerchantPricingArchive->Merchant->ProductsAndService->save([
			'merchant_id' => '00000000-0000-0000-0000-000000000003',
			'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7'
		]);
		$result = $this->MerchantPricingArchive->getCreateManyViewData($requestData);
		//Merchant has Visa Sales as an archivable product
		$this->assertSame('ACH', $result[0]['ProductsServicesType']['products_services_description']);
		//Merchant does have existing archive for the specified date for ACH
		$this->assertNotEmpty($result[0]['MerchantPricingArchive']['id']);
		//For this case creation of new archives is allowed
		$this->assertTrue($result['allow_create']);

		//Emulate request data with dates in the future
		$requestData['MerchantPricingArchive']['archive_year'] = date('Y') + 1;
		$requestData['MerchantPricingArchive']['archive_month'] = (date('M') == 12)? date('m') : date('m') + 1;

		$result = $this->MerchantPricingArchive->getCreateManyViewData($requestData);
		//For this case creation of new archives is NOT allowed because date is in the future
		$this->assertFalse($result['allow_create']);
	}

/**
 * testCreateByMerchant()
 * 
 * @covers MerchantPricingArchive::createByMerchant()
 * @return void
 */
	public function testCreateByMerchant() {
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$productIds = ['e6d8f040-9963-4539-ab75-3e19f679de16'];
		$month = 1;
		$year = 2018;

		$result = $this->MerchantPricingArchive->find('first', ['conditions' => ['merchant_id' => $merchantId, 'products_services_type_id' => $productIds, 'year' => 2018, 'month' => 1]]);
		$this->assertEmpty($result);
		//create
		$this->assertTrue($this->MerchantPricingArchive->createByMerchant($merchantId, $productIds, $month, $year));
		$result = $this->MerchantPricingArchive->find('first', ['conditions' => ['merchant_id' => $merchantId, 'products_services_type_id' => $productIds, 'year' => 2018, 'month' => 1]]);
		//verify new record was created
		$this->assertNotEmpty($result);
		$this->assertSame($merchantId, $result['MerchantPricingArchive']['merchant_id']);
		$this->assertSame($productIds[0], $result['MerchantPricingArchive']['products_services_type_id']);
		$this->assertSame($year, $result['MerchantPricingArchive']['year']);
		$this->assertSame($month, $result['MerchantPricingArchive']['month']);
		$originalId = $result['MerchantPricingArchive']['id'];

		//overwrite or rebuild archive created above
		$this->assertTrue($this->MerchantPricingArchive->createByMerchant($merchantId, $productIds, $month, $year));
		$result = $this->MerchantPricingArchive->find('all', ['conditions' => ['merchant_id' => $merchantId, 'products_services_type_id' => $productIds, 'year' => 2018, 'month' => 1]]);

		//verify original record was deleted and there is no duplicate
		$this->assertEmpty($this->MerchantPricingArchive->find('first', ['conditions' => ['id' => $originalId]]));
		$this->assertCount(1, $result);
		$result = $result[0];
		$this->assertNotEqual($originalId , $result['MerchantPricingArchive']['id']);
		$this->assertSame($merchantId, $result['MerchantPricingArchive']['merchant_id']);
		$this->assertSame($productIds[0], $result['MerchantPricingArchive']['products_services_type_id']);
		$this->assertSame($year, $result['MerchantPricingArchive']['year']);
		$this->assertSame($month, $result['MerchantPricingArchive']['month']);
	}
}
