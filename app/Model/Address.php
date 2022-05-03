<?php

App::uses('AppModel', 'Model');
App::uses('AddressType', 'Model');

/**
 * Address Model
 *
 * @property Address $Address
 * @property Merchant $Merchant
 * @property MerchantOwner $MerchantOwner
 * @property AddressType $AddressType
 */
class Address extends AppModel {

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = [
		'ChangeRequest',
		'Search.Searchable',
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'merchant_id' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		],
		'address_type_id' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		],
		'address_zip' => [
			'validUsOrCaZip' => [
				'rule' => 'isValidZip',
				'on' => 'update',
				'message' => 'A valid zip code is requred'
			],
		]
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Merchant',
		'MerchantOwner',
		'AddressType',
	];

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['address_title'])) {
			$this->data[$this->alias]['address_title'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_title']);
		}
		if (!empty($this->data[$this->alias]['address_street'])) {
			$this->data[$this->alias]['address_street'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_street']);
		}
		if (!empty($this->data[$this->alias]['address_city'])) {
			$this->data[$this->alias]['address_city'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_city']);
		}
		if (!empty($this->data[$this->alias]['address_zip'])) {
			$this->data[$this->alias]['address_zip'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_zip']);
		}
		if (!empty($this->data[$this->alias]['address_phone'])) {
			$this->data[$this->alias]['address_phone'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_phone']);
		}
		if (!empty($this->data[$this->alias]['address_fax'])) {
			$this->data[$this->alias]['address_fax'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_fax']);
		}
		if (!empty($this->data[$this->alias]['address_phone2'])) {
			$this->data[$this->alias]['address_phone2'] = $this->removeAnyMarkUp($this->data[$this->alias]['address_phone2']);
		}
		
		return parent::beforeSave($options);
	}

/**
 * isValidZip custom validation rule
 *
 * @param array $check value to check
 * @return boolean
 */
	public function isValidZip($check) {
		//Bank and mailing zip are optional
		if ((Hash::get($this->data, 'Address.address_type_id') === AddressType::BANK_ADDRESS || Hash::get($this->data, 'Address.address_type_id') === AddressType::MAIL_ADDRESS) &&
			empty($check['address_zip'])) {
			return true;
		}
		if (empty($this->data['Address']['address_title']) && empty($this->data['Address']['address_street']) && empty($this->data['Address']['address_city']) && empty($this->data['Address']['address_state'])) {
			//skip if fields are all empty 
			return true;
		}
		$zip = $check['address_zip'];
		if (empty($zip)) {
			return "You have entered an address with a missing ZIP code. Please enter zip code.";
		}
		$isValid = (Validation::postal($zip, null, 'us') || Validation::postal($zip, null, 'ca'));
		return $isValid;
	}

/**
 * afterSave callback
 *
 * @param boolean $created Whether record was created
 * @param array $options options
 * @return void
 */
	public function afterSave($created, $options = array()) {
		//If the bank address info is updated, update the MerchantBank name
		if (Hash::get($this->data, 'Address.address_type_id') === AddressType::BANK_ADDRESS) {
			$bankId = $this->Merchant->MerchantBank->field('id', ['merchant_id' => Hash::get($this->data, 'Address.merchant_id')]);
			if ($bankId) {
				$this->Merchant->MerchantBank->save(['id' => $bankId, 'bank_name' => Hash::get($this->data, 'Address.address_title')]);
			}
		}
	}

/**
 * getBusinessAddressByMerchantId
 *
 * @param string $id Merchant id
 * @return array
 */
	public function getBusinessAddressByMerchantId($id) {
		return $this->find('first', [
			'recursive' => -1,
			'conditions' => [
				'merchant_id' => $id,
				'address_type_id' => AddressType::BUSINESS_ADDRESS,
			],
		]);
	}

/**
 * getSortedLocaleInfoByMerchantId
 *
 * Return the merchant Locale addresses sorted in a predetermined and predictable static order.
 *
 * @param string $id Merchant id
 * @return array
 */
	public function getSortedLocaleInfoByMerchantId($id) {
		$contain = [
			'Address' => ['AddressType'],
		];
		$data = $this->Merchant->find("first", [
			'contain' => $contain,
			'conditions' => ['Merchant.id' => $id]
		]);

		$sortedAddresses = [];
		$countAddress = count($data['Address']);
		for ($x = 0; $x < $countAddress; $x++) {
			if ($data['Address'][$x]['address_type_id'] === AddressType::CORP_ADDRESS) {
				$sortedAddresses[0] = $data['Address'][$x];
			}
			if ($data['Address'][$x]['address_type_id'] === AddressType::BUSINESS_ADDRESS) {
				$sortedAddresses[1] = $data['Address'][$x];
			}
			if ($data['Address'][$x]['address_type_id'] === AddressType::MAIL_ADDRESS) {
				$sortedAddresses[2] = $data['Address'][$x];
			}
		}

		$data['Address'] = $sortedAddresses;
		return $data;
	}

/**
 * getBankAddressByMerchantId
 *
 * @param string $id Merchant Id
 * @return array
 */
	public function getBankAddressByMerchantId($id) {
		return $this->find('first', [
			'recursive' => -1,
			'conditions' => [
				'merchant_id' => $id,
				'address_type_id' => AddressType::BANK_ADDRESS,
			]
		]);
	}

/**
 * getMerchantBusinessData
 *
 * @param string $id Merchant id
 * @return array
 */
	public function getMerchantBusinessData($id) {
		$contain = [
			'Address' => ['AddressType', 'MerchantOwner'],
			'MerchantReference'
		];

		return $this->Merchant->find("first", [
			'recursive' => 0,
			'contain' => $contain,
			'conditions' => ['Merchant.id' => $id],
		]);
	}

/**
 * setEditViewVars
 *
 * @param array $reqData edit view data
 * @return array
 */
	public function setEditViewVars($reqData) {
		$merchant = $this->Merchant->getSummaryMerchantData(Hash::get($reqData, 'Merchant.id'));
		$isEditLog = !empty(Hash::get($reqData, 'MerchantNote.0.loggable_log_id'));
		$userCanApproveChanges = ClassRegistry::init('MerchantChange')->userCanApproveChanges();
		return compact('merchant', 'isEditLog', 'userCanApproveChanges');
	}

/**
 * getStreetByMerchantIds
 * Function returns valid AddressType::BUSINESS_ADDRESS Address.address_street's exluding PO box
 *
 * @param array $merchantIds optional array of merchant ids will return all streets if empty
 * @return array list of Address.address_street
 */
	public function getStreetByMerchantIds($merchantIds = []) {
		$conditions = [
			"address_type_id ='" . AddressType::BUSINESS_ADDRESS . "'",
			"address_street !=''",
			'length(address_street) > 2',
			"address_street NOT ILIKE '%P O box%'",
			"address_street NOT ILIKE '%PO box%'",
			"address_street NOT ILIKE '%P.O.%'",
			"address_street NOT ILIKE '%P.O%'",
			"address_street NOT ILIKE 'POB %'",
			"address_street NOT ILIKE '%test%'",
			"address_street NOT ILIKE '%vericheck%'",
		];
		if (!empty($merchantIds)) {
			$conditions['merchant_id'] = $merchantIds;
		}

		$streets = $this->find('all', [
			'fields' => [
				'DISTINCT("Address"."address_street") AS "Address__address_street"',
			],
			'conditions' => $conditions,
			'order' => 'address_street ASC'

		]);

		$streets = Hash::combine($streets, '{n}.Address.address_street', '{n}.Address.address_street');
		return $streets;
	}
}
