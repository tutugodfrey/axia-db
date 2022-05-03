<?php

App::uses('AppModel', 'Model');

/**
 * Vendor Model
 *
 * @property Vendor $Vendor
 * @property Order $Order
 */
class Vendor extends AppModel {

/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'rank';

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'vendor_description';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'vendor_description' => [
			'notBlank' => [
				'rule' => ['notBlank']
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'Vendor already exists, you must enter an unique one!',
				'allowEmpty' => false,
			],
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			]
		]
	];

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Utils.List' => array(
			'positionColumn' => self::SORT_FIELD,
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = [
		'Order' => [
			'className' => 'Order',
			'foreignKey' => 'vendor_id',
		]
	];

/**
 * getVendorsList method
 *
 * @return array
 */
	public function getVendorsList() {
		return $this->find('list', [
			'fields' => [
				'id', 'vendor_description'
			],
			'order' => [
				'rank' => 'ASC'
			]
		]);
	}
}