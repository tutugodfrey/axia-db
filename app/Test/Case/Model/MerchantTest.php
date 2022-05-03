<?php
App::uses('Merchant', 'Model');
App::uses('UserParameterType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Merchant Test Case
 *
 */
class MerchantTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Merchant = $this->getMockForModel('Merchant', [
			// AppModel method that use AuthComponent
			'_getCurrentUser',
			// ChangeRequestBehavior that use AuthComponent
			'getSourceId'
		]);
		$this->Merchant->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->Merchant->expects($this->any())
			->method('getSourceId')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->Merchant->LoggableLog = ClassRegistry::init('LoggableLog');

		$this->_mockSystemTransactionListener($this->Merchant->MerchantAch);
		//Create Headers from CSV merchant import
		//Paper App Headers
		$this->paperAppCsvHeaders = [
			//Merchant Model
			'Not_oaID', //this is not online app data
			'Owner Type - Sole Prop',
			'Owner Type - Corp',
			//MerchantPricing AND MerchantCardtypes
			'AmexNum',
			'Discover',
			'AERateStructure',
			'AEQualifiedExemptions',
			//MerchantUwVolumes Model
			'Card Present Swiped',
			'Direct to Consumer',
			'Principal',
			'Daily Discount',
		];
		$this->onlineAppCsvHeaders = [
			//Merchant Model
			//this is header identifyes this data as online app data
			'oaID',
			'OwnerType-SoleProp',
			'OwnerType-Corp',
			//MerchantPricing AND MerchantCardtypes
			'AmexNum',
			'DoYouWantToAcceptDisc-New',
			'Amex Rate Structure',
			'Amex Downgrades',
			//MerchantUwVolumes Model
			'MethodofSales-CardPresentSwiped',
			'%OfProductSold-DirectToCustomer',
			'Owner1Name',
			'Discount PaidDaily',
		];
		$this->commonCsvData = [
			//Merchant Model Data
			'ContractorID' => 'Mark Weatherford',
			'MID' => '76411100' . str_shuffle('42405678'),
			'DBA' => 'I Sell Stuff and Junk',
			'EMail' => 'example1@email.com',

			//Address Model data
			'CorpName' => 'Stuff and Junk Co.',
			'CorpAddress' => '123 Nowhere',
			'Address' => '321 Everywhere',
			'BankName' => 'Gimme Money Bank and Trust',
			'BankAddress' => '99 Money Street',

			//MerchantBank Model data shoud encrypt
			'RoutingNum' => '123456789',
			'AccountNum' => '9876543210',
			'FeesRoutingNum' => '123456789',
			'FeesAccountNum' => '9876543210',

			//MerchantUwVolume
			'VisaMcVol' => '10',

			//MerchantPricing
			'Rate Structure' => 'Cost Plus',
			'Downgrades' => 'Visa/MasterCard/Discover Cost Plus .05%',
			'DebitDiscountRate' => '.10', //adds debit
			'EBTStmtFee' => '10', //adds ebt
			//MerchantAch is created when > 0
			'CreditEquipmentFee' => '.06',
			'CreditAppFee' => '.06',
		];
		$this->coversheetHeaders = [
			'setup_partner',
			'setup_referrer',
			'setup_reseller',
			'setup_referrer_pct_profit',
			'setup_referrer_pct_volume',
			'setup_referrer_pct_gross',
			'setup_reseller_pct_profit',
			'setup_reseller_pct_volume',
			'setup_reseller_pct_gross',
		];
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Merchant);
		parent::tearDown();
	}

/**
 * testGetInstMoCount method
 *
 * @covers Merchant::getInstMoCount
 * @return void
 */
	public function testGetInstMoCount() {
		$TimelineEntry = ClassRegistry::init('TimelineEntry');
		$tEntries = $TimelineEntry->find('list', [
			'conditions' => ['timeline_item_id' => TimelineItem::GO_LIVE_DATE],
			'fields' => ['id', 'merchant_id'],
			'order' => 'timeline_date_completed DESC'
		]);
		$nowMo = (int)date('m');
		$nowY = (int)date('Y');
		$expected = [ //List in DESC order by date
			'00000000-0000-0000-0000-000000000003' => $nowMo - 1 + 12 * ($nowY - 2016),
			'4e3587be-aafb-48c4-9b6b-8dd26b8e94aa' => 1 + $nowMo - 5 + 12 * ($nowY - 2016),
			'00000000-0000-0000-0000-000000000004' => $nowMo - 5 + 12 * ($nowY - 2015),
		];
		//iterate through existing data
		foreach ($tEntries as $id => $merchantId) {
			$tEntryIds[] = $id;
			$actual = $this->Merchant->getInstMoCount($merchantId);
			$this->assertSame($expected[$merchantId], $actual);
		}

		$newData = [
			[
				'id' => $tEntryIds[0],
				'timeline_date_completed' => "$nowY-$nowMo-16", //result will be 0 for this]
			],
			[
				'id' => $tEntryIds[1],
				'timeline_date_completed' => ($nowY - 1) . "-$nowMo-01", //result will be 13 months for this]
			],
			[
				'id' => $tEntryIds[2],
				'timeline_date_completed' => ($nowY - 1) . "-$nowMo-16", //result will be 12 months for this]
			]
		];

		//update existing dat with new dates
		$this->Merchant->TimelineEntry->saveMany($newData);
		$expected = [ //List STILL in DESC order by date
			'4e3587be-aafb-48c4-9b6b-8dd26b8e94aa' => 0,
			'00000000-0000-0000-0000-000000000003' => 13,
			'00000000-0000-0000-0000-000000000004' => 12,
		];
		$tEntries = $TimelineEntry->find('list', [
			'conditions' => ['timeline_item_id' => TimelineItem::GO_LIVE_DATE],
			'fields' => ['id', 'merchant_id'],
			'order' => 'timeline_date_completed DESC'
		]);

		//iterate through updated data
		foreach ($tEntries as $id => $merchantId) {
			$actual = $this->Merchant->getInstMoCount($merchantId);
			$this->assertSame($expected[$merchantId], $actual);
		}
	}

/**
 * testIsPciQualified method
 *
 * @covers Merchant::isPciQualified
 * @return void
 */
	public function testIsPciQualified() {
		$actual = $this->Merchant->isPciQualified('00000000-0000-0000-0000-000000000003');
		$this->assertFalse($actual);
	}

/**
 * testGetDefaultSearchValues method
 *
 * @covers Merchant::getDefaultSearchValues
 * @return void
 */
	public function testGetDefaultSearchValues() {
		$actual = $this->Merchant->getDefaultSearchValues();
		$this->assertSame(['active' => 1], $actual);
	}

/**
 * testAssocTrim method
 *
 * @covers Merchant::assocTrim
 * @return void
 */
	public function testAssocTrim() {
		$testData = [
			'   lots of extra spaces ' => '      over here too!',
			'   more spaces ' => '      and here          '
		];
		$actual = $this->Merchant->assocTrim($testData);
		$expected = [
			'lots of extra spaces' => 'over here too!',
			'more spaces' => 'and here',
		];
		
		$this->assertSame($expected, $actual);
	}
/**
 * testGetViewDropDownsData method
 *
 * @covers Merchant::getViewDropDownsData
 * @return void
 */
	public function testGetViewDropDownsData() {
		$actual = $this->Merchant->getViewDropDownsData();
		$this->assertNotEmpty($actual['User']);
		$this->assertContains('The Best Partner Rep', $actual['User']);
		$this->assertNotEmpty($actual['Sm']);
		$this->assertContains('Ben Franklin', $actual['Sm']);
		$this->assertNotEmpty($actual['Sm2']);
		$this->assertContains('Frank Williams', $actual['Sm2']);
		$this->assertNotEmpty($actual['TimelineItem']);
		$this->assertContains('bc8feb41-d006-4d07-8bc1-dafc30fafedc', $actual['TimelineItem']);
	}

/**
 * testGetPCI method
 *
 * @covers Merchant::getPCI
 * @return void
 */
	public function testGetPCI() {
		$actual = $this->Merchant->getPCI('00000000-0000-0000-0000-000000000003');
		$this->assertNotEmpty($actual);
		$this->assertNotEmpty($actual['Merchant']);
		$this->assertSame('00000000-0000-0000-0000-000000000003', $actual['Merchant']['id']);
	}
/**
 * testOrConditions method
 *
 * @covers Merchant::orConditions
 * @return void
 */
	public function testOrConditions() {
		$expected = [
			'OR' => [
				'Merchant.merchant_mid ILIKE' => '%testABC123%',
				'Merchant.merchant_dba ILIKE' => '%testABC123%',
			]
		];
		$actual = $this->Merchant->orConditions(['search' => 'testABC123']);
		$this->assertSame($expected, $actual);
	}

/**
 * testValidValue method
 *
 * @covers Merchant::validValue
 * @return void
 */
	public function testValidValue() {
		$this->assertFalse($this->Merchant->validValue([]));
		//Test referrer value
		$valueToCheck = ['ref_p_value' => .55];
		$modelData = [
			'Merchant' => ['ref_p_type' => Merchant::PERCENTAGE]
		];
		$this->Merchant->set($modelData);
		$this->assertTrue($this->Merchant->validValue($valueToCheck));

		//Test reseller value
		$valueToCheck = ['res_p_value' => .55];
		$modelData = [
			'Merchant' => ['res_p_type' => Merchant::PCT_MINUS_GP]
		];
		$this->Merchant->clear();
		$this->Merchant->set($modelData);
		$this->assertTrue($this->Merchant->validValue($valueToCheck));
		
		//Test other types
		$valueToCheck = ['res_p_value' => .55];
		$modelData = [
			'Merchant' => ['res_p_type' => null]
		];
		$this->Merchant->clear();
		$this->Merchant->set($modelData);
		$this->assertTrue($this->Merchant->validValue($valueToCheck));

		//Test invalid state
		$valueToCheck = ['res_p_value' => '1.12365855'];
		$modelData = [
			'Merchant' => ['res_p_type' => null]
		];
		$this->Merchant->clear();
		$this->Merchant->set($modelData);
		$this->assertFalse($this->Merchant->validValue($valueToCheck));
	}

/**
 * testSearchByLocation method
 *
 * @covers Merchant::searchByLocation
 * @return void
 */
	public function testSearchByLocation() {
		$searchStr = ['location_description' => 'street-1'];
		$actual = $this->Merchant->searchByLocation($searchStr);
		$expected = 'SELECT "Address"."merchant_id" AS "Address__merchant_id" FROM "public"."addresses" AS "Address"   WHERE "Address"."address_type_id" = \'795f442e-04ab-43d1-a7f8-f9ba685b90ac\' AND "Address"."address_street" = \'street-1\'';
		
		$this->assertSame($expected, $actual);
	}

/**
 * testFindMerchantDBAorMID method
 *
 * @param string $searchStr string to search for
 * @param bool $isMID wether search string is an MID (true) or a DBA (false)
 * @param bool $active search active/inactive merchants
 * @param int $limit amount of results
 * @param mixed $expected Expected result
 * @dataProvider findMerchantDBAorMIDData
 * @covers Merchant::findMerchantDBAorMID
 * @return void
 */
	public function testFindMerchantDBAorMID($searchStr, $isMID, $active, $limit, $expected) {
		$actual = $this->Merchant->findMerchantDBAorMID($searchStr, $isMID, $active, $limit);
		$this->assertEquals($expected, $actual);
	}

/**
 * Data provider for testFindMerchantDBAorMID
 *
 * @return array
 */
	public function findMerchantDBAorMIDData() {
		return [
			['3948000030002785', true, 1, null, ['3bc3ac07-fa2d-4ddc-a7e5-680035ec1040' => '3948000030002785']],
			['3948000030002785', true, 1, 1, '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040'],
			['16 Hands', false, 1, null, ['00000000-0000-0000-0000-000000000004' => '16 Hands']],
			['16 Hands', false, 1, 1, ['00000000-0000-0000-0000-000000000004' => '16 Hands']],
			['4541619030000153', true, 0, null, ['00000000-0000-0000-0000-000000000005' => '4541619030000153']],
			['Inactive merchant', false, 0, null, ['00000000-0000-0000-0000-000000000005' => 'Inactive merchant']],
			['Blah!', false, 0, null, []],
			['Blah!', false, 1, null, []],
		];
	}

