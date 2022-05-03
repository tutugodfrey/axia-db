<?php

App::uses('AppController', 'Controller');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * ResidualReports Controller
 *
 */
class ResidualReportsController extends AppController {

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
 * upload method
 *
 * @return null
 */
	public function upload() {
		$years = $this->GUIbuilder->getDatePartOptns('y');
		$months = $this->GUIbuilder->getDatePartOptns('m');
		arsort($years);

		if ($this->request->is('post')) {
			$reportYear = h($this->request->data['ResidualReport']['r_year']);
			$reportMonth = h($this->request->data['ResidualReport']['r_month']);
			$reportProduct = $this->ResidualReport->ProductsServicesType->find('first', [
					'conditions' => ['id' => $this->request->data['ResidualReport']['products_services_type_id']]
				]);
			if (!empty($this->request->data['ResidualReport']['residualReportFile']['tmp_name']) &&
				is_uploaded_file($this->request->data['ResidualReport']['residualReportFile']['tmp_name'])) {

				$filename = basename($this->request->data['ResidualReport']['residualReportFile']['tmp_name']);
				$filepath = APP . 'tmp' . DS;
				$name = $this->request->data['ResidualReport']['residualReportFile']['name'];
				$validFileType = preg_match('/\.csv$/', $name);

				if ($validFileType) {
					$response = $this->ResidualReport->moveUploadedFile(
						$this->request->data['ResidualReport']['residualReportFile']['tmp_name'],
						$filepath,
						$filename
					);

					if ($response) {
						$filepath .= $filename;
						if ($this->ResidualReport->isValidCsvDataUpload($filepath) === false) {
							$this->Session->setFlash(__("** ERROR: File contents are not valid! Please ensure that the first 4 columns are 'MID, DBA, Item Count, Volume' in that order, and that all values under the MID column are numbers."),
								'default', ['class' => 'alert alert-danger strong']);
							$this->redirect($this->referer());
						}
						// check if this data has already been uploaded or not.
						try {
							$reportExists = $this->ResidualReport->dataExists($filepath, $reportYear, $reportMonth, $reportProduct['ProductsServicesType']['id']);
						} catch (Exception $e) {
							$this->_failure(__($e->getMessage()), ['action' => 'upload']);
						}
						if ($reportExists) {
							unlink($filepath);
							$this->_failure(__('Data for ' . $reportProduct['ProductsServicesType']['products_services_description'] . ': ' . $months[$reportMonth] . ' ' . $reportYear . ' already exists for some or all merchants in this CSV file.'), ['action' => 'upload']);
						}

						// this can be a very long process, so queue it and notify the user when done
						$BackgroundJob = ClassRegistry::init('BackgroundJob');
						$jobTrackerId = $BackgroundJob->addToQueue('Residual upload ' . $reportProduct['ProductsServicesType']['products_services_description']);
						if ($jobTrackerId === false) {
							$this->_failure(__('Failed to create a Background Job Tracker for this process! Try again later.'), ['action' => 'upload']);
							unlink($filepath);
						}
						CakeResque::enqueue(
							'residualAdminQueue',
							'ResidualAdminShell',
							['processResidualAdminJob', $reportYear, $reportMonth, $reportProduct, $filepath, $this->Auth->user('user_email'), $jobTrackerId]
						);
						$this->Session->setFlash(__('<h5>The job is now running on the server, once it is finished an email' .
							'<br />notification will be sent to ' . h($this->Auth->user('user_email')) . ".</h5>"), 'default', ['class' => 'alert alert-success']);
					} else {
						$this->set('response', __('There appears to be a problem with the file upload'));
					}
				} else {
					$this->set('response', __('File type not allowed, please make sure that you are uploading the correct document.'));
				}
			} else {
				$this->set('response', __('There appears to be a problem with your document, please make sure that you are uploading the correct document. If the problem persists, please contact ' .
					' <a href="mailto:helpdesk@axiapayments.com">helpdesk@axiapayments.com</a>'));
			}
		}

		$conditions = [];
		$conditions['r_year'] = date('Y');
		
		if (!empty($this->params->query['r_year'])) {
			$conditions['r_year'] = $this->params->query['r_year'];
			$yearSelected = h($this->params->query['r_year']);
		} 

		if (!empty($this->params->query['r_month'])) {
			$conditions['r_month'] = $this->params->query['r_month'];
			$this->set('monthSelected', $this->params->query['r_month']);
		}
				//plus one to prevent Paginator division by zero when there is zero count
		$allCount = $this->ResidualReport->find('count', ['conditions' => $conditions]) + 1;
		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['limit'] = $allCount;
		$this->Paginator->settings['maxLimit'] = $allCount;
		// grab results from the custom finder _findIndex and pass them to the paginator
		$this->Paginator->settings['findType'] = 'index';
		$residualReportList = $this->Paginator->paginate();
		if (!isset($yearSelected)) {
			$yearSelected = h(Hash::get($residualReportList, '0.ResidualReport.r_year', 0));
		}

		$productsServicesTypes = ClassRegistry::init('MerchantPricingArchive')->getArchivableProducts();

		

		$this->set(compact('productsServicesTypes', 'years', 'months', 'residualReportList', 'yearSelected'));
		$this->set('statusActive', ResidualReport::STATUS_ACTIVE);
		$this->set('statusInactive', ResidualReport::STATUS_INACTIVE);
	}

/**
 * toggleStatus method
 *
 * @param string $currentSatus mist be ResidualReport::STATUS_ACTIVE or  ResidualReport::STATUS_INACTIVE
 * @param string $year Residual Report year
 * @param string $month Residual Report month
 * @param string $productId Residual Report products_services_type_id
 * @throws MethodNotAllowedException
 * @return void
 */

