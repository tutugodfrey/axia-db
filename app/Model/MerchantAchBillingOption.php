<?php

App::uses('AppModel', 'Model');

/**
 * MerchantAchBillingOption Model
 *
 * @property MerchantAch $MerchantAch
 */
class MerchantAchBillingOption extends AppModel {

/**
 * String constant for Bill Rep option
 * 
 */
	const BILL_REP = "Rep";
/**
 * String constant for Dont Bill Rep option
 * 
 */
	const DONT_BILL_REP = "Don't Bill Rep";
	const CLIENT = "Client";
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'billing_option_id_old' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true,
			),
		),
		'billing_option_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Billing option already exists, you must enter an unique description!',
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
 * belongsTo associations
 *
 * @var array
 */
	public $hasMany = array(
		'MerchantAch' => array(
			'className' => 'MerchantAch',
			'foreignKey' => 'billing_option_id',
		)
	);

/**
 * getABillinOptionList method
 *
 * @return array
 */
	public function getBillingOptionsList() {
		return $this->find('list', array('fields' => array('id', 'billing_option_description')));
	}

}
