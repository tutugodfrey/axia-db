<?php
App::uses('UserCompensationProfile', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * UserCompensationProfile Test Case
 *
 */
class UserCompensationProfileTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$this->id = '5570e7fe-5a64-4a93-8724-337a34627ad4';
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserCompensationProfile);

		parent::tearDown();
	}

/**
 * testGetListGroupedByUser method
 *
 * @return void
 */
	public function testGetListGroupedByUser() {
		$expected = [
			'Slim Pickins' => [
				'6570e7dc-a4fc-444c-8929-337a34627ad4' => 'Default Rep UCP',
                '8bbe2d12-2975-4466-a309-fcf4e721d468' => null
			]
		];
		$actual = $this->UserCompensationProfile->getListGroupedByUser(['32165df7-6b97-4f86-9e6f-8638eb30cd9e']);
		$this->assertSame($expected, $actual);

		$expected = [
			'Bob Apple' => [
				'7570e7dc-a4fc-444c-8929-337a34627ad4' => 'Default Rep UCP'
			],
			'Slim Pickins' => [
				'6570e7dc-a4fc-444c-8929-337a34627ad4' => 'Default Rep UCP',
                '8bbe2d12-2975-4466-a309-fcf4e721d468' => null
			]
		];
		$actual = $this->UserCompensationProfile->getListGroupedByUser(['32165df7-6b97-4f86-9e6f-8638eb30cd9e', '43265df7-6b97-4f86-9e6f-8638eb30cd9e']);
		$this->assertSame($expected, $actual);
	}

