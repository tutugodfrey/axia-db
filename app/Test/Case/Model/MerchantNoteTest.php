<?php
App::uses('MerchantNote', 'Model');
App::uses('NoteType', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('EmailAlert', 'Lib/Event');
App::uses('SystemTransactionListener', 'Lib/Event');

/**
 * MerchantNote Test Case
 *
 */
class MerchantNoteTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantNote = ClassRegistry::init('MerchantNote');
		$this->_mockSystemTransactionListener($this->MerchantNote);
		$this->MerchantNote->MerchantChange = $this->getMockForModel('MerchantChange', [
			// this method use the AuthComponent
			'userCanApproveChanges',
		]);
		$this->MerchantNote->MerchantChange->expects($this->any())
			->method('userCanApproveChanges')
			->will($this->returnValue(true));
		$this->MerchantChange = $this->getMockForModel('MerchantChange', [
			// AppModel method that use AuthComponent
			'_getCurrentUser',
			// ChangeRequestBehavior that use AuthComponent
			'getSourceId'
		]);
		$this->MerchantChange->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->MerchantNote->Merchant->LoggableLog = ClassRegistry::init('Loggable.LoggableLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantNote);
		unset($this->MerchantChange);

		parent::tearDown();
	}

/**
 * testConstructorAndFilterArgs
 *
 * @covers MerchantNote::__construct
 * @covers MerchantNote::filterArgs property
 * testConstructorAndFilterArgs method
 *
 * @return void
 */
	public function testFilterArgs() {
		$date = new DateTime('now');
		$expected = [
			'action_type' => [
				'type' => 'query',
				'empty' => true,
				'method' => 'requestTypeConditions'
			],
			'general_status' => [
				'type' => 'query',
				'empty' => true,
				'method' => 'requestStatusConditions'
			],
			'dba_mid' => [
				'type' => 'query',
				'empty' => true,
				'method' => 'merchantOrConditions'
			],
			'author_type' => [
				'type' => 'query',
				'empty' => true,
				'method' => ''
			],
			'author_name' => [
				'type' => 'query',
				'empty' => true,
				'method' => ''
			],
			'from_date' => [
				'type' => 'query',
				'method' => 'dateRangeStartConditions',
				'empty' => true,
				'defaultValue' => [
					'year' => $date->format('Y'),
					'month' => $date->format('m')
				]
			],
			'end_date' => [
				'type' => 'query',
				'method' => 'dateRangeEndConditions',
				'empty' => true,
				'defaultValue' => [
					'year' => $date->format('Y'),
					'month' => $date->format('m')
				]
			],
			'user_id' => [
				'type' => 'subquery',
				'method' => 'searchByUserId',
				'field' => '"Merchant"."user_id"',
				'searchByMerchantEntity' => true
			]
		];
		$this->assertEquals($expected, $this->MerchantNote->filterArgs);
	}

/**
 * testDateRangeStartConditions
 *
 * @covers MerchantNote::dateRangeStartConditions
 * testDateRangeStartConditions method
 *
 * @return void
 */
	public function testDateRangeStartConditions() {
		$data = ['from_date' => [
			'year' => '2000',
			'month' => '01'
		]];
		$result = $this->MerchantNote->dateRangeStartConditions($data);
		$expected = "MerchantNote.note_date >= '2000-01-01'";
		$this->assertEquals($expected, $result);

		$result = $this->MerchantNote->dateRangeStartConditions($data, true);
		$expected = "MerchantAch.ach_date >= '2000-01-01'";
		$this->assertEquals($expected, $result);
	}

/**
 * testDateRangeEndConditions
 *
 * @covers MerchantNote::dateRangeEndConditions
 * testDateRangeEndConditions method
 *
 * @return void
 */
	public function testDateRangeEndConditions() {
		$data = ['end_date' => [
			'year' => '2000',
			'month' => '01'
		]];
		$result = $this->MerchantNote->dateRangeEndConditions($data);
		$expected = "MerchantNote.note_date <= '2000-01-31'";
		$this->assertEquals($expected, $result);

		$result = $this->MerchantNote->dateRangeEndConditions($data, true);
		$expected = "MerchantAch.ach_date <= '2000-01-31'";
		$this->assertEquals($expected, $result);
	}

/**
 * testSetJoinsByAuthor
 *
 * @covers MerchantNote::setJoins
 * testSetJoins method
 *
 * @return void
 */
	public function testSetJoinsByAuthor() {
		$this->MerchantNote->searchByAuthor = true;
		$filterArgs = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '01'
			],
		];
		$result = $this->MerchantNote->setJoins($filterArgs);
		$expected = [
			[
				'table' => 'merchant_notes',
				'alias' => 'MerchantNote',
				'type' => 'LEFT',
				'conditions' => [
					"MerchantNote.merchant_id = Merchant.id",
					"MerchantNote.note_date >= '2015-01-01'",
					"MerchantNote.note_date <= '2015-01-31'",

				]
			],
			[
				'table' => 'note_types',
				'alias' => 'NoteType',
				'type' => 'LEFT',
				'conditions' => [
					'MerchantNote.note_type_id = NoteType.id'
				]
			],
			[
				'table' => 'merchant_aches',
				'alias' => 'MerchantAch',
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.id = MerchantAch.merchant_id",
					"MerchantAch.ach_date >= '2015-01-01'",
					"MerchantAch.ach_date <= '2015-01-31'",

				]
			],
			[
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => [
					'OR' => [
						'MerchantNote.user_id = User.id',
						'MerchantAch.user_id = User.id'
					]
				]
			],
		];
		$this->assertEquals($expected, $result);
		$achJoinsExpected = [
			[
				'table' => 'merchant_aches',
				'alias' => 'MerchantAch',
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.id = MerchantAch.merchant_id",
				]
			],
			[
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => [
						'MerchantAch.user_id = User.id'
				]
			],
		];
		$filterArgs['action_type'] = MerchantAch::INV_CRT;
		$result = $this->MerchantNote->setJoins($filterArgs);
		$this->assertEquals($achJoinsExpected, $result);
		$notesJoinsExpected = [
			[
				'table' => 'merchant_notes',
				'alias' => 'MerchantNote',
				'type' => 'LEFT',
				'conditions' => [
					"MerchantNote.merchant_id = Merchant.id",
				]
			],
			[
				'table' => 'note_types',
				'alias' => 'NoteType',
				'type' => 'LEFT',
				'conditions' => [
					'MerchantNote.note_type_id = NoteType.id'
				]
			],
			[
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => [
						'MerchantNote.user_id = User.id'
				]
			],
		];
		$filterArgs['action_type'] = 'Change Request';
		$result = $this->MerchantNote->setJoins($filterArgs);
		$this->assertEquals($notesJoinsExpected, $result);
	}

