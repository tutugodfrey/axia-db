<?php

App::uses('AppModel', 'Model');
App::uses('TimelineItem', 'Model');
App::uses('SalesForce', 'Model');
App::uses('ControlScan', 'Model');

/**
 * MerchantCancellation Model
 *
 * @property Merchant $Merchant
 * @property Subreason $Subreason
 */
class MerchantCancellation extends AppModel {

/**
 * Load behaviors
 */
	public $actsAs = [
		'Search.Searchable',
		'SearchByUserId' => [
			'userRelatedModel' => 'Merchant'
		],
	];

/**
 * Set filterArgs
 */
	public $filterArgs = [
		'user_id' => [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"Merchant"."user_id"',
			'searchByMerchantEntity' => true,
		],
	];

/**
 * Status values
 */
	const STATUS_COMPLETED = 'COMP';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'status' => array(
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
		'Merchant',
		'MerchantCancellationSubreason' => array(
			'className' => 'MerchantCancellationSubreason',
			'foreignKey' => 'merchant_cancellation_subreason_id',
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		if ($this->Merchant->isAcquiringAxiaMed(Hash::get($this->data, 'MerchantCancellation.merchant_id')) && 
			$this->__isNewlyCompletedCancellation()) {
			//Axia Med Acquirign merchants that cancel must also be cancelled from control scan.
			$enrolledMid = $this->Merchant->MerchantPci->field('merchant_id_old', ['merchant_id' => $this->data['MerchantCancellation']['merchant_id']]);
			$merchantMid = $this->Merchant->field('merchant_mid', ['id' => $this->data['MerchantCancellation']['merchant_id']]);
			$cancelMid = $merchantMid;
			if (!empty($enrolledMid) && $enrolledMid !== $merchantMid) {
				$cancelMid = $enrolledMid;
			}
			ClassRegistry::init('ControlScan')->cancelMerchant($cancelMid);
		}
		if (!empty($this->data[$this->alias]['reason'])) {
            $this->data[$this->alias]['reason'] = $this->removeAnyMarkUp($this->data[$this->alias]['reason']);
        }
		if (!empty($this->data[$this->alias]['merchant_cancellation_subreason'])) {
            $this->data[$this->alias]['merchant_cancellation_subreason'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_cancellation_subreason']);
        }
	}
/**
 * afterSave callback
 *
 * @param $created boolean
 * @param $options array
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if (!empty($this->data['MerchantCancellation']['date_completed'])) {
			$externalId = $this->Merchant->ExternalRecordField->field('value', ['merchant_id' => $this->data['MerchantCancellation']['merchant_id'], 'field_name' => SalesForce::ACCOUNT_ID]);
			if (!empty($externalId)) {
				ClassRegistry::init('SalesForce')->updateSalesforceField(SalesForce::CANCEL_DATE, $this->data['MerchantCancellation']['date_completed'], $externalId, false);
			}
		}
	}

/**
 * __isNewlyCompletedCancellation
 * This method checks whether a merchant cancellation record changed from not being canceled to cancelled.
 * Requires the original unchanged record to be available so this must be called from a beforeSave callback,
 * which also allows this method to access new data being saved in $this->data
 *
 * @return boolean [description]
 */
	private function __isNewlyCompletedCancellation() {
		$isNewAndComplete = false;
		$id = Hash::get($this->data, 'MerchantCancellation.id');
		$status = Hash::get($this->data, 'MerchantCancellation.status');
		$compDate = Hash::get($this->data, 'MerchantCancellation.date_completed');
		if ( empty($id) && ($status === self::STATUS_COMPLETED || !empty($compDate)) ) {
			$isNewAndComplete = true;
		} elseif (!empty($id)) {
			//check existing record's previous state
			//false hasAny previously cancelled records means it's not a newly and competed cancellation
			$previouslyCancelled = $this->hasAny([
				'id' => $id,
				'OR' => ['status' => self::STATUS_COMPLETED, 'date_completed IS NOT NULL']]
			);

			//Compare previous record state with this data (new state)
			$isNewAndComplete = ($previouslyCancelled === false && ($status === self::STATUS_COMPLETED || !empty($compDate)));
		}
		return $isNewAndComplete;
	}

/**
 * getCancellation method
 *
 * @param string $id Merchant id
 * @return array
 */
	public function getCancellation($id) {
		$contain = array(
			'CancellationsHistory' => array(
				'MerchantCancellationSubreason'
			),
			'User',
			'MerchantCancellation' => array('MerchantCancellationSubreason'),
			'MerchantUwVolume'
		);
		$data = $this->Merchant->find('first', array('contain' => $contain, 'conditions' => array("Merchant.id" => $id)));
		return $data;
	}

/**
 * getCurrUserComplexId method
 *
 * @return array
 */
	public function getCurrUserComplexId() {
		return $this->Merchant->User->buildComplexId(User::PREFIX_USER, $this->_getCurrentUser('id'));
	}

/**
 * setPaginatorSettings method
 *
 * @param array $params Filter params
 * @return array
 */
	public function setPaginatorSettings($params) {
		$contain = array(
			'MerchantCancellationSubreason',
			'Merchant' => array(
				'fields' => array('Merchant.merchant_mid', 'Merchant.merchant_dba'),
				'MerchantUwVolume' => array('fields' => array('MerchantUwVolume.mo_volume')),
				'TimelineEntry' => array('conditions' => array('TimelineEntry.timeline_item_id' => TimelineItem::APPROVED))
			)
		);
		$settings = array(
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.client_id = Client.id'
					)
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => array(
						'Merchant.user_id = User.id'
					)
				),
				array(
					'table' => 'users',
					'alias' => 'Partner',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.partner_id = Partner.id'
					)
				),
				array(
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.entity_id = Entity.id'
					)
				),
			),
			'limit' => 500,
			'contain' => $contain,
			'fields' => array(
				'MerchantCancellation.*',
				'MerchantCancellationSubreason.*',
				'Client.client_id_global',
				'User.user_first_name',
				'User.user_last_name',
				'((' . $this->Merchant->User->getFullNameVirtualField('Partner') . ')) as "Partner__fullname"',
				'Entity.entity_name'
			),
			'conditions' => array(
				'MerchantCancellation.status' => GUIbuilderComponent::STATUS_COMPLETED,
			)
		);
		if (!empty($params['fromDate'])) {
			$settings['conditions']['MerchantCancellation.date_submitted >='] = $params['fromDate'];
		}
		if (!empty($params['toDate'])) {
			$settings['conditions']['MerchantCancellation.date_submitted <='] = $params['toDate'];
		}
		$complexUserIdArgs = $this->Merchant->User->extractComplexId(Hash::get($params, 'user_id'));
		if (Hash::get($complexUserIdArgs, 'prefix') === User::PREFIX_ENTITY) {
			$this->filterArgs['user_id']['field'] = '"Merchant"."id"';
		}
		$settings['conditions'] = Hash::merge($settings['conditions'], $this->parseCriteria($params));
		return $settings;
	}

