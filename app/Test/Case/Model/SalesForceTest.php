<?php
App::uses('SalesForce', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * SalesForce Test Case
 *
 */
class SalesForceTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ApiConfiguration = ClassRegistry::init('ApiConfiguration');

		$this->sfApiConfig = [
			'configuration_name' => SalesForce::SF_CONFIG_NAME_DEV,
			'instance_url' => 'https://www.fake-SalesForce.com',
			'authorization_url' => 'https://www.fake-auth-SalesForce.com',
			'access_token_url' => 'https://www.fake-token-SalesForce.com',
			'redirect_url' => 'https://www.go-elsewhere-not-salesforce.com',
			'client_id' => 'axiamedsftest123456789',
			'client_secret' => 'axiamedsecrettest123456789',
			'access_token' => 'tucantokentictoc',
			'access_token_lifetime_hours' => '9999999',
			'refresh_token' => 'refresh-tucantokentictoc',
		];
		$this->ApiConfiguration->create();
		$this->ApiConfiguration->save($this->sfApiConfig);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MockedSalesForce);
		parent::tearDown();
	}

/**
 * testGetApiConfig
 *
 * @covers SalesForce::getApiConfig()
 * @return void
 */
	public function testGetApiConfig() {
		$MockedSalesForce = $this->getMockBuilder('SalesForce')
		->disableOriginalConstructor()
		->setMethods(['__construct'])
		->getMock();
		$actual = $MockedSalesForce->getApiConfig();

		$this->assertSame($this->sfApiConfig['instance_url'], $actual['ApiConfiguration']['instance_url']);
		$this->assertSame($this->sfApiConfig['configuration_name'], $actual['ApiConfiguration']['configuration_name']);
	}

/**
 * test_getRefreshTokenBodyStr
 *
 * @covers SalesForce::_getRefreshTokenBodyStr()
 * @return void
 */
	public function test_getRefreshTokenBodyStr() {
		//Create a mocked SF instance with a mocked constructor to avoid calling original constructor
		$MockedSalesForce = $this->getMockBuilder('SalesForce')
		->disableOriginalConstructor()
		->setMethods(['__construct'])
		->getMock();
		$configData = $MockedSalesForce->getApiConfig();
		
		//Create reflection class in order to access protected method we are tesing
		$reflection = new ReflectionClass('SalesForce');
		$method = $reflection->getMethod('_getRefreshTokenBodyStr');
		$method->setAccessible(true);

		//Invoke reflected class private method through the mocked object instance in order to avoid calling the constructor
		$actual = $method->invokeArgs($MockedSalesForce, [&$configData]);
		$expected = 'grant_type=refresh_token&client_id=axiamedsftest123456789&client_secret=axiamedsecrettest123456789&refresh_token=refresh-tucantokentictoc';

		$this->assertSame($expected, $actual);
	}

/**
 * testIsExpiredToken
 *
 * @covers SalesForce::isExpiredToken()
 * @return void
 */
	public function testIsExpiredToken() {
		//Create a mocked SF instance with a mocked constructor to avoid calling original constructor
		$MockedSalesForce = $this->getMockBuilder('SalesForce')
		->disableOriginalConstructor()
		->setMethods(['__construct'])
		->getMock();
		
		$this->assertTrue($MockedSalesForce->isExpiredToken('1000080538819', 1));
		$this->assertFalse($MockedSalesForce->isExpiredToken('1999980538819', 3));
	}

}