/**
 * testSetJoinsByRep
 *
 * @covers MerchantNote::setJoins
 * testSetJoins method
 *
 * @return void
 */
	public function testSetJoinsByRep() {
		$this->MerchantNote->searchByAuthor = false;
		$filterArgs = [
			'from_date' => [
				'year' => '2015',
				'month' => '01'
			],
			'end_date' => [
				'year' => '2015',
				'month' => '01'
			],
		];
		$result = $this->MerchantNote->setJoins($filterArgs);
		$expected = [
			[
				'table' => 'merchant_notes',
				'alias' => 'MerchantNote',
				'type' => 'LEFT',
				'conditions' => [
					"MerchantNote.merchant_id = Merchant.id",
					"MerchantNote.note_date >= '2015-01-01'",
					"MerchantNote.note_date <= '2015-01-31'",

				]
			],
			[
				'table' => 'note_types',
				'alias' => 'NoteType',
				'type' => 'LEFT',
				'conditions' => [
					'MerchantNote.note_type_id = NoteType.id'
				]
			],
			[
				'table' => 'merchant_aches',
				'alias' => 'MerchantAch',
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.id = MerchantAch.merchant_id",
					"MerchantAch.ach_date >= '2015-01-01'",
					"MerchantAch.ach_date <= '2015-01-31'",

				]
			],
			[
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => [
					'Merchant.user_id = User.id'
				]
			],
		];
		$this->assertEquals($expected, $result);
		$achJoinsExpected = [
			[
				'table' => 'merchant_aches',
				'alias' => 'MerchantAch',
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.id = MerchantAch.merchant_id",
				]
			],
			[
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => [
						'Merchant.user_id = User.id'
				]
			],
		];
		$filterArgs['action_type'] = MerchantAch::INV_CRT;
		$result = $this->MerchantNote->setJoins($filterArgs);
		$this->assertEquals($achJoinsExpected, $result);
		$notesJoinsExpected = [
			[
				'table' => 'merchant_notes',
				'alias' => 'MerchantNote',
				'type' => 'LEFT',
				'conditions' => [
					"MerchantNote.merchant_id = Merchant.id",
				]
			],
			[
				'table' => 'note_types',
				'alias' => 'NoteType',
				'type' => 'LEFT',
				'conditions' => [
					'MerchantNote.note_type_id = NoteType.id'
				]
			],
			[
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => [
						'Merchant.user_id = User.id'
				]
			],
		];
		$filterArgs['action_type'] = 'Change Request';
		$result = $this->MerchantNote->setJoins($filterArgs);
		$this->assertEquals($notesJoinsExpected, $result);
	}

