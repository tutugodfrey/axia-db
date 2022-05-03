<?php

App::uses('AppController', 'Controller');

/**
 * Addresses Controller
 *
 * @property Address $Address
 */
class AddressesController extends AppController {

	public $components = array('GUIbuilder');

/**
 * business_info method
 *
 * @param string $id merchant id
 * @return void
 */
	public function business_info($id = null) {
		$businessAddresses = array();
		$merchantOwners = array();
		$bankAddresses = array();
		$merchantBank = $this->Address->Merchant->MerchantBank->getBankDataByMerchantId($id);
		if (!empty($merchantBank)) {
			$merchantBank['MerchantBank'] = $this->Address->decryptFields($merchantBank['MerchantBank']);
		}
		$businessData = $this->Address->getMerchantBusinessData($id);

		/* Create associative array of addresses for easier array data access and page structuring */
		foreach ($businessData['Address'] as $address) {
			if (substr_count($address['AddressType']['address_type_description'], 'Bank') > 0) {
				$bankAddresses = $address; /* Currently it is only possible to have one BankAddress */
			} else {
				$addressTypeKey = Inflector::slug($address['AddressType']['address_type_description'], "_");
				$businessAddresses[$addressTypeKey] = $address;
			}
		}
		$merchantOwners = $this->Address->MerchantOwner->getOwnersByMerchantId($id);
		$this->set('merchant', $this->Address->Merchant->getSummaryMerchantData($id));
		$this->set(compact('businessAddresses', 'merchantOwners', 'bankAddresses', 'merchantBank', 'businessData'));
	}

/**
 * edit method
 * 
 * @param string $id address id
 * @param string $merchantNoteId merchant note id
 * @throws NotFoundException
 * @return void
 */
	public function business_info_edit($id = null, $merchantNoteId = null) {
		$this->Address->id = $id;
		if (!$this->Address->exists()) {
			throw new NotFoundException(__('Invalid address'));
		}

		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->Address->field("merchant_id", array("id" => $id));
		// Check if the edit request is for the logged data
		$merchantNote = ClassRegistry::init('MerchantNote')->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');

		if ($MerchantChange->isPending($mId) && empty($logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'action' => 'business_info',
				$mId
			));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			//remove irrelevant validation rule.
			$this->Address->Merchant->validator()->remove('merchant_acquirer_id');
			if ($MerchantChange->editChange($this->request->data, "Address", array('deep' => true), $editType, 'business_info_edit')) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
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
				if (!empty($this->Address->validationErrors)) {
					$errors = !empty(Hash::extract($this->Address->validationErrors, '{s}.{n}'))?:Hash::extract($this->Address->validationErrors, '{n}.{s}.{n}');
				} else {
					$errors[] = 'Changes to this merchant could not be saved. Please, try again.';
				}
				$this->Session->setFlash($errors, 'Flash/listErrors', array('class' => 'alert alert-danger'));
			}
		} else {
			$this->request->data = $this->Address->getSortedLocaleInfoByMerchantId($mId);
			if (!empty($logId)) {
				$this->request->data = $this->Address->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Locale Change');
			}

		}
		$this->set($this->Address->setEditViewVars($this->request->data));
		$this->set('usStates', $this->GUIbuilder->getUSAStates());
	}

/**
 * Get Locations by Org
 * This method accepts AJAX requests and uses passed parameters in the request data
 * which can be either a Merchant.organization_id, a Merchant.region_id or a Merchant.subregion_id
 * in order to return a JSON encoded list of streets of merchats who matched the passed param.
 *
 * @return void
 */

	public function getLocations() {
		//this method can handle ajax and non-ajax calls
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
		}

		$orgId = ($this->request->data('Merchant.organization_id'))?: $this->request->data('organization_id');
		$regId = ($this->request->data('Merchant.region_id'))?: $this->request->data('region_id');
		$subRegId = ($this->request->data('Merchant.subregion_id'))?: $this->request->data('subregion_id');
		$conditions = [];
		if (!empty($orgId)) {
			$conditions['Merchant.organization_id'] = $orgId;
		}
		if (!empty($regId)) {
			$conditions['Merchant.region_id'] = $regId;
		}
		if (!empty($subRegId)) {
			$conditions['Merchant.subregion_id'] = $subRegId;
		}
		$mIds = [];
		if (!empty($conditions)) {
			$mIds = $this->Address->Merchant->find('list', ['fields' => ['id'], 'conditions' => $conditions]);
			//If no merchants match conditions return nothing.
			if (empty($mIds)) {
				echo json_encode([]);
			} else {
				//return all locations when no conditions are passed
				$locs = $this->Address->getStreetByMerchantIds($mIds);
				echo json_encode($locs);
			}
		}
		
	}
}
