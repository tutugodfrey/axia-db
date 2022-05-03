<?php
App::uses('AssociatedUser', 'Model');
App::uses('User', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AssociatedUser Test Case
 *
 */
class AssociatedUserTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AssociatedUser = ClassRegistry::init('AssociatedUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AssociatedUser);
		parent::tearDown();
	}

/**
 * testCheckUniqueAssociation
 *
 * @return void
 */
	public function testCheckUniqueAssociation() {
		$data = [
			'user_id' => AxiaTestCase::USER_PARTNER_REP_ID,
			'associated_user_id' => AxiaTestCase::USER_SM_ID,
			'user_compensation_profile_id' => '00000000-0000-0000-0000-999999999999',
		];
		$this->AssociatedUser->set($data);
		$this->assertTrue($this->AssociatedUser->checkUniqueAssociation([]));

		$associatedUser = $this->AssociatedUser->find('first');
		$data = [
			'user_id' => Hash::get($associatedUser, 'AssociatedUser.user_id'),
			'associated_user_id' => Hash::get($associatedUser, 'AssociatedUser.associated_user_id'),
			'user_compensation_profile_id' => Hash::get($associatedUser, 'AssociatedUser.user_compensation_profile_id'),
		];
		$this->AssociatedUser->create();
		$this->AssociatedUser->set($data);
		$this->assertFalse($this->AssociatedUser->checkUniqueAssociation([]));
	}

/**
 * testMaxRoleAssociation
 *
 * @return void
 */
	public function testMaxRoleAssociation() {
		$data = [
			'user_id' => AxiaTestCase::USER_REP_ID,
			'role' => User::ROLE_GROUP_RESELLER,
			'user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4'
		];
		$this->AssociatedUser->set($data);
		$this->assertTrue($this->AssociatedUser->maxRoleAssociation([]));

		$data = [
			'user_id' => AxiaTestCase::USER_REP_ID,
			'role' => User::ROLE_GROUP_SM,
			'user_compensation_profile_id' => '5570e7fe-5a64-4a93-8724-337a34627ad4'
		];
		$this->AssociatedUser->set($data);
		$this->assertTrue($this->AssociatedUser->maxRoleAssociation([]));

		//Save the association with the same ucp id to make sure it does not validate
		//Emulates existing data
		$ucpId = CakeText::uuid(); //create arbitrary ucp id
		$this->AssociatedUser->saveMany(
			[
				[
					'user_compensation_profile_id' => $ucpId,
					'user_id' => AxiaTestCase::USER_PARTNER_REP_ID,
					'associated_user_id' => CakeText::uuid(), //arbitrary associated user
					'permission_level' => User::ROLE_GROUP_REP,
					'role' => User::ROLE_GROUP_REP,
				],
				[
					'user_compensation_profile_id' => $ucpId,
					'user_id' => AxiaTestCase::USER_PARTNER_REP_ID,
					'associated_user_id' => CakeText::uuid(), //arbitrary associated user
					'permission_level' => User::ROLE_GROUP_REP,
					'role' => User::ROLE_GROUP_REP,
				],
			]
		);
		$data = [
			'user_id' => AxiaTestCase::USER_PARTNER_REP_ID,
			'role' => User::ROLE_GROUP_REP,
			'user_compensation_profile_id' => $ucpId
		];
		$this->AssociatedUser->set($data);
		$this->assertFalse($this->AssociatedUser->maxRoleAssociation([]));
	}

/**
 * testGetAssociatedUsers
 *
 * @return void
 */
	public function testGetAssociatedUsers() {
		$expected = [
			'003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => [
					'role' => 'Sales Manager',
					'name' => 'Bill McAbee',
			],
			'113114ae-7777-7777-7777-fa0c0f25c786' => [
					'role' => 'Installer',
					'name' => 'Mr Installer',
			],
		];
		$result = $this->AssociatedUser->getAssociatedUsers(AxiaTestCase::USER_REP_ID, '5570e7fe-5a64-4a93-8724-337a34627ad4');
		$this->assertEquals($expected, $result);

		$expected = [
			'003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6',
			'113114ae-7777-7777-7777-fa0c0f25c786',
			AxiaTestCase::USER_REP_ID,
		];
		$result = $this->AssociatedUser->getAssociatedUsers(AxiaTestCase::USER_REP_ID, '5570e7fe-5a64-4a93-8724-337a34627ad4', true);
		$this->assertEquals($expected, array_keys($result));
		$expected = [
			'role' => 'Merchant',
			'name' => 'Mark Weatherford',
		];
		$this->assertEquals($expected, Hash::get($result, AxiaTestCase::USER_REP_ID));
	}

