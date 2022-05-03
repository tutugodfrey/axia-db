<?php

App::uses('TimelineItem', 'Model');
App::uses('TimelineEntry', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * TimelineEntry Test Case
 *
 */
class TimelineEntryTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TimelineEntry = ClassRegistry::init('TimelineEntry');
		$this->TimelineItem = ClassRegistry::init('TimelineItem');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TimelineEntry);
		parent::tearDown();
	}

/**
 * testGetTimelineEntries
 *
 * @return void
 */
	public function testGetTimelineEntries() {
		$merchantId = '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa'; //this one has data in test_db.sql
		$actual = $this->TimelineEntry->getTimelineEntries($merchantId);
		$expected = $this->TimelineItem->find('all', [
			'contain' => [
				'TimelineEntry' => [
					'conditions' => [
						'TimelineEntry.merchant_id' => $merchantId
					],
					'order' => ['TimelineEntry.timeline_date_completed' => 'ASC']
					]
				],
				'order' => 'TimelineEntry.timeline_date_completed ASC NULLS last, TimelineItem.timeline_item_description ASC'
			]);

		$this->assertSame($expected, $actual);
	}

/**
 * testCleanRequestData
 *
 * @return void
 */
	public function testCleanRequestData() {
		//Emulate one empty entry form request data submission
		$data = [
			[
				'TimelineEntry' => [
					'id' => '2ce06a07-4572-4616-900b-7944fac74fc7',
					'timeline_item_old' => 'FBL',
					'timeline_item_description' => 'File Built'
				],
				'TimelineEntry' => [
					'id' => null,
					'merchant_id' => null,
					'merchant_id_old' => null,
					'timeline_item_id' => null,
					'timeline_item_old' => null,
					'timeline_date_completed' => null,
					'action_flag' => null
				]
			],
			[
				'TimelineEntry' => [
					'id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => 'APP',
					'timeline_item_description' => 'Approved'
				],
				'TimelineEntry' => [
					'id' => '00000000-0000-0000-0000-000000000011',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'merchant_id_old' => null,
					'timeline_item_id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => null,
					'timeline_date_completed' => '2016-04-20',
					'action_flag' => true
				]
			],
		];
		$actual = $this->TimelineEntry->cleanRequestData($data);
		//Clean array should only have the non empty subset
		$expected = [
			[
				'TimelineEntry' => [
					'id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => 'APP',
					'timeline_item_description' => 'Approved'
				],
				'TimelineEntry' => [
					'id' => '00000000-0000-0000-0000-000000000011',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'merchant_id_old' => null,
					'timeline_item_id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => null,
					'timeline_date_completed' => '2016-04-20',
					'action_flag' => true
				]
			],
		];
		$this->assertSame($expected, $actual);

		//Emulate one empty entry and one existing entry that was cleared out
		$data = [
			[
				'TimelineEntry' => [
					'id' => '2ce06a07-4572-4616-900b-7944fac74fc7',
					'timeline_item_old' => 'FBL',
					'timeline_item_description' => 'File Built'
				],
				'TimelineEntry' => [
					'id' => null,
					'merchant_id' => null,
					'merchant_id_old' => null,
					'timeline_item_id' => null,
					'timeline_item_old' => null,
					'timeline_date_completed' => null,
					'action_flag' => null
				]
			],
			[
				'TimelineEntry' => [
					'id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => 'APP',
					'timeline_item_description' => 'Approved'
				],
				'TimelineEntry' => [
					'id' => '00000000-0000-0000-0000-000000000011',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'merchant_id_old' => null,
					'timeline_item_id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => null,
					'timeline_date_completed' => null,
					'action_flag' => false
				]
			],
		];
		$actual = $this->TimelineEntry->cleanRequestData($data);
		//Clean array should only have the existinc cleared-out subset
		$expected = [
			[
				'TimelineEntry' => [
					'id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => 'APP',
					'timeline_item_description' => 'Approved'
				],
				'TimelineEntry' => [
					'id' => '00000000-0000-0000-0000-000000000011',
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'merchant_id_old' => null,
					'timeline_item_id' => 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66',
					'timeline_item_old' => null,
					'timeline_date_completed' => null,
					'action_flag' => false
				]
			],
		];
		$this->assertSame($expected, $actual);
	}

/**
 * testCleanRequestData
 *
 * @return void
 */
	public function testRemoveDuplicates() {
		$tstData = [
			[
				'TimelineEntry' => [
					'merchant_id' => '4e3587be-aafb-48c4-9b6b-8dd26b8e94aa',
					'timeline_item_id' => 'f8014e00-1acc-409e-8cc9-a9ac691b4cb4',
					'timeline_date_completed' => '2016-04-20',
				]
			]
		];
		//nothing removed since record does not exist
		$result = $this->TimelineEntry->removeDuplicates($tstData);
		$this->assertSame($tstData, $result);
		$this->assertFalse($this->TimelineEntry->hasAny($tstData[0]['TimelineEntry']));
		$result = $this->TimelineEntry->saveMany($tstData);

		$expected = [];
		//record removed
		$result = $this->TimelineEntry->removeDuplicates($tstData);
		$this->assertSame($expected, $result);
	}
}
