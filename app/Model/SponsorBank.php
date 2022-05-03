<?php

App::uses('AppModel', 'Model');

/**
 * SponsorBank Model
 *
 */
class SponsorBank extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'bank_name' => array(
				'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Field required'
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Bank name already exists, you must enter an unique one!',
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
 * getSponsorBanksList() method
 * 
 * @return array
 */
	public function getSponsorBanksList() {
		return $this->find('list', array('fields' => array('id', 'bank_name')));
	}

}
