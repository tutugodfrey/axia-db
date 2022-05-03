<?php
App::uses('MerchantUwVolume', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantUwVolume Test Case
 *
 */
class MerchantUwVolumeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantUwVolume = ClassRegistry::init('MerchantUwVolume');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantUwVolume);
		parent::tearDown();
	}

/**
 * testEncryptData
 *
 * @return void
 */
	public function testEncryptData() {
		$data = [
			'MerchantUwVolume' => [
				'te_amex_number' => '123456789',
				'te_diners_club_number' => '123456789',
				'te_discover_number' => '123456789',
				'te_jcb_number' => '123456789'
			]
		];
		$this->MerchantUwVolume->encryptData($data);
		$this->assertNotEqual('123456789', $data['MerchantUwVolume']['te_amex_number']);
		$this->assertNotEqual('123456789', $data['MerchantUwVolume']['te_diners_club_number']);
		$this->assertNotEqual('123456789', $data['MerchantUwVolume']['te_discover_number']);
		$this->assertNotEqual('123456789', $data['MerchantUwVolume']['te_jcb_number']);

		$this->assertTrue($this->MerchantUwVolume->isEncrypted($data['MerchantUwVolume']['te_amex_number']));
		$this->assertTrue($this->MerchantUwVolume->isEncrypted($data['MerchantUwVolume']['te_diners_club_number']));
		$this->assertTrue($this->MerchantUwVolume->isEncrypted($data['MerchantUwVolume']['te_discover_number']));
		$this->assertTrue($this->MerchantUwVolume->isEncrypted($data['MerchantUwVolume']['te_jcb_number']));
	}

/**
 * testGetDataByMerchantId
 *
 * @return void
 */
	public function testGetDataByMerchantId() {
		$result = $this->MerchantUwVolume->getDataByMerchantId('00000000-0000-0000-0000-000000000001');
		$this->assertSame('00000000-0000-0000-0000-000000000001', $result['MerchantUwVolume']['merchant_id']);
	}

/**
 * testIsVolNeeded
 *
 * @return void
 */
	public function testIsVolNeeded() {
		$checkVal = ['val' => 1200];
		$productGrup = null;
		$actual = $this->MerchantUwVolume->isVolNeeded($checkVal, $productGrup);
		$this->assertTrue($actual);

		$data = [ 
			'MerchantUwVolume' => [
				'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa'
			]
		];

		//test error returned
		$this->MerchantUwVolume->set($data);
		$productGrup = 'Visa Sales';
		$actual = $this->MerchantUwVolume->isVolNeeded(['NaN' => ''], $productGrup);

		$this->assertSame('Merchant has Visa Sales! Field is required!', $actual);

		//test retuned true -- validation good
		$this->MerchantUwVolume->set($data);
		$productGrup = 'Payment Fusion';
		$actual = $this->MerchantUwVolume->isVolNeeded(['NaN' => ''], $productGrup);
		$this->assertTrue($actual);
	}
}
