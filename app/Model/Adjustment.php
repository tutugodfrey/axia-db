<?php

App::uses('AppModel', 'Model');

/**
 * Adjustment Model
 *
 * @property User $User
 */
class Adjustment extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'adj_amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User'
	);

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array(
		'index' => true,
		'adjustments' => true
	);

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Search.Searchable',
		'SearchByUserId',
		'SearchByMonthYear' => [
			'fulldateFieldName' => 'adj_date',
		],
	);

/**
 * Filter args config for Searchable behavior
 *
 * @var array
 */
	public $filterArgs = array(
		'from_date' => array(
			'type' => 'query',
			'method' => 'dateStartConditions',
			'empty' => true,
		),
		'end_date' => array(
			'type' => 'query',
			'method' => 'dateEndConditions',
			'empty' => true,
		),
		'user_id' => array(
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"Adjustment"."user_id"'
		),
	);

/**
 * Constructor, populate default values for search
 *
 * @param type $id id
 * @param type $table table name
 * @param type $ds datasource
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$defaultValues = $this->getDefaultSearchValues();
		foreach ($defaultValues as $field => $value) {
			$this->filterArgs = Hash::insert($this->filterArgs, "{$field}.defaultValue", $value);
		}
	}

/**
 * Return the default search values
 *
 * @return array
 */
	public function getDefaultSearchValues() {
		$now = $this->_getNow();
		return array(
			'from_date' => array(
				'year' => $now->format('Y'),
				'month' => $now->format('m'),
			),
			'end_date' => array(
				'year' => $now->format('Y'),
				'month' => $now->format('m'),
			),
			'user_id' => $this->User->buildComplexId(User::PREFIX_USER, $this->_getCurrentUser('id')),
		);
	}

/**
 * Custom finder query for commission reports
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findAdjustments($state, $query, $results = array()) {
		if ($state === 'before') {
			$query['fields'] = array(
				"{$this->alias}.adj_amount",
				"{$this->alias}.adj_description",
			);
			$query['order'] = array(
				"{$this->alias}.adj_date" => 'ASC'
			);

			return $query;
		}

		return $results;
	}

/**
 * _findIndex
 *
 * @param string $state operational state
 * @param array $query query
 * @param array $results result set
 *
 * @return array $results
 */
	protected function _findIndex($state, $query, $results = array()) {
		if ($state === 'before') {
			$query['fields'] = array(
				'Adjustment.id',
				'Adjustment.user_id',
				'Adjustment.adj_date',
				'Adjustment.adj_description',
				'Adjustment.adj_amount'
			);
			$query['recursive'] = -1;

			return $query;
		}
		return $results;
	}

}
