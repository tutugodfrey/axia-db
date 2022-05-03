<?php

App::uses('AppController', 'Controller');
App::uses('UploadValidationException', 'Lib/Error');

/**
 * MerchantRejects Controller
 *
 * @property MerchantReject $MerchantReject
 */
class MerchantRejectsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Search.Prg' => array(
			'commonProcess' => array('paramType' => 'querystring'),
			'presetForm' => array('paramType' => 'querystring')
		)
	);

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array('MerchantReject');

/**
 * index method
 *
 * @param string $merchantId merchantId to filter by
 * @return void
 */
	public function index($merchantId) {
		$this->Prg->commonProcess();
		$merchant = $this->MerchantReject->Merchant->getSummaryMerchantData($merchantId);
		$conditions = $this->MerchantReject->parseCriteria($this->Prg->parsedParams());
		$conditions['MerchantReject.merchant_id'] = $merchantId;
		$this->Paginator->settings = array(
			'findType' => 'merchantRejects',
			'conditions' => $conditions,
			'ignoreRejectDate' => true,
			'rejectLineOrder' => array('MerchantRejectLine.status_date ASC NULLS FIRST'),
			'order' => "\"MerchantReject\".\"reject_date\" DESC"
		);
		$merchantRejects = $this->paginate();

		$this->set($this->MerchantReject->getItemsToSet());
		$this->set(compact('merchantRejects', 'merchant'));
	}

/**
 * report method
 *
 * @return void
 */
	public function report() {
		$this->Prg->commonProcess();
		$filterParams = $this->Prg->parsedParams();
		$filterParams = empty($filterParams)? [] : $filterParams;
		$complexUserIdArgs = $this->MerchantReject->Merchant->User->extractComplexId(Hash::get($filterParams, 'user_id'));

		if (Hash::get($complexUserIdArgs, 'prefix') === User::PREFIX_ENTITY) {
			$this->MerchantReject->filterArgs['user_id']['field'] = '"Merchant"."id"';
		}
		$conditions = $this->MerchantReject->parseCriteria($filterParams);
		$this->Paginator->settings = array(
			'findType' => 'merchantRejects',
			'conditions' => $conditions,
			'limit' => 1000,
			'maxLimit' => 1000,
		);

		$merchantRejects = $this->paginate();
		$this->set($this->MerchantReject->getItemsToSet());
		$users = $this->MerchantReject->Merchant->User->getEntityManagerUsersList();
		$this->set(compact(
			'merchantRejects',
			'users'
		));
	}

/**
 * add method
 *
 * @param string $merchantId pass a merchantId to redirect to the specific index page after add
 * @return void
 */
	public function add($merchantId = null) {
		if ($this->request->is('post')) {
			$this->MerchantReject->create();
			$url = array('action' => 'report');
			if (!empty($merchantId)) {
				$url = array('action' => 'index', $merchantId);
			} else {
				$merchantParam = Hash::get($this->request->data, 'MerchantReject.merchant_id');
				$merchantId = $this->MerchantReject->Merchant->findMerchantDBAorMID($merchantParam, is_numeric($merchantParam), 1, 1);
				if (empty($merchantId)) {
					$this->_failure("Failed to add Reject, merchant not found!", $url);
				}
				$this->request->data['MerchantReject']['merchant_id'] = (is_array($merchantId))? array_pop(array_keys($merchantId)) : $merchantId;
			}
			if ($this->MerchantReject->saveAssociated($this->request->data)) {
				$this->_success(null, $url);
			} else {
				$this->_failure(null, $url);
			}
		} else {
			$this->request->data = $this->MerchantReject->getDefaultValues();
		}
	}

