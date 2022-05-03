<?php

App::uses('AppController', 'Controller');

class ResidualProductControlsController extends AppController {

	/**
	 * Displays all the residual product controls in a form for the current logged in user
	 *
	 * @return void
	 */
	public function index() {
		$residualProductControls = $this->ResidualProductControl->allDataByUserId($this->Auth->user('id'));

		if ($this->request->is('post')) {
			if ($this->ResidualProductControl->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Session->setFlash(__('Saved'));
			} else {
				$this->Session->setFlash(__('Could not save'));
			}
		} else {
			$this->request->data = $residualProductControls;
		}

		$this->set('residualProductControls', $residualProductControls);
	}

}