	public function toggleStatus($currentSatus, $year, $month, $productId) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		if (($currentSatus === ResidualReport::STATUS_ACTIVE || $currentSatus === ResidualReport::STATUS_INACTIVE) &&
			!empty($year) && !empty($month) && !empty($productId)) {
			$newSatus = ($currentSatus === ResidualReport::STATUS_ACTIVE)? ResidualReport::STATUS_INACTIVE : ResidualReport::STATUS_ACTIVE;
			$updated = $this->ResidualReport->updateAll(
				array(
					'ResidualReport.status' => "'$newSatus'"
				),
				array(
					'ResidualReport.r_year' => $year,
					'ResidualReport.r_month' => $month,
					'ResidualReport.products_services_type_id' => $productId
				)
			);
		}

		if ($updated) {
				$this->_success(__('Successfully toggled active status'), ['action' => 'upload']);
		} else {
			$this->_falure(__('Could not change status using given parameters.'), ['action' => 'upload']);
		}
	}

/**
 * deleteMany method
 *
 * Deletes all ResidualReport data based on submitted JSON string conditions
 *
 * @throws MethodNotAllowedException 
 * @return void
 */
	public function deleteMany() {
		$passedJsonParams = json_decode($this->request->data['ResidualReport']['json_delete_data'], true);
		$conditions = Hash::filter($passedJsonParams);
		try {
			if ($this->ResidualReport->deleteMany($conditions)) {
				$msg = "All Residual Report data deleted for the selected month(s) and products.";
				$this->Session->setFlash(__($msg), 'default', array('class' => 'alert alert-success strong'));
				$this->redirect(array('action' => 'upload'));
			} else {
				$this->_failure(__('ERROR: Failed to Delete records!'), array('action' => 'upload'));
			}
		} catch (InvalidArgumentException $e) {
			$this->Session->setFlash(__('ERROR: ' . $e->getMessage()), 'default', array('class' => 'alert alert-danger strong'));
			$this->redirect(array('action' => 'upload'));
		}
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
			$filterParams = $this->ResidualReport->getDefaultSearchValues();
		} elseif (empty($this->request->query['user_id'])) {
			$filterParams['user_id'] = $this->ResidualReport->User->getDefaultUserSearch();
		}

		$complexUserIdArgs = $this->ResidualReport->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$this->ResidualReport->filterArgs['user_id'] = $this->ResidualReport->getUserSearchFilterArgs($complexUserIdArgs);
		return $filterParams;
	}

/**
 * report method
 *
 * @return void
 */
	public function report() {
		$filterParams = $this->_getFilterParams();
		if (empty($this->request->data['ResidualReport'])) {
			$this->request->data['ResidualReport'] = $filterParams;
		}
		if (is_null($this->request->query('roll_up_view'))) {
			$this->request->query['roll_up_view'] = false;
		}
		$this->request->data['ResidualReport']['roll_up_view'] = $this->request->query('roll_up_view');
		$this->request->data['Merchant'] = [
			'organization_id' => Hash::get($filterParams, 'organization_id'),
			'region_id' => Hash::get($filterParams, 'region_id'),
			'subregion_id' => Hash::get($filterParams, 'subregion_id'),
		];

		$parsedConditions = $this->ResidualReport->parseCriteria($filterParams);
		$parsedConditions['ResidualReport.status'] = RolePermissions::getAllowedReportStatuses($this->Auth->user('id'));

		$resultCount = $this->ResidualReport->find('count', array(
			'type' => 'residualReport',
			'conditions' => $parsedConditions
		));

		$getSummarizedReport = false;
		$getRolledUpReport = $this->request->data['ResidualReport']['roll_up_view'];
		//Increase memory, this report can be very big
		ini_set('memory_limit', '10G');
		set_time_limit(0);

		if ($resultCount > ResidualReport::MAX_RESULTS && Hash::get($this->request->params, 'ext') !== 'csv') {
			$reportElementPath = 'ResidualReports/summarized_content';
			$getSummarizedReport = true;
			$residualReports = $this->ResidualReport->getSummarizedReportData($parsedConditions);

		} else {
			$reportElementPath = 'ResidualReports/content';
			if ($getRolledUpReport) {
				$reportElementPath = 'ResidualReports/rollup_report_content';
			}
			//Set sort order
			$sortField = Hash::get($this->request->params, 'named.sort');
			$order = ($sortField)? [$sortField => Hash::get($this->request->params, 'named.direction')] : ['Merchant.merchant_mid' => 'asc'];
			$this->Paginator->settings = [
				'findType' => 'residualReport',
				'conditions' => $parsedConditions,
				//add one to prevent division by zero error when $resultCount === 0
				'limit' => $resultCount + 1,
				'maxLimit' => $resultCount + 1,
				'order' => $order
			];
			// Sorting on the view will not work unless the the query virtual fields
			// defined in custom find method _findResidualReport are also set
			// here before calling $this->paginate()
			$this->ResidualReport->setPaginatorVirtualFields();
			$residualReports = $this->paginate();
		}

		if (Hash::get($this->request->params, 'ext') === 'csv') {
			$this->GUIbuilder->setCsvView($filterParams);
			$this->response->download('residual-report.csv');
		}
		$this->set(compact('reportElementPath', 'resultCount'));
		$this->set($this->ResidualReport->getReportData($residualReports, $getSummarizedReport, $getRolledUpReport));
	}

}