/**
 * testFindMerchantDataById method
 *
 * @covers Merchant::findMerchantDataById
 * @return void
 */
	public function testFindMerchantDataById() {
		$actual = $this->Merchant->findMerchantDataById('00000000-9999-0000-0000-000000000001');
		$this->assertEmpty($actual);
		$actual = $this->Merchant->findMerchantDataById('00000000-0000-0000-0000-000000000004');
		$this->assertNotEmpty($actual);
		$this->assertEquals('00000000-0000-0000-0000-000000000004', Hash::get($actual, 'Merchant.id'));
		// Check the related models are contained
		$this->assertCount(39, $actual);
		// Check that some models are in the result
		$this->assertTrue(isset($actual['MerchantNote']));
		$this->assertTrue(isset($actual['User']));
		$this->assertTrue(isset($actual['BetNetwork']));
		$this->assertTrue(isset($actual['MerchantReject']));
	}

/**
 * testHasPendingMerchantNote method
 *
 * @covers Merchant::hasPendingMerchantNote
 * @return void
 */
	public function testHasPendingMerchantNote() {
		// Check Merchant without notes
		$result = $this->Merchant->hasPendingMerchantNote('3bc3ac07-fa2d-4ddc-a7e5-680035ec1040');
		$this->assertFalse($result);

		// Check Merchant with completed notes
		$result = $this->Merchant->hasPendingMerchantNote('00000000-0000-0000-0000-000000000003');
		$this->assertFalse($result);

		// Check Merchant with a pending general note
		$result = $this->Merchant->hasPendingMerchantNote('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		$this->assertTrue($result);
		// Filter by other note type
		$result = $this->Merchant->hasPendingMerchantNote('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa', NoteType::CHANGE_REQUEST_ID);
		$this->assertFalse($result);

		// Check Merchant with a pending change request
		$result = $this->Merchant->hasPendingMerchantNote('00000000-0000-0000-0000-000000000004', NoteType::CHANGE_REQUEST_ID);
		$this->assertTrue($result);
	}

/**
 * testGetEditFormRelatedData method
 *
 * @covers Merchant::getEditFormRelatedData
 * @return void
 */
	public function testGetEditFormRelatedData() {
		$data = [
			'Merchant' => [
				'id' => '00000000-0000-0000-0000-000000000003',
			],
		];

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
					'id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
				]
			],
			'ddOptns' => [
				'User' => [
					'003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee',
					'00000000-0000-0000-0000-000000000004' => 'Bill McAbee inactive',
					'00000000-0000-0000-0000-000000000005' => 'Bill McAbee blocked',
					'00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'Mark Weatherford',
					'113114ae-da7b-4970-94bd-fa0c0f25c786' => 'Mc Payne',
					'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda' => 'Noel Golondrina',
					'113114ae-7777-7777-7777-fa0c0f25c786' => 'Mr Installer',
					'113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep',
					'113114ae-9999-9999-9999-fa0c0f25c786' => 'No Profile Partner Rep',
					'54365df7-6b97-4f86-9e6f-8638eb30cd9e' => 'A Partner',
					'00000000-0000-0000-0000-000000000010' => 'Ben Franklin',
					'43265df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Bob Apple',
					'd2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'Frank Williams',
					'00000000-0000-0000-0000-000000000014' => 'John Smith',
					'32165df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Slim Pickins',
					'548bce29-9c0d-42e0-b46c-179151ca54b4' => 'Mr. Refer Referington',
					'1d58a4cd-664a-4c08-9b38-94ffc7e61afa' => 'Mr. Resel Resellington'
				],
				'Sm' => [
					'00000000-0000-0000-0000-000000000010' => 'Ben Franklin',
					'003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee'
				],
				'Sm2' => [
					'd2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'Frank Williams'
				],
				'TimelineItem' => [
					'Order Equip' => '04cca8b8-d686-41a2-9540-61eba8514ba3',
					'Kit Sent' => '0a84d63f-6976-4764-97fb-236deaded207',
					'Contact Merch' => '16490e3c-b6ee-46bb-9f1d-6853ec2d9f24',
					'Go-Live date' => '17b613f5-58c6-4dcc-b081-a060387d0108',
					'File Built' => '2ce06a07-4572-4616-900b-7944fac74fc7',
					"Equip Rec'd" => '2d34d29e-3171-4933-b23c-313b4be4c013',
					'Declined' => '320c49d0-631a-4fb2-a428-332099904692',
					'Tested' => '3fb9e110-1110-4177-84f5-4acd79359960',
					'Ts & Cs Sent' => '51c3b92a-d0d8-45a9-93ee-4045ed7d3c0d',
					"Rec'd Signed Install Sheet" => '572b98ae-cef7-45a7-bdb8-601866e6517f',
					'Follow up call' => '611580af-0aea-426c-a859-3d1b9a3480b9',
					'Keyed' => '77ef5361-0f16-48f6-985c-45afa5df0044',
					'Days to Go-Live' => '9ea45748-d05f-491f-9d5c-c77e2c66bae8',
					'Signed' => '9eff98d4-621e-4ed2-adfb-73eaacbcc38f',
					'Submitted' => 'a2f754b7-dbbc-4249-bd2c-d129815ac4c7',
					'Install Commissioned' => 'a9130700-0813-4a86-80cb-19796a054c5c',
					'Approved' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'Download' => 'e6ebcd50-60f6-4daf-a752-4cac0334fec3',
					'Received' => 'f14126a0-edf6-4e41-a6eb-2a88cb39c09b',
					'Month Paid' => 'f8014e00-1acc-409e-8cc9-a9ac691b4cb4',
					'Expected to Go-Live' => 'bc8feb41-d006-4d07-8bc1-dafc30fafedc'
				]
			],
			'cardTypes' => [
				'e9580d94-29ce-4de4-9fd4-c81d1afefbf4' => 'Visa',
				'd3216cb3-ee71-40c6-bede-ac9818e24f3a' => 'Mastercard',
				'ddea093f-ab15-44f6-87ba-fb4c4235ced1' => 'American Express',
				'd82f4282-f816-4880-975c-53d42a7f02bc' => 'Discover',
				'd0ba718a-9296-4cdd-a6be-1d6d52e9c66c' => "Diner's Club",
				'95a6b6af-c084-4ad9-bfc1-f406f0df1601' => 'JCB',
				'5581dfa8-8004-4b13-8448-093534627ad4' => 'Debit',
				'5581dfa8-7788-4db5-b1c5-093534627ad4' => 'EBT'
			],
			'partners' => [],
			'networks' => [
				'4ae4fb7d-d989-4662-b060-e7b0190bd7cf' => 'VISANET',
				'01940ba8-3135-4203-ae33-a23a2cf8697b' => 'PTI',
				'620a6eb1-8ead-42b6-8478-b1556df625a4' => 'ENVOY',
				'666ea487-76cb-4721-bfdb-b12e3c1c0e09' => 'FD North',
				'aff88e90-7fc3-4609-ad04-fc7ae2868299' => 'FD Omaha'
			],
			'merchantBins' => [
				'55cc6d66-afd8-4a04-8ccb-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-bde8-4fec-9b49-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-c680-4e70-84c1-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-ce50-4020-9f68-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-d620-4c23-8ba2-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-ddf0-419d-8fcf-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-e5c0-4bf3-aa14-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-ed90-4972-bda6-17f134627ad4' => 'Lorem ipsum dolor s',
				'55cc6d66-f560-44c7-9446-17f134627ad4' => 'Lorem ipsum dolor s',
				'5806a480-7080-4856-9b65-319b34627ad4' => '424550',
				'55cc6d66-fd30-444b-8c3f-17f134627ad4' => 'Lorem ipsum dolor s',
				'44977880-7080-5555-9b65-449778627ad4' => '449778'
			],
			'cancellationFees' => [
				'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor sit amet'
			],
			'merchantAcquirers' => [
				'e8797a20-601d-4d1e-8a07-50266a013075' => 'TSYS',
				'7a270137-75d8-4a49-8734-3d03e087d1bd' => 'Sage Payments',
				'57efbef2-0ae4-4722-92fa-d7219b7fdda4' => 'FDC',
				'4c501966-610f-4fe0-abc2-7a0245b292cf' => 'Direct Connect',
				'5739a131-00cc-43b4-84ea-17c934627ad4' => 'Pivotal Canada',
				'5739a131-0c60-4fcf-a761-17c934627ad4' => 'BAMS',
				'192f8406-836d-4341-a1ee-d9d568cdb53d' => 'Axia',
			],
			'referers' => [
				'00000000-0000-0000-0000-000000000005' => 'Bill McAbee blocked'
			],
			'resellers' => [],
			'groups' => [
				'55cc6d31-f814-49ba-bbbf-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-0624-4753-a730-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-0f20-4572-ae76-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-1754-425e-a64b-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-1f88-4bdd-bddc-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-2820-4ace-8559-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-3054-46d6-88be-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-3888-4548-a624-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-40bc-43a6-b097-17a334627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d31-49b8-4af2-8624-17a334627ad4' => 'Lorem ipsum dolor sit amet'
			],
			'entities' => [
				'00000000-0000-0000-0000-000000000001' => 'Entity 1'
			],
			'backEndNetworks' => [
				'55cc6d06-8a18-48ef-83f9-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-9b48-49b5-bc4d-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-a37c-4f5f-ae61-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-aae8-425d-97d7-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-b254-474d-8e76-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-b9c0-42f9-89ee-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-c0c8-4122-aaf2-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-c7d0-4e20-aa24-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-cf3c-44f4-8d2a-176534627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d06-d6a8-4289-895f-176534627ad4' => 'Lorem ipsum dolor sit amet'
			],
			'originalAcquirers' => [
				'55cc6d7e-a12c-4ffb-9ea9-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-ae74-4bd5-830e-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-b70c-464e-ae1c-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-be78-4c49-94d6-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-c5e4-4529-88f8-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-d7dc-4aa7-bb73-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-e330-482a-a113-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-ea9c-4d30-99e7-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-f1a4-407b-a131-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'55cc6d7e-f910-475f-b6d1-181f34627ad4' => 'Lorem ipsum dolor sit amet',
				'56e057b3-e860-4d89-be16-319134627ad4' => 'Axia (before 10/1/14)',
			],
			'betNetworks' => [
				'55cc6d99-d534-45f1-acc7-184d34627ad4' => 'Bet Network 1',
				'56e05859-7d90-4777-9fde-31a834627ad4' => 'TSYS (pre 10/1/14)',
			],
			'isEditLog' => false,
			'userCanApproveChanges' => true,
			'brands' => [
				'02daf34e-6789-49a2-b750-b1bb06e046d5' => 'Axia Med',
				'd38411ea-82d0-4f69-ae48-c728f6b96a03' => 'Axia Tech',
				'16dd937f-8878-4d99-ab74-6eab1ea05bde' => 'Axia Payments'
			],
			'nonWomplyUsers' => [],
			'womplyStatuses' => [
				'7cb1eec8-7eb8-43d8-8e63-7f6a4a8d28a9' => 'CALL',
				'855dedd6-5f57-4f25-8607-c01bcfe3f848' => 'DNC',
				'6f0a9862-7165-484a-9391-03ea52d3d5f1' => 'SOLD-ESS'
			],
			'betRateStructures' => [],
			'organizations' => [
				'8d08c7ed-c61a-4311-b088-706d6d8c052c' => 'Test Org 1'
			],
			'regions' => [
				'ef3b25d3-b4e7-4137-8663-42fc83f7fc71' => 'Org 1 - Region 1'
			],
			'subregions' => [
				'9b26cfbf-4511-4c5b-a10c-59d99b97bbd2' => 'Org 1 - Region 1 - Subregion 1'
			],
			'clients' => [],
			'merchantTypes' => [
				'267007da-e0ff-4866-b7ef-264571d29df8' => 'ACH',
				'07afdbac-d63a-4a69-90fd-8732223bcf8b' => 'Acquiring',
				'7886b254-4b38-41b0-9d87-ed409cfe549f' => 'CareCredit',
				'5f12c0ca-cf11-4033-b335-7e487aef02e5' => 'Gateway',
				'caee3415-d37e-474f-af62-1565c04b043c' => 'Text&Pay',
			],
			'acquiringTypeId' => '07afdbac-d63a-4a69-90fd-8732223bcf8b'
		];

		$MerchantChange = $this->getMockForModel('MerchantChange', ['_getCurrentUser']);
		$MerchantChange->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->Merchant->MerchantChange = $MerchantChange;
		$this->assertEquals($expected, $this->Merchant->getEditFormRelatedData($data));
	}

/**
 * testEditInvalidType method
 *
 * @covers Merchant::edit
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Invalid edit type
 * @return void
 */
	public function testEditInvalidType() {
		$merchantId = '00000000-0000-0000-0000-000000000003';
		$newData = [
			'Merchant' => [
				'id' => $merchantId,
				'merchant_dba' => 'Save with invalid type',
			],
		];
		$this->Merchant->edit($newData, [], 'invalid-type');
	}

