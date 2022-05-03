<?php

App::uses('AppController', 'Controller');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * SystemTransactions Controller
 *
 * @property SystemTransaction $SystemTransaction
 */
class SystemTransactionsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Search.Prg' => array(
			'commonProcess' => array('paramType' => 'querystring'),
			'presetForm' => array('paramType' => 'querystring')
		),
	);

/**
 * Display the list of the user activity on the application
 *
 * @return void
 */
	public function userActivity() {
		$this->Prg->commonProcess();
		// Set default search values
		if (empty($this->request->query)) {
			$this->request->data['SystemTransaction'] = $this->SystemTransaction->getDefaultSearchValues();
		} else {
			// Update the parameters that are excluded when parsed for conditions
			$this->request->data = Hash::insert($this->request->data, 'SystemTransaction.row_limit', Hash::get($this->request->query, 'row_limit'));
		}

		$conditions = $this->SystemTransaction->parseCriteria($this->Prg->parsedParams());
		$rowLimit = $this->request->data('SystemTransaction.row_limit');
		if ($rowLimit === SystemTransaction::ROW_LIMIT_ALL) {
			$rowLimit = GUIbuilderComponent::NO_LIMIT_PAGINATION;
		}
		$this->Paginator->settings = array(
			'findType' => 'userActivity',
			'conditions' => $conditions,
			'limit' => $rowLimit,
			'maxLimit' => $rowLimit,
		);
		$systemTransactions = $this->paginate();
		$users = $this->SystemTransaction->User->getEntityManagerUsersList(true);
		$rowLimits = $this->SystemTransaction->getRowLimits();

		$this->set(compact('systemTransactions', 'rowLimits', 'users'));
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->SystemTransaction->recursive = 0;
		$this->set('systemTransactions', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->SystemTransaction->id = $id;
		if (!$this->SystemTransaction->exists()) {
			throw new NotFoundException(__('Invalid system transaction'));
		}
		$this->set('systemTransaction', $this->SystemTransaction->read(null, $id));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->SystemTransaction->id = $id;
		if (!$this->SystemTransaction->exists()) {
			throw new NotFoundException(__('Invalid system transaction'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SystemTransaction->save($this->request->data)) {
				$this->Session->setFlash(__('The system transaction has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system transaction could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->SystemTransaction->read(null, $id);
		}
		$systemTransactions = $this->SystemTransaction->SystemTransaction->find('list');
		$users = $this->SystemTransaction->User->find('list');
		$merchants = $this->SystemTransaction->Merchant->find('list');
		$sessions = $this->SystemTransaction->Session->find('list');
		$merchantNotes = $this->SystemTransaction->MerchantNote->find('list');
		$changes = $this->SystemTransaction->Change->find('list');
		$orders = $this->SystemTransaction->Order->find('list');
		$programmings = $this->SystemTransaction->Programming->find('list');
		$this->set(compact('systemTransactions', 'users', 'merchants', 'sessions', 'merchantNotes', 'changes', 'orders', 'programmings'));
	}

	/**
	 * delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->SystemTransaction->id = $id;
		if (!$this->SystemTransaction->exists()) {
			throw new NotFoundException(__('Invalid system transaction'));
		}
		if ($this->SystemTransaction->delete()) {
			$this->Session->setFlash(__('System transaction deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('System transaction was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
