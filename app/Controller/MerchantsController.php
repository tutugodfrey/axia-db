<?php

App::uses('AppController', 'Controller');
App::uses('Address', 'Model');
App::uses('ControlScan', 'Model');
App::uses('AppShell', 'Console/Command');
App::uses('ShellDispatcher', 'Console');

 /**
 * @OA\Tag(name="Merchants", description="Operations and data about Merchants/Client accounts")
 *
 * @OA\Schema(
 *	   schema="Merchants",
 *     description="Merchants database table schema (These fields should not to be used in any API requests).",
 *     title="Merchants",
 *     @OA\Property(
 *			description="Merchant id is a unique identifier (UUID)",
 *			property="id",
 *			type="string/uuid"
 *     ),
 *     @OA\Property(
 *			description="Merchant/Client MID is the account number",
 *			property="merchant_mid",
 *			type="integer"
 *     ),
 *     @OA\Property(
 *			description="Merchant/Client DBA -- this is the merchant/client's business name",
 *			property="merchant_dba",
 *			type="string"
 *     ),
 * )
 */
class MerchantsController extends AppController {

/**
 * Components in use
 *
 * @var array
 */
	public $components = array(
		'FlagStatusLogic',
		'GUIbuilder',
		'Search.Prg' => [
			'commonProcess' => ['paramType' => 'querystring'],
			'presetForm' => ['paramType' => 'querystring']
		],
		'CsvHandler'
	);

	public $presetVars = true; // using the model configuration

/**
 * validate_client_id
 * Ajax method to check if the given global client ID is valid.
 *
 * @param string $clientId string template id
 * @return void
 */
	public function validate_client_id($clientId) {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->is('ajax')) {
			if ($this->Session->read('Auth.User.id')) {
				if (!empty($clientId)) {
					try {
						$result = $this->Merchant->Client->getSfClientNameByClientId($clientId);
						return ($result === false)? json_encode(['valid' => false]) : json_encode($result);
					} catch (Exception $e) {
						$this->response->statusCode(500);
						return;
					}
				} else {
					//Bad Request
					$this->response->statusCode(400);
				}
			} else {
				//session expired
				$this->response->statusCode(401);
			}
		} else {
			//Bad Request
			$this->response->statusCode(400);
		}
	}

/**
 * find method
 *
 * @return void
 */
	public function find() {
		$this->Prg->commonProcess();
		$addressTypeIds = $this->Merchant->Address->AddressType->getAllAddressTypeIds();

		$conditions = $this->Merchant->parseCriteria($this->passedArgs);
		//remove active condition when is not selected to retrieve both active/inactive
		if ($conditions['Merchant.active'] == 0) {
			unset($conditions['Merchant.active']);
		}
		//Non admins results should consist only of merchants that the current user is associated with
		if (!$this->Merchant->User->isAdmin($this->Auth->user('id'))) {
			$allowedPartners = $this->Merchant->User->getUsersByAssociatedUser($this->Auth->user('id'), User::ROLE_ACCT_MGR);
			$allowedPartners = array_keys($allowedPartners);
			$allowedPartners[] = $this->Auth->user('id');
			$conditions[]['OR'] = [
				'Merchant.user_id' => $this->Auth->user('id'),
				'Merchant.sm_user_id' => $this->Auth->user('id'),
				'Merchant.sm2_user_id' => $this->Auth->user('id'),
				'Merchant.partner_id' => $allowedPartners,
			];
		}
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'User',
				'Address' => array(
					'conditions' => array('Address.address_type_id =' => $addressTypeIds['business_address'])
				)
		));

		$results = $this->paginate();

		// if there's only 1 result, take user directly to merchant overview
		if (count($results) == 1) {
			$this->redirect(array('action' => 'view', $results[0]['Merchant']['id']));
		}

		$this->set('merchants', $results);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		//Get default search params
		if (empty($this->request->query)) {
			$this->request->query = $this->Merchant->getDefaultSearchValues();
		} elseif (empty($this->request->query['user_id']) && $this->Merchant->User->isAdmin($this->Auth->user('id')) === false) {
			$this->request->query['user_id'] = $this->Merchant->User->getDefaultUserSearch();
		}
		$this->Paginator->settings = $this->Merchant->setPaginatorSettings($this->request->query);
		if (empty($this->request->query['output'])) {
			$merchants = $this->Paginator->paginate();
			$this->set('merchants', $merchants);
		} elseif ($this->request->query['output'] === 'csv') {
			$fullCount = $this->Merchant->find('count');
			$this->Paginator->settings['limit'] = $fullCount;
			$this->Paginator->settings['maxLimit'] = $fullCount;
			//Increase memory, this report can be very big
			ini_set('memory_limit', '10G');
			$merchants = $this->Paginator->paginate();
			$csvStr = $this->Merchant->makeCsvString($merchants);
			$this->CsvHandler->saveCsvStrToFile("merchants_list", $csvStr);
			return $this->response;
		}
		$partners = [];
		if ($this->Merchant->User->roleIs($this->Auth->user('id'), User::ROLE_PARTNER) === false) {
			$partners = $this->Merchant->User->getByRole(User::ROLE_PARTNER);
		}
		$this->set('partners', $partners);
		$this->set('organizations', $this->Merchant->Organization->find('list'));
		$this->set('regions', $this->Merchant->Region->find('list'));
		$this->set('subregions', $this->Merchant->Subregion->find('list'));
		$this->set('entities', $this->Merchant->Entity->find('list'));
		$this->set('users', $this->Merchant->User->getEntityManagerUsersList(true));
		$this->set('statesOptns', $this->GUIbuilder->getUSAStates());
	}

