<?php
App::uses('ResidualVolumeTier', 'Model');
App::uses('AxiaTestCase', 'Test');

class ResidualVolumeTierTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ResidualVolumeTier = ClassRegistry::init('ResidualVolumeTier');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ResidualVolumeTier);

		parent::tearDown();
	}

/**
 * testValidateTierMinimum method
 *
 * @param array $data Data to validate
 * @param bool $validated Validation result
 * @param string|null $errorMessage Validation error message
 * @dataProvider validateTierMinimumData
 * @return void
 */
	public function testValidateTierMinimum($data, $validated, $errorMessage) {
		$this->ResidualVolumeTier->set($data);
		$this->assertEquals($validated, $this->ResidualVolumeTier->validates());
		$this->assertEquals($errorMessage, Hash::get($this->ResidualVolumeTier->validationErrors, 'tier1_minimum_volume.0'));
	}

/**
 * Data provider for testValidateTierMinimum
 *
 * @return array
 */
	public function validateTierMinimumData() {
		return [
			[
				['tier1_minimum_volume' => 1, 'tier1_maximum_volume' => 10],
				true,
				null
			],
			[
				['tier1_minimum_volume' => 11, 'tier1_maximum_volume' => 10],
				false,
				'The minimum must be less or equal than the maximum',
			],
			[
				['tier1_minimum_volume' => -1, 'tier1_maximum_volume' => 10],
				false,
				'The value must be a valid amount',
			],
			[
				['tier1_minimum_volume' => 'abc', 'tier1_maximum_volume' => 10],
				false,
				'The value must be a valid amount',
			],
			[
				['tier1_minimum_volume' => 1, 'tier1_maximum_volume' => null],
				true,
				null,
			],
		];
	}

/**
 * testValidateTierMinimumNotEmptyComparison method
 *
 * @return void
 */
	public function testValidateTierMinimumNotEmptyComparison() {
		$this->ResidualVolumeTier->validate['tier1_minimum_volume'] = [
			'rule' => ['validateTierMinimum', 'tier1_maximum_volume', false],
			'allowEmpty' => true
		];
		$this->ResidualVolumeTier->set([
			'tier1_minimum_volume' => 1,
			'tier1_maximum_volume' => null
		]);
		$this->assertEquals(false, $this->ResidualVolumeTier->validates());
		$errorMessage = Hash::get($this->ResidualVolumeTier->validationErrors, 'tier1_minimum_volume.0');
		$this->assertEquals('The minimum must be less or equal than the maximum', $errorMessage);
	}

/**
 * testValidateTierMaximum method
 *
 * @param array $data Data to validate
 * @param bool $validated Validation result
 * @param string|null $errorMessage Validation error message
 * @dataProvider validateTierMaximumData
 * @return void
 */
	public function testValidateTierMaximum($data, $validated, $errorMessage) {
		$this->ResidualVolumeTier->set($data);
		$this->assertEquals($validated, $this->ResidualVolumeTier->validates());
		$this->assertEquals($errorMessage, Hash::get($this->ResidualVolumeTier->validationErrors, 'tier1_maximum_volume.0'));
	}

/**
 * Data provider for testValidateTierMinimum
 *
 * @return array
 */
	public function validateTierMaximumData() {
		return [
			[
				['tier1_minimum_volume' => 1, 'tier1_maximum_volume' => 10],
				true,
				null
			],
			[
				['tier1_minimum_volume' => 11, 'tier1_maximum_volume' => 10],
				false,
				'The maximum must be greater or equal than the minimum',
			],
			[
				['tier1_minimum_volume' => 1, 'tier1_maximum_volume' => -1],
				false,
				'The value must be a valid amount',
			],
			[
				['tier1_minimum_volume' => 1, 'tier1_maximum_volume' => 'acb'],
				false,
				'The value must be a valid amount',
			],
			[
				['tier1_minimum_volume' => null, 'tier1_maximum_volume' => 10],
				true,
				null,
			],
		];
	}

/**
 * testValidateTierMaximumNotEmptyComparison method
 *
 * @return void
 */
	public function testValidateTierMaximumNotEmptyComparison() {
		$this->ResidualVolumeTier->validate['tier1_maximum_volume'] = [
			'rule' => ['validateTierMaximum', 'tier1_minimum_volume', false],
			'allowEmpty' => true
		];
		$this->ResidualVolumeTier->set([
			'tier1_minimum_volume' => null,
			'tier1_maximum_volume' => 10
		]);
		$this->assertEquals(false, $this->ResidualVolumeTier->validates());
		$errorMessage = Hash::get($this->ResidualVolumeTier->validationErrors, 'tier1_maximum_volume.0');
		$this->assertEquals('The maximum must be greater or equal than the minimum', $errorMessage);
	}
}