/**
 * edit method, if called via AJAX, will render the inline row
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null) {
		$merchantReject = $this->MerchantReject->get($id, array(
			'contain' => $this->MerchantReject->contain()
		));
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MerchantReject->save($this->request->data)) {
				return $this->_success(null, array('action' => 'index'));
			} else {
				$this->_failure();
			}
		} else {
			$this->request->data = $merchantReject;
		}

		$merchant = array('Merchant' => $merchantReject['Merchant']);
		$merchants = $this->MerchantReject->Merchant->find('list');
		$this->set($this->MerchantReject->getItemsToSet());
		$this->set(compact('merchants', 'merchant'));
	}

/**
 * Ajax edit merchant reject row
 * Will edit the merchant reject AND the first reject line at the same time
 *
 * @param string $id merchant reject id
 * @param bool $editCurrentLine edit the current reject line instead of the first if true
 * @return void
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 */
	public function editRow($id = null, $editCurrentLine = 0) {
		if (!$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$saveResult = $this->MerchantReject->saveAll($this->request->data);
		}
		// Get the updated Merchant reject data to show on view row or use on edit form
		try {
			$merchantReject = $this->MerchantReject->get($id, array(
				'contain' => $this->MerchantReject->rowContain(),
			));
		} catch (OutOfBoundsException $ex) {
			throw new NotFoundException($ex->getMessage());
		}
		if (!$this->request->is('post') && !$this->request->is('put')) {
			$this->request->data = $merchantReject;
		}

		$rejectLinePath = empty($editCurrentLine) ? 'FirstMerchantRejectLine' : 'CurrentMerchantRejectLine';
		$this->set($this->MerchantReject->getItemsToSet());
		$this->set(compact('merchantReject', 'rejectLinePath'));
		$this->set('rejectLine', Hash::get($merchantReject, $rejectLinePath));
		$this->set('firstLine', true);
		if (!empty($saveResult)) {
			//render view row
			$this->render('viewRow');
		}
	}

/**
 * delete method
 *
 * @param string $id id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		$this->autoRender = false;

		if (!$this->request->is('post') || !$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		$this->MerchantReject->id = $id;
		if (!$this->MerchantReject->exists()) {
			throw new NotFoundException(__('Invalid merchant reject'));
		}
		if (!$this->MerchantReject->delete()) {
			return 'error';
		}
		return 'ok';
	}

/**
 * Action for bulk import of CV data
 *
 * @return void
 */
	public function import() {
		$importResult = array();
		$importErrors = array();
		$importSkippedRows = array();

		if ($this->request->is('post')) {
			try {
				$importResult = $this->MerchantReject->importFromCsvUpload($this->request->data);
				$importErrors = $this->MerchantReject->getImportErrors();
				$importSkippedRows = $this->MerchantReject->getImportSkippedRows();
				$this->set(compact('importErrors', 'importSkippedRows', 'importResult'));
				if (!empty($importErrors)) {
					return $this->_failure(__('The file was not imported, there were validation errors.'));
				}
				return $this->_success(__('File imported successfully'));
			} catch (UploadValidationException $ex) {
				$this->set(compact('importErrors', 'importSkippedRows', 'importResult'));
				return $this->_failure($ex->getMessage());
			}
		}
	}

/**
 * Ajax cancel edit merchant reject row
 * Will cancel the edit of merchant reject row
 *
 * @param string $id merchant reject id
 * @param bool $editCurrentLine edit the current reject line instead of the first if true
 * @return void
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 */
	public function cancelRow($id = null, $editCurrentLine = 0) {
		if (!$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		try {
			$merchantReject = $this->MerchantReject->get($id, array(
				'contain' => $this->MerchantReject->rowContain(),
			));
		} catch (OutOfBoundsException $ex) {
			throw new NotFoundException($ex->getMessage());
		}

		$rejectLinePath = empty($editCurrentLine) ? 'FirstMerchantRejectLine' : 'CurrentMerchantRejectLine';
		$this->set($this->MerchantReject->getItemsToSet());
		$this->set(compact('merchantReject', 'rejectLinePath'));
		$this->set('rejectLine', Hash::get($merchantReject, $rejectLinePath));
		$this->set('firstLine', true);
		$this->render('viewRow');
	}
}
