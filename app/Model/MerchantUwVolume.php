<?php

App::uses('AppModel', 'Model');

/**
 * MerchantUwVolume Model
 *
 */
class MerchantUwVolume extends AppModel {

	const DISCOUNT_FREQ_D = "daily";
	const DISCOUNT_FREQ_M = "monthly";

/**
 * Behaviours
 *
 * @var array
 */
	public $actsAs = array('Containable', 'ChangeRequest');
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'visa_volume' => array(
			'conditionallyRequired' => array(
				'rule' => array('isVolNeeded', 'Visa'),
			)
		),
		'mc_volume' => array(
			'conditionallyRequired' => array(
				'rule' => array('isVolNeeded', 'MasterCard'),
			)
		),
		'amex_volume' => array(
			'conditionallyRequired' => array(
				'rule' => array('isVolNeeded', 'American Express'),
			)
		),
		'ds_volume' => array(
			'conditionallyRequired' => array(
				'rule' => array('isVolNeeded', 'Discover'),
			)
		),
		'pin_debit_volume' => array(
			'conditionallyRequired' => array(
				'rule' => array('isVolNeeded', 'Debit'),
			)
		),
		'te_amex_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'te_diners_club_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'te_discover_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'te_jcb_number' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
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
		'MerchantUw' => array(
			'className' => 'MerchantUw',
			'foreignKey' => 'merchant_uw_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * isVolNeeded
 * Custom validation rule checks if volume input is required.
 * Will return error string when merchant has the product but its corresponding volume field data is blank
 * Otherwise true
 *
 * @param array $productGroup the name of the product group that we are checking
 * @param string $productGroup the name of the product group that we are checking
 * @return boolean | string 
 */
	public function isVolNeeded($check, $productGrup) {
		$volVal = reset($check);
		if (strlen($volVal) > 0 && is_numeric($volVal)) {
			return true;
		}
		$ProductsAndService = ClassRegistry::init('ProductsAndService');
		$merchantId = $this->data['MerchantUwVolume']['merchant_id'];
		$hasProduct = $ProductsAndService->hasAny([
			'merchant_id' => $merchantId,
			"products_services_type_id in (select id from products_services_types where products_services_description like '%$productGrup%')"
		]);

		if ($hasProduct) {
			return "Merchant has $productGrup! Field is required!";
		} else {
			return true;
		}
	}
/**
 * getDataByMerchantId
 *
 * @param string $mId merchant_id
 * @return array | null
 */
	public function getDataByMerchantId($mId) {
		return $this->find('first', ['conditions' => ['merchant_id' => $mId]]);
	}
/**
 * Before save callback
 *
 * @param array $options options
 * @return bool
 */
	public function beforeSave($options = array()) {
		//If save was initiated by the ChangeRequestBehavior the fields will be already encrypted
		//need to decrypt to extract last 4 digits
		$this->data['MerchantUwVolume'] = $this->decryptFields($this->data['MerchantUwVolume']);

		if (array_key_exists('te_amex_number', $this->data['MerchantUwVolume'])) {
			$this->data['MerchantUwVolume']['te_amex_number_disp'] = substr(Hash::get($this->data, 'MerchantUwVolume.te_amex_number'), -4);
		}
		if (array_key_exists('te_diners_club_number', $this->data['MerchantUwVolume'])) {
			$this->data['MerchantUwVolume']['te_diners_club_number_disp'] = substr(Hash::get($this->data, 'MerchantUwVolume.te_diners_club_number'), -4);
		}
		if (array_key_exists('te_discover_number', $this->data['MerchantUwVolume'])) {
			$this->data['MerchantUwVolume']['te_discover_number_disp'] = substr(Hash::get($this->data, 'MerchantUwVolume.te_discover_number'), -4);
		}
		if (array_key_exists('te_jcb_number', $this->data['MerchantUwVolume'])) {
			$this->data['MerchantUwVolume']['te_jcb_number_disp'] = substr(Hash::get($this->data, 'MerchantUwVolume.te_jcb_number'), -4);
		}

		$this->encryptData($this->data);
		return true;
	}

/**
 * encryptData
 *
 * @param type &$data save data reference with fields to encrypt if they aren't already
 * @return void
 */
	public function encryptData(&$data) {
		//Encrypt t&e data iff needed
		if (!empty($data['MerchantUwVolume']['te_amex_number']) && !$this->isEncrypted($data['MerchantUwVolume']['te_amex_number'])) {
			$data['MerchantUwVolume']['te_amex_number'] = $this->encrypt($data['MerchantUwVolume']['te_amex_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['MerchantUwVolume']['te_diners_club_number']) && !$this->isEncrypted($data['MerchantUwVolume']['te_diners_club_number'])) {
			$data['MerchantUwVolume']['te_diners_club_number'] = $this->encrypt($data['MerchantUwVolume']['te_diners_club_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['MerchantUwVolume']['te_discover_number']) && !$this->isEncrypted($data['MerchantUwVolume']['te_discover_number'])) {
			$data['MerchantUwVolume']['te_discover_number'] = $this->encrypt($data['MerchantUwVolume']['te_discover_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['MerchantUwVolume']['te_jcb_number']) && !$this->isEncrypted($data['MerchantUwVolume']['te_jcb_number'])) {
			$data['MerchantUwVolume']['te_jcb_number'] = $this->encrypt($data['MerchantUwVolume']['te_jcb_number'], Configure::read('Security.OpenSSL.key'));
		}
	}

}
