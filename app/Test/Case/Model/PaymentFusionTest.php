<?php
App::uses('PaymentFusion', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * PaymentFusion Test Case
 */
class PaymentFusionTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PaymentFusion = ClassRegistry::init('PaymentFusion');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PaymentFusion);

		parent::tearDown();
	}

/**
 * testGetEditViewData method
 *
 * @return void
 */
	public function testGetEditViewData() {
		$id = CakeText::uuid();
		$merchantId = CakeText::uuid();
		$this->PaymentFusion->save(['id' => $id, 'merchant_id' => $merchantId], ['validate' => false]);
		//get data with truncated bank accounts
		$actual = $this->PaymentFusion->getEditViewData($id);
		$this->assertEquals($id, $actual['PaymentFusion']['id']);
		$this->assertEquals($merchantId, $actual['PaymentFusion']['merchant_id']);
	}

}
