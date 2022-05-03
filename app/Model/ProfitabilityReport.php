<?php
App::uses('AppModel', 'Model');
/**
 * ProfitabilityReport Model
 *
 * @property Merchant $Merchant
 */
class ProfitabilityReport extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'year' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Year should be a natural number',
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Year cannot be blank',
			),
		),
		'month' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Month should be a natural number',
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Month cannot be blank',
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = [
		'Search.Searchable',
		'SearchByMonthYear' => [
			'yearFieldName' => 'year',
			'monthFieldName' => 'month',
		],
		'UploadedFile'
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
	];

/**
 * Find Method
 *
 * @var array
 */
	public $findMethods = [
		'profitability' => true,
	];
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
		)
	);

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
 * importData
 * Function validates and saves file uploaded via HTTP request. File is assumed to contain all data to generate new report data.
 * The processing of the data will be queued
 *
 * @param array $requestData data passed during HTTP request including year, month and CSV file name uploaded via view form
 * @param boolean $queueJob will queue import process in a CakeResque worker job when true otherwise work will be processed in real time
 * @return boolean true
 * @throws Exception
 */
	public function importData($requestData, $queueJob = true) {
		$year = $requestData['ProfitabilityReport']['year']['year'];
		$month = $requestData['ProfitabilityReport']['month']['month'];
		if ($queueJob != $requestData['ProfitabilityReport']['serverside_processing']) {
			$queueJob = (boolean)$requestData['ProfitabilityReport']['serverside_processing'];
		}
		if ($this->hasAny(['year' => $year, 'month' => $month])) {
			$moName = date("M", mktime(0, 0, 0, $month, 10));
			throw new Exception("Import failed! Data for $moName $year already exists.");
		}

		$tmpFileName = basename($requestData['ProfitabilityReport']['profitabilityReportFile']['tmp_name']);
		$filepath = APP . 'tmp' . DS;
		$fileName = $requestData['ProfitabilityReport']['profitabilityReportFile']['name'];
		if (!empty($tmpFileName) && is_uploaded_file(DS .'tmp' . DS . $tmpFileName)) {
			//Check file extension is csv
			if (preg_match('/\.csv$/', $fileName)) {
				$isMoved = $this->moveUploadedFile(
					$requestData['ProfitabilityReport']['profitabilityReportFile']['tmp_name'],
					$filepath,
					$tmpFileName
				);
				$filepath .= $tmpFileName;
				if ($isMoved) {
					if ($this->isValidCsvContent($filepath) === false) {
						throw new Exception(__("ERROR: Invalid file content or format! Unexpected number of columns and/or column names, please ensure file content matches required format."));
					}
					if ($queueJob) {
						CakeResque::enqueue(
							'genericAxiaDbQueue',
							'ProfitabilityReportShell',
							array(
								'beginImport',
								//Args:
								$year,
								$month,
								$filepath,
								$this->_getCurrentUser('user_email'))
						);
					} else {
						return $this->processDataFile($year, $month, $filepath);
					}
				} else {
					throw new Exception(__('Error: Failed to save file for background process! Please ry again.'));
				}
			} else {
				throw new Exception(__('Invalid file type! Only ".csv" files are allowed.'));
			}
		} else {
			throw new Exception(__('File upload failed! Upload method is not allowed.'));
		}
	}

