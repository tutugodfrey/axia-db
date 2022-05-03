<?php
App::uses('MerchantAchReason', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantAchReason Test Case
 *
 * @coversDefaultClass MerchantAchReason
 */
class MerchantAchReasonTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var MerchantAchReason
 */
	public $MerchantAchReason;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantAchReason = ClassRegistry::init('MerchantAchReason');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantAchReason);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers MerchantAchReason::getAchReasonsList()
 * @return void
 */
	public function testGetAchReasonsList() {
		$result = $this->MerchantAchReason->getAchReasonsList();
		$expected = [
			'5ce6cebe-5940-490c-980b-3db10a050733' => 'Account Setup Fee',
			'5ce6ceb1-1418-4ae8-8bfa-3db10a050733' => 'Replacement Shipping',
			'5ce6cea3-bc3c-47e8-a102-3db10a050733' => 'Expedite Fee',
			'5cba5bdc-7778-4b66-8275-13f40a0101f3' => 'Reinjection Fee',
			'5cba5b96-16f8-4f0b-bc8e-78f80a0101f3' => 'ACH Setup',
			'59b8777a-483c-46b7-a6ee-1c7f34627ad4' => 'Application Fee',
			'59b8777a-9ab0-4b39-9255-1c7f34627ad4' => 'Equipment Fee',
			'59b8777a-b890-4ec2-8311-1c7f34627ad4' => 'Supplies Fee (CA - Taxed)',
			'59b8777a-6290-4a70-8fed-1c7f34627ad4' => 'Supplies Fee (Outside CA)',
			'59b8777a-ec60-490a-ab85-1c7f34627ad4' => 'Cancellation Fee',
			'5cdf0bb4-8c14-4fb4-a34b-21fc0a050733' => 'Install',
			'59b8777a-54e4-4fc9-88c5-1c7f34627ad4' => 'Replacement Fees',
			'59b8777a-14b8-4ed8-8393-1c7f34627ad4' => 'Install',
			'59b8777a-6974-4b33-bb72-1c7f34627ad4' => 'Rental Fee (CA - Taxed)',
			'59b8777a-1d34-413a-8876-1c7f34627ad4' => 'Rental Fee (Outside CA)',
			'59b8777a-2be8-4f26-931f-1c7f34627ad4' => 'Gateway/Software Fee',
			'59b8777a-36b4-4004-b906-1c7f34627ad4' => 'Exact Shipping (No Tax)',
			'59b8777a-0e80-4352-bc53-1c7f34627ad4' => 'Reject Fees',
			'5cba5b96-2034-46ca-8e89-78f80a0101f3' => 'ACH Setup',
			'5aecc84f-91d8-4de9-9613-3b2b0a0101f3' => 'Payment Fusion Terminal Setup Fee',
			'59b8777a-8444-4c8a-adfb-1c7f34627ad4' => 'Other',
		];
		$this->assertEquals($expected, $result);
	}
}
