<?php

App::uses('AppModel', 'Model');

/**
 * Gateway1 Model
 *
 * @property Merchant $Merchant
 * @property Gw1Gateway $Gw1Gateway
 */
class Gateway1 extends AppModel {

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
		'gw1_gateway_id' => array(
				'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'A Gateway is required'
			)
		),
		'gw1_mid' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'A Gateway ID cannot be blank'
			),
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'gw1_rate' => array(
			'validPercentage' => array(
				'rule' => 'validPercentage',
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
			'Gateway' => array(
						'className' => 'Gateway',
						'foreignKey' => 'gateway_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			)
	);

/**
 * beforeSave callback
 *
 * @param $options array
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (isset($this->data['Gateway1']['gw1_mid'])) {
			$this->data['Gateway1']['gw1_mid'] = trim($this->data['Gateway1']['gw1_mid']);
		}
		if (!empty($this->data['Gateway1']['gw1_rep_features'])) {
			$this->data[$this->alias]['gw1_rep_features'] = $this->removeAnyMarkUp($this->data[$this->alias]['gw1_rep_features']);
		}
		return true;
	}
}
