<?php
App::uses('BaseControllerTestCase', 'Test');

/**
 * LastDepositReportsController Test Case
 *
 */
class BaseAuthControllerTestCase extends BaseControllerTestCase {

	const USER_ID = '00ccf87a-4564-4b95-96e5-e90df32c46c1';

/**
 * Start Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function startTest($method) {
		parent::startTest($method);
	}

/**
 * End Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function endTest($method) {
		parent::endTest($method);
		if (isset($this->{$this->controllerName})) {
			unset($this->{$this->controllerName});
		}
		if (isset($this->controllerName)) {
			unset($this->controllerName);
		}
		ClassRegistry::flush();
	}

	protected function _startAuth($controllerName) {
		$this->controllerName = $controllerName;
		$this->{$controllerName} = $this->generate(
			$controllerName, array(
				'methods' => array(
					'redirect'
				),
				'components' => array(
					'Session',
					'Auth',
					'OwnerAccessControl' => array('isAuthorizedOwner')
				)
			)
		);
		$this->{$controllerName}->constructClasses();
		$this->{$controllerName}->params = array(
			'named' => array(),
			'pass' => array(),
			'url' => array());
		$this->{$controllerName}->OwnerAccessControl
				->expects($this->once())
				->method('isAuthorizedOwner')
				->will($this->returnValue(true));
		$this->_configureAuth($this->{$controllerName}->Auth);
	}
}
