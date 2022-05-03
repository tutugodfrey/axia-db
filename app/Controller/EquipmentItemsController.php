<?php

App::uses('AppController', 'Controller');
App::uses('EquipmentType', 'Model');

/**
 * EquipmentItems Controller
 *
 * @property EquipmentItem $EquipmentItem
 */
class EquipmentItemsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$totalCount = $this->EquipmentItem->find('count');
		$this->paginate = array(
				'fields' => array(
					'EquipmentItem.id',
					'EquipmentItem.equipment_item_description',
					'EquipmentItem.equipment_item_true_price',
					'EquipmentItem.equipment_item_rep_price',
				),
				'order' => array('EquipmentItem.equipment_item_description' => 'ASC'),
				'conditions' => array(
							'EquipmentItem.active' => '1',
							'EquipmentItem.equipment_type_id' => EquipmentType::HARDWARE_ID,
				),
				'limit' => $totalCount,
				'maxLimit' => $totalCount
		);
		$this->set('equipmentItems', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			//All Equipment added is hardware
			$this->request->data['EquipmentItem']['equipment_type_id'] = EquipmentType::HARDWARE_ID;
			if ($this->EquipmentItem->save($this->request->data)) {
				$this->_success(__('New item has been Added'), $this->referer());
			} else {
				$validationMsgs = Hash::extract($this->EquipmentItem->validationErrors, '{s}.{n}');
				if (empty($validationMsgs)) {
					$validationMsgs	 = 'The equipment item could not be saved. Check your imput and try again';
				} else {
					$validationMsgs = implode('. ', $validationMsgs);
				}
				$this->_failure(__($validationMsgs), $this->referer(), array('class' => 'alert alert-danger'));
			}
		}
	}

/**
 * edit method
 *
 * @param string $id EquipmentItem id
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null) {
		$this->EquipmentItem->id = $id;
		if (!$this->EquipmentItem->exists()) {
			throw new NotFoundException(__('Invalid equipment item'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->EquipmentItem->save($this->request->data)) {
				$this->Session->setFlash(__('The equipment item has been saved'), 'default', array('class' => 'success'));
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The equipment item could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->EquipmentItem->find('first', array('conditions' => array('id' => $id)));
		}
	}

/**
 * delete method
 *
 * @param string $id EquipmentItem id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->EquipmentItem->id = $id;
		if (!$this->EquipmentItem->exists()) {
			throw new NotFoundException(__('Invalid equipment item'));
		}

		if ($this->EquipmentItem->save(array('EquipmentItem' => array('active' => '0')))) {
			$this->Session->setFlash(__('Equipment Item deleted'));
			$this->redirect($this->referer());
		}

		$this->Session->setFlash(__('Equipment item was not deleted'));
		$this->redirect($this->referer());
	}

}
