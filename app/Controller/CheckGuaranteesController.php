<?php

App::uses('AppController', 'Controller');

/**
 * CheckGuarantees Controller
 *
 * @property CheckGuarantee $CheckGuarantee
 */
class CheckGuaranteesController extends AppController {

/**
 *	Load Components
 */
	public $components = array('GUIbuilder');

/**
 * edit method
 *
 * @param string $id a check guarantee id
 * @param string $merchantNoteId a note id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->CheckGuarantee->id = $id;
		if (!$this->CheckGuarantee->exists()) {
			throw new NotFoundException(__('Invalid check guarantee'));
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->CheckGuarantee->field("merchant_id", array("id" => $id));
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
			if ($MerchantChange->editChange($this->request->data, "CheckGuarantee", array('deep' => true), $editType)) {
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
			$this->request->data = $this->CheckGuarantee->get($id);
			if (!empty($logId)) {
				$this->request->data = $this->CheckGuarantee->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Check Guarantee Change');
			}
		}
		$merchant = $this->CheckGuarantee->Merchant->getSummaryMerchantData($mId);
		$this->set(compact('merchant', 'isEditLog', 'userCanApproveChanges'));
		$this->set($this->CheckGuarantee->setMenuData());
	}
}
