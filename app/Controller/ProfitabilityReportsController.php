<?php
App::uses('AppController', 'Controller');
/**
 * ProfitabilityReports Controller
 *
 * @property ProfitabilityReport $ProfitabilityReport
 * @property PaginatorComponent $Paginator
 */
class ProfitabilityReportsController extends AppController {

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
		'GUIbuilder'
	];
/**
 * report
 * Gets report data to display in view
 *
 */
 	public function report() {
 		$this->Prg->commonProcess();
		$filterParams = $this->Prg->parsedParams();
		if (empty($this->request->data['ProfitabilityReport'])) {
			$this->request->data['ProfitabilityReport'] = $filterParams;
		}
		$parsedConditions = $this->ProfitabilityReport->parseCriteria($filterParams);
		$resultCount = 0;
		$profitReports = [];
		if (!empty($parsedConditions)) {
			$resultCount = $this->ProfitabilityReport->find('count', array(
				'type' => 'profitability',
				'conditions' => $parsedConditions
			));
			//Set sort order
			$sortField = Hash::get($this->request->params, 'named.sort');
			$order = ($sortField)? [$sortField => Hash::get($this->request->params, 'named.direction')] : ['Merchant.merchant_mid' => 'asc'];
			$this->Paginator->settings = [
				'findType' => 'profitability',
				'conditions' => $parsedConditions,
				//add one to prevent division by zero error when $resultCount === 0
				'limit' => $resultCount + 1,
				'maxLimit' => $resultCount + 1,
				'order' => $order
			];

			$profitReports = $this->paginate();
			if (Hash::get($this->request->params, 'ext') === 'csv') {
				$this->GUIbuilder->setCsvView($filterParams);
				$this->response->download('profitability-report.csv');
			}
		}

		$this->set(compact('resultCount'));
		$this->set($this->ProfitabilityReport->getReportData($profitReports));
 	}

/**
 * import
 * Persists data for report from csv file
 *
 */
 	public function import() {
 		if ($this->request->is('post') || $this->request->is('put')) {
 			try {
 				$result = $this->ProfitabilityReport->importData($this->request->data);
 				if ($this->request->data['ProfitabilityReport']['serverside_processing'] == 0) {
 					if ($result['result'] === false) {
 						$this->Session->setFlash($result['errors'], 'Flash/listErrors', ['class' => 'alert alert-danger']); 						
 					} else {
 						$this->_success(__("Data imported successfully!"), $this->referer(), ['class' => 'alert alert-success']);
 					}
 				} else {
 					$this->_success(__("Process will continue on the server and you will receive an email when it is complete!"), $this->referer(), ['class' => 'alert alert-info']);
 				}
 			} catch(Exception $e) {
 				$this->_failure(__($e->getMessage()), $this->referer(), ['class' => 'alert alert-danger']);
 			}
 		}
		$year = (Hash::get($this->request->query, 'pr_years.year'))?: date('Y');
		$this->request->query['pr_years']['year'] = $year;
		$month = Hash::get($this->request->query, 'pr_months.month');
		$prList = $this->ProfitabilityReport->getExistingList($year, $month);
		//persist search params submitted by user
		$this->request->data['ProfitabilityReport'] = $this->request->query;

 		$this->set(compact('prList'));
 	}

/**
 * delete method
 *
 * Deletes all data based on submitted conditions
 *
 * @throws MethodNotAllowedException 
 * @return void
 */
	public function delete($year, $month) {
		if (!$this->request->is('post')) {
			$this->_failure(__('Request method now allowed.'), $this->referer());
		}

		if (!$this->ProfitabilityReport->hasAny(['year' => $year, 'month' => $month])) {
			$this->_failure(__("Nothing to delete! No Data exist for that month and year"), $this->referer());
		}
		
		if ($this->ProfitabilityReport->deleteAll(['year' => $year, 'month' => $month])) {
			$this->_success(__("Data successfully deleted!"), $this->referer());
		}
		$this->_failure(__('Something went wrong, could not delete data! Please try again.'), $this->referer());
	}
}
