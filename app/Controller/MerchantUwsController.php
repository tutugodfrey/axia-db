<?php

App::uses('AppController', 'Controller');
App::uses('RolePermissions', 'Lib');

/**
 * MerchantUws Controller
 *
 * @property MerchantUw $MerchantUw
 */
class MerchantUwsController extends AppController {

	public $components = [
		'GUIbuilder',
		'CsvHandler',
		'Search.Prg' => [
			'commonProcess' => ['paramType' => 'querystring'],
			'presetForm' => ['paramType' => 'querystring']
		],
	];

/**
 * index method
 *
 * @return void
 */
	public function index() {
		if (empty($this->request->query)) {
			$filterParams = $this->MerchantUw->getDefaultSearchValues();
			//Set for search form values
			$this->request->query = $filterParams;
		} elseif (empty($this->request->query['user_id'])) {
			$this->request->query['user_id'] = $this->MerchantUw->Merchant->User->getDefaultUserSearch();
		}

		$filterParams = $this->request->query;
		//Paginate Merchant instead of MerchantUw
		$this->Paginator->settings = $this->MerchantUw->setPaginatorSettings($filterParams);
		$merchantUws = $this->Paginator->paginate('Merchant');

		if (!empty($merchantUws)) {
			$uwStatuses = ClassRegistry::init('UwStatus')->find('all', array('order' => array('UwStatus.priority' => 'asc')));
			//Reindex UwStatusMerchantXref data to set correct column positioning
			foreach ($merchantUws as $key => $data) {
				$merchantUws[$key]['UwStatusMerchantXref'] = $this->MerchantUw->reIndexUwMerchantXrefsData($uwStatuses, $data['UwStatusMerchantXref']);
			}
			unset($data);
		} else {
			$uwStatuses = array();
		}
		$appQuantities = GUIbuilderComponent::getAppQuantityTypeList();
		$optnsMonths = $this->GUIbuilder->getDatePartOptns('m');
		$optnsYears = $this->GUIbuilder->getDatePartOptns('y');
		$users = $this->MerchantUw->Merchant->User->getEntityManagerUsersList(true);
		//This count will be used to align UwStatus columns with their corresponding data
		$uwStatusCount = count($uwStatuses);
		$this->set(compact('optnsMonths', 'optnsYears', 'users', 'uwStatusCount', 'appQuantities'));
		$this->set('merchantUws', $merchantUws);
	}

/**
 * view method
 *
 * @param string $id Merchant id
 * @param string $statusXrefId status
 * @return void
 * @throws NotFoundException
 */
	public function view($id = null, $statusXrefId = null) {
		$this->MerchantUw->Merchant->id = $id;
		if (!$this->MerchantUw->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		$merchant = $this->MerchantUw->getUwDataByMerchantId($id);
		$assocUwData = $this->MerchantUw->getAssociatedUwLists($merchant);
		//All [*.]MerchantXref (suffixed) data needs to be modified so that it aligns properly on the view
		$merchant['Merchant']['UwStatusMerchantXref'] = (!empty($merchant['Merchant']['UwStatusMerchantXref'])) ? $this->MerchantUw->reIndexUwMerchantXrefsData($assocUwData['uwSatuses'], $merchant['Merchant']['UwStatusMerchantXref']) : '';

		$merchant['Merchant']['UwApprovalinfoMerchantXref'] = (!empty($merchant['Merchant']['UwApprovalinfoMerchantXref'])) ? $this->MerchantUw->reIndexUwMerchantXrefsData($assocUwData['approvalInfos'], $merchant['Merchant']['UwApprovalinfoMerchantXref']) : '';

		if (!empty($statusXrefId)) {
			if ($this->MerchantUw->emailRepUwStatus($merchant, $statusXrefId)) {
				$this->Session->setFlash(__('Update email sent to ' . h($merchant['User']['user_email']) . ' <img src="/img/icon_email.gif" />'), 'default', array(
				'class' => 'success'));
			} else {
				$this->Session->setFlash(__('Error: Update email could not be sent, try again or contact admin.'));
			}
			$this->redirect(array('action' => 'view/' . $merchant['Merchant']['id']));
		}
		$riskData = $this->MerchantUw->Merchant->UsersProductsRisk->getByMerchant($id);
		$profitProjections = $this->MerchantUw->Merchant->ProfitProjection->getGroupedByProductCategory($id);
		$this->set(compact('merchant', 'assocUwData', 'riskData', 'profitProjections'));
	}

/**
 * edit method
 * This method adds and edits records depending on whether the $id is empty
 *
 * @param string $id MerchantUw.id
 * @param string $merchantNoteId merchant note id
 * @namedParam $this->request->params['named']['merchant_id'] a merchant_id passed as a named param through request data
 * @throws NotFoundException
 * @throws InvalidArgumentException
 * @return void
 */
	public function edit($id = null, $merchantNoteId = null) {
		$MerchantChange = ClassRegistry::init('MerchantChange');
		if (!empty($id)) {
			//The Model.id will not exist if request is to edit the logged ChangeRequest data of a newly created record
			//When this is the case the new record id exists only in the LoggableLogs.foreingKey until the request is approved.
			if (!$this->MerchantUw->exists($id) && !$MerchantChange->foreignExists($id, 'MerchantUw')) {
				throw new NotFoundException(__('Invalid merchant uw'));
			}
		} elseif (empty($this->request->params['named']['merchant_id'])) {
			throw new InvalidArgumentException(__("{$this->name} {$this->action} expects parameter id or named parameter merchant_id but none was passed."));
		}
		$merchantNote = null;
		$logId = null;

		if (!empty($this->request->params['named']['merchant_id'])) {
			$mId = $this->request->params['named']['merchant_id'];
		} else {
			$mId = $this->MerchantUw->field("merchant_id", array("id" => $id));
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
			//Cleanup all MerchantXref data
			$this->request->data['Merchant'] = $this->MerchantUw->filterEmptyElements($this->request->data['Merchant']);
			$this->MerchantUw->setFinalDate($this->request->data);
			//ChangeRequest behavior requires a set id for a bulkedData save
			//if this is a new record creation a new uuid must be set.
			if (empty($id) || empty($this->request->data('MerchantUw.id'))) {
				$this->request->data['MerchantUw']['id'] = CakeText::uuid();
			}
			if ($this->MerchantUw->saveAll($this->request->data, array('validate' => 'only', 'deep' => true))) {
				//Encrypt MerchantUwVolume T&E numbers contained as associated data in MerchantUw
				$this->MerchantUw->MerchantUwVolume->encryptData($this->request->data['MerchantUw']);

				//Remove Merchant validation rule that does not apply in this context
				$this->MerchantUw->Merchant->set('merchant_acquirer_id');
				if ($MerchantChange->editChange($this->request->data, "MerchantUw", array('deep' => true), $editType)) {
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
				}
			} else {
				$this->_failure(__('Changes could not be saved, fix any validation errors below and try again.'), null, array('class' => 'alert alert-danger'));
			}
		} else {
			$this->request->data = $this->MerchantUw->getUwDataByMerchantId($mId);
			if (!empty($logId)) {
				$this->request->data = $this->MerchantUw->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($mId, 'Change Request', 'Underwriting Change');
			}
			//set association and decrypt all encripted data['MerchantUwVolume']
			if (empty($this->request->data['MerchantUw']['MerchantUwVolume'])) {
				$this->request->data['MerchantUw']['MerchantUwVolume'] = $this->request->data['MerchantUwVolume'];
			}

			unset($this->request->data['MerchantUwVolume']);
		}

		$assocUwData = $this->MerchantUw->getAssociatedUwLists($this->request->data);
		//All [*.]MerchantXref (suffixed) data needs to be modified so that it aligns properly on the view
		$this->request->data['Merchant']['UwStatusMerchantXref'] = (!empty($this->request->data['Merchant']['UwStatusMerchantXref'])) ? $this->MerchantUw->reIndexUwMerchantXrefsData($assocUwData['uwSatuses'], $this->request->data['Merchant']['UwStatusMerchantXref']) : '';
		$this->request->data['Merchant']['UwApprovalinfoMerchantXref'] = (!empty($this->request->data['Merchant']['UwApprovalinfoMerchantXref'])) ? $this->MerchantUw->reIndexUwMerchantXrefsData($assocUwData['approvalInfos'], $this->request->data['Merchant']['UwApprovalinfoMerchantXref']) : '';
		$this->set($this->MerchantUw->setFormMenuData($this->request->data));
		$discountFeq = array(MerchantUwVolume::DISCOUNT_FREQ_D => "Daily", MerchantUwVolume::DISCOUNT_FREQ_M => "Monthly");
		$this->set(compact('assocUwData', 'userCanApproveChanges', 'isEditLog', 'discountFeq'));
	}
}
