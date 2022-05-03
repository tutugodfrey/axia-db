<?php

App::uses('AppController', 'Controller');

/**
 * Aches Controller
 *
 * @property Ach $Ach
 */
class AchesController extends AppController {

/**
 *	Load Components
 */
	public $components = array('GUIbuilder');

/**
 * edit method
 *
 * @param string $id an Ach id.
 * @param string $merchantNoteId a merchant id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->Ach->id = $id;
		if (!$this->Ach->exists()) {
			throw new NotFoundException(__('Invalid ach'));
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->Ach->field("merchant_id", array("id" => $id));
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
			//Encrypt before every change request
			$this->request->data['Ach'] = array_map('trim', $this->request->data['Ach']);
			$this->Ach->encryptData($this->request->data);
			if ($MerchantChange->editChange($this->request->data, "Ach", array('deep' => true), $editType)) {
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
			$this->request->data = $this->Ach->getDataById($id, false);
			if (!empty($logId)) {
				$this->request->data = $this->Ach->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'ACH Product Change');
			}
		}
		//Decrypt all encrypted fields
		$this->request->data['Ach'] = $this->Ach->decryptFields($this->request->data['Ach']);
		$merchant = $this->Ach->Merchant->getSummaryMerchantData($mId);
		$achProviders = $this->Ach->AchProvider->getList();
		$this->set(compact('merchant', 'achProviders', 'isEditLog', 'userCanApproveChanges'));
	}
}
