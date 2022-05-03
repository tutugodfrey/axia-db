<?php

App::uses('AppController', 'Controller');

/**
 * DataBreachBillingReports Controller
 *
 */
class DataBreachBillingReportsController extends AppController {

/**
 * report method
 *
 * @return null
 */
	public function report() {
		$results = null;

		if ($this->params->query('type') == DataBreachBillingReport::REPORT_AXIA_TECH) {
			$this->set('type', 'axiatech');
			$this->layout = 'ajax';
			$this->response->download("axia_tech_data_program_merchants_" . date('Y-m-d') . ".csv");
			$results = $this->DataBreachBillingReport->getAxiaTechReport();
		} elseif ($this->params->query('type') == DataBreachBillingReport::REPORT_AXIA_PAYMENTS) {
			$this->set('type', 'axiapayments');
			$this->layout = 'ajax';
			$this->response->download("merchants_" . date('Y-m-d') . ".txt");
			$results = $this->DataBreachBillingReport->getAxiaPaymentsReport();
		}

		$this->set('results', $results);
	}
}