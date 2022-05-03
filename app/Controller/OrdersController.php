<?php

App::uses('AppController', 'Controller');

/**
 * Orders Controller
 *
 * @property Order $Order
 */
class OrdersController extends AppController {

	public $components = ['FlagStatusLogic', 'GUIbuilder', 'CsvHandler', 'Search.Prg'];

	public $helpers = ['Order'];

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Prg->commonProcess();
		if (empty($this->passedArgs['date_ordered_b_month']) && empty($this->passedArgs['date_ordered_b_year'])) { //Set default beginning datesdates
			$this->passedArgs['date_ordered_b_month'] = (date('n') - 1 === 0)? null : date('n') - 1; //month without leading zeros
			$this->passedArgs['date_ordered_b_year'] = date('Y');
			//set request query so that csv export link is properly generated
			$this->request->query = $this->passedArgs;
		}
		$this->Paginator->settings = $this->Order->indexPaginatorSettings();

		$this->Paginator->settings['contain'] = array(
					'Vendor',
					'Orderitem' => array(
						'EquipmentItem' => array('fields' => array('EquipmentItem.equipment_item_description')),
						'Merchant' => array('fields' => array('Merchant.id', 'Merchant.merchant_dba')),
				)
		);

		$this->_setPaginatorConditions();
		$orders = $this->Paginator->paginate();

		if (Hash::get($this->request->query, 'output') === 'csv') {
			$csvStr = $this->Order->makeCsvString($orders);
			$this->CsvHandler->saveCsvStrToFile("user_equipment_orders", $csvStr);
			return $this->response;
		} else {
			$this->set('orders', $orders);
		}
		$filterOptns = $this->Order->getFilterOptions();
		$filterOptns['months'] = $this->GUIbuilder->getDatePartOptns('m');
		$filterOptns['years'] = $this->GUIbuilder->getDatePartOptns('y');
		$this->set(compact('filterOptns'));
		$this->set('displayIconLegend', true); //This will display the iconLegend element
		$this->set('FlagStatusLogic', $this->FlagStatusLogic);
	}

/**
 * _setPaginatorConditions
 * Set approriate conditions for search including deeper contained models conditions
 *
 * @return void
 */
	protected function _setPaginatorConditions() {
		$conditions = $this->Order->parseCriteria($this->passedArgs);

		if (isset($conditions['Orderitem.equipment_item_id'])) {
			$this->Paginator->settings['contain']['Orderitem']['conditions']['Orderitem.equipment_item_id'] = $conditions['Orderitem.equipment_item_id'];
			unset($conditions['Orderitem.equipment_item_id']);
		}
		if (isset($conditions['Orderitem.orderitem_type_id'])) {
			$this->Paginator->settings['contain']['Orderitem']['conditions']['Orderitem.orderitem_type_id'] = $conditions['Orderitem.orderitem_type_id'];
			unset($conditions['Orderitem.orderitem_type_id']);
		}
		if (isset($conditions['Orderitem.hardware_sn'])) {
			$this->Paginator->settings['contain']['Orderitem']['conditions']['Orderitem.hardware_sn'] = $conditions['Orderitem.hardware_sn'];
			unset($conditions['Orderitem.hardware_sn']);
		}
		if (isset($conditions['Orderitem.hardware_replacement_for'])) {
			$this->Paginator->settings['contain']['Orderitem']['conditions']['Orderitem.hardware_replacement_for'] = $conditions['Orderitem.hardware_replacement_for'];
			unset($conditions['Orderitem.hardware_replacement_for']);
		}
		if (isset($conditions['OR']['Merchant.merchant_dba ILIKE'])) {
			$this->Paginator->settings['contain']['Orderitem']['Merchant']['conditions']['OR'] = $conditions['OR'];
			unset($conditions['OR']);
		}
		$this->Paginator->settings['conditions'] = $conditions;
		$this->Paginator->settings['conditions']['Order.status !='] = GUIbuilderComponent::STATUS_DELETED;
	}

/**
 * view method
 *
 * @param string $id Order id
 * @throws NotFoundException
 * @return void
 */
	public function equipment_invoice($id = null) {
		$this->Order->id = $id;
		if (!$this->Order->exists()) {
			throw new NotFoundException(__('Invalid order'));
		}
		$order = $this->Order->getOrderByOrderId($id);
		$this->Paginator->settings = $this->Order->Orderitem->getSearchSettings($id);
		$orderItems = $this->Paginator->paginate('Orderitem');
		$this->set(compact('order', 'orderItems'));
		$this->set('orderStatuses', $this->GUIbuilder->getStatusList());
	}

