<?php

App::uses('AppModel', 'Model');

/**
 * WebBasedAch Model
 *
 * @property Merchant $Merchant
 */
class WebBasedAch extends AppModel {

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
			'ChangeRequest'
		);

	public $useTable = 'virtual_check_webs';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
			'rule' => 'notBlank',
			'allowEmpty' => false,
			'message' => 'Merchant id cannot be blank'
			)
		),
		'vcweb_mid' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'vcweb_web_based_rate' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'vcweb_web_based_pi' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'vcweb_monthly_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
