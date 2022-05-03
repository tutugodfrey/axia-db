<?php
App::uses('BackgroundJob', 'Model');
App::uses('JobStatus', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * BackgroundJob Test Case
 */
class BackgroundJobTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BackgroundJob = ClassRegistry::init('BackgroundJob');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BackgroundJob);

		parent::tearDown();
	}

/**
* testAfterSave
* Test that older records are deleted
*
* @covers BackgroundJob::afterSave
* @return void
*/
	public function testAfterSave() {
		$record = [
			'job_status_id' => CakeText::uuid(),
			'description' => 'This old record should be auto-deleted in afterSave callback',
			'modified' => '1999-01-01 12:00:00'
		];
		$this->BackgroundJob->create();
		$saveData = $this->BackgroundJob->save($record);
		$exists = $this->BackgroundJob->exists($saveData['BackgroundJob']['id']);
		$this->assertFalse($exists);

		//Save another recent record that should not be deleted on afterSave callback
		$record['modified'] = date('Y-m-d H:i:s');
		$this->BackgroundJob->create();
		$saveData = $this->BackgroundJob->save($record);
		$exists = $this->BackgroundJob->exists($saveData['BackgroundJob']['id']);
		$this->assertTrue($exists);
	}

/**
* testAddToQueue
* Test that records are added to the queue
*
* @covers BackgroundJob::addToQueue()
* @return void
*/
	public function testAddToQueue() {
		$id = CakeText::uuid();
		$this->BackgroundJob->addToQueue('Test Add to Queue', $id);
		
		$exists = $this->BackgroundJob->exists($id);
		$this->assertTrue($exists);

		$id = $this->BackgroundJob->addToQueue('Add another one without id Param');
		$exists = $this->BackgroundJob->exists($id);
		$this->assertTrue($exists);
	}

/**
* testInProgressExceptionThrown
*
* @expectedException Exception
* @covers BackgroundJob::inProgress()
* @return void
*/
	public function testInProgressExceptionThrown() {
		$this->BackgroundJob->inProgress(CakeText::uuid());
	}

/**
* testInProgress
* Test that records are updated with status of JobStatus::IN_PROGRESS
*
* @covers BackgroundJob::inProgress()
* @return void
*/
	public function testInProgress() {
		$id = CakeText::uuid();
		$this->BackgroundJob->addToQueue('Test Add to Queue', $id);
		$this->BackgroundJob->inProgress($id);
		$actual = $this->BackgroundJob->field('job_status_id', ['id' => $id]);
		
		$this->assertSame(JobStatus::IN_PROGRESS, $actual);
	}

/**
* testFinishedExceptionThrown
*
* @expectedException Exception
* @covers BackgroundJob::finished()
* @return void
*/
	public function testFinishedExceptionThrown() {
		$this->BackgroundJob->inProgress(CakeText::uuid());
	}

/**
* testFinished
* Test that records are updated with status of JobStatus::FINISHED and JobStatus::FAILURE
*
* @covers BackgroundJob::finished()
* @return void
*/
	public function testFinished() {
		$id = CakeText::uuid();
		$this->BackgroundJob->addToQueue('Test Add to Queue', $id);
		$this->BackgroundJob->finished($id, true);
		$actual = $this->BackgroundJob->field('job_status_id', ['id' => $id]);
		
		$this->assertSame(JobStatus::FINISHED, $actual);
		$this->BackgroundJob->finished($id, false);
		$actual = $this->BackgroundJob->field('job_status_id', ['id' => $id]);
		
		$this->assertSame(JobStatus::FAILURE, $actual);
	}

/**
* testGetViewData
* Test that records are updated with status of JobStatus::FINISHED and JobStatus::FAILURE
*
* @covers BackgroundJob::getViewData()
* @return void
*/
	public function testGetViewData() {
		$record = [
			'job_status_id' => CakeText::uuid(),
			'description' => 'Test Job',
			'modified' => date("Y-m-d H:i:s")
		];
		$this->BackgroundJob->create();
		$saveData = $this->BackgroundJob->save($record);
		$actual = $this->BackgroundJob->getViewData();

		$this->assertCount(1, $actual['processesList']);
		$this->assertSame($record['description'], $actual['processesList'][0]['BackgroundJob']['description']);
		$this->assertSame([
			'processing' => JobStatus::IN_PROGRESS,
			'idle' => JobStatus::IDLE,
			'error' => JobStatus::FAILURE,
			'done' => JobStatus::FINISHED,
		], $actual['statusList']);
	}
}
