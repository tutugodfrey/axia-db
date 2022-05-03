<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property ProductSetting $ProductSetting
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class ProductSettingsController extends AppController {

/**
 *	Load Components
 */
	public $components = array('GUIbuilder');

/**
 * edit method
 *
 * @param string $id a virtual_check_webs.id
 * @param string $merchantNoteId a note id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->ProductSetting->id = $id;
		if (!$this->ProductSetting->exists()) {
			throw new NotFoundException(__('Invalid ACH Web Based Product'));
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->ProductSetting->field("merchant_id", array("id" => $id));
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
		$options = ['contain' => ["ProductsServicesType"]];
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			if ($MerchantChange->editChange($this->request->data, "ProductSetting", array('deep' => true), $editType)) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
						'controller' => 'MerchantPricings',
						'action' => 'products_and_services',
						$mId
					);
				} else {
					$redirectUrl = array(
						'controller' => 'MerchantNotes',
						'action' => 'edit',
						Hash::get($merchantNote, 'MerchantNote.id')
					);
				}
				$this->_success(__('Changes to this merchant have been saved'), $redirectUrl);
			} else {
				$this->_failure(__('Changes to this merchant could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->ProductSetting->get($id, $options);
			if (!empty($logId)) {
				$this->request->data = $this->ProductSetting->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', "Product Settings Change");
			}
		}
		$productData = $this->ProductSetting->ProductsServicesType->get($this->request->data['ProductSetting']['products_services_type_id']);
		$merchant = $this->ProductSetting->Merchant->getSummaryMerchantData($mId);
		$customLabels = $productData['ProductsServicesType']['custom_labels'];
		$productFeatures = $this->ProductSetting->ProductFeature->find('list', ['fields' => ['id', 'feature_name', 'products_services_type_id']]);
		$this->set(compact('productData', 'merchant', 'isEditLog', 'userCanApproveChanges', 'customLabels', 'productFeatures'));
	}
}
