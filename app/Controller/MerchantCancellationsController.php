<?php

App::uses('AppController', 'Controller');

/**
 * MerchantCancellations Controller
 *
 * @property MerchantCancellation $MerchantCancellation
 */
class MerchantCancellationsController extends AppController {

	public $components = array('FlagStatusLogic', 'GUIbuilder', 'CsvHandler');

/**
 * index method
 *
 * @param boolean $showAttrition (currently disabled) whether to show the attrition report data
 * @return void
 */
	public function index($showAttrition = false) {
		$showAttrition = false; // We are disabling the Attrition report feature until further notice
		if ($showAttrition) {//Show Attrition Report data
			if (!empty($this->request->query['month'])) {/* Only show data on a request */
				$queryParams = $this->MerchantCancellation->calculateAttritionData($this->request->query);
				$this->Paginator->settings = $this->MerchantCancellation->setPaginatorSettings($queryParams);
				$reportData = empty($this->request->query['output'])?$this->Paginator->paginate('MerchantCancellation'):'';
				if (!is_array($this->request->query['user_id'])) {
					$userAttritionRate = ClassRegistry::init('AttritionRatio')->field('percentage', array(
						'user_id' => $this->Auth->user('id')));
					$this->set(compact('userAttritionRate'));
				}
			}
			$this->set(compact('queryParams'));
		} else {
			/* Show Cancellations report data */
			$reportData = array();
			if (!empty($this->request->query['month'])) {
				if (empty($this->request->query['user_id'])) {
					$this->request->query['user_id'] = $this->MerchantCancellation->getCurrUserComplexId();
				}

				$amountOfmonths = ($this->request->query['ammountOfMonths'] === '') ? 12 : $this->request->query['ammountOfMonths'];
				$dateRange = $this->MerchantCancellation->getDateRangeByMonthYear($this->request->query['month']['month'], $this->request->query['year']['year'], $amountOfmonths);
				$dateRange['user_id'] = $this->request->query['user_id'];
				$this->Paginator->settings = $this->MerchantCancellation->setPaginatorSettings($dateRange);
				$reportData = empty($this->request->query['output'])? $this->Paginator->paginate('MerchantCancellation') : '';
				$dateRange['amountOfmonths'] = $amountOfmonths;
			}
			if (!empty($dateRange)) {
				$this->set('queryParams', $dateRange);
			}
		}
		if (!empty($this->request->query['output'])) {
			$this->autoRender = false;
			if ($showAttrition) {
				$queryParams['userAttritionRate'] = $userAttritionRate;
			}
			$queryParams = (empty($queryParams))? $dateRange: $queryParams;
			$csvStr = $this->MerchantCancellation->makeCsvString($this->Paginator->settings, $queryParams);
			$this->CsvHandler->saveCsvStrToFile(($showAttrition) ? "attrition_ratio_report" : "cancellation_report", $csvStr);
			return $this->response;
		}

		if (!empty($reportData)) {
			$reportCount = count($reportData);
			for ($i = 0; $i < $reportCount; $i++) {
				if (!empty($reportData[$i]['MerchantCancellation']['status'])) {
					$reportData[$i]['MerchantCancellation']['statusFlag'] = $this->FlagStatusLogic->getThisStatusFlag($reportData[$i]['MerchantCancellation']['status']);
				} else {
					$reportData[$i]['MerchantCancellation']['statusFlag'] = '--';
				}
			}
		}

		$moYrOptns['Months'] = $this->GUIbuilder->getDatePartOptns('m');
		$moYrOptns['Years'] = $this->GUIbuilder->getDatePartOptns('y');
		$this->set('users', $this->MerchantCancellation->Merchant->User->getEntityManagerUsersList(true));
		$this->set(compact('showAttrition', 'reportData', 'moYrOptns'));
	}

/**
 * view method
 *
 * @param string $id merchant id
 * @return void
 * @throws NotFoundException
 */
	public function view($id = null) {
		$this->MerchantCancellation->Merchant->id = $id;
		if (!$this->MerchantCancellation->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Mechant'));
		}
		$merchant = $this->MerchantCancellation->getCancellation($id);

		$this->set('merchant', $merchant);
	}

/**
 * add method
 *
 * @param string $id merchant id
 * @return void
 */
	public function add($id) {
		if ($this->request->is('post')) {
			$this->MerchantCancellation->create();
			if ($this->MerchantCancellation->save($this->request->data)) {
				$this->Session->setFlash(__('The merchant cancellation has been saved'), 'default', array(
					'class' => 'success'));
				$this->redirect(array('action' => 'view', $id));
			} else {
				$this->Session->setFlash(__('The merchant cancellation could not be saved. Please, try again.'));
			}
		}
		$merchant = $this->MerchantCancellation->Merchant->find('first', array('recursive' => -1,
			'contain' => 'User', 'conditions' => array('Merchant.id' => $id)));
		$subReasons = $this->MerchantCancellation->MerchantCancellationSubreason->find('list');
		$this->set(compact('merchant', 'subReasons'));
	}

/**
 * edit method
 *
 * @param string $id MerchantCancellation id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null) {
		$this->MerchantCancellation->id = $id;
		if (!$this->MerchantCancellation->exists()) {
			throw new NotFoundException(__('Invalid merchant cancellation'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MerchantCancellation->save($this->request->data)) {
				$this->Session->setFlash(__('The merchant cancellation has been saved'), 'default', array(
					'class' => 'success'));

				$this->redirect(array('action' => 'view', $this->request->data['MerchantCancellation']['merchant_id']));
			} else {
				$this->Session->setFlash(__('The merchant cancellation could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->MerchantCancellation->get($id);

		}
		$merchant = $this->MerchantCancellation->Merchant->getSummaryMerchantData($this->request->data['MerchantCancellation']['merchant_id']);
		$subReasons = $this->MerchantCancellation->MerchantCancellationSubreason->find('list');
		$this->set(compact('merchant', 'subReasons'));
	}

/**
 * delete method
 *
 * @param string $id MerchantCancellation.id
 * @return void
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->MerchantCancellation->id = $id;
		if (!$this->MerchantCancellation->exists()) {
			throw new NotFoundException(__('Invalid merchant cancellation'));
		}
		if ($this->MerchantCancellation->delete()) {
			$this->Session->setFlash(__('Merchant cancellation deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Merchant cancellation was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
