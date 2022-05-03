<?php

App::uses('AppModel', 'Model');
App::uses('MerchantRejectStatus', 'Model');
App::uses('Hash', 'Utility');

/**
 * MerchantReject Model
 *
 * @property Merchant $Merchant
 * @property MerchantRejectLine $CurrentMerchantRejectLine
 * @property MerchantReject $MerchantReject
 */
class MerchantReject extends AppModel {

/**
 * Reject Type const ID
 *
 * @var uuid
 */
	const REJECT_TYPE_REJECT = '8b48f7ef-6115-41c0-9bbe-bcd34f786506';

/**
 * Reject recurrence type
 *
 * @var uuid
 */
	const REJECT_RECURRENCE_FIRST = '4aa0cdb2-afd4-42f2-beae-df9d39fc786b';

/**
 * Length to fix the merchant mid prepending a '7' or a '1'
 */
	const FIX_MERCHANT_MID_LENGTH = 15;

/**
 * Message when a row is skipped
 */
	const SKIPPED_MESSAGE = "Row skipped";

/**
 * Bank-specific CSV column contains bank name which can change in the future
 */
	const BANK_COLUMN = "Merrick DDA";

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Merchant does not exists'
			)
		),
		'trace' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Trace field should be numeric'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Trace number is already taken, it must be unique'
			),
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'reject_date' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => 'Invalid date format for reject date'
			),
		),
		'merchant_reject_type_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'This field should not be empty'
			)
		),
		'code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'This field should not be empty'
			),
			'input_has_only_valid_chars' => array(
	            'rule' => array('inputHasOnlyValidChars'),
	            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
	            'required' => false,
	            'allowEmpty' => true,
	        ),
		),
		'amount' => array(
			'notBlank' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Invalid amount! Enter only numbers with or without decimals.'
			),
		),
		'merchant_reject_recurrance_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			),
		),
		'open' => array(
			'boolean' => array(
				'rule' => array('boolean')
			),
		),
		'loss_axia' => array(
			'notBlank' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'required' => false,
				'message' => 'Invalid amount! Enter only numbers with or without decimals.'
			),
		),
		'loss_mgr1' => array(
			'notBlank' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'required' => false,
				'message' => 'Invalid amount! Enter only numbers with or without decimals.'
			),
		),
		'loss_mgr2' => array(
			'notBlank' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'required' => false,
				'message' => 'Invalid amount! Enter only numbers with or without decimals.'
			),
		),
		'loss_rep' => array(
			'notBlank' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'required' => false,
				'message' => 'Invalid amount! Enter only numbers with or without decimals.'
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant',
		'MerchantRejectType',
		'MerchantRejectRecurrance'
	);

/**
 * HasMany Associations
 *
 * @var array
 */
	public $hasMany = array(
		'MerchantRejectLine' => array(
			'dependent' => true,
			'order' => array(
				'MerchantRejectLine.status_date' => 'asc',
			)
		)
	);

/**
 * HasOne Associations
 *
 * @var array
 */
	public $hasOne = array(
		'FirstMerchantRejectLine' => array(
			'className' => 'MerchantRejectLine',
			'conditions' => array(
				'FirstMerchantRejectLine.id = (SELECT id FROM merchant_reject_lines
					WHERE merchant_reject_lines.merchant_reject_id = MerchantReject.id
					ORDER BY merchant_reject_lines.status_date ASC NULLS FIRST
					LIMIT 1)'
			)
		),
		'CurrentMerchantRejectLine' => array(
			'className' => 'MerchantRejectLine',
			'conditions' => array(
				'CurrentMerchantRejectLine.id = (SELECT id FROM merchant_reject_lines
					WHERE merchant_reject_lines.merchant_reject_id = MerchantReject.id
					ORDER BY merchant_reject_lines.status_date DESC NULLS LAST
					LIMIT 1)'
			)
		)
	);

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array('merchantRejects' => true);

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'CsvImportCheck' => array(
			'delimiter' => ','
		),
		'Search.Searchable',
		'SearchByUserId' => array(
			'userRelatedModel' => 'Merchant'
		),
		'SearchByMonthYear' => [
			'fulldateFieldName' => 'reject_date',
		],
	);

