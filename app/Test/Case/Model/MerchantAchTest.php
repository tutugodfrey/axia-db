<?php
App::uses('MerchantAch', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('GUIbuilderComponent', 'Controller/Component');
App::uses('MerchantAchReason', 'Model');

/**
 * MerchantAch Test Case
 *
 * @coversDefaultClass MerchantAch
 */
class MerchantAchTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var MerchantAch
 */
	public $MerchantAch;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantAch = ClassRegistry::init('MerchantAch');
		$this->_mockSystemTransactionListener($this->MerchantAch);
		$this->User = $this->getMockForModel('User', [
			'_getCurrentUser'
		]);
		$this->User->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantAch);

		parent::tearDown();
	}

/**
 * testCreateInvoiceNum
 *
 * @return void
 */
	public function testCreateInvoiceNum() {
		$newInvoiceNumber = $this->MerchantAch->createInvoiceNum();

		//extract year portion of invoice number which should be the first two digits'
		$invoiceYear = substr($newInvoiceNumber, 0, 2);
		$this->assertEquals($invoiceYear, date('y'));

		//extract month portion of invoice number which should be the third and second digits'
		$invoiceMonth = substr($newInvoiceNumber, 2, 2);
		$this->assertEquals($invoiceMonth, date('m'));

		//extract randomized numeric portion of invoice and check that is composed of all integers
		$invoiceRandInts = substr($newInvoiceNumber, 4, 7);
		$this->assertStringMatchesFormat('%d', $invoiceRandInts);

		//Invoice number must be 11 digits long
		$invoiceLength = (int)log10($newInvoiceNumber) + 1;
		$this->assertEquals($invoiceLength, 11);
	}

/**
 * test
 *
 * @expectedException BadMethodCallException
 * @expectedExceptionMessage Missing 'merchant_id' from params data
 * @return void
 */
	public function testSetPaginatorSettingsMissingMerchantId() {
		$this->MerchantAch->setPaginatorSettings([]);
	}

/**
 * test
 *
 * @return void
 */
	public function testSetPaginatorSettings() {
		$merchantId = '375a9eb2-f4c6-4790-b2e1-22d21f7e06f7';

		$expectedSettings = [
			'limit' => 500,
			'contain' => [
				'MerchantAchAppStatus',
				'MerchantAchBillingOption',
				'InvoiceItem.MerchantAchReason' => [
					'order' => ['MerchantAchReason.position ASC']],
				'InvoiceItem.NonTaxableReason'
			],
			'conditions' => [
				'MerchantAch.merchant_id' => $merchantId,
				'MerchantAch.status !=' => GUIbuilderComponent::STATUS_DELETED,
			],
			'order' => [
				'MerchantAch.date_submitted DESC',
			],
		];
		$result = $this->MerchantAch->setPaginatorSettings([
			'merchant_id' => $merchantId,
		]);
		$this->assertSame($expectedSettings, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testGetExpeditedInfoByMerchandId() {
		//merchant with no MerchantUw associated
		$merchantId = '5f4587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$this->assertEmpty($this->MerchantAch->getExpeditedInfoByMerchandId($merchantId));

		//merchant with MerchantUw associated
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$result = $this->MerchantAch->getExpeditedInfoByMerchandId($merchantId);
		$this->assertSame($merchantId, Hash::get($result, 'MerchantUw.merchant_id'));
		$this->assertTrue(Hash::get($result, 'MerchantUw.expedited'));
	}

/**
 * test
 *
 * @return void
 */
	public function testMakeCsvString() {
		$data = $this->MerchantAch->find('all', [
			'conditions' => [
				'MerchantAch.id' => [
					'db3a7409-0ad7-48be-9d83-08356f3b16fe',
					'c19d038b-b2e1-4b73-8362-67d9f5a6c40b',
				],
			],
			'contain' => ['MerchantAchReason'],
		]);
		$csvString = $this->MerchantAch->makeCsvString($data);
		$expectedHeaders = 'Ach Date,Reason,Credit Amount,Debit Amount,Tax,Total,Rejected?,Resubmit Date,Invoice Number';
		$this->assertContains($expectedHeaders, $csvString);
		$this->assertContains('Other reason,,$0.00,$0.00,$195.00,NO,,2006076142', $csvString);
		$this->assertContains(',,,$0.00,$0.00,$50.00,NO,,2007069763', $csvString);
	}
/**
 * testFindAccountingReport
 *
 * @return void
 */
	public function testFindAccountingReport() {
		$data = $this->MerchantAch->find('accountingReport', [
			'conditions' => [
				'MerchantAch.ach_date >=' => '2015-12-01',
				'MerchantAch.ach_date <=' => '2015-12-31',
				'MerchantAch.status' => 'COMP'
			]
		]);
		$this->assertNotEmpty($data);
		$this->assertCount(4, $data);
	}


/**
 * testGetReportData
 *
 * @return void
 */
	public function testGetReportData() {
		$newData = [
			'MerchantAch' => [
				'merchant_id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
				'status' => 'COMP',
				'user_id' => CakeText::uuid(),
				'invoice_number' => '190115123456',
				'tax' => 0,
				'ach_date' => '2021-01-01',
				'ach_amount' => 140,
				'total_ach'	=> 140
			],
			'InvoiceItem' => [
				[
					'merchant_ach_reason_id' => MerchantAchReason::APP_FEE,
					'commissionable' => true,
					'taxable' => false,
					'reason_not_taxed' => 'App fee not taxed',
					'amount' => 90,
					'tax_amount' => 0,
				],
				[
					'merchant_ach_reason_id' => MerchantAchReason::APP_FEE,
					'commissionable' => true,
					'taxable' => false,
					'reason_not_taxed' => 'App fee not taxed',
					'amount' => 50,
					'tax_amount' => 0,
				]
			]
		];
		$this->MerchantAch->saveAssociated($newData, ['deep' => true]);
		$data = $this->MerchantAch->find('accountingReport', [
			'conditions' => [
				'MerchantAch.ach_date >=' => '2021-01-01',
				'MerchantAch.ach_date <=' => '2021-01-31',
				'MerchantAch.status' => 'COMP'
			]
		]);

		$this->assertCount(1, $data);
		$reportData = $this->MerchantAch->getReportData($data);
		unset($reportData['reportData']['totals']);
		$this->assertNotEmpty($reportData);
		$this->assertCount(1, $reportData['reportData']);

		$this->assertEqual($reportData['reportData'][0]['PartnerFullName'], 'Slim Pickins');
		$this->assertEqual($reportData['reportData'][0]['app_fees'], $newData['MerchantAch']['ach_amount']);
		$this->assertEqual($reportData['reportData'][0]['total_ach'], $newData['MerchantAch']['ach_amount']);
		$this->assertNotEmpty($reportData['reportData'][0]['total_ach']);
		$this->assertEqual($reportData['reportData'][0]['invoice_number'], $newData['MerchantAch']['invoice_number']);
	}
/**
 * testBuildMappedColumnQuery
 *
 * @return void
 */
	public function testBuildMappedColumnQuery() {		
		foreach ($this->MerchantAch->arLabels as $colAlias => $colMetaData) {
			if (Hash::get($colMetaData, 'mapped_column')) {
				$result = $this->MerchantAch->buildMappedColumnQuery($colAlias);
				$this->assertNotEmpty($result);
			}
		}
	}
}
