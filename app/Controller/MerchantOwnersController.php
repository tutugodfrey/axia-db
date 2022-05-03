<?php

App::uses('AppController', 'Controller');

/**
 * MerchantOwners Controller
 *
 * @property MerchantOwner $MerchantOwner
 */
class MerchantOwnersController extends AppController {

	public $components = array('GUIbuilder');

/**
 * edit method
 *
 * this method handles edit-many/save-many merchant owners through the Merchant model
 *
 * @param string $id a MerchantOwner id
 * @param string $merchantNoteId a MerchantNote id
 * @return void
 * @throws NotFoundException
 * @throws InvalidArgumentException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$MerchantChange = ClassRegistry::init('MerchantChange');
		if (!empty($id)) {
			$this->MerchantOwner->id = $id;
			if (!$this->MerchantOwner->exists() && !$MerchantChange->foreignExists($id, 'MerchantOwner')) {
				throw new NotFoundException(__('Invalid merchant'));
			}
		} elseif (empty($this->request->params['named']['merchant_id'])) {
			throw new InvalidArgumentException(__("{$this->name} {$this->action} expects parameter id or named parameter merchant_id but none was passed."));
		}
		$merchantNote = null;
		$logId = null;
		if (!empty($this->request->params['named']['merchant_id'])) {
			$mId = $this->request->params['named']['merchant_id'];
		} else {
			$mId = $this->MerchantOwner->field("merchant_id", array("id" => $id));
		}
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

		$addressTypes = $this->MerchantOwner->Address->AddressType->getAllAddressTypeIds();
		//Owner zip not required from this view action
		$this->MerchantOwner->Address->validator()->remove('address_zip');
		//remove irrelevant validation rule.
		$this->MerchantOwner->Merchant->validator()->remove('merchant_acquirer_id');
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);

			/* Clean up request data by removing empty entries */
			$this->request->data = $this->MerchantOwner->cleanRequestData($this->request->data);

			//Encrypt fields for all change requests
			$this->MerchantOwner->Merchant->encryptData($this->request->data);
			foreach ($this->request->data['MerchantOwner'] as $nth => $owner) {
				//if this is a new record creation a new uuid must be set.
				if (empty($this->request->data("MerchantOwner.$nth.id"))) {
					$this->request->data['MerchantOwner'][$nth]['id'] = CakeText::uuid();
				}
			}

			if ($MerchantChange->editChange($this->request->data, "MerchantOwner", array('deep' => true), $editType)) {
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

				$validationMsgs = Hash::extract($this->MerchantOwner->validationErrors, '{n}.{s}.{n}');
				if (empty($validationMsgs)) {
					$validationMsgs	 = 'Changes to this merchant could not be saved. Please, try again.';
				} else {
					$validationMsgs = implode('. ', $validationMsgs);
				}
				$this->_failure(__($validationMsgs), $this->referer(), array('class' => 'alert alert-danger'));
			}
		} else {
			//MerchantOwners data is returned with its indexes matching corresponding Addresses data array indexes
			$this->request->data = $this->MerchantOwner->getOwnersByMerchantId($mId);

			if (!empty($logId)) {
				$this->request->data = $this->MerchantOwner->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Owner Information Change');
			}
		}
		/* Decrypt all data */
		$this->request->data['Merchant'] = $this->MerchantOwner->decryptFields($this->request->data('Merchant'));
		$merchOwners = Hash::extract($this->request->data, "MerchantOwner.{n}");
		foreach ($merchOwners as $nth => $owner) {
			$this->request->data['MerchantOwner'][$nth] = $this->MerchantOwner->decryptFields($owner);
			$this->request->data['MerchantOwner'][$nth]['Address']['merchant_id'] = $this->request->data['Merchant']['id'];
			$this->request->data['MerchantOwner'][$nth]['Address']['address_type_id'] = $addressTypes['owner_address'];
		}
		$merchant = $this->MerchantOwner->Merchant->getSummaryMerchantData($mId);
		$this->set(compact('merchant', 'addressTypes', 'userCanApproveChanges', 'isEditLog'));
		$this->set('usStates', $this->GUIbuilder->getUSAStates());
	}

