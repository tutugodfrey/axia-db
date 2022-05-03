<?php

App::uses('AppController', 'Controller');

/**
 * EquipmentProgrammings Controller
 *
 * @property EquipmentProgramming $EquipmentProgramming
 */
class EquipmentProgrammingsController extends AppController {

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add($id) {
		if ($this->request->is('post')) {
			$this->EquipmentProgramming->create();
			if ($this->EquipmentProgramming->saveAssociated($this->request->data, array('deep' => true))) {
				$this->Session->setFlash(__('The equipment programming saved!'), 'default', array('class' => 'success'));
				$this->redirect(array('controller' => 'Merchants', 'action' => 'equipment', $id));
			} else {
				$this->Session->setFlash(__('The equipment programming could not be saved. Please, try again.'));
			}
		}
		$merchant = $this->EquipmentProgramming->Merchant->getSummaryMerchantData($id);
		$gateways = $this->EquipmentProgramming->Gateway->getSortedGatewayList();
		$programmingTypes = $this->EquipmentProgramming->EquipmentProgrammingTypeXref->getHasManyProgrammingTypesList();
		$this->set(compact('merchant', 'gateways', 'programmingTypes'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {

		$this->EquipmentProgramming->id = $id;
		if (!$this->EquipmentProgramming->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Check if any pre-existing EquipmentProgrammingTypeXref were removed by the user */
			$this->request->data = $this->EquipmentProgramming->EquipmentProgrammingTypeXref->checkRemoveProgrammingTypesXref($this->request->data);
			$this->EquipmentProgramming->id = $this->request->data['EquipmentProgramming']['id'];
			if ($this->EquipmentProgramming->saveAssociated($this->request->data, array('deep' => true))) {
				$this->Session->setFlash(__('The equipment programming has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('controller' => 'Merchants', 'action' => 'equipment', $this->request->data['EquipmentProgramming']['merchant_id']));
			} else {
				$this->Session->setFlash(__('The equipment programming could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->EquipmentProgramming->findEquipmentProgrammingById($id);
			$programmingTypes = $this->EquipmentProgramming->EquipmentProgrammingTypeXref->getHasManyProgrammingTypesList();
			/* Modify request->data['EquipmentProgrammingTypeXref'] so that its indices match the Programming types list */
			foreach ($programmingTypes['EquipmentProgrammingTypeXref'] as $key1 => $val) {
				foreach ($this->request->data['EquipmentProgrammingTypeXref'] as $key2 => $val2) {
					if ((string) key($val) === $val2['programming_type']) {
						$dataCopy[$key1] = $this->request->data['EquipmentProgrammingTypeXref'][$key2];
						continue;
					}
				}
			}
			if (!empty($dataCopy))
				$this->request->data['EquipmentProgrammingTypeXref'] = $dataCopy;
		}

		$gateways = $this->EquipmentProgramming->Gateway->getSortedGatewayList();
		$merchant = $this->EquipmentProgramming->Merchant->getSummaryMerchantData($this->request->data['EquipmentProgramming']['merchant_id']);
		$this->set(compact('programmingTypes', 'merchant', 'gateways'));
	}

	/**
	 * delete method
	 * 
	 * Virtualy not physicaly deletes. Data is NOT deleted from database 
	 * Changes the status to DEL which causes to not display.
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
		$this->EquipmentProgramming->id = $id;
		if (!$this->EquipmentProgramming->exists()) {
			throw new NotFoundException(__('Invalid equipment programming'));
		}

		if ($this->EquipmentProgramming->save(
						array('EquipmentProgramming' => array(
											'status' => 'DEL'))
				)) {
			$this->Session->setFlash(__('Equipment Deleted'));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('ERROR: Could not delete equipment, try again or contact webmaster'));
	}

}