/**
 * assigned_equipment method
 *
 * @param string $id Merchant id
 * @throws NotFoundException
 * @return void
 */
	public function merchant_equipment_assigned($id) {
		$this->Order->Merchant->id = $id;
		if (!$this->Order->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		$this->Paginator->settings = $this->Order->merchEqmntPaginatorSettings($id);
		$orders = $this->Paginator->paginate('Orderitem');

		/* Mod data structure so that Orders contain all their Orderitems */
		if (!empty($orders)) {
			$orders = $this->Order->groupOrderItemsByOrder($orders);
		}
		$merchant = $this->Order->Merchant->getSummaryMerchantData($id);
		$this->set(compact('orders', 'merchant'));
		$this->set('displayIconLegend', true); //This will display the iconLegend element
		$this->set('FlagStatusLogic', $this->FlagStatusLogic); //This will display the iconLegend element
	}

/**
 * add method
 *
 * @return void
 */
	public function add_equipment_invoice() {
		if ($this->request->is('post')) {
			$isFinished = array_key_exists('Save', $this->request->data);
			$this->request->data = $this->Order->cleanupRequestData($this->request->data);
			$this->request->data = $this->Order->setItemRepPartnerCost($this->request->data);

			$this->Order->create();
			if ($this->Order->saveAll($this->request->data, array('deep' => true))) {
				if ($isFinished) {
					$this->Session->setFlash(__('The order has been saved'), 'default', array('class' => 'success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('Order has saved, more lines added!'), 'default', array('class' => 'success'));
					$this->redirect(array('action' => 'edit_equipment_invoice', $this->Order->id));
				}
			} else {
				$errors = $this->_extractErrors();
				$this->Session->setFlash($errors, 'Flash/listErrors', array('class' => 'alert alert-danger'));
			}
		}
		$formOptns = $this->Order->getOrderFormMenuData();
		//Get options for commission months
		$formOptns['CommissionMonths'] = $this->GUIbuilder->getDatePartOptns('m');
		$formOptns['CommissionYears'] = $this->GUIbuilder->getDatePartOptns('y');
		$this->set(compact('formOptns'));
	}

/**
 * _extractErrors method
 *
 * @return array
 */
	protected function _extractErrors() {
		$errors = Hash::extract($this->Order->validationErrors, '{n}.{s}.{n}');
		$errors += Hash::extract($this->Order->Orderitem->validationErrors, '{n}.{s}.{n}');
		return $errors;
	}

/**
 * edit method
 *
 * @param string $id Order id
 * @throws NotFoundException
 * @return void
 */
	public function edit_equipment_invoice($id = null) {
		$this->Order->id = $id;
		if (!$this->Order->exists()) {
			throw new NotFoundException(__('Invalid order'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$isFinished = array_key_exists('Save', $this->request->data);
			$this->request->data = $this->Order->cleanupRequestData($this->request->data);
			$this->request->data = $this->Order->setItemRepPartnerCost($this->request->data);
            //re-index the Orderitems, so that in case validation errors occur
			//hardware and supplies are separated correctly on the form and
			//validation errors are displayed next to the correct field that has the error
			$this->request->data['Orderitem'] = Hash::extract($this->request->data, 'Orderitem.{n}');
			if ($this->Order->saveAll($this->request->data, array('deep' => true))) {
				if ($isFinished) {
					$this->Session->setFlash(__('The order has been saved'), 'default', array('class' => 'success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('Order has been saved'), 'default', array('class' => 'success'));
					$this->redirect($this->referer());
				}
			} else {
				$errors = $this->_extractErrors();
				$this->Session->setFlash($errors, 'Flash/listErrors', array('class' => 'alert alert-danger'));
			}
		} else {
			$this->request->data = array_merge($this->Order->getOrderByOrderId($id), $this->Order->Orderitem->getAllOrderitemsByOrderId($id));
		}

		$formOptns = $this->Order->getOrderFormMenuData();
		//Get options for commission months
		$formOptns['CommissionMonths'] = $this->GUIbuilder->getDatePartOptns('m');
		$formOptns['CommissionYears'] = $this->GUIbuilder->getDatePartOptns('y');

		$itemCount = (count($this->request->data['Orderitem']) > 0) ? count($this->request->data['Orderitem']) + 4 : 4;
		$supplyOffset = $itemCount + 4;
		$this->set(compact('formOptns', 'itemCount', 'supplyOffset'));
	}

/**
 * mark_as_paid_order method
 *
 * Change the status to PAID.
 *
 * @param string $id Order Id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function mark_as_paid_order($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Order->id = $id;
		if (!$this->Order->exists()) {
			throw new NotFoundException(__('Invalid Order id, unable to update order.'));
		}
		if ($this->Order->save(array('Order' => array('status' => GUIbuilderComponent::STATUS_PAID)))) {
			$this->Session->setFlash(__('Order marked as Paid!'), 'default', array('class' => 'success'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('ERROR: Could not update order, try again or contact webmaster'));
	}

/**
 * delete method
 *
 * Soft-delete method, data is NOT completely deleted from database
 * Changes the status to DEL which causes to not display.
 *
 * @param string $id Order id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Order->id = $id;
		if (!$this->Order->exists()) {
			throw new NotFoundException(__('Invalid Order id, unable to delete order.'));
		}
		if ($this->Order->save(array('Order' => array('status' => 'DEL')))) {
			$this->Session->setFlash(__('Order Deleted'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('ERROR: Could not delete order, try again or contact webmaster'));
	}
}