/**
 * processDataFile
 * Function processes all data from a previosuly imported and saved CSV file and saves it as new report.
 * Gets Residual Report and Merchant data and performs some required calculations.
 *
 * @param integer $year the year to save new data for
 * @param integer $month the month to save new data for
 * @param $datafile the full path to the location of the file containing the ProfitabilityReport data
 * @return
 * @throws Exception
 */
	public function processDataFile($year, $month, $datafile) {
		$moName = date("M", mktime(0, 0, 0, $month, 10));
		$response = [
			'job_name' => 'Profitability Data Import',
			'result' => true,
			'start_time' => date('Y-m-d H:i:s'), // date time format yyyy-mm-dd hh::mm:ss
			'end_time' => null, // date time format yyyy-mm-dd hh::mm:ss
			'recordsAdded' => false,
			'log' => [
				'products' => [], //single dimentional array of products selected for archive
				'errors' => [], //single dimentional indexed array errors
				'optional_msgs' => ["Selected month/year: $moName $year" ], //single dimentional indexed array of additional optional messages
			]
		];
		$reportData = [];

		try {
			/*
			* Expected CSV headers and data structure
				[
					MID						0 => '1239220055001128',
					DBA						1 => 'FOOTHILL PRESBYTERIAN HOS',
					Number of Net Sales		2 => '601',
					Amount of Net Sales		3 => '110,758.81',
					Number of Gross Sales	4 => '645',
					Amount of Gross Sales 	5 => '125120.76',
					Total Income 			6 => '$2,659.31',
					Card Brand COGS 		7 => '1,671.37',
					Processor COGS 			8 => '21.35',
					Sponsor Bank COGS 		9 => '$12.18',
					i3 Monthly COGS 		10 => '$2.50'    
				];
				NOTE: COGS = Cost of Goods Sold
			*/
			$dataArray = $this->extractCsvToArray($datafile, true);
			$totalResidualComp = '((SUM("ResidualReport"."rep_gross_profit") + ';
			$totalResidualComp .= 'SUM("ResidualReport"."partner_gross_profit") + ';
			$totalResidualComp .= 'SUM("ResidualReport"."partner_rep_gross_profit") + ';
			$totalResidualComp .= 'SUM("ResidualReport"."manager_gross_profit") + ';
			$totalResidualComp .= 'SUM("ResidualReport"."manager2_gross_profit") + ';
			$totalResidualComp .= 'SUM("ResidualReport"."referrer_gross_profit") + ';
			$totalResidualComp .= 'SUM("ResidualReport"."reseller_gross_profit"))) as "ResidualReport__total_residual_comp"';
			//For now we are only interested in credit products
			$rProdIds = array_keys($this->Merchant->ProductsAndService->ProductsServicesType->find('creditProductsList'));
			$residualData = $this->Merchant->find('all', [
				'fields' => [
					'Merchant.id',
					'Merchant.merchant_mid',
					'ResidualReport.merchant_id',
					'SUM("ResidualReport"."partner_gross_profit") AS "ResidualReport__partner_gross_profit"',
					$totalResidualComp
				],
				'conditions' => [
					'ResidualReport.products_services_type_id' => $rProdIds,
					'ResidualReport.r_year' => $year,
					'ResidualReport.r_month' => $month,
					'Merchant.merchant_mid' => preg_replace("/\D/i", '', Hash::extract($dataArray, '{n}.0')),//sanitized MID's
				],
				'joins' => [
					[
						'table' => 'residual_reports',
						'alias' => 'ResidualReport',
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = ResidualReport.merchant_id',
						]
					]
				],
				'group' => [
					'Merchant.id',
					'Merchant.merchant_mid',
					'ResidualReport.merchant_id',
				]
			]);
			$residualData = Hash::combine($residualData, '{n}.Merchant.merchant_mid', '{n}');
			foreach ($dataArray as $data) {
				$mid = $data[0];
				$merchantId = null;
				if (empty(Hash::get($residualData, $mid))) {
					$response['log']['optional_msgs'][] = "Notice: No residual data was found for merchant $mid";
					$merchantId = $this->Merchant->field('id', ['merchant_mid' => preg_replace("/\D/i", '', $mid)]);
					if (empty($merchantId)) {
						throw New Exception("Unable to process profitability data! CSV file contains merchant $mid which does not exist!");
					}
				}
					$record = [];
					$record['merchant_id'] = ($merchantId)?:Hash::get($residualData, "{$mid}.ResidualReport.merchant_id");
					$record['year'] = (int)$year;
					$record['month'] = (int)$month;
					$record['net_sales_item_count'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "2"));
					$record['net_sales_vol'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "3"));
					$record['gross_sales_item_count'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "4"));
					$record['gross_sales_vol'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "5"));
					$record['total_income'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "6"));
					$record['card_brand_cogs'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "7"));
					$record['processor_cogs'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "8"));
					$record['sponsor_bank_cogs'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "9"));
					$record['ithree_monthly_cogs'] = preg_replace('/(,)|(\$)/i', '', Hash::get($data, "10"));
					$record['axia_net_income'] = bcsub($record['total_income'], $record['card_brand_cogs'], 3);
					$record['cost_of_goods_sold'] = bcadd(bcadd($record['processor_cogs'], $record['sponsor_bank_cogs'],3), $record['ithree_monthly_cogs'], 3);
					$record['axia_gross_profit'] = bcsub(bcsub($record['axia_net_income'], $record['cost_of_goods_sold'], 3), Hash::get($residualData, "{$mid}.ResidualReport.partner_gross_profit"), 3);
					$record['total_residual_comp'] = bcadd(Hash::get($residualData, "{$mid}.ResidualReport.total_residual_comp"), 0, 4); //using bcadd here just to set number of decimals to 4
					$record['axia_net_profit'] = bcsub($record['axia_gross_profit'], $record['total_residual_comp'], 3);
					$reportData[] = $record;
			}

			if (!empty($reportData)) {
				if (!$this->saveMany($reportData, ['atomic' => true])) {
					if (!empty($this->validationErrors)) {
						$response['errors'] = array_merge(Hash::extract($response, 'errors'), Hash::extract($this->validationErrors, '{n}.{s}.{n}'));
					}
					$response['recordsAdded'] = false;
					throw new Exception("Failed to save Profitability Report data");
				} else {
					$response['recordsAdded'] = true;
				}
			} else {
				$response['recordsAdded'] = false;
			}
		} catch (Exception $e) {
			$response['result'] = false;
			$response['errors'][] = $e->getMessage() . " line " . $e->getLine() . "\n" . $e->getTraceAsString();
			$response['end_time'] = date('Y-m-d H:i:s');

			return $response;
		}
		$response['end_time'] = date('Y-m-d H:i:s');
		return $response;
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
	public function isValidCsvContent($path) {
		$fh = fopen($path, 'rb');
		//move pointer past headers row
		fgetcsv($fh, 10000);

		while ($row = fgetcsv($fh, 10000)) {
			$row = array_map('trim', $row);
			//Minimum number of coulmns and Check MID col is not empty and is numeric
			if (count($row) < 11 || !is_numeric(Hash::get($row, '0'))) {
				fclose($fh);
				unlink($path);
				return false;
			}
		}
		fclose($fh);
		return true;
	}

