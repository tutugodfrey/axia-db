<?php

App::uses('AppModel', 'Model');

/**
 * MerchantUwFinalStatus Model
 *
 */
class MerchantUwFinalStatus extends AppModel {

/**
 * Final statuses strings as saved in the DB
 *
 * @var string
 */
	const APPROVED = 'Approved';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Field is required',
				'allowEmpty' => false,
				'required' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'That status name already exists, you must enter an unique one!',
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
 * getUwFinalStatusesList() method
 * 
 * @return array
 */
	public function getUwFinalStatusesList() {
		return $this->find('list', array('fields' => array('id', 'name')));
	}

}
