<?php

App::uses('AppModel', 'Model');

/**
 * PaymentFusion Model
 *
 */
class PaymentFusion extends AppModel {

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
		'generic_product_mid' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'required' => true,
				'message' => 'Product id is required'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => '* * Sorry that ID is already taken by another merchant.'
			),
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'account_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Fee must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'rate' => array(
			'validPercent' => array(
				'rule' => 'validPercentage',
				'message' => 'Must be a valid percentage',
				'allowEmpty' => true
			)
		),
		'per_item_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Fee must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'standard_num_devices' => array(
			'validNaturalNum' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Must be a valid natural number or blank',
				'allowEmpty' => true
			)
		),
		'standard_device_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Fee must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'vp2pe_num_devices' => array(
			'validNaturalNum' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Must be a valid natural number or blank',
				'allowEmpty' => true
			)
		),
		'vp2pe_device_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Fee must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'pfcc_num_devices' => array(
			'validNaturalNum' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Must be a valid natural number or blank',
				'allowEmpty' => true
			)
		),
		'pfcc_device_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Fee must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'vp2pe_pfcc_num_devices' => array(
			'validNaturalNum' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Must be a valid natural number or blank',
				'allowEmpty' => true
			)
		),
		'vp2pe_pfcc_device_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Fee must be a valid amount number',
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
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'PaymentFusionsProductFeature' => array(
			'className' => 'PaymentFusionsProductFeature',
			'foreignKey' => 'payment_fusion_id',
		),
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'ProductFeature' => array(
			'className' => 'ProductFeature',
			'joinTable' => 'payment_fusions_product_features',
			'foreignKey' => 'payment_fusion_id',
			'associationForeignKey' => 'product_feature_id',
			'unique' => true,
		),
	);

/**
 * beforeSave callback
 *
 * @param $options array
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (isset($this->data['PaymentFusion']['generic_product_mid'])) {
			$this->data['PaymentFusion']['generic_product_mid'] = trim($this->data['PaymentFusion']['generic_product_mid']);
		}
		if (!empty($this->data['PaymentFusion']['other_features'])) {
			$this->data[$this->alias]['other_features'] = $this->removeAnyMarkUp($this->data[$this->alias]['other_features']);
		}
		return true;
	}
/**
 * getEditViewData
 *
 * @param string $id MerchantPricing id
 * @return array merchant pricing data
 */
	public function getEditViewData($id) {
		$options = array(
			'conditions' => array('id' => $id),
			'contain' => array('PaymentFusionsProductFeature.ProductFeature')
			);
		$data = $this->find('first', $options);
		return $data;
	}
}
