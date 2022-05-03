<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('UsersController', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('OwnerAccessControlComponent', 'Controller/Component');
App::uses('AxiaTestCase', 'Test');
App::uses('AuthComponent', 'Controller/Component');

/**
 * OwnerAccessControlComponent Test Case
 *
 */
class OwnerAccessControlComponentTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$reflection = new ReflectionClass("OwnerAccessControlComponent");
		$this->OwnerAccessControlComponent = $reflection->newInstanceWithoutConstructor();
	}

/**
 * Return a mocked Owner Access Control Component
 *
 * @param string $userId User id
 * @return void
 */
	protected function _setComponentMockedObjects($Controller, $Model, $userId = '') {
		$controller = $this->getMockBuilder($Controller)
			->disableOriginalConstructor()
			->getMock();
		$controller->modelClass = $Model;

		$controller->{$Model} = $this->getMockForModel($Model, array('_getCurrentUser'));
		$controller->request = $this->getMock('CakeRequest');
		$controller->request->action = 'currentAction';

		$this->OwnerAccessControlComponent->controller = $controller;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->OwnerAccessControlComponent);

		parent::tearDown();
	}

/**
 * testIsAuthorizedOwnerExceptionThrown
 *
 * @return void
 */
	public function testIsAuthorizedOwnerExceptionThrown() {
		try{
			$this->OwnerAccessControlComponent->isAuthorizedOwner();
		} catch(BadFunctionCallException $e) {
			$expected = 'OwnerAccessControlComponent::isAuthorized() is not callable from outside a Controller Class';
			$this->assertEqual($expected, $e->getMessage());
		}
	}

/**
 * testUserIsAdmin
 *
 * @return void
 */
	public function testUserIsAdmin() {
		$this->assertTrue($this->OwnerAccessControlComponent->userIsAdmin( AxiaTestCase::USER_ADMIN_ID));
	}
}