/**
 * testToggleMainAssocUser
 *
 * @covers AssociatedUser::toggleMainAssocUser()
 * @return void
 */
	public function testToggleMainAssocUser() {
		//arbitrary uuids
		$userId = CakeText::uuid();
		$assocUserId = CakeText::uuid();
		$assocUserId2 = CakeText::uuid();
		$ucpId = CakeText::uuid();
		//Scenario A: two managers associated with the same user
		$tstData = [
			[
				'user_id' => $userId,
				'associated_user_id' => $assocUserId,
				'role' => Configure::read('AssociatedUserRoles.SalesManager.label'),
				'permission_level' => User::ROLE_SM,
				'user_compensation_profile_id' => $ucpId
			],
			[
				'user_id' => $userId,
				'associated_user_id' => $assocUserId2,
				'role' => Configure::read('AssociatedUserRoles.SalesManager.label'),
				'permission_level' => User::ROLE_SM,
				'user_compensation_profile_id' => $ucpId
			]
		];
		$this->AssociatedUser->saveMany($tstData);

		//set first $assocUserId manager as the main manager
		$this->AssociatedUser->toggleMainAssocUser($assocUserId, $ucpId);
		$associatedUsers = $this->AssociatedUser->find('all', [
			'conditions' => ['user_compensation_profile_id' => $ucpId]
		]);
		$this->assertCount(2, $associatedUsers);
		foreach ($associatedUsers as $assocUser) {
			if ($assocUser['AssociatedUser']['associated_user_id'] === $assocUserId) {
				$this->assertTrue($assocUser['AssociatedUser']['main_association']);
			} else {
				$this->assertFalse($assocUser['AssociatedUser']['main_association']);
			}
		}

		//Now switch the main manager to the second $assocUserId2 manager
		$this->AssociatedUser->toggleMainAssocUser($assocUserId2, $ucpId);
		$associatedUsers = $this->AssociatedUser->find('all', [
			'conditions' => ['user_compensation_profile_id' => $ucpId]
		]);
		$this->assertCount(2, $associatedUsers);
		foreach ($associatedUsers as $assocUser) {
			if ($assocUser['AssociatedUser']['associated_user_id'] === $assocUserId2) {
				$this->assertTrue($assocUser['AssociatedUser']['main_association']);
			} else {
				$this->assertFalse($assocUser['AssociatedUser']['main_association']);
			}
		}

		//Scenario B: NO managers associated should result in falure (only managers can be assigned as the main user association)
		//Reset arbitrary uuids
		$userId = CakeText::uuid();
		$assocUserId = CakeText::uuid();
		$assocUserId2 = CakeText::uuid();
		$ucpId = CakeText::uuid();
		$tstData = [
			[
				'user_id' => $userId,
				'associated_user_id' => $assocUserId,
				'role' => Configure::read('AssociatedUserRoles.Referrer.label'),
				'permission_level' => User::ROLE_REFERRER,
				'user_compensation_profile_id' => $ucpId
			],
			[
				'user_id' => $userId,
				'associated_user_id' => $assocUserId2,
				'role' => Configure::read('AssociatedUserRoles.Referrer.label'),
				'permission_level' => User::ROLE_REFERRER,
				'user_compensation_profile_id' => $ucpId
			]
		];
		$this->AssociatedUser->saveMany($tstData);
		//set first $assocUserId as the main associated user
		$result = $this->AssociatedUser->toggleMainAssocUser($assocUserId, $ucpId);
		//This should fail since the first $assocUserId is not a manager
		$this->assertFalse($result);
		//set second $assocUserId2 as the main associated user
		$result = $this->AssociatedUser->toggleMainAssocUser($assocUserId2, $ucpId);
		//This should fail since the seciond $assocUserId2 is not a manager either
		$this->assertFalse($result);
	}

