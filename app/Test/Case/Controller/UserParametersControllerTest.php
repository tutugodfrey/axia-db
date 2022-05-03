<?php
/* UserParameters Test cases generated on: 2014-04-14 15:04:24 : 1397481984*/
App::uses('UserParameters', 'Controller');
App::uses('BaseAuthControllerTestCase', 'Test');
class UserParametersControllerTestCase extends BaseAuthControllerTestCase {

	const USER_ID = '00ccf87a-4564-4b95-96e5-e90df32c46c1';

/**
 * Autoload entrypoint for fixtures dependecy solver
 *
 * @var string
 * @access public
 */
	public $plugin = 'app';

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array();

/**
 * Start Test callback
 *
 * @param string $method method
 * @return void
 * @access public
 */
	public function startTest($method) {
		Configure::write('Rbac.enabled', false);
		parent::startTest($method);
		$this->ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$this->UserParameterType = ClassRegistry::init('UserParameterType');
		$this->MerchantAcquirer = ClassRegistry::init('MerchantAcquirer');
		$this->_startAuth('UserParameters');
	}

/**
 * {@inheritDoc}
 */
	public function endTest($method) {
		Configure::write('Rbac.enabled', true);
	}

/**
 * test
 *
 * @expectedException NotFoundException
 * @return void
 */
	public function testEditManyForUnknownUserShouldThrowException() {
		$this->_configureAuth($this->UserParameters->Auth);
		$this->testAction(
				'/user_parameters/editMany/invalidUser', array('return' => 'vars')
		);
	}

	/**
	 * Parameter type motck
	 *
	 * @return Model mock
	 */
	protected function _getUserParameterTypeMock() {
		$mock = $this->_mockModel('UserParameterType', ['_getAssociatedUsers']);
		$mock
			->expects($this->once())
			->method('_getAssociatedUsers')
			->will($this->returnValue([
					'AssociatedUser' => [
						'00ccf87a-4564-4b95-96e5-e90df32c46c1',
						'00ccf87a-4564-4b95-96e5-e90df32c46c1',
						'00ccf87a-4564-4b95-96e5-e90df32c46c1',
						'00ccf87a-4564-4b95-96e5-e90df32c46c1',
						'00ccf87a-4564-4b95-96e5-e90df32c46c1',
						'00ccf87a-4564-4b95-96e5-e90df32c46c1',
					],
				]));

		return $mock;
	}

/**
 * test
 *
 * @return void
 */
	public function testEditManyGet() {
		$this->UserParameters->UserParameter->UserParameterType = $this->_getUserParameterTypeMock();
		$this->UserParameters->UserParameter->UserCompensationProfile = $this->_mockModel('UserCompensationProfile');
		$this->_configureAuth($this->UserParameters->Auth);
		$viewVars = $this->testAction(
			'/user_parameters/editMany/' . self::USER_ID . '/5570e7fe-5a64-4a93-8724-337a34627ad4',
			[
				'method' => 'get',
				'return' => 'vars'
			]
		);

		$this->assertCount(51, Hash::get($viewVars, 'productsServicesTypes'));
		$this->assertCount(7, Hash::get($viewVars, 'merchantAcquirers'));
		$this->assertCount(10, Hash::get($viewVars, 'userParameterHeaders'));
	}

/**
 * test
 *
 * @return void
 */
	public function testEditManyPostShouldRedirectToViewUserIdOnSuccess() {
		$this->_configureAuth($this->UserParameters->Auth);
		$data = array('UserParameter' => 'some data');
		$UserParameterMock = $this->getMockForModel('UserParameter', array('saveMany'));
		$UserParameterMock->expects($this->once())
				->method('saveMany')
				->with('some data')
				->will($this->returnValue(true));
		$this->UserParameters->UserParameter = $UserParameterMock;
		$expectedRedirect = [
			'controller' => 'users',
			'action' => 'view',
			self::USER_ID,
		];
		$this->expectRedirect($this->UserParameters, $expectedRedirect);

		$this->UserParameters->Session->expects($this->once())
			->method('setFlash')
			->with('UserParameter successfully saved')
			->will($this->returnValue(true));

		$this->testAction(
			'/user_parameters/editMany/' . self::USER_ID . '/5570e7fe-5a64-4a93-8724-337a34627ad4', [
				'data' => $data,
				'method' => 'post',
			]
		);
	}

/**
 * test
 *
 * @return void
 */
	public function testEditManyPostShouldRedirectDisplayFlashOnFail() {
		$this->_configureAuth($this->UserParameters->Auth);
		$data = array('request data, dont care');
		$UserParameterMock = $this->getMockForModel('UserParameter', array('saveMany'));
		$UserParameterMock->expects($this->once())
			->method('saveMany')
			->will($this->returnValue(false));
		$this->UserParameters->UserParameter = $UserParameterMock;
		$this->UserParameters->UserParameter->UserParameterType = $this->_getUserParameterTypeMock();

		$this->UserParameters->Session->expects($this->once())
			->method('setFlash')
			->with('UserParameter could not be saved')
			->will($this->returnValue(true));

		$viewVars = $this->testAction(
			'/user_parameters/editMany/' . self::USER_ID . '/5570e7fe-5a64-4a93-8724-337a34627ad4', [
				'data' => $data,
				'method' => 'post',
				'return' => 'vars'
			]
		);

		$this->assertCount(51, Hash::get($viewVars, 'productsServicesTypes'));
		$this->assertCount(7, Hash::get($viewVars, 'merchantAcquirers'));
		$this->assertCount(10, Hash::get($viewVars, 'userParameterHeaders'));
	}
}
