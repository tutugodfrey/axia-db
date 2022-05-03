<?php

App::uses('AppController', 'Controller');

/**
 * CommissionReports Controller
 *
 */
class CommissionReportsController extends AppController {

/**
 * Loaded models
 *
 * @var array
 */
	public $uses = [
		'CommissionReport',
		'CommissionPricing'
	];

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
 * build method
 *
 * @return null
 */
	public function build() {
		$years = $this->GUIbuilder->getDatePartOptns('y');
		$months = $this->GUIbuilder->getDatePartOptns('m');

		if ($this->request->is('post')) {
			$reportYear = $this->request->data('CommissionReport.c_year');
			$reportMonth = $this->request->data('CommissionReport.c_month');

			// run a query on the db to see if this data has already been built or not
			$commRepExists = $this->CommissionReport->hasAny(array(
						'CommissionReport.c_year' => $reportYear,
						'CommissionReport.c_month' => $reportMonth
						)
					);
			$commPriExists = $this->CommissionPricing->hasAny(array(
									'CommissionPricing.c_year' => $reportYear,
									'CommissionPricing.c_month' => $reportMonth
									)
								);

			// check if data already exists
			if ($commRepExists || $commPriExists) {
				$this->_failure(__('Data for ' . $months[$reportMonth] . ' ' . $reportYear . ' already exists'));
			} else {
				// this can be a very long process, so queue it and notify the user when done
				$BackgroundJob = ClassRegistry::init('BackgroundJob');
				$jobTrackerId = $BackgroundJob->addToQueue("Build Commissions for $reportMonth/$reportYear");
				if ($jobTrackerId === false) {
					$this->_failure(__('Failed to create a Background Job Tracker for this process! Try again later.'), ['action' => 'index']);
				}
				CakeResque::enqueue(
					'commissionAdminQueue',
					'CommissionAdminShell',
					array('processCommissionAdminJob', $reportYear, $reportMonth, $this->Auth->user('user_email'), $jobTrackerId)
				);

				$this->Session->setFlash(__('The job is now running on the server, once it is finished an email' .
					' notification will be sent to ' . h($this->Auth->user('user_email')) . "."), 'Flash/bgProcessMsg', array('class' => 'text-center shadow alert alert-warning'));
			}
		}

		if (!empty($this->params->query['action'])) {
			if ($this->params->query['action'] == 'deactivate') {
				if (!empty($this->params->query['year']) &&
					!empty($this->params->query['month'])) {

						$this->CommissionReport->updateAll(
							array(
								'CommissionReport.status' => "'I'"
							),
							array(
								'CommissionReport.c_year' => $this->params->query['year'],
								'CommissionReport.c_month' => $this->params->query['month']
							)
						);
				}
			}

			if ($this->params->query['action'] == 'activate') {
				if (!empty($this->params->query['year']) &&
					!empty($this->params->query['month'])) {

						$this->CommissionReport->updateAll(
							array(
								'CommissionReport.status' => "'A'"
							),
							array(
								'CommissionReport.c_year' => $this->params->query['year'],
								'CommissionReport.c_month' => $this->params->query['month']
							)
						);
				}
			}

			if ($this->params->query['action'] == 'delete') {
				if (!empty($this->params->query['year']) &&
					!empty($this->params->query['month'])) {
					$conditions = [
							'c_year' => $this->params->query['year'],
							'c_month' => $this->params->query['month']
						];
					$this->CommissionReport->deleteAll($conditions);
					$this->CommissionPricing->deleteAll($conditions);
				}
			}
		}

		$conditions = array();

		$conditions['c_year'] = date('Y');
		$settings['order'] = 'c_month';

		if (!empty($this->params->query['c_year'])) {
			$conditions['c_year'] = $this->params->query['c_year'];
			$this->set('yearSelected', $this->params->query['c_year']);
		}

		if (!empty($this->params->query['c_month'])) {
			$conditions['c_month'] = $this->params->query['c_month'];
			$this->set('monthSelected', $this->params->query['c_month']);
		}

		$settings['conditions'] = $conditions;
		$this->set('commissionReportList', $this->CommissionReport->getIndexData($settings));

		$this->set('years', $years);
		$this->set('months', $months);
	}

/**
 * Return the filter parameters from Searchable behavior
 *
 * @return array
 */
	protected function _getFilterParams() {
		$this->Prg->commonProcess();

		$filterParams = $this->Prg->parsedParams();
		if (empty($this->request->query)) {
			$filterParams = $this->CommissionReport->getDefaultSearchValues();
		} elseif (empty($this->request->query['user_id'])) {
			$filterParams['user_id'] = $this->CommissionReport->User->getDefaultUserSearch();
		}
		$complexUserIdArgs = $this->CommissionReport->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$this->CommissionReport->filterArgs['user_id'] = $this->CommissionReport->getUserSearchFilterArgs($complexUserIdArgs);
		return $filterParams;
	}

/**
 * Commission report method
 *
 * @return void
 */
	public function report() {
		$filterParams = $this->_getFilterParams();
		if (empty($this->request->data['CommissionReport'])) {
			$this->request->data['CommissionReport'] = $filterParams;
		}
		$this->request->data['Merchant'] = [
			'organization_id' => Hash::get($filterParams, 'organization_id'),
			'region_id' => Hash::get($filterParams, 'region_id'),
			'subregion_id' => Hash::get($filterParams, 'subregion_id'),
		];
		$conditions = $this->CommissionReport->parseCriteria($filterParams);

		$this->Paginator->settings = [
			'findType' => 'commissionReport',
			'conditions' => $conditions,
		];
		//Increase memory, this report can be very big
		ini_set('memory_limit', '10G');
		set_time_limit(0);
		$data = $this->paginate();

		$users = $this->CommissionReport->User->getEntityManagerUsersList(true);
		$productsServicesTypes = $this->CommissionPricing->ProductsServicesType->getList();

		$this->set(compact('users', 'productsServicesTypes'));
		$commMultipleData = $this->CommissionPricing->getCommissionMultipleData($filterParams);

		//insert commission multiple totals to combine with overall income totals
		$data['commissionMultipleTotals'] = Hash::get($commMultipleData, 'commissionMultipleTotals');
		$this->set($this->CommissionReport->setReportVars($data, $filterParams));
		$this->set($commMultipleData);
	}

/**
 * Export the Commission report multiple to csv
 *
 * @return void
 */
	public function exportCommissionReport() {
		$filterParams = $this->_getFilterParams();
		$this->GUIbuilder->setCsvView($filterParams);
		$this->response->download('commission-report.csv');
		$reportData = $this->CommissionReport->getCommissionReportData($filterParams);

		$headers = [];
		// //Get a sample of the data to extract the keys from
		$dataSample = Hash::get($reportData, 'commissionReports.0');
		foreach ($dataSample as $key => $val) {
			$headers[] = Hash::get($this->CommissionReport->csvHeaders, $key);
		}

		$this->set($reportData);
		$this->set('headers', $headers);
	}

/**
 * Export the Commission report multiple to csv
 *
 * @return void
 */
	public function exportCommissionMultiple() {
		$filterParams = $this->_getFilterParams();
		$this->GUIbuilder->setCsvView($filterParams);
		$this->response->download('commission-multiple.csv');
		$this->set($this->CommissionPricing->getCommissionMultipleData($filterParams));
	}
}
