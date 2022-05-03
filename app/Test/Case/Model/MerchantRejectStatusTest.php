<?php
App::uses('MerchantRejectStatus', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantRejectStatus Test Case
 *
 */
class MerchantRejectStatusTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantRejectStatus = ClassRegistry::init('MerchantRejectStatus');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantRejectStatus);

		parent::tearDown();
	}

/**
 * testListStatusesWithCollectedOpt method
 *
 * @return void
 */
	public function testListStatusesWithCollectedOpt() {
		$result = $this->MerchantRejectStatus->listStatusesWithCollectedOpt();
		$expected = array(
			'a4961985-c384-43df-b03d-b269f560d1f5' => 'Received',
			'4c82df9f-403b-4a8d-83e0-f85ed4b074d5' => 'Re-Submitted',
			'e683ee7e-97d2-4882-a5d5-bd79d5246a9e' => 'Additional Reject',
			'c0a5e7f2-5634-4d8d-81a2-ff43a33abe03' => 'Updated Banking (NOC)',
			'COLLECTED' => array(
				1 => 'Select this to search all COLLECTED',
				'03fcd6ef-726b-40c4-8942-764d8bf6bc46' => 'Re-submitted - Confirmed',
				'dd2123a3-aa3a-4747-bcfb-9b7641e48bc7' => 'Collected - Reserve',
				'c109f205-9d85-445d-8010-c0013b8f6141' => 'Collected - Other'
			),
			'NOT COLLECTED' => array(
				0 => 'Select this to search all NOT COLLECTED',
				'79d18238-e403-4596-a37b-d7d4468795df' => 'On Reserve',
				'afa257c3-9dcb-4661-8946-91805171510c' => 'Re-Rejected',
				'87828851-cde7-4ddc-b0b9-213435caf2e8' => 'Not Collected',
				'505e6e00-eda4-4b0e-b48e-00f1827655ec' => 'Written Off'
			)
		);

		$this->assertEquals($expected, $result);
	}

}
