<?php

App::uses('AppModel', 'Model');
App::uses('ProductsServicesType', 'Model');
App::uses('RolePermissions', 'Lib');
App::uses('UserParameterType', 'Model');
App::uses('ResidualParameterType', 'Model');
/**
 * ResidualReport Model
 */
class ResidualReport extends AppModel {

	const STATUS_ACTIVE = 'A';
	const STATUS_INACTIVE = 'I';

	const USER_COST_ARCHIVE_FIELD_MAP_NAME = "UserCostArchiveFieldMap";
	const MAX_RESULTS = 25000;
	const COMMISSIONABLE_PROD = 'commissionable';
/**
 * Residual report sections
 *
 * @var string
 */
	const SECTION_RESIDUAL_REPORTS = 'residualReports';

/**
 * Find Method
 *
 * @var array
 */
	public $findMethods = [
		'index' => true,
		'residualReport' => true,
		'gpaResiduals' => true
	];

/**
 * Tells whether classes needed to process residual data have been initialized.
 *
 * var boolean
 */
 	public $initializedDependencies = false;

/**
* Labels to use on report content view
* 
* @var array
*/
	protected $_labels = [
		'merchant_mid' => [
			'text' => 'MID',
			'sortField' => 'Merchant.merchant_mid',
			'sort_options' => [
				'direction' => 'desc'
			]
		],
		'merchant_dba' => [
			'text' => 'DBA',
			'sortField' => 'Merchant.merchant_dba',
		],
		'bet_name' => [
			'text' => 'Bet',
			'sortField' => 'Bet__name',
		],
		'client_id_global' => [
			'text' => 'Client ID',
			'sortField' => 'Client__client_id_global',
		],
		'products_services_description' => [
			'text' => 'Product',
			'sortField' => 'ProductsServicesType.products_services_description',
		],
		'r_avg_ticket' => [
			'text' => 'Avg Tkt',
			'sortField' => 'r_avg_ticket',
		],
		'r_items' => [
			'text' => 'Items',
			'sortField' => 'r_items',
		],
		'r_volume' => [
			'text' => 'Volume',
			'sortField' => 'r_volume',
		],
		'm_rate_pct' => [
			//this is the merchant rate unmodified
			'text' => 'Merch Rate',
			'sortField' => 'ResidualReport__processing_rate',
		],
		'm_rate_and_bet_pct' => [
			//this is the merchant rate plus an additional bet % added (for some but not all products)
			'text' => 'Merch Rate + Bet %',
			'sortField' => 'm_rate_pct',
		],
		'm_per_item_fee' => [
			'text' => 'Merch P/I',
			'sortField' => 'm_per_item_fee',
		],
		'm_statement_fee' => [
			'text' => 'Merch Stmt/Gtwy',
			'sortField' => 'm_statement_fee',
		],
		'RepFullname' => [
			'text' => 'Rep',
			'sortField' => 'Rep__RepFullname',
		],
		'r_rate_pct' => [
			'text' => 'Rep Rate',
			'sortField' => 'r_rate_pct',
		],
		'r_per_item_fee' => [
			'text' => 'Rep P/I',
			'sortField' => 'r_per_item_fee',
		],
		'r_statement_fee' => [
			'text' => 'Rep Stmt/Gtwy',
			'sortField' => 'r_statement_fee',
		],
		'rep_gross_profit' => [
			'text' => 'Rep Gross Profit',
			'sortField' => 'rep_gross_profit',
		],
		'rep_pct_of_gross' => [
			'text' => 'Rep % of Gross',
			'sortField' => 'rep_pct_of_gross',
		],
		'r_profit_pct' => [
			'text' => 'Rep Profit %',
			'sortField' => 'r_profit_pct',
		],
		'r_profit_amount' => [
			'text' => 'Rep Profit',
			'sortField' => 'r_profit_amount',
		],
		'SalesManagerFullname' => [
			'text' => 'SM',
			'sortField' => 'SalesManager__SalesManagerFullname',
		],
		'manager_rate' => [
			'text' => 'SM Rate',
			'sortField' => null,
		],
		'manager_per_item_fee' => [
			'text' => 'SM P/I',
			'sortField' => null,
		],
		'manager_statement_fee' => [
			'text' => 'SM Stmt/Gtwy',
			'sortField' => null,
		],
		'manager_gross_profit' => [
			'text' => 'SM Gross Profit',
			'sortField' => null,
		],
		'manager_profit_pct' => [
			'text' => 'SM Profit %',
			'sortField' => 'manager_profit_pct',
		],
		'manager_pct_of_gross' => [
			'text' => 'SM % of Gross',
			'sortField' => null,
		],
		'manager_profit_amount' => [
			'text' => 'SM Profit',
			'sortField' => 'manager_profit_amount',
		],
		'SalesManager2Fullname' => [
			'text' => 'SM2',
			'sortField' => 'SalesManager2__SalesManager2Fullname',
		],
		'manager2_rate' => [
			'text' => 'SM2 Rate',
			'sortField' => null,
		],
		'manager2_per_item_fee' => [
			'text' => 'SM2 P/I',
			'sortField' => null,
		],
		'manager2_statement_fee' => [
			'text' => 'SM2 Stmt/Gtwy',
			'sortField' => null,
		],
		'manager2_gross_profit' => [
			'text' => 'SM2 Gross Profit',
			'sortField' => null,
		],
		'manager_profit_pct_secondary' => [
			'text' => 'SM2 Profit %',
			'sortField' => 'manager_profit_pct_secondary',
		],
		'manager2_pct_of_gross' => [
			'text' => 'SM2 % of Gross',
			'sortField' => null,
		],
		'manager_profit_amount_secondary' => [
			'text' => 'SM2 Profit',
			'sortField' => 'manager_profit_amount_secondary',
		],
		'PartnerFullname' => [
			'text' => 'Partner',
			'sortField' => 'Partner__PartnerFullname',
		],
		'partner_rate' => [
			'text' => 'Partner Rate',
			'sortField' => null,
		],
		'partner_per_item_fee' => [
			'text' => 'Partner P/I',
			'sortField' => null,
		],
		'partner_statement_fee' => [
			'text' => 'Partner Stmt/Gtwy',
			'sortField' => null,
		],
		'partner_gross_profit' => [
			'text' => 'Partner Gross Profit',
			'sortField' => null,
		],
		'partner_profit_pct' => [
			'text' => 'Partner Profit %',
			'sortField' => null,
		],
		'partner_pct_of_gross' => [
			'text' => 'Partner % of Gross',
			'sortField' => null,
		],
		'partner_profit_amount' => [
			'text' => 'Partner Profit',
			'sortField' => 'partner_profit_amount',
		],
		'RefFullname' => [
			'text' => 'Referrer',
			'sortField' => null,
		],
		'referrer_rate' => [
			'text' => 'Ref Rate',
			'sortField' => null,
		],
		'referrer_per_item_fee' => [
			'text' => 'Ref P/I',
			'sortField' => null,
		],
		'referrer_statement_fee' => [
			'text' => 'Ref Stmt/Gtwy',
			'sortField' => null,
		],
		'referrer_gross_profit' => [
			'text' => 'Ref Gross Profit',
			'sortField' => null,
		],
		'referer_pct_of_gross' => [
			'text' => 'Ref % of Gross',
			'sortField' => null,
		],
		'refer_profit_pct' => [
			'text' => 'Ref %',
			'sortField' => null,
		],
		'refer_profit_amount' => [
			'text' => 'Ref Profit',
			'sortField' => null,
		],
		'ResFullname' => [
			'text' => 'Reseller',
			'sortField' => null,
		],
		'reseller_rate' => [
			'text' => 'Res Rate',
			'sortField' => null,
		],
		'reseller_per_item_fee' => [
			'text' => 'Res P/I',
			'sortField' => null,
		],
		'reseller_statement_fee' => [
			'text' => 'Res Stmt/Gtwy',
			'sortField' => null,
		],
		'reseller_gross_profit' => [
			'text' => 'Res Gross Profit',
			'sortField' => null,
		],
		'reseller_pct_of_gross' => [
			'text' => 'Res % of Gross',
			'sortField' => null,
		],
		'res_profit_pct' => [
			'text' => 'Res %',
			'sortField' => null,
		],
		'res_profit_amount' => [
			'text' => 'Res Profit',
			'sortField' => null,
		],
		'address_state' => [
			'text' => 'State',
			'sortField' => 'Address__address_state',
		],
		'organization' => [
			'text' => 'Organization',
		],
		'region' => [
			'text' => 'Region',
		],
		'subregion' => [
			'text' => 'Subregion',
		],
		'location' => [
			'text' => 'Location',
		],
		'ent_name' => [
			'text' => 'Company',
			'sortField' => 'Entity__entity_name',
		],
		'last_deposit_date' => [
			'text' => 'Last Activity Date',
			'sortField' => 'last_deposit_date',
		]
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'merchant_id' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		],
		'products_services_type' => [
			'notBlank' => [
				'rule' => ['notBlank']
			]
		],
		'user_id' => [
			'numeric' => [
				'rule' => ['uuid']
			]
		]
	];

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = [
		'Search.Searchable',
		'SearchByUserId' => [
			'userRelatedModel' => 'Merchant'
		],
		'SearchByMonthYear' => [
			'yearFieldName' => 'r_year',
			'monthFieldName' => 'r_month',
		],
		'UploadedFile'
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Merchant',
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
		'Manager' => [
			'className' => 'User',
			'foreignKey' => 'manager_id',
		],
		'ManagerSecondary' => [
			'className' => 'User',
			'foreignKey' => 'manager_id_secondary',
		],
		'Partner' => [
			'className' => 'User',
			'foreignKey' => 'partner_id',
		],
		'ProductsServicesType'
	];

/**
 * Filters
 *
 * @var array
 */
	public $filterArgs = [
		'dba_mid' => [
			'type' => 'query',
			'method' => 'orConditions',
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
			'field' => '"ResidualReport"."user_id"',
			'searchByMerchantEntity' => true,
		],
		'partners' => [
			'type' => 'value',
			'field' => 'ResidualReport.partner_id'
		],
		'products' => [
			'type' => 'query',
			'method' => 'productConditions'
		],
		'date_type' => [
			'type' => 'value',
			'field' => '"TimelineEntry"."timeline_item_id"'
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
 * productConditions
 * Builds the query conditions to be used when searching by products in the reports
 * using a filter built with the options provided by the self->buildDdOptions(boolean) method
 * 
 * @param string $fiterArg data passed during search
 * @return void
 */
	public function productConditions($fiterArg = []) {
		if ($fiterArg['products'] === ProductsServicesType::ALL_LEGACY_OPTN) {
			return [
				"ResidualReport.products_services_type_id IN (SELECT id from products_services_types where is_legacy = true)"
			];
		} elseif ($fiterArg['products'] === self::COMMISSIONABLE_PROD) {
			return [
				'("ResidualParameter"."value" > 0 OR "ResidualTimeParameter"."value" > 0)'
			];
		} elseif ($this->isValidUUID($fiterArg['products'])) {
			return ["ResidualReport.products_services_type_id" => $fiterArg['products']];
		}
	}

/**
 * orConditions for merchant dba/mid search
 *
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function orConditions($data = array()) {
		$filter = $data['dba_mid'];
		$cond = array(
			'OR' => array(
				'Merchant.merchant_mid ILIKE' => '%' . $filter . '%',
				'Merchant.merchant_dba ILIKE' => '%' . $filter . '%',
		));
		return $cond;
	}

/**
 * exists 
 * Checks whether ResidualReport data exists for the given parameters
 * The Path to an uploaded CSV file containing data to generate Residuals is required and must contain the following columns.
 * MID, DBA, Item Count, Volume
 * Except for the MID columns all columns can be empty.
 * 
 * @param string $filePath The Path to a previously uploaded CSV file
 * @param integer $reportYear YYYY formated year number
 * @param integer $reportMonth an integer representation of a number
 * @param string $productId a product id
 * @return boolean
 * @throws Exception
 */
	public function dataExists($filePath, $reportYear, $reportMonth, $productId) {
		$mids = Hash::extract($this->extractCsvToArray($filePath, false), '{n}.0');			
		$mids = array_map('trim', $mids);
		$merchantIds = $this->Merchant->find('list', [
			'conditions' => [
				'OR' => [
					'Merchant.merchant_mid' => $mids,
					'Gateway1.gw1_mid' => $mids,
					'PaymentFusion.generic_product_mid' => $mids,
				]
			],
			'fields' => ['Merchant.id'],
			'joins' => [
				[
					'table' => 'gateway1s',
					'alias' => 'Gateway1',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = Gateway1.merchant_id'
					]
				],
				[
					'table' => 'payment_fusions',
					'alias' => 'PaymentFusion',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = PaymentFusion.merchant_id'
					]
				],
			],
		]);
		if (empty($merchantIds)) {
			throw new Exception ('ERROR: The Merchant MIDs in the CSV file do not exists!');
		}
		return $this->hasAny([
			'ResidualReport.r_year' => $reportYear,
			'ResidualReport.r_month' => $reportMonth,
			'ResidualReport.products_services_type_id' => $productId,
			'ResidualReport.merchant_id' => $merchantIds,
		]);
	}
/**
 * isValidCsvContent 
 * validates that the csv file meets the minimum required contents
 * CSV file should have at least the following columns as the starting columns in this order:
 * MID, DBA, Item Count, Volume
 * Except for the MID columns all columns can be empty.
 * 
 * @param array $path path to csv file to validate
 * @return boolean
 */
	public function isValidCsvDataUpload($path) {
		$fh = fopen($path, 'rb');
		//move pointer past headers row
		fgetcsv($fh, 10000);

		while ($row = fgetcsv($fh, 10000)) {
			$row = array_map('trim', $row);
			//Minimum number of coulmns and Check MID col is not empty and is numeric
			if (count($row) < 4 || !is_numeric(Hash::get($row, '0'))) {
				fclose($fh);
				unlink($path);
				return false;
			}
		}
		fclose($fh);
		return true;
	}

/**
 * _dependenciesInit
 * Initializes classes needed to process residual data
 *
 * @return void
 */
	protected function _dependenciesInit() {
		$this->Merchant = ClassRegistry::init('Merchant');
		$this->User = ClassRegistry::init('User');
		$this->UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$this->MerchantPricingArchive = ClassRegistry::init('MerchantPricingArchive');
		$this->BetTable = ClassRegistry::init('BetTable');
		$this->ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$this->AttritionRatio = ClassRegistry::init('AttritionRatio');
		$this->AxiaCalculate = ClassRegistry::init('AxiaCalculate');
		$this->initializedDependencies = true;
	}

