<?php

App::uses('AppController', 'Controller');

/**
 * MerchantRejectLines Controller
 *
 * @property MerchantRejectLine $MerchantRejectLine
 */
class MerchantRejectLinesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->MerchantRejectLine->recursive = 0;
		$this->set('merchantRejectLines', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id Merchant Reject Line Id
 * @return void
 * @throws NotFoundException
 */
	public function view($id = null) {
		$this->MerchantRejectLine->id = $id;
		if (!$this->MerchantRejectLine->exists()) {
			throw new NotFoundException(__('Invalid merchant reject line'));
		}
		$this->set('merchantRejectLine', $this->MerchantRejectLine->read(null, $id));
	}

/**
 * add method
 *
 * @param string $merchantRejectId MerchantReject to add the reject line
 * @return void
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 */
	public function add($merchantRejectId = null) {
		if (!$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		$merchantReject = $this->MerchantRejectLine->MerchantReject->get($merchantRejectId, array(
			'contain' => $this->MerchantRejectLine->MerchantReject->contain()
		));
		if (empty($merchantReject)) {
			throw new NotFoundException(__('Invalid merchant reject'));
		}

		if ($this->request->is('post')) {
			$this->MerchantRejectLine->create();
			$saveResult = $this->MerchantRejectLine->saveAssociated($this->request->data);
		} else {
			// Default values
			$this->request->data['MerchantReject'] = Hash::get($merchantReject, 'MerchantReject');
			$this->request->data['MerchantRejectLine']['merchant_reject_id'] = Hash::get($merchantReject, 'MerchantReject.id');
			$this->request->data['MerchantRejectLine']['status_date'] = date('Y-m-d');
		}

		$this->set($this->MerchantRejectLine->MerchantReject->getItemsToSet());
		$this->set(compact('merchantReject'));
		if (!empty($saveResult)) {
			$rejectLine = $this->MerchantRejectLine->get($this->MerchantRejectLine->id, array(
				'contain' => array(
					'MerchantReject',
					'MerchantRejectStatus' // Needed to calculate the submitted amount
				)
			));

			$this->set(compact('rejectLine'));
			$this->set('firstLine', false);
			$this->render('viewRow');
		}
	}

/**
 * edit method
 *
 * @param string $id Merchant Reject Line Id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null) {
		$this->MerchantRejectLine->id = $id;
		if (!$this->MerchantRejectLine->exists()) {
			throw new NotFoundException(__('Invalid merchant reject line'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MerchantRejectLine->save($this->request->data)) {
				$this->Session->setFlash(__('The merchant reject line has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The merchant reject line could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->MerchantRejectLine->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @param string $id Merchant Reject Line Id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		$this->autoRender = false;

		if (!$this->request->is('post') || !$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		$this->MerchantRejectLine->id = $id;
		if (!$this->MerchantRejectLine->exists()) {
			throw new NotFoundException(__('Invalid merchant reject line'));
		}
		if (!$this->MerchantRejectLine->delete()) {
			return 'error';
		}
		return 'ok';
	}

/**
 * Ajax edit merchant reject line row
 * Will edit the merchant reject line AND merchant reject open status
 *
 * @param string $id merchant reject line id
 * @return void
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 */
	public function editRow($id = null) {
		if (!$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$saveResult = $this->MerchantRejectLine->saveAll($this->request->data);
		}
		// Get the updated Merchant reject line data to show on view row or use on edit form
		try {
			$merchantReject = $this->MerchantRejectLine->get($id, array(
				'contain' => $this->MerchantRejectLine->rowContain(),
			));
		} catch (OutOfBoundsException $ex) {
			throw new NotFoundException($ex->getMessage());
		}
		if (!$this->request->is('post') && !$this->request->is('put')) {
			$this->request->data = $merchantReject;
		}

		$this->set($this->MerchantRejectLine->MerchantReject->getItemsToSet());
		$this->set(compact('merchantReject'));
		if (!empty($saveResult)) {
			//render view row
			$this->set('rejectLine', $this->MerchantRejectLine->formatRejectLine($merchantReject));
			$this->set('firstLine', false);
			$this->render('viewRow');
		}
	}

/**
 * Ajax cancel edit merchant reject line row
 * Will cancel the edit merchant reject line row
 *
 * @param string $id merchant reject line id
 * @return void
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 */
	public function cancelRow($id = null) {
		if (!$this->request->is('ajax')) {
			throw new MethodNotAllowedException(__('Invalid call'));
		}

		if (empty($id)) {
			// does not render any view when canceling the add line row form
			$this->autoRender = false;
			return false;
		} else {
			try {
				$merchantReject = $this->MerchantRejectLine->get($id, array(
					'contain' => $this->MerchantRejectLine->rowContain(),
				));
				$rejectLine = $this->MerchantRejectLine->formatRejectLine($merchantReject);
			} catch (OutOfBoundsException $ex) {
				throw new NotFoundException($ex->getMessage());
			}

			$this->set($this->MerchantRejectLine->MerchantReject->getItemsToSet());
			$this->set(compact('merchantReject', 'rejectLine'));
			$this->set('firstLine', false);
			$this->render('viewRow');
		}
	}
}
