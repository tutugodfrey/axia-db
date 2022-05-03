<?php

App::uses('AppModel', 'Model');

/**
 * MerchantType Model
 *
 * @property MerchantType $MerchantType
 * @property Merchant $Merchant
 */
class MerchantType extends AppModel {

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'type_description';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'type_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Field is required',
				'allowEmpty' => false,
				'required' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Type already exists, you must enter an unique one!',
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
 * getAcquiringTypeId
 * Returns the uuid of the Acquiring type
 * 
 * @return string uuid
 */
	public function getAcquiringTypeId() {
		$data = $this->find('first', array('conditions' => array('type_description' => 'Acquiring')));
		return $data['MerchantType']['id'];
	}
	
}
