<?php
App::uses('Subregion', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Subregion Test Case
 *
 */
class SubregionTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Subregion = ClassRegistry::init('Subregion');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Subregion);
		parent::tearDown();
	}

/**
 * testValidOrgRegionPairs
 *
 * @covers Subregion::validOrgRegionPairs()
 * @return void
 */
	public function testValidOrgRegionPairs() {
		$this->Subregion->set([
			'Subregion' => [
				'organization_id' => '8d08c7ed-c61a-4311-b088-706d6d8c052c',
				'region_id' => 'ef3b25d3-b4e7-4137-8663-42fc83f7fc71'
			]
		]);
		$this->assertTrue($this->Subregion->validOrgRegionPairs([]));
		$this->Subregion->clear();
		$this->Subregion->set([
			'Subregion' => [
				'organization_id' => CakeText::uuid(),
				'region_id' => CakeText::uuid()
			]
		]);
		
		$this->assertFalse($this->Subregion->validOrgRegionPairs([]));
	}

/**
 * testGetByRegion
 *
 * @covers Subregion::getByRegion()
 * @return void
 */
	public function testGetByRegion() {
		$actual = $this->Subregion->getByRegion('ef3b25d3-b4e7-4137-8663-42fc83f7fc71');
		$expected = ['9b26cfbf-4511-4c5b-a10c-59d99b97bbd2' => 'Org 1 - Region 1 - Subregion 1'];
		$this->assertSame($expected, $actual);
	}
}
