<?php

App::uses('AppModel', 'Model');
App::uses('AxiaCalculate', 'Model');

class ProductSetting extends AppModel {

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'ChangeRequest'
	);

/**
 * validation
 *
 * @var array
 */
	public $validate = array(
		'generic_product_mid' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'gral_fee_multiplier' => array(
				'naturalNumber' => array(
					'rule' => 'naturalNumber',
					'allowEmpty' => true,
					'requred' => false,
					'message' => 'Enter non-negative whole numbers.'
				)
			),
		'gral_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'allowEmpty' => true,
					'requred' => false,
					'message' => 'Enter non-negative numbers with up to 3 decimals.'
				)
			),
		'monthly_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'allowEmpty' => true,
					'requred' => false,
					'message' => 'Enter non-negative numbers with up to 3 decimals.'
				)
			),
		'per_item_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'allowEmpty' => true,
					'requred' => false,
					'message' => 'Enter non-negative numbers with up to 3 decimals.'
				)
			),
		'monthly_total' => array(
				'validateCalculation' => array(
					'rule' => 'validateCalculation',
					'allowEmpty' => true,
					'message' => 'Total Monthly Fee calculation failed validation, please try again.'
				)
			),
		'rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
	);

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'ProductSetting';

/**
 * belongsTo association
 *
 * @var array $belongsTo 
 * @access public
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
		),
		'ProductsServicesType' => array(
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
		),
		'ProductFeature' => array(
			'className' => 'ProductFeature',
			'foreignKey' => 'product_feature_id',
		)
	);

/**
 * validateCalculation
 *
 * @param array $check field to check
 * @return boolean
 */
	public function validateCalculation($check) {
		$AxiaCalculate = new AxiaCalculate();
		$check = array_pop($check);
		$total = $AxiaCalculate->multSumEqType1($this->data[$this->alias], "gral_fee", "gral_fee_multiplier", "monthly_fee");
		return $check == $total;
	}
}
