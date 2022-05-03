<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('CustomAuthorize', 'Controller/Component/Auth');
App::uses('AccessControl', 'Rbac.Auth');
App::uses('AxiaTestCase', 'Test');

/**
 * Controller/Component/Auth/CustomAuthorize Test Case
 *
 */
class CustomAuthorizeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->CustomAuthorize = new CustomAuthorize($Collection, array());
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CustomAuthorize);

		parent::tearDown();
	}

/**
 * testAuthRbacDisabled method
 *
 * @return void
 */
	public function testAuthRbacDisabled() {
		$request = new CakeRequest();
		$request['controller'] = 'Testcontroller';
		$request['action'] = 'testaction';
		Configure::write('Rbac.enabled', false);
		$this->assertTrue($this->CustomAuthorize->authorize(array(), $request));

		Configure::write('Rbac.enabled', true);
		$this->assertFalse($this->CustomAuthorize->authorize(array(), $request));

		$mockAccessControl = $this->getMock('AccessControl', array('isPermitted'), array(), '', false);
		$mockAccessControl
				->expects($this->once())
				->method('isPermitted')
				->with($this->equalTo('Testcontroller/testaction'))
				->will($this->returnValue(true));
		$this->CustomAuthorize->AccessControl = $mockAccessControl;
		//check isPermitted called
		//check result is the same isPermitted return
		$result = $this->CustomAuthorize->authorize(array(), $request);
		$this->assertTrue($result);
	}
}
