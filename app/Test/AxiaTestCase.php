<?php
App::uses('AppTestCase', 'Templates.Lib');
App::uses('SystemTransactionListener', 'Lib/Event');

/**
 * Generic test case for the App with utilities
 */
class AxiaTestCase extends AppTestCase {

/**
 * Users with different roles to use on the tests
 */
	const USER_ADMIN_ID = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
	const USER_SM_ID = '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6';
	const USER_REP_ID = '00ccf87a-4564-4b95-96e5-e90df32c46c1';
	const USER_INSTALLER_ID = '113114ae-7777-7777-7777-fa0c0f25c786';
	const USER_PARTNER_REP_ID = '113114ae-8888-8888-8888-fa0c0f25c786';

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

/**
 * Replace the model SystemTransationListener for a mocked one
 *
 * @param Model $model Model instance to mock the listener
 * @param array $options Option parameters
 *	- `listenerKey`: key in Configuration 'ModelEventListeners.{modelName}' where the listener instance is
 *	- `user_id`: current user id
 * @return void
 */
	protected function _mockSystemTransactionListener($model, $options = []) {
		$defaultOptions = [
			'listenerKey' => 'SystemTransactionListener',
			'user_id' => self::USER_ADMIN_ID,
		];
		$options = Hash::merge($defaultOptions, $options);

		$mockedListener = $this->getMockBuilder('SystemTransactionListener')
			->setMethods([
				'_getCurrentUser',
				'_getClientIp'
			])
			->getMock();

		$mockedListener->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue([
				'id' => $options['user_id'],
				'last_login_date' => '2001-01-01 01:01:01',
			]));
		$mockedListener->expects($this->any())
			->method('_getClientIp')
			->will($this->returnValue('test_client_ip'));

		$model->detachListener($options['listenerKey']);
		$model->getEventManager()->attach($mockedListener);
	}

/**
 * Assert array has paths (supports dot-notation).
 *
 * @param array $paths The list of paths to check for.
 * @param array|ArrayAccess $data The data to check.
 * @param string $message The message to display on failure.
 * @return void
 */
	public static function assertArrayHasPaths(array $paths, array $data, $message = '') {
		foreach ($paths as $path) {
			self::assertArrayHasPath($path, $data, $message);
		}
	}

/**
 * Assert array has path (supports dot-notation).
 *
 * @param string $path The path to check for.
 * @param array|ArrayAccess $data The data to check.
 * @param string $message The message to display on failure.
 * @return void
 */
	public static function assertArrayHasPath($path, array $data, $message = '') {
		self::assertTrue(Hash::check($data, $path), $message);
	}

/**
 * Assert that an array contains an array (checks provided keys and values).
 *
 * @param array $expected The array with expected keys and values.
 * @param array $actual The array to check within.
 * @param string $message The message to display on failure.
 * @return void
 */
	public static function assertArrayContainsArray(array $expected, array $actual, $message = '') {
		self::assertEquals($expected, array_intersect_key($actual, $expected), $message);
	}

/**
 * Generates v4 UUIDs for testing
 * 
 * @return string Version 4 UUIDs are pseudo-random.
 */
	public static function UUIDv4() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}
}
