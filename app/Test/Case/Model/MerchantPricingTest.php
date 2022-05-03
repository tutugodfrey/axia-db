<?php
/* MerchantPricing Test cases generated on: 2017-01-03 11:01:49 : 1483471309*/
App::uses('MerchantPricing', 'Model');
App::uses('AxiaTestCase', 'Test');
class MerchantPricingTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantPricing = $this->getMockForModel('MerchantPricing', array(
			'_getCurrentUser'
		));
		$this->MerchantPricing->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->MerchantId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantPricing);
		ClassRegistry::flush();
		parent::tearDown();
	}

/**
 * testGetMerchantPricingByMerchantId test
 *
 * @covers MerchantPricing::getMerchantPricingByMerchantId()
 * @return void
 */
	public function testGetMerchantPricingByMerchantId() {
		$result = $this->MerchantPricing->getMerchantPricingByMerchantId($this->MerchantId);
		$expectedKeys = [
			'Merchant',
			'User',
			'Ach',
			'WebBasedAch',
			'CheckGuarantee',
			'MerchantPci',
			'MerchantPricing',
			'Gateway1',
			'MerchantUwVolume',
			'PaymentFusion',
			'ProductSetting',
			'GiftCard',
			'ProductsAndService',
			'MerchantCardType',
		];
		$this->assertNotEmpty($result);
		$this->assertNotEmpty($result);
		$this->assertSame($expectedKeys, array_keys($result));
	}

/**
 * testGetEditViewData test
 *
 * @covers MerchantPricing::getEditViewData()
 * @return void
 */
	public function testGetEditViewData() {
		$result = $this->MerchantPricing->getEditViewData('550d54d9-afa8-49f5-aa32-0d8c34627ad4');
		$this->assertNotEmpty(Hash::get($result, 'MerchantPricing.id'));
		$this->assertContains($this->MerchantId, Hash::get($result, 'MerchantPricing'));
	}

/**
 * testSetFormMenuData test
 *
 * @covers MerchantPricing::setFormMenuData()
 * @return void
 */
	public function testSetFormMenuData() {
		$this->MerchantChange = $this->getMockForModel('MerchantChange', array(
			'_getCurrentUser'
		));
		$this->MerchantChange->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$data = $this->MerchantPricing->getEditViewData('550d54d9-afa8-49f5-aa32-0d8c34627ad4');
		$data['MerchantNote'] = [];
		$result = $this->MerchantPricing->setFormMenuData($data);
		$expectedVars = [
			'merchant',
			'visaBetTables',
			'mcBetTables',
			'dsBetTables',
			'amexBetTables',
			'dbBetTables',
			'ebtBetTables',
			'debitAcquirers',
			'gateways',
			'isEditLog',
			'userCanApproveChanges',
		];
		$this->assertSame($expectedVars, array_keys($result));
		$this->assertFalse($result['isEditLog']);
		$this->assertTrue($result['userCanApproveChanges']);
	}

/**
 * testGetRateStructuresList test
 *
 * @covers MerchantPricing::getRateStructuresList()
 * @return void
 */
	public function testGetRateStructuresList() {
		//Pick any arbitrary existing merchant pricing as test subject
		$merchPricingData = $this->MerchantPricing->find('first');
		$rateStructureId = '57dc5551-1234-4567-acd1-6c4434627ad4'; //existing ID
		$visaBetTableId = '5555555b-4444-3333-960d-20ba34627ad4'; // bet table assigned to existing rate structure id
		$amexBetTableId = '558ade3b-9990-4f05-bfed-20ba34627ad4'; // bet table assigned to existing rate structure id
		$merchPricingData['MerchantPricing']['visa_bet_table_id'] = $visaBetTableId;
		$merchPricingData['MerchantPricing']['amex_bet_table_id'] = $amexBetTableId;

		$this->MerchantPricing->save($merchPricingData['MerchantPricing'], ['validate' => false]);
		$actual = $this->MerchantPricing->getRateStructuresList($merchPricingData['MerchantPricing']['merchant_id']);

		$expectedVMcDs = "Visa/MasterCard/Discover Interchange at Pass Thru";
		$expectedAmex = "American Express Cost Plus .05%";
		$this->assertContains($expectedVMcDs, $actual);
		$this->assertContains($expectedAmex, $actual);
	}

