<?php
App::uses('User', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * User Test Case
 *
 */
class UserTest extends AxiaTestCase {

	const USER_COMPENSATION_ID = '5570e7fe-5a64-4a93-8724-337a34627ad4';

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = [];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = $this->getMockForModel('User', [
			'_getCurrentUser'
		]);
		$this->User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->User);
		parent::tearDown();
	}

/**
 * testConstructorFilterArgsVirtualFields
 *
 * @covers User::__construct
 * @covers User::filterArgs property
 * @covers User::virtualFields property
 * @return void
 */
	public function testConstructorFilterArgsVirtualFields() {
		$UserModel = $this->_mockModel('User');
		$expected = [
			'active' => [
					'type' => 'value'
			],
			'search' => [
					'type' => 'query',
					'method' => 'orConditions'
			]
		];
		$this->assertEquals($expected, $UserModel->filterArgs);

		$expected = ['fullname'];
		$this->assertEquals($expected, array_keys($UserModel->virtualFields));
		unset($UserModel);
	}

/**
 * testPwIsValid
 *
 * @covers User::validates
 * @test
 * @return void
 */
	public function testPwIsValid() {
		$data = $this->User->save(
			[
				'id' => $this->User->field('id', ['username' => 'mweatherford']),
				'password' => 'clever password'
			],
			[
				'validate' => false
			]);

		$result = $this->User->pwIsValid($data['User']['id'], 'clever password');
		$this->assertTrue($result);

		$result = $this->User->pwIsValid($data['User']['id'], 'dumb password');
		$this->assertFalse($result);
	}
/**
 * testValidation
 *
 * @covers User::validates
 * @test
 * @return void
 */
	public function testValidation() {
		$data = [];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());

		$data = ['username' => ''];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());

		$data = [
			'username' => 'user',
			'split_commissions' => 'not boolean',
		];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());

		$data = [
			'username' => 'user',
			'bet_extra_pct' => 'not boolean',
		];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());

		$data = [
			'username' => 'user',
			'discover_bet_extra_pct' => 'not boolean',
		];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());

		$data = [
			'username' => 'user',
			'password' => 'does not match',
			'repeat_password' => 'too small',
		];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());
		$this->assertContains('Passwords do not match!', $this->User->validationErrors['repeat_password']);

		$data = [
			'username' => 'user',
			'password' => 'small',
			'repeat_password' => 'small',
		];
		$this->User->create();
		$this->User->set($data);
		$this->assertFalse($this->User->validates());
		$this->assertContains('Passwords should have 8 characters or more', $this->User->validationErrors['repeat_password']);

		//correct
		$data = [
			'user_first_name' => 'first',
			'user_last_name' => 'last',
			'user_email' => 'test@example.com',
			'entity_id' => '00000000-0000-0000-0000-000000000001',
			'username' => 'user',
			'password' => 'Password1!',
			'repeat_password' => 'Password1!',
			'user_first_name' => 'First Name',
			'user_last_name' => 'Last Name',
			'user_email' => 'test@example.com',
			'entity_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
		];

		$this->User->create();
		$this->User->set($data);
		$this->User->validates();
		$this->assertTrue($this->User->validates());
		$this->assertEmpty($this->User->validationErrors);
	}

/**
 * testBeforeSave
 *
 * @covers User::beforeSave
 * @test
 * @return void
 */
	public function testBeforeSave() {
		$data = ['key' => 'val'];
		$this->User->set($data);
		$this->User->beforeSave();
		$this->assertEquals(['User' => $data], $this->User->data);

		$data = ['password' => 'test'];
		$this->User->set($data);
		$this->assertEquals($data['password'], $this->User->data['User']['password']);
		$this->User->beforeSave();
		$this->assertNotEquals($data['password'], $this->User->data['User']['password']);

		$allUsers = $this->User->find('all', [
				'fields' => ['id', 'womply_user_enabled']
			]);

		//Emulate no change the womply_user_enabled
		foreach ($allUsers as $user) {
			$data = $user['User'];
			$this->User->set($data);
			$this->User->beforeSave();
			$this->assertArrayNotHasKey('womply_status_changed', $this->User->data['User']);
		}

		//Emulate change the womply_user_enabled
		foreach ($allUsers as $user) {
			$user['User']['womply_user_enabled'] = !$user['User']['womply_user_enabled'];
			$data = $user['User'];
			$this->User->set($data);
			$this->User->beforeSave();
			$this->assertArrayHasKey('womply_status_changed', $this->User->data['User']);
			$this->assertTrue($this->User->data['User']['womply_status_changed']);
		}
	}