/**
 * calculateAttritionData
 *
 * @param array $requestQuery query filter paramenters
 * @return array
 */
	public function calculateAttritionData($requestQuery) {
		/* Get a list of all merchants that have subimitted a cancellation and should not be excluded from attrition ratio. */
		$cancelledMerchants = $this->find('list',	array(
			'conditions' => array(
				'MerchantCancellation.date_submitted IS NOT NULL',
				'MerchantCancellation.exclude_from_attrition' => false
			),
			'fields' => array('MerchantCancellation.merchant_id')
		));

		/* Now get a list of active merchants that belong to selected rep/entity excluding merchants that have submitted a cancellation ($cancelledMerchants)
		 * Notice: Since a merchant could still be active = 1 and at the same time have cancellation submited,
		 * we need to ensure those are excluded from this query
		 */
		$activeConditions = array_merge(
						array(
							'NOT' => array('Merchant.id' => $cancelledMerchants),
							'Merchant.active =' => 1,
						), $this->parseCriteria($requestQuery)
					);
		$settings = array(
					'joins' => array(
						array('table' => 'users',
							'alias' => 'User',
							'type' => 'INNER',
							'conditions' => array(
								'Merchant.user_id = User.id'
							)
						),
					),
					'conditions' => $activeConditions,
					'fields' => array('Merchant.id'));

		$nonCancelledActiveMerchants = $this->Merchant->find('list', $settings);

		//Finally get a list of all merchants that have been 'APP'roved since 1999 but include only merchants found in $nonCancelledActiveMerchants
		$activeMerchants = $this->Merchant->TimelineEntry->find('count', array('recursive' => -1, 'conditions' => array('TimelineEntry.timeline_item_id' => TimelineItem::APPROVED,
							'TimelineEntry.timeline_date_completed >' => '1999-01-01',
							'TimelineEntry.merchant_id' => $nonCancelledActiveMerchants)));

		//Set a default of 12 months to show in the past if no ammountOfMonths is selected
		$amountOfmonths = ($requestQuery['ammountOfMonths'] === '') ? 12 : $requestQuery['ammountOfMonths'];

		$dateRange = $this->getDateRangeByMonthYear($requestQuery['month']['month'], $requestQuery['year']['year'], $amountOfmonths);

		$params = array(
				'fromDate' => $dateRange['fromDate'],
				'toDate' => $dateRange['toDate'],
				'amountOfmonths' => $amountOfmonths,
				'activeMerchants' => $activeMerchants,
				'user_id' => Hash::get($requestQuery, 'user_id'));

		return $params;
	}

