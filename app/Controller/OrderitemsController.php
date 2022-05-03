<?php
App::uses('AppController', 'Controller');

/**
 * Orderitems Controller
 *
 * @property Orderitem $Orderitem
 */
class OrderitemsController extends AppController {

/**
 * delete method
 *
 * @param string $id an order item id
 * @return void
 * @throws MethodNotAllowedException
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Orderitem->id = $id;
		if (!$this->Orderitem->exists()) {
			$this->_failure(__('Could not delte order item, item does not exist.'), $this->referer());
		}
		/* Set cascade = true to delete records associated with this orderitem */
		if ($this->Orderitem->delete($this->Orderitem->id, true)) {
			$this->_success(__('Order item deleted'), $this->referer());
		}
		$this->_failure(__('Order item was not deleted, try again.'), $this->referer());
	}
}