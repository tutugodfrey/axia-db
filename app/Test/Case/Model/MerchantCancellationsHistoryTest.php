<?php
App::uses('MerchantCancellationsHistory', 'Model');
App::uses('AxiaTestCase', 'Test');


/**
 * MerchantCancellationsHistory Test Case
 *
 */
class MerchantCancellationsHistoryTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantCancellationsHistory = ClassRegistry::init('MerchantCancellationsHistory');
		$this->MerchantCancellation = ClassRegistry::init('MerchantCancellation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantCancellationsHistory);
		parent::tearDown();
	}
/**
 * testArchivePreviousCancellation method
 */
	public function testArchivePreviousCancellation() {
		$id = '43232fe0-16cd-4f86-a612-a6b83163d20a';
		$savedSuccess = $this->MerchantCancellationsHistory->archivePreviousCancellation($id);
		$this->assertTrue($savedSuccess);

		$actual = $this->MerchantCancellation->find('count', array('conditions' => array('MerchantCancellation.merchant_id' => $id)));
		$this->assertEquals($actual, 0);
	}
}