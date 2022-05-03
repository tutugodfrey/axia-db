<?php

App::uses('AppController', 'Controller');

/**
 * MerchantBanks Controller
 *
 * @property MerchantBank $MerchantBank
 */
class MerchantBanksController extends AppController {

/**
 * Components in use
 * 
 * @var array
 */
	public $components = array(
		'GUIbuilder',
	);

/**
 * edit method
 *
 * @param string $id MerchantBank.id
 * @param string $merchantNoteId a note id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->MerchantBank->id = $id;
		if (!$this->MerchantBank->exists()) {
			throw new NotFoundException(__('Invalid merchant bank'));
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->MerchantBank->field("merchant_id", array("id" => $id));
		// Check if the edit request is for the logged data
		$merchantNote = ClassRegistry::init('MerchantNote')->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
		$isEditLog = !empty($logId);
		$userCanApproveChanges = $MerchantChange->userCanApproveChanges();

		if ($this->_hasPendChanges($mId, $logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'controller' => 'Addresses',
				'action' => 'business_info',
				$mId
			));
		}
		//Bank address zip not required, remove validation rule
		$this->MerchantBank->Merchant->Address->validator()->remove('address_zip');
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			//Encrypt bank account fields for all change requests
			$this->MerchantBank->encryptData($this->request->data);
			$ModelToSave = (empty($this->request->data('Address')))?'MerchantBank':'Address';

			if ($MerchantChange->editChange($this->request->data, $ModelToSave, array('deep' => true), $editType)) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
						'controller' => 'Addresses',
						'action' => 'business_info',
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
				$this->request->data['MerchantBank'] = $this->MerchantBank->decryptFields($this->request->data['MerchantBank']);
				$this->_failure(__('Changes to this merchant could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->MerchantBank->getBankDataByMerchantId($mId);
			$this->request->data += $this->MerchantBank->Merchant->Address->getBankAddressByMerchantId($mId);
			if (!empty($logId)) {
				$this->request->data = $this->MerchantBank->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Bank Routing / DDA Change');
			}
			//Decrypt all encrypted fields
			$this->request->data['MerchantBank'] = $this->MerchantBank->decryptFields($this->request->data['MerchantBank']);

		}
		$merchant = $this->MerchantBank->Merchant->getSummaryMerchantData($mId);
		$this->set(compact('merchant', 'isEditLog', 'userCanApproveChanges'));
	}

/**
 * delete method
 *
 * @param string $id Model.id
 * @return void
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->MerchantBank->id = $id;
		if (!$this->MerchantBank->exists()) {
			throw new NotFoundException(__('Invalid merchant bank'));
		}
		if ($this->MerchantBank->delete()) {
			$this->Session->setFlash(__('Merchant bank deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Merchant bank was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * ajaxAddAndEdit method
 * 
 * Handle only ajax request to edit or add data. 
 * This method only handles the requests, any data submitted is handled within corresponding edit/add actions.  
 *
 * @param string $id a MerchantBank.id
 * @param string $salt security salt string
 * @param string $merchantNoteId merchant note id
 * @return void
 */
	public function ajaxAddAndEdit($id, $salt, $merchantNoteId = null) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$merchantNote = null;
			$logId = null;
			$mId = $this->MerchantBank->field("merchant_id", array("id" => $id));
			// Check if the edit request is for the logged data
			$merchantNote = $this->MerchantBank->Merchant->MerchantNote->getNoteById($merchantNoteId, array('contain' => false));
			$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
			//Check for pending change request
			if ($this->_hasPendChanges($mId, $logId)) {
				$message = MerchantChange::PEND_MSG;
				$close = false; //prevent bootstraps window close functionality since this is in a modal window
				$class = 'alert alert-danger';
				$this->set(compact('message', 'class', 'close'));
				$this->render('/Elements/Flash/alert', 'ajax');
			} else {
				//Additional security check
				if ($this->MerchantBank->checkDecrypPassword($salt)) {
					$urlAction = array('action' => 'edit', $id, $merchantNoteId);
					$isEditLog = !empty($logId);
					$userCanApproveChanges = $this->MerchantBank->Merchant->MerchantChange->userCanApproveChanges();

					$this->request->data = $this->MerchantBank->getBankDataByMerchantId($mId);
					if (!empty($logId)) {
						$this->request->data = $this->MerchantBank->mergeLoggedData($logId, $this->request->data);
						$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
					} else {
						$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Bank Routing / DDA Change');
					}
					///decript all encrypted fields
					if (!empty($this->request->data['MerchantBank'])) {
						$this->request->data['MerchantBank'] = $this->MerchantBank->decryptFields($this->request->data['MerchantBank']);
					}
					$this->set(compact('urlAction', 'isEditLog', 'userCanApproveChanges'));
					$this->render('/Elements/AjaxElements/modalMerchantBanksAddEdit', 'ajax');
				} else {
					/* Return status 401: Unathorized */
					$this->response->statusCode(401);
				}
			}
		}
	}

/**
 * ajaxDisplayDecryptedVal method
 * 
 * Handle only ajax request to display encrypted data. 
 *
 * @param string $bankId Model.id
 * @param string $salt security access token
 * @param string $fieldName a MerchantBanks field name containing encrypted data
 * @return void 
 */
	public function ajaxDisplayDecryptedVal($bankId, $salt, $fieldName) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Additional security check */
			if ($this->MerchantBank->checkDecrypPassword($salt)) {
				$viewTitle = Inflector::humanize($fieldName);
				$decriptedValue = $this->MerchantBank->field($fieldName, array('id' => $bankId));
				$decriptedValue = $this->MerchantBank->decrypt($decriptedValue, Configure::read('Security.OpenSSL.key'));
				$this->set(compact('decriptedValue', 'viewTitle'));
				$this->render('/Elements/AjaxElements/modalDisplayDecrypedData', 'ajax');
			} else {
				/* Return status 401: Unathorized */
				$this->response->statusCode(401);
			}
		}
	}

}