/**
 * builds an array containing a range of dates where the end month end year are passed as parameters
 *
 * @param string $month ending month
 * @param string $year ending year
 * @param int $amountOfmonths the number of months in the past (before $month,$year) where the date range should start
 * @return array containing the date range
 */
	public function getDateRangeByMonthYear($month, $year, $amountOfmonths) {
		//Create a DateTime object
		$date = date_create_from_format('Y-m-d', $year . '-' . $month . '-' . $this->_getCurrentDay()); //using current day but day is not important here
		$toDate = $date->format('Y-m-t');
		//substract $amountOfmonths to create a start date for the range
		if ($amountOfmonths > 0) {
			date_modify($date, '-' . $amountOfmonths . ' month');
		}
		$fromDate = $date->format('Y-m-01');

		return array('fromDate' => $fromDate, 'toDate' => $toDate);
	}

/**
 * Return current day.
 * Moved logic here to be able to mock it on unit tests
 *
 * @return string
 */
	protected function _getCurrentDay() {
		return date('d');
	}

/**
 * makeCsvString
 *
 * @param array $searchSettings find options
 * @param array $queryParams filter data
 * @return string
 */
	public function makeCsvString($searchSettings, $queryParams) {
		//We want all data, remove limit
		unset($searchSettings['limit']);
		$this->virtualFields['user_fullname'] = $this->Merchant->User->virtualFields['fullname'];
		$data = $this->find('all', $searchSettings);
		$status = GUIbuilderComponent::getStatusList();
		//Invoke CakeNumber formatter class
		$Number = new CakeNumber;
		$reportType = (array_key_exists("userAttritionRate", $queryParams))?"Attrition Ratio Report":"Cancellation Report";
		$csvStr = "\n,$reportType | " . $queryParams['amountOfmonths'] . " months from " . date("F Y", strtotime($queryParams['fromDate'])) . " to " . date("F Y", strtotime($queryParams['toDate'])) . "\n";
		$csvStr .= "MID,DBA,Client ID,Rep,Volume,Axia Inv,Date Approved,Date Submitted,Date Completed,Date Inactive,Fee Charged,Reason,Details,Status\n";

		if (!empty($data)) {
			foreach ($data as $val) {
				$csvStr .= '="' . $val['Merchant']['merchant_mid'] . '"';
				$csvStr .= ',"' . trim($val['Merchant']['merchant_dba']) . '"';
				$csvStr .= ',' . $this->csvPrepString($val['Client']['client_id_global']);
				$csvStr .= ',' . $this->csvPrepString($val['MerchantCancellation']['user_fullname']);
				$csvStr .= (!empty($val['Merchant']['MerchantUwVolume']['mo_volume'])) ? ',' . $Number->currency($val['Merchant']['MerchantUwVolume']['mo_volume'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-')) : ',';
				$csvStr .= (!empty($val['MerchantCancellation']['axia_invoice_number'])) ? ',="' . $val['MerchantCancellation']['axia_invoice_number'] . '"' : ',';
				$csvStr .= (!empty($val['Merchant']['TimelineEntry'])) ? ',' . date("m/d/Y", strtotime($val['Merchant']['TimelineEntry'][0]['timeline_date_completed'])) : ',';
				$csvStr .= (!empty($val['MerchantCancellation']['date_submitted'])) ? ',' . date("m/d/Y", strtotime($val['MerchantCancellation']['date_submitted'])) : ',';
				$csvStr .= (!empty($val['MerchantCancellation']['date_completed'])) ? ',' . date("m/d/Y", strtotime($val['MerchantCancellation']['date_completed'])) : ',';
				$csvStr .= (!empty($val['MerchantCancellation']['date_inactive'])) ? ',' . date("m/d/Y", strtotime($val['MerchantCancellation']['date_inactive'])) : ',';
				$csvStr .= (!empty($val['MerchantCancellation']['fee_charged'])) ? ',' . $Number->currency($val['MerchantCancellation']['fee_charged'], 'USD', array('after' => false, 'negative' => '-', 'thousands' => '', 'places' => 2)) : ',';
				$csvStr .= (!empty($val['MerchantCancellation']['reason'])) ? ',' . $this->csvPrepString($val['MerchantCancellation']['reason']) : ',';
				$csvStr .= (!empty($val['MerchantCancellationSubreason']['name'])) ? ',' . $this->csvPrepString($val['MerchantCancellationSubreason']['name'] . " " . $val['MerchantCancellation']['merchant_cancellation_subreason']) : ',' . $this->csvPrepString($val['MerchantCancellation']['merchant_cancellation_subreason']);
				$csvStr .= ',' . $status[$val['MerchantCancellation']['status']];
				$csvStr .= ',' . Hash::get($val, 'Entity.entity_name');
				$csvStr .= ',' . Hash::get($val, 'Partner.fullname');
				$csvStr .= "\n";
			}
		}

		if (array_key_exists("userAttritionRate", $queryParams)) {
			$cancelledMerchants = count($data) - 1;
			$csvStr .= "\nNumber of Open Merchants:," . $queryParams['activeMerchants'];
			$csvStr .= "\nNumber of Canceled Merchants:," . $cancelledMerchants;
			$csvStr .= "\nAttrition Rate:," . $Number->toPercentage(bcdiv($cancelledMerchants, $queryParams['activeMerchants']), 2, array('multiply' => true));
			$csvStr .= "\nPlus/Minus Allowance:," . $Number->toPercentage($queryParams['userAttritionRate'], 2);

			/*
			 Number of Open Merchants: $queryParams['activeMerchants']
			 Number of Canceled Merchants: $queryParams['cancelledMerchants']
			 Attrition Rate: $this->Number->toPercentage($cancelledMerchants / $activeMerchants, 2, array('multiply' => true))
			 Plus/Minus Allowance: $this->Number->toPercentage($userAttritionRate, 2)
			 */

		}

		return $csvStr;
	}
}