/**
 * edit method
 *
 * If the $merchantNoteId is passed, check if there is an associated log to merge the original data
 * with the logged data and be able to edit the logged data before the approve
 *
 * @param string $id a merchant id
 * @param string $merchantNoteId a merchant id
 * @throws NotFoundException
 *
 * @return void
 */
	public function edit($id = null, $merchantNoteId = null) {
		$this->Merchant->id = $id;
		if (!$this->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		ClassRegistry::init('MerchantChange');
		$merchantNote = null;
		$logId = null;
		// Check if the edit request is for the logged data
		$merchantNote = $this->Merchant->MerchantNote->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');

		if ($this->_hasPendChanges($id, $logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'action' => 'view',
				$id
			));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			//Encrypt fields for all change requests
			$this->Merchant->encryptData($this->request->data);
			if ($this->Merchant->edit($this->request->data, array('deep' => true), $editType)) {
				if (empty($merchantNote)) {
					//redirect depends on whether the edit happened on a ajax modal window
					$requestWasAjax = (bool)Hash::get($this->request->data, 'Request.is_modal');
					$redirectUrl = array(
						'controller' => ($requestWasAjax)? 'Addresses': 'Merchants',
						'action' => ($requestWasAjax)? 'business_info': 'view',
						$this->request->data('Merchant.id')
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
			$this->request->data = $this->Merchant->findMerchantDataById($id);
			if (!empty($logId)) {
				$this->request->data = $this->Merchant->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($id, 'Change Request', 'Account Information Change');
			}
		}

		$this->set($this->Merchant->getEditFormRelatedData($this->request->data));
	}

/**
 * view method
 *
 * @param string $id Merchant ID
 * @throws NotFoundException If merchant ID not found
 * @return void
 */
	public function view($id = null) {
		$this->Merchant->id = $id;
		if (!$this->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		$merchant = $this->Merchant->findMerchantDataById($id);
		$resultArray = array();
		$allowedNoteTypes = array();
		$allowedNoteTypes[] = $this->Merchant->MerchantNote->NoteType->findByNoteTypeDescription('General Note');
		$allowedNoteTypes[] = $this->Merchant->MerchantNote->NoteType->findByNoteTypeDescription('Change Request');
		$x = 0;
		foreach ($merchant['MerchantNote'] as &$merchantNote) {
			/* Determine which note flag icon to use for each note. */
			if (!empty($merchantNote['general_status'])) {
				$merchantNote['flag_image'] = $this->FlagStatusLogic->getThisStatusFlag($merchantNote['general_status']);
			}
			/* Insert most recently created 'General Notes' and/or those marked as critical in a new array() */
			if (in_array($merchantNote['note_type_id'], $allowedNoteTypes) &&
					($merchantNote['critical'] || strtotime($merchantNote['note_date']) >= strtotime(date_format(date_modify(new DateTime(date("Y-m-d")), '-7 day'), 'Y-m-d')))) {
				$resultArray[$x] = $merchantNote;
				$x++;
			}
		}
		unset($merchantNote);

		$timelineItemIds = $this->Merchant->TimelineEntry->TimelineItem->find('list', array(
			'fields' => array(
				'timeline_item_old',
				'id'
			)
		));

		$recentAndCriticalNotes = (!empty($resultArray)) ? Set::sort($resultArray, '{n}.note_date ', 'desc') : null;

		$changeRequestId = $this->Merchant->MerchantNote->NoteType->findByNoteTypeDescription('Change Request');

		$merchant['MerchantNote'] = $this->Merchant->MerchantNote->groupNotes($merchant['MerchantNote']);
		$expectedInsDate = Hash::extract($merchant, 'TimelineEntry.{n}[timeline_item_id=' . TimelineItem::EXPECTED_TO_GO_LIVE . ']');
		$expectedInsDate = array_pop($expectedInsDate);
		$agreementEndDate = Hash::extract($merchant, 'TimelineEntry.{n}[timeline_item_id=' . TimelineItem::AGREEMENT_ENDS . ']');
		$agreementEndDate = array_pop($agreementEndDate);
		$pciQualified = $this->Merchant->isPciQualified($id);
		$nonWomplyUsers = [];
		$womplyStatus = '';
		if (!empty($merchant['Merchant']['womply_status_id'])) {
			$womplyStatus = $this->Merchant->WomplyStatus->field('status', ['id' => $merchant['Merchant']['womply_status_id']]);
		} elseif ($merchant['Merchant']['womply_merchant_enabled'] === false) {
			$nonWomplyUsers = $this->Merchant->extractNonWomplyUsers($merchant['Merchant']);
		}
		$montsSinceInstall = $this->Merchant->getInstMoCount($id);
		$betRateStructures = $this->Merchant->MerchantPricing->getRateStructuresList($id);
		$lastActivityDate = $this->Merchant->LastDepositReport->field('last_deposit_date', ['merchant_id' => $id]);
		$this->set(compact(
			'lastActivityDate',
			'betRateStructures',
			'montsSinceInstall',
			'merchant',
			'recentAndCriticalNotes',
			'timelineItemIds',
			'changeRequestId',
			'pciQualified',
			'womplyStatus',
			'nonWomplyUsers',
			'expectedInsDate',
			'agreementEndDate'
		));
	}

/**
 * Show the notes related to a merchant. The notes can be filted
 *
 * The filter parameters can be passed by request->query to allow use links with applied filters
 *
 * @param string $id Merchant id
 * @throws NotFoundException
 *
 * @return void
 */
	public function notes($id = null) {
		$this->Merchant->id = $id;
		if (!$this->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}

		// Allow filters to be applied by url links
		if (!empty($this->request->query)) {
			$this->request->data['MerchantNote'] = $this->request->query;
		}

		if (!empty($this->request->data)) {
			$beginM = $this->request->data('MerchantNote.begin_m');
			$beginY = $this->request->data('MerchantNote.begin_y');
			$endM = $this->request->data('MerchantNote.end_m');
			$endY = $this->request->data('MerchantNote.end_y');

			if ($beginY > $endY) {
				$this->_failure(__("ERROR: Starting year must be before end year!"));
			} elseif (($beginY === $endY) && ($beginM > $endM)) {
				$this->_failure(__("ERROR: Starting month must be before end month!"));
			}

			$noteTpeId = $this->request->data('MerchantNote.note_type_id');
			if ($this->Merchant->isValidUUID($noteTpeId)) {
				$conditions['MerchantNote.note_type_id'] = $noteTpeId;
			}
			$generalStatus = $this->request->data('MerchantNote.general_status');
			if (!empty($generalStatus)) {
				$conditions['MerchantNote.general_status'] = $generalStatus;
			}
			if (!empty($beginM) && !empty($beginY)) {
				$conditions['MerchantNote.note_date >='] = $beginY . '-' . $beginM . '-01';
			}
			if (!empty($endM) && !empty($endY)) {
				$conditions['MerchantNote.note_date <='] = $endY . '-' . $endM . '-' . cal_days_in_month(CAL_GREGORIAN, $endM, $endY);
			}
		}

		$conditions['MerchantNote.merchant_id'] = $id;
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'User' => array(
					'fields' => array(
						'User.user_first_name',
						'User.user_last_name',
						'User.id'
					)
				)
			),
			'fields' => array(
				'MerchantNote.id',
				'MerchantNote.note_date',
				'MerchantNote.date_changed',
				'MerchantNote.note_title',
				'MerchantNote.note',
				'MerchantNote.note_type_id',
				'MerchantNote.general_status',
				'MerchantNote.critical',
				'MerchantNote.note_sent',
				'MerchantNote.loggable_log_id',
			),
			'limit' => 100,
			'order' => 'MerchantNote.note_date DESC'
		);
		$notes = $this->paginate('MerchantNote');

		$optnsYears = $this->GUIbuilder->getDatePartOptns('y');
		$optnsMonths = $this->GUIbuilder->getDatePartOptns('m');
		$optnsNoteTypes = $this->GUIbuilder->getNoteTypesOptns();
		$statusList = $this->Merchant->MerchantNote->getStatusList();

		$this->set(compact('notes', 'optnsYears', 'optnsMonths', 'optnsNoteTypes', 'statusList'));
		$this->set('merchant', $this->Merchant->getSummaryMerchantData($id));
	}

/**
 * equipment method
 *
 * @param string $id merchant id
 * @throws \NotFoundException
 * @return void
 */
	public function equipment($id = null) {
		$this->Merchant->id = $id;
		if (!$this->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		$gateways = $this->Merchant->Gateway1->Gateway->getSortedGatewayList();
		$equipment = $this->Merchant->EquipmentProgramming->getAllEquipmentProgrammingByMerchantId($id);
		$prgDescriptions = $this->Merchant->EquipmentProgramming->EquipmentProgrammingTypeXref->getProgrammingTypes();
		$prgNotes = $this->Merchant->MerchantNote->getNotesByMerchantId($id, $this->Merchant->MerchantNote->NoteType->findByNoteTypeDescription('Programming Note'));
		$this->set(compact('equipment', 'prgNotes', 'prgDescriptions', 'gateways'));
		$this->set('FlagStatusLogic', $this->FlagStatusLogic);
		$this->set('merchant', $this->Merchant->getSummaryMerchantData($id));
	}
/**
 * Merchant PCI DSS Compilance
 *
 * @param int $id Merchant ID
 * @throws NotFoundException If merchant ID not found
 * @return void
 */
	public function pci($id = null) {
		if (is_null($id) || !$this->Merchant->exists($id)) {
			throw new NotFoundException();
		}

		$merchant = $this->Merchant->getSummaryMerchantData($id);

		$pci = $this->Merchant->getPCI($id);

		$this->set(compact('merchant', 'pci'));
	}

/**
 * Merchant PCI DSS Compilance Edit
 *
 * @param string $id Merchant ID
 * @param string $merchantNoteId MerchantNote ID
 * @return void
 * @throws NotFoundException If merchant ID not found
 */
	public function pci_edit($id = null, $merchantNoteId = null) {
		if (!$this->Merchant->exists($id)) {
			throw new NotFoundException(__('The given merchant id was not found'));
		}
		$merchantNote = null;
		$logId = null;
		$MerchantChange = ClassRegistry::init('MerchantChange');
		// Check if the edit request is for the logged data
		$merchantNote = ClassRegistry::init('MerchantNote')->getNoteById($merchantNoteId, array('contain' => false));
		$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
		$isEditLog = !empty($logId);
		$userCanApproveChanges = $MerchantChange->userCanApproveChanges();

		if ($this->_hasPendChanges($id, $logId)) {
			$this->_failure(__(MerchantChange::PEND_MSG), array(
				'action' => 'pci',
				$id
			));
		}
		$pci = $this->Merchant->getPCI($id);
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check the submit button used
			$editType = $this->GUIbuilder->checkNoteBtnClicked($this->request->data);
			//we are only savig Merchant-associated data no need to validate Merchant.bet_network_id
			$this->Merchant->validator()->remove('bet_network_id');
			//remove irrelevant validation rule.
			$this->Merchant->validator()->remove('merchant_acquirer_id');
			$this->Merchant->SaqMerchant->SaqPrequalification->validator()->remove('result');

			if ($MerchantChange->editChange($this->request->data, "Merchant", array('deep' => true), $editType, 'pci_edit')) {
				if (empty($merchantNote)) {
					$redirectUrl = array(
						'action' => 'pci',
						$id
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
			$this->request->data = $pci;
			if (!empty($logId)) {
				$this->request->data = $this->Merchant->mergeLoggedData($logId, $this->request->data);
				$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
			} else {
				$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($id, 'Change Request', 'Mechant PCI Change');
			}
		}
		//set data array structure ready for a saveAssociated procedure
		$assoc['Merchant'] = $this->request->data['Merchant'];
		$assoc['MerchantNote'] = $this->request->data['MerchantNote'];
		unset($this->request->data['Merchant']);
		unset($this->request->data['MerchantNote']);
		$assoc['Merchant'] = array_merge($assoc['Merchant'], $this->request->data);
		$this->request->data = $assoc;
		$merchant = $this->Merchant->getSummaryMerchantData($id);
		$compilanceLevels = $this->Merchant->MerchantPci->getListOfCompilanceLevel();
		$validationTypes = $this->Merchant->SaqMerchant->SaqPrequalification->getListOfValidationTypes();

		$this->set(compact('merchant', 'compilanceLevels', 'validationTypes', 'pci', 'userCanApproveChanges', 'isEditLog'));
	}

/**
 * upload method
 *
 * @return void
 */
	public function upload() {
		if ($this->request->is('post')) {
			if (substr($this->request->data('Choose File.name'), -4, 4) == '.csv') {
				$merchCsvData = $this->CsvHandler->csv_to_array($this->request->data('Choose File.tmp_name'));
				if ($merchCsvData === false) {
					$this->Session->setFlash(__('Fatal Error: CSV file is empty, upload failed!'));
					return false;
				} else {
					$importResult = $this->Merchant->importMerchantData($merchCsvData);
				}
				if (!empty($importResult['Errors'])) {
					$this->Session->setFlash($importResult['Errors'], 'Flash/listErrors', array('class' => 'alert alert-danger'));
					return false;
				} else {
					$this->set('uploadedMerchants', $importResult);
					return;
				}
			} else {
				$this->Session->setFlash(__('Error: File type not supported!'));
			}
		}
	}

/**
 * api_add method
 * Method handles API calls to create merchants.
 * See top of Merchant model file for full swagger annotattion/documentation about this method.
 *
 * @return void
 */
	public function api_add() {
		$this->autoRender = false;
		$response = array('status' => 'failed', 'messages' => 'HTTP method not allowed');
		if ($this->request->is('post')) {
			//get data from request object
			$data = $this->request->input('json_decode', true);
			if (empty($data)){
				$data = $this->request->data;
			}
			//response if JSON data or form data was not passed
			$response = array('status'=>'failed', 'messages'=>'No data was sent');
			if (!empty($data)) {
				//importMerchantData() expects a 2D array
				$importResult = $this->Merchant->importMerchantData([$data]);
				if (!empty($importResult['Errors'])) {
					$response['messages'] = $importResult['Errors'];
				} else {
					$response['status'] = 'success';
					$response['messages'] = 'Merchant account created in the database! If needed, you may re-submit this data in order to re-create or overwrite this new account.';
				}
			}
		} else {
			$this->response->statusCode(405); //405 Method Not Allowed
		}
		$this->response->type('application/json');
		$this->response->body(json_encode($response));
	}

/**
 *
 * Handles API GET request to search for a client/Merchant.
 * Accepts request query data containig up to 4 search parameters: merchant_mid, merchant_dba, application_id and external_record_id.
 * 
 * Each additional search param provided wil be treated as an AND condition for search i.e.: WHERE merchant_mid=[...] AND application_id =[...] AND ...
 * 
 * If no search parameters are provided a list containing supported search parameters will be returned and can be used as reference to build the search query params
 *
 * @OA\Get(
 *   path="/api/Merchants/get_merchant?merchant_mid=1234567890123456[&merchant_dba=Merchant merchant_dba& ...]",
 *	 tags={"Merchants"},
 *   summary="find a merchant",
 *	 @OA\Parameter(
 *		   name="merchant_mid",
 *		   description="A full merchant ID
 *	 			Example 1: /api/Merchants/get_merchant?merchant_mid=1239567890123456
 *	 			Example 2: /api/Merchants/get_merchant?merchant_mid=7341111234567890",
 *         in="query",
 *         @OA\Schema(type="string")
 *   ),
 *	 @OA\Parameter(
 *		   name="merchant_dba",
 *		   description="A merchant Business name
 *	 			Example 1: /api/Merchants/get_merchant?merchant_dba=The Good Merchant
 *	 			Example 2: /api/Merchants/get_merchant?merchant_dba=Health Care Clinic",
 *         in="query",
 *         @OA\Schema(type="string")
 *   ),
 *	 @OA\Parameter(
 *		   name="application_id",
 *		   description="An Online App Application id. Only merchant accounts created by importing data from AxiaMed's Online Application system will have this id
 *	 			Example 1: /api/Merchants/get_merchant?application_id=23456
 *	 			Example 2: /api/Merchants/get_merchant?application_id=123",
 *         in="query",
 *         @OA\Schema(type="integer")
 *   ),
 *	 @OA\Parameter(
 *		   name="external_record_id",
 *		   description="A record id from an external system. Only merchant accounts containing data that orignated from an external non-AxiaMed system (such as salesforce) and imported via API will have this.
 *	 			Example 1: /api/Merchants/get_merchant?external_record_id=12345
 *	 			Example 2: /api/Merchants/get_merchant?external_record_id=2345678910",
 *         in="query",
 *         @OA\Schema(type="string")
 *   ),
 *	 @OA\Response(
 *     response=200,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *		   example={"merchant_mid": "1239123654789632","application_id": "9999999","external_record_id": "ABCDEFGHI1234567890","corp_name": "Family Health Service","merchant_dba": "Family Health","corp_address": "1234 Narrow Street","corp_city": "Red Bluff","corp_state": "CA","corp_zip": "96080","corp_phone": "(530)555-5789","rep_full_name": "Jess Noss","contact": "Jane Doe","business_address": "999 Wide Street","business_city": "Red Bluff","business_state": "CA","business_zip": "96080","business_phone": "(530)555-9875","contact_email": "janedoe@nomail.com","setup_referrer": "Mr. Referrer Smith","setup_reseller": "Mr. Reseller Jackson","setup_partner": "Health Partner","org_name": "Family Health Corporation","region_name": "Red Bluff Fam Health","subregion_name": "","expected_install_date": "2020-01-25"},
 *         @OA\Schema(
 *	   	   	 ref="#/components/schemas/Merchants",
 *         )
 *     ),
 *     description="
 * 			status=success - JSON array of client account data (empty when nothng found).
 *			status=failed - When no parameters provided",
 *   ),
 *   @OA\Response(
 *     response=405,
 *     description="HTTP method not allowed when request method is not GET"
 *   ),
 * )
 *
 * @return void
 */
	public function api_get_merchant() {
		$this->autoRender = false;
		$response = array('status' => 'failed', 'messages' => 'HTTP method not allowed');
		if ($this->request->is('get')) {
			//get data from request query
			$data = $this->request->query;

			if (empty($data)) {
				$response['data'] = [
					'description' => 'Provide one or more of the following parameters in the query URI to search for a client',
					'merchant_mid' => 'A merchant id string i.e.: 1239110000000001',
					'merchant_dba' => 'A merchant Business Name',
					'application_id' => 'If merchant originally signed up from AxiaMeds Online App system then this is the integer id of the Application that was filled up',
					'external_record_id' => 'Only merchant accounts containing data that orignated from an external non-AxiaMed system (such as salesforce) may have this'
				];

				$response['messages'] = 'Missing search parameter(s).';
			} else {
				$conditions = [];
				!empty(Hash::get($data, 'merchant_mid'))? $conditions['Merchant.merchant_mid'] = Hash::get($data, 'merchant_mid') : null;
				!empty(Hash::get($data, 'merchant_dba'))? $conditions['Merchant.merchant_dba'] = Hash::get($data, 'merchant_dba') : null;
				!empty(Hash::get($data, 'application_id'))? $conditions['Merchant.onlineapp_application_id'] = Hash::get($data, 'application_id') : null;
				!empty(Hash::get($data, 'external_record_id'))? $conditions['ExternalRecordField.value'] = Hash::get($data, 'external_record_id') : null;

				$result = [];

				if (!empty($conditions)) {
					$result = $this->Merchant->getApiMerchantData($conditions);
				}
				if (empty($result)) {
					$response['messages'] = 'Nothing found with given search parameters.';
				} else {
					$response['status'] = 'success';
					$response['messages'] = 'Merchant found!';
				}
				$response['data'] = $result;
			}
		} else {
			$this->response->statusCode(405); //405 Method Not Allowed
		}
		$this->response->type('application/json');
		$this->response->body(json_encode($response));
	}

/**
 * delete method
 *
 * @param string $id merchant id
 * @param bool $delete indicates whether to soft-delete or undelete (reactivate)
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id, $delete) {
		if (!$this->request->is('post')) {
			$this->set('name', 'ERROR 405: Method Not Allowed. The merchant status button on the merchant overview must be used to activate/deactivate merchants.');
			$this->set('url', $this->here);
			$this->render('/Errors/errorCust');
			return;
		}
		$this->Merchant->id = $id;
		if (!$this->Merchant->exists()) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		if ($delete) {
			$updateData = array('active' => 0, 'inactive_date' => date('Y-m-d'));
			//validation not needed to deactivate merchant
			if ($this->Merchant->save($updateData, array('validate' => false))) {
				$this->_success(__('This merchant has been deactivated!'), array('action' => 'view', $id));
			}
		} else {
			$isArchived = $this->Merchant->CancellationsHistory->archivePreviousCancellation($id);
			if ($isArchived !== false) {
				$updateData = array('active' => 1, 'active_date' => date('Y-m-d'), 'inactive_date' => null);
				//validation not needed to activate merchant
				if ($this->Merchant->save($updateData, array('validate' => false))) {
					$this->_success(__('This merchant is now active.'), array('action' => 'view', $id));
					$this->redirect($this->referer());
				}
			}
		}
		$this->_failure('Unexpected error unable to save merchant active status.', array('action' => 'view', $id));
	}

/**
 * merchantQuickOpen method
 *
 * @param string $merchantMID a merchant mid
 * @return void
 */
	public function merchantQuickOpen($merchantMID = null) {
		/* This function handles only ajax rerquests */
		$this->autoRender = false;

		if ($this->request->is('ajax')) {

			/* Check if session is still active by checking currently logged in user id */
			if ($this->Auth->user('id')) {
				$merchantUUID = $this->Merchant->findMerchantDBAorMID($merchantMID, true, 1, 1);

				if (!empty($merchantUUID)) {
					echo $merchantUUID;
				} else {
					/* if no merchants are found */
					$this->response->statusCode(404);
				}
			} else {
				/* Upon session expiration, we cannot use $this->redirect(...) dutring an ajax requests in process.
				 * Therefore return a Forbidden status 403 responce and handle this responce on the client side with ajax. */
				$this->response->statusCode(403);
			}
		}
	}

/**
 * AutoCompleteSuggestions method
 *
 * @param bool $activeStatus indicates whether active/incative merchants are to be searched
 * @param string $searchData data string to search
 * @return void
 */
	public function AutoCompleteSuggestions($activeStatus, $searchData = null) {
		/* This function handles only ajax rerquests */
		$this->autoRender = false;

		if ($this->request->is('ajax')) {

			/* Check if session is still active by checking currently logged in user id */
			if ($this->Auth->user('id')) {
				//a numeric value implies $searchData is an MID
				$isMID = is_numeric($searchData);
				$merchantUUID = $this->Merchant->findMerchantDBAorMID($searchData, $isMID, $activeStatus);

				if (!empty($merchantUUID)) {
					echo json_encode($merchantUUID);
				} else {
					/* if no merchants are found */
					$this->response->statusCode(404);
				}
			} else {
				/* Upon session expiration, we cannot use $this->redirect(...) dutring an ajax requests in process.
				 * Therefore return a Forbidden status 403 responce and handle this responce on the client side with ajax. */
				$this->response->statusCode(403);
			}
		}
	}

/**
 * ajaxDisplayDecryptedVal method
 *
 * Handle only ajax request to display encrypted data.
 *
 * @param string $merchantId a merchant id
 * @param string $salt security salt string
 * @param string $fieldName name of the field in merchants model where the date to be decryptied lives
 * @return void
 */
	public function ajaxDisplayDecryptedVal($merchantId, $salt, $fieldName) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Additional security check */
			if ($this->Merchant->checkDecrypPassword($salt)) {
				$viewTitle = Inflector::humanize($fieldName); //remove the te_ prefix from the field name
				$decriptedValue = $this->Merchant->field($fieldName, array('id' => $merchantId));
				$decriptedValue = $this->Merchant->decrypt($decriptedValue, Configure::read('Security.OpenSSL.key'));
				$this->set(compact('decriptedValue', 'viewTitle'));
				$this->render('/Elements/AjaxElements/modalDisplayDecrypedData', 'ajax');
			} else {
				/* Return status 401: Unathorized */
				$this->response->statusCode(401);
			}
		}
	}

/**
 * mid_generator method
 * Handle request to generate.
 * This method only handles the requests for data and any data posted to be saved is handled by edit/add actions.
 *
 * @return void
 */
	public function mid_generator() {
		if ($this->request->is('post')) {
			$lastKnown = $this->request->data('Merchant.last_known_mid');
			$amount = $this->request->data('Merchant.amount');
			if (strlen($lastKnown) >= 16 && strstr($lastKnown, '123922005') !== false) {
				$midList = $this->Merchant->getNextMod10Num(substr($lastKnown, -7, 7), $amount, substr($lastKnown, 0, 9), true);
				$this->set(compact('midList'));
				$this->_success('New list of MIDs has been created successfully!', null, ["class" => "alert alert-success strong"]);
			} else {
				$this->_failure('The last known MID number must be at least 16 digist long and start with 123922005, please check your entry and try again.', null, ["class" => "alert alert-danger strong"]);
			}
		}
	}
/**
 * ajaxAddAndEdit method
 *
 * Handle only ajax request to edit or add data.
 * This method only handles the requests for data and any data posted to be saved is handled by edit/add actions.
 *
 * @param string $id a merchant id
 * @param string $salt  security salt string
 * @param string $merchantNoteId merchant note id
 * @throws NotFoundException
 * @return void
 */
	public function ajaxAddAndEdit($id, $salt, $merchantNoteId = null) {
		if (empty($id)) {
			throw new NotFoundException(__('Invalid Merchant'));
		}
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$merchantNote = null;
			$logId = null;
			// Check if the edit request is for the logged data
			$merchantNote = $this->Merchant->MerchantNote->getNoteById($merchantNoteId, array('contain' => false));
			$logId = Hash::get($merchantNote, 'MerchantNote.loggable_log_id');
			//Check for pending change request
			if ($this->_hasPendChanges($id, $logId)) {
				$message = MerchantChange::PEND_MSG;
				$close = false; //prevent bootstraps window close functionality since this is in a modal window
				$class = 'alert alert-danger';
				$this->set(compact('message', 'class', 'close'));
				$this->render('/Elements/Flash/alert', 'ajax');
			} else {
				/* Additional security check */
				if ($this->Merchant->checkDecrypPassword($salt)) {
					$urlAction = array('action' => 'edit', $id, $merchantNoteId);
					$options = array('conditions' => array('Merchant.id' => $id));
					$isEditLog = !empty($logId);
					$userCanApproveChanges = $this->Merchant->MerchantChange->userCanApproveChanges();

					$this->set(compact('urlAction', 'isEditLog', 'userCanApproveChanges'));
					$this->request->data = $this->Merchant->find('first', $options);
					if (!empty($logId)) {
						$this->request->data = $this->Merchant->mergeLoggedData($logId, $this->request->data);
						$this->request->data['MerchantNote'] = array(Hash::get($merchantNote, 'MerchantNote'));
					} else {
						$this->request->data['MerchantNote'] = $this->GUIbuilder->setRequesDataNoteData($id, 'Change Request', 'Tax ID / D&B Change');
					}
					//decript all encrypted fields
					$this->request->data['Merchant'] = $this->Merchant->decryptFields($this->request->data('Merchant'));
					$this->render('/Elements/AjaxElements/modalMerchantTINandDBform', 'ajax');
				} else {
					/* Return status 401: Unathorized */
					$this->response->statusCode(401);
				}
			}
		}
	}
}
