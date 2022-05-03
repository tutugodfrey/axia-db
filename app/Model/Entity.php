<?php

App::uses('AppModel', 'Model');

/**
 * Entity Model
 *
 */
class Entity extends AppModel {

	public $displayField = 'entity_name';
/**
 * Axia Payments entity id
 *
 */
	const AX_PAY_ID = '2843e82e-c32f-4aa8-a8ee-a6edbc418f0a';

/**
 * Axia Tech entity id
 *
 */
	const AX_TECH_ID = '4e67ab64-a424-4c22-a13e-ffc78a45c421';


/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'entity' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must enter a unique entity name',
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Entity already exists, you must enter an unique name.',
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

/**
 * hasMany
 *
 * @var array
 */
	public $hasMany = array(
		'User'
	);

}
