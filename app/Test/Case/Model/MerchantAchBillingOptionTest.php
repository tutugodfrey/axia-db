<?php
App::uses('MerchantAchBillingOption', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantAchBillingOption Test Case
 *
 * @coversDefaultClass MerchantAchBillingOption
 */
class MerchantAchBillingOptionTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var MerchantAchBillingOption
 */
	public $MerchantAchBillingOption;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantAchBillingOption = ClassRegistry::init('MerchantAchBillingOption');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantAchBillingOption);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers MerchantAchBillingOption::getBillingOptionsList()
 * @return void
 */
	public function testGetBillingOptionsList() {
		$result = $this->MerchantAchBillingOption->getBillingOptionsList();
		$expected = [
			'00877702-ac70-4b9e-b3e8-518b651cdde7' => 'Rep',
			'a355e6ca-4167-48cb-8ef0-e8cf94104e99' => 'Bill Rep his percentage',
			'a6b2867a-0e6b-4059-a5d0-ea259baa99e4' => "Don't Bill Rep",
			'5cdca7f1-8248-4a73-90c9-4bd434627ad4' => 'Client',
			'5cdca7f1-53e8-4838-849e-4bd434627ad4' => 'Partner',
		];
		$this->assertEquals($expected, $result);
	}
}
