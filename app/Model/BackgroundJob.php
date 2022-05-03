<?php
App::uses('AppModel', 'Model');
App::uses('JobStatus', 'Model');
/**
 * BackgroundJob Model
 *
 * @property JobStatus $JobStatus
 */
class BackgroundJob extends AppModel {


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'JobStatus' => array(
			'className' => 'JobStatus',
			'foreignKey' => 'job_status_id',
		)
	);

/**
 * afterSave callback
 *
 * @param $created boolean
 * @param $options array
 * @return void
 */
	public function afterSave($created, $options = array()) {
		//only keep 2 days worth of records at all times
		$this->deleteAll([
			'BackgroundJob.modified < ' . "'" . date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . ' -2 days')) . "'"]
			, false
		);
	}

/**
 * addToQueue
 * Adds a Backgrounded job entry for tracking with a status id of JobStatus::IDLE
 * This method should be called IFF the job being tracked is about to start (before it actually starts)
 * For example if the CakeResque plugin is used to background the process, 
 * then call this method just before before invoking CakeResque::enqueue()
 * 
 * @param string $description a brief descptiption of the process (less than 200 characters)
 * @param string $id the UUID to give to the backgrounded job to track. If empty a new id will be generated and returned
 * @return mixed boolean|string if param is not empty, returns true on success or false on failure. Otherwise, returns the id generated for the job tracker added to queue or false on falure;
 */
	public function addToQueue($description, $id = null) {
		if (strlen($description) > 200) {
			substr($string,0, 197) . "...";
		}
		$data = ['job_status_id' => JobStatus::IDLE, 'description' => $description];
		if (!empty($id)) {
			$data['id'] = $id;
		} else {
			$this->create();
		}
		if($this->save($data)) {
			return (empty($id))? $this->id : true;
		} else {
			false;
		}
	}

/**
 * inProgress
 * Updates backgrounded job tracker with a status id of JobStatus::IN_PROGRESS
 *
 * @param string $id Backgrounded job tracker id
 * @return boolean true on success, false on falure
 * @throws Exception if no process exists with given id
 */
	public function inProgress($id) {
		if (!$this->exists($id)) {
			throw new Exception("Background Job Tracker with id:'$id' does not exist!");
		}
		return (bool)$this->save(['id' => $id, 'job_status_id' => JobStatus::IN_PROGRESS]);
	}
/**
 * finished
 * Updates backgrounded job tracker with a status id of JobStatus::FINISHED if successful or JobStatus::FAILURE
 *
 * @param string $id Backgrounded job tracker id
 * @param boolean $successful whether the backgrounded job ended successfully. False will update tracker with a status id of JobStatus::FAILURE otherwise JobStatus::FINISHED
 * @return boolean true on success, false on falure
 * @throws Exception if no process exists with given id
 */
	public function finished($id, $successful) {
		if (!$this->exists($id)) {
			throw new Exception("Background Job Tracker with id:'$id' does not exist!");
		}

		if ($successful === true) {
			return (bool)$this->save(['id' => $id, 'job_status_id' => JobStatus::FINISHED]);
		}
		return (bool)$this->save(['id' => $id, 'job_status_id' => JobStatus::FAILURE]);
	}

/**
 * getViewData
 * Returns all tracked backgrounded jobs and their corresponding statuses
 *
 * @return array
 */
	public function getViewData() {
		$processesList = $this->getJobStatuses();
		$statusList = [
			'processing' => JobStatus::IN_PROGRESS,
			'idle' => JobStatus::IDLE,
			'error' => JobStatus::FAILURE,
			'done' => JobStatus::FINISHED,
		];
		return compact('processesList', 'statusList');
	}
/**
 * getJobStatuses
 * Returns all tracked backgrounded jobs and their corresponding statuses
 *
 * @return array
 */
	public function getJobStatuses() {
		return $this->find('all', [
			'fields' => [
				'BackgroundJob.description',
				'BackgroundJob.modified',
				'JobStatus.id',
				'JobStatus.name',
			],
			'order' => ['JobStatus.rank ASC', 'BackgroundJob.modified DESC'],
			'joins' => [
				[
					'table' => 'job_statuses',
					'alias' => 'JobStatus',
					'type' => 'LEFT',
					'conditions' => [
						'BackgroundJob.job_status_id = JobStatus.id'
					],
				]
			]
		]);
	}
}