/**
 * Filter args config for Searchable behavior
 *
 * @var array
 */
	public $filterArgs = array(
		'merchant_reject_type_id' => array('type' => 'value'),
		'merchant_dba' => array('type' => 'like', 'field' => 'Merchant.merchant_dba'),
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
		'open' => array('type' => 'value'),
		'user_id' => array('type' => 'subquery', 'method' => 'searchByUserId', 'field' => '"Merchant"."user_id"', 'searchByMerchantEntity' => true),
		'merchant_reject_status_id' => array(
			'type' => 'query',
			'method' => 'searchByStatus'
		)
	);

/**
 *
 * @var array
 */
	public $csvFieldsMap = array(
		'merchant_id' => 'Individual ID',
		'reject_date' => 'Date',
		'amount' => 'Amount',
		'trace' => 'Trace Number',
		'code' => 'Ret Code'
	);

/**
 * constructor, populate default values for search
 *
 * @param type $id id
 * @param type $table table name
 * @param type $ds datasource
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$now = $this->_getNow();
		$this->filterArgs['from_date']['defaultValue'] = array(
			'year' => $now->format('Y'),
			'month' => $now->format('m'),
		);
		$this->filterArgs['end_date']['defaultValue'] = array(
			'year' => $now->format('Y'),
			'month' => $now->format('m'),
		);
	}

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 */
	public function afterFind($results, $primary = false) {
		// Calculate the submitted amounts of the reject lines
		// The first reject line dont have submitted amount
		foreach ($results as &$result) {
			$amount = Hash::get($result, 'MerchantReject.amount');
			if (isset($result['CurrentMerchantRejectLine'])) {
				$result['CurrentMerchantRejectLine']['submitted_amount'] = $this->MerchantRejectLine->getSubmittedAmount($amount, $result['CurrentMerchantRejectLine']);
			}
			if (isset($result['MerchantRejectLine'])) {
				foreach ($result['MerchantRejectLine'] as &$rejectLine) {
					$rejectLine['submitted_amount'] = $this->MerchantRejectLine->getSubmittedAmount($amount, $rejectLine);
				}
			}
		}
		return $results;
	}

/**
 * Get a list of codes
 *
 * ```
 * array(
 *     'C01' => 'C01',
 *     'C02' => 'C02',
 *     ... etc
 * ```
 *
 * @return array|null
 */
	public function listCodes() {
		$codes = $this->find('all', array(
			'fields' => "DISTINCT {$this->alias}.code",
			'order' => "{$this->alias}.code",
			'recursive' => -1
		));
		$codeValues = Hash::extract($codes, "{n}.{$this->alias}.code");
		$codeList = array_combine($codeValues, $codeValues);

		return $codeList;
	}

/**
 * try to fix the merchantId based on the merchant_id if present in $data
 *
 * @param array $data model data
 * @return array model data
 */
	public function fixMerchant($data) {
		$merchantId = Hash::get($data, "{$this->alias}.merchant_id");
		$calculatedMerchantId = $this->getMerchantId($merchantId);
		$data[$this->alias]['merchant_id'] = $calculatedMerchantId;

		return $data;
	}

/**
 * Before import callback that triggers for each record in csv
 *
 * @param array $data current excel record to be saved to db
 * @return array Final data to be imported
 */
	public function beforeImport($data) {
		$modelData = $this->mapCsvDataFields($data);
		$modelData = $this->cleanCsvData($modelData);
		$modelData = $this->fixMerchant($modelData);
		// Add fields
		$modelData[$this->alias]['merchant_reject_recurrance_id'] = self::REJECT_RECURRENCE_FIRST;
		$modelData[$this->alias]['merchant_reject_type_id'] = self::REJECT_TYPE_REJECT;
		$modelData['MerchantRejectLine'][0] = ClassRegistry::init('MerchantRejectLine')->defaultMerchantRejectLine();

		return $modelData;
	}

/**
 * Map csv data fields
 *
 * @param array $data array of data passed to be imported
 * @return array
 */
	public function mapCsvDataFields($data) {
		$modelData = array();
		foreach ($this->csvFieldsMap as $field => $column) {
			$modelData[$field] = Hash::get($data, $this->alias . '.' . $column);
			$modelData[$field] = trim($modelData[$field]);
		}
		$modelData = array(
			$this->alias => $modelData
		);

		return $modelData;
	}

