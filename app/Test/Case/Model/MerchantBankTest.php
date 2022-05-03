<?php
App::uses('MerchantBank', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantBank Test Case
 *
 */
class MerchantBankTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantBank = ClassRegistry::init('MerchantBank');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantBank);
		parent::tearDown();
	}

/**
 * testGetBankDataByMerchantId
 *
 * @covers MerchantBank::getBankDataByMerchantId()
 * @return void
 */
	public function testGetBankDataByMerchantId() {
		$id = $this->MerchantBank->field('id');
		$this->assertSame(
			$this->MerchantBank->find('first', ['conditions' => ['MerchantBank.merchant_id' => $id]]),
			$this->MerchantBank->getBankDataByMerchantId($id)
		);
	}

/**
 * testBeforeSave
 *
 * @covers MerchantBank::beforeSave()
 * @return void
 */
	public function testBeforeSave() {
		$data = [
			'MerchantBank' => [
				'bank_routing_number' => '123456789',
				'bank_dda_number' => '987654321',
				'fees_routing_number' => '123456789',
				'fees_dda_number' => '987654321',
			]
		];
		$this->MerchantBank->set($data);
		$this->MerchantBank->beforeSave();
		$this->assertTrue($this->MerchantBank->isEncrypted($this->MerchantBank->data['MerchantBank']['bank_routing_number']));
		$this->assertTrue($this->MerchantBank->isEncrypted($this->MerchantBank->data['MerchantBank']['bank_dda_number']));
		$this->assertTrue($this->MerchantBank->isEncrypted($this->MerchantBank->data['MerchantBank']['fees_routing_number']));
		$this->assertTrue($this->MerchantBank->isEncrypted($this->MerchantBank->data['MerchantBank']['fees_dda_number']));
	}

/**
 * testEncryptData
 *
 * @covers MerchantBank::encryptData()
 * @return void
 */
	public function testEncryptData() {
		$data = [
			'MerchantBank' => [
				'bank_routing_number' => '123456789',
				'bank_dda_number' => '987654321',
				'fees_routing_number' => '123456789',
				'fees_dda_number' => '987654321',
			]
		];
		$this->MerchantBank->encryptData($data);
		$this->assertTrue($this->MerchantBank->isEncrypted($data['MerchantBank']['bank_routing_number']));
		$this->assertTrue($this->MerchantBank->isEncrypted($data['MerchantBank']['bank_dda_number']));
		$this->assertTrue($this->MerchantBank->isEncrypted($data['MerchantBank']['fees_routing_number']));
		$this->assertTrue($this->MerchantBank->isEncrypted($data['MerchantBank']['fees_dda_number']));
	}
}