/**
 * testGetViewData test
 *
 * @covers MerchantPricing::getViewData()
 * @return void
 */
	public function testGetViewData() {
		$result = $this->MerchantPricing->getViewData($this->MerchantId);
		$expectedVars = [
			'enabledProds',
			'merchant',
			'archivedProds',
			'disabledProducts',
			'profitProjections',
		];
		$this->assertSame($expectedVars, array_keys($result));
		$expectedKeys = [
			'Merchant',
			'User',
			'Ach',
			'WebBasedAch',
			'CheckGuarantee',
			'MerchantPci',
			'MerchantPricing',
			'Gateway1',
			'MerchantUwVolume',
			'PaymentFusion',
			'ProductSetting',
			'GiftCard',
			'ProductsAndService',
			'MerchantCardType',
			'PaymentFusionFeatures'
		];
		$this->assertSame($expectedKeys, array_keys($result['merchant']));
	}

/**
 * testSetDynamicValidation test
 *
 * @covers MerchantPricing::setDynamicValidation()
 * @return void
 */
	public function testSetDynamicValidation() {
		$merchantId = '5f4587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$MerchantPricing = ClassRegistry::init('MerchantPricing');
		$ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$ProductsAndService = ClassRegistry::init('ProductsAndService');

		$testData = $MerchantPricing->find('first', ['conditions' => ['merchant_id' => $merchantId]]);
		$check  = $testData;

		//Save a Visa product
		$ProductsAndService->create();
		$ProductsAndService->saveMany([
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'Visa%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'MasterCard%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'Discover%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'American Express%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'Debit%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'EBT %'"])
			],
		]);

		$check['MerchantPricing']['visa_bet_table_id'] = null;
		$check['MerchantPricing']['processing_rate'] = null;
		$check['MerchantPricing']['mc_vi_auth'] = null;
		$check['MerchantPricing']['amex_auth_fee'] = null;
		$check['MerchantPricing']['mc_bet_table_id'] = null;
		$MerchantPricing->setDynamicValidation(['MerchantPricing' => ['merchant_id' => $merchantId]]);
		$this->MerchantPricing->save($check);
		$this->assertContains('BET is required! This merchant has a Visa product', $this->MerchantPricing->validationErrors['visa_bet_table_id']);
		$this->assertContains('BET is required! This merchant has a MasterCard product', $this->MerchantPricing->validationErrors['mc_bet_table_id']);
		$this->assertContains('BET is required! This merchant has Discover products', $this->MerchantPricing->validationErrors['ds_bet_table_id']);
		$this->assertContains('BET is required! This merchant has American Express products', $this->MerchantPricing->validationErrors['amex_bet_table_id']);
		$this->assertContains('BET is required! This merchant has Debit products', $this->MerchantPricing->validationErrors['db_bet_table_id']);
		$this->assertContains('BET is required! This merchant has EBT products', $this->MerchantPricing->validationErrors['ebt_bet_table_id']);
	}

/**
 * testIsRequiredBetOrProduct
 *
 * @covers MerchantPricing::isRequiredBetOrProduct()
 * @return void
 */
	public function testIsRequiredBetOrProduct() {
		$merchantId = '5f4587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$MerchantPricing = ClassRegistry::init('MerchantPricing');
		$ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$check = [
			'Visa' => ['visa_bet_table_id' => CakeText::uuid()],
			'MasterCard' => ['mc_bet_table_id' => CakeText::uuid()],
			'Discover' => ['ds_bet_table_id' => CakeText::uuid()],
			'American Express' => ['amex_bet_table_id' => CakeText::uuid()],
			'Debit' => ['db_bet_table_id' => CakeText::uuid()],
			'EBT' => ['ebt_bet_table_id' => CakeText::uuid()],
		];
		$MerchantPricing->set(['MerchantPricing' => ['merchant_id' => $merchantId]]);
		foreach($check as $productName => $betCheck) {
			$result = $MerchantPricing->isRequiredBetOrProduct($betCheck);
			$this->assertSame("Merchant doesn't have $productName! Add product first. Leave blank if not needed.", $result);
		}
		//Save a Visa product
		$ProductsAndService->create();
		$ProductsAndService->saveMany([
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'Visa%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'MasterCard%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'Discover%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'American Express%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'Debit%'"])
			],
			[
					'merchant_id' => $merchantId,
					'products_services_type_id' => $ProductsServicesType->field('id', ["products_services_description ILIKE 'EBT %'"])
			],
		]);
		foreach($check as $productName => $betCheck) {
			//overwrite to clear data
			$newCheck = array_map(function ($val){return null;}, $betCheck);
			$result = $MerchantPricing->isRequiredBetOrProduct($newCheck);
			$this->assertSame("BET is required! This merchant has a $productName product", $result);
		}

		foreach($check as $productName => $betCheck) {
			$this->assertTrue($MerchantPricing->isRequiredBetOrProduct($betCheck));
		}
	}
}