/**
 * add method
 *
 * @param int $year report year
 * @param int $month report month
 * @param array $productsServicesType containing all data of a ProductsServicesType
 * @param string $datafile data file on disk
 * @param string $userEmail user email
 *
 * @return array $response
 * @throws Exception exception
 */
	public function add($year, $month, array $productsServicesType, $datafile, $userEmail) {
		$this->_dependenciesInit();

		$response = [
			'job_name' => 'Residual Report Admin',
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

		$productId = $productsServicesType['ProductsServicesType']['id'];
		$product = $productsServicesType['ProductsServicesType']['products_services_description'];
		$response['log']['products'] = [$product];
		$dataSource = $this->getDataSource();

		try {
			$dataArray = $this->extractCsvToArray($datafile);
			$dataSource->begin();
			// Retrieve Previously archived pricing data data for all merchants and selected $productId
			$archivedPricingData = $this->MerchantPricingArchive->find('allPricing', [
					'conditions' => [
						'MerchantPricingArchive.year' => $year,
						'MerchantPricingArchive.month' => $month,
						'MerchantPricingArchive.products_services_type_id' => $productId,
					],
					'contain' => ['Merchant']
				]
			);

			$pathStr = '{n}.Merchant.merchant_mid';
			//for the dateway 1 product run residuals using the gateway mid aka human id
			if ($product === 'Gateway 1') {
				$pathStr = '{n}.MerchantPricingArchive.gateway_mid';
			} elseif ($product === 'Payment Fusion') {
				$pathStr = '{n}.MerchantPricingArchive.generic_product_mid';
			}
			// add merchant MID to the array keys to have direct access while iterating the csv file
			$archivedPricingData = Hash::combine($archivedPricingData, $pathStr, '{n}');

			$residualReports = [];
			foreach ($dataArray as $data) {
				$data = array_map('trim', $data);//remove trailing stpaces in each element
				$rdMerchMID = $data[0]; // MID (could also be the gateway MID for the Gateway 1 product)
				$rdDBA = $data[1]; // DBA
				$rdNumItems = $this->_returnZeroIfEmpty($data[2]); // item count
				$rdVolume = $this->_returnZeroIfEmpty($data[3]); // volume
				$rdGrossProfit = 0; // profit

				//sanitize numeric data that came from the csv
				$rdNumItems = preg_replace('/,/i', '', $rdNumItems); //remove all commas
				$rdNumItems = (int)$rdNumItems; //cast to remove any decimals this should always be an integer
				$rdVolume = preg_replace('/,/i', '', $rdVolume); //remove commas

				if (!empty($data[4])) {
					$rdGrossProfit = $data[4];
				}

				$merchantResults = Hash::get($archivedPricingData, $rdMerchMID);

				if (!empty($merchantResults)) {
					$merchantId = Hash::get($merchantResults, 'Merchant.id');
					//Don't check if merchant is approved for Gateway 1 product
					if ($product !== 'Gateway 1') {
						$UwStatusMerchantXref = ClassRegistry::init('UwStatusMerchantXref');

						$merchantStatusIsApproved = $UwStatusMerchantXref->hasAny([
							'UwStatusMerchantXref.merchant_id' => $merchantId,
							"UwStatusMerchantXref.uw_status_id = (SELECT id from uw_statuses where name ='Approved')",
							"UwStatusMerchantXref.datetime IS NOT NULL",
						]);
						//Skip merchants that have not been approved
						if ($merchantStatusIsApproved === false) {
							continue;
						}
					}
				} else {
					// If merchant data is not found in archived pricing
					// the merchant did not have the product when data was archived.
					continue;
				}
				$record = $this->compileNewRecord($merchantResults, $productsServicesType, $year, $month, $rdVolume, $rdNumItems);
				$residualReports[] = $record;
			}

			if (!empty($residualReports)) {
				$this->create();
				if (!$this->saveMany($residualReports)) {
					$response['recordsAdded'] = false;
					throw new Exception("failed to save residual report data");
				} else {
					$response['recordsAdded'] = true;
				}
			} else {
				$response['recordsAdded'] = false;
			}

			$dataSource->commit();

		} catch (Exception $e) {
			$dataSource->rollback();
			$response['result'] = false;
			$response['errors'][] = $e->getMessage() . " line " . $e->getLine() . "\n" . $e->getTraceAsString();
			$response['end_time'] = date('Y-m-d H:i:s');
			return $response;
		}
		$response['end_time'] = date('Y-m-d H:i:s');
		return $response;
	}

/**
 * compileNewRecord
 * Calculates residuals and compiles result into an array ready to be saved
 * The returned array has ResidualReport field names as the keys.
 *
 * @param array $merchantResults array containg Merchant data, MerchantPricingArchive and UserCostsArchive data specific to the Product month and year
 * @param array $productsServicesType ProductsServicesType data specifically the id and product description
 * @param integer $year for which this new record corresponds
 * @param integer $month for which this new record corresponds
 * @param float $rdVolume monetary amount of residual volume or Merchant's product volume fror the product being processed
 * @param integer $rdNumItems number of items
 * @return array new record data
 */
	public function compileNewRecord($merchantResults, $productsServicesType, $year, $month, $rdVolume, $rdNumItems) {
		//Check if dependencies have been initialized before this function was invoked
		if ($this->initializedDependencies === false) {
			$this->_dependenciesInit();
		}
		$productId = $productsServicesType['ProductsServicesType']['id'];
		$product = $productsServicesType['ProductsServicesType']['products_services_description'];
		$rdDBA = $merchantResults['Merchant']['merchant_dba'];
		$merchantId = $merchantResults['Merchant']['id'];
		$mInstMonths = $this->Merchant->getInstMoCount($merchantId);

		$userCostResults = Hash::get($merchantResults, 'UserCostsArchive');
		$merchantArchivedPricing['MerchantPricingArchive'] = Hash::get($merchantResults, 'MerchantPricingArchive');

		$repCompProfile = [];
		$partnerCompProfile = [];
		$partnerRepCompProfile = [];
		$managerCompProfile = [];
		$manager2CompProfile = [];
		$resellerCompProfile = [];
		$referrerCompProfile = [];

		$repAttritionRatio = 0;
		$partnerRepAttritionRatio = 0;
		$managerAttritionRatio = 0;
		$manager2AttritionRatio = 0;

		$record = [];

		$record['r_rate_pct'] = 0;
		$record['r_per_item_fee'] = 0;
		$record['r_statement_fee'] = 0;
		$record['r_risk_pct'] = 0;
		$record['partner_rate'] = 0;
		$record['partner_per_item_fee'] = 0;
		$record['partner_statement_fee'] = 0;
		$record['partner_risk_pct'] = 0;
		$record['partner_rep_rate'] = 0;
		$record['partner_rep_per_item_fee'] = 0;
		$record['partner_rep_statement_fee'] = 0;
		$record['partner_rep_risk_pct'] = 0;
		$record['manager_rate'] = 0;
		$record['manager_per_item_fee'] = 0;
		$record['manager_statement_fee'] = 0;
		$record['manager_risk_pct'] = 0;
		$record['manager2_rate'] = 0;
		$record['manager2_per_item_fee'] = 0;
		$record['manager2_statement_fee'] = 0;
		$record['manager2_risk_pct'] = 0;
		$record['reseller_rate'] = 0;
		$record['reseller_per_item_fee'] = 0;
		$record['reseller_statement_fee'] = 0;
		$record['reseller_risk_pct'] = 0;
		$record['referrer_rate'] = 0;
		$record['referrer_per_item_fee'] = 0;
		$record['referrer_statement_fee'] = 0;
		$record['referrer_risk_pct'] = 0;
		$referrerPayType = null;
		$resellerPayType = null;
		$record['ref_p_type'] = null;
		$record['ref_p_value'] = null;
		$record['ref_p_pct'] = null;
		$record['res_p_type'] = null;
		$record['res_p_value'] = null;
		$record['res_p_pct'] = null;
		$record['products_services_type_id'] = $productId;
		$hasPartner = (boolean)$merchantResults['Merchant']['partner_id'];
		$hasSm = (boolean)$merchantResults['Merchant']['sm_user_id'];
		$hasSm2 = (boolean)$merchantResults['Merchant']['sm2_user_id'];
		$hasRef = (boolean)$merchantResults['Merchant']['referer_id'];
		$hasRes = (boolean)$merchantResults['Merchant']['reseller_id'];

		foreach ($userCostResults as $userCostResult) {
			$userCostResult['cost_pct'] = $this->percentToDec($userCostResult['cost_pct']);
			$userCostResult['risk_assmnt_pct'] = $this->percentToDec($userCostResult['risk_assmnt_pct']);

			// this is the rep
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['user_id'] && empty($merchantResults['Merchant']['partner_id'])) {
				$record['r_rate_pct'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['r_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['r_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['r_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);

				$repCompProfile = $this->UserCompensationProfile->getUcpResidualData($merchantResults['Merchant']['user_id'], $mInstMonths, null, $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);

				$repAttritionRatio = $this->_getAttritionRatio(Hash::get($repCompProfile, 'UserCompensationProfile.id'));
				$repAttritionRatio = $this->_returnZeroIfEmpty($repAttritionRatio['AttritionRatio']['percentage']);
			}

			// see if there's a partner
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['partner_id']) {
				$record['partner_rate'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['partner_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['partner_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['partner_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);

				$partnerCompProfile = $this->UserCompensationProfile->getUcpResidualData($merchantResults['Merchant']['partner_id'], $mInstMonths, null, $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);
			}

			// see if there's a partner rep
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['user_id'] && $hasPartner) {
				$record['partner_rep_rate'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['partner_rep_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['partner_rep_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['partner_rep_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);

				$partnerRepCompProfile = $this->UserCompensationProfile->getUcpResidualData($merchantResults['Merchant']['user_id'], $mInstMonths, $merchantResults['Merchant']['partner_id'], $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);
				$partnerRepAttritionRatio = $this->_getAttritionRatio(Hash::get($partnerRepCompProfile, 'UserCompensationProfile.id'));
				$partnerRepAttritionRatio = $this->_returnZeroIfEmpty($partnerRepAttritionRatio['AttritionRatio']['percentage']);
			}

			// see if there's a manager
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['sm_user_id']) {
				$record['manager_rate'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['manager_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['manager_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['manager_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);

				$managerCompProfile = $this->UserCompensationProfile->getUcpResidualData($merchantResults['Merchant']['sm_user_id'], $mInstMonths, null, $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);

				$managerAttritionRatio = $this->_getAttritionRatio(Hash::get($managerCompProfile, 'UserCompensationProfile.id'));
				$managerAttritionRatio = $this->_returnZeroIfEmpty($managerAttritionRatio['AttritionRatio']['percentage']);
			}

			// see if there's a manager2
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['sm2_user_id']) {
				$record['manager2_rate'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['manager2_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['manager2_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['manager2_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);

				$manager2CompProfile = $this->UserCompensationProfile->getUcpResidualData($merchantResults['Merchant']['sm2_user_id'], $mInstMonths, null, $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);
				$manager2AttritionRatio = $this->_getAttritionRatio(Hash::get($manager2CompProfile, 'UserCompensationProfile.id'));
				$manager2AttritionRatio = $this->_returnZeroIfEmpty($manager2AttritionRatio['AttritionRatio']['percentage']);
			}

			// see if there's a reseller
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['reseller_id']) {
				$record['reseller_rate'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['reseller_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['reseller_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['reseller_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);
				$resellerPayType = !empty($userCostResult['res_p_type'])? $userCostResult['res_p_type'] : null;
				$record['res_p_type'] = $resellerPayType;
				$record['res_p_value'] = $this->_returnZeroIfEmpty($userCostResult['res_p_value']);
				$record['res_p_pct'] = $this->_returnZeroIfEmpty($userCostResult['res_p_pct']);
				$resellerCompProfile = $this->UserCompensationProfile->getUcpResidualData($userCostResult['user_id'], $mInstMonths, null, $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);
			}

			// see if there's a referer
			if ($userCostResult['user_id'] == $merchantResults['Merchant']['referer_id']) {
				$record['referrer_rate'] = $this->_returnZeroIfEmpty($userCostResult['cost_pct']);
				$record['referrer_per_item_fee'] = $this->_returnZeroIfEmpty($userCostResult['per_item_cost']);
				$record['referrer_statement_fee'] = $this->_returnZeroIfEmpty($userCostResult['monthly_statement_cost']);
				$record['referrer_risk_pct'] = $this->_returnZeroIfEmpty($userCostResult['risk_assmnt_pct']);

				$referrerPayType = !empty($userCostResult['ref_p_type'])? $userCostResult['ref_p_type'] : null;
				$record['ref_p_type'] = $referrerPayType;
				$record['ref_p_value'] = $this->_returnZeroIfEmpty($userCostResult['ref_p_value']);
				$record['ref_p_pct'] = $this->_returnZeroIfEmpty($userCostResult['ref_p_pct']);

				$referrerCompProfile = $this->UserCompensationProfile->getUcpResidualData($userCostResult['user_id'], $mInstMonths, null, $record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id']);
			}
		}

		$record['r_network'] = $merchantArchivedPricing['MerchantPricingArchive']['bet_network_id'];

		$record['r_month'] = $month;
		$record['r_year'] = $year;

		$rdAvgTkt = 0;

		if ($rdNumItems > 0) {
			$rdAvgTkt = $rdVolume / $rdNumItems;
		}

		$record['status'] = self::STATUS_INACTIVE;
		$record['r_avg_ticket'] = $rdAvgTkt;
		$record['r_items'] = $rdNumItems;
		$record['r_volume'] = $rdVolume;

		$record['bet_table_id'] = $merchantArchivedPricing['MerchantPricingArchive']['bet_table_id'];
		$record['bet_extra_pct'] = $this->_returnZeroIfEmpty($merchantArchivedPricing['MerchantPricingArchive']['bet_extra_pct']);

		$refGpSubstracted = $this->_isRefResGpSubstacted($referrerPayType);
		$resGpSubstracted = $this->_isRefResGpSubstacted($resellerPayType);

		$merchantRate = $this->_returnZeroIfEmpty($merchantArchivedPricing['MerchantPricingArchive']['m_rate_pct']);
		$merchantRateOriginal = $this->_returnZeroIfEmpty($merchantArchivedPricing['MerchantPricingArchive']['original_m_rate_pct']);
		$merchantPerItemFee = $this->_returnZeroIfEmpty($merchantArchivedPricing['MerchantPricingArchive']['m_per_item_fee']);
		$merchantDiscountItemFee = $this->_returnZeroIfEmpty($merchantArchivedPricing['MerchantPricingArchive']['m_discount_item_fee']);

		$record['merchant_id'] = $merchantId;
		$record['m_rate_pct'] = $merchantRate;
		$record['original_m_rate_pct'] = $merchantRateOriginal;
		$record['m_per_item_fee'] = $merchantPerItemFee;
		$record['m_discount_item_fee'] = $merchantDiscountItemFee;
		$record['m_statement_fee'] = $this->_returnZeroIfEmpty($merchantArchivedPricing['MerchantPricingArchive']['m_statement_fee']);
		$record['merchant_state'] = Hash::get($merchantResults, 'Address.address_state');

		$record['user_id'] = $merchantResults['Merchant']['user_id'];
		$record['partner_id'] = $merchantResults['Merchant']['partner_id'];
		$record['manager_id'] = $merchantResults['Merchant']['sm_user_id'];
		$record['manager_id_secondary'] = $merchantResults['Merchant']['sm2_user_id'];
		$record['referer_id'] = $merchantResults['Merchant']['referer_id'];
		$record['reseller_id'] = $merchantResults['Merchant']['reseller_id'];

		$record['partner_exclude_volume'] = Hash::get($merchantResults, 'Merchant.partner_exclude_volume');

		$repPercentOfGross = $this->_returnZeroIfEmpty(Hash::get($repCompProfile, 'UserParameter.percent_of_gross_profit'));
		$partnerPercentOfGross = $this->_returnZeroIfEmpty(Hash::get($partnerCompProfile, 'UserParameter.percent_of_gross_profit'));
		$partnerRepPercentOfGross = $this->_returnZeroIfEmpty(Hash::get($partnerRepCompProfile, 'UserParameter.percent_of_gross_profit'));

		//Get associated managers' % of Gross
		$managerPercentOfGross = 0;
		$manager2PercentOfGross = 0;
		if (!empty($repCompProfile) && !empty($repCompProfile['UserCompensationProfile']['user_id'])) {
			if (!empty($record['manager_id'])) {
				$managerPercentOfGross = $this->_getRepManagerPercentOfGross($repCompProfile['UserCompensationProfile']['user_id'],
					$record['partner_id'],
					$record['manager_id'],
					$record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id'])['UserParameter']['percent_of_gross'];
			}
			if (!empty($record['manager_id_secondary'])) {
				$manager2PercentOfGross = $this->_getRepManagerPercentOfGross($repCompProfile['UserCompensationProfile']['user_id'],
					$record['partner_id'],
					$record['manager_id_secondary'],
					$record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id'])['UserParameter']['percent_of_gross'];
			}
		}

		if (!empty($partnerRepCompProfile) && !empty($partnerRepCompProfile['UserCompensationProfile']['user_id'])) {
			if (!empty($record['manager_id'])) {
				$managerPercentOfGross = $this->_getRepManagerPercentOfGross($partnerRepCompProfile['UserCompensationProfile']['user_id'],
					$record['partner_id'],
					$record['manager_id'],
					$record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id'])['UserParameter']['percent_of_gross'];
			}
			if (!empty($record['manager_id_secondary'])) {
				$manager2PercentOfGross = $this->_getRepManagerPercentOfGross($partnerRepCompProfile['UserCompensationProfile']['user_id'],
					$record['partner_id'],
					$record['manager_id_secondary'],
					$record['products_services_type_id'], $merchantResults['Merchant']['merchant_acquirer_id'])['UserParameter']['percent_of_gross'];
			}
		}

		$referrerPercentOfGross = 0;
		$resellerPercentOfGross = 0;

		if ($record['ref_p_pct'] > 0) {
			$referrerPercentOfGross = $record['ref_p_pct'];
		} else {
			$referrerPercentOfGross = $this->_returnZeroIfEmpty(Hash::get($referrerCompProfile, 'UserParameter.percent_of_gross_profit'));
		}

		if ($record['res_p_pct'] > 0) {
			$resellerPercentOfGross = $record['res_p_pct'];
		} else {
			$resellerPercentOfGross = $this->_returnZeroIfEmpty(Hash::get($resellerCompProfile, 'UserParameter.percent_of_gross_profit'));
		}
		$resPValue = $record['res_p_value'] / 100;
		$refPValue = $record['ref_p_value'] / 100;

		//$merchantRate should be in decimal form of a percent in calculations
		$merchantRate = $this->percentToDec($merchantRate);
		if ($repPercentOfGross > 0) {
			$repPercentOfGross /= 100;
		}
		if ($partnerPercentOfGross > 0) {
			$partnerPercentOfGross /= 100;
		}
		if ($partnerRepPercentOfGross > 0) {
			$partnerRepPercentOfGross /= 100;
		}
		if ($managerPercentOfGross > 0) {
			$managerPercentOfGross /= 100;
		}
		if ($manager2PercentOfGross > 0) {
			$manager2PercentOfGross /= 100;
		}
		if ($referrerPercentOfGross > 0) {
			$referrerPercentOfGross /= 100;
		}
		if ($resellerPercentOfGross > 0) {
			$resellerPercentOfGross /= 100;
		}

		$repResidualPercent = $this->_returnZeroIfEmpty(Hash::get($repCompProfile, 'ResidualParameter.residual_percent'));
		$partnerResidualPercent = $this->_returnZeroIfEmpty(Hash::get($partnerCompProfile, 'ResidualParameter.residual_percent'));
		$partnerRepResidualPercent = $this->_returnZeroIfEmpty(Hash::get($partnerRepCompProfile, 'ResidualParameter.residual_percent'));
		$referrerResidualPercent = $this->_returnZeroIfEmpty(Hash::get($referrerCompProfile, 'ResidualParameter.residual_percent'));
		$resellerResidualPercent = $this->_returnZeroIfEmpty(Hash::get($resellerCompProfile, 'ResidualParameter.residual_percent'));

		$repManagerResidualPercent = 0;
		$repManager2ResidualPercent = 0;

		if (!empty($repCompProfile) && !empty($repCompProfile['UserCompensationProfile']['user_id'])) {
			if (!empty($record['manager_id'])) {
				$repManagerResidualPercent = $this->_getRepManagerResidualPercent($repCompProfile['UserCompensationProfile']['user_id'],
					$mInstMonths, $record['manager_id'], null, $record['products_services_type_id'])['ResidualParameter']['residual_percent'];
			}
			if (!empty($record['manager_id_secondary'])) {
				$repManager2ResidualPercent = $this->_getRepManagerResidualPercent($repCompProfile['UserCompensationProfile']['user_id'],
					$mInstMonths, $record['manager_id_secondary'], null, $record['products_services_type_id'])['ResidualParameter']['residual_percent'];
			}
		}

		if (!empty($partnerRepCompProfile) && !empty($partnerRepCompProfile['UserCompensationProfile']['user_id'])) {
			if (!empty($record['manager_id'])) {
				$repManagerResidualPercent = $this->_getRepManagerResidualPercent($partnerRepCompProfile['UserCompensationProfile']['user_id'],
					$mInstMonths, $record['manager_id'], $partnerRepCompProfile['UserCompensationProfile']['partner_user_id'], $record['products_services_type_id'])['ResidualParameter']['residual_percent'];
			}
			if (!empty($record['manager_id_secondary'])) {
				$repManager2ResidualPercent = $this->_getRepManagerResidualPercent($partnerRepCompProfile['UserCompensationProfile']['user_id'],
					$mInstMonths, $record['manager_id_secondary'], $partnerRepCompProfile['UserCompensationProfile']['partner_user_id'], $record['products_services_type_id'])['ResidualParameter']['residual_percent'];
			}
		}

		if ($repResidualPercent > 0) {
			$repResidualPercent /= 100;
		}
		if ($partnerResidualPercent > 0) {
			$partnerResidualPercent /= 100;
		}
		if ($partnerRepResidualPercent > 0) {
			$partnerRepResidualPercent /= 100;
		}
		if ($referrerResidualPercent > 0) {
			$referrerResidualPercent /= 100;
		}
		if ($resellerResidualPercent > 0) {
			$resellerResidualPercent /= 100;
		}
		if ($repManagerResidualPercent > 0) {
			$repManagerResidualPercent /= 100;
		}
		if ($repManager2ResidualPercent > 0) {
			$repManager2ResidualPercent /= 100;
		}

		if ($repAttritionRatio > 0) {
			$repAttritionRatio /= 100;
		}
		if ($partnerRepAttritionRatio > 0) {
			$partnerRepAttritionRatio /= 100;
		}
		if ($managerAttritionRatio > 0) {
			$managerAttritionRatio /= 100;
		}
		if ($manager2AttritionRatio > 0) {
			$manager2AttritionRatio /= 100;
		}

		$repGrossProfit = 0;
		$rProfitAmount = 0;

		$partnerGrossProfit = 0;
		$partnerProfitAmount = 0;

		$partnerRepGrossProfit = 0;
		$partnerRepProfitAmount = 0;

		$managerGrossProfit = 0;
		$managerProfitAmount = 0;

		$manager2GrossProfit = 0;
		$managerProfitAmountSecondary = 0;

		$referrerGrossProfit = 0;
		$referProfitAmount = 0;

		$resellerGrossProfit = 0;
		$resProfitAmount = 0;

		if ($product == 'American Express Discount' ||
			$product == 'American Express Discount (Converted Merchants)' ||
			$product == 'Discover Discount' ||
			$product == 'MasterCard Discount' ||
			$product == 'Visa Discount') {

			if ($hasPartner) {
				$partnerGrossProfit = ($rdVolume * ($merchantRate - ($record['partner_rate'] + $record['partner_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);
				$partnerProfitAmount = $partnerGrossProfit * $partnerPercentOfGross * $partnerResidualPercent;
			}
			if ($hasRef) {
				$referrerGrossProfit = ($rdVolume * ($merchantRate - ($record['referrer_rate'] + $record['referrer_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);
				if (preg_match('/percentage/', $referrerPayType)) {
					$referProfitAmount = ($referrerGrossProfit * $referrerPercentOfGross * $refPValue);
				} elseif (preg_match('/points/', $referrerPayType)) {
					$referProfitAmount = ($rdVolume * $referrerPercentOfGross * $refPValue);
				}
			}

			if ($hasRes) {
				$resellerGrossProfit = ($rdVolume * ($merchantRate - ($record['reseller_rate'] + $record['reseller_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);

				if (preg_match('/percentage/', $resellerPayType)) {
					$resProfitAmount = ($resellerGrossProfit * $resellerPercentOfGross * $resPValue);
				} elseif (preg_match('/points/', $resellerPayType)) {
					$resProfitAmount = ($rdVolume * $resellerPercentOfGross * $resPValue);
				}
			}

			if (!$hasPartner) {
				$repGrossProfit = ($rdVolume * ($merchantRate - ($record['r_rate_pct'] + $record['r_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$rProfitAmount = ($repGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $repPercentOfGross * ($repResidualPercent - $repAttritionRatio);
			} else {
				$partnerRepGrossProfit = ($rdVolume * ($merchantRate - ($record['partner_rep_rate'] + $record['partner_rep_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$partnerRepProfitAmount = ($partnerRepGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $partnerRepPercentOfGross * ($partnerRepResidualPercent - $partnerRepAttritionRatio);
			}
			if ($hasSm) {
				$managerGrossProfit = ($rdVolume * ($merchantRate - ($record['manager_rate'] + $record['manager_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$managerProfitAmount = ($managerGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $managerPercentOfGross * ($repManagerResidualPercent - $managerAttritionRatio);
			}
			if ($hasSm2) {
				$manager2GrossProfit = ($rdVolume * ($merchantRate - ($record['manager2_rate'] + $record['manager2_risk_pct']))) + ($rdNumItems * $merchantDiscountItemFee);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$managerProfitAmountSecondary = ($manager2GrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $manager2PercentOfGross * ($repManager2ResidualPercent - $manager2AttritionRatio);
			}
		}

		if ($product == 'ACH') {
			$amounts = [
				"num_items" => $rdNumItems,
				"m_item_fee" => $record['m_per_item_fee'],
				"volume" => $rdVolume,
				'm_rate' => $merchantRate * 100, //the function uGrossPrftType4() will convert this into a decimal form again
				'm_monthly' => $record['m_statement_fee'],
			];
			if ($hasPartner) {
				$amounts['user_pi'] = $record['partner_per_item_fee'];
				$amounts['u_monthly'] = $record['partner_statement_fee'];
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				$partnerProfitAmount = $partnerGrossProfit * $partnerPercentOfGross * $partnerResidualPercent;
			}
			if ($hasRef) {
				$amounts['user_pi'] = $record['referrer_per_item_fee'];
				$amounts['u_monthly'] = $record['referrer_statement_fee'];
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				if (preg_match('/percentage/', $referrerPayType)) {
					$referProfitAmount = ($referrerGrossProfit * $referrerPercentOfGross * $referrerResidualPercent);
				} elseif (preg_match('/points/', $referrerPayType)) {
					$referProfitAmount = ($rdVolume * $referrerPercentOfGross * $refPValue);
				}
			}

			if ($hasRes) {
				$amounts['user_pi'] = $record['reseller_per_item_fee'];
				$amounts['u_monthly'] = $record['reseller_statement_fee'];
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				if (preg_match('/percentage/', $resellerPayType)) {
					$resProfitAmount = ($resellerGrossProfit * $resellerPercentOfGross * $resellerResidualPercent);
				} elseif (preg_match('/points/', $resellerPayType)) {
					$resProfitAmount = ($rdVolume * $resellerPercentOfGross * $resPValue);
				}
			}

			if (!$hasPartner) {
				$amounts['user_pi'] = $record['r_per_item_fee'];
				$amounts['u_monthly'] = $record['r_statement_fee'];
				$repGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$rProfitAmount = ($repGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $repPercentOfGross * ($repResidualPercent - $repAttritionRatio);
			} else {
				$amounts['user_pi'] = $record['partner_rep_per_item_fee'];
				$amounts['u_monthly'] = $record['partner_rep_statement_fee'];
				$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$partnerRepProfitAmount = ($partnerRepGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $partnerRepPercentOfGross * ($partnerRepResidualPercent - $partnerRepAttritionRatio);
			}
			if ($hasSm) {
				$amounts['user_pi'] = $record['manager_per_item_fee'];
				$amounts['u_monthly'] = $record['manager_statement_fee'];
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$managerProfitAmount = ($managerGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $managerPercentOfGross * ($repManagerResidualPercent - $managerAttritionRatio);
			}
			if ($hasSm2) {
				$amounts['user_pi'] = $record['manager2_per_item_fee'];
				$amounts['u_monthly'] = $record['manager2_statement_fee'];
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrftType4($amounts);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$managerProfitAmountSecondary = ($manager2GrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $manager2PercentOfGross * ($repManager2ResidualPercent - $manager2AttritionRatio);
			}
		}

		if ($product == 'Gateway 1') {
			$amounts = [
				'volume' => $rdVolume,
				'm_rate' => $merchantRate * 100, //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				'num_items' => $rdNumItems,
				'm_item_fee' => $record['m_per_item_fee']
			];
			if ($hasPartner) {
				$amounts['user_rate'] = $record['partner_rate'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['partner_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['partner_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$partnerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['partner_statement_fee']);
				$partnerProfitAmount = $partnerGrossProfit * $partnerPercentOfGross * $partnerResidualPercent;
			}
			if ($hasRef) {
				$amounts['user_rate'] = $record['referrer_rate'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['referrer_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['referrer_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$referrerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['referrer_statement_fee']);
				$referProfitAmount = ($referrerGrossProfit * $referrerPercentOfGross * $referrerResidualPercent);
			}

			if ($hasRes) {
				$amounts['user_rate'] = $record['reseller_rate'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['reseller_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['reseller_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$resellerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['reseller_statement_fee']);
				$resProfitAmount = ($resellerGrossProfit * $resellerPercentOfGross * $resellerResidualPercent);
			}

			if (!$hasPartner) {
				$amounts['user_rate'] = $record['r_rate_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['r_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['r_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$repGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['r_statement_fee']);
				$rProfitAmount = ($repGrossProfit * $repPercentOfGross * $repResidualPercent);
			} else {
				$amounts['user_rate'] = $record['partner_rep_rate'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['partner_rep_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['partner_rep_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$partnerRepGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['partner_rep_statement_fee']);
				$partnerRepProfitAmount = ($partnerRepGrossProfit * $partnerRepPercentOfGross * $partnerRepResidualPercent);
			}
			if ($hasSm) {
				$amounts['user_rate'] = $record['manager_rate'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['manager_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['manager_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$managerGrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['manager_statement_fee']);
				$managerProfitAmount = ($managerGrossProfit * $managerPercentOfGross * $repManagerResidualPercent);
			}
			if ($hasSm2) {
				$amounts['user_rate'] = $record['manager2_rate'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_risk_pct'] = $record['manager2_risk_pct'] * 100; //convert back to percentage, AxiaCalculate->uGrossPrft method converts this to decimal also
				$amounts['user_pi'] = $record['manager2_per_item_fee'];
				//Gross profit has a statment fee substraction term at the end for this product
				$manager2GrossProfit = $this->AxiaCalculate->uGrossPrft($amounts) + ($record['m_statement_fee'] - $record['manager2_statement_fee']);
				$managerProfitAmountSecondary = ($manager2GrossProfit * $manager2PercentOfGross * $repManager2ResidualPercent);
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
			$product == 'Visa Settled Items' ||
			$product == 'Credit Monthly' ||
			$product == 'Debit Monthly' ||
			$product == 'EBT Monthly'
			) {

				if ($hasPartner) {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$partnerGrossProfit = $record['m_statement_fee'] - $record['partner_statement_fee'];
					} else {
						$partnerGrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['partner_per_item_fee']));
					}
					$partnerProfitAmount = $partnerGrossProfit * $partnerPercentOfGross * $partnerResidualPercent;
				}
				if ($hasRef) {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$referrerGrossProfit = $record['m_statement_fee'] - $record['referrer_statement_fee'];
					} else {
						$referrerGrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['referrer_per_item_fee']));
					}
					$referProfitAmount = $referrerGrossProfit * $referrerPercentOfGross * $referrerResidualPercent;
				}
				if ($hasRes) {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$resellerGrossProfit = $record['m_statement_fee'] - $record['reseller_statement_fee'];
					} else {
						$resellerGrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['reseller_per_item_fee']));
					}
					$resProfitAmount = $resellerGrossProfit * $resellerPercentOfGross * $resellerResidualPercent;
				}
				if (!$hasPartner) {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$repGrossProfit = $record['m_statement_fee'] - $record['r_statement_fee'];
					} else {
						$repGrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['r_per_item_fee']));
					}
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$rProfitAmount = ($repGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $repPercentOfGross * ($repResidualPercent - $repAttritionRatio);
				} else {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$partnerRepGrossProfit = $record['m_statement_fee'] - $record['partner_rep_statement_fee'];
					} else {
						$partnerRepGrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['partner_rep_per_item_fee']));
					}
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$partnerRepProfitAmount = ($partnerRepGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $partnerRepPercentOfGross * ($partnerRepResidualPercent - $partnerRepAttritionRatio);
				}
				if ($hasSm) {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$managerGrossProfit = $record['m_statement_fee'] - $record['manager_statement_fee'];
					} else {
						$managerGrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['manager_per_item_fee']));
					}
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$managerProfitAmount = ($managerGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $managerPercentOfGross * ($repManagerResidualPercent - $managerAttritionRatio);
				}

				if ($hasSm2) {
					if ($product == 'Credit Monthly' || $product == 'Debit Monthly' || $product == 'EBT Monthly') {
						$manager2GrossProfit = $record['m_statement_fee'] - $record['manager2_statement_fee'];
					} else {
						$manager2GrossProfit = ($rdNumItems * ($merchantPerItemFee - $record['manager2_per_item_fee']));
					}
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$managerProfitAmountSecondary = ($manager2GrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $manager2PercentOfGross * ($repManager2ResidualPercent - $manager2AttritionRatio);
				}
		}

		if ($product == 'Debit Discount' ||
			$product == 'EBT Discount') {

			if ($hasPartner) {
				$partnerGrossProfit = $rdVolume * ($merchantRate - $record['partner_rate']);
				$partnerProfitAmount = $partnerGrossProfit * $partnerPercentOfGross * $partnerResidualPercent;
			}
			if ($hasRef) {
				$referrerGrossProfit = $rdVolume * ($merchantRate - $record['referrer_rate']);
				if (preg_match('/percentage/', $referrerPayType)) {
					$referProfitAmount = ($referrerGrossProfit * $referrerPercentOfGross * $refPValue);
				} elseif (preg_match('/points/', $referrerPayType)) {
					$referProfitAmount = ($rdVolume * $referrerPercentOfGross * $refPValue);
				}
			}
			if ($hasRes) {
				$resellerGrossProfit = $rdVolume * ($merchantRate - $record['reseller_rate']);

				if (preg_match('/percentage/', $resellerPayType)) {
					$resProfitAmount = ($resellerGrossProfit * $resellerPercentOfGross * $resPValue);
				} elseif (preg_match('/points/', $resellerPayType)) {
					$resProfitAmount = ($rdVolume * $resellerPercentOfGross * $resPValue);
				}
			}

			if (!$hasPartner) {
				$repGrossProfit = $rdVolume * ($merchantRate - $record['r_rate_pct']);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$rProfitAmount = ($repGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $repPercentOfGross * $repResidualPercent;
			} else {
				$partnerRepGrossProfit = $rdVolume * ($merchantRate - $record['partner_rep_rate']);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$partnerRepProfitAmount = ($partnerRepGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $partnerRepPercentOfGross * $partnerRepResidualPercent;
			}
			if ($hasSm) {
				$managerGrossProfit = $rdVolume * ($merchantRate - $record['manager_rate']);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$managerProfitAmount = ($managerGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $managerPercentOfGross * $repManagerResidualPercent;
			}

			if ($hasSm2) {
				$manager2GrossProfit = $rdVolume * ($merchantRate - $record['manager2_rate']);
				//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
				//Multiplication takes care of whether an amount should be substractied.
				$managerProfitAmountSecondary = ($manager2GrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $manager2PercentOfGross * $repManager2ResidualPercent;
			}
		}

		if ($product == 'Debit Sales' ||
			$product == 'EBT Sales' ||
			$product == 'Corral License Fee' ||
			$product == 'Payment Fusion'
			) {

			$isCorral = ($product == 'Corral License Fee');
			$isPayFusion = ($product == 'Payment Fusion');
			if ($isPayFusion) {
				$PaymentFusion = ClassRegistry::init('PaymentFusion');
				$PaymentFusionRepCost = ClassRegistry::init('PaymentFusionRepCost');
				$pfMerchData = $PaymentFusion->findByMerchantId($record['merchant_id']);
			}

			if ($hasPartner) {
				if ($isCorral) {
					$partnerGrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['partner_id']);
					$partnerGrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'partner_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$partnerGrossProfit = $rdNumItems * ($merchantPerItemFee - $record['partner_per_item_fee']);
				}

				$partnerProfitAmount = $partnerGrossProfit * $partnerPercentOfGross * $partnerResidualPercent;
			}
			if ($hasRef) {
				if ($isCorral) {
					$referrerGrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['referer_id']);
					$referrerGrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'referrer_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$referrerGrossProfit = $rdNumItems * ($merchantPerItemFee - $record['referrer_per_item_fee']);
				}

				$referProfitAmount = $referrerGrossProfit * $referrerPercentOfGross * $referrerResidualPercent;
			}
			if ($hasRes) {
				if ($isCorral) {
					$resellerGrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['referer_id']);
					$resellerGrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'reseller_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$resellerGrossProfit = $rdNumItems * ($merchantPerItemFee - $record['reseller_per_item_fee']);
				}

				$resProfitAmount = $resellerGrossProfit * $resellerPercentOfGross * $resellerResidualPercent;
			}
			if (!$hasPartner) {
				if ($isCorral) {
					$repGrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['user_id']);
					$repGrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'r_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$repGrossProfit = $rdNumItems * ($merchantPerItemFee - $record['r_per_item_fee']);
				}

				if ($isPayFusion) {
					$rProfitAmount = $repGrossProfit * $repPercentOfGross * $repResidualPercent;
				} else {
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$rProfitAmount = ($repGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $repPercentOfGross * ($repResidualPercent);
				}
			} else {
				if ($isCorral) {
					$partnerRepGrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['user_id'], $record['partner_id']);
					$partnerRepGrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'partner_rep_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$partnerRepGrossProfit = $rdNumItems * ($merchantPerItemFee - $record['partner_rep_per_item_fee']);
				}
				if ($isPayFusion) {
					$partnerRepProfitAmount = $partnerRepGrossProfit * $partnerRepPercentOfGross * $partnerRepResidualPercent;
				} else {
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$partnerRepProfitAmount = ($partnerRepGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $partnerRepPercentOfGross * ($partnerRepResidualPercent);
				}
			}
			if ($hasSm) {
				if ($isCorral) {
					$managerGrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['manager_id'], null, true);
					$managerGrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'manager_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$managerGrossProfit = $rdNumItems * ($merchantPerItemFee - $record['manager_per_item_fee']);
				}

				if ($isPayFusion) {
					$managerProfitAmount = $managerGrossProfit * $managerPercentOfGross * $repManagerResidualPercent;
				} else {
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$managerProfitAmount = ($managerGrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $managerPercentOfGross * ($repManagerResidualPercent);
				}
			}
			if ($hasSm2) {
				if ($isCorral) {
					$manager2GrossProfit = $record['m_statement_fee'];
				} elseif ($isPayFusion) {
					$pfUserCost = $PaymentFusionRepCost->getCosts($record['manager_id_secondary'], null, false, true);
					$manager2GrossProfit = $this->AxiaCalculate->multSumEqType2($record, 'r_items', 'm_per_item_fee', 'manager2_per_item_fee') + Hash::get($pfMerchData, 'PaymentFusion.account_fee') + $this->AxiaCalculate->pmntFusionDevicesMonthly($pfMerchData, $pfUserCost);
				} else {
					$manager2GrossProfit = $rdNumItems * ($merchantPerItemFee - $record['manager2_per_item_fee']);
				}
				if ($isPayFusion) {
					$managerProfitAmountSecondary = $manager2GrossProfit * $manager2PercentOfGross * $repManager2ResidualPercent;
				} else {
					//The $refGpSubstracted and $resGpSubstracted are booleans which determine whether to substract referer or reseller GP.
					//Multiplication takes care of whether an amount should be substractied.
					$managerProfitAmountSecondary = ($manager2GrossProfit - $partnerProfitAmount - ($referProfitAmount * (int)$refGpSubstracted) - ($resProfitAmount * (int)$resGpSubstracted)) * $manager2PercentOfGross * ($repManager2ResidualPercent);
				}
			}
		}

		$record['rep_gross_profit'] = $repGrossProfit;
		$record['r_profit_pct'] = $repResidualPercent * 100;
		$record['r_profit_amount'] = CakeNumber::precision($rProfitAmount, 4);

		$record['partner_gross_profit'] = $partnerGrossProfit;
		$record['partner_profit_pct'] = $partnerResidualPercent * 100;
		$record['partner_profit_amount'] = CakeNumber::precision($partnerProfitAmount, 4);

		$record['partner_rep_gross_profit'] = $partnerRepGrossProfit;
		$record['partner_rep_profit_pct'] = $partnerRepResidualPercent * 100;
		$record['partner_rep_profit_amount'] = $partnerRepProfitAmount;

		$record['manager_gross_profit'] = $managerGrossProfit;
		$record['manager_profit_pct'] = $repManagerResidualPercent * 100;
		$record['manager_profit_amount'] = CakeNumber::precision($managerProfitAmount, 4);

		$record['manager2_gross_profit'] = $manager2GrossProfit;
		$record['manager_profit_pct_secondary'] = $repManager2ResidualPercent * 100;
		$record['manager_profit_amount_secondary'] = CakeNumber::precision($managerProfitAmountSecondary, 4);

		$record['referrer_gross_profit'] = $referrerGrossProfit;
		$record['refer_profit_pct'] = $referrerResidualPercent * 100;
		$record['refer_profit_amount'] = CakeNumber::precision($referProfitAmount, 4);

		$record['reseller_gross_profit'] = $resellerGrossProfit;
		$record['res_profit_pct'] = $resellerResidualPercent * 100;
		$record['res_profit_amount'] = CakeNumber::precision($resProfitAmount, 4);

		$record['total_profit'] = bcadd($rProfitAmount, $partnerRepProfitAmount, 4);
		//Set Users % of Gross
		$record['rep_pct_of_gross'] = $repPercentOfGross * 100;
		$record['partner_rep_pct_of_gross'] = $partnerRepPercentOfGross * 100;
		$record['manager_pct_of_gross'] = $managerPercentOfGross * 100;
		$record['manager2_pct_of_gross'] = $manager2PercentOfGross * 100;
		$record['partner_pct_of_gross'] = $partnerPercentOfGross * 100;
		$record['referer_pct_of_gross'] = $referrerPercentOfGross * 100;
		$record['reseller_pct_of_gross'] = $resellerPercentOfGross * 100;

		//Save as percent not decimal form
		$record['r_rate_pct'] = $record['r_rate_pct'] * 100;
		$record['r_risk_pct'] = $record['r_risk_pct'] * 100;
		$record['partner_rate'] = $record['partner_rate'] * 100;
		$record['partner_risk_pct'] = $record['partner_risk_pct'] * 100;
		$record['partner_rep_rate'] = $record['partner_rep_rate'] * 100;
		$record['partner_rep_risk_pct'] = $record['partner_rep_risk_pct'] * 100;
		$record['manager_rate'] = $record['manager_rate'] * 100;
		$record['manager_risk_pct'] = $record['manager_risk_pct'] * 100;
		$record['manager2_rate'] = $record['manager2_rate'] * 100;
		$record['manager2_risk_pct'] = $record['manager2_risk_pct'] * 100;
		$record['reseller_rate'] = $record['reseller_rate'] * 100;
		$record['reseller_risk_pct'] = $record['reseller_risk_pct'] * 100;
		$record['referrer_rate'] = $record['referrer_rate'] * 100;
		$record['referrer_risk_pct'] = $record['referrer_risk_pct'] * 100;
		
		return $record;
	}

/**
 * _isRefResGpSubstacted
 * This function's return value is intended to determine whether the referer or reseller profit should be substracted from other users's GP in their profit amount calculations
 *
 * @param string $profitType The type of profit specified for the Merchant referrers or resellers.
 * @return array $results
 */
	protected function _isRefResGpSubstacted($profitType) {
		return $profitType === Merchant::PCT_MINUS_GP || $profitType === Merchant:: POINTS_MINUS_GP;
	}

/**
 * _getRepManagerResidualPercent
 *
 * @param string $userId user_id
 * @param int $mInstMonths number of months since merchant went live
 * @param string $managerId The manager user id 
 * @param string $partnerId The partner id associated with the Partner-Rep comp profile (if any)
 * @param string $productsServicesTypeId products_services_type_id
 *
 * @return array $results
 */
	protected function _getRepManagerResidualPercent($userId, $mInstMonths, $managerId = null, $partnerId = null, $productsServicesTypeId = null) {
		$conditions = [
			'UserCompensationProfile.user_id' => $userId,
			'UserCompensationProfile.is_default' => empty($partnerId)
		];
		if (!empty($partnerId)) {
			$conditions['UserCompensationProfile.partner_user_id'] = $partnerId;
		}

		$results = $this->UserCompensationProfile->find('first', [
			'fields' => [
				'UserCompensationProfile.*',
				'ResidualParameter.value',
				'ResidualTimeParameter.value'
			],
			'joins' => [
				[
					'alias' => 'ResidualParameter',
					'table' => 'residual_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'AND' => [
							['"UserCompensationProfile"."id" = "ResidualParameter"."user_compensation_profile_id"'],
							['ResidualParameter.products_services_type_id' => $productsServicesTypeId],
							['ResidualParameter.associated_user_id' => $managerId],
							['ResidualParameter.tier' => 0],
							["ResidualParameter.residual_parameter_type_id = (select id from residual_parameter_types where name = 'Mgr %')"]
						]
					]
				],
				[
					'alias' => 'ResidualTimeFactor',
					'table' => 'residual_time_factors',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualTimeFactor"."user_compensation_profile_id"',
					]
				],
				[
					'alias' => 'ResidualTimeParameter',
					'table' => 'residual_time_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualTimeParameter"."user_compensation_profile_id"',
						'"ResidualTimeParameter"."tier" = ((CASE WHEN "ResidualTimeFactor"."tier1_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier1_end_month" >= ' . $mInstMonths . ' THEN 1 WHEN "ResidualTimeFactor"."tier2_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier2_end_month" >= ' . $mInstMonths . ' THEN 2 WHEN "ResidualTimeFactor"."tier3_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier3_end_month" >= ' . $mInstMonths . ' THEN 3 ELSE 4 END))',
						'ResidualTimeParameter.products_services_type_id' => $productsServicesTypeId,
						'ResidualTimeParameter.associated_user_id' => $managerId,
						"ResidualTimeParameter.residual_parameter_type_id = (select id from residual_parameter_types where name = 'Mgr %')"
					]
				]
			],
			'conditions' => $conditions
		]);

		$mgrPercent['ResidualParameter']['residual_percent'] = 0;
		if ($results['UserCompensationProfile']['is_profile_option_1']) {
			$mgrPercent['ResidualParameter']['residual_percent'] = $results['ResidualParameter']['value'];
		} elseif ($results['UserCompensationProfile']['is_profile_option_2']) {
			$mgrPercent['ResidualParameter']['residual_percent'] = $results['ResidualTimeParameter']['value'];
		}

		return $mgrPercent;
	}

/**
 * _getRepManagerPercentOfGross
 *
 * @param string $userId user_id
 * @param string $partnerId the id of the partner associated with the partner rep compensation profile (if any)
 * @param string $managerId manager_id
 * @param string $productsServicesTypeId products_services_type_id
 * @param string $merchantAquirerId a UserParameter.merchant_acquierer_id
 * @return array $results
 */
	protected function _getRepManagerPercentOfGross($userId, $partnerId = null, $managerId = null, $productsServicesTypeId = null, $merchantAquirerId = null) {
		$conditions = [
			'UserCompensationProfile.user_id' => $userId,
		];

		if (!empty($partnerId)) {
			$conditions['UserCompensationProfile.partner_user_id'] = $partnerId;
		} else {
			$conditions['UserCompensationProfile.is_default'] = true;
		}

		$results = $this->UserCompensationProfile->find('first', [
			'fields' => [
				'UserParameter.value'
			],
			'joins' => [
				[
					'alias' => 'UserParameter',
					'table' => 'user_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'AND' => [
							['"UserCompensationProfile"."id" = "UserParameter"."user_compensation_profile_id"'],
							['UserParameter.products_services_type_id' => $productsServicesTypeId],
							['UserParameter.associated_user_id' => $managerId],
							['UserParameter.merchant_acquirer_id' => $merchantAquirerId],
							["UserParameter.user_parameter_type_id = (select id from user_parameter_types where name = '% of Gross Profit')"]
						]
					]
				]
			],
			'conditions' => $conditions
		]);

		$results['UserParameter']['percent_of_gross'] = $results['UserParameter']['value'];
		unset($results['UserParameter']['value']);

		return $results;
	}

/**
 * _getAttritionRatio
 *
 * @param string $userCompensationProfileId user_compensation_profile_id
 *
 * @return array $results
 */
	protected function _getAttritionRatio($userCompensationProfileId) {
		$results = $this->AttritionRatio->find('first', [
			'conditions' => [
				'user_compensation_profile_id' => $userCompensationProfileId
			]
		]);

		if (empty($results)) {
			$results['AttritionRatio']['percentage'] = 0;
		}

		return $results;
	}

/**
 * _returnZeroIfEmpty
 *
 * @param int/float $val value
 *
 * @return int/float 0 or $val
 */
	protected function _returnZeroIfEmpty($val) {
		if (empty($val)) {
			return 0;
		}
		return $val;
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
				'ResidualReport.r_year',
				'ResidualReport.r_month',
				'ResidualReport.status',
				'ProductsServicesType.id',
				'ProductsServicesType.products_services_description'
			];
			$query['recursive'] = -1;
			$query['joins'] = [
				[
					'alias' => 'ProductsServicesType',
					'table' => 'products_services_types',
					'type' => 'LEFT',
					'conditions' => [
						'ResidualReport.products_services_type_id = ProductsServicesType.id',
					]
				]
			];
			$query['group'] = [
				'ResidualReport.r_year',
				'ResidualReport.r_month',
				'ResidualReport.status',
				'ProductsServicesType.id',
				'ProductsServicesType.products_services_description'
			];

			// Set initial sort always sorting first by month
			if (empty($query['order'])) {
				$query['order'][] = 'ResidualReport.r_month ASC';
				$query['order'][] = 'ProductsServicesType.products_services_description ASC';
			} else {
				array_unshift($query['order'], 'ResidualReport.r_month ASC');
			}

			return $query;
		}
		return $results;
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
			'subject' => 'Residual Admin Notice',
			'emailBody' => $response
		]);

		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
	}

/**
 * Calculate total residuals based on filtering options
 *
 * @param string $userRole User role on the residual report operation
 * @param array $filterParams Filter parameters
 * @throws InvalidArgumentException
 *
 * @return string with the decimal numer
 */
	public function getTotalResiduals($userRole, $filterParams = []) {
		$amountField = null;
		$userField = null;
		$addMerchantFilter = false;

		switch ($userRole) {
			case User::ROLE_REP:
			case User::ROLE_PARTNER_REP:
				$amountField = 'total_profit';
				$userField = 'user_id';
				$addMerchantFilter = true;
				break;

			case User::ROLE_SM:
				$amountField = 'manager_profit_amount';
				$userField = 'manager_id';
				break;

			case User::ROLE_SM2:
				$amountField = 'manager_profit_amount_secondary';
				$userField = 'manager_id_secondary';
				break;

			case User::ROLE_PARTNER:
				$amountField = 'partner_profit_amount';
				$userField = 'partner_id';
				break;

			default:
				throw new InvalidArgumentException(__('Invalid user role'));
		}

		$options = [
			'fields' => ["SUM({$this->alias}.{$amountField}) as total_residuals"],
			'contain' => [],
			'joins' => [],
			'conditions' => [
				"{$this->alias}.status" => RolePermissions::getAllowedReportStatuses($this->_getCurrentUser('id'))
			],
		];

		// Conditions for filter parameters
		if ($addMerchantFilter && !empty($filterParams['merchant_dba'])) {
			$options['joins'][] = [
				'table' => 'merchants',
				'alias' => 'Merchant',
				'type' => 'INNER',
				'conditions' => [
					"Merchant.id = {$this->alias}.merchant_id",
				],
			];
			$options['conditions']['Merchant.merchant_dba'] = $filterParams['merchant_dba'];
			if (!empty(Hash::get($filterParams, 'organization_id'))) {
				$options['conditions']['Merchant.organization_id'] = Hash::get($filterParams, 'organization_id');
			}
			if (!empty(Hash::get($filterParams, 'region_id'))) {
				$options['conditions']['Merchant.region_id'] = Hash::get($filterParams, 'region_id');
			}
			if (!empty(Hash::get($filterParams, 'subregion_id'))) {
				$options['conditions']['Merchant.subregion_id'] = Hash::get($filterParams, 'subregion_id');
			}
			if (!empty(Hash::get($filterParams, 'location_description'))) {
				$options['joins'][] = [
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'INNER',
					'conditions' => [
						"Address.merchant_id = Merchant.id",
						'Address.address_type_id' => AddressType::BUSINESS_ADDRESS,
						'Address.address_street' => Hash::get($filterParams, 'location_description')
					]
				];
			}
		}
		if (!empty($filterParams['user_id'])) {
			$options['conditions']["{$this->alias}.{$userField}"] = $this->User->getRelatedUsersByComplexId($filterParams['user_id']);
		}
		//We want the previous month's residual totals
		$this->__setPrevMonth($filterParams);

		if (!empty($filterParams['from_date'])) {
			$options['conditions'][] = $this->dateStartConditions($filterParams);
		}
		if (!empty($filterParams['end_date'])) {
			$options['conditions'][] = $this->dateEndConditions($filterParams);
		}
		$result = $this->find('all', $options);

		return Hash::get($result, '0.0.total_residuals');
	}

/**
 * __setPrevMonth
 * Modifies the $filterParams from_date and end_date by substracting one month
 *
 * @param array &$filterParams a reference to the params submitted for a search in which the 'month' and 'year' elements may exist.
 * @return void
 * @access private
 */
	private function __setPrevMonth(&$filterParams) {
		if (!empty($filterParams['from_date'])) {
			$fMonth = $filterParams['from_date']['month'] - 1;
			$fYear = $filterParams['from_date']['year'];
			if ($fMonth === 0) {
				$fMonth = 12;
				$fYear--;
			}
			$filterParams['from_date']['month'] = $fMonth;
			$filterParams['from_date']['year'] = $fYear;
		}

		if (!empty($filterParams['end_date'])) {
			$eMonth = $filterParams['end_date']['month'] - 1;
			$eYear = $filterParams['end_date']['year'];
			if ($eMonth === 0) {
				$eMonth = 12;
				$eYear--;
			}
			$filterParams['end_date']['month'] = $eMonth;
			$filterParams['end_date']['year'] = $eYear;
		}
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
				'month' => $now->format('m'),
			],
			'end_date' => [
				'year' => $now->format('Y'),
				'month' => $now->format('m'),
			],
			'user_id' => $this->User->getDefaultUserSearch()
		];
	}

/**
 * Return the values for the residual report
 *
 * @param array $residualReports array with the residual reports to process
 * @param boolean $summarized if true a summarized version containing only totals will be returned. 
 * @return array
 */
	public function getReportData($residualReports = [], $summarized = false, $rolledUp = true) {
		$users = $this->User->getEntityManagerUsersList(true);
		$organizations = $this->Merchant->Organization->find('list');
		$regions = $this->Merchant->Region->find('list');
		$subregions = $this->Merchant->Subregion->find('list');
		$partners = [];
		$products = $this->ProductsServicesType->buildDdOptions(true);
		if ($this->User->roleIs($this->_getCurrentUser('id'), User::ROLE_PARTNER) === false) {
			$partners = $this->User->getByRole(User::ROLE_PARTNER);
		}

		if ($rolledUp == true && $summarized == false) {
			$residualData = $this->_rollUpReportData($residualReports);
		} else {
			$residualData = $this->_processResidualReportData($residualReports, $summarized);			
		}
		$labels = $this->_labels;

		return Hash::merge(
			$residualData,
			compact('labels', 'users', 'partners', 'products', 'organizations', 'regions', 'subregions')
		);
	}

/**
 * Return the values for the residual report
 *
 * @param array $residualReports array with the residual reports to process
 * @param array $projectedResiduals commission multiple data from CommissionPricing
 * @param int $numberOfMonths a number of months to use to calculate averages
 * @return array
 */
	public function getGpaReportData($residualReports, $projectedResiduals, $numberOfMonths, $commissionReportType) {
		$residualReports = $this->_processGpaActualResiduals($residualReports, $projectedResiduals, $numberOfMonths, $commissionReportType);
		return $residualReports;
	}

/**
 * _extractProjectedData
 * Extracts the projected residuals data that martch the merchant mid and the product name parameters
 * The projected residuals is CommissionPricing data built using commission multiple report functionality
 * All matching subsets will be removed from the first param reference in order to reduce time complexity when
 * calling this function consecutively in a loop using the same array reference as the first param.
 *
 * @param array &$projectedResiduals containing CommissionPricing data
 * @param string $merchantMid a merchant_mid to search for
 * @param string $productName a product name
 * @param boolean $removeMatches if true All matches be removed from the reference.
 * @return array
 */
	protected function _extractProjectedData(&$projectedResiduals, $merchantMid, $productName, $removeMatches = true) {
		if (!empty($projectedResiduals)) {
			foreach ($projectedResiduals as $idx => $projData) {
				if ($projData['mid'] == $merchantMid && $projData['product'] == $productName) {
					if ($removeMatches) {
						unset($projectedResiduals[$idx]);
					}
					return $projData;
				}
			}
		}
		return [];
	}

/**
 * _processGpaActualResiduals
 *
 * @param array $actualResiduals residual report data
 * @param array $projectedResiduals commission multiple data from CommissionPricing
 * @param int $numberOfMonths a number of months to use to calculate averages
 * @return array
 * @throws InvalidArgumentException
 */
	protected function _processGpaActualResiduals($actualResiduals, $projectedResiduals, $numberOfMonths, $commissionReportType) {
		if (!is_array($actualResiduals)) {
			throw new InvalidArgumentException(__('"ResidualReport" must be an array'));
		}

		$numberOfMonths = ($numberOfMonths == 0)? 1 : $numberOfMonths;
		$projectedTotals = Hash::get($projectedResiduals, 'commissionMultipleTotals');
		$projectedResiduals = Hash::extract($projectedResiduals, 'commissionMultiples');
		$allOtherProductsTotalItems = $discountProductsTotalItems = 0;
		$totals = [
			'r_avg_ticket' => 0,
			'r_items' => 0,
			'avg_volume' => 0,
			'rep_avg_gross_profit' => 0,
			'rep_avg_residual_gp' => 0,
			'gp_amount_diff' => 0,
			'gp_pct_diff' => 0,
		];
		if ($commissionReportType === CommissionPricing::REPORT_GP_ANALYSIS) {
			$totals['avg_partner_profit_amount'] = 0;
		}

		if ($commissionReportType === CommissionPricing::REPORT_CM_ANALYSIS) {
			$totals['r_volume'] = 0;
			$totals['rep_gross_profit'] = 0;
			$totals['rep_total_residual_gp'] = 0;
			$totals['rep_projected_res_gp'] = 0;
			$totals['actual_multiple'] = 0;
		}
		$residuals = [];
		foreach ($actualResiduals as $residualReport) {
			if (Hash::get($residualReport, 'RepUserParameter.value') == 1 || Hash::get($residualReport, 'PartnerUserParameter.value') == 1) {
				continue;
			}
			$isPartnerRep = !empty(Hash::get($residualReport, 'PartnerRep.PartnerRepFullname'));
			$productName = Hash::get($residualReport, 'ProductsServicesType.products_services_description');
			$rVolumes = 0;
			//Show/calculate volume only for products matching this logic
			if (stripos($productName, "Discount") !== false || $productName === 'ACH') {
				$rVolumes = Hash::get($residualReport, 'ResidualReport.r_volume');
			} else {
				$rVolumes = null;
			}
			$avgPrtnrProfit = $partnerProfit = Hash::get($residualReport, 'ResidualReport.partner_profit_amount');
			$repGp = ($isPartnerRep)? Hash::get($residualReport, 'ResidualReport.partner_rep_gross_profit') : Hash::get($residualReport, 'ResidualReport.rep_gross_profit');
			$totalResidualGp = $repGp - $partnerProfit - Hash::get($residualReport, 'ResidualReport.refer_profit_amount') - Hash::get($residualReport, 'ResidualReport.res_profit_amount');
			
			$currProjectedData = [];
			if (!empty($projectedResiduals)) {
				$currProjectedData = $this->_extractProjectedData($projectedResiduals, Hash::get($residualReport, 'Merchant.merchant_mid'), Hash::get($residualReport, 'ProductsServicesType.products_services_description'));
			}

			$repProjectedGp = Hash::get($currProjectedData, 'rep_residual_gp');
			$avgTicket = Hash::get($residualReport, 'ResidualReport.r_avg_ticket') / $numberOfMonths;
			$rItems = Hash::get($residualReport, 'ResidualReport.r_items');
			$avgPrtnrProfit = $avgPrtnrProfit / $numberOfMonths;
			$avgVolume = $rVolumes / $numberOfMonths;
			$repAvgGp = $repGp / $numberOfMonths;
			$avgResidualGp = $totalResidualGp / $numberOfMonths;
			$gpAmountDiff = $avgResidualGp - $repProjectedGp;
			$gpPctDiff = 0;
			
			if ($commissionReportType === CommissionPricing::REPORT_CM_ANALYSIS) {
				
				$actualMultiple = $avgResidualGp * (Hash::get($currProjectedData, 'rep_pct_of_gross') / 100) * (Hash::get($currProjectedData, 'multiple') / 100);
				
			}
			if ($avgResidualGp != $repProjectedGp && $repProjectedGp !=0) {
				$gpPctDiff =  ($avgResidualGp / $repProjectedGp) * 100;
			}
			$actuals = [
				'active_merchant' => Hash::get($residualReport, 'Merchant.active'),
				'merchant_mid' => Hash::get($residualReport, 'Merchant.merchant_mid'),
				'merchant_dba' => Hash::get($residualReport, 'Merchant.merchant_dba'),
				'client_id_global' => Hash::get($residualReport, 'Client.client_id_global'),
				'RepFullname' => ($isPartnerRep)? Hash::get($residualReport, 'PartnerRep.PartnerRepFullname') : Hash::get($residualReport, 'Rep.RepFullname'),
				'products_services_description' => Hash::get($residualReport, 'ProductsServicesType.products_services_description'),
				'r_avg_ticket' => CakeNumber::currency($avgTicket),
				'r_items' => $rItems,
				'rep_avg_gross_profit' => CakeNumber::currency($repAvgGp),
				'gp_amount_diff' => CakeNumber::currency($gpAmountDiff),
				'gp_pct_diff' => (empty($repProjectedGp))? CakeNumber::toPercentage(100) : CakeNumber::toPercentage($gpPctDiff),
				'organization' => Hash::get($residualReport, 'Organization.name'),
				'region' => Hash::get($residualReport, 'Region.name'),
				'subregion' => Hash::get($residualReport, 'Subregion.name'),
				'location' => Hash::get($residualReport, 'Address.address_street'),
			];
			if ($commissionReportType === CommissionPricing::REPORT_GP_ANALYSIS) {
				$actuals['avg_partner_profit_amount'] = CakeNumber::currency($avgPrtnrProfit);
				$actuals['PartnerFullname'] = Hash::get($residualReport, 'Partner.PartnerFullname');
				//totals
				$totals['avg_partner_profit_amount'] += $avgPrtnrProfit;

			} elseif ($commissionReportType === CommissionPricing::REPORT_CM_ANALYSIS) {
				$actuals['rep_projected_res_gp'] = CakeNumber::currency($repProjectedGp);
				$actuals['actual_multiple'] = CakeNumber::currency($actualMultiple, 'USD3dec');
				$actuals['r_volume'] = CakeNumber::currency($rVolumes);
				$actuals['rep_gross_profit'] = CakeNumber::currency($repGp);
				$actuals['rep_total_residual_gp'] = CakeNumber::currency($totalResidualGp);
				$actuals['commissionable'] = Hash::get($residualReport, 'ResidualReport.commissionable');
				$actuals['PartnerFullname'] = Hash::get($residualReport, 'Partner.PartnerFullname');

				//totals
				$totals['actual_multiple'] = bcadd($totals['actual_multiple'], $actualMultiple, 5);
				$totals['r_volume'] = bcadd($totals['r_volume'], $rVolumes, 4);
				$totals['rep_gross_profit'] = bcadd($totals['rep_gross_profit'], $repGp, 4);
				$totals['rep_total_residual_gp'] = bcadd($totals['rep_total_residual_gp'], $totalResidualGp, 3);
			}
			//common actuals
			$actuals['avg_volume'] = CakeNumber::currency($avgVolume);
			$actuals['rep_avg_residual_gp'] = CakeNumber::currency($avgResidualGp);
			//common totals
			$totals['avg_volume'] = bcadd($totals['avg_volume'], $avgVolume, 4);
			$totals['r_avg_ticket'] += $avgTicket;
			$totals['rep_avg_gross_profit'] = bcadd($totals['rep_avg_gross_profit'], $repAvgGp, 5);
			$totals['rep_avg_residual_gp'] = bcadd($totals['rep_avg_residual_gp'], $avgResidualGp, 5);

			//Discount products item count is the same as Non-dial products so use this logic to avoid double-counting the items from these products
			if (stripos($productName, "Discount") !== false) {
				$discountProductsTotalItems += $rItems;
			} else {
				$allOtherProductsTotalItems += $rItems;
			}

			$residuals[] = $actuals;
		}

		if ($allOtherProductsTotalItems > 0) {
			$totals['r_items'] = $allOtherProductsTotalItems;
		} else {
			//If a discount product was the only product selected then update the total item count for that product otherwise it will be zero.
			$totals['r_items'] = $discountProductsTotalItems;
		}

		$totals['rep_projected_res_gp'] = $projectedTotals['rep_residual_gp'];
		if ($commissionReportType === CommissionPricing::REPORT_GP_ANALYSIS) {
			unset($totals['rep_projected_res_gp']);
		} elseif ($commissionReportType === CommissionPricing::REPORT_CM_ANALYSIS) {
			$totals['multiple_diff'] = $totals['actual_multiple'] - $projectedTotals['multiple_amount'];
		}
		$totals['gp_amount_diff'] = $totals['rep_avg_residual_gp'] - $projectedTotals['rep_residual_gp'];
		$overallPgPctDiff = ($projectedTotals['rep_residual_gp'] != 0)? bcdiv($totals['rep_avg_residual_gp'], $projectedTotals['rep_residual_gp'], 5): 1;
		$totals['gp_pct_diff'] = $overallPgPctDiff * 100;
		$userId = $this->_getCurrentUser('id');
		return [
			'actualResiduals' => RolePermissions::filterReportData(self::SECTION_RESIDUAL_REPORTS, $userId, $residuals),
			'totals' => $totals,
		];
	}

/**
 * Custom finder query for Gross Profit Analysis Residual data
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findGpaResiduals($state, $query, $results = []) {
		if ($state === 'before') {
			$query = $this->_buildQuery($query);
			if (Hash::get($query, 'report_type') === CommissionPricing::REPORT_CM_ANALYSIS) {
				//We dont care about Discover
				$query['conditions'][] = "ResidualReport.products_services_type_id NOT IN (Select id from products_services_types where products_services_description ilike '%Discover%')";
			}
			if (Hash::get($query, 'report_type') === CommissionPricing::REPORT_GP_ANALYSIS) {
				$query['conditions'][] = "TimelineEntry.timeline_item_id = '" . TimelineItem::GO_LIVE_DATE . "'";
			}
			return $query;
		}

		return $results;
	}

/**
 * Custom finder query for Projected Residuals data
 *
 * @param array $query query arguments
 * @return array Modified query OR results of query
 */
	protected function _buildQuery($query) {
		if (!isset($query['fields'])) {
			$query['fields'] = [
				"Merchant.active",
				"Merchant.merchant_mid",
				"Merchant.merchant_dba",
				"Client.client_id_global",
				'((SUM("ResidualReport"."total_profit"))) AS "ResidualReport__total_profit"',
				"ProductsServicesType.products_services_description",
				'((SUM("ResidualReport"."r_avg_ticket"))) AS "ResidualReport__r_avg_ticket"',
				'((' . $this->getReportVirtualFields("r_items") . ')) AS "ResidualReport__r_items"',
				'((' . $this->getReportVirtualFields("r_volume") . ')) AS "ResidualReport__r_volume"',
				$this->getReportVirtualFields("Rep__RepFullname") . ' as "Rep__RepFullname"',
				'((' . $this->getReportVirtualFields("ResidualReport__rep_gross_profit") . ')) AS "ResidualReport__rep_gross_profit"',
				'((' . $this->getReportVirtualFields("Partner__PartnerFullname") . ')) as "Partner__PartnerFullname"',
				'((SUM("ResidualReport"."partner_gross_profit"))) AS "ResidualReport__partner_gross_profit"',
				'((' . $this->getReportVirtualFields("partner_profit_amount") . ')) AS "ResidualReport__partner_profit_amount"',
				'((' . $this->getReportVirtualFields("rep_pct_of_gross") . ')) AS "ResidualReport__rep_pct_of_gross"',
				'((' . $this->getReportVirtualFields("r_profit_pct") . ')) AS "ResidualReport__r_profit_pct"',
				'((CASE WHEN "Merchant"."partner_id" IS NOT NULL THEN ' . $this->User->getFullNameVirtualField('PartnerRep') . ' ELSE NULL END)) as "PartnerRep__PartnerRepFullname"',
				'((SUM("ResidualReport"."partner_rep_gross_profit"))) AS "ResidualReport__partner_rep_gross_profit"',
				'((SUM("ResidualReport"."refer_profit_amount"))) AS "ResidualReport__refer_profit_amount"',
				'((AVG("ResidualReport"."partner_pct_of_gross"))) AS "ResidualReport__partner_pct_of_gross"',
				'((SUM("ResidualReport"."referrer_gross_profit"))) AS "ResidualReport__referrer_gross_profit"',
				'((AVG("ResidualReport"."referer_pct_of_gross"))) AS "ResidualReport__referer_pct_of_gross"',
				'((SUM("ResidualReport"."res_profit_amount"))) AS "ResidualReport__res_profit_amount"',
				'((SUM("ResidualReport"."reseller_gross_profit"))) AS "ResidualReport__reseller_gross_profit"',
				'((AVG("ResidualReport"."reseller_pct_of_gross"))) AS "ResidualReport__reseller_pct_of_gross"',
				'((CASE WHEN SUM("ResidualParameter"."value") > 0 OR SUM("ResidualTimeParameter"."value") > 0 then true ELSE false END)) AS "ResidualReport__commissionable"',
				'Organization.name',
				'Region.name',
				'Subregion.name',
				'Address.address_street',
			];
			$query['group'] = [
				"Merchant.active",
				"Merchant.merchant_mid",
				"Merchant.merchant_dba",
				"Client.client_id_global",
				"Merchant.partner_id",
				"ResidualReport.user_id",
				"ResidualReport.rep_gross_profit",
				"ResidualReport.products_services_type_id",
				"ProductsServicesType.products_services_description",
				"Rep.user_first_name",
				"Rep.user_last_name",
				"PartnerRep.user_first_name",
				"PartnerRep.user_last_name",
				"Partner.user_first_name",
				"Partner.user_last_name",
				'Organization.name',
				'Region.name',
				'Subregion.name',
				'Address.address_street',
			];
		}

		$query['joins'] = [
			[
				'table' => 'merchants',
				'alias' => 'Merchant',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.merchant_id = Merchant.id",
				],
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
					"Address.merchant_id = Merchant.id",
					'Address.address_type_id' => AddressType::BUSINESS_ADDRESS
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
			],
			[
				'table' => 'timeline_entries',
				'alias' => 'TimelineEntry',
				'type' => 'INNER',
				'conditions' => [
					"Merchant.id = TimelineEntry.merchant_id",
				],
			],
			[
				'table' => 'products_services_types',
				'alias' => 'ProductsServicesType',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.products_services_type_id = ProductsServicesType.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Rep',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.user_id = Rep.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'PartnerRep',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.user_id = PartnerRep.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Partner',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.partner_id = Partner.id",
				],
			],
			[
				'table' => 'entities',
				'alias' => 'Entity',
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.entity_id = Entity.id",
				],
			],
			[
				'table' => 'user_compensation_profiles',
				'alias' => 'RepUcp',
				'type' => 'LEFT',
				'conditions' => [
					"RepUcp.user_id = {$this->alias}.user_id",
					"RepUcp.partner_user_id = {$this->alias}.partner_id",
					"RepUcp.is_default = (CASE when {$this->alias}.partner_id is not null THEN false ELSE true END)"
				]
			],
			[
				'table' => 'residual_parameters',
				'alias' => 'ResidualParameter',
				'type' => 'LEFT',
				'conditions' => [
					"ResidualParameter.user_compensation_profile_id = (CASE WHEN RepUcp.is_profile_option_1 = 1 THEN RepUcp.id ELSE null END)",
					"ResidualParameter.is_multiple = 1",
					"ResidualParameter.residual_parameter_type_id" => ResidualParameterType::R_MULTIPLE,
					"ResidualParameter.tier = 0",
					"ResidualParameter.value > 0",
					"ResidualParameter.products_services_type_id = {$this->alias}.products_services_type_id"
				]
			],
			[
				'table' => 'residual_time_parameters',
				'alias' => 'ResidualTimeParameter',
				'type' => 'LEFT',
				'conditions' => [
					"ResidualTimeParameter.user_compensation_profile_id = (CASE WHEN RepUcp.is_profile_option_2 = 1 THEN RepUcp.id ELSE null END)",
					"ResidualTimeParameter.is_multiple = 1",
					"ResidualTimeParameter.residual_parameter_type_id" => ResidualParameterType::R_MULTIPLE,
					"ResidualTimeParameter.tier = 0",
					"ResidualTimeParameter.value > 0",
					"ResidualTimeParameter.products_services_type_id = {$this->alias}.products_services_type_id"
				]
			],
		];
		$allowedAdmins = [User::ROLE_ADMIN_I, User::ROLE_ADMIN_II, User::ROLE_ADMIN_III];
		if ($this->User->roleIs($this->_getCurrentUser('id'), $allowedAdmins) === false) {
			$query['fields'][] = 'RepUserParameter.value';
			$ucpJoins = [
				[
					'table' => 'user_parameters',
					'alias' => 'RepUserParameter',
					'type' => 'LEFT',
					'conditions' => [
						"RepUserParameter.user_compensation_profile_id = RepUcp.id",
						"RepUserParameter.products_services_type_id = {$this->alias}.products_services_type_id",
						"RepUserParameter.user_parameter_type_id = " . "'" . UserParameterType::DONT_DISPLAY . "'",
					]
				],

			];
			$query['group'][] = 'RepUserParameter.value';
			$query['joins'] = array_merge($query['joins'], $ucpJoins);
		}
		return $query;
	}
/**
 * Custom finder query for Residual Report multiple
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findResidualReport($state, $query, $results = []) {
		if ($state === 'before') {
			if (!isset($query['fields'])) {
				$query['fields'] = [
					"Merchant.merchant_mid",
					"Merchant.merchant_dba",
					"Client.client_id_global",
					//this is the merchant rate unmodified
					'((' . $this->getReportVirtualFields("original_m_rate_pct") . ')) AS "ResidualReport__processing_rate"',
					"{$this->alias}.partner_exclude_volume", //bool val
					'((SUM("ResidualReport"."total_profit"))) AS "ResidualReport__total_profit"',
					$this->getReportVirtualFields("Bet__name"),
					"ProductsServicesType.products_services_description",
					"ProductCategory.category_name",
					'((' . $this->getReportVirtualFields("r_avg_ticket") . ')) AS "ResidualReport__r_avg_ticket"',
					'((' . $this->getReportVirtualFields("r_items") . ')) AS "ResidualReport__r_items"',
					'((' . $this->getReportVirtualFields("r_volume") . ')) AS "ResidualReport__r_volume"',
					'((' . $this->getReportVirtualFields("m_rate_pct") . ')) AS "ResidualReport__m_rate_pct"',
					'((' . $this->getReportVirtualFields("m_per_item_fee") . ')) AS "ResidualReport__m_per_item_fee"',
					'((' . $this->getReportVirtualFields("m_statement_fee") . ')) AS "ResidualReport__m_statement_fee"',
					$this->getReportVirtualFields("Rep__RepFullname") . ' as "Rep__RepFullname"',
					'((' . $this->getReportVirtualFields("r_rate_pct") . ')) AS "ResidualReport__r_rate_pct"',
					'((' . $this->getReportVirtualFields("r_per_item_fee") . ')) AS "ResidualReport__r_per_item_fee"',
					'((' . $this->getReportVirtualFields("r_statement_fee") . ')) AS "ResidualReport__r_statement_fee"',
					'((' . $this->getReportVirtualFields("ResidualReport__rep_gross_profit") . ')) AS "ResidualReport__rep_gross_profit"',
					'((' . $this->getReportVirtualFields("r_profit_pct") . ')) AS "ResidualReport__r_profit_pct"',
					'((' . $this->getReportVirtualFields("rep_pct_of_gross") . ')) AS "ResidualReport__rep_pct_of_gross"',
					'((' . $this->getReportVirtualFields("r_profit_amount") . ')) AS "ResidualReport__r_profit_amount"',
					'((CASE WHEN "Merchant"."partner_id" IS NOT NULL THEN ' . $this->User->getFullNameVirtualField('PartnerRep') . ' ELSE NULL END)) as "PartnerRep__PartnerRepFullname"',
					'((AVG("ResidualReport"."partner_rep_rate"))) AS "ResidualReport__partner_rep_rate"',
					'((AVG("ResidualReport"."partner_rep_per_item_fee"))) AS "ResidualReport__partner_rep_per_item_fee"',
					'((AVG("ResidualReport"."partner_rep_statement_fee"))) AS "ResidualReport__partner_rep_statement_fee"',
					'((SUM("ResidualReport"."partner_rep_gross_profit"))) AS "ResidualReport__partner_rep_gross_profit"',
					'((AVG("ResidualReport"."partner_rep_profit_pct"))) AS "ResidualReport__partner_rep_profit_pct"',
					'((AVG("ResidualReport"."partner_rep_pct_of_gross"))) AS "ResidualReport__partner_rep_pct_of_gross"',
					'((' . $this->getReportVirtualFields("SalesManager__SalesManagerFullname") . ')) as "SalesManager__SalesManagerFullname"',
					'((AVG("ResidualReport"."manager_rate"))) AS "ResidualReport__manager_rate"',
					'((AVG("ResidualReport"."manager_per_item_fee"))) AS "ResidualReport__manager_per_item_fee"',
					'((AVG("ResidualReport"."manager_statement_fee"))) AS "ResidualReport__manager_statement_fee"',
					'((SUM("ResidualReport"."manager_gross_profit"))) AS "ResidualReport__manager_gross_profit"',
					'((' . $this->getReportVirtualFields("manager_profit_pct") . ')) AS "ResidualReport__manager_profit_pct"',
					'((AVG("ResidualReport"."manager_pct_of_gross"))) AS "ResidualReport__manager_pct_of_gross"',
					'((' . $this->getReportVirtualFields("manager_profit_amount") . ')) AS "ResidualReport__manager_profit_amount"',
					'((' . $this->getReportVirtualFields("SalesManager2__SalesManager2Fullname") . ')) as "SalesManager2__SalesManager2Fullname"',
					'((AVG("ResidualReport"."manager2_rate"))) AS "ResidualReport__manager2_rate"',
					'((AVG("ResidualReport"."manager2_per_item_fee"))) AS "ResidualReport__manager2_per_item_fee"',
					'((AVG("ResidualReport"."manager2_statement_fee"))) AS "ResidualReport__manager2_statement_fee"',
					'((SUM("ResidualReport"."manager2_gross_profit"))) AS "ResidualReport__manager2_gross_profit"',
					'((' . $this->getReportVirtualFields("manager_profit_pct_secondary") . ')) AS "ResidualReport__manager_profit_pct_secondary"',
					'((AVG("ResidualReport"."manager2_pct_of_gross"))) AS "ResidualReport__manager2_pct_of_gross"',
					'((' . $this->getReportVirtualFields("manager_profit_amount_secondary") . ')) AS "ResidualReport__manager_profit_amount_secondary"',
					'((AVG("ResidualReport"."partner_rate"))) AS "ResidualReport__partner_rate"',
					'((AVG("ResidualReport"."partner_per_item_fee"))) AS "ResidualReport__partner_per_item_fee"',
					'((AVG("ResidualReport"."partner_statement_fee"))) AS "ResidualReport__partner_statement_fee"',
					'((SUM("ResidualReport"."partner_gross_profit"))) AS "ResidualReport__partner_gross_profit"',
					'((AVG("ResidualReport"."partner_profit_pct"))) AS "ResidualReport__partner_profit_pct"',
					'((AVG("ResidualReport"."partner_pct_of_gross"))) AS "ResidualReport__partner_pct_of_gross"',
					'((' . $this->getReportVirtualFields("partner_profit_amount") . ')) AS "ResidualReport__partner_profit_amount"',
					'((' . $this->User->getFullNameVirtualField('Ref') . ')) as "Ref__RefFullname"',
					'((AVG("ResidualReport"."referrer_rate"))) AS "ResidualReport__referrer_rate"',
					'((AVG("ResidualReport"."referrer_per_item_fee"))) AS "ResidualReport__referrer_per_item_fee"',
					'((AVG("ResidualReport"."referrer_statement_fee"))) AS "ResidualReport__referrer_statement_fee"',
					'((SUM("ResidualReport"."referrer_gross_profit"))) AS "ResidualReport__referrer_gross_profit"',
					'((AVG("ResidualReport"."referer_pct_of_gross"))) AS "ResidualReport__referer_pct_of_gross"',
					'((AVG("ResidualReport"."refer_profit_pct"))) AS "ResidualReport__refer_profit_pct"',
					'((SUM("ResidualReport"."refer_profit_amount"))) AS "ResidualReport__refer_profit_amount"',
					'((' . $this->User->getFullNameVirtualField('Res') . ')) as "Res__ResFullname"',
					'((AVG("ResidualReport"."reseller_rate"))) AS "ResidualReport__reseller_rate"',
					'((AVG("ResidualReport"."reseller_per_item_fee"))) AS "ResidualReport__reseller_per_item_fee"',
					'((AVG("ResidualReport"."reseller_statement_fee"))) AS "ResidualReport__reseller_statement_fee"',
					'((SUM("ResidualReport"."reseller_gross_profit"))) AS "ResidualReport__reseller_gross_profit"',
					'((AVG("ResidualReport"."reseller_pct_of_gross"))) AS "ResidualReport__reseller_pct_of_gross"',
					'((AVG("ResidualReport"."res_profit_pct"))) AS "ResidualReport__res_profit_pct"',
					'((SUM("ResidualReport"."res_profit_amount"))) AS "ResidualReport__res_profit_amount"',
					"Address.address_state",
					"Entity.entity_name",
					'Organization.name',
					'Region.name',
					'Subregion.name',
					'Address.address_street',
					'LastDepositReport.last_deposit_date',
					'((' . $this->getReportVirtualFields("Partner__PartnerFullname") . ')) as "Partner__PartnerFullname"',
				];
				$query['group'] = [
					"Merchant.merchant_mid",
					"Merchant.merchant_dba",
					"Client.client_id_global",
					"Merchant.partner_id",
					"ResidualReport.user_id",
					"ResidualReport.partner_exclude_volume",
					"ResidualReport.bet_table_id",
					"Bet.name",
					"ResidualReport.products_services_type_id",
					"ProductsServicesType.products_services_description",
					"ProductCategory.category_name",
					"Rep.user_first_name",
					"Rep.user_last_name",
					"PartnerRep.user_first_name",
					"PartnerRep.user_last_name",
					"SM.user_first_name",
					"SM.user_last_name",
					"SM2.user_first_name",
					"SM2.user_last_name",
					"Ref.user_first_name",
					"Ref.user_last_name",
					"Res.user_first_name",
					"Res.user_last_name",
					"Partner.user_first_name",
					"Partner.user_last_name",
					"Address.address_state",
					"Entity.entity_name",
					'Organization.name',
					'Region.name',
					'Subregion.name',
					'Address.address_street',
					'LastDepositReport.last_deposit_date',
				];
			}

			$query['joins'] = [
				[
					'table' => 'bet_tables',
					'alias' => 'Bet',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.bet_table_id = Bet.id",
					],
				],
				[
					'table' => 'merchants',
					'alias' => 'Merchant',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.merchant_id = Merchant.id",
					],
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
					'table' => 'last_deposit_reports',
					'alias' => 'LastDepositReport',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = LastDepositReport.merchant_id'
					]
				],
				[
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'LEFT',
					'conditions' => [
						"Address.merchant_id = Merchant.id",
						"Address.address_type_id = '" . AddressType::BUSINESS_ADDRESS . "'"
					],
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
				],
				[
					'table' => 'products_services_types',
					'alias' => 'ProductsServicesType',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.products_services_type_id = ProductsServicesType.id",
					],
				],
				[
					'table' => 'product_categories',
					'alias' => 'ProductCategory',
					'type' => 'LEFT',
					'conditions' => [
						"ProductsServicesType.product_category_id = ProductCategory.id",
					],
				],
				[
					'table' => 'product_settings',
					'alias' => 'ProductSetting',
					'type' => 'LEFT',
					'conditions' => [
						"ProductSetting.merchant_id = Merchant.id",
						//This condition will only be sattisfied for products in the product_setting table (which are indepedent from merchant_pricings)
						"ProductSetting.products_services_type_id = ProductsServicesType.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'Rep',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.user_id = Rep.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'PartnerRep',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.user_id = PartnerRep.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'Partner',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.partner_id = Partner.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'Res',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.reseller_id = Res.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'Ref',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.referer_id = Ref.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'SM',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.manager_id = SM.id",
					],
				],
				[
					'table' => 'users',
					'alias' => 'SM2',
					'type' => 'LEFT',
					'conditions' => [
						"{$this->alias}.manager_id_secondary = SM2.id",
					],
				],
				[
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => [
						"Merchant.entity_id = Entity.id",
					],
				],
			];
			$allowedAdmins = [User::ROLE_ADMIN_I, User::ROLE_ADMIN_II, User::ROLE_ADMIN_III];
			if ($this->User->roleIs($this->_getCurrentUser('id'), $allowedAdmins) === false) {
				$query['fields'][] = 'RepUserParameter.value';
				$query['fields'][] = 'PartnerUserParameter.value';
				$ucpJoins = [
					[
						'table' => 'user_compensation_profiles',
						'alias' => 'RepUcp',
						'type' => 'LEFT',
						'conditions' => [
							"RepUcp.user_id = {$this->alias}.user_id",
							"RepUcp.partner_user_id = {$this->alias}.partner_id",
							"RepUcp.is_default = (CASE when {$this->alias}.partner_id is not null THEN false ELSE true END)"
						]
					],
					[
						'table' => 'user_compensation_profiles',
						'alias' => 'PartnerUcp',
						'type' => 'LEFT',
						'conditions' => [
							"PartnerUcp.user_id = {$this->alias}.partner_id",
							"PartnerUcp.is_default = true"
						]
					],
					[
						'table' => 'user_parameters',
						'alias' => 'RepUserParameter',
						'type' => 'LEFT',
						'conditions' => [
							"RepUserParameter.user_compensation_profile_id = RepUcp.id",
							"RepUserParameter.products_services_type_id = {$this->alias}.products_services_type_id",
							"RepUserParameter.user_parameter_type_id = " . "'" . UserParameterType::DONT_DISPLAY . "'",
						]
					],
					[
						'table' => 'user_parameters',
						'alias' => 'PartnerUserParameter',
						'type' => 'LEFT',
						'conditions' => [
							"PartnerUserParameter.user_compensation_profile_id = PartnerUcp.id",
							"PartnerUserParameter.products_services_type_id = {$this->alias}.products_services_type_id",
							"PartnerUserParameter.user_parameter_type_id = " . "'" . UserParameterType::DONT_DISPLAY . "'",
						]
					],

				];
				$query['group'][] = 'RepUserParameter.value';
				$query['group'][] = 'PartnerUserParameter.value';
				$query['joins'] = array_merge($query['joins'], $ucpJoins);
			}
			return $query;
		}

		return $results;
	}

/**
 * getReportFieldAliases
 * The elements in the array returned by this method must be used consistently throughout as the virtual fields.
 * If column sorting is desired on the report view, sortField names in the report view must exactly match the keys defined in the array returned by this method.
 * 
 * @param string $fieldName the name of the ResidualReport field
 * @return Mixed string | array containing all if no params are passed
 */
	public function getReportVirtualFields($fieldName = '') {
		$fields = [
			'Bet__name' => 'Bet.name',
			'r_avg_ticket' => 'AVG("ResidualReport"."r_avg_ticket")',
			'r_items' => 'SUM("ResidualReport"."r_items")',
			'r_volume' => 'TRUNC(SUM("ResidualReport"."r_volume"), 4)',
			'm_rate_pct' => 'AVG("ResidualReport"."m_rate_pct")',
			'original_m_rate_pct' => 'AVG("ResidualReport"."original_m_rate_pct")',
			'ProductSetting__rate' => 'AVG("ProductSetting"."rate")',
			'm_per_item_fee' => 'AVG("ResidualReport"."m_per_item_fee")',
			'm_statement_fee' => 'AVG("ResidualReport"."m_statement_fee")',
			'Rep__RepFullname' => '((CASE WHEN "Merchant"."partner_id" IS NOT NULL THEN NULL ELSE ' . $this->User->getFullNameVirtualField('Rep') . ' END))',
			'r_rate_pct' => 'AVG("ResidualReport"."r_rate_pct")',
			'r_per_item_fee' => 'AVG("ResidualReport"."r_per_item_fee")',
			'r_statement_fee' => 'AVG("ResidualReport"."r_statement_fee")',
			'ResidualReport__rep_gross_profit' => 'TRUNC(SUM("ResidualReport"."rep_gross_profit"), 4)',
			'r_profit_pct' => 'AVG("ResidualReport"."r_profit_pct")',
			'rep_pct_of_gross' => 'AVG("ResidualReport"."rep_pct_of_gross")',
			'r_profit_amount' => 'TRUNC(SUM("ResidualReport"."r_profit_amount") + SUM("ResidualReport"."partner_rep_profit_amount"), 4)',
			'SalesManager__SalesManagerFullname' => $this->User->getFullNameVirtualField('SM'),
			'manager_profit_pct' => 'AVG("ResidualReport"."manager_profit_pct")',
			'manager_profit_amount' => 'TRUNC(SUM("ResidualReport"."manager_profit_amount"), 4)',
			'SalesManager2__SalesManager2Fullname' => $this->User->getFullNameVirtualField('SM2'),
			'manager_profit_pct_secondary' => 'AVG("ResidualReport"."manager_profit_pct_secondary")',
			'manager_profit_amount_secondary' => 'TRUNC(SUM("ResidualReport"."manager_profit_amount_secondary"), 4)',
			'partner_profit_amount' => 'TRUNC(SUM("ResidualReport"."partner_profit_amount"), 4)',
			'Address__address_state' => 'Address.address_state',
			'Entity__entity_name' => 'Entity.entity_name',
			'Partner__PartnerFullname' => $this->User->getFullNameVirtualField('Partner'),
		];
		return (!empty($fieldName))? Hash::get($fields, $fieldName) : $fields;
	}

/**
 * setPaginatorVirtualFields
 * In order for column sorting to work on the report view, the paginator requires virtual fields to be set
 * before paginate method is called in the controller. 
 * The sortField names are defined as the keys in the array returned by the ResidualReport->getReportVirtualFields()
 * and those must sortfield names must be used on the report view for sort to work.
 *
 * @return void
 */
	public function setPaginatorVirtualFields() {
		$this->virtualFields = $this->getReportVirtualFields();
	}

/**
 * Format the residual section data from the residual report to match the permission matrix
 *
 * @param array $residualReports Residual Reports data
 * @param boolean $summarized if true a summarized version containing only totals will be returned. 
 * @throws InvalidArgumentException
 * @return array
 */
	protected function _processResidualReportData($residualReports, $summarized) {
		if (!is_array($residualReports)) {
			throw new InvalidArgumentException(__('"ResidualReport" must be an array'));
		}
		$allOtherProductsTotalItems = $discountProductsTotalItems = 0;
		$totals = [
			'r_items' => 0,
			'r_volume' => 0,
			'total_profit' => 0,
			'r_profit_amount' => 0,
			'refer_profit_amount' => 0,
			'res_profit_amount' => 0,
			'manager_profit_amount' => 0,
			'manager_profit_amount_secondary' => 0,
			'partner_profit_amount' => 0
		];

		$totalsNonReferral = [
			'r_volume' => 0,
			'gross_profit' => 0
		];

		$totalsReferral = [
			'r_volume' => 0,
			'gross_profit' => 0
		];

		$residuals = [];
		foreach ($residualReports as $residualReport) {
			if (Hash::get($residualReport, 'RepUserParameter.value') == 1 || Hash::get($residualReport, 'PartnerUserParameter.value') == 1) {
				continue;
			}
			$isPartnerRep = !empty(Hash::get($residualReport, 'PartnerRep.PartnerRepFullname'));
			if (!$summarized) {
				$productName = Hash::get($residualReport, 'ProductsServicesType.products_services_description');
				$rVolumes = 0;
				//Show/calculate volume only for products matching this logic
				if (stripos($productName, "Discount") !== false || $productName === 'ACH') {
					$rVolumes = Hash::get($residualReport, 'ResidualReport.r_volume');
				} else {
					$rVolumes = null;
				}
				$residuals[] = [
					'merchant_mid' => Hash::get($residualReport, 'Merchant.merchant_mid'),
					'merchant_dba' => Hash::get($residualReport, 'Merchant.merchant_dba'),
					'bet_name' => Hash::get($residualReport, 'Bet.name'),
					'client_id_global' => Hash::get($residualReport, 'Client.client_id_global'),
					'products_services_description' => Hash::get($residualReport, 'ProductsServicesType.products_services_description'),
					'r_avg_ticket' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.r_avg_ticket')),
					'r_items' => Hash::get($residualReport, 'ResidualReport.r_items'),
					'r_volume' => CakeNumber::currency($rVolumes),
					'm_rate_pct' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.processing_rate'), 3),
					'm_rate_and_bet_pct' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.m_rate_pct'), 3),
					'm_per_item_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.m_per_item_fee'), 'USD3dec'),
					'm_statement_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.m_statement_fee'), 'USD3dec'),
					//Consolidate rep and partner rep data
					'RepFullname' => ($isPartnerRep)? Hash::get($residualReport, 'PartnerRep.PartnerRepFullname') : Hash::get($residualReport, 'Rep.RepFullname'),
					'r_rate_pct' => ($isPartnerRep)? CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.partner_rep_rate'), 3) : CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.r_rate_pct'), 3),
					'r_per_item_fee' => ($isPartnerRep)? CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_rep_per_item_fee'), 'USD3dec') : CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.r_per_item_fee'), 'USD3dec'),
					'r_statement_fee' => ($isPartnerRep)? CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_rep_statement_fee')) : CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.r_statement_fee')),
					'rep_gross_profit' => ($isPartnerRep)? CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_rep_gross_profit')) : CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.rep_gross_profit')),
					'r_profit_pct' => ($isPartnerRep)? CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.partner_rep_profit_pct'), 3) : CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.r_profit_pct'), 3),
					'rep_pct_of_gross' => ($isPartnerRep)? CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.partner_rep_pct_of_gross'), 3) : CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.rep_pct_of_gross'), 3),
					'r_profit_amount' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.r_profit_amount'), 'USD3dec'),
					'SalesManagerFullname' => Hash::get($residualReport, 'SalesManager.SalesManagerFullname'),
					'manager_rate' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.manager_rate'), 3),
					'manager_per_item_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager_per_item_fee'), 'USD3dec'),
					'manager_statement_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager_statement_fee')),
					'manager_gross_profit' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager_gross_profit')),
					'manager_profit_pct' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.manager_profit_pct'), 3),
					'manager_pct_of_gross' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.manager_pct_of_gross'), 3),
					'manager_profit_amount' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager_profit_amount'), 'USD3dec'),
					'SalesManager2Fullname' => Hash::get($residualReport, 'SalesManager2.SalesManager2Fullname'),
					'manager2_rate' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.manager2_rate'), 3),
					'manager2_per_item_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager2_per_item_fee'), 'USD3dec'),
					'manager2_statement_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager2_statement_fee')),
					'manager2_gross_profit' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager2_gross_profit')),
					'manager_profit_pct_secondary' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.manager_profit_pct_secondary'), 3),
					'manager2_pct_of_gross' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.manager2_pct_of_gross'), 3),
					'manager_profit_amount_secondary' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.manager_profit_amount_secondary'), 'USD3dec'),
					'PartnerFullname' => Hash::get($residualReport, 'Partner.PartnerFullname'),
					'partner_rate' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.partner_rate'), 3),
					'partner_per_item_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_per_item_fee'), 'USD3dec'),
					'partner_statement_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_statement_fee')),
					'partner_gross_profit' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_gross_profit')),
					'partner_profit_pct' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.partner_profit_pct'), 3),
					'partner_pct_of_gross' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.partner_pct_of_gross'), 3),
					'partner_profit_amount' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.partner_profit_amount'), 'USD3dec'),
					'RefFullname' => Hash::get($residualReport, 'Ref.RefFullname'),
					'referrer_rate' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.referrer_rate'), 3),
					'referrer_per_item_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.referrer_per_item_fee'), 'USD3dec'),
					'referrer_statement_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.referrer_statement_fee')),
					'referrer_gross_profit' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.referrer_gross_profit')),
					'referer_pct_of_gross' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.referer_pct_of_gross'), 3),
					'refer_profit_pct' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.refer_profit_pct'), 3),
					'refer_profit_amount' => Hash::get($residualReport, 'ResidualReport.refer_profit_amount'),
					'ResFullname' => Hash::get($residualReport, 'Res.ResFullname'),
					'reseller_rate' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.reseller_rate'), 3),
					'reseller_per_item_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.reseller_per_item_fee'), 'USD3dec'),
					'reseller_statement_fee' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.reseller_statement_fee')),
					'reseller_gross_profit' => CakeNumber::currency(Hash::get($residualReport, 'ResidualReport.reseller_gross_profit')),
					'reseller_pct_of_gross' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.reseller_pct_of_gross'), 3),
					'res_profit_pct' => CakeNumber::toPercentage(Hash::get($residualReport, 'ResidualReport.res_profit_pct'), 3),
					'res_profit_amount' => Hash::get($residualReport, 'ResidualReport.res_profit_amount'),
					'address_state' => Hash::get($residualReport, 'Address.address_state'),
					'organization' => Hash::get($residualReport, 'Organization.name'),
					'region' => Hash::get($residualReport, 'Region.name'),
					'subregion' => Hash::get($residualReport, 'Subregion.name'),
					'location' => Hash::get($residualReport, 'Address.address_street'),
					'ent_name' => Hash::get($residualReport, 'Entity.entity_name'),
					'last_deposit_date' => !empty(Hash::get($residualReport, 'LastDepositReport.last_deposit_date'))? date_format(date_create(Hash::get($residualReport, 'LastDepositReport.last_deposit_date')), 'M j, Y'):null
				];
			}

			$rItems = Hash::get($residualReport, 'ResidualReport.r_items');
			$totalProfit = Hash::get($residualReport, 'ResidualReport.total_profit');
			$rProfitAmount = Hash::get($residualReport, 'ResidualReport.r_profit_amount');
			$referProfitAmount = Hash::get($residualReport, 'ResidualReport.refer_profit_amount');
			$resProfitAmount = Hash::get($residualReport, 'ResidualReport.res_profit_amount');
			$managerProfitAmount = Hash::get($residualReport, 'ResidualReport.manager_profit_amount');
			$managerProfitAmountSecondary = Hash::get($residualReport, 'ResidualReport.manager_profit_amount_secondary');
			$partnerProfitAmount = Hash::get($residualReport, 'ResidualReport.partner_profit_amount');

			$rVolumesNon = 0;
			$grossProfitNon = 0;
			$rVolumesRef = 0;
			$grossProfitRef = 0;
			//This exclution when true is meant to exclude volume from Non referal and include the volume into referal volume
			if (Hash::get($residualReport, 'ResidualReport.partner_exclude_volume') === true) {
				$rVolumesRef = $rVolumes;
				$grossProfitRef = Hash::get($residualReport, 'ResidualReport.total_profit');
			} else {
				$rVolumesNon = $rVolumes;
				$grossProfitNon = Hash::get($residualReport, 'ResidualReport.total_profit');
			}

			//Discount products item count is the same as Non-dial products so use this logic to avoid double-counting the items from these products
			if (stripos($productName, "Discount") !== false) {
				$discountProductsTotalItems += $rItems;
			} else {
				$allOtherProductsTotalItems += $rItems;
			}
			
			$totals['r_volume'] = bcadd($totals['r_volume'], $rVolumes, 6);
			$totals['total_profit'] = bcadd($totals['total_profit'], $totalProfit, 6);
			$totals['r_profit_amount'] = bcadd($totals['r_profit_amount'], $rProfitAmount, 6);
			$totals['refer_profit_amount'] = bcadd($totals['refer_profit_amount'], $referProfitAmount, 6);
			$totals['res_profit_amount'] = bcadd($totals['res_profit_amount'], $resProfitAmount, 6);
			$totals['manager_profit_amount'] = bcadd($totals['manager_profit_amount'], $managerProfitAmount, 6);
			$totals['manager_profit_amount_secondary'] = bcadd($totals['manager_profit_amount_secondary'], $managerProfitAmountSecondary, 6);
			$totals['partner_profit_amount'] = bcadd($totals['partner_profit_amount'], $partnerProfitAmount, 6);

			$totalsNonReferral['r_volume'] = bcadd($totalsNonReferral['r_volume'], $rVolumesNon, 6);
			$totalsNonReferral['gross_profit'] = bcadd($totalsNonReferral['gross_profit'], $grossProfitNon, 6);

			$totalsReferral['r_volume'] = bcadd($totalsReferral['r_volume'], $rVolumesRef, 6);
			$totalsReferral['gross_profit'] = bcadd($totalsReferral['gross_profit'], $grossProfitRef, 6);

		}

		if ($allOtherProductsTotalItems > 0) {
			$totals['r_items'] = $allOtherProductsTotalItems;
		} else {
			//If a discount product was the only product selected then update the total item count for that product otherwise it will be zero.
			$totals['r_items'] = $discountProductsTotalItems;
		}

		$userId = $this->_getCurrentUser('id');
		return [
			'residualReports' => RolePermissions::filterReportData(self::SECTION_RESIDUAL_REPORTS, $userId, $residuals),
			'totals' => $totals,
			'totalsNonReferrals' => $totalsNonReferral,
			'totalsReferrals' => $totalsReferral
		];
	}

