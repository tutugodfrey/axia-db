<?php
App::uses('ChangeRequestBehavior', 'Model/Behavior');
App::uses('AxiaTestCase', 'Test');
App::uses('NoteType', 'Model');

/**
 * ChangeRequestBehavior Test Case
 *
 * @property ChangeRequestBehavior ChangeRequest
 */
class ChangeRequestBehaviorTest extends AxiaTestCase {

/**
 * Id of the merchant to run the tests
 *
 * @var string
 */
	const TEST_MERCHANT_ID = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
/**
 * LoggableLog id to run the tests
 *
 * @var string
 */
	const TEST_LOG_ID = '565d85d0-93ec-4977-bd98-03fbc0a8ad65';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ChangeRequest = new ChangeRequestBehavior();

		$this->model = $this->getMockForModel('Merchant', array('getSourceId'));
		$this->model->expects($this->any())
				->method('getSourceId')
				->will($this->returnValue('00000000-0000-0000-0000-000000000001'));
		$this->model->LoggableLog = ClassRegistry::init('LoggableLog');
		$this->_mockSystemTransactionListener($this->model->MerchantNote);

		$this->changeRequestData = array(
				'id' => '00000000-0000-0000-0000-000000000001',
				'merchant_id' => '00000000-0000-0000-0000-000000000001',
				'user_id' => '7e248833-7cd8-43ef-bf6c-7dc7287e271c',
				'merchant_mid' => '0153',
				'merchant_dba' => 'Nicole Harris',
				'active_date' => '2010-09-17',
				'active' => 1,
				'partner_exclude_volume' => 0,
				'aggregated' => 0,
				'bet_network_id' => '00000000-0000-0000-0000-000000000001',
		);
		$this->changeRequestData['MerchantNote'][0] = array(
			'note_type_id' => NoteType::CHANGE_REQUEST_ID,
			'user_id' => '7e248833-7cd8-43ef-bf6c-7dc7287e271c',
			'merchant_id' => self::TEST_MERCHANT_ID,
			'note_date' => '2016-05-12',
			'note_title' => 'Account Information Change',
			'general_status' => 'COMP',
			'loggable_log_id' => '',
			'note' => 'ttest'
		);
		$this->LoggableLog = ClassRegistry::init('LoggableLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ChangeRequest);
		unset($this->model);
		unset($this->LoggableLog);

		parent::tearDown();
	}

/**
 * testNewChangeRequest method
 *
 * @test
 * @return void
 */
	public function testNewChangeRequest() {
		$options = array(
			'conditions' => array(
				'model' => 'Merchant',
				'action' => 'newChangeRequest',
				'foreign_key' => '00000000-0000-0000-0000-000000000001',
				'source_id' => '00000000-0000-0000-0000-000000000001',
			),
		);
		$loggableLogs = $this->LoggableLog->find('all', $options);
		$this->assertCount(0, $loggableLogs);

		$result = $this->ChangeRequest->newChangeRequest($this->model, $this->changeRequestData);
		$this->assertTrue($result);

		$loggableLogs = $this->LoggableLog->find('all', $options);
		$this->assertCount(1, $loggableLogs);
		$this->assertTextContains('"merchant_dba";s:13:"Nicole Harris"', Hash::get($loggableLogs, '0.LoggableLog.content'));
	}

/**
 * testNewChangeRequestNotValid method
 *
 * @test
 * @return void
 */
	public function testNewChangeRequestNotValid() {
		$loggableLogsCount = $this->LoggableLog->find('count');
		try
		{
				$result = $this->ChangeRequest->newChangeRequest($this->model, array('Merchant' => array()));
				$this->fail("Expected exception");
		}
		catch(Exception $e) {
				$this->assertNotEmpty($e->getMessage());
				$this->assertContains("Missing Merchant Note data", $e->getMessage());
		}
		$this->assertEquals($loggableLogsCount, $this->LoggableLog->find('count'));
	}

/**
 * testNewChangeRequestWithAssociated method
 *
 * @test
 * @return void
 */
	public function testNewChangeRequestWithAssociated() {
		$options = [
			'conditions' => [
				'model' => 'Merchant',
				'action' => 'newChangeRequest',
				'foreign_key' => '00000000-0000-0000-0000-000000000001',
				'source_id' => '00000000-0000-0000-0000-000000000001',
			],
		];
		$loggableLogs = $this->LoggableLog->find('all', $options);
		$this->assertCount(0, $loggableLogs);

		$this->changeRequestData['MerchantNote'][0] = [
			'note_title' => 'Associated Change Request Test',
			'note' => 'Merchant change request Note',
			'note_type_id' => NoteType::CHANGE_REQUEST_ID,
			'note_date' => date('Y-m-d'),
			'user_id' => '00000000-0000-0000-0000-000000000001',
			'merchant_id' => '00000000-0000-0000-0000-000000000001',
		];
		$result = $this->ChangeRequest->newChangeRequest($this->model, $this->changeRequestData, [
			'associateLog' => [
				'model' => 'MerchantNote',
				'field' => 'loggable_log_id',
			]
		]);
		$this->assertTrue($result);

		$loggableLogs = $this->LoggableLog->find('all', $options);
		$this->assertCount(1, $loggableLogs);
		$this->assertTextContains('"merchant_dba";s:13:"Nicole Harris"', Hash::get($loggableLogs, '0.LoggableLog.content'));
		$logId = Hash::get($loggableLogs, '0.LoggableLog.id');
		$associatedRecord = $this->model->MerchantNote->find('first', [
			'conditions' => ['MerchantNote.loggable_log_id' => $logId]
		]);
		$this->assertNotEmpty($associatedRecord);
	}

/**
 * testEditChangeRequest method
 *
 * @return void
 */
	public function testEditChangeRequest() {
		$totalLogs = 4;
		$this->assertCount($totalLogs, $this->LoggableLog->find('all'));

		$logId = self::TEST_LOG_ID;
		$log = $this->LoggableLog->read(null, $logId);
		$result = $this->ChangeRequest->editChangeRequest(
			$this->model,
			Hash::get($log, 'LoggableLog.id'),
			array('Merchant' => array_merge($this->changeRequestData, array('merchant_dba' => 'Kylo Ren')))
		);
		$this->assertTrue($result);
		$loggableLogAfterChange = $this->LoggableLog->find('first', array(
			'conditions' => array(
				'model' => 'Merchant',
				'foreign_key' => Hash::get($log, 'LoggableLog.foreign_key'),
				'id' => $logId
			)
		));
		$this->assertCount($totalLogs, $this->LoggableLog->find('all'));
		$this->assertEquals('editChangeRequest', Hash::get($loggableLogAfterChange, 'LoggableLog.action'));
		$this->assertTextContains('"merchant_dba";s:8:"Kylo Ren"', Hash::get($loggableLogAfterChange, 'LoggableLog.content'));
	}

/**
 * testEditChangeRequestNotFound method
 *
 * @test
 * @return void
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Missing LoggableLog with id 00000000-9999-0000-0000-000000000001
 */
	public function testEditChangeRequestNotFound() {
		$this->assertFalse($this->ChangeRequest->editChangeRequest($this->model, '00000000-9999-0000-0000-000000000001'));
	}

/**
 * testApproveChange method
 *
 * @return void
 */
	public function testApproveChange() {
		$merchantId = '00000000-0000-0000-0000-000000000003';
		$merchant = $this->model->read(null, $merchantId);
		$this->assertNotEmpty($merchant);

		$merchant['Merchant']['merchant_dba'] = 'Kylo Ren';
		$merchant['MerchantNote'][0] = $this->changeRequestData['MerchantNote'][0];
		$this->ChangeRequest->newChangeRequest($this->model, $merchant);
		$options = array(
			'conditions' => array(
				'LoggableLog.model' => 'Merchant',
				'LoggableLog.foreign_key' => Hash::get($merchant, 'Merchant.id'),
			),
			'order' => array('created' => 'DESC'),
		);

		$loggableLog = $this->LoggableLog->find('first', $options);
		$this->assertEquals('newChangeRequest', Hash::get($loggableLog, 'LoggableLog.action'));
		$storedMerchant = $this->model->read(null, $merchantId);
		$this->assertEquals('Another Merchant', Hash::get($storedMerchant, 'Merchant.merchant_dba'));
		$result = $this->ChangeRequest->approveChange(
			$this->model,
			Hash::get($loggableLog, 'LoggableLog.id')
		);
		$this->assertTrue($result);
		$approvedMerchant = $this->model->read(null, $merchantId);
		$this->assertEquals('Kylo Ren', Hash::get($approvedMerchant, 'Merchant.merchant_dba'));
	}

/**
 * testApproveChangeNotFound method
 *
 * @test
 * @return void
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Missing LoggableLog with id 00000000-9999-0000-0000-000000000001
 */
	public function testApproveChangeNotFound() {
		$result = $this->ChangeRequest->approveChange(
				$this->model,
				'00000000-9999-0000-0000-000000000001'
		);
	}

/**
 * testMergeLoggedData method
 *
 * @return void
 */
	public function testMergeLoggedData() {
		$merchantId = '00000000-0000-0000-0000-000000000003';
		$merchant = $this->model->read(null, $merchantId);
		$this->assertNotEmpty($merchant);

		$merchant['Merchant']['merchant_dba'] = 'Kylo Ren';
		$merchant['MerchantNote'][0] = $this->changeRequestData['MerchantNote'][0];
		$this->ChangeRequest->newChangeRequest($this->model, $merchant);
		$options = array(
			'conditions' => array(
				'model' => 'Merchant',
				'foreign_key' => Hash::get($merchant, 'Merchant.id'),
			),
			'order' => array('created' => 'DESC'),
		);
		$loggableLog = $this->LoggableLog->find('first', $options);
		$result = $this->ChangeRequest->mergeLoggedData(
				$this->model,
				Hash::get($loggableLog, 'LoggableLog.id')
		);
		$this->assertEquals('Kylo Ren', Hash::get($result, 'Merchant.merchant_dba'));

		$result = $this->ChangeRequest->mergeLoggedData(
				$this->model,
				Hash::get($loggableLog, 'LoggableLog.id'),
				array('Merchant' => array(
					'merchant_dba' => 'Jar Jar',
					'another_field' => 'test'
				))
		);
		$this->assertEquals('Kylo Ren', Hash::get($result, 'Merchant.merchant_dba'));
		$this->assertEquals('test', Hash::get($result, 'Merchant.another_field'));
	}

/**
 * testGetLog method
 *
 * @test
 * @return void
 */
	public function testGetLog() {
		$logId = self::TEST_LOG_ID;
		$log = $this->LoggableLog->read(null, $logId);
		$expected = $log;
		$expected['Unserialized'] = $this->model->unserialize(Hash::get($log, 'LoggableLog.content'));

		$result = $this->ChangeRequest->getLog($this->model, $logId);
		$this->assertEquals($result, $expected);
	}

/**
 * testGetLogNotFound method
 *
 * @test
 * @return void
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Missing LoggableLog with id 00000000-9999-0000-0000-000000000001
 */
	public function testGetLogNotFound() {
		$this->ChangeRequest->getLog($this->model, '00000000-9999-0000-0000-000000000001');
	}
}
