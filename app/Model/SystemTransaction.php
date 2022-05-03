<?php

App::uses('AppModel', 'Model');
App::uses('TransactionType', 'Model');

/**
 * SystemTransaction Model
 *
 * @property User $User
 * @property Merchant $Merchant
 * @property MerchantNote $MerchantNote
 * @property Orderitems $Orderitems
 * @property EquipmentProgramming $EquipmentProgramming
 */
class SystemTransaction extends AppModel {

/**
 *	Limit value to show all records
 *
 * @var string
 */
	const ROW_LIMIT_ALL = 'all';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'transaction_type_id' => array(
			'exists' => array(
				'rule' => array('typeExists'),
				'message' => 'Invalid transaction type',
				'allowEmpty' => false,
			),
		),
		'user_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'system_transaction_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'message' => 'Enter a valid date in Y-m-d format',
				'allowEmpty' => false,
			),
		),
		'system_transaction_time' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User',
		'Merchant',
		'MerchantChange',
		'MerchantNote',
		'TransactionType',
		'Orderitems' => array(
			'className' => 'Orderitems',
			'foreignKey' => 'order_id',
		),
		'EquipmentProgramming' => array(
			'className' => 'EquipmentProgramming',
			'foreignKey' => 'programming_id',
		)
	);

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array('userActivity' => true);

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Search.Searchable',
		'SearchByUserId',
		'SearchByMonthYear' => [
			'fulldateFieldName' => 'system_transaction_date',
		],
	);

/**
 * Filter args config for Searchable behavior
 *
 * @var array
 */
	public $filterArgs = array(
		'user_id' => array(
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"SystemTransaction"."user_id"'
		),
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

		// ---- Virtual fields
		$descriptionSql = "CASE";
		$descriptionSql .= " WHEN {$this->alias}.transaction_type_id = '" . TransactionType::MERCHANT_NOTE . "' THEN";
		$descriptionSql .= "	(SELECT merchant_notes.note FROM merchant_notes WHERE merchant_notes.id = {$this->alias}.merchant_note_id)";
		$descriptionSql .= " WHEN {$this->alias}.transaction_type_id = '" . TransactionType::CHANGE_REQUEST . "' THEN";
		$descriptionSql .= "	(SELECT merchant_notes.note FROM merchant_changes, merchant_notes";
		$descriptionSql .= "	 WHERE merchant_changes.id = {$this->alias}.merchant_change_id";
		$descriptionSql .= "	 AND merchant_changes.merchant_note_id = merchant_notes.id)";
		$descriptionSql .= " WHEN {$this->alias}.transaction_type_id = '" . TransactionType::ACH_ENTRY . "' THEN";
		$descriptionSql .= "	(SELECT merchant_aches.reason FROM merchant_aches WHERE merchant_aches.id = {$this->alias}.merchant_ach_id)";
		$descriptionSql .= " WHEN {$this->alias}.transaction_type_id = '" . TransactionType::EQUIPMENT_ORDER . "' THEN";
		$descriptionSql .= "	(SELECT CAST(orders.invoice_number as VARCHAR(50)) FROM orders WHERE orders.id = {$this->alias}.order_id)";
		$descriptionSql .= " WHEN {$this->alias}.transaction_type_id = '" . TransactionType::PROGRAMMING_CHANGE . "' THEN";
		$descriptionSql .= "	(SELECT equipment_programmings.terminal_number FROM equipment_programmings WHERE equipment_programmings.id = {$this->alias}.programming_id)";
		$descriptionSql .= " ELSE NULL";
		$descriptionSql .= " END";
		$this->virtualFields['transaction_description'] = $descriptionSql;

		// ---- Searchable defaults
		$defaultValues = $this->getDefaultSearchValues();
		foreach ($defaultValues as $field => $value) {
			if (isset($this->filterArgs[$field])) {
				$this->filterArgs[$field]['defaultValue'] = $value;
			}
		}
	}

/**
 * Custom validation method to check if the transaction type exists
 *
 * @param array $check Field and value to check
 * @return bool
 */
	public function typeExists($check) {
		if (is_array($check)) {
			$check = reset($check);
		}
		return $this->TransactionType->exists($check);
	}

/**
 * Return the default search values
 *
 * @return array
 */
	public function getDefaultSearchValues() {
		$now = $this->_getNow();
		$rowLimits = $this->getRowLimits();

		return array(
			'from_date' => array(
				'year' => $now->format('Y'),
				'month' => $now->format('m'),
			),
			'end_date' => array(
				'year' => $now->format('Y'),
				'month' => $now->format('m'),
			),
			'row_limit' => key($rowLimits),
		);
	}

/**
 * Return the page limits values array
 *
 * @return array
 */
	public function getRowLimits() {
		return array(
			50 => 50,
			100 => 100,
			200 => 200,
			500 => 500,
			self::ROW_LIMIT_ALL => __('All'));
	}

/**
 * Custom finder query for user activity
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findUserActivity($state, $query, $results = array()) {
		if ($state === 'before') {
			$this->virtualFields['user_fullname'] = $this->User->virtualFields['fullname'];
			$query['fields'] = array(
				"{$this->alias}.id",
				"{$this->alias}.user_id",
				"{$this->alias}.user_fullname",
				"{$this->alias}.transaction_type_id",
				"{$this->alias}.system_transaction_date",
				"{$this->alias}.system_transaction_time",
				"{$this->alias}.login_date",
				"{$this->alias}.client_address",
				"{$this->alias}.merchant_id",
				"{$this->alias}.merchant_note_id",
				"{$this->alias}.merchant_change_id",
				"{$this->alias}.merchant_ach_id",
				"{$this->alias}.order_id",
				"{$this->alias}.programming_id",
				"TransactionType.transaction_type_description",
				'User.username',
				'Merchant.merchant_dba',
				'MerchantChange.merchant_note_id',
			);

			$defaultContain = array(
				'User',
				'Merchant',
				'MerchantChange',
				'TransactionType',
			);
			$query['contain'] = Hash::merge($defaultContain, Hash::get($query, 'contain'));

			$defaultConditions = array(
				'User.active' => 1,
				"{$this->alias}.transaction_type_id !=" => TransactionType::RECORD_UPDATED,
			);
			$query['conditions'] = Hash::merge($defaultConditions, (array)Hash::get($query, 'conditions'));

			if (empty($query['order'])) {
				$query['order'] = array(
					"{$this->alias}.login_date" => 'DESC',
					"User.username" => 'ASC',
					"{$this->alias}.system_transaction_date" => 'DESC',
					"{$this->alias}.system_transaction_time" => 'DESC',
				);
			}

			return $query;
		}

		return $results;
	}
}
