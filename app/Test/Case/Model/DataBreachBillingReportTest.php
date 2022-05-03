<?php
App::uses('DataBreachBillingReport', 'Model');
App::uses('Entity', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * DataBreachBillingReport Test Case
 *
 */
class DataBreachBillingReportTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->DataBreachBillingReport = ClassRegistry::init('DataBreachBillingReport');
		$this->Merchant = ClassRegistry::init('Merchant');
		$this->MerchantPci = ClassRegistry::init('MerchantPci');
		$this->TimelineEntry = ClassRegistry::init('TimelineEntry');
		$this->TimelineItem = ClassRegistry::init('TimelineItem');
		$this->MerchantBank = ClassRegistry::init('MerchantBank');
		$this->Entity = ClassRegistry::init('Entity');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DataBreachBillingReport);
		parent::tearDown();
	}

/**
 * testGetAxiaTechReport
 *
 * @covers DataBreachBillingReport::getAxiaTechReport()
 * @return void
 */
	public function testGetAxiaTechReport() {
		$this->_saveTestData();
		$actual = $this->DataBreachBillingReport->getAxiaTechReport();
		$this->_checkReportTest($actual);
	}

/**
 * testGetReport
 *
 * @covers DataBreachBillingReport::getReport()
 * @return void
 */
	public function testGetReport() {
		$teAppDate = $this->_getTestAppDate(false);

		$conditions = [
			"MerchantPci.insurance_fee is NOT NULL",
			"MerchantPci.insurance_fee != 0",
			"Merchant.active" => 1,
			"TimelineEntry.timeline_date_completed < " . "'$teAppDate'",
			"MerchantCancellation.date_completed IS NULL",
			"Entity.entity_name = 'AxiaMed'"
		];
		$this->_saveTestData();
		$actual = $this->DataBreachBillingReport->getReport($conditions);

		$this->_checkReportTest($actual);
	}

	protected function _checkReportTest($actual) {
		$teAppDate = $this->_getTestAppDate();

		$this->assertSame(['merchant_mid' => '1239123456789876', 'merchant_dba' => 'Test Merchant'], $actual[0]['Merchant']);
		$this->assertSame(['fees_routing_number' => '123456789', 'fees_dda_number' => '123456789'], $actual[0]['MerchantBank']);
		$this->assertEquals(['insurance_fee' => 6.99], $actual[0]['MerchantPci']);
		$this->assertSame(['timeline_item_id' => TimelineItem::APPROVED, 'timeline_date_completed' => $teAppDate], $actual[0]['TimelineEntry']);
	}


	protected function _getTestAppDate($subtaractTwo = true) {
		$monthModifier = ($subtaractTwo)? 2 : 1;
		return date("Y-m", strtotime("-$monthModifier months")) . '-01';
	}


	protected function _saveTestData() {
		
		$merchId = CakeText::uuid();
		$testdata = [
			'Merchant' => [
				'id' => $merchId,
				'user_id' => CakeText::uuid(),
				'merchant_mid' => '1239123456789876',
				'merchant_dba' => 'Test Merchant',
				'active' => 1,
				'entity_id' => Entity::AX_TECH_ID
			],
			'MerchantPci' => [
				'merchant_id' => $merchId,
				'insurance_fee' => 6.99
			],
			'MerchantBank' => [
				'merchant_id' => $merchId,
				'fees_routing_number' => '123456789',
				'fees_dda_number' => '123456789',
			],
			'TimelineEntry' => [
				'merchant_id' => $merchId,
				'timeline_item_id' => TimelineItem::APPROVED,
				'timeline_date_completed' => $this->_getTestAppDate()
			],
			'Entity' => [
				'id' => Entity::AX_TECH_ID,
				'entity_name' => 'AxiaMed'
			],


		];
		$this->Merchant->save($testdata['Merchant'], ['validate' => false]);
		$this->MerchantPci->save($testdata['MerchantPci'], ['validate' => false]);
		$this->MerchantBank->save($testdata['MerchantBank'], ['validate' => false]);
		$this->TimelineEntry->save($testdata['TimelineEntry'], ['validate' => false]);
		$this->Entity->save($testdata['Entity'], ['validate' => false]);
	}
}
