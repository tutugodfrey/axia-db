<?php
App::uses('SaqMerchant', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * SaqMerchant Test Case
 *
 */
class SaqMerchantTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SaqMerchant = ClassRegistry::init('SaqMerchant');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SaqMerchant);

		parent::tearDown();
	}

/**
 * SaqMerchant::edit
 *
 * @return void
 */
	public function testEdit() {
		$data = [
			'SaqMerchant' => [
				'id' => '00000000-0000-0000-0000-000000000001',
				'id_old' => 1,
				'merchant_id' => '00000000-0000-0000-0000-000000000002',
				'merchant_id_old' => '1',
				'merchant_name' => 'Ray Chen 2',
				'merchant_email' => 'Management@hiehb.com',
				'password' => 'otsI6GVOUbc=',
				'email_sent' => '2011-02-08 15:15:43',
				'billing_date' => '2011-05-20 00:00:00',
				'next_billing_date' => '2012-05-20 00:00:00'
			],
		];

		$this->assertTrue($this->SaqMerchant->edit($data));
		$this->SaqMerchant->contain();
		$saqMerchant = $this->SaqMerchant->findById('00000000-0000-0000-0000-000000000001');

		$this->assertEqual($saqMerchant, $data);

		$this->assertFalse($this->SaqMerchant->save(['SaqMerchant' => ['id' => '00000000-0000-0000-0000-000000000001']]));
		$this->assertFalse($this->SaqMerchant->save([]));
	}
}
