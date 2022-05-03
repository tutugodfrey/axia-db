<?php

App::uses('AppModel', 'Model');

/**
 * UwStatus Model
 *
 */
class UwStatus extends AppModel {

/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'priority';

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
	public $order = "UwStatus.priority ASC";

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank']
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'Name already exists, you must enter an unique one!',
				'allowEmpty' => false,
			],
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			]
		],
		'priority' => [
			'numeric' => [
				'rule' => ['numeric']
			]
		]
	];
}