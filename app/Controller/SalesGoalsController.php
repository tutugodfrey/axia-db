<?php

App::uses('AppController', 'Controller');

/**
 * SalesGoals Controller
 *
 * @property SalesGoal $SalesGoal
 */
class SalesGoalsController extends AppController {

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->SalesGoal->recursive = 0;
		$this->set('salesGoals', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->SalesGoal->id = $id;
		if (!$this->SalesGoal->exists()) {
			throw new NotFoundException(__('Invalid sales goal'));
		}
		$this->set('salesGoal', $this->SalesGoal->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SalesGoal->create();
			if ($this->SalesGoal->save($this->request->data)) {
				$this->Session->setFlash(__('The sales goal has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The sales goal could not be saved. Please, try again.'));
			}
		}
		$users = $this->SalesGoal->User->find('list');
		$this->set(compact('users'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->SalesGoal->id = $id;
		if (!$this->SalesGoal->exists()) {
			throw new NotFoundException(__('Invalid sales goal'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SalesGoal->save($this->request->data)) {
				$this->Session->setFlash(__('The sales goal has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The sales goal could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->SalesGoal->read(null, $id);
		}
		$users = $this->SalesGoal->User->find('list');
		$this->set(compact('users'));
	}

	/**
	 * editMany, edit a grid of Sales Goals related to a given userId
	 *
	 * @throws NotFoundException
	 * @param string $userId
	 * @return void
	 */
	public function editMany($userId = null) {
		$this->SalesGoal->User->id = $userId;
		if (!$this->SalesGoal->User->exists()) {
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SalesGoal->User->saveAssociated($this->request->data)) {
				$this->_success(null, array(
						  'controller' => 'users',
						  'action' => 'view',
						  $userId
				));
			} else {
				$this->_failure();
			}
		} else {
			$this->request->data = $this->SalesGoal->User->view($userId);
		}
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
		$this->SalesGoal->id = $id;
		if (!$this->SalesGoal->exists()) {
			throw new NotFoundException(__('Invalid sales goal'));
		}
		if ($this->SalesGoal->delete()) {
			$this->Session->setFlash(__('Sales goal deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Sales goal was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
