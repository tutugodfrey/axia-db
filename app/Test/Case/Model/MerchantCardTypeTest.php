<?php
App::uses('MerchantCardType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantCardType Test Case
 *
 */
class MerchantCardTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductsAndService = ClassRegistry::init('ProductsAndService');
		$this->MerchantCardType = ClassRegistry::init('MerchantCardType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantCardType);
		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testGetMerchantCardTypesByMerchantId() {
		$this->assertEmpty($this->MerchantCardType->getMerchantCardTypesByMerchantId('00000000-9999-0000-0000-000000000001'));

		$expectedContain = ['MerchantCardType', 'CardType'];
		$result = $this->MerchantCardType->getMerchantCardTypesByMerchantId('3bc3ac07-fa2d-4ddc-a7e5-680035ec1040');
		$this->assertCount(3, $result);
		$this->assertEquals($expectedContain, array_keys(Hash::get($result, '0')));
		$expectedTypes = ['Visa', 'Mastercard', 'American Express'];
		$this->assertEquals($expectedTypes, Hash::extract($result, '{n}.CardType.card_type_description'));
	}

/**
 * test
 *
 * @return void
 */
	public function testGetListMerchantCardTypesByMerchantId() {
		$result = $this->MerchantCardType->getListMerchantCardTypesByMerchantId('00000000-0000-0000-0000-000000000003');
		$this->assertCount(2, $result);
		$expectedTypes = [
			'ddea093f-ab15-44f6-87ba-fb4c4235ced1' => 'American Express',
			'd82f4282-f816-4880-975c-53d42a7f02bc' => 'Discover',
		];
		$this->assertEquals($expectedTypes, $result);
	}

/**
 * testUpdateWithProductId
 *
 * @return void
 * @covers MerchantCardType::updateWithProductId()
 * @dataProvider providerTestUpdateWithProductId()
 */
	public function testUpdateWithProductId($mProdctsToSave, $cardTypesToSave, $mId, $productIds, $addNew, $expectations) {
		if (!empty($mProdctsToSave)) {
			$this->ProductsAndService->saveMany($mProdctsToSave);
		}
		if (!empty($cardTypesToSave)) {
			$this->MerchantCardType->saveMany($cardTypesToSave);
		}
		$this->MerchantCardType->updateWithProductId($mId, $productIds, $addNew);
		$actualRemaing = $this->MerchantCardType->find('list', ['fields' => ['card_type_id', 'card_type_id'], 'conditions' => ['merchant_id' => $mId]]);
		$actualCount = count($actualRemaing);

		$this->assertSame($actualCount, $expectations['count']);
		foreach ($expectations['card_types_remaining'] as $cardtypeId) {
			$this->assertNotEmpty(Hash::get($actualRemaing, $cardtypeId));
		}
	}

/**
 * Provider for testHasDiscover
 *
 * @dataProvider for self::testUpdateWithProductId()
 * @return void
 */
	public function providerTestUpdateWithProductId() {
		//Using an arbitrary merchant id/uuid
		$merchantId = CakeText::uuid();
		return [
			//Params list: [merchProducts, testCardtypesToSave, merchant_id, productIdArray/string, AddNew/Delete Cardtype, expected result]
			[	//ProductsAndService data array = 2 Visa and 2 MC products
				[
					["merchant_id" => $merchantId, 'products_services_type_id' => '3d4f7842-1cb9-4bad-b9fd-415b1d0eee30'],
					["merchant_id" => $merchantId, 'products_services_type_id' => '28dd4748-9699-41b9-9391-2524d3941018'],
					["merchant_id" => $merchantId, 'products_services_type_id' => 'ec5b6684-6f5c-4881-b26c-5a8233bde091'],
					["merchant_id" => $merchantId, 'products_services_type_id' => 'e6d8f040-9963-4539-ab75-3e19f679de16'],
				],
				//List of MerchantCardTypes to save to simulate initial state and test deletion
				[],
				//merchant id param to pass to updateWithProductId()
				$merchantId,
				//list of products param to pass to updateWithProductId()
				[
					'3d4f7842-1cb9-4bad-b9fd-415b1d0eee30',
					'28dd4748-9699-41b9-9391-2524d3941018',
					'ec5b6684-6f5c-4881-b26c-5a8233bde091',
					'e6d8f040-9963-4539-ab75-3e19f679de16',
				],
				//adding new cardtypes
				true,
				["count" => 2, 'card_types_remaining' => ['e9580d94-29ce-4de4-9fd4-c81d1afefbf4', 'd3216cb3-ee71-40c6-bede-ac9818e24f3a']],
			],
			//Test case: Passing both Visa And Mastercard as product params should only delete MasterCard CardType since the merchant does not have that Product
			[	//ProductsAndService data array = 1 Visa 
				[
					["merchant_id" => $merchantId, 'products_services_type_id' => 'ec5b6684-6f5c-4881-b26c-5a8233bde091'], //Visa Discount
				],
				//List of MerchantCardTypes to save to simulate initial state and test deletion
				[
					["merchant_id" => $merchantId, 'card_type_id' => 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4'],
					["merchant_id" => $merchantId, 'card_type_id' => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a'],
				],
				//merchant id param to pass to updateWithProductId()
				$merchantId,
				//list of products param to pass to updateWithProductId()
				[
					'28dd4748-9699-41b9-9391-2524d3941018',
					'ec5b6684-6f5c-4881-b26c-5a8233bde091',
					'e6d8f040-9963-4539-ab75-3e19f679de16',
				],
				//remove cardtypes
				false,
				["count" => 1, 'card_types_remaining' => ['e9580d94-29ce-4de4-9fd4-c81d1afefbf4']],
			],
			//Test case: Merchant has no products that are associated with any card types but initial state is has ALL CardTypes.
			[	//ProductsAndService data array = 1 ACH does not belong to any CardType
				[
					["merchant_id" => $merchantId, 'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7'], //ACH only
				],
				//List of MerchantCardTypes to save to simulate initial state and test deletion
				[
					["merchant_id" => $merchantId, 'card_type_id' => 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4'],
					["merchant_id" => $merchantId, 'card_type_id' => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a'],
					["merchant_id" => $merchantId, 'card_type_id' => '5581dfa8-8004-4b13-8448-093534627ad4'],
					["merchant_id" => $merchantId, 'card_type_id' => '5581dfa8-7788-4db5-b1c5-093534627ad4'],
					["merchant_id" => $merchantId, 'card_type_id' => 'ddea093f-ab15-44f6-87ba-fb4c4235ced1'],
					["merchant_id" => $merchantId, 'card_type_id' => 'd82f4282-f816-4880-975c-53d42a7f02bc'],
				],
				//merchant id param to pass to updateWithProductId()
				$merchantId,
				//list of products param to pass to updateWithProductId()
				[
					'3d4f7842-1cb9-4bad-b9fd-415b1d0eee30',
					'ec5b6684-6f5c-4881-b26c-5a8233bde091',
					'566f5f47-bac0-41f8-ac68-23f534627ad4',
					'72a445f3-3937-4078-8631-1f569d6a30ed',
					'551038c2-2b7c-4330-982d-1a3f34627ad4',
					'38d6c5b3-b6ca-4be8-9998-8b78976f0767',
				],
				//remove cardtypes
				false,
				//Final State should be no cardtypes
				["count" => 0, 'card_types_remaining' => []],
			],
			//Test case: Merchant has products associated with card types but Product Being passed as param is not (this should change nothing).
			[	//ProductsAndService data array = 1 ACH does not belong to any CardType
				[
					["merchant_id" => $merchantId, 'products_services_type_id' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7'], //ACH 
					["merchant_id" => $merchantId, 'products_services_type_id' => '3d4f7842-1cb9-4bad-b9fd-415b1d0eee30'], 
					["merchant_id" => $merchantId, 'products_services_type_id' => 'ec5b6684-6f5c-4881-b26c-5a8233bde091'], 
					["merchant_id" => $merchantId, 'products_services_type_id' => '566f5f47-bac0-41f8-ac68-23f534627ad4'], 
					["merchant_id" => $merchantId, 'products_services_type_id' => '72a445f3-3937-4078-8631-1f569d6a30ed'], 
					["merchant_id" => $merchantId, 'products_services_type_id' => '551038c2-2b7c-4330-982d-1a3f34627ad4'], 
					["merchant_id" => $merchantId, 'products_services_type_id' => '38d6c5b3-b6ca-4be8-9998-8b78976f0767'], 
				],
				//List of MerchantCardTypes to save to simulate initial state and test deletion
				[
					["merchant_id" => $merchantId, 'card_type_id' => 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4'],
					["merchant_id" => $merchantId, 'card_type_id' => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a'],
					["merchant_id" => $merchantId, 'card_type_id' => '5581dfa8-8004-4b13-8448-093534627ad4'],
					["merchant_id" => $merchantId, 'card_type_id' => '5581dfa8-7788-4db5-b1c5-093534627ad4'],
					["merchant_id" => $merchantId, 'card_type_id' => 'ddea093f-ab15-44f6-87ba-fb4c4235ced1'],
					["merchant_id" => $merchantId, 'card_type_id' => 'd82f4282-f816-4880-975c-53d42a7f02bc'],
				],
				//merchant id param to pass to updateWithProductId()
				$merchantId,
				//list of products param to pass to updateWithProductId()
				[
					'e8fa66a0-790f-4710-b7de-ef79be75a1c7',//Pass ACH product id
				],
				//remove cardtypes
				false,
				//Final State should be all initial cardtypes remain intact
				[
					"count" => 6, 
					'card_types_remaining' => [
						'e9580d94-29ce-4de4-9fd4-c81d1afefbf4',
						'd3216cb3-ee71-40c6-bede-ac9818e24f3a',
						'5581dfa8-8004-4b13-8448-093534627ad4',
						'5581dfa8-7788-4db5-b1c5-093534627ad4',
						'ddea093f-ab15-44f6-87ba-fb4c4235ced1',
						'd82f4282-f816-4880-975c-53d42a7f02bc',
					]
				],
			],
		];
	}
}
