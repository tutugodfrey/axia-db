<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('CustomAuthenticate', 'Controller/Component/Auth');
App::uses('AxiaTestCase', 'Test');

/**
 * Controller/Component/Auth/CustomAuthenticate Test Case
 *
 */
class CustomAuthenticateTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$settings = array();
		$this->CustomAuthenticate = new CustomAuthenticate($Collection, $settings);
		//Removing the User, so it's re-created again using the test configuration, and not the real DB
		ClassRegistry::removeObject('User');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CustomAuthenticate);

		parent::tearDown();
	}

/**
 * testAuthenticate method
 *
 * @covers CustomAuthenticate::authenticate
 * @return void
 */
	public function testAuthenticate() {
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		//no request data at all
		$request->data = array();
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));

		//no array($fields['username'], $fields['password'] in request->data[Model]
		$request->data = array(
			'User' => array(
				'username' => 'bmcabee',
				//'password' => 'password',
			),
		);
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));

		$request->data = array(
			'User' => array(
				//'username' => 'fail',
				'password' => 'password',
			),
		);
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));

		//username, wrong password
		$request->data = array(
			'User' => array(
				'username' => 'bmcabee',
				'password' => 'wrong password',
			),
		);
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));
		//email, wrong password
		$request->data = array(
			'User' => array(
				'user_email' => 'bmcabee@test.com',
				'password' => 'wrong password',
			),
		);
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));

		//username, password right
		$request->data = array(
			'User' => array(
				'username' => 'bmcabee',
				'password' => 'password',
			),
		);
		$this->assertEquals('bmcabee', Hash::get($this->CustomAuthenticate->authenticate($request, $response), 'username'));

		//email, password right
		$request->data = array(
			'User' => array(
				'username' => 'bmcabee@test.com',
				'password' => 'password',
			),
		);
		$this->assertEquals('bmcabee', Hash::get($this->CustomAuthenticate->authenticate($request, $response), 'username'));

		//scope checks, user inactive
		$request->data = array(
			'User' => array(
				'username' => 'inactive-bmcabee',
				'password' => 'password',
			),
		);
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));

		//scope checks, user blocked
		$request->data = array(
			'User' => array(
				'username' => 'blocked-bmcabee',
				'password' => 'password',
			),
		);
		$this->assertFalse($this->CustomAuthenticate->authenticate($request, $response));
	}
}