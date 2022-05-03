<?php
App::uses('AppController', 'Controller');
/**
 * ImportedDataCollections Controller
 *
 */
class ImportedDataCollectionsController extends AppController {

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
 * report method
 *
 * @return void
 */
	public function report() {
		$this->Prg->commonProcess();
		$filterParams = $this->Prg->parsedParams();
		$reportData = [];
		if (!empty($filterParams)) {
			//persist any selected organization reated filters
			$this->request->data['Merchant'] = [
				'organization_id' => Hash::get($filterParams, 'organization_id'),
				'region_id' => Hash::get($filterParams, 'region_id'),
				'subregion_id' => Hash::get($filterParams, 'subregion_id'),
			];
			$parsedConditions = $this->ImportedDataCollection->parseCriteria($filterParams);
			$resultCount = $this->ImportedDataCollection->find('count', [
				'type' => 'collection',
				'conditions' => $parsedConditions
			]);
			$sortField = Hash::get($this->request->params, 'named.sort');
			$order = ($sortField)? [$sortField => Hash::get($this->request->params, 'named.direction')] : ['Merchant.merchant_mid' => 'asc'];
			$order['ImportedDataCollection.month'] = 'asc';
			$this->Paginator->settings = [
				'findType' => 'collection',
				'conditions' => $parsedConditions,
				//add one to prevent division by zero error when $resultCount === 0
				'limit' => $resultCount + 1,
				'maxLimit' => $resultCount + 1,
				'order' => $order
			];
			$reportData = $this->paginate();
			$this->set(compact('resultCount'));
		}
		$isExportRequest = false;
		if (Hash::get($this->request->params, 'ext') === 'csv') {
			$isExportRequest = true;
			$this->GUIbuilder->setCsvView($filterParams);
			$this->response->download('data-collection.csv');
		}

		$this->set($this->ImportedDataCollection->getData($reportData, $filterParams, $isExportRequest));
	}

/**
 * upload method
 *
 * @return void
 */
	public function upload() {
		if ($this->request->is('post')) {
			$uploadError = false;
			$tmpFname = $this->request->data('ImportedDataCollection.file.tmp_name');
			$inputYear = $this->request->data('ImportedDataCollection.year');
			$imputMonth = $this->request->data('ImportedDataCollection.month');
			if (is_uploaded_file($tmpFname)) {
				$validation = $this->ImportedDataCollection->validUploadFile($tmpFname, $this->request->data('ImportedDataCollection.file.name'));
				if (!empty($validation['errors'])) {
					$this->Session->setFlash($validation['errors'], 'Flash/listErrors', array('class' => 'alert alert-danger'));
				} else {
					$isAsyncJob = $validation['row_count'] > ImportedDataCollection::SYNC_JOB_MAX_ROWS;
					$fullPath = $this->request->data('ImportedDataCollection.file.tmp_name');

					if ($this->ImportedDataCollection->moveUploadedFile($fullPath, APP . 'tmp' . DS,basename($fullPath))) {
						$fullPath = APP . $fullPath;
						$params = ['month' => $imputMonth, 'year' => $inputYear, 'file_path' => $fullPath];
						$result = $this->ImportedDataCollection->delegateUpsertJob($params, $isAsyncJob);
						if (empty($result['log']['errors'])) {
							$alertMsg = 'All data has been uploaded and saved successfully!';
							$cssClass = 'alert alert-success strong';
							if ($isAsyncJob) {
								$alertMsg = 'Due to the large amount of data in this file, this job will be processed on the server side. A notification will be sent to ' . $this->Auth->user('user_email') . ' when done.';
								$cssClass = 'alert alert-info strong shadow';
							}
							$this->_success($alertMsg, null, ['class' => $cssClass]);
						}
						$this->set(compact('result'));
					} else {
						$uploadError = true;
					}
				}
			} else {
				$uploadError = true;
			}
			if ($uploadError) {
				$this->_failure('Unexpected error! Upload failed, make sure you are uploading the correct document and try again');
			}
		}
		$years = $this->GUIbuilder->getDatePartOptns('y');
		$months = $this->GUIbuilder->getDatePartOptns('m');
		$csvColNames = $this->ImportedDataCollection->getFieldMap();
		unset($csvColNames['MID']);
		$csvColNames = array_keys($csvColNames);
		sort($csvColNames, SORT_STRING);
		array_unshift($csvColNames, 'MID');
		arsort($years);
		$this->set(compact('years', 'months', 'csvColNames'));
	}

/**
 * ajax method
 *
 * @return void
 */
	public function checkDataExists() {
		/* This function handles only ajax rerquests */
		$this->autoRender = false;
		if ($this->request->is('ajax')) {
			/* Check if session is still active by checking currently logged in user id */
			if ($this->Auth->user('id')) {
				$month = $this->request->data('ImportedDataCollection.month');
				$year = $this->request->data('ImportedDataCollection.year');
				$resonse['exists'] = $this->ImportedDataCollection->hasAny(['year' => $year, 'month' => $month]);
				return json_encode($resonse);
			} else {
				/* Upon session expiration, we cannot use $this->redirect(...) dutring an ajax requests in process.
				 * Therefore return a Forbidden status 403 responce and handle this responce on the client side with ajax. */
				$this->response->statusCode(403);
			}
		}
	}
}
