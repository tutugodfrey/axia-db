<?php

App::uses('AppModel', 'Model');

/**
 * MerchantBank Model
 *
 * @property Merchant $Merchant
 */
class MerchantBank extends AppModel {

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
				'rule' => array('notBlank'),
			),
		),
		'bank_routing_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'bank_dda_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'fees_routing_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'fees_dda_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
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

/**
 * getBankDataByMerchantId
 *
 * @param string $id merchant id
 * @return array
 */
	public function getBankDataByMerchantId($id) {
		return $this->find('first', array('recursive' => -1,
					'conditions' => array('MerchantBank.merchant_id' => $id)));
	}

/**
 * beforeSave
 *
 * @param type $options beforeSave options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		//If save was initiated by the ChangeRequestBehavior the fields will be already encrypted
		//need to decrypt to extract last 4 digits
		$this->data['MerchantBank'] = $this->decryptFields($this->data['MerchantBank']);

		//Extract last four digits for display fields
		if (!empty($this->data['MerchantBank']['bank_routing_number'])) {
			$this->data['MerchantBank']['bank_routing_number_disp'] = substr($this->data['MerchantBank']['bank_routing_number'], -4);
		}
		if (!empty($this->data['MerchantBank']['bank_dda_number'])) {
			$this->data['MerchantBank']['bank_dda_number_disp'] = substr($this->data['MerchantBank']['bank_dda_number'], -4);
		}
		if (!empty($this->data['MerchantBank']['fees_routing_number'])) {
			$this->data['MerchantBank']['fees_routing_number_disp'] = substr($this->data['MerchantBank']['fees_routing_number'], -4);
		}
		if (!empty($this->data['MerchantBank']['fees_dda_number'])) {
			$this->data['MerchantBank']['fees_dda_number_disp'] = substr($this->data['MerchantBank']['fees_dda_number'], -4);
		}
		if (!empty($this->data[$this->alias]['bank_name'])) {
            $this->data[$this->alias]['bank_name'] = $this->removeAnyMarkUp($this->data[$this->alias]['bank_name']);
        }

		//Encrypt again
		$this->encryptData($this->data);
		return true;
	}

/**
 * encryptData
 *
 * @param type &$data save data reference with bank account fields to encrypt if they aren't already
 * @return void
 */
	public function encryptData(&$data) {
		//check if data already encrypted first
		if (!empty($data['MerchantBank']['bank_routing_number']) && !$this->isEncrypted($data['MerchantBank']['bank_routing_number'])) {
			$data['MerchantBank']['bank_routing_number'] = $this->encrypt($data['MerchantBank']['bank_routing_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['MerchantBank']['bank_dda_number']) && !$this->isEncrypted($data['MerchantBank']['bank_dda_number'])) {
			$data['MerchantBank']['bank_dda_number'] = $this->encrypt($data['MerchantBank']['bank_dda_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['MerchantBank']['fees_routing_number']) && !$this->isEncrypted($data['MerchantBank']['fees_routing_number'])) {
			$data['MerchantBank']['fees_routing_number'] = $this->encrypt($data['MerchantBank']['fees_routing_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['MerchantBank']['fees_dda_number']) && !$this->isEncrypted($data['MerchantBank']['fees_dda_number'])) {
			$data['MerchantBank']['fees_dda_number'] = $this->encrypt($data['MerchantBank']['fees_dda_number'], Configure::read('Security.OpenSSL.key'));
		}
	}

}
