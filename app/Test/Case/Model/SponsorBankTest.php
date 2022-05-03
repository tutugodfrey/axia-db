<?php
App::uses('SponsorBank', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * SponsorBank Test Case
 */
class SponsorBankTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SponsorBank = ClassRegistry::init('SponsorBank');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SponsorBank);

		parent::tearDown();
	}

/**
 * testGetSponsorBanksList method
 *
 * @covers SponsorBank::getSponsorBanksList()
 * @return void
 */
	public function testGetSponsorBanksList() {
		$actual = $this->SponsorBank->getSponsorBanksList();
		$expected = $this->SponsorBank->find('list', array('fields' => array('id', 'bank_name')));
		$this->assertSame($expected, $actual);
	}

}