/**
 * testIsCancelled method
 *
 * @covers Merchant::isCancelled
 * @return void
 */
	public function testIsCancelled() {
		$this->assertFalse($this->Merchant->isCancelled('00000000-0000-0000-0000-000000000003'));
		$this->assertTrue($this->Merchant->isCancelled('00000000-0000-0000-0000-000000000004'));
	}

/**
 * testMakeCsvString method
 *
 * @covers Merchant::makeCsvString
 * @return void
 */
	public function testMakeCsvString() {
		$merchants = [
			[
				'Merchant' => [
					'merchant_dba' => 'Merchant-Test-1',
					'merchant_mid' => '11111',
					'merchant_contact' => 'First contact',
					'active' => false,
				],
				'User' => [
					'user_first_name' => 'Mr Merchant',
					'user_last_name' => 'Test'
				],
				'AddressBus' => [
					'address_city' => 'New York',
				],
				'MerchantPci' => [
					'saq_completed_date' => '2011-06-28',
				],
				'SaqMerchant' => [
					'SaqMerchantSurveyXref' => [
						0 => [
							'datecomplete' => '2011-06-27',
						],
					],
				],
				'SaqControlScan' => [
					'pci_compliance' => 'Yes',
				],
			],
			[
				'Merchant' => [
					'merchant_dba' => 'Merchant-Test-2',
					'merchant_mid' => '2222',
					'active' => true,
				],
				'User' => [
					'user_first_name' => 'Dr',
					'user_last_name' => 'Merchant'
				],
				'AddressBus' => [
					'address_state' => 'S/C de Tenerife',
				],
				'MerchantPci' => [
					'saq_completed_date' => '2014-11-01',
				],
				'SaqControlScan' => [
					'pci_compliance' => 'Nop',
				],
				'EquipmentProgramming' => [
					'hardware_serial' => 'h-serial-2'
				],
			],
		];
		$csvString = $this->Merchant->MakeCsvString($merchants);

		$expected = 'DBA,MID,Client ID,Rep,City,State,Phone,Contact,PCI,V#,Company,Organization,Region,Subregion,Location,Partner,Last Activity Date,Status' . "\n";
		$expected .= '"Merchant-Test-1",="11111",,Mr Merchant Test,New York,,,First contact,Compliant,,,,,,,,,Active' . "\n";
		$expected .= '"Merchant-Test-2",="2222",,Dr Merchant,,S/C de Tenerife,,,Non-Compliant,h-serial-2,,,,,,,,Active' . "\n";
		$this->assertEquals($expected, $csvString);
	}

/**
 * testSetPaginatorSettings method
 *
 * @covers Merchant::setPaginatorSettings
 * @return void
 */
	public function testSetPaginatorSettings() {
		$filters = [
			'active' => 1,
			'userIdsFilter' => [],
			'address_city' => 'New York',
			'PCI_compliance' => 'no',
		];
		$settings = $this->Merchant->setPaginatorSettings($filters);
		$expected = [
			'limit' => 100,
			'order' => [
				'merchant_dba' => 'asc'
			],
			'conditions' => [
				'Merchant.active' => 1,
				'AddressBus.address_city ilike' => 'New York',
				[
					'AND' => [
						[
							'OR' => [
								'MerchantPci.saq_completed_date =' => '',
								"MerchantPci.saq_completed_date < now() - interval '1 year'",
							]
						]
					]
				],
				'SaqControlScan.pci_compliance =' => null
			],
			'fiter' => true,
			'fields' => [
				'Merchant.id',
				'Merchant.merchant_mid',
				'Merchant.user_id',
				'Merchant.active',
				'Merchant.merchant_dba',
				'Merchant.merchant_contact',
				'Client.client_id_global',
				'User.id',
				'User.user_first_name',
				'User.user_last_name',
				'EquipmentProgramming.hardware_serial',
				'Entity.entity_name',
				'Organization.name',
				'Region.name',
				'Subregion.name',
				'LastDepositReport.last_deposit_date'
			],
			'contain' => [
				'AddressBus' => [
					'fields' => [
						'AddressBus.address_city',
						'AddressBus.address_street',
						'AddressBus.address_state',
						'AddressBus.address_phone'
					],
				],
				'MerchantPci' => [
					'fields' => [
						'MerchantPci.saq_completed_date'
					]
				],
				'SaqControlScan' => [
					'fields' => [
						'((CASE WHEN "SaqControlScan"."pci_compliance" IS NULL THEN \'n/a\' ELSE "SaqControlScan"."pci_compliance" END)) as "SaqControlScan__pci_compliance"',
					]
				],
				'Partner' => ['fields' => ['Partner.user_first_name', 'Partner.user_last_name']],
			],
			'joins' => [
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => [
						'Merchant.user_id = User.id'
					]
				],
				[
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
						'conditions' => [
							"Merchant.client_id = Client.id",
						]
				],
				[
					'table' => '(SELECT DISTINCT ON (merchant_id) merchant_id, hardware_serial FROM equipment_programmings ORDER BY merchant_id, date_entered ASC)',
					'alias' => 'EquipmentProgramming',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = EquipmentProgramming.merchant_id'
					]
				],
				[
					'table' => 'last_deposit_reports',
					'alias' => 'LastDepositReport',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = LastDepositReport.merchant_id'
					]
				],
				[
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.entity_id = Entity.id'
					]
				],
				[
					'table' => 'organizations',
					'alias' => 'Organization',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.organization_id = Organization.id'
					]
				],
				[
					'table' => 'regions',
					'alias' => 'Region',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.region_id = Region.id'
					]
				],
				[
					'table' => 'subregions',
					'alias' => 'Subregion',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.subregion_id = Subregion.id'
					]
				],
			]
		];
		$this->assertEquals($expected, $settings);

		$filters = [
			'active' => 2,
			'userIdsFilter' => [],
			'dba_mid' => 'dba-mid-2',
			'address_state' => 'Mississippi',
			'PCI_compliance' => 'yes',
		];
		$settings = $this->Merchant->setPaginatorSettings($filters);
		$expected = [
			'limit' => 100,
			'order' => [
				'merchant_dba' => 'asc'
			],
			'conditions' => [
				[
					'OR' => [
						'Merchant.merchant_mid ILIKE' => '%dba-mid-2%',
						'Merchant.merchant_dba ILIKE' => '%dba-mid-2%'
					]
				],
				'AddressBus.address_state' => 'Mississippi',
				[
					'AND' => [
						[
							'OR' => [
									"MerchantPci.saq_completed_date > now() - interval '1 year'",
									'SaqControlScan.pci_compliance =' => 'Yes'
							]
						]
					]
				]
			],
			'fiter' => true,
			'fields' => [
				'Merchant.id',
				'Merchant.merchant_mid',
				'Merchant.user_id',
				'Merchant.active',
				'Merchant.merchant_dba',
				'Merchant.merchant_contact',
				'Client.client_id_global',
				'User.id',
				'User.user_first_name',
				'User.user_last_name',
				'EquipmentProgramming.hardware_serial',
				'Entity.entity_name',
				'Organization.name',
				'Region.name',
				'Subregion.name',
				'LastDepositReport.last_deposit_date'
			],
			'contain' => [
				'AddressBus' => [
					'fields' => [
						'AddressBus.address_city',
						'AddressBus.address_street',
						'AddressBus.address_state',
						'AddressBus.address_phone'
					]
				],
				'MerchantPci' => [
					'fields' => [
						'MerchantPci.saq_completed_date'
					]
				],
				'SaqControlScan' => [
					'fields' => [
						'((CASE WHEN "SaqControlScan"."pci_compliance" IS NULL THEN \'n/a\' ELSE "SaqControlScan"."pci_compliance" END)) as "SaqControlScan__pci_compliance"',
					]
				],
				'Partner' => ['fields' => ['Partner.user_first_name', 'Partner.user_last_name']],
			],
			'joins' => [
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => [
						'Merchant.user_id = User.id'
					]
				],
				[
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
						'conditions' => [
							"Merchant.client_id = Client.id",
						]
				],
				[
					'table' => '(SELECT DISTINCT ON (merchant_id) merchant_id, hardware_serial FROM equipment_programmings ORDER BY merchant_id, date_entered ASC)',
					'alias' => 'EquipmentProgramming',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = EquipmentProgramming.merchant_id'
					]
				],
				[
					'table' => 'last_deposit_reports',
					'alias' => 'LastDepositReport',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = LastDepositReport.merchant_id'
					]
				],
				[
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.entity_id = Entity.id'
					]
				],
				[
					'table' => 'organizations',
					'alias' => 'Organization',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.organization_id = Organization.id'
					]
				],
				[
					'table' => 'regions',
					'alias' => 'Region',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.region_id = Region.id'
					]
				],
				[
					'table' => 'subregions',
					'alias' => 'Subregion',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.subregion_id = Subregion.id'
					]
				],
			]
		];
		$this->assertEquals($expected, $settings);
	}

/**
 * testImportMerchantsWithDuplicatedMerchants method
 *
 * @param array $testData sample that emulates data extracted from a CSV file
 * @dataProvider getImportMerchantData
 * @covers Merchant::importMerchantData
 * @return void
 */
	public function testImportMerchantsWithDuplicatedMerchants($testData) {
		$uId = $this->Merchant->User->field('id', ['fullname' => $testData[0]['ContractorID']]);
		if (empty($this->Merchant->User->UserCompensationProfile->field('id', ['user_id' => $uId]))) {
			$this->Merchant->User->UserCompensationProfile->create();
			$ucpData = [
				'user_id' => $uId
			];
			$this->Merchant->User->UserCompensationProfile->save($ucpData);
		}
		$preUploadCount = $this->Merchant->find('count');
		$result = $this->Merchant->importMerchantData($testData);
		//There should be no errors returned
		$this->assertEmpty(Hash::get($result, "Errors"));
		$expected = $this->Merchant->find('count');
		///param1 paper app data with duplicated merchants should result in only one merchant being created
		$this->assertEqual($expected, $preUploadCount + 1);
	}

/**
 * Data provider for testImportMerchantsWithDuplicatedMerchants
 *
 * @return array
 */
	public function getImportMerchantData() {
		return [
			[
				[//param1 paper app data with duplicated merchants should
					//result in only one being created
					[
						'ContractorID' => 'Mark Weatherford',
						'MID' => '3948906204000946',
						'DBA' => '16 Hands',
						'Restaurant' => 'On',
						'EMail' => 'example1@email.com',
						'VisaMcVol' => '50',
						'MonthlyVol' => '25',
						'AvgTicket' => '5',
						'CreditEquipmentFee' => 0.6,
						'EBTStmtFee' => 0.1,
					],
					[
						'ContractorID' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
						'MID' => '3948906204000946',
						'DBA' => '16 Hands',
						'EMail' => 'example2@email.com',
						'Internet' => 'TRUE',
						'VisaMcVol' => '100',
						'MonthlyVol' => '55',
						'AvgTicket' => '12',
						'DebitMonthlyAccessFee' => 0.2,
						'AnnualFee' => 0.25,
					],
				],
			],
		];
	}

