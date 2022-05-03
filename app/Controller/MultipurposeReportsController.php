<?php

App::uses('AppController', 'Controller');

/**
 * MultipurposeReports Controller
 *
 */
class MultipurposeReportsController extends AppController {

/**
 * Components in use
 *
 * @var array
 */
	public $components = array('CsvHandler');

/**
 * report method
 * Renders view and builds merchant_data_export.csv report file on post request
 *
 * @return null
 */
	public function report() {
		if ($this->request->is('post')) {
			$conditions = array();
			$active = $this->request->data['MultipurposeReport']['active'];
			$entity = $this->request->data['MultipurposeReport']['entity_id'];
			if (!empty($entity)) {
				$conditions['Merchant.entity_id'] = $entity;
			}
			if ($active == 0 || $active == 1) {
				$conditions['Merchant.active'] = $active;
			}

			if ($this->request->data('MultipurposeReport.mask_secured_data') == false) {
				$this->MultipurposeReport->logUnsecureExport($this->Auth->user(), $this->request->data);
			}

			$results = $this->MultipurposeReport->getReport($conditions, $this->request->data('MultipurposeReport.mask_secured_data'));
			date_default_timezone_set('America/Los_Angeles');
			$this->CsvHandler->saveCsvStrToFile("merchant_data_export_" . $this->request->data['MultipurposeReport']['cl_date_time'], $results);
			return $this->response;
		}
		$this->request->data['MultipurposeReport']['mask_secured_data'] = true;
		$entities = ClassRegistry::init('Entity')->getList();
		$this->set(compact('entities'));
	}
}