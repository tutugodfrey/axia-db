<?php
App::uses('RateStructure', 'Model');

/**
 * RateStructure Test Case
 */
class RateStructureTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->RateStructure = ClassRegistry::init('RateStructure');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RateStructure);

		parent::tearDown();
	}

/**
 * _setVisaMcDsAmexBets
 *
 * @return void
 * @covers RateStructure::getRateStructureBets()
 */
	public function testGetRateStructureBets() {
		
		$this->assertEmpty($this->RateStructure->getRateStructureBets('', ''));

		$expected = array(
			'Mastercard' => '558ade3b-0a28-4c31-82a1-20ba34627ad4',
			'Visa' => '558ade3c-d180-423c-a50d-20ba34627ad4',
			'Discover' => '484f88f8-c3ac-48db-a967-af612c1772fd'
		);
		$actual = $this->RateStructure->getRateStructureBets('Cost Plus', 'Visa/MasterCard/Discover Cost Plus .05%');

		$this->assertEquals($expected, $actual);
		$expected = array(
			'American Express' => '558ade3b-9990-4f05-bfed-20ba34627ad4'
		);
		$actual = $this->RateStructure->getRateStructureBets('Cost Plus', 'American Express Cost Plus .05%');
		$this->assertEquals($expected, $actual);
	}

/**
 * testGetRateStructureBetsNotFound
 *
 * @expectedException Exception
 * @expectedExceptionMessage Rate structure combination 'Non Existent Name' with 'Non Existent Downgrades' does not exist!
 * @return void
 */
	public function testGetRateStructureBetsNotFound() {
		$this->RateStructure->getRateStructureBets('Non Existent Name', 'Non Existent Downgrades');
	}

}
