<?php

App::uses('AppModel', 'Model');

/**
 * UwInfodoc Model
 *
 */
class UwInfodoc extends AppModel {

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
 * Custom finder
 *
 * @var array
 */
	public $findMethods = ['byRequired' => true];

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
		'required' => [
			'boolean' => [
				'rule' => ['boolean']
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
/**
 * Special find to get UwInfoDocs
 *
 * @param string $state query state
 * @param array $query array
 * @param array $results results
 * @return array
 */
	protected function _findByRequired($state, $query, $results = array()) {
		if ($state === 'before') {
			$query['order'] = ["{$this->alias}.priority" => 'ASC'];

			$query['conditions']["{$this->alias}.required"] = (bool)Hash::get($query, 'required');

			return $query;
		}

		return $results;
	}
}