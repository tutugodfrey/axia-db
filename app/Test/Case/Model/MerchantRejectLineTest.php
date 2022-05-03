<?php
App::uses('MerchantRejectLine', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantRejectLine Test Case
 *
 */
class MerchantRejectLineTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantRejectLine = ClassRegistry::init('MerchantRejectLine');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantRejectLine);

		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testAfterFind() {
		$results = array(
			array(
				'MerchantRejectLine' => array(
					'id' => '55637fb7-d2fc-46ab-910b-26f134627111',
					'fee' => 100.5,
					'status_date' => '2015-05-25',
				)
			)
		);
		$afterFindResults = $this->MerchantRejectLine->afterFind($results, true);
		$this->assertEquals(100.5, Hash::get($afterFindResults, '0.MerchantRejectLine.submitted_amount'));
		$results = array(
			array(
				'MerchantReject' => array('amount' => 100),
				'MerchantRejectLine' => array(
					'id' => '55637fb7-d2fc-46ab-910b-26f134627111',
					'fee' => 100.5,
					'status_date' => '2015-05-25',
				)
			)
		);
		$afterFindResults = $this->MerchantRejectLine->afterFind($results, true);
		$this->assertEquals(200.5, Hash::get($afterFindResults, '0.MerchantRejectLine.submitted_amount'));
		$results = array(
			array(
				'MerchantReject' => array('amount' => 100),
				'MerchantRejectLine' => array(
					'id' => '55637fb7-d2fc-46ab-910b-26f134627111',
					'fee' => 100.5,
					'status_date' => null,
				)
			)
		);
		$afterFindResults = $this->MerchantRejectLine->afterFind($results, true);
		$this->assertNull(Hash::get($afterFindResults, '0.MerchantRejectLine.submitted_amount'));
	}

/**
 * testGetSubmittedAmount method
 *
 * @return void
 */
	public function testGetSubmittedAmount() {
		$amount = null;
		$rejectLine = null;
		$result = $this->MerchantRejectLine->getSubmittedAmount($amount, $rejectLine);
		$this->assertNull($result);
		$amount = 100;
		$rejectLine = null;
		$result = $this->MerchantRejectLine->getSubmittedAmount($amount, $rejectLine);
		$this->assertNull($result);
		$fee = 100;
		$rejectLine = array(
			'status_date' => 'not-null',
			'fee' => $fee
		);
		$result = $this->MerchantRejectLine->getSubmittedAmount($amount, $rejectLine);
		$this->assertEquals($amount + $fee, $result);
	}

/**
 * testDefaultMerchantRejectLine method
 *
 * @return void
 */
	public function testDefaultMerchantRejectLine() {
		$model = $this->_mockModel('MerchantRejectLine');
		$expected = array(
			'merchant_reject_status_id' => 'a4961985-c384-43df-b03d-b269f560d1f5',
			'status_date' => '2015-01-01'
		);
		$result = $model->defaultMerchantRejectLine();
		$this->assertEquals($expected, $result);
	}

}
