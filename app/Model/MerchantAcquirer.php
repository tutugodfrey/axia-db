<?php

App::uses('AppModel', 'Model');

/**
 * MerchantAcquirer Model
 *
 * @property Acquirer $Acquirer
 */
class MerchantAcquirer extends AppModel {

	public $displayField = 'acquirer';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'acquirer_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'acquirer' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Aquirer already exists, you must enter an unique one!',
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

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $hasMany = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_acquirer_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