/**
 * testSetExportStructure
 *
 * @covers User::getByRole
 * @test
 * @return void
 */
	public function testSetExportStructure() {
		$original = [
			[
				'User' => [
					'id' => 'e8bee1a8-8160-4b33-b3f2-6b1f68b7438f',
					'user_first_name' => '1st Republic',
					'user_last_name' => 'Bank',
					'username' => 'frbank',
					'initials' => 'FRB',
					'user_email' => 'rclark@axiapayments.com',
					'active' => 0,
					'is_blocked' => false
				],
				'Role' => [
					'name' => 'Rep'
				],
				'Entity' => [
					'entity_name' => 'Axia Payments'
				]
			]
		];
		$expected = [
			[
				'user_last_name' => 'Bank',
				'user_first_name' => '1st Republic',
				'role_name' => 'Rep',
				'username' => 'frbank',
				'initials' => 'FRB',
				'user_email' => 'rclark@axiapayments.com',
				'entity' => 'Axia Payments',
				'active' => 0,
				'is_blocked' => false
			]
		];
		$actual = $this->User->setExportStructure($original);
		$this->assertSame($expected, $actual);

	}

/**
 * testGetByRole
 *
 * @covers User::getByRole
 * @test
 * @return void
 */
	public function testGetByRole() {
		$this->assertEmpty($this->User->getByRole());
		$usersByRole = $this->User->getByRole('Sales Manager');
		$expected = [
			AxiaTestCase::USER_SM_ID => 'Bill McAbee',
			'd2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'Frank Williams',
			'00000000-0000-0000-0000-000000000010' => 'Ben Franklin'
		];
		$this->assertEquals($expected, $usersByRole);
	}

/**
 * testGetRoleNames
 *
 * @covers User::getRoleNames
 * @test
 * @return void
 */
	public function testGetRoleNames() {
		$expected = [
			'Sales Manager' => 'Sales Manager',
			'Installer' => 'Installer',
			'Partner Rep' => 'Partner Rep',
			'Rep' => 'Rep',
			'Partner' => 'Partner',
			'Referrer' => 'Referrer',
			'Reseller' => 'Reseller',
			'Account Manager' => 'Account Manager'
		];
		$this->assertEquals($expected, $this->User->getRoleNames());
	}

/**
 * testGetPermissionsByRole
 *
 * @param string $roleGroup Role group
 * @param mixed $expected Expected result
 * @dataProvider providerGestPermissionsByRole
 * @return void
 */
	public function testGetPermissionsByRole($roleGroup, $expected) {
		$this->assertEquals($expected, $this->User->getPermissionsByRole($roleGroup));
	}

/**
 * Povider for testGetPermissionsByRole
 *
 * @return array
 */
	public function providerGestPermissionsByRole() {
		return [
			[null, []],
			[User::ROLE_GROUP_ADMIN, []],
			[User::ROLE_GROUP_REP, ['Rep' => 'Rep']],
			[User::ROLE_GROUP_SM, ['SM' => 'SM', 'SM2' => 'SM2']],
		];
	}

/**
 * testViewNotFound
 *
 * @covers User::view
 * @test
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testViewNotFound() {
		$this->User->view('00000000-9999-0000-0000-000000000001');
	}

/**
 * testView
 *
 * @covers User::view
 * @test
 * @return void
 */
	public function testView() {
		$user = $this->User->view(AxiaTestCase::USER_ADMIN_ID);
		$keys = array_keys($user);
		$expected = [
			'User',
			'Entity',
			'UsersRole',
			'PermissionConstraint',
			'AssociatedUser',
			'SalesGoal'
		];

		$this->assertEquals($expected, $keys);
	}

/**
 * testGetAssociatedUsersUserParameters
 *
 * @covers User::getAssociatedUsersUserParameters
 * @test
 * @return void
 */
	public function testGetAssociatedUsersUserParameters() {
		$associatedUsers = $this->User->getAssociatedUsersUserParameters(AxiaTestCase::USER_ADMIN_ID, self::USER_COMPENSATION_ID);
		$expectedFirst = [
			'AssociatedUser' => [
				'associated_user_id' => AxiaTestCase::USER_ADMIN_ID
			]
		];
		$this->assertEquals($expectedFirst, $associatedUsers[0]);
		$this->assertCount(1, $associatedUsers);
	}

