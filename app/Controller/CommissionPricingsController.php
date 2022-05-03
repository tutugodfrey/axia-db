<?php

App::uses('AppController', 'Controller');

/**
 * CommissionPricings Controller
 *
 * @property CommissionPricing $CommissionPricing
 */
class CommissionPricingsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = [
		'Search.Prg' => [
			'commonProcess' => ['paramType' => 'querystring'],
			'presetForm' => ['paramType' => 'querystring']
		],
		'GUIbuilder',
		'RequestHandler' => [
			'viewClassMap' => ['csv' => 'CsvView.Csv']
		]
	];

/**
 * gross profit estimate report
 *
 * @return void
 */
	public function grossProfitReport() {
		$this->Prg->commonProcess();

		$filterParams = $this->Prg->parsedParams();
		if (empty($this->request->query)) {
			$filterParams = $this->CommissionPricing->getDefaultSearchValues();
		} elseif (empty($this->request->query['user_id'])) {
			$filterParams['user_id'] = $this->CommissionPricing->getDefaultUserSearch();
		}
		if (is_null($this->request->query('roll_up_view'))) {
			$this->request->query['roll_up_view'] = true;
		}
		$this->request->data['CommissionPricing']['roll_up_view'] = $this->request->query('roll_up_view');

		$filterParams['report_type'] = CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE;
		if (empty($this->request->data['CommissionPricing'])) {
			$this->request->data['CommissionPricing'] = $filterParams;
		}
		$this->request->data['Merchant'] = [
			'organization_id' => Hash::get($filterParams, 'organization_id'),
			'region_id' => Hash::get($filterParams, 'region_id'),
			'subregion_id' => Hash::get($filterParams, 'subregion_id'),
		];
		//set fiterArgs
		$complexUserIdArgs = $this->CommissionPricing->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$this->CommissionPricing->filterArgs['user_id'] = $this->CommissionPricing->getUserSearchFilterArgs($complexUserIdArgs);

		$users = $this->CommissionPricing->User->getEntityManagerUsersList(true);
		$productsServicesTypes = $this->CommissionPricing->ProductsServicesType->buildDdOptions(true);
		$limit = $this->CommissionPricing->find('count');
		$getRolledUpData = $this->request->data['CommissionPricing']['roll_up_view'];
		$totalRecords = $this->CommissionPricing->find('count', [
			'type' => 'grossProfitEstimate',
			'conditions' => $this->CommissionPricing->parseCriteria($filterParams),
		]);		
		$this->Paginator->settings = [
			'findType' => 'grossProfitEstimate',
			'conditions' => $this->CommissionPricing->parseCriteria($filterParams),
			'limit' => $limit,
			'maxLimit' => $limit,
			'roll_up_data' => $getRolledUpData
		];
		//Increase memory, this report can be very big.
		ini_set('memory_limit', '512M');
		$grossProfitEstimates = $this->paginate();

		$this->set($grossProfitEstimates);
		$organizations = $this->CommissionPricing->Merchant->Organization->find('list');
		$regions = $this->CommissionPricing->Merchant->Region->find('list');
		$subregions = $this->CommissionPricing->Merchant->Subregion->find('list');
		$labels = $this->CommissionPricing->gprColLabels;
		$this->set(compact('users', 'productsServicesTypes', 'organizations', 'regions', 'subregions', 'getRolledUpData', 'labels', 'totalRecords'));

		if (Hash::get($this->request->params, 'ext') === 'csv') {
			$this->GUIbuilder->setCsvView($filterParams);
			$this->response->download('gross-profit-report.csv');
		}
	}

/**
 * gross profit estimate report
 *
 * @return void
 */
	public function commission_multiple_analysis() {
		$this->_generateReport(CommissionPricing::REPORT_CM_ANALYSIS);
	}

