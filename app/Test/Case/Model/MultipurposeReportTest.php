<?php
App::uses('MultipurposeReport', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MultipurposeReport Test Case
 */
class MultipurposeReportTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MultipurposeReport = ClassRegistry::init('MultipurposeReport');
		$this->SystemTransaction = ClassRegistry::init('SystemTransaction');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MultipurposeReport);

		parent::tearDown();
	}

/**
 * testGetReport method
 *
 * @return void
 */
	public function testGetReport() {
		$conditions = ['Merchant.merchant_mid' => '3948000030002785'];
		$actual = $this->MultipurposeReport->getReport($conditions);
		$this->assertContains('Square One Internet Solutions', $actual);
		$this->assertContains('3948000030002785', $actual);
		$this->assertContains('*****4321', $actual);
		$this->assertContains('****3992', $actual);
	}

/**
 * test_makeCsvString method
 *
 * @return void
 */
	public function test_makeCsvString() {
		$reportData = array(
			array(
                'Client' => array(
                        'name' => null
                ),
                'Merchant' => array(
                        'merchant_mid' => '3948000030002785',
                        'merchant_dba' => 'Square One Internet Solutions',
                        'merchant_ownership_type' => 'Corporation - LLC',
                        'merchant_buslevel' => 'EFTSecure VT Only',
                        'merchant_bustype' => 'BUSINESS SERVICES (NOT ELSEWHE',
                        'merchant_url' => 'www.startatsquareone.com',
                        'merchant_tin' => 'tdGYA+LVUNxE4dHOjlpt6BMfo7javbZWrRQwXzy6gHSmcfFTTeaappKiTaAI29HtP8Vd/vqCDHED4rKj8UUZQA==',
                        'ref_p_type' => null,
                        'ref_p_value' => null,
                        'ref_p_pct' => null,
                        'res_p_type' => null,
                        'res_p_value' => null,
                        'res_p_pct' => null,
                        'merchant_ps_sold' => null,
                        'merchant_contact' => 'Adam',
                        'merchant_sic' => (int) 7399,
                        'merchant_email' => 'adam@startatsquareone.com'
                ),
                'MerchantBank' => array(
                        'bank_dda_number' => null,
                        'bank_routing_number' => null,
                        'fees_dda_number' => null,
                        'fees_routing_number' => null
                ),
                'MerchantPricing' => array(
                        'processing_rate' => '0.4500',
                        'ds_processing_rate' => null,
                        'amex_processing_rate' => null,
                        'mc_vi_auth' => '0.1900',
                        'ds_auth_fee' => null,
                        'amex_auth_fee' => '0.2000',
                        'discount_item_fee' => null
                ),
                'MerchantUwVolume' => array(
                        'mo_volume' => null,
                        'average_ticket' => null,
                        'card_present_swiped' => null,
                        'card_present_imprint' => null,
                        'card_not_present_keyed' => null,
                        'card_not_present_internet' => null,
                        'visa_volume' => null,
                        'mc_volume' => null,
                        'ds_volume' => null,
                        'amex_volume' => null
                ),
                'VisaBet' => array(
                        'name' => '3145'
                ),
                'MasterCardBet' => array(
                        'name' => '3145'
                ),
                'DiscoverBet' => array(
                        'name' => '3145'
                ),
                'AmexBet' => array(
                        'name' => '3145'
                ),
                'User' => array(
                        'user_first_name' => 'Slim',
                        'user_last_name' => 'Pickins'
                ),
                'Manager' => array(
                        'name' => null
                ),
                'Manager2' => array(
                        'name' => null
                ),
                'AddressCorp' => array(
                        'address_title' => null,
                        'address_street' => null,
                        'address_city' => null,
                        'address_state' => null,
                        'address_zip' => null,
                        'address_phone' => null,
                        'address_fax' => null
                ),
                'AddressMail' => array(
                        'address_title' => null,
                        'address_street' => null,
                        'address_city' => null,
                        'address_state' => null,
                        'address_zip' => null,
                        'address_phone' => null,
                        'address_fax' => null
                ),
                'AddressBusiness' => array(
                        'address_street' => null,
                        'address_city' => null,
                        'address_state' => null,
                        'address_zip' => null
                ),
                'MerchantOwner' => array(
                        'list_of_owners' => '"Owner Name 1"~"Owner title 1"~60~*****4321<ER>"Owner Name 2"~"Owner title 2"~40~*****4321'
                ),
                'MerchantAcquirer' => array(
                        'acquirer' => null
                ),
                'MerchantBin' => array(
                        'bin' => null
                ),
                'UwStatusMerchantXref' => array(
                        'datetime' => null
                ),
                'TimelineEntryIns' => array(
                        'timeline_date_completed' => null
                ),
                'TimelineEntryInsComm' => array(
                        'timeline_date_completed' => null
                ),
                'TimelineEntrySub' => array(
                        'timeline_date_completed' => null
                ),
                'TimelineEntryReSiInSh' => array(
                        'timeline_date_completed' => null
                ),
                'SaqControlScan' => array(
                        'pci_compliance' => null
                ),
                'MerchantPci' => array(
                        'insurance_fee' => '6.9500',
                        'saq_completed_date' => null,
                        'saq_type' => null
                ),
                'MerchantCancellation' => array(
                        'date_inactive' => null,
                        'date_completed' => null
                ),
                'Referer' => array(
                        'name' => null
                ),
                'Reseller' => array(
                        'name' => null
                ),
                'LastDepositReport' => array(
                        'last_deposit_date' => null
                ),
                'Network' => array(
                        'network_description' => null
                ),
                'BetNetwork' => array(
                        'name' => null
                ),
                'Partner' => array(
                        'name' => 'Slim Pickins'
                ),
                'Entity' => array(
                        'entity_name' => null
                ),
                'Organization' => array(
                        'name' => null
                ),
                'Region' => array(
                        'name' => null
                ),
                'Subregion' => array(
                        'name' => null
                ),
                'PaymentFusion' => array(
                        'is_hw_as_srvc' => null
                )
	        )
		);
		$reflection = new ReflectionClass('MultipurposeReport');
		$method = $reflection->getMethod('_makeCsvString');
		$method->setAccessible(true);
		$actual = $method->invokeArgs($this->MultipurposeReport, [&$reportData, true]);
		$this->assertContains('Square One Internet Solutions', $actual);
		$this->assertContains('3948000030002785', $actual);
		$this->assertContains('*****4321', $actual);
		$this->assertContains('****3992', $actual);
	}
}
