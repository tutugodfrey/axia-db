<?php

App::uses('AppModel', 'Model');

/**
 * UwReceived Model
 *
 */
class UwReceived extends AppModel {

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
		]
	];

/**
 * Custom finders
 * @var array
 */
	public $findMethods = ['prioritized' => true];

/**
 * Get list of UwReceived in default ascending order of priority
 *
 * @param string $state state
 * @param bool $query query settings
 * @param mixed $results list of active products with same structure as find('list') or null 
 * @return array
 */
	protected function _findPrioritized($state, $query, $results = []) {
		if ($state === 'before') {
			if (empty($query['order'])) {
				$query['order'] = ["{$this->alias}.priority ASC"];
			}
			$query['fields'] = ['id', 'name'];
			return $query;
		} elseif ($state === 'after') {
			$results = array_combine(Hash::extract($results, "{n}.UwReceived.id"), Hash::extract($results, "{n}.UwReceived.name"));
		}
		return $results;
	}
}