/**
 * Clean csv data
 *
 * @param array $data array of data passed to be imported
 * @return array Data
 */
	public function cleanCsvData($data) {
		// fix date format
		$data[$this->alias]['reject_date'] = date('Y-m-d', strtotime(Hash::get($data, "{$this->alias}.reject_date")));

		// Remove $ from amount
		$amount = Hash::get($data, "{$this->alias}.amount");
		$amount = str_replace(array('$', ','), '', $amount);
		if (strstr($amount, '(')) {
			$amount = str_replace(array('(', ')'), '', $amount);
			$amount = $amount * -1;
		}
		$data[$this->alias]['amount'] = $amount;

		// Fix merchant_id
		// If count(mid) === 15 prepend 7 IFF the first 3 digists are 641
		// Else if the first digists are 239 prepend 1 and search else skip
		$mid = Hash::get($data, "{$this->alias}.merchant_id");
		if (strlen($mid) === self::FIX_MERCHANT_MID_LENGTH) {
			if (substr($mid, 0, 3) === substr(Merchant::AX_PAY_MID_PREFIX, 1, 3)) {
				$data[$this->alias]['merchant_id'] = '7' . $mid;
			} elseif (substr($mid, 0, 3) === substr(Merchant::AX_TECH_MID_PREFIX, 1, 3)) {
				$data[$this->alias]['merchant_id'] = '1' . $mid;
			}
		}
		return $data;
	}

/**
 * Custom Finder query for merchant rejects
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 * @return array Modified query OR results of query
 */
	protected function _findMerchantRejects($state, $query, $results = array()) {
		if ($state === 'before') {
			if (Hash::get($query, 'ignoreRejectDate')) {
				unset($query['conditions']['MerchantReject.reject_date >=']);
				unset($query['conditions']['MerchantReject.reject_date <=']);
			}

			$query['recursive'] = -1;
			$query['contain'] = $this->contain(array(
				'rejectLineOrder' => Hash::get($query, 'rejectLineOrder')
			));

			$query['fields'] = array(
				'id', 'trace', 'reject_date', 'merchant_reject_type_id', 'merchant_id', 'code', 'amount', 'open', 'loss_axia', 'loss_mgr1', 'loss_mgr2', 'loss_rep', 'merchant_reject_recurrance_id'
			);
			if (Hash::get($query, 'operation') === 'count') {
				$query['contain'] = array(
					// Table used at "searchByUserId" subquery with Searchable behavior
					'Merchant',
					'CurrentMerchantRejectLine'
				);
			}

			return $query;
		}

		return $results;
	}

/**
 * Default contain for MerchantReject
 *
 * @param array $options Array of options to modify the contain
 *
 * @return array
 */
	public function contain($options = array()) {
		$rejectLineOrder = Hash::get($options, 'rejectLineOrder');
		if (empty($rejectLineOrder)) {
			$rejectLineOrder = array('MerchantRejectLine.status_date DESC NULLS LAST');
		}

		return array(
			'MerchantRejectType' => array(
				'fields' => array('id', 'name')
			),
			'MerchantRejectLine' => array(
				'fields' => array('id', 'fee', 'status_date', 'notes'),
				'order' => $rejectLineOrder,
				'MerchantRejectStatus' => array(
					'fields' => array('id', 'name')
				)
			),
			'FirstMerchantRejectLine' => array(
				'order' => array(
					'FirstMerchantRejectLine.status_date' => 'ASC NULLS FIRST'
				),
				'MerchantRejectStatus' => array(
					'fields' => array('id', 'name')
				)
			),
			'CurrentMerchantRejectLine' => array(
				'order' => array(
					'CurrentMerchantRejectLine.status_date' => 'DESC NULLS LAST'
				),
				'MerchantRejectStatus' => array(
					'fields' => array('id', 'name')
				)
			),
			'Merchant' => array(
				'fields' => array('id', 'user_id', 'merchant_mid', 'merchant_dba'),
				'User' => array(
					'fields' => array('id', 'initials', 'user_first_name', 'user_last_name')
				)
			),
			'MerchantRejectRecurrance' => array(
				'fields' => array('id', 'name')
			)
		);
	}

/**
 * Contain for MerchantReject row
 *
 * @return array
 */
	public function rowContain() {
		$contain = $this->contain();
		// Remove the RejectLines from the default contain, it is not need for the row
		unset($contain['MerchantRejectLine']);
		return $contain;
	}

