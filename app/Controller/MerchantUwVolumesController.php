<?php

App::uses('AppController', 'Controller');

/**
 * Underwritings Controller
 *
 * @property MerchantUwVolume $MerchantUwVolume
 */
class MerchantUwVolumesController extends AppController {

/**
 * Components in use
 * 
 * @var array
 */
	public $components = array(
		'GUIbuilder',
	);

/**
 * view method
 *
 * @param string $id MerchantUwVolume id
 * @return void
 * @throws NotFoundException
 */
	public function view($id = null) {
		if (!$this->MerchantUwVolume->exists($id)) {
			throw new NotFoundException(__('Invalid underwriting'));
		}
		$options = array('conditions' => array('MerchantUwVolume.' . $this->MerchantUwVolume->primaryKey => $id));
		$this->set('underwriting', $this->MerchantUwVolume->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->MerchantUwVolume->create();
			if ($this->MerchantUwVolume->save($this->request->data)) {
				$this->Session->setFlash(__('The underwriting has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The underwriting could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 * This method is only required to process post/put requests. Data request are done via self::ajaxAddAndEdit
 * 
 * @param string $id MerchantUwVolume.id
 * @param string $merchantNoteId merchant note id
 * @return void
 */
	public function edit($id = null, $merchantNoteId = null) {
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$merchantNote = null;
		$logId = null;

		if (!empty($this->request->params['named']['merchant_id'])) {
			$mId = $this->request->params['named']['merchant_id'];
		} else {
			$mId = $this->MerchantUwVolume->field("merchant_id", array("id" => $id));
		}

		// Check if the edit request is for the logged data
		$merchantNote = ClassRegistry::init('MerchantNote')->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
		$isEditLog = !empty($logId);
		$userCanApproveChanges = $MerchantChange->userCanApproveChanges();

		if ($this->_hasPendChanges($mId, $logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'controller' => 'MerchantUws',
				'action' => 'view',
				$mId
			));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			//Encrypt MerchantUwVolume T&E numbers contained as associated data in MerchantUw
			$this->MerchantUwVolume->encryptData($this->request->data);
			if ($MerchantChange->editChange($this->request->data, "MerchantUwVolume", array('deep' => true), $editType)) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
						'controller' => 'MerchantUws',
						'action' => 'view',
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
		}
	}

/**
 * ajaxAddAndEdit method
 * 
 * Handle only ajax request to edit or add data. 
 * This method only handles the requests, any data submitted is handled within corresponding edit/add actions.  
 *
 * @param string $id a MerchantUwVolumes.id | Merchant.id depending on whether the request came from Underwriting or merchant notes.
 * 					 If came from underwriting the a merchant.id is being passed otherwise a MerchantUwVolumes.id
 * @param string $salt security access token
 * @param string $merchantNoteId merchant note id
 * @return void
 * @throws NotFoundException
 */
	public function ajaxAddAndEdit($id, $salt, $merchantNoteId = null) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$merchantNote = null;
			$logId = null;

			// Check if the edit request is for the logged data
			$merchantNote = $this->MerchantUwVolume->Merchant->MerchantNote->getNoteById($merchantNoteId, array('contain' => false));
			$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
			//determine if a merchant id was passed
			if ($this->MerchantUwVolume->Merchant->exists($id)) {
				$merchantId = $id;
			} else {
				$merchantId = $this->MerchantUwVolume->field("merchant_id", array("id" => $id));
			}
			//Check for pending change request
			if ($this->_hasPendChanges($merchantId, $logId)) {
				$message = MerchantChange::PEND_MSG;
				$close = false; //prevent bootstraps window close functionality since this is in a modal window
				$class = 'alert alert-danger';
				$this->set(compact('message', 'class', 'close'));
				$this->render('/Elements/Flash/alert', 'ajax');
			} else {
				/* Additional security check */
				if ($this->MerchantUwVolume->checkDecrypPassword($salt)) {
					$isEditLog = !empty($logId);
					$userCanApproveChanges = $this->MerchantUwVolume->Merchant->MerchantChange->userCanApproveChanges();
					$this->request->data = $this->MerchantUwVolume->getDataByMerchantId($merchantId);
					//is possible this merchant has no MerchantUwVolume data
					if (empty($this->request->data)) {
						$this->request->data["MerchantUwVolume"] = [
								//ChangeRequest behavior requires a Model id to be set
								"id" => CakeText::uuid(),
								"merchant_id" => $merchantId
							];
					}
					if (!empty($logId)) {
						$this->request->data = $this->MerchantUwVolume->mergeLoggedData($logId, $this->request->data);
						$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
					} else {
						$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($merchantId, 'Change Request', 'T&E Change');
					}

					$this->request->data['MerchantUwVolume'] = $this->MerchantUwVolume->decryptFields($this->request->data['MerchantUwVolume']);
					$urlAction = Router::url(array('action' => 'edit', Hash::get($this->request->data, "MerchantUwVolume.id"), $merchantNoteId, 'merchant_id' => $merchantId));
					$this->set(compact('urlAction', 'isEditLog', 'userCanApproveChanges', 'merchantId'));
					$this->render('/Elements/AjaxElements/modalTAndESectionAddEdit', 'ajax');
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
 * @param string $merchantId a merchant id
 * @param string $salt security access token
 * @param string $fieldName a "MerchantUwVolume" field
 * @return void
 */
	public function ajaxDisplayDecryptedVal($merchantId, $salt, $fieldName) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Additional security check */
			if ($this->MerchantUwVolume->checkDecrypPassword($salt)) {

				$viewTitle = Inflector::humanize(substr($fieldName, 3)); //remove the te_ prefix from the field name
				$decriptedValue = $this->MerchantUwVolume->field($fieldName, array('merchant_id' => $merchantId));
				$decriptedValue = $this->MerchantUwVolume->decrypt($decriptedValue, Configure::read('Security.OpenSSL.key'));

				$this->set(compact('decriptedValue', 'viewTitle'));
				$this->render('/Elements/AjaxElements/modalDisplayDecrypedData', 'ajax');
			} else {
				/* Return status 401: Unathorized */
				$this->response->statusCode(401);
			}
		}
	}

/**
 * delete method
 *
 * @param string $id MerchantUwVolume id
 * @return void
 * @throws NotFoundException
 */
	public function delete($id = null) {
		$this->MerchantUwVolume->id = $id;
		if (!$this->MerchantUwVolume->exists()) {
			throw new NotFoundException(__('Invalid underwriting'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->MerchantUwVolume->delete()) {
			$this->Session->setFlash(__('MerchantUwVolume deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('MerchantUwVolume was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
