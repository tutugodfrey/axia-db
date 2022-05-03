<?php

App::uses('AppModel', 'Model');

/**
 * EquipmentProgramming Model
 *
 * @property Programming $Programming
 * @property Merchant $Merchant
 * @property User $User
 * @property Gateway $Gateway
 */
class EquipmentProgramming extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'terminal_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter Term #',
				'allowEmpty' => false,
				'required' => true,
				'on' => 'create'
			),
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => true,
	            'allowEmpty' => false,
	        ),
		),
		'serial_number' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'pin_pad' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'printer' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'auto_close' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'chain' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'agent' => array(
	        'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select status',
				'allowEmpty' => false,
				'required' => true,
				'on' => 'create'
			),
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $hasMany = array(
		'EquipmentProgrammingTypeXref' => array(
			'className' => 'EquipmentProgrammingTypeXref',
			'foreignKey' => 'equipment_programming_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
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
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		//sanitize specific fields
		if (!empty($this->data[$this->alias]['hardware_serial'])) {
			$this->data[$this->alias]['hardware_serial'] = $this->removeAnyMarkUp($this->data[$this->alias]['hardware_serial']);
		}if (!empty($this->data[$this->alias]['terminal_type'])) {
			$this->data[$this->alias]['terminal_type'] = $this->removeAnyMarkUp($this->data[$this->alias]['terminal_type']);
		}if (!empty($this->data[$this->alias]['network'])) {
			$this->data[$this->alias]['network'] = $this->removeAnyMarkUp($this->data[$this->alias]['network']);
		}if (!empty($this->data[$this->alias]['provider'])) {
			$this->data[$this->alias]['provider'] = $this->removeAnyMarkUp($this->data[$this->alias]['provider']);
		}if (!empty($this->data[$this->alias]['app_type'])) {
			$this->data[$this->alias]['app_type'] = $this->removeAnyMarkUp($this->data[$this->alias]['app_type']);
		}if (!empty($this->data[$this->alias]['version'])) {
			$this->data[$this->alias]['version'] = $this->removeAnyMarkUp($this->data[$this->alias]['version']);
		}
		
		return parent::beforeSave($options);
	}

/**
 * getAllEquipmentProgrammingByMerchantId
 *
 * @param string $id Merchant id
 * @return array
 */
	public function getAllEquipmentProgrammingByMerchantId($id) {
		return $this->find('all', array('recursive' => -1,
					'order' => array('EquipmentProgramming.terminal_number' => 'asc'),
					'conditions' => array(
						'EquipmentProgramming.merchant_id' => $id,
						'EquipmentProgramming.status !=' => 'DEL'
					),
					'contain' => array('EquipmentProgrammingTypeXref')));
	}

/**
 * findEquipmentProgrammingById
 *
 * @param string $id EquipmentProgramming id
 * @return array
 */
	public function findEquipmentProgrammingById($id) {
		return $this->find('first', array('recursive' => -1,
					'conditions' => array('EquipmentProgramming.id' => $id),
					'contain' => array('EquipmentProgrammingTypeXref')));
	}

}