/**
 * testGetAssociatedUsers
 *
 * @covers User::getAssociatedUsers
 * @return void
 */
	public function testGetAssociatedUsers() {
		$result = $this->User->getAssociatedUsers(AxiaTestCase::USER_REP_ID, '00000000-9999-0000-0000-000000000001');
		$this->assertEmpty($result);

		$result = $this->User->getAssociatedUsers(AxiaTestCase::USER_REP_ID, self::USER_COMPENSATION_ID);
		$expected = $this->User->AssociatedUser->find('count', [
			'conditions' => [
				'user_id' => AxiaTestCase::USER_REP_ID,
				'user_compensation_profile_id' => self::USER_COMPENSATION_ID
			]
		]);
		$this->assertCount($expected, $result);
		$this->assertEquals('bmcabee', Hash::get($result, '0.UserAssociated.username'));

		$result = $this->User->getAssociatedUsers(
			AxiaTestCase::USER_REP_ID,
			self::USER_COMPENSATION_ID,
			['Sales Manager']
		);
		$this->assertCount(2, $result);
		$this->assertEquals('bmcabee', Hash::get($result, '0.UserAssociated.username'));

		$result = $this->User->getAssociatedUsers(
			AxiaTestCase::USER_REP_ID,
			self::USER_COMPENSATION_ID,
			['Sales Manager'],
			['SM2']
		);
		$this->assertEmpty($result);
	}

/**
 * testGetBreadcrumbUserInfo
 *
 * @covers User::getBreadcrumbUserInfo
 * @test
 * @return void
 */
	public function testGetBreadcrumbUserInfo() {
		$userInfo = $this->User->getBreadcrumbUserInfo(AxiaTestCase::USER_REP_ID);
		$expected = [
			'User' => [
				'id' => AxiaTestCase::USER_REP_ID,
				'user_first_name' => 'Mark',
				'user_last_name' => 'Weatherford'
			]
		];
		$this->assertEquals($expected, $userInfo);
	}

/**
 * testGetEntityManagerUsersList
 *
 * @covers User::getEntityManagerUsersList
 * @covers User::_addComplexIdOption
 * @test
 * @return void
 */
	public function testGetEntityManagerUsersList() {
		$expected = [
			'Select all' => [
						'select_all:select_all' => 'All Entities/Users '
				],
			'Entity 1' => [
						'entity_id:00000000-0000-0000-0000-000000000001' => 'Entity 1 '
				],
			'Bill McAbee' => [
					'parent_user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (All in this group)',
					'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee ',
					'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'Mark Weatherford (Rep)',
			],
			'No Entity' => [
					'entity_id:' => 'No Entity '
			],
			'No Manager' => [
					'parent_user_id:' => 'No Manager (All in this group)',
					'user_id:00000000-0000-0000-0000-000000000010' => 'Ben Franklin (SM)',
					'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (SM)',
					'user_id:d2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'Frank Williams (SM2)',
					'user_id:113114ae-7777-7777-7777-fa0c0f25c786' => 'Mr Installer (Installer)',
					'user_id:113114ae-9999-9999-9999-fa0c0f25c786' => 'No Profile Partner Rep (PartnerRep)',
					'user_id:32165df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Slim Pickins (PartnerRep)',
					'user_id:113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep (PartnerRep)',
			],
			'Referrers/Resellers' => [
				'user_id:00000000-0000-0000-0000-000000000005' => 'Bill McAbee blocked (Referrer)'
			]
		];
		$this->assertEquals($expected, $this->User->getEntityManagerUsersList());

		$UserModel = $this->getMockForModel('User', ['_getCurrentUser']);
		$UserModel->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_SM_ID));

		$expected = [
			'Bill McAbee' => [
				'parent_user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (All in this group)',
				'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee ',
				'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'Mark Weatherford (Rep)'
			],
			'No Manager' => [
					'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (SM)'
			]
		];
		$this->assertEquals($expected, $UserModel->getEntityManagerUsersList(true));

		//Test adding/assigning one more user under this manager
		$expected['Bill McAbee'] = [
			'parent_user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee (All in this group)',
			'user_id:003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'Bill McAbee ',
			'user_id:00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'Mark Weatherford (Rep)',
			'user_id:d2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'Frank Williams (SM2)' //<<< expected new one
		];
		$AssociatedUser = ClassRegistry::init('AssociatedUser');
		$AssociatedUser->save(
			[
				'user_id' => 'd2b7550c-d761-40b7-a769-ca1cf2ac9332', //Frank Williams
				'associated_user_id' => '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6', //Assigning Bill McAbee to frank as the manager
				'role' => $UserModel->userRoles['roles'][User::ROLE_GROUP_SM]['label'],
				'permission_level' => User::ROLE_SM,
				'user_compensation_profile_id' => CakeText::uuid() //arbitrary
			]
		);

		$this->assertEquals($expected, $UserModel->getEntityManagerUsersList(true));
	}

