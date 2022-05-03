<?php

App::uses('AppModel', 'Model');
App::uses('AxiaCalculate', 'Model');
App::uses('RolePermissions', 'Lib');
App::uses('AddressType', 'Model');
App::uses('GUIbuilderComponent', 'Controller/Component');
App::uses('MerchantAchReason', 'Model');
App::uses('MerchantAchBillingOption', 'Model');
App::uses('UserCostsArchive', 'Model');

/**
 * CommissionReport Model
 */
class CommissionReport extends AppModel {

	const STATUS_INACTIVE = "I";
	const STATUS_ACTIVE = "A";
	const USER_COST_ARCHIVE_FIELD_MAP_NAME = "UserCostArchiveFieldMap";

/**
 * Commission reports sections
 *
 * @var string
 */
	const SECTION_INCOME = 'income';
	const SECTION_ADJUSTMENTS = 'repAdjustments';
	const SECTION_NET_INCOME = 'netIncome';
	const SECTION_COMMISION_REPORTS = 'commissionReports';

/**
 * CSV column header names
 *
 * @var array
 */
	public $csvHeaders = [
		'mid' => 'MID',
		'dba' => 'DBA',
		'client_id_global' => 'Client ID',
		'axia_invoice' => 'Axia Inv',
		'rep' => 'Rep',
		'description' => 'Description',
		'retail' => 'Retail',
		'rep_cost' => 'Rep Cost',
		'partner_cost' => 'Partner Cost',
		'shipping_type' => 'Shipping Type',
		'shipping_cost' => 'Shipping Cost',
		'expedited' => 'Expedited',
		'rep_profit' => 'Rep Profit',
		'partner_profit' => 'Partner Profit',
		'axia_profit' => 'Axia Profit',
		'tax_amount' => 'Tax',
		'state' => 'State',
		'ent_name' => 'Company',
		'partner_name' => 'Partner'
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Order',
		'Merchant',
		'ShippingType',
		'User',
		'Partner' => [
			'className' => 'User',
			'foreignKey' => 'partner_id',
		],
		'Referer' => [
			'className' => 'User',
			'foreignKey' => 'referer_id',
		],
		'Reseller' => [
			'className' => 'User',
			'foreignKey' => 'reseller_id',
		],
	];

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = [
		'index' => true,
		'commissionReport' => true,
	];

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = [
		'Search.Searchable',
		'SearchByUserId' => [],
		'SearchByMonthYear' => [
			'yearFieldName' => 'c_year',
			'monthFieldName' => 'c_month',
		],
	];

/**
 * Filter args config for Searchable behavior
 *
 * @var array
 */
	public $filterArgs = [
		'merchant_dba' => [
			'type' => 'ilike',
			'field' => 'Merchant.merchant_dba'
		],
		'from_date' => [
			'type' => 'query',
			'method' => 'dateStartConditions',
			'empty' => true,
		],
		'end_date' => [
			'type' => 'query',
			'method' => 'dateEndConditions',
			'empty' => true,
		],
		'user_id' => [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"CommissionReport"."user_id"',
			'searchByMerchantEntity' => true,
		],
		'partners' => [
			'type' => 'value',
			'field' => 'CommissionReport.partner_id'
		],
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
	];

/**
 * Populate default values for search and set virtual fields
 *
 * @param type $id id
 * @param type $table table name
 * @param type $ds datasource
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->actsAs['SearchByUserId'] = [
			'loggedInUserId' => $this->_getCurrentUser('id')
		];
		// ---- Virtual fields
		$cbusinessCalculated = "CASE WHEN {$this->alias}.split_commissions = true THEN";
		$cbusinessCalculated .= "  CASE WHEN {$this->alias}.c_business IS NULL THEN 0";
		$cbusinessCalculated .= "  ELSE c_business";
		$cbusinessCalculated .= "  END  + ({$this->alias}.c_expecting / 2)";
		$cbusinessCalculated .= " ELSE {$this->alias}.c_business";
		$cbusinessCalculated .= " END";
		$this->virtualFields['c_business_calculated'] = $cbusinessCalculated;

		$cExpectingCalculated = "CASE WHEN {$this->alias}.split_commissions = true THEN {$this->alias}.c_expecting / 2";
		$cExpectingCalculated .= " ELSE {$this->alias}.c_expecting";
		$cExpectingCalculated .= " END";
		$this->virtualFields['c_expecting_calculated'] = $cExpectingCalculated;

		// ---- Searchable defaults
		$defaultValues = $this->getDefaultSearchValues();
		foreach ($defaultValues as $field => $value) {
			$this->filterArgs = Hash::insert($this->filterArgs, "{$field}.defaultValue", $value);
		}
	}

/**
 * Search conditions by location_description
 *
 * @param array $data
 * @return array
 */
	public function searchByLocation($data) {
		$query = $this->Merchant->searchByLocation($data);
		return $query;
	}

/**
 * searchByUserAndManager 
 * Builds search conditions for a search in which the user performing the search is a manager and the user being searched for is any
 * This method is dynamically set as the method element in $this->filterArgs[method] member variable when current user is a manager
 *
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function searchByUserAndManager($data = array()) {
		$complexUserId = $this->User->extractComplexId(Hash::get($data, 'user_id'));
		$searchId = $complexUserId['id'];
		$curUserId = $this->_getCurrentUser('id');
		$role = $this->User->getUserRolesList($searchId);

		if (!empty($role[User::ROLE_PARTNER]) || !empty($role[User::ROLE_REFERRER]) || !empty($role[User::ROLE_RESELLER])) {
			$cond = array(
				//If searching for a partner get the records where that partner is and also users that are associated with the current manager
				"{$this->alias}.user_id IN (select distinct user_id from associated_users where associated_user_id = '$curUserId' AND (permission_level = '" . User::ROLE_SM . "' OR permission_level = '" . User::ROLE_SM2 . "'))",
				'OR' => array(
					"{$this->alias}.partner_id = (select distinct associated_user_id from associated_users where user_id = '$curUserId' AND associated_user_id = '$searchId' AND permission_level = '" . User::ROLE_PARTNER . "')",
					"{$this->alias}.referer_id = (select distinct associated_user_id from associated_users where user_id = '$curUserId' AND associated_user_id = '$searchId' AND permission_level = '" . User::ROLE_REFERRER . "')",
					"{$this->alias}.reseller_id = (select distinct associated_user_id from associated_users where user_id = '$curUserId' AND associated_user_id = '$searchId' AND permission_level = '" . User::ROLE_RESELLER . "')",
				)
			);
		} else {
			$cond = array(
				"{$this->alias}.user_id = (select distinct user_id from associated_users where user_id = '$searchId' AND associated_user_id = '$curUserId' AND (permission_level = '" . User::ROLE_SM . "' OR permission_level = '" . User::ROLE_SM2 . "'))"
			);
		}

		return $cond;
	}

/**
 * Return the default search form values
 *
 * @return array
 */
	public function getDefaultSearchValues() {
		$now = $this->_getNow();
		return [
			'from_date' => [
				'year' => $now->format('Y'),
				'month' => $now->format('m')
			],
			'end_date' => [
				'year' => $now->format('Y'),
				'month' => $now->format('m')
			],
			'user_id' => $this->User->getDefaultUserSearch(),
		];
	}

/**
 * Return the default Associated Models contain
 *
 * @return array
 */
	public function getDefaultContain() {
		return [
			'User',
			'Order',
			'Merchant',
			'Partner',
			'ShippingType'
		];
	}

/**
 * build
 *
 * @param int $year report year
 * @param int $month report month
 * @param string $userEmail user email
 *
 * @return array $response
 * @throws Exception
 */
	public function build($year, $month, $userEmail) {
		$response = [
			'job_name' => 'Commission Report Admin',
			'result' => true,
			'start_time' => date('Y-m-d H:i:s'), // date time format yyyy-mm-dd hh::mm:ss
			'end_time' => null, // date time format yyyy-mm-dd hh::mm:ss
			'recordsAdded' => false,
			'log' => [
				'products' => [], //single dimentional array of products selected for archive
				'errors' => [], //single dimentional indexed array errors
				'optional_msgs' => ["Selected month/year: $month/$year" ], //single dimentional indexed array of additional optional messages
			]
		];

		$this->AxiaCalculate = new AxiaCalculate();
		$this->MerchantPricingArchive = ClassRegistry::init('MerchantPricingArchive');
		//Instantiate w/object notiation to set member variables
		$this->UserCostsArchive = new UserCostsArchive(false, null, null, true);
		$this->UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$this->BetTable = ClassRegistry::init('BetTable');
		$this->CommissionPricing = ClassRegistry::init('CommissionPricing');
		$this->MerchantAchReason = ClassRegistry::init('MerchantAchReason');

		$cfgAchReason = $this->MerchantAchReason->find('list', ['fields' => ['id', 'reason']]);

		// orders for time period
		$orders = $this->__getOrders($month, $year);

		// ach for time period
		$invoices = $this->__getInvoices($month, $year);

		$dataSource = $this->getDataSource();
		try {
			$dataSource->begin();

			// we should now have two separate arrays to put into commission_report table
			// 1. orders
			foreach ($orders as $line) {
				try {
					if ($this->__compileAndPersistData($line, $year, $month, $cfgAchReason, false)) {
						$response['recordsAdded'] = true;
					}
				} catch (Exception $exception) {
					$response['recordsAdded'] = false;
					throw $exception;
				}
			}

			// 2. axia invoices
			foreach ($invoices as $invoice) {
				try {
					if ($this->__compileAndPersistData($invoice, $year, $month, $cfgAchReason, true)) {
						$response['recordsAdded'] = true;
					}
				} catch (Exception $exception) {
					$response['recordsAdded'] = false;
					throw $exception;
				}
			}

			// archive data to commission_pricings table
			if (!$this->__archiveCommissionPricings($year, $month, $userEmail)) {
				$response['recordsAdded'] = false;
				throw new Exception("failed to save commission pricings data");
			} else {
				$response['recordsAdded'] = true;
			}

			$dataSource->commit();

		} catch (Exception $e) {
			$dataSource->rollback();
			$response['result'] = false;
			$response['log']['errors'][] = $e->getMessage();
			$response['end_time'] = date('Y-m-d H:i:s');
			return $response;
		}
		$response['end_time'] = date('Y-m-d H:i:s');
		return $response;
	}

/**
 * __numberOrZero
 *
 * @param int $nbr value to check if numeric
 *
 * @return int $nbr or zero
 */
	private function __numberOrZero($nbr) {
		if (is_numeric($nbr)) {
			return $nbr;
		} else {
			return 0;
		}
	}

/**
 * Add commission report records for an order with and without matching axia invoice
 *
 * @param array $data order data
 * @param int $year order year
 * @param int $month order month
 * @param array $cfgAchReason ach reasons list
 * @param bool $isInvoice true if the data param is MerchantAch specific data false it it's Order data
 * @return bool true if the record is added, false if it is not needed
 * @throws Exception when the record fail to save
 */
	private function __compileAndPersistData($data, $year, $month, $cfgAchReason, $isInvoice) {
		// ignore if invoice item is not commissionable
		if (Hash::get($data, 'commissionable_item') === false) {
			return false;
		}
		$shippingTypeId = Hash::get($data, 'shipping_type_id');
		$orderId = (!$isInvoice)? $data['id'] : null;
		$merchantAchId = ($isInvoice)? $data['id'] : null;
		$merchantId = Hash::get($data, 'merchant_id');
		if (!empty($merchantId)) {
			$merchant = $this->Merchant->find('first', [
				'fields' => [
					'Merchant.user_id',
					'Merchant.partner_id',
					'Merchant.referer_id',
					'Merchant.reseller_id',
					'MerchantUw.expedited'
				],
				'conditions' => ['Merchant.id' => $merchantId],
				'contain' => ['MerchantUw'],
			]);
			$userId = $merchant['Merchant']['user_id'];
			$partnerId = $merchant['Merchant']['partner_id'];
			$refererId = $merchant['Merchant']['referer_id'];
			$resellerId = $merchant['Merchant']['reseller_id'];
			$expedited = $merchant['MerchantUw']['expedited'];
		} else {
			// ignore data with no merchant
			return false;
		}
		$fees = $this->User->UserCompensationProfile->getProfitLossFees($userId, $partnerId);
		$partnerProfitLossFees = $this->User->UserCompensationProfile->getProfitLossFees($partnerId);
		$userCompensationProfileId = Hash::get($fees, 'UserCompensationProfile.id');
		$appStatus = [];
		$partnerAppStatus = [];
		$appCost = 0;
		$expediteCost = 0;
		$appTruePrice = 0;
		$appRepPrice = 0;
		$partnerAppCost = 0;
		$partnerExpediteCost = 0;
		$isAppFee = false;
		if ($isInvoice) {
			$appStatus = $this->_getAppStatus($userCompensationProfileId, $data['merchant_ach_app_status_id']);
			$appCost = Hash::get($appStatus, 'AppStatus.rep_cost', 0);
			$appTruePrice = Hash::get($data, 'app_true_price', 0);
			$appRepPrice = Hash::get($data, 'app_rep_price', 0);
			if (!empty($partnerId)) {
				$partnerCompProfileId = $this->User->UserCompensationProfile->field('id', ['user_id' => $partnerId]);
				if (!empty($partnerCompProfileId)) {
					$partnerAppStatus = $this->_getAppStatus($partnerCompProfileId, $data['merchant_ach_app_status_id']);
				}
			}
			$partnerAppCost = Hash::get($partnerAppStatus, 'AppStatus.rep_cost', 0);
			//Set expedite cost only for when Application fee is the reason for the invoice
			$isAppFee = (Hash::get($data, 'merchant_ach_reason_id') === MerchantAchReason::APP_FEE);
			if ($isAppFee && $expedited == true) {
				$expediteCost = Hash::get($appStatus, 'AppStatus.rep_expedite_cost', 0);
				$partnerExpediteCost = Hash::get($partnerAppStatus, 'AppStatus.rep_expedite_cost', 0);
			}
		}
		$cDesc = $this->_getDescription($isInvoice, $data, $year, $month, $cfgAchReason);
		$cTax = $this->__numberOrZero(Hash::get($data, 'tax')); //tax field has the same name in both invoices and orders
		$axiainv = Hash::get($data, 'invoice_number');
		$cRetail = ($isInvoice)? $data['ach_amount'] : 0;
		$cRepCost = $this->_getRepCost($isInvoice, $this->__numberOrZero(Hash::get($data, 'rep_cost')), $appCost, $expedited, $expediteCost);
		$partnerCost = !empty($partnerId)? $this->_getRepCost($isInvoice, $this->__numberOrZero(Hash::get($data, 'partner_cost')), $partnerAppCost, $expedited, $partnerExpediteCost) : 0;
		if ($isInvoice) {
			//For invoices any shipping amounts are added to the invoice amount/retail and should not be saved separately
			$cRetail += (Hash::get($data, 'exact_shipping_amount'))?: Hash::get($data, 'general_shipping_amount');
		}
		$cShipping = $this->__numberOrZero(Hash::get($data, 'shipping_cost')); //this shipping costs comes from orders only
		$cExpecting = $this->_calculateUserProfit($isInvoice, $isAppFee, $cRetail, $cRepCost, $fees, $cShipping, $expediteCost, $appCost);
		$cSplitCommissions = $this->_getUserSplitCommissions($userId);

		$partnerProfit = !empty($partnerId)? $this->_calculateUserProfit($isInvoice, $isAppFee, $cRetail, $partnerCost, $partnerProfitLossFees, $cShipping, $partnerExpediteCost, $partnerAppCost) : 0;
		$cBusiness = $this->_getAxiaProfit(
				$cRetail,
				$cRepCost,
				$partnerCost,
				$cShipping,
				$this->__numberOrZero(Hash::get($data, 'true_cost')),
				$expediteCost,
				$appCost,
				$appTruePrice,
				$appRepPrice,
				$cExpecting,
				$partnerProfit
			);

		if ($cRetail != 0 || $cRepCost != 0 || $cShipping != 0 || $cBusiness != 0) {
			$record = [];
			$record['status'] = self::STATUS_INACTIVE;
			$record['merchant_id'] = $merchantId;
			$record['c_year'] = ($year == '') ? null : $year;
			$record['c_month'] = ($month == '') ? null : $month;
			$record['order_id'] = ($orderId == '') ? null : $orderId;
			$record['axia_invoice_number'] = ($axiainv == '') ? null : $axiainv;
			$record['merchant_ach_id'] = ($merchantAchId == '') ? null : $merchantAchId;
			$record['user_id'] = ($userId == '') ? null : $userId;
			$record['c_retail'] = ($cRetail == '') ? null : $cRetail;
			$record['c_rep_cost'] = ($cRepCost == '') ? null : $cRepCost;
			$record['c_shipping'] = ($cShipping == '') ? null : $cShipping;
			$record['c_expecting'] = ($cExpecting == '') ? null : $cExpecting;
			$record['c_business'] = ($cBusiness == '') ? null : $cBusiness;
			$record['description'] = ($cDesc == '') ? null : addslashes($cDesc);
			$record['split_commissions'] = ($cSplitCommissions == '') ? "f" : $cSplitCommissions;
			$record['referer_id'] = $refererId;
			$record['reseller_id'] = $resellerId;
			$record['partner_id'] = $partnerId;
			$record['partner_cost'] = ($partnerCost == '') ? null : $partnerCost;
			$record['shipping_type_id'] = ($shippingTypeId == '') ? '' : $shippingTypeId;
			$record['partner_profit'] = ($partnerProfit == '') ? null : $partnerProfit;
			$record['tax_amount'] = ($cTax == '') ? null : $cTax;

			if (!$this->__addCommissionReportRecord($record)) {
				throw new Exception("failed to save commission report data");
			}

			return true;
		}

		return false;
	}

/**
 * Get orders for time period
 *
 * @param int $month commission_month
 * @param int $year commission_year
 * @return array
 */
	private function __getOrders($month, $year) {
		$results = $this->Order->find('all', [
			'fields' => [
				'Order.id',
				'Order.user_id',
				'Order.vendor_id',
				'Order.shipping_cost',
				'sum("OrderItem"."quantity" * "OrderItem"."equipment_item_true_price") as true_cost',
				'sum("OrderItem"."quantity" * "OrderItem"."equipment_item_rep_price") as rep_cost',
				'sum("OrderItem"."quantity" * "OrderItem"."equipment_item_partner_price") as partner_cost',
				'OrderItem.merchant_id',
				'sum("OrderItem"."quantity" * "OrderItem"."item_tax") as tax',
				'sum("OrderItem"."item_ship_cost") as shipping_cost',
				'Order.shipping_type_id',
				'OrderItem.equipment_item_id',
				'OrderItem.equipment_item_description'
			],
			'joins' => [
				[
					'alias' => 'OrderItem',
					'table' => 'orderitems',
					'type' => 'LEFT',
					'conditions' => 'Order.id = OrderItem.order_id',
				]
			],
			'conditions' => [
				'Order.status !=' => GUIbuilderComponent::STATUS_DELETED,
				'OrderItem.commission_month' => $month,
				'OrderItem.commission_year' => $year
			],
			'group' => [
				'Order.id',
				'Order.user_id',
				'Order.vendor_id',
				'Order.shipping_cost',
				'OrderItem.merchant_id',
				'OrderItem.equipment_item_id',
				'OrderItem.equipment_item_description'
			],
			'order' => [
				'Order.id'
			]
		]);

		$formatedData = [];
		foreach ($results as &$order) {
			$order['Order']['orderlevel_shipping'] = $order['Order']['shipping_cost'];
			$order['Order']['true_cost'] = $order['0']['true_cost'];
			$order['Order']['rep_cost'] = $order['0']['rep_cost'];
			$order['Order']['partner_cost'] = $order['0']['partner_cost'];
			$order['Order']['tax'] = $order['0']['tax'];
			$order['Order']['shipping_cost'] = $order['0']['shipping_cost'];
			$order['Order']['merchant_id'] = $order['OrderItem']['merchant_id'];
			$order['Order']['equipment_item_id'] = $order['OrderItem']['equipment_item_id'];
			$order['Order']['equipment_item_description'] = $order['OrderItem']['equipment_item_description'];
			$formatedData[] = $order['Order'];
		}

		return $formatedData;
	}

/**
 * Ach for time period
 *
 * @param int $month MerchantAch.commission_month
 * @param int $year MerchantAch.commission_year
 * @return array
 */
	private function __getInvoices($month, $year) {
		$results = ClassRegistry::init('MerchantAch')->find('all', [
			'fields' => [
				'MerchantAch.id',
				'MerchantAch.merchant_id',
				'MerchantAch.invoice_number',
				'MerchantAch.merchant_ach_app_status_id',
				'MerchantAch.exact_shipping_amount',
				'MerchantAch.general_shipping_amount',
				'MerchantAchBillingOption.billing_option_description',
				'MerchantAchAppStatus.app_true_price',
				'MerchantAchAppStatus.app_rep_price',
			],
			'contain' => [
				'InvoiceItem' => [
					'fields' => [
						'InvoiceItem.merchant_ach_id',
						'InvoiceItem.merchant_ach_reason_id',
						'InvoiceItem.reason_other',
						'InvoiceItem.commissionable',
						'InvoiceItem.amount',
						'InvoiceItem.tax_amount',
					],
					'conditions' => ['InvoiceItem.commissionable' => true]
				],
				'MerchantAchBillingOption',
				'MerchantAchAppStatus',
			],
			'conditions' => [
				'MerchantAch.status' => GUIbuilderComponent::STATUS_COMPLETED,
				'MerchantAch.rejected' => false,
				'OR' => [
					[
						'AND' => [
							['MerchantAch.commission_month IS NULL'],
							['MerchantAch.commission_year IS NULL'],
							['MerchantAch.ach_date >=' => $year . "-" . $month . "-01"],
							['MerchantAch.ach_date <=' => $year . "-" . $month . "-" . date("t", mktime(0, 0, 0, $month, 1, $year))],
						]
					],
					[
						'AND' => [
							['MerchantAch.commission_month' => $month],
							['MerchantAch.commission_year' => $year]
						]
					]
				]
			]
		]);

		$data = [];
		foreach ($results as $ach) {
			if (!empty($ach['InvoiceItem'])) {
				foreach($ach['InvoiceItem'] as $idx => $item) {
					if ($idx === 0) {
						//This invoice data should be included only once per invoice
						//Therefore combine the invoice data with the first item's data
						$invData = [
							'merchant_ach_app_status_id' => $ach['MerchantAch']['merchant_ach_app_status_id'],
							'exact_shipping_amount' => $ach['MerchantAch']['exact_shipping_amount'],
							'general_shipping_amount' => $ach['MerchantAch']['general_shipping_amount'],
							'app_true_price' => $ach['MerchantAchAppStatus']['app_true_price'],
							'app_rep_price' => $ach['MerchantAchAppStatus']['app_rep_price'],
						];
					} else {
						$invData = [
							'merchant_ach_app_status_id' => null,
							'exact_shipping_amount' => null,
							'general_shipping_amount' => null,
							'app_true_price' => null,
							'app_rep_price' => null,
						];
					}
					$invData['id'] = $ach['MerchantAch']['id'];
					$invData['merchant_id'] = $ach['MerchantAch']['merchant_id'];
					$invData['invoice_number'] = $ach['MerchantAch']['invoice_number'];
					$invData['merchant_ach_reason_id'] = $item['merchant_ach_reason_id'];
					$invData['reason_other'] = $item['reason_other'];
					$invData['commissionable_item'] = $item['commissionable'];
					$invData['ach_amount'] = $item['amount'];
					$invData['tax'] = $item['tax_amount'];
					$data[] = $invData;
				}
			}			
		}
		return $data;
	}

/**
 * Get the invoice app status
 *
 * @param string $userCompensationProfileid user compensation profile
 * @param string $merchantAchAppStatusId merchant ach app status
 * @return int|decimal
 */
	protected function _getAppStatus($userCompensationProfileid, $merchantAchAppStatusId) {
		if (empty($userCompensationProfileid) || empty($merchantAchAppStatusId)) {
			return [];
		}
		$appStatus = ClassRegistry::init('AppStatus')->find('first', [
			'conditions' => [
				'user_compensation_profile_id' => $userCompensationProfileid,
				'merchant_ach_app_status_id' => $merchantAchAppStatusId
			]
		]);

		return $appStatus ?: [];
	}

/**
 * (orderitems.equipment_item_description) equipment fees
 * (merchant_aches.reason or merchant_aches.reason_other) app fees
 *
 * @param bool $hasInvoice true if the order has an invoice
 * @param array $data order/invoice information
 *	Required data:
 *  - `id`
 *  - `merchant_id`
 *  - `merchant_ach_id`
 *  - `reason`
 *  - `reason_other`
 * @param int $year year
 * @param int $month month
 * @param array $cfgAchReason reason list
 * @return string
 */
	protected function _getDescription($hasInvoice, $data, $year, $month, $cfgAchReason) {
		$description = '';
		$merchantId = Hash::get($data, 'merchant_id');

		if ($hasInvoice) {
			if ($data['merchant_ach_reason_id'] == MerchantAchReason::REASON_OTHER && !empty($data['reason_other'])) {
				$description = $data['reason_other'];
			} else {
				if (!empty($cfgAchReason[$data['merchant_ach_reason_id']])) {
					$description = $cfgAchReason[$data['merchant_ach_reason_id']];
				}
			}
		} else {
			if (!empty($data['equipment_item_description'])) {
				$itemDescription = $data['equipment_item_description'];
			} else {
				$itemDescription = $this->Order->Orderitem->field('equipment_item_description', [
					'order_id' => $data['id'],
					'merchant_id' => $merchantId,
					'commission_month' => $month,
					'commission_year' => $year,
					'equipment_item_id' => $data['equipment_item_id']
				]);
			}
			$description = $itemDescription ?: 'Equipment Order';
		}

		return $description;
	}

/**
 * - Equipment Cost (sum(orderitems.quantity * orderitems.equipment_item_rep_price) as rep_cost)
 * - App Cost (app_statuses.rep_cost or app_statuses.rep_expedite_cost) expedite determined by merchant_uws.expedited for the given merchant
 *
 * @param bool $hasInvoice true if the order has an invoice
 * @param decimal $repCost equipment cost
 * @param decimal $appCost app status rep cost
 * @param bool $expedited expedited
 * @param decimal $expetiteCost app status expedite cost
 * @return int|decimal
 */
	protected function _getRepCost($hasInvoice, $repCost, $appCost, $expedited, $expetiteCost) {
		$repCost = $this->__numberOrZero($repCost);
		if ($hasInvoice) {
			if ($expedited) {
				$repCost = $this->__numberOrZero($appCost) + $this->__numberOrZero($expetiteCost);
			} else {
				$repCost = $this->__numberOrZero($appCost);
			}
		}

		return $repCost;
	}

/**
 *   o Equipment Fees = Retail - (Rep Cost - Shipping Cost * % of Profit or Loss on Equipment Fees from Rep User Profile)
 *   o App Fees = (App Fee - App Cost - Expedite Cost * % of Profit or Loss on Application Fees from Rep User Profile)
 *
 * - Formula for a merchant with a partner:
 *   o Equipment Fees = (Retail - Partner Rep Cost - Shipping Cost) * % of Profit or Loss on Equipment Fees from Partner Rep Profile
 *   o App Fees = (App Fee - Partner Rep App Cost - Partner Rep Expedite Cost * % of Profit or Loss on Application Fees from Partner Rep Profile)
 *
 * @param bool $hasInvoice true if the order has an invoice
 * @param bool $isAppFee true if the invoice reason is Application Fee
 * @param decimal $retailCost retail cost
 * @param decimal $repCost rep cost
 * @param array $fees array with user compensation CommissionFee model data
 * @param decimal $shippingCost shipping cost
 * @param decimal $appExpediteCost expedited cost
 * @param null|array $appRepCost the AppSatus.rep_cost model
 * @return int|decimal
 */
	protected function _calculateUserProfit($hasInvoice, $isAppFee, $retailCost, $repCost, $fees, $shippingCost, $appExpediteCost, $appRepCost) {
		$repProfit = 0;
		$percentOfProfitOrLoss = 0;
		if (!empty($fees)) {
			if ($retailCost > 0) {
				$percentOfProfitOrLoss = ($isAppFee)? $fees['CommissionFee']['app_fee_profit'] : $fees['CommissionFee']['non_app_fee_profit'];
			} else {
				$percentOfProfitOrLoss = ($isAppFee)? $fees['CommissionFee']['app_fee_loss'] : $fees['CommissionFee']['non_app_fee_loss'];
			}
		}

		if ($hasInvoice) {
			$repProfit = ($retailCost - $appRepCost - $appExpediteCost) * ($percentOfProfitOrLoss / 100);
		} else {
			$repProfit = ($retailCost - $repCost - $shippingCost) * ($percentOfProfitOrLoss / 100);
		}

		return $repProfit;
	}

/**
 * (Retail - Rep Cost - Partner Cost - shipping)
 *  + (Difference in True Cost and Partner Cost)
 *  +(App Retail - Axia Expedite Cost - App Cost)
 *  + (Difference in True App Cost and Rep App Cost)
 *  - Rep Profit - Partner Rep Profit - Partner Profit
 *
 * @param decimal $retailCost retail cost
 * @param decimal $repCost rep cost
 * @param decimal $partnerCost partner cost
 * @param decimal $shippingCost shipping cost
 * @param decimal $trueCost true cost
 * @param null|string $expediteCost expedite cost
 * @param null|string $appCost App cost
 * @param null|string $trueAppCost true app cost
 * @param null|string $repAppCost rep's app costs
 * @param null|string $repProfit rep profit
 * @param null|string $partnerProfit partner profit
 * @return int|decimal
 */
	protected function _getAxiaProfit($retailCost, $repCost, $partnerCost, $shippingCost, $trueCost, $expediteCost, $appCost, $trueAppCost, $repAppCost, $repProfit, $partnerProfit) {
		return ($retailCost - $repCost - $partnerCost - $shippingCost)
				+ ($trueCost - $partnerCost)
				+ ($retailCost - $expediteCost - $appCost)
				+ ($trueAppCost - $repAppCost)
				- $repProfit - $partnerProfit;
	}

/**
 * Get the user split commission
 * 
 * @param int $userId user_id
 * @return bool split_commissions
 */
	protected function _getUserSplitCommissions($userId) {
		return $this->User->field('split_commissions', ['User.id' => $userId]);
	}

/**
 * __dbCommissionReportGetSplitCommissions
 * Get the User.split_commissions data
 *
 * @param int $userId user_id
 * @return bool split_commissions
 */
	private function __dbCommissionReportGetSplitCommissions($userId) {
		return  $this->User->field('split_commissions', ['User.id' => $userId]);
	}

/**
 * __archiveCommissionPricings
 *
 * @param int $year report year
 * @param int $month report month
 * @param string $userEmail user email
 *
 * @return bool
 */
	private function __archiveCommissionPricings($year, $month, $userEmail) {
		$products = $this->MerchantPricingArchive->getArchivableProducts();

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		foreach ($products as $productId => $prodName) {
			try {
				/*Get Merchants who have current product AND have install commission date within specified month and year*/
				$merchantIds = $this->Merchant->getCommissionPricingMerchants($productId, $month, $year);
				if (empty($merchantIds)) {
					continue;
				}
				$merchantIds = array_keys($merchantIds);
				$allMerchantsResults = $this->UserCostsArchive->getUserCostArchiveData($merchantIds, $productId);
				if (empty($allMerchantsResults)) {
					continue;
				}

				// add merchant Id to the array keys to have direct access while iterating the exising merchants that have the current product
				$allMerchantsResults = Hash::combine($allMerchantsResults, '{n}.Merchant.id', '{n}');

				foreach ($merchantIds as $merchantId) {
					$merchantResult = Hash::get($allMerchantsResults, $merchantId, []);
					//merchant not in the set of merchants that have the product
					if (empty($merchantResult)) {
						continue;
					}
					$merchantPricingResults = $this->MerchantPricingArchive->setMpArchiveData($merchantId, $productId, $month, $year, $userEmail);
					$userCostResults = $this->UserCostsArchive->formatUserCostArchiveData($merchantResult);

					$mAcquirerId = $merchantResult['Merchant']['merchant_acquirer_id'];
					$mInstMonths = $this->Merchant->getInstMoCount($merchantId);

					$repId = $merchantResult['Merchant']['user_id'];
					$partnerId = Hash::get($merchantResult, 'Merchant.partner_id');

					$record = [];
					// extract the rep/partner_rep data
					$curUsrId = $repId;
					$record['user_id'] = $curUsrId;
					$record['r_risk_assessment'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'risk_assmnt_pct') * 1;
					$record['r_rate_pct'] = ((empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') : 0;
					$record['r_per_item_fee'] = ((empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') : 0;
					$record['r_statement_fee'] = ((empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') : 0;
					$record['rep_pct_of_gross'] = ((empty($partnerId)))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($curUsrId, $productId, $mAcquirerId) : 0;

					$record['partner_rep_rate'] = ((!empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') : 0;
					$record['partner_rep_per_item_fee'] = ((!empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') : 0;
					$record['partner_rep_statement_fee'] = ((!empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') : 0;
					$record['partner_rep_pct_of_gross'] = ((!empty($partnerId)))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($curUsrId, $productId, $mAcquirerId, null, $partnerId) : 0;

					//partner
					$curUsrId = $partnerId;
					$record['partner_id'] = $curUsrId;
					$record['partner_rate'] = ((!empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') : 0;
					$record['partner_per_item_fee'] = ((!empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') : 0;
					$record['partner_statement_fee'] = ((!empty($partnerId)))? $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') : 0;
					$record['partner_pct_of_gross'] = ((!empty($partnerId)))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($curUsrId, $productId, $mAcquirerId) : 0;
					$record['partner_profit_pct'] = ((!empty($partnerId)))? $this->UserCompensationProfile->getDefaultResidualPct($partnerId, $productId) : 0;
					//Extract sm data
					$curUsrId = Hash::get($merchantResult, 'Merchant.sm_user_id');
					$record['manager_id'] = $curUsrId;
					$record['manager_rate'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') * 1;
					$record['manager_per_item_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') * 1;
					$record['manager_statement_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') * 1;
					$record['manager_pct_of_gross'] = (!empty($curUsrId))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($repId, $productId, $mAcquirerId, $curUsrId, $partnerId) : 0;

					//Extract sm2 data
					$curUsrId = Hash::get($merchantResult, 'Merchant.sm2_user_id');
					$record['secondary_manager_id'] = $curUsrId;
					$record['manager2_rate'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') * 1;
					$record['manager2_per_item_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') * 1;
					$record['manager2_statement_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') * 1;
					$record['manager2_pct_of_gross'] = (!empty($curUsrId))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($repId, $productId, $mAcquirerId, $curUsrId, $partnerId) : 0;

					//Extract referrer data
					$curUsrId = Hash::get($merchantResult, 'Merchant.referer_id');
					$record['referrer_rate'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') * 1;
					$record['referrer_per_item_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') * 1;
					$record['referrer_statement_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') * 1;
					$record['referrer_profit_pct'] = ((!empty($partnerId)))? $this->UserCompensationProfile->getDefaultResidualPct($curUsrId, $productId) : 0;

					if ($merchantResult['Merchant']['ref_p_pct'] > 0) {
						$record['referrer_pct_of_gross'] = $merchantResult['Merchant']['ref_p_pct'];
					} else {
						$record['referrer_pct_of_gross'] = (!empty($curUsrId))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($curUsrId, $productId, $mAcquirerId) : 0;
					}

					//Extract reseller data
					$curUsrId = Hash::get($merchantResult, 'Merchant.reseller_id');
					$record['reseller_rate'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'cost_pct') * 1;
					$record['reseller_per_item_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'per_item_cost') * 1;
					$record['reseller_statement_fee'] = $this->UserCostsArchive->getUCAFieldData($userCostResults, $curUsrId, 'monthly_statement_cost') * 1;
					$record['reseller_profit_pct'] = ((!empty($partnerId)))? $this->UserCompensationProfile->getDefaultResidualPct($curUsrId, $productId) : 0;

					if ($merchantResult['Merchant']['res_p_pct'] > 0) {
						$record['reseller_pct_of_gross'] = $merchantResult['Merchant']['res_p_pct'];
					} else {
						$record['reseller_pct_of_gross'] = (!empty($curUsrId))? $this->UserCompensationProfile->getRepOrMgrPctOfGross($curUsrId, $productId, $mAcquirerId) : 0;
					}
					//Set m_avg_ticket, m_monthly_volume, num_items. These values vary depending on the product.
					$this->setItemsVolAvgTicket($merchantResult, $record, $prodName);

					$record['merchant_id'] = $merchantId;
					$record['c_month'] = ($month == '') ? null : $month;
					$record['c_year'] = ($year == '') ? null : $year;
					$record['m_rate_pct'] = Hash::get($merchantPricingResults, 'MerchantPricingArchive.m_rate_pct', null);
					$record['original_m_rate_pct'] = Hash::get($merchantPricingResults, 'MerchantPricingArchive.original_m_rate_pct', null);
					$record['m_per_item_fee'] = Hash::get($merchantPricingResults, 'MerchantPricingArchive.m_per_item_fee', null);
					$record['m_statement_fee'] = Hash::get($merchantPricingResults, 'MerchantPricingArchive.m_statement_fee', null);
					$record['referer_id'] = ($merchantResult['Merchant']['referer_id'] == '') ? null : $merchantResult['Merchant']['referer_id'];
					$record['reseller_id'] = ($merchantResult['Merchant']['reseller_id'] == '') ? null : $merchantResult['Merchant']['reseller_id'];
					$record['products_services_type_id'] = ($productId == '') ? null : $productId;
					$record['bet_table_id'] = Hash::get($merchantPricingResults, 'MerchantPricingArchive.bet_table_id', null);
					$record['merchant_state'] = Hash::get($merchantResult, 'Address.address_state');

					$betTable = $this->__getBetTable(Hash::get($merchantPricingResults, 'MerchantPricingArchive.bet_table_id', null));
					if (!empty($betTable)) {
						$record['bet_extra_pct'] = ($betTable['BetTable']['bet_extra_pct'] == '') ? null : $betTable['BetTable']['bet_extra_pct'];
					}

					/*Set Users Gross Profit */
					$this->_setUsrsGrossProfit($record, $merchantResult, $prodName, $userCostResults, $merchantPricingResults);

					$smUsrId = Hash::get($merchantResult, 'Merchant.sm_user_id');
					$sm2UsrId = Hash::get($merchantResult, 'Merchant.sm2_user_id');
					$record['multiple'] = $this->UserCompensationProfile->getMultiple($repId, $partnerId, $productId, $mInstMonths);
					$record['manager_multiple'] = !empty($smUsrId)? $this->UserCompensationProfile->getMultiple($repId, $partnerId, $productId, $mInstMonths, $smUsrId) : 0;
					$record['manager2_multiple'] = !empty($sm2UsrId)? $this->UserCompensationProfile->getMultiple($repId, $partnerId, $productId, $mInstMonths, $sm2UsrId) : 0;
					$this->_setMultipleAmounts($record, $merchantResult, $productId);

					$percent = $this->User->UserCompensationProfile->getUcpResidualData($repId, $mInstMonths, $partnerId, $productId);

					if (!empty($percent)) {
						$record['rep_product_profit_pct'] = Hash::get($percent, 'ResidualParameter.residual_percent');
					}

					$record['ref_p_type'] = ($merchantResult['Merchant']['ref_p_type'] == '') ? null : $merchantResult['Merchant']['ref_p_type'];
					$record['ref_p_value'] = ($merchantResult['Merchant']['ref_p_value'] == '') ? null : $merchantResult['Merchant']['ref_p_value'];
					$record['ref_p_pct'] = ($merchantResult['Merchant']['ref_p_pct'] == '') ? null : $merchantResult['Merchant']['ref_p_pct'];
					$record['res_p_type'] = ($merchantResult['Merchant']['res_p_type'] == '') ? null : $merchantResult['Merchant']['res_p_type'];
					$record['res_p_value'] = ($merchantResult['Merchant']['res_p_value'] == '') ? null : $merchantResult['Merchant']['res_p_value'];
					$record['res_p_pct'] = ($merchantResult['Merchant']['res_p_pct'] == '') ? null :$merchantResult['Merchant']['res_p_pct'];

					$this->CommissionPricing->create();
					if (!$this->CommissionPricing->save($record)) {
						$dataSource->rollback();
						return false;
					}
				}

			} catch (Exception $e) {
				$dataSource->rollback();
				print $e->getMessage() . "\n";
				return false;
			}
		}
		$dataSource->commit();
		return true;
	}

/**
 * setItemsVolAvgTicket
 *
 * @param array $merchantData merchant data containing information about volume, number of items and average ticket
 * @param array &$commPricingRecord id
 * @param string $productName a product name.
 * @return void
 */
	public function setItemsVolAvgTicket($merchantData, &$commPricingRecord, $productName) {
		$monthlyVol = null;
		$items = null;
		$avgTicket = null;
		if (strpos($productName, 'Visa') !== false) {
			$monthlyVol = Hash::get($merchantData, 'MerchantUwVolume.visa_volume');
			$avgTicket = Hash::get($merchantData, 'MerchantUwVolume.average_ticket');
		}
		if (strpos($productName, 'MasterCard') !== false) {
			$monthlyVol = Hash::get($merchantData, 'MerchantUwVolume.mc_volume');
			$avgTicket = Hash::get($merchantData, 'MerchantUwVolume.average_ticket');
		}
		if (strpos($productName, 'American Express') !== false) {
			$monthlyVol = Hash::get($merchantData, 'MerchantUwVolume.amex_volume');
			$avgTicket = Hash::get($merchantData, 'MerchantUwVolume.amex_avg_ticket');
		}
		if (strpos($productName, 'Discover') !== false) {
			$monthlyVol = Hash::get($merchantData, 'MerchantUwVolume.ds_volume');
			$avgTicket = Hash::get($merchantData, 'MerchantUwVolume.average_ticket');
		}
		if (strpos($productName, 'Debit') !== false || strpos($productName, 'EBT') !== false) {
			//Set values iff these products are not Monthly products
			if (strpos($productName, 'Monthly') === false) {
				$monthlyVol = Hash::get($merchantData, 'MerchantUwVolume.pin_debit_volume');
				$avgTicket = Hash::get($merchantData, 'MerchantUwVolume.pin_debit_avg_ticket');
			}
		}
		if ($productName === 'Gateway 1') {
			$monthlyVol = Hash::get($merchantData, 'Gateway1.gw1_monthly_volume');
			$items = Hash::get($merchantData, 'Gateway1.gw1_monthly_num_items');
			//No field exists for gateway1s average ticket but can be calculated as follows
			if ($items > 0) {
				$avgTicket = bcdiv($monthlyVol, $items, 2);
			}
		}
		if ($productName === 'ACH') {
			$monthlyVol = Hash::get($merchantData, 'Ach.ach_expected_annual_sales') / 12;
			$avgTicket = Hash::get($merchantData, 'Ach.ach_average_transaction');
		}
		if (is_null($items) && $avgTicket > 0) {
			$items = bcdiv($monthlyVol, $avgTicket, 2);
		}
		$commPricingRecord['m_monthly_volume'] = $monthlyVol;
		$commPricingRecord['m_avg_ticket'] = $avgTicket;
		$commPricingRecord['num_items'] = $items;
	}

/**
 * __getBetTable
 *
 * @param string $id id
 *
 * @return array $results
 */
	private function __getBetTable($id) {
		$results = $this->BetTable->find('first',
			[
				'conditions' => [
					'id' => $id
				]
			]
		);

		return $results;
	}

/**
 * _setMultipleAmounts
 *
 * @param array &$record nonempty reference of commission report data containing previously set values involved in the multiple ammount calculation
 * @param array &$merchant merchant record subset associated with the commission report data
 * @param string $productId used to retrieve the user residual percent
 * @return void
 */
	protected function _setMultipleAmounts(&$record, &$merchant, $productId) {
		//Array of values to pass for multimple ammount calculation
		$calVals = [];
		/*
		Get
		Partner Profit
		Referrer Profit	 > User Gross Profit * Percent of Gross * Residual %
		Reseller Profit
		*/
		$calVals['p_profit_amount'] = $this->AxiaCalculate->thirdPartyProfitAmnt(
			[
				'u_gross_profit' => $record['partner_gross_profit'],
				'u_pct_of_gross' => $record['partner_pct_of_gross'],
				'u_residual_pct' => $record['partner_profit_pct']
			]
		);
		$calVals['ref_profit_amount'] = $this->AxiaCalculate->thirdPartyProfitAmnt(
			[
				'u_gross_profit' => $record['referrer_gross_profit'],
				'u_pct_of_gross' => $record['referrer_pct_of_gross'],
				'u_residual_pct' => $this->User->UserCompensationProfile->getDefaultResidualPct(
					$merchant['Merchant']['referer_id'], $productId
				)
			]
		);
		$calVals['res_profit_amount'] = $this->AxiaCalculate->thirdPartyProfitAmnt(
			[
				'u_gross_profit' => $record['reseller_gross_profit'],
				'u_pct_of_gross' => $record['reseller_pct_of_gross'],
				'u_residual_pct' => $this->User->UserCompensationProfile->getDefaultResidualPct(
					$merchant['Merchant']['reseller_id'], $productId
				)
			]
		);

		if ($record['multiple'] > 0) {
			$calVals['rep_gross_prft'] = (empty($merchant['Merchant']['partner_id']))? $record['rep_gross_profit'] : $record['partner_rep_gross_profit'];
			$calVals['rep_pct_of_gross'] = (empty($merchant['Merchant']['partner_id']))? $record['rep_pct_of_gross'] : $record['partner_rep_pct_of_gross'];
			$calVals['multiple'] = $record['multiple'];
			$record['multiple_amount'] = $this->AxiaCalculate->multipleAmnt($calVals);
		}
		if ($record['manager_multiple'] > 0) {
			$calVals['rep_gross_prft'] = $record['manager_gross_profit'];
			$calVals['rep_pct_of_gross'] = $record['manager_pct_of_gross'];
			$calVals['multiple'] = $record['manager_multiple'];
			$record['manager_multiple_amount'] = $this->AxiaCalculate->multipleAmnt($calVals);
		}
		if ($record['manager2_multiple'] > 0) {
			$calVals['rep_gross_prft'] = $record['manager2_gross_profit'];
			$calVals['rep_pct_of_gross'] = $record['manager2_pct_of_gross'];
			$calVals['multiple'] = $record['manager2_multiple'];
			$record['manager2_multiple_amount'] = $this->AxiaCalculate->multipleAmnt($calVals);
		}
	}
/**
 * __addCommissionReportRecord
 *
 * @param array $record commission report data
 *
 * @return bool result
 */
	private function __addCommissionReportRecord($record) {
		if (empty($record)) {
			return false;
		}
		$this->create();

		return $this->save($record);
	}

/**
 * sendCompletionStatusEmail
 *
 * @param array $response job status info
 * @param string $email email address
 *
 * @return void
 */
	public function sendCompletionStatusEmail($response, $email) {
		$event = new CakeEvent('App.Model.readyForEmail', $this, [
			'template' => Configure::read('App.bgJobEmailTemplate'),
			'from' => Configure::read('App.defaultSender'),
			'to' => $email,
			'subject' => 'Commission Admin Notice',
			'emailBody' => $response
		]);

		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
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
	protected function _findIndex($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = [
				'CommissionReport.c_year',
				'CommissionReport.c_month',
				'CommissionReport.status'
			];

			$query['group'] = [
				'CommissionReport.c_year',
				'CommissionReport.c_month',
				'CommissionReport.status'
			];
			$query['recursive'] = -1;

			return $query;
		}
		return $results;
	}

/**
 * getIndexData
 * When Commission report is built, CommissionReport data may or may not be generated but CommissionPricing data always is in every build.
 * This function uses the more reliably existing CommissionPricing data for the given year/month.
 * 
 * @param array $settings search settings
 * @return array containing CommissionReport and/or CommissionPricing data
 */
	public function getIndexData($settings = array()) {
		//CommissionReport and CommissionPricing have the same month/year field names
		$settings['fields'] = 'DISTINCT c_year, c_month';
		$settings['order'] = ['c_month ASC'];
		$cPrResult = ClassRegistry::init('CommissionPricing')->find('all', $settings);

		$settings['fields'] = 'DISTINCT c_year, c_month, status';
		$cReportResult = $this->find('all', $settings);
       return Hash::merge(Hash::extract($cPrResult, '{n}.CommissionPricing'), Hash::extract($cReportResult, '{n}.CommissionReport'));
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
	protected function _findCommissionReport($state, $query, $results = []) {
		if ($state === 'before') {
			// @todo: commented fields are created on branch "feature/352-commission-admin". Uncomment when branch is merged
			$this->virtualFields['rep'] = $this->User->getFullNameVirtualField('User');
			//Get the full result set so that calculation totals display the full amounts
			$count = $this->find('count');
			$query['limit'] = $count;
			$query['maxLimit'] = $count;
			$query['fields'] = [
				"{$this->alias}.id",
				"{$this->alias}.merchant_id",
				"{$this->alias}.order_id",
				"{$this->alias}.axia_invoice_number",
				"{$this->alias}.c_year",
				"{$this->alias}.c_month",
				"{$this->alias}.c_retail",
				"{$this->alias}.c_rep_cost",
				"{$this->alias}.partner_cost",
				"{$this->alias}.c_shipping",
				"{$this->alias}.shipping_type_id",
				"{$this->alias}.c_app_rtl",
				"{$this->alias}.c_app_cost",
				"{$this->alias}.c_from_nw1",
				"{$this->alias}.c_other_cost",
				"{$this->alias}.c_from_other",
				"{$this->alias}.description",
				"{$this->alias}.c_business_calculated",
				"{$this->alias}.c_expecting_calculated",
				"{$this->alias}.rep",
				"{$this->alias}.partner_rep_profit",
				"{$this->alias}.partner_profit",
				"{$this->alias}.tax_amount",
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Client.client_id_global',
				'Address.address_street',
				'Address.address_state',
				'MerchantUws.expedited',
				'Entity.entity_name',
				'Organization.name',
				'Region.name',
				'Subregion.name',
				'((' . $this->User->getFullNameVirtualField('Partner') . ')) AS "Partner__partner_name"',
			];

			$query['joins'] = [
				[
					'table' => 'merchants',
					'alias' => 'Merchant',
					'type' => 'LEFT',
					'conditions' => [
						"Merchant.id = {$this->alias}.merchant_id",
					]
				],
				[
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
					'conditions' => [
						"Merchant.client_id = Client.id",
					]
				],
				[
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'LEFT',
					'conditions' => [
						"Address.merchant_id = {$this->alias}.merchant_id",
						'Address.address_type_id' => AddressType::BUSINESS_ADDRESS
					]
				],
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => [
						"User.id = {$this->alias}.user_id"
					]
				],
				[
					'table' => 'users',
					'alias' => 'Partner',
					'type' => 'LEFT',
					'conditions' => [
						"Partner.id = {$this->alias}.partner_id"
					]
				],
				[
					'table' => 'merchant_uws',
					'alias' => 'MerchantUws',
					'type' => 'LEFT',
					'conditions' => [
						"MerchantUws.merchant_id = {$this->alias}.merchant_id"
					]
				],
				[
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => [
						"Entity.id = Merchant.entity_id"
					]
				],
				[
					'table' => 'organizations',
					'alias' => 'Organization',
					'type' => 'LEFT',
					'conditions' => [
						"Organization.id = Merchant.organization_id"
					]
				],
				[
					'table' => 'regions',
					'alias' => 'Region',
					'type' => 'LEFT',
					'conditions' => [
						"Region.id = Merchant.region_id"
					]
				],
				[
					'table' => 'subregions',
					'alias' => 'Subregion',
					'type' => 'LEFT',
					'conditions' => [
						"Subregion.id = Merchant.subregion_id"
					]
				]
			];

			$currentUserId = $this->_getCurrentUser('id');
			$defaultConditions = [
				"{$this->alias}.status" => RolePermissions::getAllowedReportStatuses($currentUserId),
			];
			if ($this->User->roleIs($currentUserId, $this->User->userRoles['roles'][User::ROLE_GROUP_REFERRER]['roles'])) {
				$defaultConditions[] = [
					'OR' => [
						"{$this->alias}.referer_id" => $currentUserId,
						"Merchant.referer_id" => $currentUserId
					]
				];
			}
			if ($this->User->roleIs($currentUserId, $this->User->userRoles['roles'][User::ROLE_GROUP_RESELLER]['roles'])) {
				$defaultConditions[] = [
					'OR' => [
						"{$this->alias}.reseller_id" => $currentUserId,
						"Merchant.reseller_id" => $currentUserId
					]
				];
			}
			$query['conditions'] = Hash::merge($defaultConditions, Hash::get($query, 'conditions'));
			return $query;
		} else {

			if (!empty($results)) {
				$Ordertiem = ClassRegistry::init('Orderitem');

				foreach ($results as $idx => $data) {
					if ($data['CommissionReport']['description'] === 'Equipment Order' && !empty($data['CommissionReport']['order_id'])) {
						$items = $Ordertiem->find('all', [
							'fields' => [
							'(("Orderitem"."equipment_item_description" || \' (x \' || sum(Orderitem.quantity) || \')\')) AS "Orderitem__items"',
							],
							'group' => ['Orderitem.equipment_item_description'],
							'conditions' => [
								'order_id' => $data['CommissionReport']['order_id'],
								'merchant_id' => $data['CommissionReport']['merchant_id'],
								'commission_month' => $data['CommissionReport']['c_month'],
								'commission_year' => $data['CommissionReport']['c_year'],

							]
						]);

						$results[$idx]['Orderitems'] = Hash::format($items, ['{n}.Orderitem.items'], '%1$s');
					}
				}
			}
		}

		return $results;
	}

/**
 * Return the values for the commission report
 *
 * @param array $filterParams Filter parameters
 * @return array
 * @return array
 */
	public function getCommissionReportData($filterParams = []) {
		$commissions = $this->find('commissionReport', [
			'conditions' => $this->parseCriteria($filterParams),
		]);
		$CommissionPricing = ClassRegistry::init('CommissionPricing');
		$commMultipleTotals['multiple_amount'] = $CommissionPricing->getTotalMultipleAmnt(User::ROLE_REP, $filterParams);
		$commMultipleTotals['manager_multiple_amount'] = $CommissionPricing->getTotalMultipleAmnt(User::ROLE_SM, $filterParams);
		$commMultipleTotals['manager2_multiple_amount'] = $CommissionPricing->getTotalMultipleAmnt(User::ROLE_SM2, $filterParams);
		$userId = $this->_getCurrentUser('id');
		$commissions['commissionMultipleTotals'] = RolePermissions::filterReportData(CommissionPricing::REPORT_COMMISION_MULTIPLE, $userId, $commMultipleTotals);
		return $this->setReportVars($commissions, $filterParams);
	}

/**
 * setReportVars
 *
 * @param array $commissions Reports data
 * @param array $filterParams Report search parameters
 * @return array
 */
	public function setReportVars($commissions, $filterParams = []) {
		$repAdjustments = [];
		$income = [];
		$netIncome = null;
		$commissionReports = [];
		$shippingTypes = [];

		$reportStatusParams['from_date'] = $filterParams['from_date'];
		$reportStatusParams['end_date'] = $filterParams['end_date'];
		$conditions = $this->parseCriteria($reportStatusParams);

		$currentUserId = $this->_getCurrentUser('id');
		$allowedReportStatuses = RolePermissions::getAllowedReportStatuses($currentUserId);
		$conditions[] = ["{$this->alias}.status" => $allowedReportStatuses];
		$hasAny =  (bool)$this->find('count', [
			'conditions' => $conditions,
			'joins' => [
				[ //need to add a Merchant join since a Merchant condition is injected by the SearcByUserIdBehavior::searchByEntity method
					'table' => 'merchants',
					'type' => 'LEFT',
					'alias' => 'Merchant',
					'conditions' => [
						'CommissionReport.merchant_id = Merchant.id'
					]
				]
			],
			'limit' => 1
		]);

		if ($hasAny || in_array(self::STATUS_INACTIVE, $allowedReportStatuses)) {
			$repAdjustments = $this->_processRepAdjustmentsData($filterParams);
			$income = $this->_processIncomeData($commissions, $filterParams);
			$netIncome = RolePermissions::filterReportData(self::SECTION_NET_INCOME, $currentUserId, [
				'netIncome' => Hash::get($income, 'gross_income') + Hash::get($repAdjustments, 'gross_adjustments'),
			]);
			$shippingTypes = $this->ShippingType->find('list');
		}
		$commissionReports = $this->_processCommissionReportData($commissions);
		$organizations = $this->Merchant->Organization->find('list');
		$regions = $this->Merchant->Region->find('list');
		$subregions = $this->Merchant->Subregion->find('list');
		$partners = [];
		if ($this->User->roleIs($currentUserId, User::ROLE_PARTNER) === false) {
			$partners = $this->User->getByRole(User::ROLE_PARTNER);
		}
		return Hash::merge(
			$commissionReports,
			compact('shippingTypes', 'income', 'repAdjustments', 'netIncome', 'organizations', 'regions', 'subregions', 'partners')
		);
	}
/**
 * Calculate and format the income section data from the commission report to match the permission matrix
 *
 * @param array $commissionReports Commission Reports data
 * @param array $filterParams Filter parameters
 * @throws InvalidArgumentException
 * @return array
 */
	protected function _processIncomeData($commissionReports, $filterParams = array()) {
		if (!is_array($commissionReports)) {
			throw new InvalidArgumentException(__('CommissionReports must be an array'));
		}
		$parsedId = $this->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$selUserIsParent = false;
		$selUserIsSm = false;
		$selUserIsSm2 = false;
		if ($parsedId['prefix'] == User::PREFIX_PARENT || $parsedId['prefix'] == User::PREFIX_ALL) {
			$selUserIsParent = true;
		} elseif ($this->User->roleIs($parsedId['id'], User::ROLE_SM)) {
			$selUserIsSm = true;
		} elseif ($this->User->roleIs($parsedId['id'], User::ROLE_SM2)) {
			$selUserIsSm2 = true;
		}

		$ResidualReportModel = ClassRegistry::init('ResidualReport');
		$repResiduals = $ResidualReportModel->getTotalResiduals(User::ROLE_REP, $filterParams);
		$income = [
			'rep_residuals' => $this->setDecimalsNoRounding($repResiduals, 2),
			'sm_residuals' => $this->setDecimalsNoRounding($ResidualReportModel->getTotalResiduals(User::ROLE_SM, $filterParams), 2),
			'sm2_residuals' => $this->setDecimalsNoRounding($ResidualReportModel->getTotalResiduals(User::ROLE_SM2, $filterParams), 2),
			'partner_residuals' => $this->setDecimalsNoRounding($ResidualReportModel->getTotalResiduals(User::ROLE_PARTNER, $filterParams), 2),
			'commission' => 0
		];
		//Inject the commission multiples totals. Some of the values may not be present depending on current user's report permissions
		if (array_key_exists('multiple_amount', Hash::get($commissionReports, 'commissionMultipleTotals'))) {
			$income['multiple_amount'] = $this->setDecimalsNoRounding(Hash::get($commissionReports, 'commissionMultipleTotals.multiple_amount'),2);
		}
		if (array_key_exists('manager_multiple_amount', Hash::get($commissionReports, 'commissionMultipleTotals'))) {
			$income['manager_multiple_amount'] = $this->setDecimalsNoRounding(Hash::get($commissionReports, 'commissionMultipleTotals.manager_multiple_amount'),2);
		}
		if (array_key_exists('manager2_multiple_amount', Hash::get($commissionReports, 'commissionMultipleTotals'))) {
			$income['manager2_multiple_amount'] = $this->setDecimalsNoRounding(Hash::get($commissionReports, 'commissionMultipleTotals.manager2_multiple_amount'),2);
		}

		$income['partner_commission'] = 0;
		$income['gross_income'] = 0;
		foreach ($commissionReports as $commission) {
			$income['commission'] += $this->setDecimalsNoRounding(Hash::get($commission, 'CommissionReport.c_expecting_calculated'), 2);
			$income['partner_commission'] += $this->setDecimalsNoRounding(Hash::get($commission, 'CommissionReport.partner_profit'), 2);
		}
		

		foreach ($income as $key => $incomeValue) {
			if ($key !== 'gross_income' && $key !== 'partner_residuals' && $key !== 'partner_commission') {
				if ($key !== 'manager_multiple_amount' && $key !== 'manager2_multiple_amount') {
					$income['gross_income'] += $incomeValue;
				} else {
					if ($key === 'manager_multiple_amount' && ($selUserIsSm || $selUserIsParent)) {
						$income['gross_income'] += $incomeValue;
					}
					if ($key === 'manager2_multiple_amount' && $selUserIsSm2) {
						$income['gross_income'] += $incomeValue;
					}
				}
			}
		}

		return RolePermissions::filterReportData(self::SECTION_INCOME, $this->_getCurrentUser('id'), $income);
	}

/**
 * Get and format the rep adjustments section data from the commission report to match the permission matrix
 *
 * @param array $filterParams Filter parameters
 * @return array
 */
	protected function _processRepAdjustmentsData($filterParams = []) {
		$AdjustmentModel = ClassRegistry::init('Adjustment');
		$adjustments = $AdjustmentModel->find('adjustments', [
			'conditions' => $AdjustmentModel->parseCriteria($filterParams)
		]);
		$repAdjustments = [
			'adjustments' => Hash::extract($adjustments, '{n}.Adjustment'),
			'gross_adjustments' => 0,
		];
		foreach ($repAdjustments['adjustments'] as $adjustment) {
			$repAdjustments['gross_adjustments'] += Hash::get($adjustment, 'adj_amount');
		}

		return RolePermissions::filterReportData(self::SECTION_ADJUSTMENTS, $this->_getCurrentUser('id'), $repAdjustments);
	}

/**
 * Format the commissions section data from the commission report to match the permission matrix
 *
 * @param array $commissionReports Commission Reports data
 * @throws InvalidArgumentException
 * @return array
 */
	protected function _processCommissionReportData($commissionReports) {
		if (!is_array($commissionReports)) {
			throw new InvalidArgumentException(__('"commissionReports" must be an array'));
		}
		$commissions = [];
		$totals = [
			'rep_profit' => 0,
			'partner_profit' => 0,
			'axia_profit' => 0,
			'tax_amount' => 0,
		];
		foreach ($commissionReports as $commissionReport) {
			$repProfit = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.c_expecting_calculated'), 2);
			$partnerProfit = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.partner_profit'), 2);
			$axiaProfit = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.axia_profit'), 2);
			$taxAmount = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.tax_amount'), 2);
			$appCost = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.c_app_cost'), 2);
			$partnerAppCost = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.partner_app_cost'), 2);
			$appFee = $this->setDecimalsNoRounding(Hash::get($commissionReport, 'CommissionReport.c_app_rtl'), 2);
			$description = Hash::get($commissionReport, 'CommissionReport.description');

			if ($description === 'Equipment Order') {
				$items = Hash::get($commissionReport, 'Orderitems');
				if (count($items) <= 2) {
					$description = implode( ', and ', $items);
					unset($items);
				}
			}
			if (!empty(Hash::get($commissionReport, 'CommissionReport.id'))) {
				$commission = [
					'mid' => Hash::get($commissionReport, 'Merchant.merchant_mid'),
					'dba' => Hash::get($commissionReport, 'Merchant.merchant_dba'),
					'client_id_global' => Hash::get($commissionReport, 'Client.client_id_global'),
					'axia_invoice' => Hash::get($commissionReport, 'CommissionReport.axia_invoice_number'),
					'rep' => Hash::get($commissionReport, 'CommissionReport.rep'),
					'description' => $description,
					'retail' => Hash::get($commissionReport, 'CommissionReport.c_retail') + $appFee,
					'rep_cost' => Hash::get($commissionReport, 'CommissionReport.c_rep_cost') + $appCost,
					'partner_cost' => Hash::get($commissionReport, 'CommissionReport.partner_cost') + $partnerAppCost,
					'shipping_type' => Hash::get($commissionReport, 'CommissionReport.shipping_type_id'),
					'shipping_cost' => Hash::get($commissionReport, 'CommissionReport.c_shipping'),
					'expedited' => Hash::get($commissionReport, 'MerchantUws.expedited') ? __('Yes') : __('No'),
					'rep_profit' => $repProfit,
					'partner_profit' => $partnerProfit,
					'axia_profit' => $axiaProfit,
					'tax_amount' => $taxAmount,
					'state' => Hash::get($commissionReport, 'Address.address_state'),
					'ent_name' => Hash::get($commissionReport, 'Entity.entity_name'),
					'organization' => Hash::get($commissionReport, 'Organization.name'),
					'region' => Hash::get($commissionReport, 'Region.name'),
					'subregion' => Hash::get($commissionReport, 'Subregion.name'),
					'location' => Hash::get($commissionReport, 'Address.address_street'),
					'partner_name' => Hash::get($commissionReport, 'Partner.partner_name'),
				];
				$commissions[] = $commission;
			}

			$totals['rep_profit'] += $repProfit;
			$totals['partner_profit'] += $partnerProfit;
			$totals['axia_profit'] += $axiaProfit;
			$totals['tax_amount'] += $taxAmount;

			if (Hash::get($commissionReport, 'CommissionReport.description') === 'Equipment Order' && isset($items) && count($items) > 2) {
				//Add a row per each item when there are many items
				foreach ($items as $item) {
					$commissions[] = array_merge(array_fill(0, 5, ''), [$item], array_fill(6, count($commission) - 5, ''));
				}
			}
		}

		$userId = $this->_getCurrentUser('id');
		return [
			'commissionReports' => RolePermissions::filterReportData(self::SECTION_COMMISION_REPORTS, $userId, $commissions),
			'commissionReportTotals' => RolePermissions::filterReportData(self::SECTION_COMMISION_REPORTS, $userId, $totals),
		];
	}

/**
 * _setUsrsGrossProfit
 * Sets all calculated users gross profit data associated with the referenced &$assocMrchnt.
 * The gross profit fields are set in the commission pricing record reference &$comPricingRec.
 *
 * @param array &$comPricingRec containing commission pricing data reference
 * @param array &$assocMrchnt merchant associated with current commission pricing record
 * @param string $product a product name
 * @param array &$userCostResults reference to user costs archive data related to current commission pricing data reference
 * @param array &$merchantPricingResults reference to user merchant pricings data related current commission pricing data reference
 * @return array with the allowed data
 */
	protected function _setUsrsGrossProfit(&$comPricingRec, &$assocMrchnt, $product, &$userCostResults, &$merchantPricingResults) {
		$comPricingRec['m_monthly_volume'] = Hash::get($comPricingRec, 'm_monthly_volume', 0);
		$comPricingRec['m_rate_pct'] = Hash::get($comPricingRec, 'm_rate_pct', 0);
		$comPricingRec['num_items'] = Hash::get($comPricingRec, 'num_items', 0);
		$comPricingRec['m_per_item_fee'] = Hash::get($comPricingRec, 'm_per_item_fee', 0);
		$mDiscItemFee = Hash::get($merchantPricingResults, 'MerchantPricingArchive.m_discount_item_fee', 0);
		//Set amounts array structure required for AxiaCalculate->uGrossPrft($amounts) method
		$amounts = [
			"volume" => $comPricingRec['m_monthly_volume'],
			"m_rate" => $comPricingRec['m_rate_pct'],
			"user_rate" => 0.00,
			"user_risk_pct" => 0.00,
			"num_items" => $comPricingRec['num_items'],
			"m_item_fee" => $comPricingRec['m_per_item_fee'],
			"user_pi" => 0.00
		];

		$rRiskPct = $this->UserCostsArchive->getUCAFieldData($userCostResults, $assocMrchnt['Merchant']['user_id'], 'risk_assmnt_pct') * 1;
		if (empty($assocMrchnt['Merchant']['partner_id'])) {
			$comPricingRec['r_rate_pct'] = Hash::get($comPricingRec, 'r_rate_pct' , 0);
			$comPricingRec['r_per_item_fee'] = Hash::get($comPricingRec, 'r_per_item_fee' , 0);
		} else {
			$comPricingRec['partner_rep_rate'] = Hash::get($comPricingRec, 'partner_rep_rate' , 0);
			$comPricingRec['partner_rep_per_item_fee'] = Hash::get($comPricingRec, 'partner_rep_per_item_fee' , 0);
		}

		$comPricingRec['partner_rate'] = Hash::get($comPricingRec, 'partner_rate' , 0);
		$comPricingRec['partner_per_item_fee'] = Hash::get($comPricingRec, 'partner_per_item_fee' , 0);
		$pRiskPct = $this->UserCostsArchive->getUCAFieldData($userCostResults, Hash::get($assocMrchnt, 'Merchant.partner_id'), 'risk_assmnt_pct') * 1;

		$comPricingRec['manager_rate'] = Hash::get($comPricingRec, 'manager_rate' , 0);
		$comPricingRec['manager_per_item_fee'] = Hash::get($comPricingRec, 'manager_per_item_fee' , 0);
		$smRiskPct = $this->UserCostsArchive->getUCAFieldData($userCostResults, Hash::get($assocMrchnt, 'Merchant.sm_user_id'), 'risk_assmnt_pct') * 1;

		$comPricingRec['manager2_rate'] = Hash::get($comPricingRec, 'manager2_rate' , 0);
		$comPricingRec['manager2_per_item_fee'] = Hash::get($comPricingRec, 'manager2_per_item_fee' , 0);
		$sm2RiskPct = $this->UserCostsArchive->getUCAFieldData($userCostResults, Hash::get($assocMrchnt, 'Merchant.sm2_user_id'), 'risk_assmnt_pct') * 1;

		$comPricingRec['referrer_rate'] = Hash::get($comPricingRec, 'referrer_rate' , 0);
		$comPricingRec['referrer_per_item_fee'] = Hash::get($comPricingRec, 'referrer_per_item_fee' , 0);
		$refRriskPct = $this->UserCostsArchive->getUCAFieldData($userCostResults, Hash::get($assocMrchnt, 'Merchant.referer_id'), 'risk_assmnt_pct') * 1;

		$comPricingRec['reseller_rate'] = Hash::get($comPricingRec, 'reseller_rate' , 0);
		$comPricingRec['reseller_per_item_fee'] = Hash::get($comPricingRec, 'reseller_per_item_fee' , 0);
		$resRiskPct = $this->UserCostsArchive->getUCAFieldData($userCostResults, Hash::get($assocMrchnt, 'Merchant.reseller_id'), 'risk_assmnt_pct') * 1;

		if ($product == 'ACH') {
			$amounts = [
				"num_items" => $comPricingRec['num_items'],
				"m_item_fee" => $comPricingRec['m_per_item_fee'],
				"volume" => $comPricingRec['m_monthly_volume'],
				'm_rate' => $comPricingRec['m_rate_pct'],
				'm_monthly' => $comPricingRec['m_statement_fee'],
			];
			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$amounts['user_pi'] = $comPricingRec['partner_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['partner_statement_fee'];
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);

				$amounts['user_pi'] = $comPricingRec['partner_rep_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['partner_rep_statement_fee'];
				$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
			} else {
				$amounts['user_pi'] = $comPricingRec['r_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['r_statement_fee'];
				$repGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
			}

			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$amounts['user_pi'] = $comPricingRec['manager_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['manager_statement_fee'];
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$amounts['user_pi'] = $comPricingRec['manager2_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['manager2_statement_fee'];
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$amounts['user_pi'] = $comPricingRec['referrer_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['referrer_statement_fee'];
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$amounts['user_pi'] = $comPricingRec['reseller_per_item_fee'];
				$amounts['u_monthly'] = $comPricingRec['reseller_statement_fee'];
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
			}
		}
		if ($product == 'Gateway 1') {
			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$amounts['user_rate'] = $comPricingRec['partner_rate'];
				$amounts['user_risk_pct'] = $pRiskPct;
				$amounts['user_pi'] = $comPricingRec['partner_per_item_fee'];
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['partner_statement_fee']);
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$amounts['user_rate'] = $comPricingRec['referrer_rate'];
				$amounts['user_risk_pct'] = $refRriskPct;
				$amounts['user_pi'] = $comPricingRec['referrer_per_item_fee'];
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['referrer_statement_fee']);
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$amounts['user_rate'] = $comPricingRec['reseller_rate'];
				$amounts['user_risk_pct'] = $resRiskPct;
				$amounts['user_pi'] = $comPricingRec['reseller_per_item_fee'];
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['reseller_statement_fee']);
			}
				if (empty($assocMrchnt['Merchant']['partner_id'])) {
					$amounts['user_rate'] = $comPricingRec['r_rate_pct'];
					$amounts['user_risk_pct'] = $rRiskPct;
					$amounts['user_pi'] = $comPricingRec['r_per_item_fee'];
					$repGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['r_statement_fee']);
				} else {
					$amounts['user_rate'] = $comPricingRec['partner_rep_rate'];
					$amounts['user_risk_pct'] = $rRiskPct;
					$amounts['user_pi'] = $comPricingRec['partner_rep_per_item_fee'];
					$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['partner_rep_statement_fee']);
				}
			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$amounts['user_rate'] = $comPricingRec['manager_rate'];
				$amounts['user_risk_pct'] = $smRiskPct;
				$amounts['user_pi'] = $comPricingRec['manager_per_item_fee'];
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['manager_statement_fee']);
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$amounts['user_rate'] = $comPricingRec['manager2_rate'];
				$amounts['user_risk_pct'] = $sm2RiskPct;
				$amounts['user_pi'] = $comPricingRec['manager2_per_item_fee'];
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($comPricingRec['m_statement_fee'] - $comPricingRec['manager2_statement_fee']);
			}
		}
		if ($product == 'American Express Discount' || $product == 'American Express Discount (Converted Merchants)' ||
			$product == 'Discover Discount' ||
			$product == 'MasterCard Discount' || $product == 'Visa Discount') {

			$amounts['m_disc_item_fee'] = $mDiscItemFee;
			unset($amounts['m_item_fee'], $amounts['user_pi']);
			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$amounts['user_rate'] = $comPricingRec['partner_rate'];
				$amounts['user_risk_pct'] = $pRiskPct;
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);

				$amounts['user_rate'] = $comPricingRec['partner_rep_rate'];
				$amounts['user_risk_pct'] = $rRiskPct;
				$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);
			} else {
				$amounts['user_rate'] = $comPricingRec['r_rate_pct'];
				$amounts['user_risk_pct'] = $rRiskPct;
				$repGrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$amounts['user_rate'] = $comPricingRec['referrer_rate'];
				$amounts['user_risk_pct'] = $refRriskPct;
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$amounts['user_rate'] = $comPricingRec['reseller_rate'];
				$amounts['user_risk_pct'] = $resRiskPct;
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$amounts['user_rate'] = $comPricingRec['manager_rate'];
				$amounts['user_risk_pct'] = $smRiskPct;
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$amounts['user_rate'] = $comPricingRec['manager2_rate'];
				$amounts['user_risk_pct'] = $sm2RiskPct;
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrftType3($amounts);
			}
		}

		if ($product == 'American Express Sales' ||
			$product == 'American Express Dial Sales' ||
			$product == 'American Express Non Dial Sales' ||
			$product == 'American Express Authorizations' ||
			$product == 'American Express Dial Authorizations' ||
			$product == 'American Express Non Dial Authorizations' ||
			$product == 'American Express Settled Items' ||
			$product == 'Discover Sales' ||
			$product == 'Discover Dial Sales' ||
			$product == 'Discover Non Dial Sales' ||
			$product == 'Discover Authorizations' ||
			$product == 'Discover Dial Authorizations' ||
			$product == 'Discover Non Dial Authorizations' ||
			$product == 'Discover Settled Items' ||
			$product == 'MasterCard Sales' ||
			$product == 'MasterCard Dial Sales' ||
			$product == 'MasterCard Non Dial Sales' ||
			$product == 'MasterCard Authorizations' ||
			$product == 'MasterCard Dial Authorizations' ||
			$product == 'MasterCard Non Dial Authorizations' ||
			$product == 'MasterCard Settled Items' ||
			$product == 'Visa Sales' ||
			$product == 'Visa Dial Sales' ||
			$product == 'Visa Non Dial Sales' ||
			$product == 'Visa Authorizations' ||
			$product == 'Visa Dial Authorizations' ||
			$product == 'Visa Non Dial Authorizations' ||
			$product == 'Visa Settled Items') {

				$amounts['user_rate'] = 0;
				$amounts['user_risk_pct'] = 0;
				if (!empty($assocMrchnt['Merchant']['partner_id'])) {
					$amounts['user_pi'] = $comPricingRec['partner_per_item_fee'];
					$partnerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				}

				if (!empty($assocMrchnt['Merchant']['referer_id'])) {
					$amounts['user_pi'] = $comPricingRec['referrer_per_item_fee'];
					$referrerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				}
				if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
					$amounts['user_pi'] = $comPricingRec['reseller_per_item_fee'];
					$resellerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				}

				$amounts['user_rate'] = 0;
				$amounts['user_risk_pct'] = 0;
				if (empty($assocMrchnt['Merchant']['partner_id'])) {
					$amounts['user_pi'] = $comPricingRec['r_per_item_fee'];

					$repGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				} else {
					$amounts['user_pi'] = $comPricingRec['partner_rep_per_item_fee'];
					$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				}
				if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
					$amounts['user_pi'] = $comPricingRec['manager_per_item_fee'];

					$managerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				}
				if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
					$amounts['user_pi'] = $comPricingRec['manager2_per_item_fee'];
					$manager2GrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
				}
		}

		if ($product == 'Debit Discount' || $product == 'EBT Discount') {
			$amounts['user_risk_pct'] = 0;
			$amounts['user_pi'] = 0;

			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$amounts['user_rate'] = $comPricingRec['partner_rate'];
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));

				$amounts['user_rate'] = $comPricingRec['partner_rep_rate'];
				$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));
			} else {
				$amounts['user_rate'] = $comPricingRec['r_rate_pct'];
				$repGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$amounts['user_rate'] = $comPricingRec['referrer_rate'];
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$amounts['user_rate'] = $comPricingRec['reseller_rate'];
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$amounts['user_rate'] = $comPricingRec['manager_rate'];
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$amounts['user_rate'] = $comPricingRec['manager2_rate'];
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["num_items" => 0, "m_item_fee" => 0]));
			}
		}

		if ($product == 'Debit Sales' || $product == 'EBT Sales') {
			$amounts['user_rate'] = 0;
			$amounts['user_risk_pct'] = 0;
			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$amounts['user_pi'] = $comPricingRec['partner_per_item_fee'];
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));

				$amounts['user_pi'] = $comPricingRec['partner_rep_per_item_fee'];
				$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
			} else {
				$amounts['user_pi'] = $comPricingRec['r_per_item_fee'];
				$repGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$amounts['user_pi'] = $comPricingRec['referrer_per_item_fee'];
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$amounts['user_pi'] = $comPricingRec['reseller_per_item_fee'];
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$amounts['user_pi'] = $comPricingRec['manager_per_item_fee'];
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$amounts['user_pi'] = $comPricingRec['manager2_per_item_fee'];
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrft(array_merge($amounts, ["volume" => 0, "m_rate" => 0]));
			}
		}

