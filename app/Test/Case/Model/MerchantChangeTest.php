<?php
App::uses('MerchantChange', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantChange Test Case
 */
class MerchantChangeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->MerchantNote = ClassRegistry::init('MerchantNote');
		$this->_mockSystemTransactionListener($this->MerchantNote);

		$this->Merchant = ClassRegistry::init('Merchant');
		$this->MerchantChange = $this->getMockForModel('MerchantChange', [
			// AppModel method that use AuthComponent
			'_getCurrentUser',
			// ChangeRequestBehavior that use AuthComponent
			'getSourceId'
		]);
		$this->MerchantChange->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->MerchantChange->expects($this->any())
			->method('getSourceId')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->Merchant->LoggableLog = ClassRegistry::init('LoggableLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantChange);
		unset($this->MerchantNote);
		unset($this->Merchant);
		parent::tearDown();
	}

/**
 * testEdit method
 *
 * @covers MerchantChange::editChange
 * @return void
 */
	public function testEditChange() {
		$merchantId = '00000000-0000-0000-0000-000000000003';
		$newData = [
			'Merchant' => [
				'id' => $merchantId,
				'merchant_dba' => 'DBA Save and Approve',
				'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d'
			],
			'MerchantNote' => [
				[
					'note_title' => 'Change Request Test',
					'note' => 'Merchant change request',
					'note_type_id' => NoteType::CHANGE_REQUEST_ID,
					'note_date' => date('Y-m-d'),
					'user_id' => 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda',
					'merchant_id' => $merchantId,
				],
			],
		];

		// ---------- Test "Save and Approve"
		$merchantDba = $this->Merchant->field('merchant_dba', ['Merchant.id' => $merchantId]);
		$this->assertEquals('Another Merchant', $merchantDba);

		$this->Merchant->create();
		$expected = $this->Merchant->edit($newData, [], MerchantChange::EDIT_APPROVED);
		$this->assertTrue($expected);

		$logId = $this->Merchant->LoggableLog->id;
		$logContent	= $this->Merchant->LoggableLog->field('content', ['LoggableLog.id' => $logId]);
		$this->assertTextContains(':"DBA Save and Approve"', $logContent);

		$merchantDba = $this->Merchant->field('merchant_dba', ['Merchant.id' => $merchantId]);
		$this->assertEquals('DBA Save and Approve', $merchantDba);

		$noteStatus = $this->Merchant->MerchantNote->field('general_status', [
			'MerchantNote.loggable_log_id' => $logId
		]);
		$this->assertEquals(MerchantNote::STATUS_COMPLETE, $noteStatus);

		// ---------- Test "Save for Later"
		$newData['Merchant']['merchant_dba'] = 'DBA Save for Later';
		$this->Merchant->create();
		$expected = $this->Merchant->edit($newData, [], MerchantChange::EDIT_PENDING);
		$this->assertTrue($expected);

		$logId = $this->Merchant->LoggableLog->id;
		$logContent	= $this->Merchant->LoggableLog->field('content', ['LoggableLog.id' => $logId]);
		$this->assertTextContains(':"DBA Save for Later"', $logContent);

		$merchantDba = $this->Merchant->field('merchant_dba', ['Merchant.id' => $merchantId]);
		$this->assertNotEquals('DBA Save for Later', $merchantDba);

		$noteStatus = $this->Merchant->MerchantNote->field('general_status', [
			'MerchantNote.loggable_log_id' => $logId
		]);
		$this->assertEquals(MerchantNote::STATUS_PENDING, $noteStatus);

		// ---------- Test "Save log changes"
		$merchantNote = $this->Merchant->MerchantNote->find('first', [
			'conditions' => array('MerchantNote.loggable_log_id' => $logId)
		]);
		$newData['Merchant']['merchant_dba'] = 'DBA Save log changes';
		$newData['MerchantNote'][0] = Hash::get($merchantNote, 'MerchantNote');

		$logContent	= $this->Merchant->LoggableLog->field('content', ['LoggableLog.id' => $logId]);
		$this->assertTextContains(':"DBA Save for Later"', $logContent);

		$this->Merchant->create();
		$expected = $this->Merchant->edit($newData, [], MerchantChange::EDIT_LOG);
		$this->assertTrue($expected);

		$logContent	= $this->Merchant->LoggableLog->field('content', ['LoggableLog.id' => $logId]);
		$this->assertTextContains(':"DBA Save log changes"', $logContent);

		$merchantDba = $this->Merchant->field('merchant_dba', ['Merchant.id' => $merchantId]);
		$this->assertNotEquals('DBA Save log changes', $merchantDba);

		$noteStatus = $this->Merchant->MerchantNote->field('general_status', [
			'MerchantNote.loggable_log_id' => $logId
		]);
		$this->assertEquals(MerchantNote::STATUS_PENDING, $noteStatus);
	}

/**
 * testEditNoLog method
 *
 * @covers MerchantChange::editChange
 * @return void
 */
	public function testEditNoLog() {
		$merchantId = '00000000-0000-0000-0000-000000000003';
		$newData = array(
			'Merchant' => array(
				'id' => $merchantId,
				'merchant_dba' => 'DBA Save and Approve',
			),
			'MerchantNote' => array(
				array(
					'note_title' => 'Change Request Test',
					'note' => 'Merchant change request',
					'note_type_id' => NoteType::CHANGE_REQUEST_ID,
					'note_date' => date('Y-m-d'),
					'user_id' => 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda',
					'merchant_id' => $merchantId,
				),
			),
		);

		//Not defined loggable_log_id for merchant notes
		$this->Merchant->create();
		$expected = $this->Merchant->edit($newData, array(), MerchantChange::EDIT_LOG);
		$this->assertFalse($expected);
	}

/**
 * testEditNoMerchantNote method
 *
 * @covers MerchantChange::editChange
 * @return void
 */
	public function testEditNoMerchantNote() {
		$newData = array(
			'Merchant' => array(
				'id' => '00000000-0000-0000-0000-000000000003',
				'merchant_dba' => 'not-valid-change-request',
			),
		);

		$expected = null;
		try {
			$this->Merchant->create();
			$expected = $this->Merchant->edit($newData, array(), MerchantChange::EDIT_APPROVED);
		} catch (Exception $e) {
			$this->assertEquals('OutOfBoundsException', get_class($e));
		}
		$this->assertNull($expected);

		try {
			$this->Merchant->create();
			$expected = $this->Merchant->edit($newData, array(), MerchantChange::EDIT_PENDING);
		} catch (Exception $e) {
			$this->assertEquals('OutOfBoundsException', get_class($e));
		}
		$this->assertNull($expected);
	}

/**
 * testUserCanApproveChanges method
 *
 * @param string $userId User id
 * @param mixed $expected Expected result
 * @dataProvider userCanApproveChangesData
 * @covers MerchantChange::userCanApproveChanges
 * @return void
 */
	public function testUserCanApproveChanges($userId, $expected) {
		$MerchantChangeModel = $this->getMockForModel('MerchantChange', ['_getCurrentUser']);
		$MerchantChangeModel->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		$this->assertEquals($expected, $MerchantChangeModel->userCanApproveChanges());
		unset($MerchantChangeModel);
	}

/**
 * Data provider for testUserCanApproveChanges
 *
 * @return array
 */
	public function userCanApproveChangesData() {
		return [
			[AxiaTestCase::USER_ADMIN_ID, true],
			[AxiaTestCase::USER_SM_ID, false],
			[AxiaTestCase::USER_REP_ID, false],
		];
	}

/**
 * testIsPending method
 *
 * @covers MerchantChange::isPending
 * @return void
 */
	public function testIsPending() {
		// Check Merchant without notes
		$result = $this->MerchantChange->isPending('3bc3ac07-fa2d-4ddc-a7e5-680035ec1040');
		$this->assertFalse($result);

		// Check Merchant with completed notes
		$result = $this->MerchantChange->isPending('00000000-0000-0000-0000-000000000003');
		$this->assertFalse($result);

		// Check Merchant with a pending general note
		$result = $this->MerchantChange->isPending('4e3587be-aafb-48c4-9b6b-8dd26b8e94aa');
		$this->assertFalse($result);

		// Check Merchant with a pending change request
		$result = $this->MerchantChange->isPending('00000000-0000-0000-0000-000000000004');
		$this->assertTrue($result);
	}
}