/**
 * testGetEntityManagerUsersListRepUser
 *
 * @covers User::getEntityManagerUsersList
 * @covers User::_addComplexIdOption
 * @test
 * @return void
 */
	public function testGetEntityManagerUsersListRepUser() {
		$repUserId = AxiaTestCase::USER_REP_ID;
		$TempModel = $this->getMockForModel('User', [
			'_getCurrentUser'
		]);
		$TempModel->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($repUserId));

		$expected = [
			'Mark Weatherford' => [
				"user_id:{$repUserId}" => 'Mark Weatherford (Rep)',
			]
		];

		$this->assertEquals($expected, $TempModel->getEntityManagerUsersList(true));

		//Test adding/assigning one more user under this manager and emulate this new user the user being currently logged in
		$repUserId = 'd2b7550c-d761-40b7-a769-ca1cf2ac9332'; //Frank Williams
		$TempModel = $this->getMockForModel('User', [
			'_getCurrentUser'
		]);
		$TempModel->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($repUserId));

		$expected = [
			'No Manager' => [
				"user_id:{$repUserId}" => 'Frank Williams (SM2)',
			]
		];
		$AssociatedUser = ClassRegistry::init('AssociatedUser');
		$AssociatedUser->save(
			[
				'user_id' => 'd2b7550c-d761-40b7-a769-ca1cf2ac9332', //Frank Williams
				'associated_user_id' => '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6', //Assigning Bill McAbee to frank as the manager
				'role' => $TempModel->userRoles['roles'][User::ROLE_GROUP_SM]['label'],
				'permission_level' => User::ROLE_SM,
				'user_compensation_profile_id' => CakeText::uuid() //arbitrary
			]
		);

		$this->assertEquals($expected, $TempModel->getEntityManagerUsersList(true));
		unset($TempModel);
	}

/**
 * testGetPrefixes
 *
 * @covers User::getPrefixes
 * @test
 * @return void
 */
	public function testGetPrefixes() {
		$expected = [
			'entity_id' => __('Entity'),
			'parent_user_id' => __('Parent user'),
			'user_id' => __('User'),
			'select_all' => __('All Entities/Users'),
		];
		$this->assertEquals($expected, $this->User->getPrefixes());
	}

/**
 * testIsValidPrefix
 *
 * @covers User::isValidPrefix
 * @test
 * @return void
 */
	public function testIsValidPrefix() {
		$expected = [
			'entity_id' => __('Entity'),
			'parent_user_id' => __('Parent user'),
			'user_id' => __('User'),
		];
		$prefixes = $this->User->getPrefixes();
		foreach ($prefixes as $prefix => $name) {
			$this->assertTrue($this->User->isValidPrefix($prefix));
		}

		$this->assertFalse($this->User->isValidPrefix('not-valid'));
	}

/**
 * testNotValidPrefix
 *
 * @covers User::isValidPrefix
 * @test
 * @return void
 */
	public function testNotValidPrefix() {
		$this->assertFalse($this->User->isValidPrefix('not-valid'));
	}

/**
 * testBuildComplexId
 *
 * @covers User::buildComplexId
 * @test
 * @return void
 */
	public function testBuildComplexId() {
		$this->assertEquals('user_id:user-1', $this->User->buildComplexId(User::PREFIX_USER, 'user-1'));
		$this->assertEquals('parent_user_id:user-2', $this->User->buildComplexId(User::PREFIX_PARENT, 'user-2'));
		$this->assertEquals('entity_id:user-3', $this->User->buildComplexId(User::PREFIX_ENTITY, 'user-3'));
	}

