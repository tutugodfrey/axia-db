<?php

App::uses('AppController', 'Controller');

/**
 * UserPermissions Controller
 *
 * @property UserPermission $UserPermission
 */
class UserPermissionsController extends AppController {

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->UserPermission->recursive = 0;
		$this->set('userPermissions', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->UserPermission->id = $id;
		if (!$this->UserPermission->exists()) {
			throw new NotFoundException(__('Invalid user permission'));
		}
		$this->set('userPermission', $this->UserPermission->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->UserPermission->create();
			if ($this->UserPermission->save($this->request->data)) {
				$this->Session->setFlash(__('The user permission has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user permission could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->UserPermission->id = $id;
		if (!$this->UserPermission->exists()) {
			throw new NotFoundException(__('Invalid user permission'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->UserPermission->save($this->request->data)) {
				$this->Session->setFlash(__('The user permission has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user permission could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->UserPermission->read(null, $id);
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
		$this->UserPermission->id = $id;
		if (!$this->UserPermission->exists()) {
			throw new NotFoundException(__('Invalid user permission'));
		}
		if ($this->UserPermission->delete()) {
			$this->Session->setFlash(__('User permission deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User permission was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
