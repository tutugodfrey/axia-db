<?php
App::uses('JobStatus', 'Model');
class CreateBgProcessTrackingTables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_bg_process_tracking_tables';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'background_jobs' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false
					),
					'job_status_id' => array(
						'type' => 'uuid',
						'null' => false
					),
					'description' => array(
						'type' => 'string',
						'length' => '200',
						'default' => null
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => false,
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'BG_JOBS_STATUS_ID' => array(
							'column' => 'job_status_id',
							'unique' => false
						),
						'BG_JOBS_MODIFIED' => array(
							'column' => 'modified',
							'unique' => false
						),
					)
				),
				'job_statuses' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false
					),
					'name' => array(
						'type' => 'string',
						'length' => '100',
						'null' => false
					),
					'rank' => array(
						'type' => 'integer',
						'null' => false
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'background_jobs',
				'job_statuses'
			)
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'up') {
			$JobStatus = $this->generateModel('JobStatus');
			$data = [
				[
					'id' => JobStatus::IN_PROGRESS,
					'name' => 'In Progress',
					'rank' => 1
				],
				[
					'id' => JobStatus::IDLE,
					'name' => 'Waiting',
					'rank' => 2
				],
				[
					'id' => JobStatus::FAILURE,
					'name' => 'Error',
					'rank' => 3
				],
				[
					'id' => JobStatus::FINISHED,
					'name' => 'Done',
					'rank' => 4
				],
			];
			$JobStatus->saveMany($data);
		}
		return true;
	}
}
