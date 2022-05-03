<?php
App::uses('MerchantAchAppStatus', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantAchAppStatus Test Case
 *
 * @coversDefaultClass MerchantAchAppStatus
 */
class MerchantAchAppStatusTest extends AxiaTestCase {

/**
 * Class under test
 *
 * @var MerchantAchAppStatus
 */
	public $MerchantAchAppStatus;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantAchAppStatus = ClassRegistry::init('MerchantAchAppStatus');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantAchAppStatus);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers MerchantAchAppStatus::order property
 * @return void
 */
	public function testOrder() {
		$result = $this->MerchantAchAppStatus->find('list', [
			'fields' => ['id', 'rank']
		]);
		$expected = [
			'fd5a7409-0ad7-48be-9d83-08356f3b16fe' => 2,
			'ec4a7409-0ad7-48be-9d83-08356f3b16fe' => 4
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @covers MerchantAchAppStatus::getAchAppStatusList()
 * @return void
 */
	public function testGetAchAppStatusList() {
		$result = $this->MerchantAchAppStatus->getAchAppStatusList();
		$expected = [
			'fd5a7409-0ad7-48be-9d83-08356f3b16fe' => 'Online app + shipped S/K',
			'ec4a7409-0ad7-48be-9d83-08356f3b16fe' => 'Online app + emailed S/K',
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindEnabled
 *
 * @covers MerchantAchAppStatus::_findEnabled()
 * @return void
 */
	public function testFindEnabled() {
		$result = $this->MerchantAchAppStatus->find('enabled');
		$expected = [
			'fd5a7409-0ad7-48be-9d83-08356f3b16fe' => 'Online app + shipped S/K',
			'ec4a7409-0ad7-48be-9d83-08356f3b16fe' => 'Online app + emailed S/K',
		];
		$this->assertEquals($expected, $result);
	}
}
