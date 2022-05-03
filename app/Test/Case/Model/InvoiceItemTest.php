<?php
App::uses('AxiaTestCase', 'Test');

/**
 * InvoiceItem Test Case
 *
 * @coversDefaultClass InvoiceItem
 */
class InvoiceItemTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var InvoiceItem
 */
	public $InvoiceItem;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->InvoiceItem = ClassRegistry::init('InvoiceItem');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->InvoiceItem);
		parent::tearDown();
	}

/**
 * testRemoveEmpty
 *
 * @return void
 */
	public function testRemoveEmpty() {		
		$data = array(
			'InvoiceItem' => array(
				array(
					'id' => '5cf15bae-ef44-4df7-8459-60c734627ad4',
					'merchant_ach_reason_id' => '',
					'reason_other' => '',
					'commissionable' => '0',
					'taxable' => '0',
					'non_taxable_reason_id' => '',
					'amount' => '15.50',
					'tax_amount' => ''
				),
				array(
					'merchant_ach_reason_id' => '',
					'reason_other' => '',
					'commissionable' => '0',
					'taxable' => '0',
					'non_taxable_reason_id' => '',
					'amount' => '',
					'tax_amount' => ''
				)
			)
		);
		$actual = $data;
		$this->InvoiceItem->removeEmpty($actual);
		$this->assertCount(1, $actual['InvoiceItem']);
		$actual = $data['InvoiceItem'];
		$this->InvoiceItem->removeEmpty($actual);
		$this->assertCount(1, $actual);
		$this->assertSame($data['InvoiceItem'][0], $actual[0]);

		//fill with all empty entries
		$actual = array_fill(0, 10, $data['InvoiceItem'][1]);
		$this->InvoiceItem->removeEmpty($actual);
		$this->assertEmpty($actual); //all entries removed

		//fill with 10 non empty entries
		$actual = $expected = array_fill(0, 10, $data['InvoiceItem'][0]);
		$this->InvoiceItem->removeEmpty($actual);
		$this->assertCount(10, $actual); //all entries still there
		$this->assertSame($expected, $actual); //still the same
	}
}
