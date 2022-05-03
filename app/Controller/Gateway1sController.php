<?php

App::uses('AppController', 'Controller');

/**
 * Gateway1 Controller
 *
 * @property Gateway1 $Gateway1
 */
class Gateway1sController extends AppController {

/**
 *	Load Components
 */
	public $components = array('GUIbuilder');

/**
 * edit method
 *
 * @param string $id a gateway1 id
 * @param string $merchantNoteId a note id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->Gateway1->id = $id;
		if (!$this->Gateway1->exists()) {
			$this->Session->setFlash(__('No Gateway 1 data exists for this merchant, the product may not have been added properly. Try removing and re-adding the Gateway 1 product.'), 'default', ['class' => 'strong alert alert-danger']);
			$this->redirect($this->referer());
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->Gateway1->field("merchant_id", array("id" => $id));
		// Check if the edit request is for the logged data
		$merchantNote = ClassRegistry::init('MerchantNote')->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
		$isEditLog = !empty($logId);
		$userCanApproveChanges = $MerchantChange->userCanApproveChanges();

		if ($this->_hasPendChanges($mId, $logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'controller' => 'MerchantPricings',
				'action' => 'products_and_services',
				$mId
			));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			if ($MerchantChange->editChange($this->request->data, "Gateway1", array('deep' => true), $editType)) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
						'controller' => 'MerchantPricings',
						'action' => 'products_and_services',
						$mId
					);
				} else {
					$redirectUrl = array(
						'controller' => 'merchant_notes',
						'action' => 'edit',
						Hash::get($merchantNote, 'MerchantNote.id')
					);
				}
				$this->_success(__('Changes to this merchant have been saved'), $redirectUrl);
			} else {
				$this->_failure(__('Changes to this merchant could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Gateway1->get($id);
			if (!empty($logId)) {
				$this->request->data = $this->Gateway1->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Gateway 1 Change');
			}
		}
		$merchant = $this->Gateway1->Merchant->getSummaryMerchantData($mId);
		$gateways = $this->Gateway1->Gateway->getList();
		$this->set(compact('merchant', 'isEditLog', 'userCanApproveChanges', 'gateways'));
	}
}
