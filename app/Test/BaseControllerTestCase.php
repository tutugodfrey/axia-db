<?php
App::import('Lib', 'Templates.AppControllerTestCase');
class BaseControllerTestCase extends AppControllerTestCase {

	const USER_ID = '00ccf87a-4564-4b95-96e5-e90df32c46c1';

/**
 * Users with different roles to use on the tests
 */
	const USER_ADMIN_ID = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
	const USER_SM_ID = '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6';
	const USER_REP_ID = '00ccf87a-4564-4b95-96e5-e90df32c46c1';
	const USER_INSTALLER_ID = '113114ae-7777-7777-7777-fa0c0f25c786';
	const USER_PARTNER_REP_ID = '113114ae-8888-8888-8888-fa0c0f25c786';

	protected function _configureAuth(AuthComponent &$auth) {
		//mock as logged in user
		$user = ClassRegistry::init('User')->find('first');
		$auth->staticExpects($this->any())
				->method('user')
				->will($this->onConsecutiveCalls($user, $user['User']['user_first_name'], $user['User']['user_first_name'], $user));
		$auth->expects($this->any())
				->method('user')
				->with('id')
				->will($this->returnValue($user['User']['id']));
		return $auth;
	}

/**
 * Utility to mock a model and some methods from AppModel by default
 *
 * @param string $modelAlias model alias
 * @param array $methods List of methods to mock
 * @param array $expects Expected value and matcher for the mocked methods
 *	- Example to overwrite '_getCurrentUser' default values
 *	[
 *		'_getCurrentUser' => [
 *			'matcher' => $this->once(),
 *			'value' => 'my-new-custom-user-id',
 *		]
 *	];
 * @return Model|PHPUnit_Framework_MockObject_MockObject Mock for the model alias with some default values
 */
	protected function _mockModel($modelAlias, array $methods = [], array $expects = []) {
		$defaultMethods = ['_getNow', '_getCurrentUser'];
		$methods = array_unique(Hash::merge($defaultMethods, $methods));

		$defaultExpects = [
			'_getNow' => [
				'matcher' => $this->any(),
				'value' => new DateTime('2015-01-01 12:34:56'),
			],
			'_getCurrentUser' => [
				'matcher' => $this->any(),
				'value' => self::USER_REP_ID,
			],
		];
		$expects = Hash::merge($defaultExpects, $expects);

		/** @var Model|PHPUnit_Framework_MockObject_MockObject $model */
		$model = $this->getMockBuilder($modelAlias)
			->disableOriginalConstructor()
			->setMethods($methods)
			->getMock();

		$model->alias = $modelAlias;
		$model->useTable = Inflector::tableize($modelAlias);
		$model->useDbConfig = 'test';
		$model->setDataSource('test');

		foreach ($expects as $methodName => $expect) {
			$model->expects(Hash::get($expect, 'matcher'))
				->method($methodName)
				->will($this->returnValue(Hash::get($expect, 'value')));
		}
		/** @noinspection ImplicitMagicMethodCallInspection */
		$model->__construct();

		return $model;
	}
}
