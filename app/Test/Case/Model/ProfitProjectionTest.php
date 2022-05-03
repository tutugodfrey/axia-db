<?php
App::uses('ProfitProjection', 'Model');

/**
 * ProfitProjection Test Case
 */
class ProfitProjectionTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProfitProjection = ClassRegistry::init('ProfitProjection');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProfitProjection);

		parent::tearDown();
	}

/**
 * testUpdateAllProjections
 *
 * @covers ProfitProjection::updateAllProjections
 * @return void
 */
	public function testUpdateAllProjections() {
		//Merchant has Visa Sales product
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		
		$this->assertEmpty($this->ProfitProjection->find('all', ['conditions' => ['merchant_id' => $merchantId]]));
		$this->assertTrue($this->ProfitProjection->updateAllProjections($merchantId));
		$actual = $this->ProfitProjection->find('all', ['conditions' => ['merchant_id' => $merchantId]]);
		$this->assertNotEmpty($actual);
		$visaSalesId = $this->ProfitProjection->ProductsServicesType->field('id', ['products_services_description' => 'Visa Sales']);
		$this->assertCount(1, $actual);
		$this->assertSame($visaSalesId, $actual[0]['ProfitProjection']['products_services_type_id']);
	}

/**
 * testUpdateAllProjectionsNoProdsExceptionThrown
 * test exception thrown when merchant does not have any archivable products
 *
 * @expectedException Exception
 * @expectedExceptionMessage Merchant does not have any products with which to calculate profit projections.
 * @return void
 */
	public function testUpdateAllProjectionsNoProdsExceptionThrown() {
		$this->ProfitProjection->updateAllProjections(CakeText::uuid());
	}

/**
 * testUpdateAllProjectionsInvalidArgumentExceptionThrown
 * test InvalidArgumentException thrown whe no merchant id argument is passed
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Merchant id parameter missing! Unable to upate projections.
 * @return void
 */
	public function testUpdateAllProjectionsInvalidArgumentExceptionThrown() {
		$this->ProfitProjection->updateAllProjections(null);
	}

/**
 * testGetGroupedByProductCategory
 *
 * @covers ProfitProjection::getGroupedByProductCategory
 * @return void
 */
	public function testGetGroupedByProductCategory() {
		//Merchant has Visa Sales product
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa';
		$expected = [
		'Credit' => [
				'Visa Sales' => [
					'ProfitProjection' => [
						'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
						'products_services_type_id' => 'e6d8f040-9963-4539-ab75-3e19f679de16',
						'rep_gross_profit' => '0',
						'rep_profit_amount' => '0.0000',
						'axia_profit_amount' => '0'
					],
					'ProductsServicesType' => [
						'products_services_description' => 'Visa Sales'
					],
					'ProductCategory' => [
						'category_name' => 'Credit'
					]
				]
			]
		];
		$this->assertTrue($this->ProfitProjection->updateAllProjections($merchantId));
		$actual = $this->ProfitProjection->getGroupedByProductCategory($merchantId);
		$actual = Hash::remove($actual, '{s}.{s}.ProfitProjection.id');
		$this->assertEqual($expected, $actual);
	}
}
