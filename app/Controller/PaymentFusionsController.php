<?php

App::uses('AppController', 'Controller');

/**
 * PaymentFusion Controller
 *
 */
class PaymentFusionsController extends AppController {

/**
 *	Load Components
 */
	public $components = array('GUIbuilder');

/**
 * edit method
 *
 * @param string $id a PaymentFusion id
 * @param string $merchantNoteId a note id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->PaymentFusion->id = $id;
		if (!$this->PaymentFusion->exists()) {
			$this->Session->setFlash(__('No Payment Fusion data exists for this merchant, the product may not have been added properly. Try removing and re-adding the product.'), 'default', ['class' => 'strong alert alert-danger']);
			$this->redirect($this->referer());
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		$mId = $this->PaymentFusion->field("merchant_id", array("id" => $id));
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
			if ($MerchantChange->editChange($this->request->data, "PaymentFusion", array('deep' => true), $editType)) {
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
			$this->request->data = $this->PaymentFusion->getEditViewData($id);
			if (!empty($this->request->data['PaymentFusionsProductFeature'])) {
				$index = 0;
				foreach ($this->request->data['PaymentFusionsProductFeature'] as $productFeature) {
					$this->request->data['ProductFeature']['ProductFeature'][$index] = $productFeature['product_feature_id'];
					$index++;
				}
			}
			if (!empty($logId)) {
				$this->request->data = $this->PaymentFusion->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Payment Fusion Change');
			}
		}
		$merchant = $this->PaymentFusion->Merchant->getSummaryMerchantData($mId);
		$productFeatures = $this->PaymentFusion->ProductFeature->getList([
			'conditions' => array("products_services_type_id = (SELECT id from products_services_types where products_services_description = 'Payment Fusion')")
		]);
		$this->set(compact('merchant', 'isEditLog', 'userCanApproveChanges', 'productFeatures'));
	}
}
