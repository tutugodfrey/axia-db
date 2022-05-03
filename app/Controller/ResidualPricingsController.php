<?php

App::uses('AppController', 'Controller');

/**
 * ResidualPricings Controller
 *
 * @property ResidualPricing $ResidualPricing
 */
class ResidualPricingsController extends AppController {

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->ResidualPricing->recursive = 0;
		$this->set('residualPricings', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->ResidualPricing->id = $id;
		if (!$this->ResidualPricing->exists()) {
			throw new NotFoundException(__('Invalid residual pricing'));
		}
		$this->set('residualPricing', $this->ResidualPricing->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ResidualPricing->create();
			if ($this->ResidualPricing->save($this->request->data)) {
				$this->Session->setFlash(__('The residual pricing has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The residual pricing could not be saved. Please, try again.'));
			}
		}
		$merchants = $this->ResidualPricing->Merchant->find('list');
		$users = $this->ResidualPricing->User->find('list');
		$this->set(compact('merchants', 'users'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->ResidualPricing->id = $id;
		if (!$this->ResidualPricing->exists()) {
			throw new NotFoundException(__('Invalid residual pricing'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ResidualPricing->save($this->request->data)) {
				$this->Session->setFlash(__('The residual pricing has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The residual pricing could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->ResidualPricing->read(null, $id);
		}
		$merchants = $this->ResidualPricing->Merchant->find('list');
		$users = $this->ResidualPricing->User->find('list');
		$this->set(compact('merchants', 'users'));
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
		$this->ResidualPricing->id = $id;
		if (!$this->ResidualPricing->exists()) {
			throw new NotFoundException(__('Invalid residual pricing'));
		}
		if ($this->ResidualPricing->delete()) {
			$this->Session->setFlash(__('Residual pricing deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Residual pricing was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
