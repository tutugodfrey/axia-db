<?php

App::uses('AppModel', 'Model');

/**
 * Organization Model
 *
 */
class Organization extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be blank'
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'That Organization already exists, you must enter an unique one!',
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
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'organization_id',
			'dependent' => true
		),
		'Subregion' => array(
			'className' => 'Subregion',
			'foreignKey' => 'organization_id',
			'dependent' => true
		),
	);
}
