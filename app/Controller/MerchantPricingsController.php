<?php

App::uses('AppController', 'Controller');

/**
 * MerchantPricings Controller
 *
 * @property MerchantPricing $MerchantPricing
 */
class MerchantPricingsController extends AppController {

	public $components = array('GUIbuilder');

/**
 * productsAndServices method
 *
 * calls view function to make all merchant data available in the notes view
 *
 * @param string $id merchant id
 * @return void
 * @throws NotFoundException
 */
	public function products_and_services($id = null) {
		if (!$this->MerchantPricing->Merchant->exists($id)) {
			throw new NotFoundException(__('Invalid Merchant Id'));
		}
		$this->set($this->MerchantPricing->getViewData($id));
	}

/**
 * edit method
 *
 * @param string $id merchant pricing id
 * @param string $merchantNoteId a merchant note id
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null, $merchantNoteId = null) {
		if (!$this->MerchantPricing->exists($id)) {
			throw new NotFoundException(__('Invalid merchant pricing'));
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->MerchantPricing->field("merchant_id", array("id" => $id));
		// Check if the edit request is for the logged data
		$merchantNote = ClassRegistry::init('MerchantNote')->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');

		if ($MerchantChange->isPending($mId) && empty($logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'action' => 'products_and_services',
				$mId
			));
		}
		//set dynamic validation
		$this->MerchantPricing->setDynamicValidation(['MerchantPricing' =>['merchant_id' => $mId]]);
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			if ($this->request->data('MerchantPricing.gateway_access_fee') > 0) {
				$this->MerchantPricing->validator()->add('gateway_id', 'required', array(
					'rule' => 'notBlank',
					'message' => 'Must select Gateway when Gateway Access Fee is set'
				));
			}

			if ($MerchantChange->editChange($this->request->data, "MerchantPricing", array('deep' => true), $editType)) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
						'action' => 'products_and_services',
						$this->request->data('MerchantPricing.merchant_id')
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
			$this->request->data = $this->MerchantPricing->getEditViewData($id);
			if (!empty($logId)) {
				$this->request->data = $this->MerchantPricing->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Products & Services Change');
			}
		}
		//Removing rules for this field so that users can save after clearing any gateway access fee
		$this->MerchantPricing->validator()->remove('gateway_id');
		$this->set($this->MerchantPricing->setFormMenuData($this->request->data));
	}

}