/**
 * testImportWithAssocRefererResellerErrors method
 *
 * @param array $values test values that emulate data extracted from a CSV file
 * @param array $cSheetValues sample that emulates data for coversheet
 * @dataProvider getImportWithOnlineAppData
 * @covers Merchant::importMerchantData
 * @return void
 */
	public function testImportWithAssocRefererResellerErrors($values, $cSheetValues) {
		//Perform tests for only if data contains referer or reseller
		$hasRef = in_array('Mr. Refer Referington', $cSheetValues);
		$hasRes = in_array('Mr. Resel Resellington', $cSheetValues);
		$refId = $this->Merchant->User->field('id', ['fullname' => 'Mr. Refer Referington']);
		$resId = $this->Merchant->User->field('id', ['fullname' => 'Mr. Resel Resellington']);
		$partnerId = $this->Merchant->User->field('id', ['fullname' => 'The Best Partner Rep']);

		$this->Merchant->User->updateAll(
			['active' => 1, 'username' => "'arbitraryusername'"],
			[
				'OR' => [
					['id' => $refId], ['id' => $resId], ['id' => $partnerId]
				]
			]
		);
		$this->Merchant->User->UsersRole->saveMany([
			[
				'role_id' => $this->Merchant->User->Role->field('id', ['name' => User::ROLE_RESELLER]),
				'user_id' => $resId
			],
			[
				'role_id' => $this->Merchant->User->Role->field('id', ['name' => User::ROLE_REFERRER]),
				'user_id' => $refId
			],
			[
				'role_id' => $this->Merchant->User->Role->field('id', ['name' => User::ROLE_PARTNER]),
				'user_id' => $partnerId
			],
		]);
		if ($hasRef || $hasRes) {
			$dataSets = [];
			$keyVals = array_combine($this->onlineAppCsvHeaders, $values);
			$cShKeyVals = array_combine($this->coversheetHeaders, $cSheetValues);

			//Put everything in zeroth array entry which is what is expected
			$collection = [array_merge($keyVals, $cShKeyVals, $this->commonCsvData)];
			//First dataset is with Online app headers and coversheet headers/values
			$dataSets[] = $collection;

			foreach ($dataSets as $csvDat) {
				$uId = $this->Merchant->User->field('id', ['fullname' => $csvDat[0]['ContractorID']]);
				if (empty($this->Merchant->User->UserCompensationProfile->field('id', ['user_id' => $uId]))) {
					$this->Merchant->User->UserCompensationProfile->create();
					$ucpData = [
						'user_id' => $uId
					];
					$this->Merchant->User->UserCompensationProfile->save($ucpData);
				}
				$preUploadCount = $this->Merchant->find('count');
				$result = $this->Merchant->importMerchantData($csvDat);

				if ($hasRef) {
					$this->assertContains(
						"Mark Weatherford Partner-Rep's compensation profile is associated with Partner The Best Partner Rep, but is not associated with the Referrer Mr. Refer Referington",
						Hash::extract($result, "Errors")
					);
				} elseif ($hasRes) {
					$this->assertContains(
						"Mark Weatherford Partner-Rep's compensation profile is associated with Partner The Best Partner Rep, but is not associated with  nor the Reseller Mr. Resel Resellington.",
						Hash::extract($result, "Errors")
					);
				}
			}
		}
	}

/**
 * testImportMerchantData method
 *
 * @param array $values test values that emulate data extracted from a CSV file
 * @param array $cSheetValues sample that emulates data for coversheet
 * @dataProvider getImportWithOnlineAppData
 * @covers Merchant::importMerchantData
 * @return void
 */
	public function testImportMerchantData($values, $cSheetValues) {
		$dataSets = [];
		$keyVals = array_combine($this->onlineAppCsvHeaders, $values);
		$cShKeyVals = array_combine($this->coversheetHeaders, $cSheetValues);

		//Put everything in zeroth array entry which is what is expected
		$collection = [array_merge($keyVals, $cShKeyVals, $this->commonCsvData)];
		//First dataset is with Online app headers and coversheet headers/values
		$dataSets[] = $collection;

		$keyVals = array_combine($this->paperAppCsvHeaders, $values);
		// Generate a different MID
		$this->commonCsvData['MID'] = '76411100' . str_shuffle('42405678');
		//Put everything in zeroth array entry which is what is expected
		$collection = [array_merge($keyVals, $this->commonCsvData)];
		//Next dataset use with Paper app headers - No coversheet exists in this case
		$dataSets[] = $collection;
		//save associated referers and resellers
		$this->Merchant->User->AssociatedUser->saveMany([
				[
					'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
					'associated_user_id' => '548bce29-9c0d-42e0-b46c-179151ca54b4',
					'role' => User::ROLE_REFERRER,
					'permission_level' => User::ROLE_REFERRER,
					'user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4',
				],
				[
					'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
					'associated_user_id' => '1d58a4cd-664a-4c08-9b38-94ffc7e61afa',
					'role' => User::ROLE_RESELLER,
					'permission_level' => User::ROLE_RESELLER,
					'user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4',
				]
			]);

		//Make sure all users are active and have roles assigned
		$partnerId = $this->Merchant->User->field('id', ['fullname' => 'The Best Partner Rep']);
		$resId = $this->Merchant->User->field('id', ['fullname' => 'Mr. Resel Resellington']);
		$refId = $this->Merchant->User->field('id', ['fullname' => 'Mr. Refer Referington']);


		$this->Merchant->User->updateAll(
			['active' => 1, 'username' => "'arbitraryusername'"],
			['OR' => [
				['id' => $refId], ['id' => $resId], ['id' => $partnerId]
				]
			]
		);
		$this->Merchant->User->UsersRole->saveMany([
			[
				'role_id' => $this->Merchant->User->Role->field('id', ['name' => User::ROLE_PARTNER]),
				'user_id' => $partnerId
			],
			[
				'role_id' => $this->Merchant->User->Role->field('id', ['name' => User::ROLE_RESELLER]),
				'user_id' => $resId
			],
			[
				'role_id' => $this->Merchant->User->Role->field('id', ['name' => User::ROLE_REFERRER]),
				'user_id' => $refId
			],
		]);

		foreach ($dataSets as $csvDat) {
			$uId = $this->Merchant->User->field('id', ['fullname' => $csvDat[0]['ContractorID']]);
			if (empty($this->Merchant->User->UserCompensationProfile->field('id', ['user_id' => $uId]))) {
				$this->Merchant->User->UserCompensationProfile->create();
				$ucpData = [
					'user_id' => $uId
				];
				$this->Merchant->User->UserCompensationProfile->save($ucpData);
			}
			$preUploadCount = $this->Merchant->find('count');
			$result = $this->Merchant->importMerchantData($csvDat);

			//There should be no errors returned
			$this->assertEmpty(Hash::get($result, "Errors"));
			$expected = $this->Merchant->find('count');
			$this->assertEqual($expected, $preUploadCount + 1);
			$mId = Hash::get($result, '0.Merchant.id');
			//Get all Associated Merchant data that should be created
			$allData = $this->Merchant->find('first', [
					'conditions' => ['Merchant.id' => $mId],
					'contain' => [
						'Address',
						'MerchantBank',
						'MerchantOwner',
						'MerchantUw',
						'MerchantUwVolume',
						'MerchantPricing',
						'MerchantAch.InvoiceItem',
						'MerchantCardType',
						'ProductsAndService',
					]
				]
			);

			// CHECK COMMON DATA ----------------------------------------------------------------
			//Merchant
			$this->assertEqual($uId, $allData['Merchant']['user_id']);
			$this->assertEqual($csvDat[0]['MID'], $allData['Merchant']['merchant_mid']);
			$this->assertEqual($csvDat[0]['DBA'], $allData['Merchant']['merchant_dba']);
			$this->assertEqual($csvDat[0]['EMail'], $allData['Merchant']['merchant_email']);
			$this->assertFalse($allData['Merchant']['womply_merchant_enabled']);

			//Address
			$addsNames = Hash::extract($allData, 'Address.{n}.address_title');
			$addsStreet = Hash::extract($allData, 'Address.{n}.address_street');
			$this->assertContains($csvDat[0]['DBA'], $addsNames);
			$this->assertContains($csvDat[0]['CorpName'], $addsNames);
			$this->assertContains($csvDat[0]['CorpAddress'], $addsStreet);
			$this->assertContains($csvDat[0]['Address'], $addsStreet);
			$this->assertContains($csvDat[0]['BankName'], $addsNames);
			$this->assertContains($csvDat[0]['BankAddress'], $addsStreet);

			//MerchantBank
			$this->assertContains($csvDat[0]['BankName'], $allData['MerchantBank']);
			$this->assertTrue($this->Merchant->isEncrypted($allData['MerchantBank']['bank_routing_number']));
			$this->assertTrue($this->Merchant->isEncrypted($allData['MerchantBank']['bank_dda_number']));
			$this->assertTrue($this->Merchant->isEncrypted($allData['MerchantBank']['fees_routing_number']));
			$this->assertTrue($this->Merchant->isEncrypted($allData['MerchantBank']['fees_dda_number']));

			//MerchantOwner
			$ownName = empty($csvDat[0]['Principal'])? $csvDat[0]['Owner1Name'] : $csvDat[0]['Principal'];
			$this->assertEqual($ownName, $allData['MerchantOwner'][0]['owner_name']);

			//MerchantUw
			$this->assertNotEmpty($allData['MerchantUw']['merchant_id']);
			$this->assertFalse($allData['MerchantUw']['expedited']);

			//MerchantUwVolume
			$pctSwipe = empty($csvDat[0]['Card Present Swiped'])? $csvDat[0]['MethodofSales-CardPresentSwiped'] : $csvDat[0]['Card Present Swiped'];
			$pctConsumer = empty($csvDat[0]['Direct to Consumer'])? $csvDat[0]['%OfProductSold-DirectToCustomer'] : $csvDat[0]['Direct to Consumer'];

			$this->assertEqual($csvDat[0]['VisaMcVol'] * .65, $allData['MerchantUwVolume']['visa_volume']);
			$this->assertEqual($csvDat[0]['VisaMcVol'] * .35, $allData['MerchantUwVolume']['mc_volume']);
			$this->assertEqual($pctSwipe, $allData['MerchantUwVolume']['card_present_swiped']);
			$this->assertEqual($pctConsumer, $allData['MerchantUwVolume']['direct_to_consumer']);
			$this->assertEqual('daily', $allData['MerchantUwVolume']['discount_frequency']);

			//MerchantPricing
			$this->assertNotEmpty($allData['MerchantPricing']['mc_bet_table_id']);
			$this->assertNotEmpty($allData['MerchantPricing']['visa_bet_table_id']);
			$this->assertNotEmpty($allData['MerchantPricing']['ds_bet_table_id']);
			$this->assertNotEmpty($allData['MerchantPricing']['amex_bet_table_id']);
			$this->assertEqual($csvDat[0]['DebitDiscountRate'], $allData['MerchantPricing']['debit_processing_rate']);
			$this->assertEqual($csvDat[0]['EBTStmtFee'], $allData['MerchantPricing']['ebt_access_fee']);

			//MerchantAch
			foreach ($allData['MerchantAch'] as $mAch) {
				$this->assertNotEmpty($mAch['merchant_id']);
				$this->assertEqual($csvDat[0]['CreditEquipmentFee'], ($mAch['ach_amount'] + $mAch['non_taxable_ach_amount']));
				$this->assertEqual('5cdca7f1-8248-4a73-90c9-4bd434627ad4', $mAch['merchant_ach_billing_option_id']);
				$this->assertEqual($csvDat[0]['CreditEquipmentFee'], $mAch['InvoiceItem'][0]['amount']);
				$this->assertTrue($mAch['InvoiceItem'][0]['commissionable']);
			}

			//MerchantCardtype - (all 6 major card types should be present for merchants with mid.length ==16: V MC Ds Amex Debit EBT)
			//else only 4 Ds Amex Debit EBT
			$expectCount = 6;
			$this->assertCount($expectCount, $allData['MerchantCardType']);

			//ProductsAndService at least one product should be added.
			//(acuracy of actual products being added on separate test)
			$this->assertNotEmpty($allData['ProductsAndService'][0]['merchant_id']);

			//If OnlineApp data check Coversheet stuff
			/* Coversheet headers:
				'setup_partner',
				'setup_referrer',
				'setup_reseller',
				'setup_referrer_pct_profit',
				'setup_referrer_pct_volume',
				'setup_referrer_pct_gross',
				'setup_reseller_pct_profit',
				'setup_reseller_pct_volume',
				'setup_reseller_pct_gross',
			*/
			if (array_key_exists('oaID', $csvDat[0])) {
				if (!empty($csvDat[0]['setup_partner'])) {
					$this->assertNotEmpty($allData['Merchant']['partner_id']);
				}
				$expectedPctGross = '80';
				if (!empty($csvDat[0]['setup_referrer'])) {
					$this->assertEqual($expectedPctGross, $allData['Merchant']['ref_p_pct']);
					//setup_referrer_pct_profit
					if (!empty($csvDat[0]['setup_referrer_pct_profit'])) {
						$this->assertEqual(Merchant::PCT_MINUS_GP, $allData['Merchant']['ref_p_type']);
						$this->assertEqual($csvDat[0]['setup_referrer_pct_profit'], $allData['Merchant']['ref_p_value']);
					} elseif (!empty($csvDat[0]['setup_referrer_pct_volume'])) {
						$this->assertEqual(Merchant::POINTS_MINUS_GP, $allData['Merchant']['ref_p_type']);
						$this->assertEqual($csvDat[0]['setup_referrer_pct_volume'], $allData['Merchant']['ref_p_value']);
					}

				}

				if (!empty($csvDat[0]['setup_reseller'])) {
					$this->assertEqual($expectedPctGross, $allData['Merchant']['res_p_pct']);
					//setup_reseller_pct_profit
					if (!empty($csvDat[0]['setup_reseller_pct_profit'])) {
						$this->assertEqual(Merchant::PCT_MINUS_GP, $allData['Merchant']['res_p_type']);
						$this->assertEqual($csvDat[0]['setup_reseller_pct_profit'], $allData['Merchant']['res_p_value']);
					} elseif (!empty($csvDat[0]['setup_reseller_pct_volume'])) {
						$this->assertEqual(Merchant::POINTS_MINUS_GP, $allData['Merchant']['res_p_type']);
						$this->assertEqual($csvDat[0]['setup_reseller_pct_volume'], $allData['Merchant']['res_p_value']);
					}
				}
			}
		}//endforeach
	}

