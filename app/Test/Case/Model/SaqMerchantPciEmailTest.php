<?php
App::uses('SaqMerchantPciEmail', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * SaqMerchantPciEmail Test Case
 *
 */
class SaqMerchantPciEmailTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SaqMerchantPciEmail = ClassRegistry::init('SaqMerchantPciEmail');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SaqMerchantPciEmail);
		parent::tearDown();
	}

/**
 * testGetLabelById
 *
 * @covers SaqMerchantPciEmail::getLabelById()
 * @return void
 */
	public function testGetLabelById() {
		$data = array(
			'90170996-5681-4ee2-9b7e-219f06c0e9b0' => 'Introdutory Email',
			'1e8b0888-a7d8-4a99-bd36-7a0841c568eb' => 'First Reminder',
			'daa48863-cf8b-425b-8bca-03bafe9ceb2b' => 'Secondary Reminder',
			'd27454cb-da68-409b-8784-b1ac561655ef' => 'Third Reminder',
			'11bb3864-6009-49c3-a1e5-b6fd9294068e' => 'Non-Compliance Reminder 1',
			'8c773d51-feee-4bc2-afe5-80b1c08edf9a' => 'Third Reminder',
			'ea4328ee-2334-4edd-ac7c-c12c212f144d' => 'Annual Reminder 1',
			'34662ac9-25a5-4de5-88b2-95a30b8cd2a1' => 'Annual Reminder 2',
		);
		$this->assertFalse($this->SaqMerchantPciEmail->getLabelById('fakeId'));
		foreach ($data as $key => $val) {
			$actual = $this->SaqMerchantPciEmail->getLabelById($key);
			$this->assertSame($data[$key], $actual);
		}
	}
}