/**
 * Search by status
 *
 * @param array $data merchant data
 * @throws InvalidArgumentException
 * @return array
 */
	public function searchByStatus($data = array()) {
		if (!isset($data['merchant_reject_status_id'])) {
			throw new InvalidArgumentException(__('Merchant reject status is required'));
		}

		$filter = $data['merchant_reject_status_id'];

		if ($filter === strval(MerchantRejectStatus::STATUS_NOT_COLLECTED_GENERAL)) {
			$filter = array(
				MerchantRejectStatus::STATUS_ON_RESERVE,
				MerchantRejectStatus::STATUS_RE_REJECTED,
				MerchantRejectStatus::STATUS_NOT_COLLECTED,
				MerchantRejectStatus::STATUS_WRITTEN_OFF
			);
		} elseif ($filter === strval(MerchantRejectStatus::STATUS_COLLECTED_GENERAL)) {
			$filter = array(
				MerchantRejectStatus::STATUS_RE_SUBMITTED_CONFIRMED,
				MerchantRejectStatus::STATUS_COLLECTED_RESERVE,
				MerchantRejectStatus::STATUS_COLLECTED_OTHER,
			);
		}

		$condition = array(
			'CurrentMerchantRejectLine.merchant_reject_status_id' => $filter
		);

		return $condition;
	}

/**
 * Get merchant id based on current import data
 *
 * @param string $merchantMid merchant id
 * @return string merchantId
 */
	public function getMerchantId($merchantMid) {
		$merchant = $this->Merchant->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'OR' => [
					'Merchant.merchant_mid' => $merchantMid,
					'Merchant.merchant_id_old' => $merchantMid,
				]
			)
		));

		return Hash::get($merchant, 'Merchant.id');
	}

/**
 * Import the uploaded CSV file into Merchant Reject
 *
 * @param array $data form data, including the uploaded file
 * @throws UploadValidationException
 * @return bool|array true or array of errors found
 * @todo: Push this into custom behavior
 */
	public function importFromCsvUpload($data = array()) {
		$this->set($data);
		$this->Behaviors->load('FileStorage.UploadValidator', array(
			'localFile' => true,
			'validate' => true,
			'allowedExtensions' => array('csv')
		));
		if (!$this->Behaviors->UploadValidator->beforeValidate($this, array())) {
			$message = implode(',', $this->validationErrors['file']);
			throw new UploadValidationException($message);
		}
		//we don't want to check it again on every save...
		$this->Behaviors->unload('UploadValidator');
		$tmpFileName = Hash::get($data, "{$this->alias}.file.tmp_name");
		return $this->importCSV($tmpFileName, array(), true);
	}

/**
 * Get items to set
 *
 * @return array
 */
	public function getItemsToSet() {
		$merchantRejectRecurrances = $this->MerchantRejectRecurrance->find('list');
		$merchantRejectStatuses = $this->MerchantRejectLine->MerchantRejectStatus->listStatusesWithCollectedOpt();
		$merchantRejectTypes = $this->MerchantRejectType->find('list');
		$merchantRejectCodes = $this->listCodes();
		return compact(
			'merchantRejectStatuses',
			'merchantRejectRecurrances',
			'merchantRejectTypes',
			'merchantRejectCodes'
		);
	}

/**
 * Get the default values to create a new Marchant Reject with his first Reject Line
 *
 * @return array
 */
	public function getDefaultValues() {
		return array(
			$this->alias => array(
				'merchant_reject_type_id' => self::REJECT_TYPE_REJECT,
				'merchant_reject_recurrance_id' => self::REJECT_RECURRENCE_FIRST,
				'open' => 1
			),
			'MerchantRejectLine' => array(
				'merchant_reject_status_id' => MerchantRejectStatus::STATUS_RECEIVED
			)
		);
	}

/**
 * skipRowImport method callback called in behavior to skip invalid line from csv file
 *
 * @param array $rowData data of line from csv file
 * @return string|bool
 */
	public function skipRowImport($rowData) {
		if (!isset($rowData['MerchantReject']['Individual ID']) || !isset($rowData['MerchantReject'][self::BANK_COLUMN])) {
			return self::SKIPPED_MESSAGE . ": Required column 'Idividual ID' or '" . self::BANK_COLUMN . "' was missing in the CSV";
		}

		if (empty(Hash::filter($rowData))) {
			return self::SKIPPED_MESSAGE . ": Row was completely empty";
		}

		if (empty($rowData['MerchantReject']['Individual ID'])) {
			return self::SKIPPED_MESSAGE . ": The Idividual ID was blank";
		}

		return false;
	}
}
