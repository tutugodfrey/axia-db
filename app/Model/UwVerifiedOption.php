<?php

App::uses('AppModel', 'Model');

/**
 * UwVerifiedOption Model
 *
 */
class UwVerifiedOption extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		],
		'verified_type' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		]
	];

/**
 * getUwVerifiedOptions() method
 * 
 * @return multidimentional array containing all verified options grouped by verified type
 */
	public function getUwVerifiedOptions() {
		$typesKeys = [];
		$data = [];
		$typesKeys = $this->find('list', [
			'fields' => [
				'verified_type', 'count'
			],
			'group' => 'verified_type'
		]); //DISTINCT clause wont work, using group by clause instead.

		foreach ($typesKeys as $key => $val) {
			$data[$key] = $this->find('list', [
				'conditions' => [
					'verified_type' => $key
				]
			]);
		}
		return $data;
	}
}