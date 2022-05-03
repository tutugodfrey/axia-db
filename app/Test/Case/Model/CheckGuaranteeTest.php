<?php
App::uses('CheckGuarantee', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * CheckGuarantee Test Case
 *
 */
class CheckGuaranteeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CheckGuarantee = ClassRegistry::init('CheckGuarantee');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CheckGuarantee);
		parent::tearDown();
	}

/**
 * testGetList
 *
 * @covers CheckGuarantee::setMenuData()
 * @return void
 */
	public function testSetMenuData() {
		$actual = $this->CheckGuarantee->setMenuData();

		$expected = [
			'checkGuaranteeProviders' => $this->CheckGuarantee->CheckGuaranteeProvider->getList(),
			'checkGuaranteeServiceTypes' => $this->CheckGuarantee->CheckGuaranteeServiceType->getList()
		];
		$this->assertSame($expected, $actual);
	}
}