/**
 * gross profit estimate report
 *
 * @return void
 */
	public function gp_analysis() {
		$this->_generateReport(CommissionPricing::REPORT_GP_ANALYSIS);
	}

	protected function _generateReport($reportType){
		$this->Prg->commonProcess();

		$filterParams = $this->Prg->parsedParams();
		if (empty($this->request->query)) {
			$filterParams = $this->CommissionPricing->getDefaultSearchValues();
		} elseif (empty($this->request->query['user_id'])) {
			$filterParams['user_id'] = $this->CommissionPricing->getDefaultUserSearch();
		}
		$this->request->data['Merchant'] = [
			'organization_id' => Hash::get($filterParams, 'organization_id'),
			'region_id' => Hash::get($filterParams, 'region_id'),
			'subregion_id' => Hash::get($filterParams, 'subregion_id'),
		];

		$filterParams['res_months'] = Hash::get($this->request->query, 'res_months');
		if ($reportType === CommissionPricing::REPORT_CM_ANALYSIS) {
			$filterParams['date_type'] = Hash::get($this->request->query, 'date_type');
		}
		$cpFilterParams = $this->CommissionPricing->getAnalysisFilterParams($filterParams, true, $reportType);
		$rFilterParams = $this->CommissionPricing->getAnalysisFilterParams($filterParams);

		$this->request->data['CommissionPricing'] = $cpFilterParams;

		//set fiterArgs
		$complexUserIdArgs = $this->CommissionPricing->User->extractComplexId(Hash::get($cpFilterParams, 'user_id'));
		$this->CommissionPricing->Merchant->ResidualReport->filterArgs['user_id'] = $this->CommissionPricing->Merchant->ResidualReport->getUserSearchFilterArgs($complexUserIdArgs);

		$users = $this->CommissionPricing->User->getEntityManagerUsersList(true);
		$productsServicesTypes = $this->CommissionPricing->ProductsServicesType->buildDdOptions(true);
		$productsServicesTypes = array_merge(['commissionable' => "Commissionable Products"], $productsServicesTypes);
		$dateTypes = $this->CommissionPricing->getDateTypeOptions();
		$sortField = Hash::get($this->request->params, 'named.sort');
		$order = ($sortField)? [$sortField => Hash::get($this->request->params, 'named.direction')] : ['Merchant.merchant_mid' => 'asc'];

		ini_set('memory_limit', '10G');
		$cpFilterParams['report_type'] = $reportType;
		$cpFilterParams['order'] = $order;
		$reportProjeted = $this->CommissionPricing->getCommissionMultipleData($cpFilterParams);

		$this->CommissionPricing->Merchant->ResidualReport->setPaginatorVirtualFields();
		$conditions = $this->CommissionPricing->Merchant->ResidualReport->parseCriteria($rFilterParams);

		if (!empty($reportProjeted['commissionMultiples'])) {
			$conditions['Merchant.merchant_mid'] = array_unique(Hash::extract($reportProjeted, 'commissionMultiples.{n}.mid'));
		}

		$residualData = $this->CommissionPricing->Merchant->ResidualReport->find('gpaResiduals', [
				'conditions' => $conditions,
				'order' => $order,
				'report_type' => $reportType
			]
		);

		$reportGpa = $this->CommissionPricing->Merchant->ResidualReport->getGpaReportData($residualData, $reportProjeted, $rFilterParams['res_months'], $reportType);
		if (Hash::get($this->request->params, 'ext') === 'csv') {
			$this->GUIbuilder->setCsvView($cpFilterParams);
			if ($reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
				$dlFileName = 'gross-profit-analysis.csv';
			} elseif ($reportType === CommissionPricing::REPORT_CM_ANALYSIS) {
				$dlFileName = 'commission_multiple_analysis.csv';
			}
			$this->response->download($dlFileName);
		}
		$this->set($reportProjeted);
		$this->set($reportGpa);
		$organizations = $this->CommissionPricing->Merchant->Organization->find('list');
		$regions = $this->CommissionPricing->Merchant->Region->find('list');
		$subregions = $this->CommissionPricing->Merchant->Subregion->find('list');
		$partners = [];
		if ($reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
			$partners = $this->CommissionPricing->User->getByRole(User::ROLE_PARTNER);			
		}
		$this->set(compact('users', 'productsServicesTypes', 'dateTypes', 'organizations', 'regions', 'subregions', 'partners'));
	}
}
