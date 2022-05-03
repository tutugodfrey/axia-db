<?php
App::uses('ControlScan', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ControlScan Test Case
 *
 */
class ControlScanTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ApiConfiguration = ClassRegistry::init('ApiConfiguration');

		$this->csApiConfig = [
			'configuration_name' => 'controlscan sandbox',
			'instance_url' => 'https://www.fake-controlscan.com',
			'authorization_url' => 'https://www.fake-auth-controlscan.com',
			'access_token_url' => 'not_needed',
			'redirect_url' => 'https://www.redirect-fake-controlscan.com',
			'client_id' => 'axiamedclienttest123456789',
			'client_secret' => 'axiamedsecrettest123456789',
			'access_token' => 'not_needed',
			'access_token_lifetime_hours' => '9999999',
			'refresh_token' => 'not_needed',
		];
		$this->ApiConfiguration->create();
		$this->ApiConfiguration->save($this->csApiConfig);
		$this->ControlScan = ClassRegistry::init('ControlScan');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ControlScan);
		parent::tearDown();
	}

/**
 * testSetHost
 *
 * @covers ControlScan::setHost()
 * @return void
 */
	public function testSetHost() {
		$this->ControlScan->setHost();
		$this->assertSame($this->csApiConfig['instance_url'], $this->ControlScan->host);
	}

/**
 * testSetSecretKey
 *
 * @covers ControlScan::setSecretKey()
 * @return void
 */
	public function testSetSecretKey() {
		$this->ControlScan->setSecretKey();
		$this->assertSame($this->csApiConfig['client_secret'], $this->ControlScan->getSecretKey());
	}

/**
 * testSetRequestHeaders
 *
 * @covers ControlScan::setRequestHeaders()
 * @return void
 */
	public function testSetRequestHeaders() {
		$this->ControlScan->setRequestHeaders();
		$expected = [
			'CS-Access-Key' => $this->csApiConfig['client_id'],
			'CS-DateTime' => gmdate("Y-m-d H:i:s")
		];
		$this->assertSame($this->csApiConfig['client_id'], $this->ControlScan->api->config['request']['header']['CS-Access-Key']);
		
		$actualCsDateTimeHeaderParts = date_parse($this->ControlScan->api->config['request']['header']['CS-DateTime']);
		$this->assertEquals(gmdate("Y"), $actualCsDateTimeHeaderParts['year']);
		$this->assertEquals(gmdate("m"), $actualCsDateTimeHeaderParts['month']);
		$this->assertEquals(gmdate("d"), $actualCsDateTimeHeaderParts['day']);
		$this->assertEquals(gmdate("H"), $actualCsDateTimeHeaderParts['hour']);
		$this->assertEquals(gmdate("i"), $actualCsDateTimeHeaderParts['minute']);

		$this->assertNotEmpty($this->ControlScan->api->config['request']['header']['CS-Nonce']);
	}

/**
 * testGetSecretKey
 *
 * @covers ControlScan::getSecretKey()
 * @return void
 */
	public function testGetSecretKey() {
		$this->assertSame($this->csApiConfig['client_secret'], $this->ControlScan->getSecretKey());
	}

/**
 * testGenerateNonce
 *
 * @covers ControlScan::generateNonce()
 * @return void
 */
	public function testGenerateNonce() {
		$actual = $this->ControlScan->generateNonce();
		$this->assertSame(96, strlen($actual));
	}

/**
 * testCreateSignatureParams
 *
 * @covers ControlScan::createSignatureParams()
 * @return void
 */
	public function testCreateSignatureParams() {
		$date = gmdate("Y-m-d H:i:s");
		$headers = [
			'CS-Nonce' => 'nonce',
			'CS-DateTime' => null,
		];
		$params = ['fake' => 'request params'];

		$actual = $this->ControlScan->createSignatureParams($headers, $params);
		$this->assertEmpty($actual);

		$headers['CS-DateTime'] = $date;
		$expected = array_merge($headers, $params);
		$actual = $this->ControlScan->createSignatureParams($headers, $params);
		$this->assertSame($expected, $actual);
	}

/**
 * testSignRequest
 *
 * @covers ControlScan::signRequest()
 * @return void
 */
	public function testSignRequest() {
		$params = ['fake' => 'request params'];
		$secret = 'shhh_secret!';
		$method = 'POST';
		$path = 'fake/api/endpoint/test_signature'; //test_signature generates a header for testing signatures

		$actual = $this->ControlScan->signRequest($params,$secret,$method,$path);
		$expected = '22498879b4182fecea7006705691c5cf5adbcc3f62205357d6e2c543d5270155';
		$this->assertSame($expected, $actual);

		$expected = 'UE9TVC9mYWtlL2FwaS9lbmRwb2ludC90ZXN0X3NpZ25hdHVyZWZha2U9cmVxdWVzdCBwYXJhbXM=';
		$this->assertSame($expected, $this->ControlScan->api->config['request']['header']['Cs-Sig-String']);
	}

/**
 * testGenerateParamString
 *
 * @covers ControlScan::generateParamString()
 * @return void
 */
	public function testGenerateParamString() {
		$params = ['fake' => 'request params'];
		$method = 'POST';
		$path = 'fake/api/endpoint/test_signature'; //test_signature generates a header for testing signatures

		$actual = $this->ControlScan->generateParamString($params,$path,$method);
		$expected = 'POST/fake/api/endpoint/test_signaturefake=request params';
		$this->assertSame($expected, $actual);
	}
}
