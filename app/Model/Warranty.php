<?php

App::uses('AppModel', 'Model');

/**
 * Warranty Model
 *
 */
class Warranty extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'warranty' => [
			'numeric' => [
				'rule' => ['numeric']
			]
		],
		'warranty_description' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		]
	];

/**
 * getWarrantiesList
 * 
 * @return array
 */
	public function getWarrantiesList() {
		return $this->find('list', [
			'fields' => [
				'id', 'warranty_description'
			]
		]);
	}
}