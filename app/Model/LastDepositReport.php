<?php

App::uses('AppModel', 'Model');
App::uses('UploadValidationException', 'Lib/Error');
App::uses('TimelineItem', 'Model');
App::uses('Entity', 'Model');

/**
 * LastDepositReport Model
 *
 * @property Merchant $Merchant
 * @property User $User
 */
class LastDepositReport extends AppModel {

/**
 * Used to detect Sage format in imported CSV file
 */
	const SAGE_KEY_CHECK = 'MerchID';
/**
 * Used to detect TSYS format in imported CSV file
 */
	const TSYS_KEY_CHECK = 'Merchant ID';

/**
 * behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'CsvImportCheck' => array(
			'delimiter' => ',',
		),
		'Search.Searchable',
		'SearchByUserId' => array(
			'userRelatedModel' => 'Merchant'
		),
		'UploadedFile'
	);

/**
 * validation
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'user_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
			)
		),
	);

/**
 * Filter args
 *
 * @var array
 */
	public $filterArgs = array(
		'user_id' => array('type' => 'subquery', 'method' => 'searchByUserId', 'field' => '"Merchant"."user_id"', 'searchByMerchantEntity' => true),
		'initials' => array('type' => 'value', 'field' => 'User.id'),
		'active' => array('type' => 'value', 'field' => 'Merchant.active'),
		'merchant' => array('type' => 'query', 'method' => 'searchByMerchant'),
		'organization_id' => [
			'type' => 'value',
			'field' => '"Merchant"."organization_id"',
		],
		'region_id' => [
			'type' => 'value',
			'field' => '"Merchant"."region_id"',
		],
		'subregion_id' => [
			'type' => 'value',
			'field' => '"Merchant"."subregion_id"',
		],
		'location_description' => [
			'type' => 'subquery',
			'method' => 'searchByLocation',
			'field' => '"Merchant"."id"'
		],
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant',
		'User',
		'InstalledTimeLineEntry' => array(
			'className' => 'TimelineEntry',
			'foreignKey' => false,
			'conditions' => array(
				'InstalledTimeLineEntry.timeline_item_id' => TimelineItem::GO_LIVE_DATE,
				'InstalledTimeLineEntry.merchant_id = LastDepositReport.merchant_id'
			)
		),
		'ApprovedUwStatus' => array(
			'className' => 'UwStatusMerchantXref',
			'foreignKey' => false,
			'conditions' => array(
				"ApprovedUwStatus.uw_status_id = (SELECT id from uw_statuses where name = 'Approved')",
				'ApprovedUwStatus.merchant_id = LastDepositReport.merchant_id'
			)
		)
	);
/**
 * Search conditions by location_description
 *
 * @param array $data
 * @return array
 */
	public function searchByLocation($data) {
		$settings = array(
			'conditions' => array(
				'Address.address_type_id' => AddressType::BUSINESS_ADDRESS,
				'Address.address_street' => $data['location_description']
			),
			'fields' => array(
				'Address.merchant_id'
			)
		);
		//Limit the result set for partners referrers and resellers
		if (!empty($conditionKey)) {
			$settings['conditions'][$conditionKey] = $userId;
		}

		$query = $this->Merchant->Address->getQuery('all', $settings);
		return $query;
	}
/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array('lastDepositReports' => true);

/**
 * Import the uploaded CSV file into LastDepositReport
 *
 * @param array $data form data, including the uploaded file
 * @param string $userId id of User who started the import. This is needed since this process is backgrounded and no user session exists f.
 * @throws UploadValidationException
 * @return bool|array true or array of errors found
 * @todo: Push this into custom csv import behaviour
 */
	public function importFromCsvUpload($data = array(), $userId) {
		$response = [
			'job_name' => 'Last Activity CSV Upload',
			'result' => true,
			'start_time' => date('Y-m-d H:i:s'), // date time format yyyy-mm-dd hh::mm:ss
			'end_time' => null, // date time format yyyy-mm-dd hh::mm:ss
			'recordsAdded' => false,
			'log' => [
				'products' => [], //single dimentional array of products selected for archive
				'errors' => [], //single dimentional indexed array errors
				'optional_msgs' => [], //single dimentional indexed array of additional optional messages
			]
		];

		$this->set($data);
		//Set $this->userId as class var so it can be used later in the process
		$this->userId = $userId;
		$importResult = null;
		try {
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
			$tmpFileName = APP . 'tmp' . DS . basename(Hash::get($data, "{$this->alias}.file.tmp_name"));
			$importResult = $this->importCSV($tmpFileName, array(), true);
			$response['result'] = true;			
		} catch (Exception $e) {
			$response['result'] = false;
			$response['log']['errors'][] = $e->getMessage();
		}

		$importErrors = $this->getImportErrors();
		$importSkippedRows = $this->getImportSkippedRows();
		$recordsAdded = is_countable($importResult)? count($importResult):0;
		$response['log']['optional_msgs'][] = __('Imported %s rows ', $recordsAdded);
		$response['log']['optional_msgs'][] = __('Skipped %s rows ', count($importSkippedRows));
		$response['recordsAdded']= (bool)$recordsAdded;

		if (!empty($importErrors)) {
			$response['log']['errors'] = array_merge([__("The file was not imported, there were validation errors.")], $this->_extractErrors($importErrors));
		}
		if (!empty($importSkippedRows)) {
			$response['log']['optional_msgs'] = array_merge($response['log']['optional_msgs'], $importSkippedRows);
		}
		if (file_exists($tmpFileName)) {
			unlink($tmpFileName);			
		}
		$response['end_time'] = date('Y-m-d H:i:s');
		return $response;
	}

/**
 * _extractErrors callback
 * Returns a single dimmentional array of errors extracted from the importErrors array which is returned by the getImportErrors() function
 *
 * @param $importErrors array
 * @return array
 */
	protected function _extractErrors($importErrors) {
		if (empty($importErrors)) {
			return [];
		}
		$errors = [];
		foreach ($importErrors as $key => $row) {
			if (!is_array($row)) {
				$errors[] = $row;
			} else {
				foreach ($row['validation'] as $field => $errors) {
					foreach($errors as $error) {
						$errors[] = Inflector::humanize($field) . ": " . $error;
					}
				}
			}
		}
		return $errors;
	}

/**
 * beforeSave callback
 *
 * @param $options array
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if ((Hash::get($this->data, 'LastDepositReport.sales_num') > 1 || Hash::get($this->data, 'LastDepositReport.monthly_volume') > 10) && !empty($this->data['LastDepositReport']['merchant_id'])) {
			$merchantMid = $this->Merchant->field('merchant_mid', ['id' => $this->data['LastDepositReport']['merchant_id']]);
			$merchEntityId = $this->Merchant->field('entity_id', ['id' => $this->data['LastDepositReport']['merchant_id']]);
			//If non acquiring merchant create install timeline entries if needed
			if ($this->Merchant->isNonAcquiringMid($merchantMid) || $merchEntityId === Entity::AX_TECH_ID) {
				$updatingEntry = false;
				$tEntries = [];
				$conditions = ['merchant_id' => $this->data['LastDepositReport']['merchant_id'], 'timeline_item_id' => TimelineItem::GO_LIVE_DATE];
				$instEntry = $this->Merchant->TimelineEntry->find('first', ['fields' => ['id', 'timeline_date_completed'], 'conditions' => $conditions]);
				$entry = [
					'merchant_id' => $this->data['LastDepositReport']['merchant_id'],
					'timeline_item_id' => TimelineItem::GO_LIVE_DATE,
					'timeline_date_completed' => date('Y-m-d')
				];
				if (!empty(Hash::get($instEntry, 'TimelineEntry.id')) && empty(Hash::get($instEntry, 'TimelineEntry.timeline_date_completed'))) {
					$entry['id'] = Hash::get($instEntry, 'TimelineEntry.id');
					$updatingEntry = true;
				}
				if ($updatingEntry || empty(Hash::get($instEntry, 'TimelineEntry.id'))) {
					$tEntries[] = $entry;
				}

				if (!empty($tEntries)){
					$this->Merchant->TimelineEntry->saveMany($tEntries);
				}
			}
		}
		return true;
	}

/**
 * beforeImport callback for each csv row
 *
 * @param array $data row
 * @return array updated row
 */
	public function beforeImport($data = array()) {
		$newData = $this->_matchImportData(Hash::get($data, $this->alias));
		$data[$this->alias] = $newData;
		return $data;
	}

/**
 * skipRowImport callback for each csv row
 *
 * @param array $data row
 * @return bool true if the row should be skipped
 */
	public function skipRowImport($data = array()) {
		//check if row is empty
		$mId = Hash::get($data, 'LastDepositReport.ID');
		$mId = ($mId)? : Hash::get($data, 'LastDepositReport.' . self::TSYS_KEY_CHECK);
		$mId = ($mId)? : Hash::get($data, 'LastDepositReport.' . self::SAGE_KEY_CHECK);
		$merchantId = $this->Merchant->field('id', ['merchant_mid' => $mId]);
		if (empty($mId)) {
			return __('Merchant ID is blank in CSV file');
		}
		if (empty($merchantId)) {
			return __('Merchant %s does not exist', $mId);
		}
		//check if merchant is disabled
		if ($this->Merchant->isCancelled($merchantId)) {
			return __('Merchant %s is cancelled', $mId);
		}
		//check if sales is non zero or empty
		if (empty(Hash::get($data, 'LastDepositReport.Sales'))) {
			return __('Merchant %s had no sales activity and was skipped', $mId);
		}

		return false;
	}

/**
 * Match imported data from the CSV file into the LastDepositReport
 *
 * @param array $data model data
 * @return array new data matched
 */
	protected function _matchImportData($data = array()) {
		if ($this->_checkSageFormat($data)) {
			return $this->_matchSageImportData($data);
		}
		if ($this->_checkTsysFormat($data)) {
			return $this->_matchTsysImportData($data);
		}

		return $this->_matchUsaEpayImportData($data);
	}

/**
 * Match Sage fields for import
 *
 * @param array $data model data
 * @return array data ready to import
 */
	protected function _matchUsaEpayImportData($data = array()) {
		//Sage format
		$oldMerchantId = str_replace("'", "", Hash::get($data, "ID"));
		$newData = $this->_getMerchantData($oldMerchantId);
		$newData['last_deposit_date'] = date('Y-m-d', strtotime('-1 day'));
		$newData['user_id'] = (isset($this->userId))? $this->userId: $this->_getCurrentUser('id');
		$newData['sales_num'] = str_replace(',', '', trim(Hash::get($data, "Sales")));
		$newData['monthly_volume'] = str_replace(',', '', trim(Hash::get($data, "Amount")));

		return $newData;
	}
/**
 * Match Sage fields for import
 *
 * @param array $data model data
 * @return array data ready to import
 */
	protected function _matchSageImportData($data = array()) {
		//Sage format
		$oldMerchantId = str_replace("'", "", Hash::get($data, "MerchID"));
		$newData = $this->_getMerchantData($oldMerchantId);
		$newData['last_deposit_date'] = Hash::get($data, "LastDepositDate");
		$newData['user_id'] = (isset($this->userId))? $this->userId: $this->_getCurrentUser('id');
		$newData['monthly_volume'] = Hash::get($data, "InitialMonthlyVolume");

		return $newData;
	}

/**
 * Match Tsys fields for import
 *
 * @param array $data model data
 * @return array data ready to import
 */
	protected function _matchTsysImportData($data = array()) {
		//TSYS format
		$oldMerchantId = Hash::get((array)$data, "Merchant ID");
		$newData = $this->_getMerchantData($oldMerchantId);
		$newData['last_deposit_date'] = Hash::get($data, "Date of Last Deposit");
		$newData['user_id'] = (isset($this->userId))? $this->userId: $this->_getCurrentUser('id');

		return $newData;
	}

/**
 * Get merchant related data
 *
 * @param type $oldMerchantId old merchant id
 * @return array
 */
	protected function _getMerchantData($oldMerchantId) {
		$newData = array();
		$merchant = $this->Merchant->find('first', array(
			'fields' => array(
				'Merchant.id',
				'Merchant.merchant_dba',
				'LastDepositReport.id'
			),
			'conditions' => array(
				'Merchant.merchant_mid' => $oldMerchantId,
			),
			'contain' => array(
				'LastDepositReport',
			)
		));

		//find merchant & LastDepositReport entry (if exists)
		if (!empty($merchant)) {
			$newData['merchant_id'] = Hash::get($merchant, 'Merchant.id');
			$newData['merchant_id_old'] = $oldMerchantId;
			$newData['merchant_dba'] = Hash::get($merchant, 'Merchant.merchant_dba');
			//set the id to update the current row
			$newData[$this->primaryKey] = Hash::get($merchant, 'LastDepositReport.id');
		}
		return $newData;
	}

/**
 * Check if the import data is Sage
 *
 * @param array $data model data
 * @return bool
 */
	protected function _checkSageFormat($data = array()) {
		return Hash::check((array)$data, self::SAGE_KEY_CHECK);
	}

/**
 * Check if the import data is TSYST
 *
 * @param array $data model data
 * @return bool
 */
	protected function _checkTsysFormat($data = array()) {
		return Hash::check((array)$data, self::TSYS_KEY_CHECK);
	}

/**
 * Custom Finder query for last deposit reports
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 * @return array Modified query OR results of query
 */
	protected function _findLastDepositReports($state, $query, $results = array()) {
		if ($state === 'before') {
			$query['contain'] = array(
				'Merchant' => array(
					'fields' => array('id', 'user_id', 'merchant_mid', 'merchant_dba'),
					'Organization.name',
					'Region.name',
					'Subregion.name',
					'AddressBus.address_street',
					'User' => array(
						'fields' => 'id, initials, user_first_name, user_last_name'
					),
				),
				'InstalledTimeLineEntry' => array(
					'fields' => array('id', 'timeline_date_completed')
				),
				'ApprovedUwStatus' => array(
					'fields' => array('datetime'),
				)
			);
			$query['fields'] = array(
				$this->alias . '.last_deposit_date',
				$this->alias . '.sales_num',
				$this->alias . '.monthly_volume',
				'Client.client_id_global'
			);
			$query['joins'] = array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.client_id = Client.id'
					)
				),
			);
			return $query;
		}
		return $results;
	}

/**
 * Search by merchant
 *
 * @param array $data request data
 * @return array
 */
	public function searchByMerchant($data = array()) {
		$filter = Hash::get($data, 'merchant');
		$conditions = array(
			'OR' => array(
				'Merchant.merchant_dba iLIKE' => '%' . $filter . '%',
				'Merchant.merchant_mid iLIKE' => '%' . $filter . '%',
			)
		);
		return $conditions;
	}
}
