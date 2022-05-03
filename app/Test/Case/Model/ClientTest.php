<?php
App::uses('Client', 'Model');

/**
 * Client Test Case
 */
class ClientTest extends CakeTestCase {
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Client = ClassRegistry::init('Client');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Client);

		parent::tearDown();
	}

/**
 * testGetAutocompleteMatches method
 * test custom validation rule
 * @return void
 */
	public function testGetAutocompleteMatches() {
		$this->Client->saveAll(
			[
				['client_id_global' => '10004321','client_name_global' => 'Test Client Name 1'],
				['client_id_global' => '10004322','client_name_global' => 'Test Client Name 2'],
				['client_id_global' => '10004323','client_name_global' => 'Test Client Name 3'],
			],
			['validate' => false]
		);
		$actual = json_decode($this->Client->getAutocompleteMatches('10004321'), true);
		$expected = "10004321 - Test Client Name 1";
		$this->assertSame($expected, Hash::get($actual, '0.value'));

		$actual = json_decode($this->Client->getAutocompleteMatches('Name 1'), true);
		$this->assertSame($expected, Hash::get($actual, '0.value'));

		$actual = json_decode($this->Client->getAutocompleteMatches('Fake'), true);
		$this->assertEmpty($actual);
	}
/**
 * testIsValidClientId method
 * test custom validation rule
 * @return void
 */
	public function testIsValidClientId() {
		$Client = $this->getMockForModel('Client', ['getSfClientNameByClientId']);
		$Client->expects($this->any())
			->method('getSfClientNameByClientId')
			->will($this->returnValue(false));

		$expected = 'The client id is invalid! It does not match any known id.';
		$actual = $Client->isValidClientId(['tst_val' => '10000004']);
		$this->assertSame($actual, $expected);

		$actual = $this->Client->isValidClientId(['tst_val' => '!numbers']);
		$expected = 'Client id is invalid! Enter a unique 8 digit Client ID that was generated from SalesForce.';
		$this->assertSame($actual, $expected);
	}

/**
 * testGetSfClientNameByClientId method
 * 
 * @expectedException Exception
 * @expectedExceptionMessage Salesforce API connection configuration not found! Cannot connect to salesforce!
 * @return void
 */
	public function testGetSfClientNameByClientId() {
		$this->Client->getSfClientNameByClientId('lorem ipsum');
	}

}
