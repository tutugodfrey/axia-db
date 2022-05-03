<?php

App::uses('AppModel', 'Model');

/**
 * JobStatus Model
 *
 */
class JobStatus extends AppModel {

/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'rank';
/**
 * Name of Possibible statuses
 *
 */
	const IN_PROGRESS = '709a73b7-be02-44ad-a95f-e009e1601a7e';
	const IDLE = 'b2913141-d790-4dc9-bf64-991d08dbf200';
	const FAILURE = '17d206d2-97f8-4d99-bd1e-c13c349e284f';
	const FINISHED = '02b4534a-fe71-4db3-ab07-330b1839ecf0';

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Utils.List' => array(
			'positionColumn' => self::SORT_FIELD,
		)
	);

/**
 * Default Order
 *
 * @var array
 */
	public $order = "JobStatus.rank ASC";

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		],
		'rank' => [
			'numeric' => [
				'rule' => ['numeric']
			]
		]
	];
}