/**
 * testDeleteAllAssocData
 *
 * @return void
 */
	public function testDeleteAllAssocData() {
		$userId = CakeText::uuid();
		$userAssociatedId = CakeText::uuid();
		$ucpId = CakeText::uuid();
		$associatedUserId = CakeText::uuid(); //id for AssociatedUser.id
		$tstData = [
			'UserCompensationProfile' => ['id' => $ucpId, 'user_id' => $userId],
			'UserCompensationAssociation' => [['id' => $associatedUserId, 'user_id' => $userId, 'user_compensation_profile_id' => $ucpId, 'associated_user_id' => $userAssociatedId, 'role' => 'Sales Manager', 'permission_level' => 'SM']],
			'AttritionRatio' => [['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId]],
			'CommissionFee' => [['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId]],
			'UserParameter' => [
				['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId],
				['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId],
			],
			'ResidualParameter' => [
				['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId],
				['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId],
			],
			'ResidualTimeParameter' => [
				['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId],
				['id' => CakeText::uuid(), 'associated_user_id' => $userAssociatedId, 'user_compensation_profile_id' => $ucpId],
			],
		];
		$UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$UserCompensationProfile->saveAll($tstData);
		$result = $UserCompensationProfile->find('first', ['recursive' => 1, 'conditions' => ['UserCompensationProfile.id' => $ucpId]]);
		$this->assertNotEmpty($result['AttritionRatio']);
		$this->assertNotEmpty($result['CommissionFee']);
		$this->assertNotEmpty($result['UserCompensationAssociation']);
		$this->assertNotEmpty($result['UserParameter']);
		$this->assertCount(2, $result['UserParameter']);
		$this->assertNotEmpty($result['ResidualParameter']);
		$this->assertCount(2, $result['ResidualParameter']);
		$this->assertNotEmpty($result['ResidualTimeParameter']);
		$this->assertCount(2, $result['ResidualTimeParameter']);
		$this->AssociatedUser->deleteAllAssocData($associatedUserId, $userAssociatedId, $ucpId);

		$result = $UserCompensationProfile->find('first', ['recursive' => 1, 'conditions' => ['UserCompensationProfile.id' => $ucpId]]);
		$result = Hash::filter($result);

		//the only data that should remain is UserCompensationProfile
		$this->assertCount(1, $result);
		$this->assertNotEmpty(Hash::get($result, 'UserCompensationProfile'));
		$this->assertEmpty(Hash::get($result, 'UserCompensationAssociation'));
		$this->assertEmpty(Hash::get($result, 'AttritionRatio'));
		$this->assertEmpty(Hash::get($result, 'CommissionFee'));
		$this->assertEmpty(Hash::get($result, 'UserParameter'));
		$this->assertEmpty(Hash::get($result, 'ResidualParameter'));
		$this->assertEmpty(Hash::get($result, 'ResidualTimeParameter'));
	}
/**
 * testGetUsersByAssociatedUser
 *
 * @return void
 */
	public function testGetUsersByAssociatedUser() {
		$result = $this->AssociatedUser->getUsersByAssociatedUser(AxiaTestCase::USER_SM_ID);
		$expectedContain = ['AssociatedUser', 'User'];
		$this->assertEquals($expectedContain, array_keys(Hash::get($result, '0')));

		$this->assertEquals(AxiaTestCase::USER_SM_ID, Hash::get($result, '0.AssociatedUser.associated_user_id'));
		$this->assertEquals('Sales Manager', Hash::get($result, '0.AssociatedUser.role'));

		$this->assertEquals(AxiaTestCase::USER_REP_ID, Hash::get($result, '0.User.id'));
		$this->assertEquals('Mark Weatherford', Hash::get($result, '0.User.fullname'));
	}
}