/**
 * _rollUpReportData
 * Structures the data as follows
 * Data is grouped by merchant, then within each merchant group the products data is groupped by Product Category.
 * The resulting structure is
 * [ 
 *		'<mid 1>' => [
 *		 		'Product category A' => [
 *					'residualReports' => [
 *						[<residual report data>]
 *						[<residual report data>]
 *		 		],
 *		 		'Product category Z' => [
 *					'residualReports' => [
 *						[<residual report data>]
 *						[<residual report data>]
 *		 		]
 *	 		],
 *		'<mid n>' => [...]
 *	]
 *
 * @param array $data Residual Report data retreived using custom find method _findResidualReport
 * @return array
 */
	protected function _rollUpReportData($data) {
		$mGroups = Hash::combine($data, '{n}.ProductsServicesType.products_services_description', '{n}', '{n}.Merchant.merchant_mid');
		$totals = [
			'r_items' => 0,
			'r_volume' => 0,
			'total_profit' => 0,
			'r_profit_amount' => 0,
			'refer_profit_amount' => 0,
			'res_profit_amount' => 0,
			'manager_profit_amount' => 0,
			'manager_profit_amount_secondary' => 0,
			'partner_profit_amount' => 0
		];

		$totalsNonReferral = [
			'r_volume' => 0,
			'gross_profit' => 0
		];

		$totalsReferral = [
			'r_volume' => 0,
			'gross_profit' => 0
		];
		$result = [];
		foreach ($mGroups as $mid => $mGroup) {
			$categorizedProds = Hash::combine($mGroup, '{s}.ProductsServicesType.products_services_description', '{s}', '{s}.ProductCategory.category_name');
			foreach ($categorizedProds as $categoryName => $subset) {
				$categoryData = $this->_processResidualReportData($subset, false);

				if (empty($categoryData['residualReports'])) {
					continue;
				}
				$result[$mid][$categoryName] = $categoryData;

				//Add up all subset totals to get the grand total
				$totals['r_items'] += $result[$mid][$categoryName]['totals']['r_items'];
				$totals['r_volume'] = bcadd($totals['r_volume'], $result[$mid][$categoryName]['totals']['r_volume'], 4);
				$totals['total_profit'] = bcadd($totals['total_profit'], $result[$mid][$categoryName]['totals']['total_profit'], 4);
				$totals['r_profit_amount'] = bcadd($totals['r_profit_amount'], $result[$mid][$categoryName]['totals']['r_profit_amount'], 4);
				$totals['refer_profit_amount'] = bcadd($totals['refer_profit_amount'], $result[$mid][$categoryName]['totals']['refer_profit_amount'], 4);
				$totals['res_profit_amount'] = bcadd($totals['res_profit_amount'], $result[$mid][$categoryName]['totals']['res_profit_amount'], 4);
				$totals['manager_profit_amount'] = bcadd($totals['manager_profit_amount'], $result[$mid][$categoryName]['totals']['manager_profit_amount'], 4);
				$totals['manager_profit_amount_secondary'] = bcadd($totals['manager_profit_amount_secondary'], $result[$mid][$categoryName]['totals']['manager_profit_amount_secondary'], 4);
				$totals['partner_profit_amount'] = bcadd($totals['partner_profit_amount'], $result[$mid][$categoryName]['totals']['partner_profit_amount'], 4);

				$totalsNonReferral['r_volume'] = bcadd($totalsNonReferral['r_volume'], $result[$mid][$categoryName]['totalsNonReferrals']['r_volume'], 4);
				$totalsNonReferral['gross_profit'] = bcadd($totalsNonReferral['gross_profit'], $result[$mid][$categoryName]['totalsNonReferrals']['gross_profit'], 4);

				$totalsReferral['r_volume'] = bcadd($totalsReferral['r_volume'], $result[$mid][$categoryName]['totalsReferrals']['r_volume'], 4);
				$totalsReferral['gross_profit'] = bcadd($totalsReferral['gross_profit'], $result[$mid][$categoryName]['totalsReferrals']['gross_profit'], 4);
				unset($result[$mid][$categoryName]['totalsNonReferrals'], $result[$mid][$categoryName]['totalsReferrals']);
			}
		}
			$residualReport['residualRollUpData'] = $result;
			$residualReport['totals'] = $totals;
			$residualReport['totalsReferrals'] = $totalsReferral;
			$residualReport['totalsNonReferrals'] = $totalsNonReferral;

		return $residualReport;
	}