/**
 * testBuildComplexIdNotValid
 *
 * @covers User::buildComplexId
 * @test
 * @expectedException OutOfBoundsException
 * @return void
 */
	public function testBuildComplexIdNotValid() {
		$this->User->buildComplexId('bad-prefix', 'worse-id');
	}

/**
 * testGetUserIndexInfo
 *
 * @return void
 */
	public function testGetUserIndexInfo() {
		$result = $this->User->getUserIndexInfo();
		$expected = [
			'fields' => [
				'id',
				"string_agg(\"Role\".\"name\", ', ')  AS \"Role__name\"",
				'Entity.entity_name',
				'user_first_name',
				'user_last_name',
				'username',
				'initials',
				'user_email',
				'active',
				'is_blocked'
			],
			'recursive' => -1,
			'joins' => [
				[
					'table' => 'users_roles',
					'alias' => 'UsersRole',
					'type' => 'LEFT',
					'conditions' => ['User.id = UsersRole.user_id']
				],
				[
					'table' => 'roles',
					'alias' => 'Role',
					'type' => 'LEFT',
					'conditions' => ['UsersRole.role_id = Role.id']
				],
				[
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => ['User.entity_id = Entity.id']
				]
			],
			'order' => [
				'user_first_name' => 'ASC'
			],
			'group' => [
				'User.id',
				'Entity.entity_name',
			],
			'limit' => 30
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testOrConditions
 *
 * @return void
 */
	public function testOrConditions() {
		$result = $this->User->orConditions(['search' => 'User 1']);
		$expected = [
			'OR' => [
				'Role.name ILIKE' => '%User 1%',
				'User.user_first_name ILIKE' => '%User 1%',
				'User.user_last_name ILIKE' => '%User 1%',
				'User.username ILIKE' => '%User 1%',
				'User.user_email ILIKE' => '%User 1%',
				'User.fullname ILIKE' => '%User 1%'
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->User->orConditions(['search' => 'User 2']);
		$this->assertEquals('%User 2%', $result['OR']['Role.name ILIKE']);
		$this->assertEquals('%User 2%', $result['OR']['User.user_email ILIKE']);

		$result = $this->User->orConditions([]);
		$this->assertEquals('%%', $result['OR']['User.user_first_name ILIKE']);
		$this->assertEquals('%%', $result['OR']['User.user_last_name ILIKE']);
	}

/**
 * testFindUserByUsername
 *
 * @param string $search Search string
 * @param bool $active Filter users by active status
 * @param array $expected Expected result
 * @dataProvider providerFindUserByUsername
 * @return void
 */
	public function testFindUserByUsername($search, $active, $expected) {
		$result = $this->User->findUserByUsername($search, $active);
		$this->assertEquals($expected, $result);
	}

/**
 * Provider for testFindUserByUsername
 *
 * @dataProvider
 * @return void
 */
	public function providerFindUserByUsername() {
		return [
			[
				'bee',
				1,
				[
					'003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6' => 'bmcabee',
					'00000000-0000-0000-0000-000000000005' => 'blocked-bmcabee'
				]
			],
			[ 'bee', 0, ['00000000-0000-0000-0000-000000000004' => 'inactive-bmcabee']],
			[ 'ford', 1, ['00ccf87a-4564-4b95-96e5-e90df32c46c1' => 'mweatherford']],
		];
	}

/**
 * testRoleIsNotValid
 *
 * @param string $userId User id
 * @param string|array $roles User roles to check
 * @dataProvider providerRoleIsNotValid

 * @return void
 */
	public function testRoleIsNotValid($userId, $roles) {
		try {
			$this->User->roleIs($userId, $roles);
		} catch (InvalidArgumentException $e){
			$this->assertEquals($e->getMessage(), 'User::role method is missing required parameters');
		}
	}

/**
 * Provider for testRoleIsNotValid
 *
 * @dataProvider
 * @return void
 */
	public function providerRoleIsNotValid() {
		return [
			[null, null],
			[null, User::ROLE_ADMIN_I],
			[AxiaTestCase::USER_ADMIN_ID, null],
		];
	}

/**
 * testRoleIsNotValid
 *
 * @param string $userId User id
 * @param string|array $roles User roles to check
 * @param bool $expected Expected result
 * @dataProvider providerRoleIs
 * @return void
 */
	public function testRoleIs($userId, $roles, $expected) {
		$result = $this->User->roleIs($userId, $roles);
		$this->assertEquals($result, $expected);
	}

/**
 * Provider for testRoleIsNotValid
 *
 * @dataProvider
 * @return void
 */
	public function providerRoleIs() {
		return [
			[AxiaTestCase::USER_ADMIN_ID, User::ROLE_ADMIN_I, true],
			[AxiaTestCase::USER_ADMIN_ID, User::ROLE_SM, false],
			[AxiaTestCase::USER_SM_ID, User::ROLE_ADMIN_I, false],
			[AxiaTestCase::USER_SM_ID, User::ROLE_SM, true],
			[AxiaTestCase::USER_REP_ID, [User::ROLE_ADMIN_I, User::ROLE_REP], true],
			[AxiaTestCase::USER_REP_ID, User::ROLE_ADMIN_I, false],
			[AxiaTestCase::USER_REP_ID, User::ROLE_REP, true],
		];
	}

/**
 * testGetPartnerReps
 *
 * @return void
 */
	public function testGetPartnerReps() {
		$expected = [
			'113114ae-8888-8888-8888-fa0c0f25c786' => 'The Best Partner Rep',
			'32165df7-6b97-4f86-9e6f-8638eb30cd9e' => 'Slim Pickins'
		];

		$result = $this->User->getPartnerReps(AxiaTestCase::USER_PARTNER_REP_ID);
		$this->assertEquals($expected, $result);
		$result = $this->User->getPartnerReps(AxiaTestCase::USER_REP_ID);
		$this->assertEquals($expected, $result);
	}

/**
 * testExtractComplexIdNotValidPrefix
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid complex user id prefix
 * @return void
 */
	public function testExtractComplexIdNotValidPrefix() {
		$this->User->extractComplexId('invalid-prefix:' . AxiaTestCase::USER_ADMIN_ID);
	}

/**
 * testExtractComplexId
 *
 * @param string $complexId Complex User id
 * @param array $expected Expected result
 * @dataProvider providerExtractComplexId
 * @return void
 */
	public function testExtractComplexId($complexId, $expected) {
		$result = $this->User->extractComplexId($complexId);
		$this->assertEquals($expected, $result);
	}

/**
 * Provider for testExtractComplexId
 *
 * @dataProvider
 * @return void
 */
	public function providerExtractComplexId() {
		return [
			[null, ['prefix' => null, 'id' => null]],
			[
				'user-id-1',
				['prefix' => null, 'id' => 'user-id-1']
			],
			[
				User::PREFIX_USER . ':user-id-2',
				['prefix' => User::PREFIX_USER, 'id' => 'user-id-2']
			],
			[
				User::PREFIX_PARENT . ':user-id-3',
				['prefix' => User::PREFIX_PARENT, 'id' => 'user-id-3']
			],
			[
				User::PREFIX_ENTITY . ':entity-id-1',
				['prefix' => User::PREFIX_ENTITY, 'id' => 'entity-id-1']
			],
		];
	}

/**
 * testGetRelatedUsersByComplexId
 *
 * @param string $complexId User commplex id
 * @param mixed $expected Expected result
 * @covers User::getRelatedUsersByComplexId
 * @dataProvider providerGetRelatedUsersByComplexId
 * @return void
 */
	public function testGetRelatedUsersByComplexId($complexId, $expected) {
		$result = $this->User->getRelatedUsersByComplexId($complexId);
		$this->assertEquals($expected, $result);
	}

/**
 * Provider for testGetRelatedUsersByComplexId
 *
 * @covers User::getRelatedUsersByComplexId
 * @dataProvider
 * @return void
 */
	public function providerGetRelatedUsersByComplexId() {
		return [
			['entity_id:',
				[
					'548bce29-9c0d-42e0-b46c-179151ca54b4' => '548bce29-9c0d-42e0-b46c-179151ca54b4',
					'1d58a4cd-664a-4c08-9b38-94ffc7e61afa' => '1d58a4cd-664a-4c08-9b38-94ffc7e61afa'
				]
			],
			[
				'entity_id:00000000-0000-0000-0000-000000000001',
				[AxiaTestCase::USER_REP_ID => AxiaTestCase::USER_REP_ID]
			],
			['parent_user_id:' . AxiaTestCase::USER_ADMIN_ID, []],
			[
				'parent_user_id:' . AxiaTestCase::USER_SM_ID,
				[
					AxiaTestCase::USER_REP_ID => AxiaTestCase::USER_REP_ID,
					'00000000-0000-0000-0000-000000000004' => '00000000-0000-0000-0000-000000000004',
					'00000000-0000-0000-0000-000000000005' => '00000000-0000-0000-0000-000000000005',
				]
			],
			[
				'parent_user_id:',
				[
					AxiaTestCase::USER_INSTALLER_ID => AxiaTestCase::USER_INSTALLER_ID,
					'00000000-0000-0000-0000-000000000010' => '00000000-0000-0000-0000-000000000010',
					'54365df7-6b97-4f86-9e6f-8638eb30cd9e' => '54365df7-6b97-4f86-9e6f-8638eb30cd9e',
					'32165df7-6b97-4f86-9e6f-8638eb30cd9e' => '32165df7-6b97-4f86-9e6f-8638eb30cd9e',
					'43265df7-6b97-4f86-9e6f-8638eb30cd9e' => '43265df7-6b97-4f86-9e6f-8638eb30cd9e',
					'00000000-0000-0000-0000-000000000014' => '00000000-0000-0000-0000-000000000014',
					'd2b7550c-d761-40b7-a769-ca1cf2ac9332' => 'd2b7550c-d761-40b7-a769-ca1cf2ac9332',
					'548bce29-9c0d-42e0-b46c-179151ca54b4' => '548bce29-9c0d-42e0-b46c-179151ca54b4',
					'1d58a4cd-664a-4c08-9b38-94ffc7e61afa' => '1d58a4cd-664a-4c08-9b38-94ffc7e61afa'
				]
			],
			[
				'user_id:' . AxiaTestCase::USER_REP_ID,
				[AxiaTestCase::USER_REP_ID],
			],
		];
	}

/**
 * testIsAdmin
 *
 * @return void
 */
	public function testIsAdmin() {
		$this->assertTrue($this->User->isAdmin(AxiaTestCase::USER_ADMIN_ID));
		$this->assertFalse($this->User->isAdmin(AxiaTestCase::USER_SM_ID));
		$this->assertFalse($this->User->isAdmin(AxiaTestCase::USER_REP_ID));
	}

/**
 * testGetRoles
 *
 * @return void
 */
	public function testGetRoles() {
		$result = $this->User->getRoles();
		$this->assertCount(13, $result);
		$this->assertEquals('Admin I', Hash::get($result, User::ROLE_ADMIN_I));
		$this->assertEquals('Sales Manager 2', Hash::get($result, User::ROLE_SM2));
		$this->assertEquals('Referrer', Hash::get($result, User::ROLE_REFERRER));
	}

/**
 * testNeedsPasswordRehash
 *
 * @return void
 */
	public function testNeedsPasswordRehash() {
		$this->assertTrue($this->User->needsPasswordRehash(AxiaTestCase::USER_SM_ID));
		$this->assertFalse($this->User->needsPasswordRehash(AxiaTestCase::USER_REP_ID));
	}

/**
 * testRehashPassword
 *
 * @return void
 */
	public function testRehashPassword() {
		$userId = AxiaTestCase::USER_SM_ID;
		$result = $this->User->field('password', ['User.id' => $userId]);
		$expected = '078cf6a4f62531d8a3d1f389f10e9dff0e106351';
		$this->assertEquals($expected, $result);
		$this->User->rehashPassword($userId, 5);
		$result = $this->User->field('password', ['User.id' => $userId]);
		$this->assertEquals('$2y$10$DYhG93b0qyJfen3ieNT83e9cqkFv25jHy8W1O10idPqc9cj7GQS6O', $result);
	}

/**
 * test
 *
 * @param string $prefix Complex id prefix
 * @param string $userId User/Entity id
 * @param string $expectedName Expected User/Entity name
 * @return void
 * @covers User::getUserNameByComplexId()
 * @dataProvider providerTestGetUserNameByComplexId
 */
	public function testGetUserNameByComplexId($prefix, $userId, $expectedName) {
		$complexId = $this->User->buildComplexId($prefix, $userId);
		$this->assertSame($expectedName, $this->User->getUserNameByComplexId($complexId));
	}

/**
 * Provider for testGetUserNameByComplexId
 *
 * @dataProvider
 * @return void
 */
	public function providerTestGetUserNameByComplexId() {
		return [
			[User::PREFIX_ENTITY, '00000000-0000-0000-0000-000000000001', 'Entity 1'],
			[User::PREFIX_PARENT, '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6', 'McAbee Group'],
			[User::PREFIX_USER, '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6', 'Bill McAbee'],
		];
	}

/**
 * testWomplyEnabled
 *
 * @return void
 */
	public function testWomplyEnabled() {
		$users = $this->User->find('list', ['fields' => ['id', 'womply_user_enabled']]);

		foreach ($users as $id => $expected) {
			$result = $this->User->womplyEnabled($id);
			$this->assertfalse($result);
			$this->assertEquals($expected, $result);
		}
	}

/**
 * testUpdateLastApiRequest
 *
 * @return void
 */
	public function testUpdateLastApiRequest(){
		$id = $this->User->field('id'); //retrieve any existing user id
		$this->assertTrue($this->User->updateLastApiRequest($id));
		$this->assertNotEmpty($this->User->field('api_last_request', ['id' => $id]));
	}

/**
 * testRevokeApiAccess
 *
 * @return void
 */
	public function testRevokeApiAccess() {
		$id = $this->User->field('id'); //retrieve any existing user id
		$this->User->save([
			'id' => $id,
			'access_token' => 'arcadetoken',
			'api_password' => 'secret12345'
		],['validate' => false]);

		$this->assertTrue($this->User->revokeApiAccess($id));
		$this->assertEmpty($this->User->field('api_password', ['id' => $id]));
		$this->assertEmpty($this->User->field('access_token', ['id' => $id]));
	}

/**
 * testGetSimilarNames
 *
 * @return void
 */
	public function testGetSimilarNames() {
		$expected = array(
		 array(
			'User' => array(
					'id' => '00000000-0000-0000-0000-000000000004',
					'user_first_name' => 'Bill',
					'user_last_name' => 'McAbee inactive',
					'active' => 0
			)
		 ),
		 array(
			'User' => array(
					'id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
					'user_first_name' => 'Mark',
					'user_last_name' => 'Weatherford',
					'active' => 1
			)
		 ));
		
		$actual = $this->User->getSimilarNames(User::ROLE_REP);
		$this->assertSame($expected, $actual);

		$actual = $this->User->getSimilarNames(User::ROLE_REP, 'Bill');
		unset($expected[1]);
		$this->assertSame($expected, $actual);
		$actual = $this->User->getSimilarNames(User::ROLE_REP, 'Mark Weatherford');
		$expected = array(
		 array(
			'User' => array(
					'id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
					'user_first_name' => 'Mark',
					'user_last_name' => 'Weatherford',
					'active' => 1
			)
		 ));
		$this->assertSame($expected, $actual);

		$actual = $this->User->getSimilarNames(User::ROLE_REP, 'Weatherford Mark');
		$expected = array(
		 array(
			'User' => array(
					'id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
					'user_first_name' => 'Mark',
					'user_last_name' => 'Weatherford',
					'active' => 1
			)
		 ));
		$this->assertSame($expected, $actual);

		$actual = $this->User->getSimilarNames(User::ROLE_REP, null, 1);
		$this->assertSame($expected, $actual);
	}

/**
 * testToggleBlockUser
 *
 * @return void
 */
	public function testToggleBlockUser() {
		$notBLockedUser = $this->User->find('first', array('conditions' => array('is_blocked' => false))); //retrieve any unblocked user
		$this->User->toggleBlockUser($notBLockedUser['User']['id'], true);
		$this->assertTrue($this->User->hasAny(array('is_blocked' => true, 'id' => $notBLockedUser['User']['id'])));

		$this->User->toggleBlockUser($notBLockedUser['User']['id'], false);
		$this->assertFalse($this->User->hasAny(array('is_blocked' => true, 'id' => $notBLockedUser['User']['id'])));
		
	}

/**
 * testTrackIncorrectLogIn
 *
 * @return void
 */
	public function testTrackIncorrectLogIn() {

		for ($x = 1; $x < 7; $x ++) {
			$curentCount = $this->User->trackIncorrectLogIn('apartner@axia-eft.com', false);
			$this->assertEquals($curentCount, $x);
		}		
		$this->assertTrue($this->User->hasAny(array('is_blocked' => true, 'user_email' => 'apartner@axia-eft.com', 'pw_reset_hash IS NOT NULL')));
		
		$this->User->trackIncorrectLogIn('apartner@axia-eft.com', true);
		$this->assertTrue($this->User->hasAny(array('is_blocked' => false, 'user_email' => 'apartner@axia-eft.com', 'wrong_log_in_count' => 0)));
	}

}