/**
 * _findProfitability
 *
 * @param string $state operational state
 * @param array $query query
 * @param array $results result set
 *
 * @return array $results
 */
	protected function _findProfitability($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = [
				'ProfitabilityReport.*',
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Client.client_id_global',
				'((SUM("ResidualReport"."rep_gross_profit"))) AS "ResidualReport__pr_total_rep_gp"',
				'((SUM("ResidualReport"."partner_gross_profit"))) AS "ResidualReport__pr_total_partner_gp"',
				'((SUM("ResidualReport"."partner_rep_gross_profit"))) AS "ResidualReport__pr_total_partner_rep_gp"',
				'((SUM("ResidualReport"."manager_gross_profit"))) AS "ResidualReport__pr_total_sm_gp"',
				'((SUM("ResidualReport"."manager2_gross_profit"))) AS "ResidualReport__pr_total_sm2_gp"',
				'((SUM("ResidualReport"."referrer_gross_profit"))) AS "ResidualReport__pr_total_referrer_gp"',
				'((SUM("ResidualReport"."reseller_gross_profit"))) AS "ResidualReport__pr_total_reseller_gp"',
			];
			$query['group'] = [
				'ProfitabilityReport.id',
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Client.client_id_global',
			];

			//For now we are only interested in credit products from residual report
			$rProdIds = array_keys($this->Merchant->ProductsAndService->ProductsServicesType->find('creditProductsList'));
			$query['joins'] = [
				[
					'table' => 'merchants',
					'alias' => 'Merchant',
					'type' => 'LEFT',
					'conditions' => [
						'ProfitabilityReport.merchant_id = Merchant.id'
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
					'table' => 'residual_reports',
					'alias' => 'ResidualReport',
					'type' => 'LEFT',
					'conditions' => [
						'ResidualReport.merchant_id = Merchant.id',
						'ResidualReport.products_services_type_id' => $rProdIds,
						'ResidualReport.r_year = ProfitabilityReport.year',
						'ResidualReport.r_month = ProfitabilityReport.month',
					]
				]

			];
			return $query;
		}

		return $results;
	}

/**
 * Return the values for the profitability report
 *
 * @param array $residualReports array with the residual reports to process
 * @param boolean $summarized if true a summarized version containing only totals will be returned. 
 * @return array
 */
	public function getReportData($profitReports = []) {
		$profitReports = $this->_processReportData($profitReports);
		return $profitReports;
	}