/**
 * testCopyMany method
 *
 * @return void
 */
	public function testCopyMany() {
		$this->UserCompensationProfile = $this->_mockModel('UserCompensationProfile', ['rbacIsPermitted']);
		$this->UserCompensationProfile
				->expects($this->any())
				->method('rbacIsPermitted')
				->with('app/actions/UserCompensationProfiles/view/module/SMCompModule')
				->will($this->returnValue(true));
		$targetUserID = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
		$souceUcpIds = ['121affc3-76f2-4600-a817-0fc44dffef79'];
		
		$this->assertFalse($this->UserCompensationProfile->hasAny(['user_id' => 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda']));
		$this->assertTrue($this->UserCompensationProfile->copyMany($targetUserID, $souceUcpIds));
		$this->assertTrue($this->UserCompensationProfile->hasAny(['user_id' => 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda']));
	}

/**
 * testGetUsersWithComps method
 *
 * @return void
 */
	public function testGetUsersWithComps() {
		$expected = [
			'54365df7-6b97-4f86-9e6f-8638eb30cd9e' => 'A Partner',
			'00000000-0000-0000-0000-000000000010' => 'Ben Franklin',
			'003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee',
			'43265df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Bob Apple',
			'00000000-0000-0000-0000-000000000014' => 'John Smith',
			'00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'Mark Weatherford',
			'32165df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Slim Pickins',
			'113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep'
		];
		$actual = $this->UserCompensationProfile->getUsersWithComps();
		$this->assertSame($expected, $actual);
		
		$actual = $this->UserCompensationProfile->getUsersWithComps(null, true);
		$this->assertSame($expected, $actual);

		$expected = [
			'32165df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Slim Pickins',
			'113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep'
		];
		$actual = $this->UserCompensationProfile->getUsersWithComps('113114ae-8888-8888-8888-fa0c0f25c786', true);
		$this->assertSame($expected, $actual);
	}

/**
 * testBuildUcpList method
 *
 * @return void
 */
	public function testBuildUcpList() {
		$expected = ['8bbe2d12-2975-4466-a309-fcf4e721d468' => 'Partner-Rep UCP associated with '];
		$actual = $this->UserCompensationProfile->buildUcpList('32165df7-6b97-4f86-9e6f-8638eb30cd9e', '113114ae-8888-8888-8888-fa0c0f25c786');
		$this->assertSame($expected, $actual);

		$expected = ['6570e7dc-a4fc-444c-8929-337a34627ad4' => 'Default UCP'];
		$actual = $this->UserCompensationProfile->buildUcpList('32165df7-6b97-4f86-9e6f-8638eb30cd9e', '1d58a4cd-664a-4c08-9b38-94ffc7e61afa');
		$this->assertSame($expected, $actual);

	}

/**
 * testGetUserOrPartnerUserCompProfile method
 *
 * @return void
 */
	public function testGetUserOrPartnerUserCompProfile() {
		$expected = [
			'UserCompensationProfile' => [
				'id' => '5570e7fe-5a64-4a93-8724-337a34627ad4',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'partner_user_id' => '113114ae-8888-8888-8888-fa0c0f25c786',
				'is_partner_rep' => false,
				'is_default' => true,
				'is_profile_option_1' => 0,
				'is_profile_option_2' => 0,
				'role_id' => null
			]
		];
		$actual = $this->UserCompensationProfile->getUserOrPartnerUserCompProfile('00ccf87a-4564-4b95-96e5-e90df32c46c1');
		$this->assertSame($expected, $actual);
		$actual = $this->UserCompensationProfile->getUserOrPartnerUserCompProfile('00ccf87a-4564-4b95-96e5-e90df32c46c1', '113114ae-8888-8888-8888-fa0c0f25c786');
		$this->assertSame($expected, $actual);
	}

/**
 * testGetAssociatedCompList method
 *
 * @return void
 */
	public function testGetAssociatedCompList() {
		$this->UserCompensationProfile = $this->_mockModel('UserCompensationProfile', ['rbacIsPermitted']);
		$this->UserCompensationProfile
				->expects($this->any())
				->method('rbacIsPermitted')
				->with('app/actions/UserCompensationProfiles/addCompProfile')
				->will($this->returnValue(true));

		$data = $this->UserCompensationProfile->getAssociatedCompList('00ccf87a-4564-4b95-96e5-e90df32c46c1', true, false);
		$this->assertNotEmpty($data['UserCompensationAssociation']);
	}

/**
 * testGetCompData method
 *
 * @return void
 */
	public function testGetCompData() {
		$this->UserCompensationProfile = $this->_mockModel('UserCompensationProfile', ['rbacIsPermitted']);
		$this->UserCompensationProfile
				->expects($this->once())
				->method('rbacIsPermitted')
				->with('app/actions/UserCompensationProfiles/view/module/SMCompModule')
				->will($this->returnValue(true));

		$data = $this->UserCompensationProfile->getCompData($this->id, null, true);
		$this->assertArrayNotHasKey('UserCompensationAssociation', Hash::extract($data, 'contain.{s}'));
		$this->assertArrayNotHasKey('UserCompensationAssociation', Hash::extract($data, 'contain'));
		$this->assertNotEmpty($data['UserCompensationProfile']);
		$this->assertNotEmpty($data['User']['id']);
		$this->assertNotEmpty($data['PartnerUser']['id']);
		$this->assertNotEmpty($data['AddlAmexRepCost']['id']);
		$this->assertNotEmpty($data['ResidualParameter']);
		$this->assertNotEmpty($data['UserCompensationAssociation']);
		$this->assertNotEmpty($data['AchRepCost']);
		$this->assertNotEmpty($data['RepMonthlyCost']);
		$this->assertNotEmpty($data['GatewayCostStructure']);
		$this->assertNotEmpty($data['RepProductSetting']);
		$this->assertArrayHasKey('ResidualVolumeTier', $data);
		$this->assertArrayHasKey('ResidualTimeFactor', $data);
		$this->assertArrayHasKey('WebAchRepCost', $data);
	}

/**
 * testValidateProfileOption method
 *
 * @return void
 */
	public function testValidateProfileOption() {
		$data = array(
			'UserCompensationProfile' => array(
				'is_profile_option_1' => 1,
				'is_profile_option_2' => 1
			));
		$this->UserCompensationProfile->set($data);
		$actual = $this->UserCompensationProfile->validates(array('fieldList' => array('is_profile_option_1', 'is_profile_option_2')));
		$this->assertFalse($actual);

		$data['UserCompensationProfile']['is_profile_option_1'] = 0;
		$this->UserCompensationProfile->set($data);
		$actual = $this->UserCompensationProfile->validates(array('fieldList' => array('is_profile_option_1', 'is_profile_option_2')));
		$this->assertTrue($actual);
	}

/**
 * testCreateInitialCompData method
 *
 * @return void
 */
	public function testCreateInitialCompData() {
		$actual = $this->UserCompensationProfile->createInitialCompData($this->id, '123465-5a64-4a93-8724-337a34627ad4');
		$this->assertTrue($actual);
	}

/**
 * testHasDefault method
 *
 * @return void
 */
	public function testHasDefault() {
		$this->assertTrue($this->UserCompensationProfile->hasDefault('00ccf87a-4564-4b95-96e5-e90df32c46c1'));
		$this->assertFalse($this->UserCompensationProfile->hasDefault('00000000-9999-0000-0000-000000000001'));
	}

/**
 * testGetMgrRoleCompProfileList method
 *
 * @return void
 */
	public function testGetMgrRoleCompProfileList() {
		$actual = $this->UserCompensationProfile->getMgrRoleCompProfileList('d2b7550c-d761-40b7-a769-ca1cf2ac9332');
		$expected = Configure::read('AssociatedUserRoles.SalesManager.roles');
		$actual = Hash::extract($actual, '{s}');
		$this->assertEquals($actual, $actual);
		$this->assertEmpty($this->UserCompensationProfile->getMgrRoleCompProfileList('00000000-9999-0000-0000-000000000001'));
	}

/**
 * testGetRepOrMgrPctOfGross
 *
 * @return void
 */
	public function testGetRepOrMgrPctOfGross() {
		$userId = $this->UUIDv4();
		$mgrUserId = $this->UUIDv4();
		$pUserId = $this->UUIDv4();
		$productId = $this->UUIDv4();
		//test get rep percent of gross
		$tstData = [
			'UserCompensationProfile' => [
				'user_id' => $userId
			],
			'UserParameter' => [
				[
					'user_parameter_type_id' => 'd78ed6bf-739f-43b9-a262-dd343d3bcb5b',
					'products_services_type_id' => $productId,
					'associated_user_id' => $userId,
					'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
					'value' => .15
				],
				[
					'associated_user_id' => $mgrUserId,
					'user_parameter_type_id' => 'd78ed6bf-739f-43b9-a262-dd343d3bcb5b',
					'products_services_type_id' => $productId,
					'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
					'value' => .17
				]
			]
		];
		$this->UserCompensationProfile->saveAssociated($tstData);
		$actual = $this->UserCompensationProfile->getRepOrMgrPctOfGross($userId, $productId, '192f8406-836d-4341-a1ee-d9d568cdb53d');
		$this->assertEquals(.15, $actual);

		//test get assocoated manager percent of gross
		$actual = $this->UserCompensationProfile->getRepOrMgrPctOfGross($userId, $productId, '192f8406-836d-4341-a1ee-d9d568cdb53d', $mgrUserId);
		$this->assertEquals(.17, $actual);

		//test get partner rep percent of gross
		//unset($tstData['UserParameter'][1]); //remove manager stuff
		$tstData['UserCompensationProfile']['is_default'] = false;
		$tstData['UserCompensationProfile']['is_partner_rep'] = true;
		$tstData['UserCompensationProfile']['partner_user_id'] = $pUserId;
		$tstData['UserParameter'][0]['associated_user_id'] = $userId; //self associated
		$tstData['UserParameter'][0]['value'] = .16;

		$this->UserCompensationProfile->saveAssociated($tstData);
		$actual = $this->UserCompensationProfile->getRepOrMgrPctOfGross($userId, $productId, '192f8406-836d-4341-a1ee-d9d568cdb53d', null, $pUserId);
		$this->assertEquals(.16, $actual);

		//test that passing both manager id and partner id params will return associated manager percent of gross from the PartnerRep comp profile
		$actual = $this->UserCompensationProfile->getRepOrMgrPctOfGross($userId, $productId, '192f8406-836d-4341-a1ee-d9d568cdb53d', $mgrUserId, $pUserId);
		$this->assertEquals(.17, $actual);
	}

/**
 * testGetDefaultResidualPct
 *
 * @return void
 */
	public function testGetDefaultResidualPct() {
		$refUserId = '00ccf87a-4564-4b95-96e5-e90df32c46c1';
		$productId1 = $this->UUIDv4();
		$productId2 = $this->UUIDv4();
		$residualParameterType1 = $this->UUIDv4();
		$residualParameterType2 = $this->UUIDv4();
		$tstData = [
			'UserCompensationProfile' => [
				'user_id' => $refUserId
			],
			'ResidualParameter' => [
				[
				'residual_parameter_type_id' => $residualParameterType1,
				'products_services_type_id' => $productId1,
				'tier' => 0,
				'value' => .99
				],
				[
				'residual_parameter_type_id' => $residualParameterType2,
				'products_services_type_id' => $productId2,
				'tier' => 0,
				'value' => .98
				],
			]
		];

		$this->UserCompensationProfile->saveAssociated($tstData);
		$actual = $this->UserCompensationProfile->getDefaultResidualPct($refUserId, $productId1);
		$this->assertEquals(0, $actual);

		$actual = $this->UserCompensationProfile->getDefaultResidualPct($refUserId, $productId2);
		$this->assertEquals(0, $actual);
	}

/**
 * testGetMultiple
 *
 * @return void
 */
	public function testGetMultiple() {
		//Arbitrary product id
		$productId1 = $this->UUIDv4();
		$userId = '32165df7-6b97-4f86-9e6f-8638eb30cd9e';
		//Arbitrary manager id
		$smUserId = $this->UUIDv4();
		//Save muiltiple test data
		$tstData = [
			'UserCompensationProfile' => [
				'id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
				'user_id' => $userId,
				'is_profile_option_1' => 1,
				'is_profile_option_2' => 0
			],
			'ResidualParameter' => [
				[
					'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
					'residual_parameter_type_id' => ResidualParameterType::R_MULTIPLE, //rep multiple type
					'products_services_type_id' => $productId1,
					'is_multiple' => 1,
					'tier' => 0,
					'value' => 5
				],
				[
					'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
					'residual_parameter_type_id' => ResidualParameterType::MGR_MULTIPLE, //manager multiple type
					'associated_user_id' => $smUserId,
					'products_services_type_id' => $productId1,
					'is_multiple' => 1,
					'tier' => 0,
					'value' => 7
				]
			],
			'ResidualTimeFactor' => [
				[
					'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
					'tier1_begin_month' => 0,
					'tier1_end_month' => 1,
					'tier2_begin_month' => 2,
					'tier2_end_month' => 3,
					'tier3_begin_month' => 4,
					'tier3_end_month' => 5,
					'tier4_begin_month' => 6,
					'tier4_end_month' => 7,
				]
			],
			'ResidualTimeParameter' => [
				[
					'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
					'residual_parameter_type_id' => ResidualParameterType::R_MULTIPLE, //rep multiple type
					'products_services_type_id' => $productId1,
					'is_multiple' => 1,
					'tier' => 1,
					'value' => 6
				],
				[
					'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
					'residual_parameter_type_id' => ResidualParameterType::MGR_MULTIPLE, //manager multiple type
					'associated_user_id' => $smUserId,
					'products_services_type_id' => $productId1,
					'is_multiple' => 1,
					'tier' => 1,
					'value' => 12
				]
			],

		];
		$this->UserCompensationProfile->saveAll($tstData, ['deep' => true]);
		//Get rep multiple
		$actual = $this->UserCompensationProfile->getMultiple($userId, '', $productId1, 0);
		$expected = $tstData['ResidualParameter'][0]['value'];
		$this->assertEquals($expected, $actual);

		//Get rep's manager's multiple
		$actual = $this->UserCompensationProfile->getMultiple($userId, '', $productId1, 0, $smUserId);
		$expected = $tstData['ResidualParameter'][1]['value'];
		$this->assertEquals($expected, $actual);

		//Test option 2
		$this->UserCompensationProfile->save([
			'id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
			'user_id' => $userId,
			'is_profile_option_1' => 0,
			'is_profile_option_2' => 1
		]);
		$actual = $this->UserCompensationProfile->getMultiple($userId, '', $productId1, 0);
		$expected = $tstData['ResidualTimeParameter'][0]['value'];
		$this->assertEquals($expected, $actual);
		//Get rep's manager's multiple
		$actual = $this->UserCompensationProfile->getMultiple($userId, '', $productId1, 0, $smUserId);
		$expected = $tstData['ResidualTimeParameter'][1]['value'];
		$this->assertEquals($expected, $actual);

		//Test Partner Rep UCP with option 1
		$partnerId = $this->UUIDv4(); //Arbitrary partner
		$this->UserCompensationProfile->save([
			'id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
			'user_id' => $userId,
			'partner_user_id' => $partnerId,
			'is_default' => false,
			'is_profile_option_1' => 1,
			'is_profile_option_2' => 0
		]);
		//Get rep multiple
		$actual = $this->UserCompensationProfile->getMultiple($userId, $partnerId, $productId1, 0);
		$expected = $tstData['ResidualParameter'][0]['value'];
		$this->assertEquals($expected, $actual);

		//Get rep's manager's multiple
		$actual = $this->UserCompensationProfile->getMultiple($userId, $partnerId, $productId1, 0, $smUserId);
		$expected = $tstData['ResidualParameter'][1]['value'];
		$this->assertEquals($expected, $actual);

		//Test PartnerRep UCP option 2
		$this->UserCompensationProfile->save([
			'id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
			'user_id' => $userId,
			'partner_user_id' => $partnerId,
			'is_default' => false,
			'is_profile_option_1' => 0,
			'is_profile_option_2' => 2
		]);
		$actual = $this->UserCompensationProfile->getMultiple($userId, $partnerId, $productId1, 0);
		$expected = $tstData['ResidualTimeParameter'][0]['value'];
		$this->assertEquals($expected, $actual);
		//Get rep's manager's multiple
		$actual = $this->UserCompensationProfile->getMultiple($userId, $partnerId, $productId1, 0, $smUserId);
		$expected = $tstData['ResidualTimeParameter'][1]['value'];
		$this->assertEquals($expected, $actual);
	}

/**
 * testGetAssociatedPartners
 *
 * @return void
 */
	public function testGetAssociatedPartners() {
		$userIds =[
			'00ccf87a-4564-4b95-96e5-e90df32c46c1',
			'43265df7-6b97-4f86-9e6f-8638eb30cd9e',
			'32165df7-6b97-4f86-9e6f-8638eb30cd9e',
		];
		$expected = [
			'43265df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Bob Apple',
			'113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep',
		];
		$actual = $this->UserCompensationProfile->getAssociatedPartners($userIds);
		$this->assertSame($expected, $actual);

		$userIds = '43265df7-bad7-bad6-badf-8638eb30cd9e'; //bad id no results
		$expected = [];
		$actual = $this->UserCompensationProfile->getAssociatedPartners($userIds);
		$this->assertSame($expected, $actual);
	}

/**
 * testGetDefaultResidualPct
 *
 * @return void
 */
	public function testSetHasManyAssocData() {
		$data = [
			'UserCompensationProfile' => [
				'some' => 'stuff'
			],
			'AssocModel1' => [
				['key' => 'val'],
				['key' => 'val2']
			],
			'AssocModel2' => [
				[
					'AssocModel2' => [
						'key' => 'val'
					],
					'SecondaryAssocModel' => [
						'key' => 'val'
					],
				],
				[
					'AssocModel2' => [
						'key' => 'val2'
					]
				]
			]
		];
		$expected = [
			'UserCompensationProfile' => [
				'some' => 'stuff'
			],
			'AssocModel1' => [
				['key' => 'val'],
				['key' => 'val2']
			],
			'AssocModel2' => [
				['key' => 'val'],
				['key' => 'val2']
			]
		];
		$this->UserCompensationProfile->setHasManyAssocData($data);
		$this->assertSame($expected, $data);
	}
}