/**
 * testAddPCIEditNote
 *
 * @covers MerchantNote::addPCIEditNote
 * testAddPCIEditNote method
 *
 * @return void
 */
	public function testAddPCIEditNote() {
		$userId = '7da107ab-efda-4a39-8520-b61991bbe48a';
		$merchantId = '9f3c9149-6301-4b2b-873a-0d077999497d';
		$note = 'test note9ABCDEF';
		$result = $this->MerchantNote->addPCIEditNote($userId, $merchantId, $note);
		$this->assertContains('test note9ABCDEF', $result['MerchantNote']);
	}

/**
 * testShouldSave
 *
 * @covers MerchantNote::shouldSave
 * testShouldSave method
 *
 * @return void
 */
	public function testShouldSave() {
		$data = [
			'MerchantNote' => [
			[
				'note' => 'test note9ABCDEF'
				]
			]
		];
		$result = $this->MerchantNote->shouldSave($data);
		$this->assertTrue($result);

		$result = $this->MerchantNote->shouldSave(['should not save']);
		$this->assertFalse($result);
	}

/**
 * testGetNotesByMerchantId
 *
 * @covers MerchantNote::getNotesByMerchantId
 * testGetNotesByMerchantId method
 *
 * @return void
 */
	public function testGetNotesByMerchantId() {
		$data = [
			'id' => '00000000-0000-0000-0000-000000000003',
			'note_type_id' => NoteType::GENERAL_NOTE_ID,
			'user_id' => '047296f8-cdde-4e48-bd73-9094b8415bbc',
			'merchant_id' => '00000000-0000-0000-0000-000000000003',
			'note_date' => '2006-04-17',
			'note' => 'Merchant pays $25 monthly for equipment rental.',
			'note_title' => 'MSC Rental',
			'general_status' => 'COMP',
			'date_changed' => '2006-04-17',
			'critical' => 1,
			'note_sent' => null
		];
		$this->MerchantNote->save($data);
		$result = $this->MerchantNote->getNotesByMerchantId('00000000-0000-0000-0000-000000000003');

		$this->assertContains('00000000-0000-0000-0000-000000000003', $result['MerchantNote']['0']);
		unset($result);

		$result = $this->MerchantNote->getNotesByMerchantId('00000000-0000-0000-0000-000000000003', NoteType::GENERAL_NOTE_ID);
		$this->assertContains('00000000-0000-0000-0000-000000000003', $result['MerchantNote']['0']);
	}

/**
 * testGetNoteById
 *
 * @covers MerchantNote::getNoteById
 * testGetNoteById method
 *
 * @return void
 */
	public function testGetNoteById() {
		$expected = $data = [
			'id' => '00000000-0000-0000-0000-000000000003',
			'merchant_note_id_old' => null,
			'note_type_id_old' => null,
			'note_type_id' => '0bfee249-5c37-417c-aec7-83dcd2b2f566',
			'user_id_old' => null,
			'user_id' => '047296f8-cdde-4e48-bd73-9094b8415bbc',
			'merchant_id_old' => null,
			'merchant_id' => '00000000-0000-0000-0000-000000000003',
			'note_date' => '2006-04-17 00:00:00',
			'note' => 'Merchant pays $25 monthly for equipment rental.',
			'note_title' => 'MSC Rental',
			'general_status' => 'PEND',
			'date_changed' => '2006-04-17',
			'critical' => 1,
			'note_sent' => null,
			'resolved_date' => null,
			'resolved_time' => null,
			'loggable_log_id' => null,
			'change_id_old' => null,
			'approved_by_user_id' => null
		];
		$this->MerchantNote->save($data);
		$result = $this->MerchantNote->getNoteById('00000000-0000-0000-0000-000000000003');
		$this->assertEquals($expected, $result['MerchantNote']);
	}

