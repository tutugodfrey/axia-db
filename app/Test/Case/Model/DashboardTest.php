<?php
App::uses('Dashboard', 'Model');

/**
 * Dashboard Test Case
 */
class DashboardTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Dashboard = ClassRegistry::init('Dashboard');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Dashboard);

		parent::tearDown();
	}

/**
 * testGetAcquiringMerch method
 *
 * @return void
 */
	public function testGetAcquiringMerch() {
		$expected = [
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0
		];

		$data = $this->Dashboard->getAcquiringMerch(2015);
		$this->assertSame($expected, $data);
		$this->assertCount(12, $data);
	}

/**
 * getPiChartData method
 *
 * @return void
 */
	public function testGetPiChartData() {
		$expected = [
			0,
			0,
			0,
			0,
			1,
		];

		$data = $this->Dashboard->getPiChartData(1);
		$this->assertSame($expected, $data);
		$this->assertCount(5, $data);
	}
}
