<?php

App::uses('AppModel', 'Model');

/**
 * UwApprovalinfo Model
 *
 */
class UwApprovalinfo extends AppModel {

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
	public $order = "UwApprovalinfo.priority ASC";

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
			]
		],
		'priority' => [
			'numeric' => [
				'rule' => ['numeric']
			]
		],
		'verified_type' => [
			'notBlank' => [
				'rule' => ['notBlank']
			],
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			]
		]
	];


/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['name'])) {
			$this->data[$this->alias]['name'] = $this->removeAnyMarkUp($this->data[$this->alias]['name']);
		}
		
		return parent::beforeSave($options);
	}
}