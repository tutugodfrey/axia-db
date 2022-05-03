<?php

App::uses('AppModel', 'Model');

/**
 * AchProvider Model
 *
 * @property Ach $Ach
 */
class AchProvider extends AppModel {
/**
 * constant vars
 */
	const NAME_VERICHECK = 'Vericheck';
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'provider_name';

/**
 * order field
 *
 * @var order
 */
	public $order = ['AchProvider.provider_name' => 'ASC'];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'provider_name' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
	];

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = [
		'Ach' => [
			'className' => 'Ach',
			'foreignKey' => 'ach_provider_id',
			'dependent' => false,
		]
	];

/**
 * Returns a list of ach providers
 *
 * @return array
 */
	public function getList() {
		return $this->find('list', [
			'fields' => [
				"{$this->alias}.id",
				"{$this->alias}.provider_name"
			],
			'order' => ["{$this->alias}.provider_name ASC"]
		]);
	}
}
