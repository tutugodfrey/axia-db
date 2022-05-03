<?php

App::uses('AppModel', 'Model');

/**
 * Region Model
 *
 */
class Region extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be blank'
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
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Organization' => array(
			'className' => 'Organization',
			'foreignKey' => 'organization_id',
		),
	);

/**
 * getByOrganization
 * Returns a list of regions tha belongTo the passed Organization.id param
 *
 * @param string $orgId an associated model's Organization id
 * @return array list of regions tha belongTo the passed Organization.id param
 */
	public function getByOrganization($orgId) {
		return $this->find('list', ['conditions' => ['organization_id' => $orgId]]);
	}

}
