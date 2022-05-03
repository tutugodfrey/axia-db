<?php
App::uses('SaqPrequalification', 'Model');
App::uses('AppTestCase', 'Templates.Lib');

/**
 * SaqPrequalification Test Case
 *
 */
class SaqPrequalificationTest extends AppTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SaqPrequalification = ClassRegistry::init('SaqPrequalification');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SaqPrequalification);

		parent::tearDown();
	}

/**
 * SaqPrequalification::shouldSave
 *
 * @return void
 */
	public function testShouldSave() {
		$this->assertFalse($this->SaqPrequalification->shouldSave([]));
		$this->assertFalse(
			$this->SaqPrequalification->shouldSave(['SaqPrequalification'])
		);
		$this->assertFalse(
			$this->SaqPrequalification->shouldSave(
				['SaqPrequalification' => []]
			)
		);
		$this->assertFalse(
			$this->SaqPrequalification->shouldSave(
				[
					'SaqPrequalification' => [
						'result' => 'test',
					]
				]
			)
		);
		$this->assertTrue(
			$this->SaqPrequalification->shouldSave(
				[
					'SaqPrequalification' => [
						'result' => 'test',
						'date_completed' => '2013-04-06 20:30:01'
					]
				]
			)
		);
	}

/**
 * SaqPrequalification::getBySaqMerchantIdAndDateCompleted
 *
 * @return void
 */
	public function testGetBySaqMerchantIdAndDateCompleted() {
		$expected = [
			'SaqPrequalification' => [
				'id' => '00000000-0000-0000-0000-000000000827',
				'id_old' => null,
				'saq_merchant_id' => '00000000-0000-0000-0000-000000000001',
				'saq_merchant_id_old' => null,
				'result' => 'C',
				'date_completed' => '2011-02-24 14:25:12',
				'control_scan_code' => 0,
				'control_scan_message' => 'The transaction was successful'
			],
		];
		$result = $this->SaqPrequalification->getBySaqMerchantIdAndDateCompleted(
			'00000000-0000-0000-0000-000000000001',
			'2011-02-24 14:25:12'
		);

		$this->assertEqual($expected, $result);

		$result = $this->SaqPrequalification->getBySaqMerchantIdAndDateCompleted(
			'00000000-0000-0000-0000-000000000001',
			'2011-02-24 14:25:19'
		);
		$this->assertEmpty($result);
	}
/**
 * SaqPrequalification::addOrEdit
 *
 * @return void
 */
	public function testAddOrEdit() {
		$expected = [
			'SaqPrequalification' => [
				'id' => '00000000-0000-0000-0000-000000000827',
				'id_old' => null,
				'saq_merchant_id' => '00000000-0000-0000-0000-000000000001',
				'saq_merchant_id_old' => null,
				'result' => 'C',
				'date_completed' => '2011-02-24 14:25:12',
				'control_scan_code' => 0,
				'control_scan_message' => 'The transaction was successful 2'
			],
		];
		$result = $this->SaqPrequalification->addOrEdit($expected);
		$this->assertTrue($result);

		$this->SaqPrequalification->contain();
		$saqPrequalification = $this->SaqPrequalification->findById('00000000-0000-0000-0000-000000000827');
		$this->assertEqual($saqPrequalification, $expected);

		$data = [
			'SaqPrequalification' => [
				'id' => '00000000-0000-0000-0000-000000000827',
				'id_old' => null,
				'saq_merchant_id' => '00000000-0000-0000-0000-000000000001',
				'saq_merchant_id_old' => null,
				'result' => 'C',
				'date_completed' => '2015-03-24 14:25:12',
				'control_scan_code' => 0,
				'control_scan_message' => 'The transaction was successful'
			],
		];
		$result = $this->SaqPrequalification->addOrEdit($data);
		$this->assertTrue($result);
		$id = $this->SaqPrequalification->id;
		$this->SaqPrequalification->contain();
		$saqPrequalification = $this->SaqPrequalification->findById($id);
		$data['SaqPrequalification']['id'] = $id;
		$this->assertEqual($saqPrequalification, $data);
	}
}
