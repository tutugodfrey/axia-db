<?php

App::uses('AppModel', 'Model');

/**
 * MerchantBin Model
 *
 * @property Bin $Bin
 */
class MerchantBin extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'bin' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Field required'
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'BIN number already exists, you must enter an unique one!',
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

}
