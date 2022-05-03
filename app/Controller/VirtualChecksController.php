<?php

App::uses('AppController', 'Controller');

/**
 * VirtualChecks Controller
 *
 * @property VirtualCheck $VirtualCheck
 */
class VirtualChecksController extends AppController {

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->VirtualCheck->recursive = 0;
		$this->set('virtualChecks', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->VirtualCheck->id = $id;
		if (!$this->VirtualCheck->exists()) {
			throw new NotFoundException(__('Invalid virtual check'));
		}
		$this->set('virtualCheck', $this->VirtualCheck->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->VirtualCheck->create();
			if ($this->VirtualCheck->save($this->request->data)) {
				$this->Session->setFlash(__('The virtual check has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The virtual check could not be saved. Please, try again.'));
			}
		}
		$merchants = $this->VirtualCheck->Merchant->find('list');
		$this->set(compact('merchants'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->VirtualCheck->id = $id;
		if (!$this->VirtualCheck->exists()) {
			throw new NotFoundException(__('Invalid virtual check'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->VirtualCheck->save($this->request->data)) {
				$this->Session->setFlash(__('The virtual check has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The virtual check could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->VirtualCheck->read(null, $id);
		}
		$merchants = $this->VirtualCheck->Merchant->find('list');
		$this->set(compact('merchants'));
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
		$this->VirtualCheck->id = $id;
		if (!$this->VirtualCheck->exists()) {
			throw new NotFoundException(__('Invalid virtual check'));
		}
		if ($this->VirtualCheck->delete()) {
			$this->Session->setFlash(__('Virtual check deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Virtual check was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
