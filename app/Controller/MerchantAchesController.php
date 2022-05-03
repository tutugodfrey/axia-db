<?php

App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * MerchantAches Controller
 *
 * @property MerchantAch $MerchantAch
 */
class MerchantAchesController extends AppController {

	public $components = array(
		'Search.Prg' => [
			'commonProcess' => ['paramType' => 'querystring'],
			'presetForm' => ['paramType' => 'querystring']
		],
		'FlagStatusLogic', 'GUIbuilder', 'RequestHandler', 'CsvHandler');

	public $presetVars = true; // using the model configuration

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->MerchantAch->recursive = 0;
		$this->set('merchantAches', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id a Merchant id
 * @return void
 * @throws NotFoundException
 */
	public function view($id = null) {
		$this->MerchantAch->Merchant->id = $id;
		if (!$this->MerchantAch->Merchant->exists()) {
			throw new NotFoundException(__('Invalid merchant'));
		}
		$this->Paginator->settings = $this->MerchantAch->setPaginatorSettings(array('merchant_id' => $id));
		$merchantAches = $this->Paginator->paginate('MerchantAch');
		if (in_array('csv', $this->passedArgs)) {
			$csvStr = $this->MerchantAch->makeCsvString($merchantAches);
			$this->CsvHandler->saveCsvStrToFile("merchant_aches", $csvStr);
			exit;
		}
		$merchant = $this->MerchantAch->Merchant->getSummaryMerchantData($id);
		$this->set(compact('merchant', 'merchantAches'));
		$this->set('FlagStatusLogic', $this->FlagStatusLogic);
		$this->set('achReasons', $this->MerchantAch->MerchantAchReason->getList());
	}

/**
 * add method
 *
 * @return void
 */
	public function add($merchantId) {
		if ($this->request->is('post')) {
			$messageF = '';
			$apiTaxRate = array();
			//If a new MerchantUwVolume record needs to bee created
			if (empty($this->request->data['MerchantUw']['id'])) {
				$this->MerchantAch->Merchant->MerchantUw->create();
			}

			$messageF = $this->_saveExpediteStatus();
			//remove empty invoice items
			$this->MerchantAch->InvoiceItem->removeEmpty($this->request->data);
			if ($this->MerchantAch->saveAssociated($this->request->data)) {
				$this->_success(__('The Invoice has been saved' . $messageF), array('action' => 'view', $merchantId), array('class' => 'alert alert-success'));
				$this->redirect(array('action' => 'view', $merchantId));
			} else {
				$this->Session->setFlash(__('The invoice could not be saved. Please, try again.'));
				$this->redirect(array('action' => 'view', $merchantId));
			}
		}
		$this->request->data['MerchantAch']['invoice_number'] = $this->MerchantAch->createInvoiceNum();
		$this->request->data += $this->MerchantAch->getExpeditedInfoByMerchandId($merchantId);
		//Get options for commission months
		$moOptns = $this->GUIbuilder->getDatePartOptns('m');
		$yrOptns = $this->GUIbuilder->getDatePartOptns('y');
		
		$viewData = $this->MerchantAch->getEditViewData($merchantId);
		$taxApiData = array();
		if (strtoupper($viewData['merchBusinessState']) === 'CA') {
			$taxApiData = $this->_getTaxRates($viewData['merchBusinessZip'], $viewData['merchBusinessState'], $viewData['merchBusinessCity']);
		}
		$this->set($viewData);
		$this->set(compact('moOptns', 'yrOptns', 'taxApiData'));
	}

/**
 * edit method
 *
 * @param string $id a MerchantAch id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null) {
		$this->MerchantAch->id = $id;
		if (!$this->MerchantAch->exists()) {
			throw new NotFoundException(__('Invalid merchant ach'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$messageF = $this->_saveExpediteStatus();
			$this->request->data['MerchantAch']['id'] = $this->MerchantAch->id;
			//remove empty invoice items
			$this->MerchantAch->InvoiceItem->removeEmpty($this->request->data);
			if ($this->MerchantAch->saveAssociated($this->request->data)) {
				$this->_success(__('The Invoice has been saved' . $messageF), array('action' => 'view', $this->request->data['MerchantAch']['merchant_id']), array('class' => 'alert alert-success'));
			} else {
				$this->_failure(__('The Invoice could not be saved. Please, try again.'), array('action' => 'view', $this->request->data['MerchantAch']['merchant_id']), array('class' => 'alert alert-danger'));
			}
		} else {
			$this->request->data = $this->MerchantAch->get($id, array('contain' => ['InvoiceItem.MerchantAchReason']));
		}
		unset($this->request->data['User']); //Remove this from the request to prevent user nav menu to show up
		//Get options for commission months
		$moOptns = $this->GUIbuilder->getDatePartOptns('m');
		$yrOptns = $this->GUIbuilder->getDatePartOptns('y');
		
		$this->request->data += $this->MerchantAch->getExpeditedInfoByMerchandId($this->request->data['MerchantAch']['merchant_id']);
		$viewData = $this->MerchantAch->getEditViewData($this->request->data['MerchantAch']['merchant_id']);
		$viewData['achReasons'] = array_merge($viewData['achReasons'], $this->MerchantAch->MerchantAchReason->getList());
		//request tax rates iff is a CA merchant and no tax rates have been retrieved
		if (strtoupper($viewData['merchBusinessState']) === 'CA' && !is_numeric($this->request->data('MerchantAch.tax_rate_state'))) {
			$taxApiData = $this->_getTaxRates($viewData['merchBusinessZip'], $viewData['merchBusinessState'], $viewData['merchBusinessCity']);
		}
		$this->set($viewData);
		$this->set(compact('moOptns', 'yrOptns', 'taxApiData'));
	}

/**
 * _saveExpediteStatus
 * Utility method to update MerchantUw.expedite status
 *
 * @return string message to commiunicate succes/falure
 */
	protected function _saveExpediteStatus() {
		$message = ($this->MerchantAch->Merchant->MerchantUw->save($this->request->data['MerchantUw'])) ? ', EXPEDITED status has been updated' : ', However an error prevented updating EXPEDITED status!';
		unset($this->request->data['MerchantUw']);
		return $message;
	}

/**
 * _getTaxRates AJAX method
 * Gets tax rate for given zip code
 *
 * @param string $zipCode a US zip code
 * @return void
 */
	protected function _getTaxRates($zipCode = null, $state = null, $city = null) {
		$response = $this->MerchantAch->requestAPITaxRate($zipCode, $state, $city);
			$taxApiData = json_decode($response, true);
			if (empty($taxApiData) || $taxApiData['rCode'] !== 100) {
				$this->Session->setFlash(__("Warning: Unable to retrieve tax rates! Refresh page to try again. (rCode: {$taxApiData['rCode']})"), 'default', array('class' => 'alert alert-warning strong'));
			}
			return Hash::get($taxApiData, 'results.0');
	}

/**
 * renderNewItem AJAX method
 * Handles request for neew invoice line items
 *
 * @param integer $itemIdxNumber the new item's index position to be userd in the item's html form input. Usually n + 1 where n is the last item and n+1 is this new item being rendered.
 * @return void
 */
	public function renderNewItem($itemIdxNumber) {
		/* This function handles only ajax rerquests */
		$this->autoRender = false;

		if ($this->request->is('ajax')) {
			/* Check if session is still active by checking currently logged in user id */
			if (!$this->Session->check('Auth.User.id')) {
				$this->response->statusCode(403);
			}
			$this->set('achReasons', $this->MerchantAch->MerchantAchReason->getAchReasonsList());
			$this->set('nonTaxableReasons', $this->MerchantAch->InvoiceItem->NonTaxableReason->getList());
			$this->set('idx', $itemIdxNumber);
   			$this->render('/Elements/Layout/Merchant/invoiceLines', 'ajax');
		}
	}

/**
 * delete method
 *
 * ---This is a soft delete implementation.---
 *
 * @param string $id MerchantAch id
 * @return void
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->MerchantAch->id = $id;
		if (!$this->MerchantAch->exists()) {
			throw new NotFoundException(__('Invalid merchant ach'));
		}
		if ($this->MerchantAch->save(array('MerchantAch' => array('status' => 'DEL')), array('validate' => false))) {
			$this->Session->setFlash(__('Invoice deleted'), 'default', array('class' => 'success'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Error: Invoice could not be deleted, please try again'));
		$this->redirect($this->referer());
	}

/**
 * Return the filter parameters from Searchable behavior
 *
 * @return array
 */
	protected function _getFilterParams() {
		$this->Prg->commonProcess();
		$filterParams = $this->Prg->parsedParams();

		$complexUserIdArgs = $this->MerchantAch->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$this->MerchantAch->filterArgs['user_id'] = $this->MerchantAch->getUserSearchFilterArgs($complexUserIdArgs);
		if (empty($this->MerchantAch->filterArgs['user_id']['searchByMerchantEntity']) && $this->MerchantAch->filterArgs['user_id']['method'] === 'searchByUserId') {
			$this->MerchantAch->filterArgs['user_id']['field'] = '"Merchant"."user_id"';
		}

		if (!empty($this->request->query) && empty($filterParams['invoice_status'])) {
			$filterParams['invoice_status'] = [GUIbuilderComponent::STATUS_COMPLETED, GUIbuilderComponent::STATUS_PENDING];
		}
		return $filterParams;
	}
/**
 * accounting_report method
 *
 * @return void
 */
	public function accounting_report() {
		$filterParams = $this->_getFilterParams();
		if (empty($this->request->data['MerchantAch'])) {
			$this->request->data['MerchantAch'] = $filterParams;
		}
		$this->request->data['Merchant'] = [
			'organization_id' => Hash::get($filterParams, 'organization_id'),
			'region_id' => Hash::get($filterParams, 'region_id'),
			'subregion_id' => Hash::get($filterParams, 'subregion_id'),
		];
		$reportData = [];
		$conditions = $this->MerchantAch->parseCriteria($filterParams);
		$resultCount = null;

		if (!empty($conditions)) {
			$resultCount = $this->MerchantAch->find('count', array(
				'type' => 'accountingReport',
				'findType' => 'accountingReport',
				'conditions' => $conditions
			));

			//Set sort order
			$sortField = Hash::get($this->request->params, 'named.sort');
			$order = ($sortField)? [$sortField => Hash::get($this->request->params, 'named.direction')] : ['Merchant.merchant_mid' => 'asc'];

			$this->Paginator->settings = [
				'type' => 'accountingReport',
				'findType' => 'accountingReport',
				'conditions' => $conditions,
				//add one to prevent division by zero error when $resultCount === 0
				'limit' => $resultCount + 1,
				'maxLimit' => $resultCount + 1,
				'order' => $order
			];
			// Sorting on the view will not work unless the the query virtual fields
			// defined in custom find method _findAccountingReport are also set
			// here before calling $this->paginate()
			$this->MerchantAch->setPaginatorVirtualFields();
			$reportData = $this->paginate();

		}
		$isCsvExport = (Hash::get($this->request->params, 'ext') === 'csv');
		if ($isCsvExport) {
			$this->GUIbuilder->setCsvView($filterParams);
			$this->response->download('accounting_report.csv');
		}
		$this->set('statuses', [GUIbuilderComponent::STATUS_COMPLETED => 'Completed', GUIbuilderComponent::STATUS_PENDING => 'Pending']);
		$this->set('resultCount', $resultCount);
		$this->set($this->MerchantAch->getReportData($reportData, $isCsvExport));
	}

/**
 * updateStatus method
 * This method handles ajax and synchronous POST requests.
 * The client must pass a single dimentional array of MerchantAch.id's that will be updated in the request data array.
 *
 * @param boolean $setComplete when true the invoices will be marked as completed otherwise as pending
 * @return void
 */
	public function updateStatus($setComplete) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Check if session is still active by checking currently logged in user id */
			if (!$this->Session->check('Auth.User.id')) {
				$this->response->statusCode(403);
				return;
			}
		} elseif (!$this->request->is('post')) {
			$this->_failure('Error: Request method not allowed.');
		}
		$updated = false;
		if (!empty($this->request->data)) {
			$status = ($setComplete)? GUIbuilderComponent::STATUS_COMPLETED : GUIbuilderComponent::STATUS_PENDING;
			
			$updated = $this->MerchantAch->updateAll(
				[
					'MerchantAch.status' => "'$status'",
					'MerchantAch.date_completed' => ($setComplete)? "'" . date('Y-m-d') . "'": null
				],
				['MerchantAch.id' => $this->request->data]
			);
		}
		if ($this->request->is('ajax')) {
			if (!$updated) {
				$this->response->statusCode(500);
			}
			return;
		} else {
			$this->_success(__('Invoice(s) updated!'), $this->referer(), ['class' => 'alert alert-sucess']);
		}
	}
}