/**
 * Data provider for testImportMerchantData
 * Emulates csv data. Each array element here represents a value for of its corresponding csv header 
 * declared in the header arrays in the initial setup. Here is a descriptive representation of each value:
 *
 * 	'First entry is always blank the key is what matters',
 *	'Owner Type - Sole Prop',
 *	'Owner Type - Corp',	
 *	'Add amex',
 *	'Add Discover',
 *	'Amex Rate Structure',
 *	'Amex Qualified Exemptions',
 *	'Card Present Swiped percentage',
 *	'Direct to Consumer percentage',
 *	'Name of Main Company owner',
 *	'Discount paod Daily?',
 *
 * @return array
 */
	public function getImportWithOnlineAppData() {
		return [
			"import with partner" => [
				//Emulated csv values
				[
					null,
					'Yes',
					'Off',
					'55555ArbitraryAmexNumber555555', //add amex
					'Yes', //add Discover
					'Cost Plus', //Rate Structure
					'American Express Cost Plus .05%', //Downgrades
					'100',
					'100',
					'Owner-name-1',
					'On',
				],
				//Coversheet values
				[
					//Partner name
					'The Best Partner Rep',
					//Referrer
					'',
					//Reseller
					'',
					//setup_referrer_pct_profit
					'',
					//setup_referrer_pct_volume
					'',
					//setup_referrer_pct_gross
					'',
					//setup_reseller_pct_profit
					'',
					//setup_reseller_pct_volume
					'',
					//setup_reseller_pct_gross
					'',
				]
			],
			"import with partner & referer with pct_profit" => [
				//Emulated csv values
				[
					null,
					'Yes',
					'Off',
					'55555ArbitraryAmexNumber555555', //add amex
					'Yes', //add Discover
					'Cost Plus', //Rate Structure
					'American Express Cost Plus .05%', //Downgrades
					'100',
					'100',
					'Owner-name-1',
					'On',
				],
				//Coversheet values
				[
					//Partner name
					'The Best Partner Rep',
					//Referrer
					'Mr. Refer Referington',
					//Reseller
					'',
					//setup_referrer_pct_profit
					'10',
					//setup_referrer_pct_volume
					'',
					//setup_referrer_pct_gross
					'80',
					//setup_reseller_pct_profit
					'',
					//setup_reseller_pct_volume
					'',
					//setup_reseller_pct_gross
					'',
				]
			],
			"import with partner & referer with pct_volume" => [
				//Emulated csv values
				[
					null,
					'Yes',
					'Off',
					'55555ArbitraryAmexNumber555555', //add amex
					'Yes', //add Discover
					'Cost Plus', //Rate Structure
					'American Express Cost Plus .05%', //Downgrades
					'100',
					'100',
					'Owner-name-1',
					'On',
				],
				//Coversheet values
				[
					//Partner name
					'The Best Partner Rep',
					//Referrer
					'Mr. Refer Referington',
					//Reseller
					'',
					//setup_referrer_pct_profit
					'',
					//setup_referrer_pct_volume
					'50',
					//setup_referrer_pct_gross
					'80',
					//setup_reseller_pct_profit
					'',
					//setup_reseller_pct_volume
					'',
					//setup_reseller_pct_gross
					'',
				]
			],
			"import with partner, reseller pct_profit" => [
				//Emulated csv values
				[
					null,
					'Yes',
					'Off',
					'55555ArbitraryAmexNumber555555', //add amex
					'Yes', //add Discover
					'Cost Plus', //Rate Structure
					'American Express Cost Plus .05%', //Downgrades
					'100',
					'100',
					'Owner-name-1',
					'On',
				],
				//Coversheet values
				[
					//Partner name
					'The Best Partner Rep',
					//Referrer
					'',
					//Reseller
					'Mr. Resel Resellington',
					//setup_referrer_pct_profit
					'',
					//setup_referrer_pct_volume
					'',
					//setup_referrer_pct_gross
					'',
					//setup_reseller_pct_profit
					'20',
					//setup_reseller_pct_volume
					'',
					//setup_reseller_pct_gross
					'80',
				]
			],
			"import with partner, reseller pct_volume" => [
				//Emulated csv values
				[
					null,
					'Yes',
					'Off',
					'55555ArbitraryAmexNumber555555', //add amex
					'Yes', //add Discover
					'Cost Plus', //Rate Structure
					'American Express Cost Plus .05%', //Downgrades
					'100',
					'100',
					'Owner-name-1',
					'On',
				],
				//Coversheet values
				[
					//Partner id instead of name
					'113114ae-8888-8888-8888-fa0c0f25c786', //(The Best Partner Rep),
					//Referrer
					'',
					//Reseller
					'Mr. Resel Resellington',
					//setup_referrer_pct_profit
					'',
					//setup_referrer_pct_volume
					'',
					//setup_referrer_pct_gross
					'',
					//setup_reseller_pct_profit
					'',
					//setup_reseller_pct_volume
					'40',
					//setup_reseller_pct_gross
					'80',
				]
			],
		];
	}
/**
 * testImportMerchantDataCsvDataErrors method
 *
 * @covers Merchant::importMerchantData
 * @return void
 */
	public function testImportMerchantDataCsvDataErrors() {
		$testData = [
			[
				'ContractorID' => 'Mark Weatherford',
				'MID' => '3948906204000946',
				'DBA' => '16 Hands',
			],
		];
		$repId = $this->Merchant->User->field('id', ['user_first_name' => 'Mark', 'user_last_name' => 'Weatherford']);

		$csvData = $testData;
		$csvData[0]['ContractorID'] = null;
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertTextContains("Rep '' not found in database for {$testData[0]['MID']} {$testData[0]['DBA']}", Hash::extract($result, 'Errors'));

		$csvData = $testData;
		$csvData[0]['MID'] = null;
		$this->Merchant->User->UserCompensationProfile->deleteAll(['user_id' => $repId]);
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertTextContains($testData[0]['DBA'] . ' Merchant is missing MID number!', Hash::extract($result, 'Errors'));
		$this->assertTextContains($testData[0]['ContractorID'] . ' Contractor does not have a User Comp Profile', Hash::extract($result, 'Errors'));

		$this->Merchant->User->UserCompensationProfile->create();
		$ucpData = [
			'user_id' => $this->Merchant->User->field('id', ['fullname' => $testData[0]['ContractorID']])
			];
		$this->Merchant->User->UserCompensationProfile->save($ucpData);

		$csvData[0]['MID'] = 'merchant-dont.exist';
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertNotContains($testData[0]['ContractorID'] . ' Contractor does not have a User Comp Profile', Hash::extract($result, 'Errors'));
		$this->assertTextContains('Truncated MID as scientific notation is not allowed: ' . $csvData[0]['MID'] . ' ' . $testData[0]['DBA'], Hash::extract($result, 'Errors'));

		$csvData = $testData;
		$csvData[0]['setup_partner'] = "A Partner";
		$result = $this->Merchant->importMerchantData($csvData);
		$msg = 'The Partner A Partner in this data import does not have an active user profile setup.';
		$this->assertTextContains($msg, Hash::extract($result, 'Errors'));
	}
/**
 * testImportDataCsvWithGatewayAndPaymentFusion method
 *
 * @covers Merchant::importMerchantData
 * @return void
 */
	public function testImportDataCsvWithGatewayAndPaymentFusion() {
		$testData = [
			[
				'ContractorID' => 'Bill McAbee',
				'MID' => '3948906204000946',
				'DBA' => '16 Hands',
				'Gateway' => 'yes',
				'Gateway_Name' => 'MerchantLink',
				'Gateway_ID' => '1234567890',
				'PaymentFusion' => 'Yes',
				'PF_Feature' => 'VP2PE',
				'PF_ID' => '987654'
			],
		];
		$repId = $this->Merchant->User->field('id', ['user_first_name' => 'Bill', 'user_last_name' => 'McAbee']);
		$roleId = $this->Merchant->User->Role->field('id', ['name' => User::ROLE_REP]);
		$this->Merchant->User->UsersRole->updateAll(
				['role_id' => "'$roleId'"],
				['user_id' => $repId]
			);
		$csvData = $testData;
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertNotEmpty(Hash::get($result, '0.Merchant.id'));
		$this->assertSame('3948906204000946', Hash::get($result, '0.Merchant.merchant_mid'));

		// Test errors are returned
		$csvData[0]['Gateway_Name'] = 'No way gateway';
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertTextContains('Gateway Name specified in the CSV file does not match any existing enabled gateways.', Hash::extract($result, 'Errors'));

		$csvData = $testData;
		$csvData[0]['Gateway_ID'] = '1.2368E+18';
		$csvData[0]['PF_ID'] = '567894';
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertTextContains('Gateway ID in the CSV file is formatted incorrectly or is invalid: ' . $csvData[0]['Gateway_ID'], Hash::extract($result, 'Errors'));

		$csvData = $testData;
		$csvData[0]['Gateway_ID'] = '';
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertTextContains('Gateway ID in the CSV file cannot be blank.', Hash::extract($result, 'Errors'));

		$csvData = $testData;
		$csvData[0]['Gateway_ID'] = '456987';
		$csvData[0]['PF_ID'] = '951357';
		$csvData[0]['PF_Feature'] = 'Fake Feature Foo';
		$result = $this->Merchant->importMerchantData($csvData);
		$this->assertTextContains('The value specified under PF_Feature in the CSV file does not match any Product Features.', Hash::extract($result, 'Errors'));
	}

/**
 * testBeforeSave method
 *
 * @covers Merchant::beforeValidate
 * @return void
 */
	public function testBeforeValidate() {
		$merchantId = '00000000-0000-0000-0000-000000000004';
		$merchant = $this->Merchant->find('first', [
			'conditions' => ['Merchant.id' => $merchantId]
		]);

		$merchant['Merchant']['partner_id'] = CakeText::uuid();
		$this->assertFalse($this->Merchant->save($merchant['Merchant']));

		$errors = "A compensation profile associating the specified Rep with the Partner was not found.";
		$this->assertEqual($errors, Hash::get($this->Merchant->validationErrors, 'partner_id.0'));

		unset($merchant['Merchant']['partner_id']);
		$merchant['Merchant']['sm_user_id'] = CakeText::uuid();
		$this->assertFalse($this->Merchant->save($merchant['Merchant']));

		$errors = "Selected 'SM' is not associated with the Rep with 'SM' permission level in Rep's default comp profile!";
		$this->assertEqual(Hash::get($this->Merchant->validationErrors, 'sm_user_id.0'), $errors);

		unset($merchant['Merchant']['sm_user_id']);
		$merchant['Merchant']['sm2_user_id'] = CakeText::uuid();
		$this->assertFalse($this->Merchant->save($merchant['Merchant']));

		$errors = "Selected 'SM2' is not associated with the Rep with 'SM2' permission level in Rep's default comp profile!";
		$this->assertEqual(Hash::get($this->Merchant->validationErrors, 'sm2_user_id.0'), $errors);

		unset($merchant['Merchant']['sm2_user_id']);
		$merchant['Merchant']['referer_id'] = CakeText::uuid();
		$this->assertFalse($this->Merchant->save($merchant['Merchant']));

		$errors = "Selected 'Referrer' is not associated with the Rep with 'Referrer' permission level in Rep's default comp profile!";
		$this->assertEqual(Hash::get($this->Merchant->validationErrors, 'referer_id.0'), $errors);

		unset($merchant['Merchant']['referer_id']);
		$merchant['Merchant']['reseller_id'] = CakeText::uuid();
		$this->assertFalse($this->Merchant->save($merchant['Merchant']));

		$errors = "Selected 'Reseller' is not associated with the Rep with 'Reseller' permission level in Rep's default comp profile!";
		$this->assertEqual(Hash::get($this->Merchant->validationErrors, 'reseller_id.0'), $errors);
	}