		if ($product == 'Corral License Fee') {
			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$partnerGrossProfit = $comPricingRec['m_statement_fee'];
				$partnerRepGrossProfit = $comPricingRec['m_statement_fee'];
			} else {
				$repGrossProfit = $comPricingRec['m_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$referrerGrossProfit = $comPricingRec['m_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$resellerGrossProfit = $comPricingRec['m_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$managerGrossProfit = $comPricingRec['m_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$manager2GrossProfit = $comPricingRec['m_statement_fee'];
			}
		}

		if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
			if (!empty($assocMrchnt['Merchant']['partner_id'])) {
				$partnerGrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['partner_statement_fee'];
				$partnerRepGrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['partner_rep_statement_fee'];
			} else {
				$repGrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['r_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['referer_id'])) {
				$referrerGrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['referrer_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['reseller_id'])) {
				$resellerGrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['reseller_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['sm_user_id'])) {
				$managerGrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['manager_statement_fee'];
			}
			if (!empty($assocMrchnt['Merchant']['sm2_user_id'])) {
				$manager2GrossProfit = $comPricingRec['m_statement_fee'] - $comPricingRec['manager2_statement_fee'];
			}
		}
		$comPricingRec['rep_gross_profit'] = isset($repGrossProfit)? $repGrossProfit : 0;
		$comPricingRec['partner_rep_gross_profit'] = isset($partnerRepGrossProfit)? $partnerRepGrossProfit : 0;
		$comPricingRec['manager_gross_profit'] = isset($managerGrossProfit)? $managerGrossProfit : 0;
		$comPricingRec['manager2_gross_profit'] = isset($manager2GrossProfit)? $manager2GrossProfit : 0;
		$comPricingRec['partner_gross_profit'] = isset($partnerGrossProfit)? $partnerGrossProfit : 0;
		$comPricingRec['referrer_gross_profit'] = isset($referrerGrossProfit)? $referrerGrossProfit : 0;
		$comPricingRec['reseller_gross_profit'] = isset($resellerGrossProfit)? $resellerGrossProfit : 0;
	}
}