/**
 * getSummarizedReportData
 *
 * @param array $parsedConditions conditions for search parsed using method ResidualReport->parseCriteria()
 * @return array
 */
	public function getSummarizedReportData($parsedConditions) {
		$data = $this->find('residualReport', [
			'conditions' => $parsedConditions,
			'fields' => [
				"{$this->alias}.r_items",
				"{$this->alias}.r_volume",
				"{$this->alias}.total_profit",
				"{$this->alias}.r_profit_amount",
				"{$this->alias}.refer_profit_amount",
				"{$this->alias}.res_profit_amount",
				"{$this->alias}.manager_profit_amount",
				"{$this->alias}.manager_profit_amount_secondary",
				"{$this->alias}.partner_exclude_volume",
			]
		]);
		return $data;
	}

/**
 * deleteMany
 * Deletes many records matching conditions.
 * Expected $conditions param data structure is as follows (All keys must have values)
 * $conditions = [
 * 		'year' => yyyy, //only one year per deletion
 * 		'months' => [single dimentional indexed array of months as numbers mm],
 * 		'products' => [
 *			[single dimentional indexed array of product ids]
 * 		]
 *	]
 *
 * @param array $conditions result set
 * @return boolean true on success or false on falure
 * @throws InvalidArgumentException
 */
	public function deleteMany($conditions) {
		if (empty(Hash::get($conditions, 'year')) || !is_array(Hash::get($conditions, 'months_products')) || empty(Hash::get($conditions, 'months_products')) || !is_array(Hash::get($conditions, 'months_products')) || empty(Hash::extract($conditions, 'months_products.{n}'))) {
			throw new InvalidArgumentException('Missing or invalid entry in conditions array parameter');
		}
		$year = Hash::get($conditions, 'year');

		$orConditions = '';
		foreach (Hash::get($conditions, 'months_products') as $month => $productIds) {
			array_walk($productIds, function(&$item, $key){
				$item = "'$item'";
			});
			$productIds = implode(',', $productIds);
			if (!empty($orConditions)) {
				$orConditions = "$orConditions OR";
			}
			$orConditions .= "(r_month = $month AND products_services_type_id in ($productIds))";
		}
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		//Using $this->query() is much faster than $this->deleteAll() when deleting thousands of records
		$this->query("DELETE FROM residual_reports where r_year = $year and ($orConditions)");
		$stillHasRecords = $this->hasAny([
			"r_year = $year AND ($orConditions)",
		]);

		if ($stillHasRecords) {
			$dataSource->rollback();
			return false;
		}

		$dataSource->commit();
		return true;
	}
}