/**
 * ajaxDisplayDecryptedVal method
 *
 * Handle only ajax request to display encrypted data.
 *
 * @param string $ownerId an owner id
 * @param string $salt security token
 * @param string $fieldName field to be decrypted
 * @return void
 */
	public function ajaxDisplayDecryptedVal($ownerId, $salt, $fieldName) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Additional security check */
			if ($this->MerchantOwner->checkDecrypPassword($salt)) {
				$viewTitle = Inflector::humanize($fieldName); //remove the te_ prefix from the field name
				$decriptedValue = $this->MerchantOwner->field($fieldName, array('id' => $ownerId));
				$decriptedValue = $this->MerchantOwner->decrypt($decriptedValue, Configure::read('Security.OpenSSL.key'));
				$this->set(compact('decriptedValue', 'viewTitle'));
				$this->render('/Elements/AjaxElements/modalDisplayDecrypedData', 'ajax');
			} else {
				/* Return status 401: Unathorized */
				$this->response->statusCode(401);
			}
		}
	}

/**
 * ajaxAddAndEdit method
 *
 * Handle only ajax request to edit or add data.
 * This method only handles the requests, any data submitted is handled within corresponding edit/add actions.
 *
 * @param string $id a Merchant id
 * @param string $salt security access token
 * @param string $merchantNoteId a MerchantNote id
 * @return void
 * @throws NotFoundException
 */
	public function ajaxAddAndEdit($id, $salt, $merchantNoteId = null) {
		if (empty($id)) {
			throw new NotFoundException(__('Invalid Merchant id'));
		}
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$merchantNote = null;
			$logId = null;
			// Check if the edit request is for the logged data
			$merchantNote = $this->MerchantOwner->Merchant->MerchantNote->getNoteById($merchantNoteId, array('contain' => false));
			$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
			if ($this->_hasPendChanges($id, $logId)) {
				$message = MerchantChange::PEND_MSG;
				$close = false; //prevent bootstraps window close functionality since this is in a modal window
				$class = 'alert alert-danger';
				$this->set(compact('message', 'class', 'close'));
				$this->render('/Elements/Flash/alert', 'ajax');
			} else {
				/* Additional security check */
				if ($this->MerchantOwner->checkDecrypPassword($salt)) {
					$ownId = $this->MerchantOwner->field('id', array('merchant_id' => $id));
					$urlAction = array('action' => 'edit', $ownId, $merchantNoteId);
					$isEditLog = !empty($logId);
					$userCanApproveChanges = $this->MerchantOwner->Merchant->MerchantChange->userCanApproveChanges();

					$this->request->data = $this->MerchantOwner->getOwnersByMerchantId($id);
					if (!empty($logId)) {
						$this->request->data = $this->MerchantOwner->mergeLoggedData($logId, $this->request->data);
						$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
					} else {
						$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($id, 'Change Request', 'Owner SSN Change');
					}
					//decript all encrypted fields
					foreach ($this->request->data['MerchantOwner'] as $nth => $owner) {
						$this->request->data['MerchantOwner'][$nth] = $this->MerchantOwner->decryptFields($owner);
					}

					$this->set(compact('urlAction', 'isEditLog', 'userCanApproveChanges'));
					$this->render('/Elements/AjaxElements/modalMerchantOwnersAddEdit', 'ajax');
				} else {
					/* Return status 401: Unathorized */
					$this->response->statusCode(401);
				}
			}
		}
	}

/**
 * delete method
 *
 * @param string $id MerchantOwner.id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id) {
		if (!$this->request->is('post')) {
			$this->_failure(__('ERROR: Request type not allowed!'), $this->referer());
		}
		$this->MerchantOwner->id = $id;
		if (!$this->MerchantOwner->exists()) {
			$this->_failure(__('ERROR: Invalid merchant owner id!'), $this->referer());
		}

		if ($this->MerchantOwner->delete($id, true)) {
			$this->_success(__('Owner/Officer deleted'), $this->referer());
		}
		$this->_failure(__('ERROR: Something went wrong! Try again.'), $this->referer());
	}
}