/**
 * Format the report data
 *
 * @param array $profitReports ProfitabilityReport data
 * @throws InvalidArgumentException
 * @return array
 */
	protected function _processReportData($profitReports) {
		if (!is_array($profitReports)) {
			throw new InvalidArgumentException(__('"ProfitabilityReport" data must be an array'));
		}
		$totals = [
			'total_income' => 0,
			'axia_net_income' => 0,
			'cost_of_goods_sold' => 0,
			'axia_gross_profit' => 0,
			'total_residual_comp' => 0,
			'axia_net_profit' => 0,
		];
		$profitability = [];
		foreach($profitReports as $profitReport) {
			$repGp = (Hash::get($profitReport, 'ResidualReport.pr_total_rep_gp'))?: Hash::get($profitReport, 'ResidualReport.pr_total_partner_rep_gp');
			$date = Hash::get($profitReport, 'ProfitabilityReport.year') . '-' .  Hash::get($profitReport, 'ProfitabilityReport.month') .'-' . '01';
			
			$montYear = date('M, Y', strtotime($date));
			$profitability[] = [
				'merchant_mid' => Hash::get($profitReport, 'Merchant.merchant_mid'),
				'merchant_dba' => Hash::get($profitReport, 'Merchant.merchant_dba'),
				'client_id_global' => Hash::get($profitReport, 'Client.client_id_global'),
				'mo_year' => $montYear,
				'merchant_dba' => Hash::get($profitReport, 'Merchant.merchant_dba'),
				'net_sales_item_count' => Hash::get($profitReport, 'ProfitabilityReport.net_sales_item_count'),
				'net_sales_vol' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.net_sales_vol'), 'USD3dec'),
				'gross_sales_item_count' => Hash::get($profitReport, 'ProfitabilityReport.gross_sales_item_count'),
				'gross_sales_vol' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.gross_sales_vol'), 'USD3dec'),
				'total_income' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.total_income'), 'USD3dec'),
				'card_brand_cogs' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.card_brand_cogs'), 'USD3dec'),
				'processor_cogs' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.processor_cogs'), 'USD3dec'),
				'sponsor_bank_cogs' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.sponsor_bank_cogs'), 'USD3dec'),
				'ithree_monthly_cogs' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.ithree_monthly_cogs'), 'USD3dec'),
				'axia_net_income' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.axia_net_income'), 'USD3dec'),
				'cost_of_goods_sold' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.cost_of_goods_sold'), 'USD3dec'),
				'pr_total_partner_gp' => CakeNumber::currency(Hash::get($profitReport, 'ResidualReport.pr_total_partner_gp'), 'USD3dec'),
				'axia_gross_profit' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.axia_gross_profit'), 'USD3dec'),
				'pr_total_rep_gp' => CakeNumber::currency($repGp, 'USD3dec'),
				'pr_total_sm_gp' => CakeNumber::currency(Hash::get($profitReport, 'ResidualReport.pr_total_sm_gp'), 'USD3dec'),
				'pr_total_sm2_gp' => CakeNumber::currency(Hash::get($profitReport, 'ResidualReport.pr_total_sm2_gp'), 'USD3dec'),
				'pr_total_referrer_gp' => CakeNumber::currency(Hash::get($profitReport, 'ResidualReport.pr_total_referrer_gp'), 'USD3dec'),
				'pr_total_reseller_gp' => CakeNumber::currency(Hash::get($profitReport, 'ResidualReport.pr_total_reseller_gp'), 'USD3dec'),
				'total_residual_comp' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.total_residual_comp'), 'USD3dec'),
				'axia_net_profit' => CakeNumber::currency(Hash::get($profitReport, 'ProfitabilityReport.axia_net_profit'), 'USD3dec'),
			];
			$totals['total_income'] = bcadd($totals['total_income'], Hash::get($profitReport, 'ProfitabilityReport.total_income'), 3);
			$totals['axia_net_income'] = bcadd($totals['axia_net_income'], Hash::get($profitReport, 'ProfitabilityReport.axia_net_income'), 3);
			$totals['cost_of_goods_sold'] = bcadd($totals['cost_of_goods_sold'], Hash::get($profitReport, 'ProfitabilityReport.cost_of_goods_sold'), 3);
			$totals['axia_gross_profit'] = bcadd($totals['axia_gross_profit'], Hash::get($profitReport, 'ProfitabilityReport.axia_gross_profit'), 3);
			$totals['total_residual_comp'] = bcadd($totals['total_residual_comp'], Hash::get($profitReport, 'ProfitabilityReport.total_residual_comp'), 3);
			$totals['axia_net_profit'] = bcadd($totals['axia_net_profit'], Hash::get($profitReport, 'ProfitabilityReport.axia_net_profit'), 3);
		}
		return [
			'profitReports' => $profitability,
			'totals' => $totals
		];

	}

/**
 * getExistingList
 *
 * @param string $state operational state
 * @param array $query query
 * @param array $results result set
 *
 * @return array $results
 */
	public function getExistingList($year, $month = null) {
		$conditions = ['year' => $year];
		if (!empty($month)) {
			$conditions['month'] = $month;
		}
		$existingList = $this->find('all', [
			'fields' => ['year', 'month'],
			'conditions' => [$conditions],
			'group' => ['year', 'month'],
			'order' => ['month ASC']
		]);

		return $existingList;
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
			'subject' => 'Profitability Report Import Notice',
			'emailBody' => $response
		]);

		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
	}
}
