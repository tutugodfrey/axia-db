<?php

App::uses('AppModel', 'Model');

/**
 * AddressType Model
 *
 */
class AddressType extends AppModel {

	const BUSINESS_ADDRESS = '795f442e-04ab-43d1-a7f8-f9ba685b90ac';
	const BANK_ADDRESS = "63494506-9aed-4d7c-b83c-898e6254ff20";
	const CORP_ADDRESS = '1c7c0709-86df-4643-8f7f-78bd28259008';
	const MAIL_ADDRESS = 'edf125da-8592-42e3-bd26-ff0614bd27ba';
	const OWNER_ADDRESS = '31e277ff-423a-4af8-9042-8310d7c320df';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'address_type' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		]
	];

/**
 * Returns all address type ids
 *
 * @return array
 */
	public function getAllAddressTypeIds() {
		return [
			'business_address' => self::BUSINESS_ADDRESS,
			'bank_address' => self::BANK_ADDRESS,
			'corp_address' => self::CORP_ADDRESS,
			'mail_address' => self::MAIL_ADDRESS,
			'owner_address' => self::OWNER_ADDRESS
		];
	}
}
