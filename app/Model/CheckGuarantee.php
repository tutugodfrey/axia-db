<?php

App::uses('AppModel', 'Model');

/**
 * CheckGuarantee Model
 *
 * @property Merchant $Merchant
 */
class CheckGuarantee extends AppModel {

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
		'cg_mid' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'cg_account_number' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'cg_station_number' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'cg_transaction_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
		'cg_per_item_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'cg_monthly_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'cg_monthly_minimum_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'rep_processing_rate_pct' => array(
			'validPercentage' => array(
				'rule' => 'validPercentage',
				'allowEmpty' => true,
			)
		),
		'rep_per_item_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => ' must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'rep_monthly_cost' => array(
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
			'CheckGuaranteeProvider' => array(
						'className' => 'CheckGuaranteeProvider',
						'foreignKey' => 'check_guarantee_provider_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'CheckGuaranteeServiceType' => array(
						'className' => 'CheckGuaranteeServiceType',
						'foreignKey' => 'check_guarantee_service_type_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
	);

/**
 * setMenuData
 * sets and returns data to use for menues in edit mode view
 *  
 * @return array
 */
	public function setMenuData() {
		$checkGuaranteeProviders = $this->CheckGuaranteeProvider->getList();
		$checkGuaranteeServiceTypes = $this->CheckGuaranteeServiceType->getList();

		return compact(
			'checkGuaranteeProviders',
			'checkGuaranteeServiceTypes'
		);
	}
}
