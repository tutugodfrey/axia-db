<?php

App::uses('AppModel', 'Model');

/**
 * GiftCard Model
 *
 * @property Merchant $Merchant
 */
class GiftCard extends AppModel {

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
			'ChangeRequest'
		);

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
		'gc_mid' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'gc_plan' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'gc_statement_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_gift_item_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_loyalty_item_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_chip_card_one_rate_monthly' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_loyalty_mgmt_database' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_application_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_merch_prov_art_setup_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_training_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'gc_card_reorder_fee' => array(
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
			),
			'GiftCardProvider' => array(
						'className' => 'GiftCardProvider',
						'foreignKey' => 'gift_card_provider_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
	);
}