/**
 * testGetSummaryMerchantData method
 *
 * @covers Merchant::getSummaryMerchantData
 * @return void
 */
	public function testGetSummaryMerchantData() {
		$expected = [
			'Merchant' => [
				'merchant_dba' => 'Another Merchant',
				'merchant_mid' => '3948000030003049',
				'id' => '00000000-0000-0000-0000-000000000003',
				'user_id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
			],
			'User' => [
				'user_first_name' => 'Slim',
				'user_last_name' => 'Pickins',
				'id' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
			]
		];
		$this->assertEquals($expected, $this->Merchant->getSummaryMerchantData('00000000-0000-0000-0000-000000000003'));
	}

/**
 * testGetListByProductId method
 *
 * @covers Merchant::getListByProductId
 * @return void
 */
	public function testGetListByProductId() {
		$productTypeId = '952e7099-42b7-48d0-909b-6cb09d4d6706';
		$expected = [
			'4e3587be-aafb-48c4-9b6b-8dd26b8e94aa' => 'Refer Aloha, LLC',
			'00000000-0000-0000-0000-000000000003' => 'Another Merchant',
		];
		$result = $this->Merchant->getListByProductId($productTypeId, null, 1);
		$this->assertEquals($expected, $result);
		$this->assertEquals('Refer Aloha, LLC', reset($result));

		$result = $this->Merchant->getListByProductId($productTypeId, true);
		$expected['00000000-0000-0000-0000-000000000005'] = 'Inactive merchant';
		$this->assertEquals($expected, $result);
		$this->assertEquals('Another Merchant', reset($result));

		$expected = ['00000000-0000-0000-0000-000000000005' => 'Inactive merchant'];
		$result = $this->Merchant->getListByProductId($productTypeId, true, 0);
		$this->assertEquals($expected, $result);

		$expected = [
			'4e3587be-aafb-48c4-9b6b-8dd26b8e94aa' => 'Refer Aloha, LLC',
			'00000000-0000-0000-0000-000000000003' => 'Another Merchant',
		];
		$result = $this->Merchant->getListByProductId($productTypeId, true, 1);
		$this->assertEquals($expected, $result);

		$this->assertEmpty($this->Merchant->getListByProductId('f446b74f-ae19-4505-82c7-67cf93fcfe3d'));
	}

/**
 * testGetListByProductId method
 *
 * @expectedException InvalidArgumentException
 * @covers Merchant::getListByProductId
 * @return void
 */
	public function testGetListByProductIdInvalid() {
		$this->Merchant->getListByProductId('f446b74f-ae19-4505-82c7-67cf93fcfe3d', false, 'not-valid-value');
	}

/**
 * testGetNextMod10Num_InvalidArgumentException method
 *
 * @expectedException InvalidArgumentException
 * @covers Merchant::getNextMod10Num
 * @return void
 */
	public function testGetNextMod10NumInvalidArgumentException() {
		//The following should all throw an exception
		//Integer
		$this->Merchant->getNextMod10Num(123);
		//Negative integer
		$this->Merchant->getNextMod10Num(-123);
		//Negative string integer
		$this->Merchant->getNextMod10Num('-123');
		//Floating point string number
		$this->Merchant->getNextMod10Num("123.5");
		//Floating point number
		$this->Merchant->getNextMod10Num(123.5);
	}

/**
 * testGetNextMod10Num method
 *
 * @param string $strNum String representation of a natural number
 * @param int $amount an integer indicating the amount of mod10 numbers to generate
 * @param array $expected Expected result
 * @dataProvider getNextMod10NumData
 * @covers Merchant::getNextMod10Num
 * @return void
 */
	public function testGetNextMod10Num($strNum, $amount, $expected) {
		$actual = $this->Merchant->getNextMod10Num($strNum, $amount);
		$this->assertEquals($expected, $actual);
	}

/**
 * Data provider for testGetNextMod10Num
 *
 * @return array
 */
	public function getNextMod10NumData() {
		return [
			['1', 50, [
					'18',
					'26',
					'34',
					'42',
					'59',
					'67',
					'75',
					'83',
					'91',
					'109',
					'117',
					'125',
					'133',
					'141',
					'158',
					'166',
					'174',
					'182',
					'190',
					'208',
					'216',
					'224',
					'232',
					'240',
					'257',
					'265',
					'273',
					'281',
					'299',
					'307',
					'315',
					'323',
					'331',
					'349',
					'356',
					'364',
					'372',
					'380',
					'398',
					'406',
					'414',
					'422',
					'430',
					'448',
					'455',
					'463',
					'471',
					'489',
					'497',
					'505'
				]
			],
			["7641110042404240", 50, [
					'7641110042404241',
					'7641110042404258',
					'7641110042404266',
					'7641110042404274',
					'7641110042404282',
					'7641110042404290',
					'7641110042404308',
					'7641110042404316',
					'7641110042404324',
					'7641110042404332',
					'7641110042404340',
					'7641110042404357',
					'7641110042404365',
					'7641110042404373',
					'7641110042404381',
					'7641110042404399',
					'7641110042404407',
					'7641110042404415',
					'7641110042404423',
					'7641110042404431',
					'7641110042404449',
					'7641110042404456',
					'7641110042404464',
					'7641110042404472',
					'7641110042404480',
					'7641110042404498',
					'7641110042404506',
					'7641110042404514',
					'7641110042404522',
					'7641110042404530',
					'7641110042404548',
					'7641110042404555',
					'7641110042404563',
					'7641110042404571',
					'7641110042404589',
					'7641110042404597',
					'7641110042404605',
					'7641110042404613',
					'7641110042404621',
					'7641110042404639',
					'7641110042404647',
					'7641110042404654',
					'7641110042404662',
					'7641110042404670',
					'7641110042404688',
					'7641110042404696',
					'7641110042404704',
					'7641110042404712',
					'7641110042404720',
					'7641110042404738'
				]
			],
			["3948000030004060", 50, [
					'3948000030004062',
					'3948000030004070',
					'3948000030004088',
					'3948000030004096',
					'3948000030004104',
					'3948000030004112',
					'3948000030004120',
					'3948000030004138',
					'3948000030004146',
					'3948000030004153',
					'3948000030004161',
					'3948000030004179',
					'3948000030004187',
					'3948000030004195',
					'3948000030004203',
					'3948000030004211',
					'3948000030004229',
					'3948000030004237',
					'3948000030004245',
					'3948000030004252',
					'3948000030004260',
					'3948000030004278',
					'3948000030004286',
					'3948000030004294',
					'3948000030004302',
					'3948000030004310',
					'3948000030004328',
					'3948000030004336',
					'3948000030004344',
					'3948000030004351',
					'3948000030004369',
					'3948000030004377',
					'3948000030004385',
					'3948000030004393',
					'3948000030004401',
					'3948000030004419',
					'3948000030004427',
					'3948000030004435',
					'3948000030004443',
					'3948000030004450',
					'3948000030004468',
					'3948000030004476',
					'3948000030004484',
					'3948000030004492',
					'3948000030004500',
					'3948000030004518',
					'3948000030004526',
					'3948000030004534',
					'3948000030004542',
					'3948000030004559'
				]
			],

		];
	}
