<?php
App::uses('MerchantPci', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantPci Test Case
 *
 */
class MerchantPciTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantPci = ClassRegistry::init('MerchantPci');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantPci);

		parent::tearDown();
	}

/**
 * testListPendedCsCancelledGuids method
 *
 * @covers MerchantPci::listPendedCsCancelledGuids
 * @return void
 */
	public function testListPendedCsCancelledGuids() {
		$this->assertEmpty($this->MerchantPci->listPendedCsCancelledGuids());
		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_cancel_request_guid' => CakeText::uuid()], ['validate' => false]);
		$this->assertNotEmpty($this->MerchantPci->listPendedCsCancelledGuids());
	}

/**
 * testListPendedCsBoardedGuids method
 *
 * @covers MerchantPci::listPendedCsBoardedGuids
 * @return void
 */
	public function testListPendedCsBoardedGuids() {
		$this->assertEmpty($this->MerchantPci->listPendedCsBoardedGuids());
		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_board_request_guid' => CakeText::uuid()], ['validate' => false]);
		$this->assertNotEmpty($this->MerchantPci->listPendedCsBoardedGuids());
	}

/**
 * testUpdateAsBoarded method
 *
 * @covers MerchantPci::updateAsBoarded
 * @return void
 */
	public function testUpdateAsBoarded() {
		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_board_request_guid' => CakeText::uuid()], ['validate' => false]);
		$this->MerchantPci->updateAsBoarded('4167fda9-c1b0-405f-a3ff-93ced026fe2b');
		$this->assertEmpty($this->MerchantPci->listPendedCsBoardedGuids());
	}

/**
 * testUpdateAsCancelled method
 *
 * @covers MerchantPci::updateAsCancelled
 * @return void
 */
	public function testUpdateAsCancelled() {
		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_cancel_request_guid' => CakeText::uuid()], ['validate' => false]);
		$this->MerchantPci->updateAsCancelled('4167fda9-c1b0-405f-a3ff-93ced026fe2b');
		$this->assertTrue($this->MerchantPci->hasAny(['cancelled_controlscan_date IS NOT NULL', 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));
	}

/**
 * testGetListOfCompilanceLevel method
 *
 * @covers MerchantPci::getListOfCompilanceLevel
 * @return void
 */
	public function testGetListOfCompilanceLevel() {
		$result = $this->MerchantPci->getListOfCompilanceLevel();
		$expected = [
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4
		];

		$this->assertEquals($expected, $result);
	}

/**
 * test_setAsBoardedOrCancelled method
 *
 * @covers MerchantPci::_setAsBoardedOrCancelled
 * @return void
 */
	public function test_setAsBoardedOrCancelled() {
		$reflection = new ReflectionClass('MerchantPci');
		$method = $reflection->getMethod('_setAsBoardedOrCancelled');
		$method->setAccessible(true);

		
		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_board_request_guid' => CakeText::uuid()], ['validate' => false]);
		$this->assertTrue($this->MerchantPci->hasAny(['cs_board_request_guid IS NOT NULL', 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));

		$actual = $method->invokeArgs($this->MerchantPci, ['4167fda9-c1b0-405f-a3ff-93ced026fe2b', true]);
		$this->assertFalse($this->MerchantPci->hasAny(['cs_board_request_guid IS NOT NULL', 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));
		
		//Set as cancelled
		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_cancel_request_guid' => CakeText::uuid()], ['validate' => false]);
		$this->assertTrue($this->MerchantPci->hasAny(['cs_cancel_request_guid IS NOT NULL', 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));

		$actual = $method->invokeArgs($this->MerchantPci, ['4167fda9-c1b0-405f-a3ff-93ced026fe2b', false]);
		$this->assertTrue($this->MerchantPci->hasAny(['cs_cancel_request_guid IS NULL', 'cancelled_controlscan' => true, 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));
	}

/**
 * testProcessPendingBoardings method
 *
 * @covers MerchantPci::processPendingBoardings
 * @return void
 */
	public function testProcessPendingBoardings() {
		$mockedModel = $this->getMockForModel('MerchantPci', ['checkCsRequestStatus']);
		$mockedModel->expects($this->any())
			->method('checkCsRequestStatus')
			->will($this->returnValue('success'));

		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_board_request_guid' => CakeText::uuid()], ['validate' => false]);
		$mockedModel->processPendingBoardings();
		$this->assertFalse($this->MerchantPci->hasAny(['cs_board_request_guid IS NOT NULL', 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));
	}

/**
 * testProcessPendingCancellations method
 *
 * @covers MerchantPci::processPendingCancellations
 * @return void
 */
	public function testProcessPendingCancellations() {
		$mockedModel = $this->getMockForModel('MerchantPci', ['checkCsRequestStatus']);
		$mockedModel->expects($this->any())
			->method('checkCsRequestStatus')
			->will($this->returnValue('success'));

		$this->MerchantPci->save(['id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b', 'cs_cancel_request_guid' => CakeText::uuid()], ['validate' => false]);
		$mockedModel->processPendingCancellations();
		$this->assertTrue($this->MerchantPci->hasAny(['cs_cancel_request_guid IS NULL', 'cancelled_controlscan' => true, 'id' => '4167fda9-c1b0-405f-a3ff-93ced026fe2b']));
	}
}
