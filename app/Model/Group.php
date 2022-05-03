<?php

App::uses('AppModel', 'Model');

/**
 * Group Model
 *
 * @property Group $Group
 * @property Merchant $Merchant
 * @property OnlineappUser $OnlineappUser
 * @property Group $Group
 * @property Onlineapp $Onlineapp
 * @property Permission $Permission
 */
class Group extends AppModel {

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
		'group_description' => array(
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

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'group_id',
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
