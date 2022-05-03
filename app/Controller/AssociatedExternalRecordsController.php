<?php
App::uses('AppController', 'Controller');
/**
 * AssociatedExternalRecords Controller
 */
class AssociatedExternalRecordsController extends AppController {


/**
 * upsert method
 * updates or inserts new data
 *
 * @return void
 */
	public function upsert($merchantId = '') {
		$this->autoRender = false;
		// CHECK USER SESSION IS STILL VALID
		if ($this->Session->read('Auth.User.id')) {
				//if no form data was submitted return read mode data
				if (empty($this->request->data)) {
					$this->request->data = $this->AssociatedExternalRecord->getSalesforceUpsertViewDataByMerchantId($merchantId);
					return $this->render('/Elements/AssociatedExternalRecords/upsert_salesforce', 'ajax');

				} else {
					$this->request->data = $this->AssociatedExternalRecord->cleanUpEmptyValue($this->request->data);
					if ($this->AssociatedExternalRecord->saveAssociated($this->request->data)) {
						$this->_success(__('Data has been updated!'));
						return json_encode(['success' => true]);
					} else {
						return $this->render('/Elements/AssociatedExternalRecords/upsert_salesforce', 'ajax');
					}
				}
		} else {
			//session expired
			$this->response->statusCode(403);
		}
	}


/**
 * deleteAll method
 * Cascade deletes AssociatedExternalRecords
 *
 * @return void
 */
	public function deleteAll($id) {
		if ($this->request->is('post')) {
			if ($this->AssociatedExternalRecord->delete($id, true)) {
				$this->_success('All data has been deleted.');
			} else {
				$this->_failure('Unexpeted eerror! Data could not be deleted.');
			}
		}
		$this->redirect($this->referer());
	}
}
