<?php

App::uses('AppModel', 'Model');

/**
 * Network Model
 *
 * @property Network $Network
 * @property Merchant $Merchant
 */
class Network extends AppModel {

/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'position';

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'network_description';
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
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'network_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Field is required',
				'allowEmpty' => false,
				'required' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Network name already exists, you must enter an unique one!',
				'allowEmpty' => false,
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be remove

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'network_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
}