/**
 * testGetStatusList
 *
 * @covers MerchantNote::getStatusList
 * testGetStatusList method
 *
 * @return void
 */
	public function testGetStatusList() {
		$expected = [
			'PEND' => __('Pending'),
			'COMP' => __('Completed'),
			'REJEC' => __('Rejected')
		];

		$result = $this->MerchantNote->getStatusList();
		$this->assertEquals($expected, $result);
	}

/**
 * testGetTypes
 *
 * @covers MerchantNote::getTypes
 * testGetTypes method
 *
 * @return void
 */
	public function testGetTypes() {
		$expected = [
			'General Note' => __('General Note'),
			'Programming Note' => __('Programming Note'),
			'Installation & Setup Note' => __('Installation & Setup Note'),
			'Change Request' => __('Change Request')
		];

		$result = $this->MerchantNote->getTypes();
		$this->assertEquals($expected, $result);
	}

/**
 * testGetStatusGnrOptions
 *
 * @covers MerchantNote::getStatusGnrOptions
 * testGetStatusGnrOptions method
 *
 * @return void
 */
	public function testGetStatusGnrOptions() {
		$expected = [
			'M' => __('Male'),
			'F' => __('Female')
		];

		$result = $this->MerchantNote->getStatusGnrOptions();
		$this->assertEquals($expected, $result);
	}

/**
 * testGroupNotes
 *
 * @covers MerchantNote::groupNotes
 * testGroupNotes method
 *
 * @test
 * @return void
 */
	public function testGroupNotes() {
		$merchantId = '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040';
		$merchant = $this->MerchantNote->Merchant->find('first', [
			'conditions' => ['Merchant.id' => $merchantId],
			'contain' => [
				'MerchantNote' => [
					'NoteType'
				]
			]
		]);

		$expected = [
			'PEND' => [],
			'COMP' => []
		];

		$result = $this->MerchantNote->groupNotes($merchant['MerchantNote']);
		$this->assertEquals($expected, $result);

		$merchantId = '00000000-0000-0000-0000-000000000004';
		$merchant = $this->MerchantNote->Merchant->find('first', [
			'conditions' => ['Merchant.id' => $merchantId],
			'contain' => [
				'MerchantNote' => [
					'NoteType'
				]
			]
		]);
		$expectedPend = 'PEND';
		$expectedCOMP = 'COMP';

		$result = $this->MerchantNote->groupNotes($merchant['MerchantNote']);

		$this->assertNotEmpty(Hash::get($result, 'PEND'));
		$this->assertEquals($expectedPend, Hash::get($result, 'PEND.0.general_status'));
		$this->assertNotEmpty(Hash::get($result, 'COMP'));
		$this->assertEquals($expectedCOMP, Hash::get($result, 'COMP.0.general_status'));
	}