/**
 * testHasDiscover
 *
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param boolean $expected Expected boolean value indicating whether discover is there
 * @return void
 * @covers Merchant::_hasDiscover()
 * @dataProvider providerTestHasDiscover
 */
	public function testHasDiscover($csvDataArr, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_hasDiscover');
		$method->setAccessible(true);

		$actual = $method->invokeArgs($this->Merchant, [&$csvDataArr]);
		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testHasDiscover
 *
 * @dataProvider
 * @return void
 */
	public function providerTestHasDiscover() {
		return [
			[["Discover" => "Nope"], false],
			[["DoYouWantToAcceptDisc-New" => "On"], true],
			[["DoYouWantToAcceptDisc-NotNew" => "On"], true],
		];
	}
/**
 * testHasAmex
 *
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param boolean $expected Expected boolean value indicating whether discover is there
 * @return void
 * @covers Merchant::_hasAmex()
 * @dataProvider providerTestHasAmex
 */
	public function testHasAmex($csvDataArr, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_hasAmex');
		$method->setAccessible(true);

		$actual = $method->invokeArgs($this->Merchant, [&$csvDataArr]);

		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testHasAmex
 *
 * @dataProvider
 * @return void
 */
	public function providerTestHasAmex() {
		return [
			[["AENew" => "Nope"], false],
			[["AmexNum" => "123456789"], true],
			[["DoYouWantToAcceptAE-New" => "On"], true],
		];
	}
/**
 * testHasDebit
 *
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param boolean $expected Expected boolean value indicating whether discover is there
 * @return void
 * @covers Merchant::_hasDebit()
 * @dataProvider providerTestHasDebit
 */
	public function testHasDebit($csvDataArr, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_hasDebit');
		$method->setAccessible(true);

		$actual = $method->invokeArgs($this->Merchant, [&$csvDataArr]);
		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testHasDebit
 *
 * @dataProvider
 * @return void
 */
	public function providerTestHasDebit() {
		return [
			[["DebitPerItem" => "0", "DebitDiscountRate" => "0", "DebitMonthlyAccessFee" => "0"], false],
			[["DebitPerItem" => "1", "DebitDiscountRate" => "0", "DebitMonthlyAccessFee" => "0"], true],
			[["DebitTranFee" => "0", "DebitDiscountRate" => "1", "DebitMonthlyAccessFee" => "0"], true],
		];
	}
/**
 * testHasEbt
 *
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param boolean $expected Expected boolean value indicating whether discover is there
 * @return void
 * @covers Merchant::_hasEbt()
 * @dataProvider providerTestHasEbt
 */
	public function testHasEbt($csvDataArr, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_hasEbt');
		$method->setAccessible(true);

		$actual = $method->invokeArgs($this->Merchant, [&$csvDataArr]);
		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testHasEbt
 *
 * @dataProvider
 * @return void
 */
	public function providerTestHasEbt() {
		return [
			[["EBTStmtFee" => "0", "EBTTranFee" => "0", "EBTDiscRate" => "0"], false],
			[["EBTStmtFee" => "1", "EBTTranFee" => "0", "EBTDiscRate" => "0"], true],
			[["EBTAccess" => "0", "EBTTranFee" => "1", "EBTDiscRate" => "0"], true],
		];
	}
/**
 * testSetMerchantCardTypes
 *
 * @param array $merchData representation of data being generated by Merchant upload method
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param boolean $isOnlneApp whether the csvData comes from an online app
 * @param array $expected Expected result
 * @return void
 * @covers Merchant::_setMerchantCardTypes()
 * @dataProvider providerTestSetMerchantCardTypes
 */
	public function testSetMerchantCardTypes($merchData, $csvDataArr, $isOnlneApp, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_setMerchantCardTypes');
		$method->setAccessible(true);

		$method->invokeArgs($this->Merchant, [&$merchData, &$csvDataArr, $isOnlneApp]);
		$actual["MerchantCardType"] = $merchData["MerchantCardType"];
		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testSetMerchantCardTypes
 *
 * @dataProvider
 * @return void
 */
	public function providerTestSetMerchantCardTypes() {
		return [
				//online app fields
				[
					[ "Merchant" => ['merchant_mid' => '1234567890123456'] ], [], true, [
						"MerchantCardType" => [
							["card_type_id" => 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4'], //V
							["card_type_id" => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a'], //MC
							["card_type_id" => 'd82f4282-f816-4880-975c-53d42a7f02bc'], //Discover
					]]
				],
				//online app fields
				[
					[ "Merchant" => ['merchant_mid' => '1234567890123456'] ],
					["DoYouWantToAcceptAE-New" => "On"], true, [
						"MerchantCardType" => [
							["card_type_id" => 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4'], //V
							["card_type_id" => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a'], //MC
							["card_type_id" => 'd82f4282-f816-4880-975c-53d42a7f02bc'], //Discover
						["card_type_id" => 'ddea093f-ab15-44f6-87ba-fb4c4235ced1'], //Amex
					]]
				],
				//non-online app fields
				[
					[ "Merchant" => ['merchant_mid' => '7890123456']],
					["Discover" => "Yes", "AENew" => "On", "DebitPerItem" => "10", "EBTStmtFee" => "10"], false, [
						"MerchantCardType" => [
							["card_type_id" => 'd82f4282-f816-4880-975c-53d42a7f02bc'], //Discover
							["card_type_id" => 'ddea093f-ab15-44f6-87ba-fb4c4235ced1'], //Amex
							["card_type_id" => '5581dfa8-8004-4b13-8448-093534627ad4'], //Debit
							["card_type_id" => '5581dfa8-7788-4db5-b1c5-093534627ad4'], //EBT
					]]
				],
				//non-online app fields
				[
					[ "Merchant" => ['merchant_mid' => '1234567890123456'] ],
					["Discover" => "Yes", "AENew" => "Off", "DebitPerItem" => "0", "EBTStmtFee" => "0"], false, [
						"MerchantCardType" => [
							["card_type_id" => 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4'], //V
							["card_type_id" => 'd3216cb3-ee71-40c6-bede-ac9818e24f3a'], //MC
							["card_type_id" => 'd82f4282-f816-4880-975c-53d42a7f02bc'], //Only Discover
					]]
				],
			];
	}

/**
 * testGetBusLvl
 *
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param string $expected Expected string value indicating business level
 * @return void
 * @covers Merchant::_getBusLvl()
 * @dataProvider providerTestGetBusLvl
 */
	public function testGetBusLvl($csvDataArr, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_getBusLvl');
		$method->setAccessible(true);

		$actual = $method->invokeArgs($this->Merchant, [&$csvDataArr]);
		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testGetBusLvl
 *
 * @dataProvider
 * @return void
 */
	public function providerTestGetBusLvl() {
		return [
			[["BusinessType-Retail" => "Off", "Retail" => "Off"], null],
			[["BusinessType-Retail" => "Yes", "Retail" => "Sure"], Merchant::B_LVL_RETAIL],
			[["BusinessType-Retail" => "On"], Merchant::B_LVL_RETAIL],
			[["Retail" => "On"], Merchant::B_LVL_RETAIL],
			[["Retail" => "TRUE"], Merchant::B_LVL_RETAIL],
			[["BusinessType-Restaurant" => "On"], Merchant::B_LVL_RESTAURANT],
			[["Restaurant" => "On"], Merchant::B_LVL_RESTAURANT],
			[["Restaurant" => "TRUE"], Merchant::B_LVL_RESTAURANT],
			[["BusinessType-Lodging" => "On"], Merchant::B_LVL_LODGE],
			[["Lodging" => "On"], Merchant::B_LVL_LODGE],
			[["Lodging" => "TRUE"], Merchant::B_LVL_LODGE],
			[["BusinessType-MOTO" => "On"], Merchant::B_LVL_MOTO],
			[["MOTO" => "On"], Merchant::B_LVL_MOTO],
			[["MOTO" => "TRUE"], Merchant::B_LVL_MOTO],
			[["BusinessType-Internet" => "On"], Merchant::B_LVL_NET],
			[["Internet" => "On"], Merchant::B_LVL_NET],
			[["Internet" => "TRUE"], Merchant::B_LVL_NET],
			[["BusinessType-Grocery" => "On"], Merchant::B_LVL_GROCERY],
			[["Grocery" => "On"], Merchant::B_LVL_GROCERY],
			[["Grocery" => "TRUE"], Merchant::B_LVL_GROCERY],
		];
	}

/**
 * _setVisaMcDsAmexBets
 *
 * @param array $merchData representation of data being generated by Merchant upload method
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param array $expected Expected result
 * @return void
 * @covers Merchant::_setVisaMcDsAmexBets()
 * @dataProvider providerTestSetVisaMcDsAmexBets
 */
	public function testSetVisaMcDsAmexBets($merchData, $csvDataArr, $expected) {
		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_setVisaMcDsAmexBets');
		$method->setAccessible(true);

		$method->invokeArgs($this->Merchant, [&$merchData, &$csvDataArr]);
		$actual["MerchantPricing"] = $merchData["MerchantPricing"];

		$this->AssertEquals($expected, $actual);
	}

/**
 * Provider for testSetVisaMcDsAmexBets
 *
 * @dataProvider
 * @return void
 */
	public function providerTestSetVisaMcDsAmexBets() {
		return [
				[
					[/*Set empty initial Merchant data*/],
					//no reate structure
					['Rate Structure' => '', 'Downgrades' => ''],
					//expected
					["MerchantPricing" =>
						[
							"mc_bet_table_id" => null,
							"visa_bet_table_id" => null,
							"ds_bet_table_id" => null,
							"amex_bet_table_id" => null,
						]
					]
				],
				[
					[/*Set empty initial Merchant data*/],
					['Rate Structure' => 'Pass Thru', 'Downgrades' => 'Visa/MasterCard/Discover Interchange at Pass Thru'],
					//expected
					["MerchantPricing" =>
						[
							"mc_bet_table_id" => '558ade3b-1da4-43a1-960d-20ba34627ad4',
							"visa_bet_table_id" => '5555555b-4444-3333-960d-20ba34627ad4',
							"ds_bet_table_id" => null,
							"amex_bet_table_id" => null,
						]
					]
				],
				[
					[/*Set empty initial Merchant data*/],
					['Rate Structure' => 'Cost Plus', 'Downgrades' => 'Visa/MasterCard/Discover Cost Plus .05%'],
					//expected
					["MerchantPricing" =>
						[
							"mc_bet_table_id" => '558ade3b-0a28-4c31-82a1-20ba34627ad4',
							"visa_bet_table_id" => '558ade3c-d180-423c-a50d-20ba34627ad4',
							"ds_bet_table_id" => '484f88f8-c3ac-48db-a967-af612c1772fd',
							"amex_bet_table_id" => null,
						]
					]
				],
				[
					[/*Set empty initial Merchant data*/],
					//online app csv fields for amex
					['Amex Rate Structure' => 'Cost Plus', 'Amex Downgrades' => 'American Express Cost Plus .05%'],
					//expected
					["MerchantPricing" =>
						[
							"mc_bet_table_id" => null,
							"visa_bet_table_id" => null,
							"ds_bet_table_id" => null,
							"amex_bet_table_id" => '558ade3b-9990-4f05-bfed-20ba34627ad4',
						]
					]
				],
				[
					[/*Set empty initial Merchant data*/],
					['Amex Rate Structure' => 'Cost Plus',
					'Amex Downgrades' => 'American Express Cost Plus .05%',
					'Rate Structure' => 'Cost Plus',
					'Downgrades' => 'Visa/MasterCard/Discover Cost Plus .05%'
					],
					//Expect all fields should populate
					["MerchantPricing" =>
						[
							"mc_bet_table_id" => '558ade3b-0a28-4c31-82a1-20ba34627ad4',
							"visa_bet_table_id" => '558ade3c-d180-423c-a50d-20ba34627ad4',
							"ds_bet_table_id" => '484f88f8-c3ac-48db-a967-af612c1772fd',
							"amex_bet_table_id" => '558ade3b-9990-4f05-bfed-20ba34627ad4',
						]
					]
				],
			];
	}

/**
 * testSetMerchantProducts
 *
 * @param array $csvDataArr representation of data array extracted from a csv string
 * @param array $merchData representation of data being generated by Merchant upload method
 * @param string $repUcpId a user compensation profile id
 * @param boolean $enableProducs whether the enable products for test
 * @param boolean $isOnlneApp whether the csvData comes from an online app
 * @param array $expected Expected result
 * @return void
 * @covers Merchant::_setMerchantProducts()
 * @dataProvider providerTestSetMerchantProducts
 */
	public function testSetMerchantProducts($csvDataArr, $merchData, $repUcpId, $enableProducs, $isOnlneApp, $expected) {
		//save user parameters products
		if (empty($expected)) {
			$prodsToEnable = [["products_services_type_id" => '551038c2-2220-4a58-b8d8-1a3f34627ad4']];
		} else {
			$prodsToEnable = $expected;
		}
		if (!$this->_enableProducts(Hash::extract($prodsToEnable, '{n}.products_services_type_id'), $repUcpId, $enableProducs)) {
			$this->fail("Unable to save test data in UserParameters");
		}

		$reflection = new ReflectionClass('Merchant');
		$method = $reflection->getMethod('_setMerchantProducts');
		$method->setAccessible(true);

		$method->invokeArgs($this->Merchant, [&$csvDataArr, &$merchData, $repUcpId, $isOnlneApp]);
		$actual = Hash::extract($merchData, 'ProductsAndService.{n}.products_services_type_id');

		foreach (Hash::extract($expected, '{n}.products_services_type_id') as $prodId) {
			$this->assertContains($prodId, $actual);
		}

		if (array_key_exists('Term1-IP', $csvDataArr)) {
			//Check that when merchant is IP only Non Dial products are present and not the Dial ones
			if (in_array(Hash::get($csvDataArr, 'Term1-IP'), $this->Merchant->csvAffirm)) {
				//Find any products returned in actual that are Dial
				$countDial = $this->Merchant->ProductsAndService->ProductsServicesType->find('count', [
					'conditions' => [
						'id' => $actual,
						'is_active' => true,
						"products_services_description NOT like '%Non Dial%'",
						"products_services_description like '%Dial%'"
					]
				]);
				//Find any products returned in actual that are Non Dial
				$countNonDial = $this->Merchant->ProductsAndService->ProductsServicesType->find('count', [
					'conditions' => [
						'id' => $actual,
						'is_active' => true,
						"products_services_description like '%Non Dial%'",
					]
				]);
				//Should be at least one for Amex, visa and Mc NonDial
				$this->assertGreaterThanOrEqual(1, $countNonDial);
				//Should always be zero
				$this->assertEqual(0, $countDial);

			} else {	//CHECK THE VICEVERSA
				//Find any products returned in actual that are Dial
				$countDial = $this->Merchant->ProductsAndService->ProductsServicesType->find('count', [
					'conditions' => [
						'id' => $actual,
						'is_active' => true,
						"products_services_description NOT like '%Non Dial%'",
						"products_services_description like '%Dial%'"
					]
				]);
				//Find any products returned in actual that are Non Dial
				$countNonDial = $this->Merchant->ProductsAndService->ProductsServicesType->find('count', [
					'conditions' => [
						'id' => $actual,
						'is_active' => true,
						"products_services_description like '%Non Dial%'",
					]
				]);
				//Should be at least one for Amex, visa and Mc Dial
				$this->assertGreaterThanOrEqual(1, $countDial);
				//Should always be zero
				$this->assertEqual(0, $countNonDial);
			}
		}
		$discount = $this->Merchant->ProductsAndService->ProductsServicesType->find('list', [
			'conditions' => ['is_active' => true, "products_services_description like '%Discount'"],
			'fields' => ['id'],
		]);
		$flatRate = $this->Merchant->ProductsAndService->ProductsServicesType->find('list', [
			'conditions' => ['is_active' => true, "products_services_description like '%Flat Rate'"],
			'fields' => ['id'],
		]);
		//there should be no flat rate products when Discount
		if (in_array('Discount', Hash::extract($merchData, 'MerchantPricing'))) {
			foreach ($flatRate as $frPrId) {
				$this->assertNotContains($frPrId, $actual);
			}
		}
	}

/**
 * Provider for testSetMerchantProducts
 *
 * @dataProvider
 * @return void
 */
	public function providerTestSetMerchantProducts() {
		return [
			"products always added" => [	//First data set intent:
					//Check only default products that always are added on upload are added
					['Arbitrary' => 'CSV data', 'MID' => '12345'],
					['Merchant' => ['id' => "cd69c94c-fake-fake-fake-af71e72757a2", 'merchant_mid' => '12345', 'user_id' => AxiaTestCase::USER_REP_ID]],
					'5570e7fe-5a64-4a93-8724-337a34627ad4',
					false, //do not enable Mark Weatherford's UCP products
					false,
					[
						//no products since this is considered a non-acquiring merchant
					]
				],
			"Online app Add V Mc flat rate" => [	//Data set intent:
					//Online app Add V Mc only with corresponding FLAT RATE products but exclude Dial and Non Dial products
					['Arbitrary' => 'CSV data', 'MID' => '1234567890123456'],
					[
						'Merchant' => ['id' => "cd69c94c-fake-fake-fake-af71e72757a2", 'merchant_mid' => '1234567890123456', 'user_id' => AxiaTestCase::USER_REP_ID],
						'MerchantPricing' => [
								//Flat rate bet_table ids for visa/Mc
								'FlatRate',
								'visa_bet_table_id' => 'f52ecb82-dc5e-4122-898d-256f5da32f46',
								'mc_bet_table_id' => 'd901273e-38f5-47ec-9bf3-1a684d0c3310',
							]
					],
					'5570e7fe-5a64-4a93-8724-337a34627ad4', //no products enabled for Mark Weatherford's user compensation profile
					true,
					true,
					[
						//expect all visa/Mc products with corresponding FLAT RATE products
						["products_services_type_id" => '551038c2-2220-4a58-b8d8-1a3f34627ad4'], //Credit Mothly
					]
				],
			"Online app Add V Mc Discount Exclude Flat Rate include Non Dial" => [	//Data set intent:
					//Online app Add visa and mastercard only with corresponding Discount products exclude Flat Rate include Non Dial products 'Term1-IP' => 'On'
					['Arbitrary' => 'CSV data', 'MID' => '1234567890123456', 'Term1-IP' => 'On'],
					[
						'Merchant' => ['id' => "cd69c94c-fake-fake-fake-af71e72757a2", 'merchant_mid' => '1234567890123456', 'user_id' => AxiaTestCase::USER_REP_ID],
						'MerchantPricing' => [
								//Dicount Rate Structure any non flat rate bet_table id
								'Discount',
								'visa_bet_table_id' => '56df682f-2400-41bd-974f-078234627ad4',
								'mc_bet_table_id' => '56df682f-9514-4fb5-b656-078234627ad4',
							]
					],
					'5570e7fe-5a64-4a93-8724-337a34627ad4',
					true, //products enabled for Mark Weatherford's user compensation profile
					true,
					[
						//expect all visa/Mc products with corresponding FLAT RATE products
						["products_services_type_id" => '551038c2-2220-4a58-b8d8-1a3f34627ad4'], //Credit Mothly
						["products_services_type_id" => '624250b2-55ae-4423-b2e9-95c837943115'], //'Visa Non Dial Sales',
						["products_services_type_id" => 'ec5b6684-6f5c-4881-b26c-5a8233bde091'], //'Visa Discount',
						["products_services_type_id" => 'fdafb816-4a0c-4e81-84df-96d794041372'], //'MasterCard Non Dial Sales',
					]
				],
			"Online app Add V Mc Discount Exclude Flat Rate include Dial" => [	//Data set intent:
					//Online app Add visa and mastercard only with corresponding Discount products exclude Flat Rate include Dial products 'Term1-IP' => 'Off'
					['Arbitrary' => 'CSV data', 'MID' => '1234567890123456', 'Term1-IP' => 'Off'],
					[
					'Merchant' => ['id' => "cd69c94c-fake-fake-fake-af71e72757a2", 'merchant_mid' => '1234567890123456', 'user_id' => AxiaTestCase::USER_REP_ID],
					'MerchantPricing' => [
							//Dicount Rate Structure any non flat rate bet_table id
							'Discount',
							'visa_bet_table_id' => '56df682f-2400-41bd-974f-078234627ad4',
							'mc_bet_table_id' => '56df682f-9514-4fb5-b656-078234627ad4',
						]
					],
					'5570e7fe-5a64-4a93-8724-337a34627ad4',
					true, //products enabled for Mark Weatherford's user compensation profile
					true,
					[
						//expect all visa/Mc products with corresponding FLAT RATE products
						["products_services_type_id" => '551038c2-2220-4a58-b8d8-1a3f34627ad4'], //Credit Mothly
						["products_services_type_id" => 'c907b3b8-2cf5-475b-898c-53ed1b0adaf6'], //'Visa Dial Sales',
						["products_services_type_id" => 'ec5b6684-6f5c-4881-b26c-5a8233bde091'], //'Visa Discount',
						["products_services_type_id" => '728ff26a-8209-4cc4-86ae-e79a2df21e7b'], //'MasterCard Dial Sales',
					]
				],
			"Non-Online app amex only Discount Exclude Flat Rate" => [	//Data set intent:
					//Non-Online app Add amex only with corresponding Discount products exclude Flat Rate
					['AmexNum' => '123AmexNumber', 'MID' => '1234567890123456', 'Term1-IP' => 'Off'],
					[
					'Merchant' => ['id' => "cd69c94c-fake-fake-fake-af71e72757a2", 'merchant_mid' => '1234567890123456', 'user_id' => AxiaTestCase::USER_REP_ID],
					'MerchantPricing' => [
							//Dicount Rate Structure any non flat rate bet_table id
							'Discount',
							'amex_bet_table_id' => '558ade3b-f5ec-43e1-bd89-20ba34627ad4',
						]
					],
					'5570e7fe-5a64-4a93-8724-337a34627ad4',
					true, //products enabled for Mark Weatherford's user compensation profile
					false,
					[
						//expect all amex products with corresponding discount products
						["products_services_type_id" => '551038c2-2220-4a58-b8d8-1a3f34627ad4'], //Credit Mothly
						["products_services_type_id" => '566f5f47-bac0-41f8-ac68-23f534627ad4'], //'Amex Discount',
						["products_services_type_id" => '62d2eb5d-e3b8-44e9-b1ac-d770289d7f46'], //'Amex Dial Sales',
					]
				],
			"Non-Online app amex only Excludes Discount and Flat Rate" => [	//Data set intent:
					//Non-Online app Add amex only exclude Discount and Dial and Flat Rate products
					['AmexNum' => '123AmexNumber', 'MID' => '1234567890123456', Merchant::AMEX_CSV_ACCEPT_EXIST => 'Yes', 'Term1-IP' => 'On'],
					[
					'Merchant' => ['id' => "cd69c94c-fake-fake-fake-af71e72757a2", 'merchant_mid' => '1234567890123456', 'user_id' => AxiaTestCase::USER_REP_ID],
					'MerchantPricing' => [
							//Dicount Rate Structure any non flat rate bet_table id
							'Discount',
							'amex_bet_table_id' => '558ade3b-f5ec-43e1-bd89-20ba34627ad4',
						]
					],
					'5570e7fe-5a64-4a93-8724-337a34627ad4',
					true, //products enabled for Mark Weatherford's user compensation profile
					false,
					[
						//expect all amex products with corresponding discount products
						["products_services_type_id" => '551038c2-2220-4a58-b8d8-1a3f34627ad4'], //Credit Mothly
						["products_services_type_id" => 'e5324035-1074-4a40-90f4-5cfc7c4fd10e'], //'Amex Non Dial Sales',
					]
				]
			];
	}
/**
 * _enableProducts
 * Saves data to UserParameters that enables products for a certain rep
 * 
 * @param array $pIds product ids indexed
 * @param string $ucpId user compensation profileId
 * @return void
 */
	protected function _enableProducts($pIds, $ucpId, $isEnabled) {
		foreach ($pIds as $pId) {
			$data[] = [
				'user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP,
				'products_services_type_id' => $pId,
				'value' => (int)$isEnabled,
				'user_compensation_profile_id' => $ucpId,
			];
		}
		$this->Merchant->User->UserCompensationProfile->UserParameter->deleteAll(['user_compensation_profile_id' => $ucpId], false);
		return $this->Merchant->User->UserCompensationProfile->UserParameter->saveMany($data);
	}

/**
 * testExtractNonWomplyUsers()
 * 
 * @return void
 */
	public function testExtractNonWomplyUsers() {
		$data = [
			'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
			'partner_id' => '54365df7-6b97-4f86-9e6f-8638eb30cd9e',
			'referer_id' => '00000000-0000-0000-0000-000000000005',
			'reseller_id' => '1d58a4cd-664a-4c08-9b38-94ffc7e61afa',
		];
		$expected = [User::ROLE_REP, User::ROLE_PARTNER, User::ROLE_REFERRER, User::ROLE_RESELLER];
		$actual = $this->Merchant->extractNonWomplyUsers($data);
		$this->assertEquals($expected, $actual);
	}

/**
 * testUpdateUserMerchantsWomplyrs()
 * 
 * @return void
 */
	public function testUpdateUserMerchantsWomply() {
		$userId = '32165df7-6b97-4f86-9e6f-8638eb30cd9e';
		$status = true;
		$dataB4Update = $this->Merchant->find('all', [
				'conditions' => ['user_id' => $userId],
				'fields' => ['id', 'womply_merchant_enabled']
			]);
		//changing the user womply status will trigger beforeSave and afterSave callbacks which call Merchant->updateUserMerchantsWomply()
		$this->Merchant->User->save(['id' => $userId, 'womply_user_enabled' => $status], ['validate' => false]);

		$dataAfterUpdate = $this->Merchant->find('all', [
				'conditions' => ['user_id' => $userId],
				'fields' => ['id', 'womply_merchant_enabled']
			]);

		foreach ($dataB4Update as $preUpdate) {
			$newVal = Hash::extract($dataAfterUpdate, "{n}.Merchant[id={$preUpdate['Merchant']['id']}].womply_merchant_enabled");
			$actual = array_pop($newVal);
			//initially before the update should be false
			$this->assertFalse($preUpdate['Merchant']['womply_merchant_enabled']);
			//after the update is should be false
			$this->assertTrue($actual);
		}
	}

/**
 * testExternalIdsAreDistinct()
 * 
 * @return void
 */
	public function testExternalIdsAreDistinct() {
		$csvData = [
			['oaID' => '', 'external_foreign_id' => '3210'],
			['oaID' => '3210', 'external_foreign_id' => ''],
		];
		
		$this->assertTrue($this->Merchant->externalIdsAreDistinct($csvData));
		$dupCsvData = [$csvData[0], $csvData[0]];
		$actual = $this->Merchant->externalIdsAreDistinct($dupCsvData);
		$this->assertCount(1, $actual);//one error
		$this->assertContains("Duplicate value 3210 found in external_foreign_id column. Cannot create more than one merchant account with the same external_foreign_id!", $actual);
		$csvData = [
			['oaID' => '123', 'external_foreign_id' => '3210'],
			['oaID' => '123', 'external_foreign_id' => '3210']
		];

		$actual = $this->Merchant->externalIdsAreDistinct($csvData);
		$this->assertCount(2, $actual);//two errors
		$this->assertContains("Data for online application with id 123 has already been uploaded. Cannot create more than one merchant account with the same application ID!", $actual);
		$this->assertContains("Duplicate value 3210 found in external_foreign_id column. Cannot create more than one merchant account with the same external_foreign_id!", $actual);

	}

/**
 * testConditionallyRequired()
 * 
 * @return void
 */
	public function testConditionallyRequired() {
		//Test scenario where merchant is required to have the data present during
		$tstData = [
			'id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
			'bet_network_id' => CakeText::uuid(),
			'merchant_acquirer_id' => CakeText::uuid()
		];
		//All good
		$this->Merchant->set($tstData);
		$this->assertTrue($this->Merchant->validates());
		$expected = 'This option is required for this merchant.';
		$invalidData = $tstData;

		//Missing merchant_acquirer_id
		$invalidData['merchant_acquirer_id'] = null;
		$this->Merchant->set($invalidData);
		$this->assertFalse($this->Merchant->validates());
		$this->assertEqual($expected, Hash::get($this->Merchant->validationErrors, 'merchant_acquirer_id.0'));

		//Missing bet_network_id
		$invalidData = $tstData;
		$invalidData['bet_network_id'] = null;
		$this->Merchant->set($invalidData);
		$this->assertFalse($this->Merchant->validates());
		$this->assertEqual($expected, Hash::get($this->Merchant->validationErrors, 'bet_network_id.0'));

		//Missing both
		$invalidData['merchant_acquirer_id'] = null;
		$this->Merchant->set($invalidData);
		$this->assertFalse($this->Merchant->validates());
		$this->assertEqual($expected, Hash::get($this->Merchant->validationErrors, 'bet_network_id.0'));
		$this->assertEqual($expected, Hash::get($this->Merchant->validationErrors, 'merchant_acquirer_id.0'));

		//Test with a merchant that doesnt have any of the products that would make these fields required
		$tstData['id'] = CakeText::uuid();
		$this->Merchant->set($tstData);
		$this->assertTrue($this->Merchant->validates());
	}

/**
 * testGetApiMerchantData()
 * 
 * @covers Merchant::getApiMerchantData
 * @return void
 */
	public function testGetApiMerchantData() {
		$conditions = [
			'Merchant.merchant_mid' => '4541619030000153'
		];
		$actual = $this->Merchant->getApiMerchantData($conditions);
		$this->assertNotEmpty($actual);
		$this->assertSame(array_keys(Configure::read('ApiFieldNames')), array_keys($actual));
		$this->assertSame($conditions['Merchant.merchant_mid'], $actual['merchant_mid']);
		
		$conditions['Merchant.merchant_mid'] = '9999999999999';//fake

		$actual = $this->Merchant->getApiMerchantData($conditions);
		$this->assertEmpty($actual);

		$conditions = [
			'Merchant.merchant_mid' => '4048000030003049',
			'Merchant.merchant_dba' => 'Aloha Burger'
		];

		$actual = $this->Merchant->getApiMerchantData($conditions);
		$this->assertNotEmpty($actual);
		$this->assertSame($conditions['Merchant.merchant_mid'], $actual['merchant_mid']);
		$this->assertSame($conditions['Merchant.merchant_dba'], $actual['merchant_dba']);
	}

}
