<?php
App::uses('AppController', 'Controller');

/**
 * InvoiceItems Controller
 *
 * @property MerchantAch $MerchantAch
 */
class InvoiceItemsController extends AppController {

	public $components = ['GUIbuilder'];

/**
 * delete method
 *
 *
 * @param string $id Invoice id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			$this->_failure(__('Method not allowed'), $this->referer(), ['class' => 'alert alert-danger']);
		}
		$this->InvoiceItem->id = $id;
		if (!$this->InvoiceItem->exists()) {
			$this->_failure(__('Could not delete invoice item, item does not exist!'), $this->referer(), ['class' => 'alert alert-danger']);
		}
		if ($this->InvoiceItem->delete($id, false)) {
			$this->_success(__('Invoice line deleted'),  $this->referer(), array('class' => 'alert alert-success'));
		}
		$this->_failure(__('Error: Invoice line could not be deleted, please try again'), $this->referer(), ['class' => 'alert alert-danger']);
	}
}
