<?php

App::uses('AppModel', 'Model');

/**
 * MerchantOwner Model
 *
 * @property Owner $Owner
 * @property Merchant $Merchant
 */
class MerchantOwner extends AppModel {

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
				'message' => 'Merchant\' merchant_id is not set',
			),
		),
		'owner_title' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 40),
				'required' => false,
				'allowEmpty' => true,
				'message' => 'The Owner Title is too long! Must be no larger than 40 characters long.'
			),
		),
		'owner_social_sec_no' => array(
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'owner_name' => array(
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
		'Merchant'
	);

/**
 * $hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'merchant_owner_id',
			'dependent' => true,
		),
	);

/**
 * beforeValidate callback
 *
 * @param array $options befroreValiate options
 * @return bool
 */
	public function beforeValidate($options = array()) {
		$addressValidator = $this->Address->validator();
		$rule = array('notBlank' => array(
			'rule' => 'notBlank',
			'allowEmpty' => false,
			'message' => ''
		));
		/* Set validation rules for the Owners' Addresses if any MerchantOwner data was entered */
		$anyOwnerData = Hash::get($this->data, "MerchantOwner.owner_name") .
				Hash::get($this->data, "MerchantOwner.owner_title") .
				Hash::get($this->data, "MerchantOwner.owner_equity") .
				Hash::get($this->data, "MerchantOwner.owner_social_sec_no");

		if (!empty($anyOwnerData) && array_key_exists('Address', $this->data)) {
			$rule['notBlank']['message'] = 'Please enter Address for ' . $this->data['MerchantOwner']['owner_name'];
			$addressValidator['address_street'] = $rule;
			$rule['notBlank']['message'] = 'Please enter City for ' . $this->data['MerchantOwner']['owner_name'];
			$addressValidator['address_city'] = $rule;
			$rule['notBlank']['message'] = 'Please enter State for ' . $this->data['MerchantOwner']['owner_name'];
			$addressValidator['address_state'] = $rule;
			$rule['notBlank']['message'] = 'Please enter Zip code for ' . $this->data['MerchantOwner']['owner_name'];
			$addressValidator['address_zip'] = $rule;
		} else {
			$addressValidator->remove('address_street');
			$addressValidator->remove('address_city');
			$addressValidator->remove('address_state');
			$addressValidator->remove('address_zip');
		}

		/* Make sure MerchantNote is required */
		$this->Merchant->MerchantNote->validator()->add('note', 'required', array(
			'rule' => array('notBlank'),
			'allowEmpty' => false,
			'message' => 'Please enter a note that describes your change.'
		));

		return true;
	}

/**
 * cleanRequestData method
 *
 * Remove all empty MerchantOwner data from $this->request->data to avoid inserting empty records
 *
 * @param type $data array containing all data in $this->request->data
 * @return type
 */
	public function cleanRequestData($data) {
		$reqData = Hash::extract($data, 'MerchantOwner.{n}');
		foreach ($reqData as $key => $record) {
			$anyOwnerData = Hash::get($record, "owner_name") .
					Hash::get($record, "owner_title") .
					Hash::get($record, "owner_equity") .
					Hash::get($record, "owner_social_sec_no");
			// The Merchant owner numerical index should be the same for Address
			$anyAddressData = Hash::get($data, "MerchantOwner.$key.Address.address_title") .
					Hash::get($data, "MerchantOwner.$key.Address.address_street") .
					Hash::get($data, "MerchantOwner.$key.Address.address_state") .
					Hash::get($data, "MerchantOwner.$key.Address.address_phone") .
					Hash::get($data, "MerchantOwner.$key.Address.address_phone2") .
					Hash::get($data, "MerchantOwner.$key.Address.address_zip");
			/* Remove entire index entry if no Onwer data was entered */
			if (empty($anyOwnerData)) {
				unset($data['MerchantOwner'][$key]);
			} elseif (empty($anyAddressData)) {
				//remove only the address if no address data was entered
				unset($data['MerchantOwner'][$key]['Address']);
			}
		}
		return $data;
	}

/**
 * beforeSave callback
 *
 * @param array $options beforeSave options
 * @return mixed
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data['MerchantOwner']['owner_social_sec_no'])) {
			//If save was initiated by the ChangeRequestBehavior the fields will be already encrypted
			//need to decrypt to extract last 4 digits
			$this->data['MerchantOwner'] = $this->decryptFields($this->data['MerchantOwner']);
			//Extract last four digits for display fields
			$this->data['MerchantOwner']['owner_social_sec_no_disp'] = substr($this->data['MerchantOwner']['owner_social_sec_no'], -4);
			//Encrypt full numbers
			$this->data['MerchantOwner']['owner_social_sec_no'] = $this->encryptData($this->data['MerchantOwner']['owner_social_sec_no']);
		}
		
        if (!empty($this->data[$this->alias]['owner_title'])) {
            $this->data[$this->alias]['owner_title'] = $this->removeAnyMarkUp($this->data[$this->alias]['owner_title']);
        }
		return true;
	}

/**
 * encryptData
 *
 * @param str $str string to encrypt
 * @return bool
 */
	public function encryptData($str) {
		//Encrypt if needed
		if (!$this->isEncrypted($str)) {
			return $this->encrypt($str, Configure::read('Security.OpenSSL.key'));
		} else {
			return $str;
		}
	}

/**
 * getOwnersByMerchantId
 *
 * @param string $id a merchant id
 * @return array of business owners
 */
	public function getOwnersByMerchantId($id) {
		$options = [
			'conditions' => ['MerchantOwner.merchant_id' => $id],
			'contain' => ['Address']
		];
		$merchant = $this->Merchant->find('first', [
			'conditions' => ['Merchant.id' => $id],
			'fields' => [
				'Merchant.id',
				'Merchant.merchant_ownership_type',
				'Merchant.merchant_tin',
				'Merchant.merchant_d_and_b',
			]
		]);

		$data = $this->find('all', $options);

		//Build data structure in a form compatible with the change request behavor
		//It is important that MerchantOwner indexes must match with their corresponding Address!
		$result = [];
		if (!empty($data)) {
			foreach ($data as $n => $ownDat) {
				$result["MerchantOwner"][$n] = $data[$n]["MerchantOwner"];
				$result["MerchantOwner"][$n]['Address'] = $data[$n]["Address"];
			}
		}

		return array_merge($merchant, $result);
	}

}