/**
 * testEdit method
 *
 * @covers MerchantNote::edit
 * @return void
 */
	public function testEdit() {
		$MerchantNote = $this->getMockForModel('MerchantNote', ['emailNoteToRep', '_getCurrentUser']);
		$MerchantNote->expects($this->any())
			->method('emailNoteToRep')
			->will($this->returnValue(true));
		$MerchantNote->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
		$this->_mockSystemTransactionListener($MerchantNote, [
			'user_id' => AxiaTestCase::USER_ADMIN_ID,
		]);

		$merchantNoteId = '00000000-0000-0000-0000-000000000014';

		// Testing "Approve"
		$newData = [
			'MerchantNote' => [
				'id' => $merchantNoteId,
				'note_type_id' => 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'note_date' => '2015-12-07',
				'note' => 'New data COMP',
				'note_title' => 'New data COMP',
				'critical' => 1,
				'general_status' => MerchantNote::STATUS_COMPLETE,
				'note_sent' => 1,
				'loggable_log_id' => '56698fed-a29c-414d-8298-5792c0a8ad65',
				'dataForModel' => 'Merchant'
			]
		];

		$expected = $MerchantNote->edit($newData);
		$this->assertTrue($expected);

		$note = $MerchantNote->field('note', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('New data COMP', $note);

		$generalStatus = $MerchantNote->field('general_status', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('COMP', $generalStatus);

		// Testing "Approve failed"
		$newData = [
			'MerchantNote' => [
				'id' => $merchantNoteId,
				'note_type_id' => 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'note_date' => '2015-12-07',
				'note' => 'New data COMP',
				'note_title' => 'New data COMP',
				'critical' => 1,
				'general_status' => 'COMP',
				'note_sent' => 1,
				'loggable_log_id' => '67709fed-a29c-414d-8298-5792c0a8ad65',
				'dataForModel' => 'MerchantNote'
			],
			'approve-change-submit' => []
		];

		$expected = __('The merchant changes could not be applied. Please, try again.');
		$result = $MerchantNote->edit($newData);
		$this->assertEquals($expected, $result);

		$note = $MerchantNote->field('note', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('New data COMP', $note);

		$generalStatus = $MerchantNote->field('general_status', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('COMP', $generalStatus);

		// Testing "Rejected"
		$newData = [
			'MerchantNote' => [
				'id' => $merchantNoteId,
				'note_type_id' => 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'note_date' => '2015-12-07',
				'note' => 'New data REJECTED',
				'note_title' => 'New data REJECTED',
				'critical' => 1,
				'note_sent' => 1,
				'loggable_log_id' => '',
				'dataForModel' => 'MerchantChange'
			],
			'reject-change-submit' => []
		];

		$expected = $MerchantNote->edit($newData);
		$this->assertTrue($expected);

		$note = $MerchantNote->field('note', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('New data REJECTED', $note);

		$generalStatus = $MerchantNote->field('general_status', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('REJEC', $generalStatus);

		//Testing "No validation"
		$newData = [
			'MerchantNote' => [
				'id' => $merchantNoteId,
				//'note_type_id' => 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'note_date' => '2015-12-07',
				'note' => 'No Validation',
				'note_title' => 'No Validation',
				'general_status' => 'PEND',
				'critical' => 1,
				'note_sent' => 1,
				'loggable_log_id' => '',
				'dataForModel' => 'MerchantChange'
			]
		];

		$expected = $MerchantNote->edit($newData, false);
		$this->assertTrue($expected);

		$note = $MerchantNote->field('note', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('No Validation', $note);

		$generalStatus = $MerchantNote->field('general_status', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertEquals('PEND', $generalStatus);

		//Testing falling to save
		$newData = [
			'MerchantNote' => [
				'id' => $merchantNoteId,
				//'note_type_id' => 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2',
				'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				'merchant_id' => '00000000-0000-0000-0000-000000000004',
				'note_date' => '2015-12-07',
				'note' => 'not saved',
				'note_title' => 'not saved',
				'general_status' => 'PEND',
				'critical' => 1,
				'note_sent' => 1,
				'loggable_log_id' => '',
				'dataForModel' => 'MerchantChange'
			]
		];

		$expected = __('The merchant note could not be saved. Please, try again.');
		$result = $MerchantNote->edit($newData);
		$this->assertEquals($expected, $result);

		$note = $MerchantNote->field('note', ['MerchantNote.id' => $merchantNoteId]);
		$this->assertNotEquals('not saved', $note);
	}

/**
 * testUserHasAccess
 *
 * @covers MerchantNote::userHasAccess
 * testUserHasAccess method
 *
 * @return void
 */
	public function testUserHasAccess() {
		$merchantNoteId = '00000000-0000-0000-0000-000000000014';
		$adminUser = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
		$result = $this->MerchantNote->userHasAccess($merchantNoteId, $adminUser);
		$this->assertTrue($result);

		$noteOwnerId = '113114ae-da7b-4970-94bd-fa0c0f25c786';
		$result = $this->MerchantNote->userHasAccess($merchantNoteId, $noteOwnerId);
		$this->assertTrue($result);

		$userId = '00000000-0000-0000-0000-000000000004';
		$result = $this->MerchantNote->userHasAccess($merchantNoteId, $userId);
		$this->assertFalse($result);
	}

/**
 * testMerchantOrConditions
 *
 * @covers MerchantNote::merchantOrConditions
 * testMerchantOrConditions method
 *
 * @return void
 */
	public function testMerchantOrConditions() {
		$data = ['dba_mid' => 'merchant_note'];
		$result = $this->MerchantNote->merchantOrConditions($data);
		$expected = ['AND' => [
				'OR' => [
					'Merchant.merchant_mid ILIKE' => '%merchant_note%',
					'Merchant.merchant_dba ILIKE' => '%merchant_note%'
				]
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testGetEditFormRelatedData
 *
 * @covers MerchantNote::getEditFormRelatedData
 * testGetEditFormRelatedData method
 *
 * @return void
 */
	public function testGetEditFormRelatedData() {
		$data = [];

		$expected = [
			'controllerName' => '',
			'actionName' => 'edit',
			'loggedForeignModel' => [],
			'merchant' => [],
			'isGeneralNote' => false,
			'needToApproveChanges' => false,
			'userCanApproveChanges' => true,
			'isModal' => null,
			'actionName' => 'edit',
			'loggedForeignModel' => []
		];
		$result = $this->MerchantNote->getEditFormRelatedData($data);
		$this->assertEquals($expected, $result);

		$merchantNoteId = '00000000-0000-0000-0000-000000000014';
		$contain = [
			'contain' => [
				'Merchant',
				'NoteType',
			]
		];

		$expected = [
			'isModal' => false,
			'controllerName' => 'Merchants',
			'actionName' => 'edit',
			'loggedForeignModel' => [
				'LoggableLog' => [
					'model' => 'Merchant',
					'action' => 'newChangeRequest',
					'foreign_key' => '592b1d43-f312-44e9-8e4d-b8665ee212bc',
					'controller_action' => null
				]
			],
			'merchant' => [
				'Merchant' => [
					'merchant_dba' => '16 Hands',
					'merchant_mid' => '3948906204000946',
					'id' => '00000000-0000-0000-0000-000000000004',
					'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
				],
				'User' => [
					'user_first_name' => 'Mark',
					'user_last_name' => 'Weatherford',
					'id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
				]
			],
			'isGeneralNote' => false,
			'needToApproveChanges' => false,
			'userCanApproveChanges' => true,
			'isModal' => null
		];

		$data = $this->MerchantNote->get($merchantNoteId, $contain);
		$result = $this->MerchantNote->getEditFormRelatedData($data);
		$this->assertEquals($expected, $result);
	}

/**
 * testBeforeSave
 *
 * @param string $status MerchantNote status
 * @param mixed $expectedDate Expected resolved date
 * @param bool $isEmptyTime Expected if resolved time is empty
 * @dataProvider providerBeforeSave
 * @return void
 */
	public function testBeforeSave($status, $expectedDate, $isEmptyTime) {
		$userId = AxiaTestCase::USER_SM_ID;
		$MerchantNote = $this->getMockForModel('MerchantNote', ['_getCurrentUser']);
		$MerchantNote->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue($userId));

		$this->_mockSystemTransactionListener($MerchantNote, [
			'user_id' => $userId,
		]);

		$data = [
			'note_type_id_old' => null,
			'note_type_id' => 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2',
			'user_id' => '00000000-0000-0000-0000-000000000001',
			'merchant_id' => '00000000-0000-0000-0000-000000000001',
			'note_date' => '2001-01-01',
			'note' => 'Test before save',
			'note_title' => 'Test before save',
			'general_status' => $status,
		];
		$this->assertTrue($MerchantNote->save($data));
		$note = $MerchantNote->read(null, $MerchantNote->id);
		$this->assertEquals($expectedDate, Hash::get($note, 'MerchantNote.resolved_date'));
		$this->assertEquals($isEmptyTime, empty(Hash::get($note, 'MerchantNote.resolved_time')));
		unset($MerchantNote);
	}

/**
 * Provider for testBeforeSave
 *
 * @return array
 */
	public function providerBeforeSave() {
		return [
			[MerchantNote::STATUS_COMPLETE, date('Y-m-d'), false],
			[MerchantNote::STATUS_PENDING, null, true],
		];
	}
}
