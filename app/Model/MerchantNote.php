<?php

App::uses('AppModel', 'Model');
App::uses('NoteType', 'Model');
App::uses('GUIbuilderComponent', 'Controller/Component');
App::uses('RolePermissions', 'Lib');
App::uses('MerchantChange', 'Model');
App::uses('MerchantAch', 'Model');

/**
 * MerchantNote Model
 *
 * @property MerchantNote $MerchantNote
 * @property User $User
 * @property Merchant $Merchant
 * @property SystemTransaction $SystemTransaction
 * @property MerchantChange $MerchantChange
 */
class MerchantNote extends AppModel {

/**
 * behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Search.Searchable',
		'ChangeRequest',
		'SearchByUserId' => array(
			'userRelatedModel' => 'Merchant'
		),
	);

/**
 * Toggle Search by author or rep.
 * Search by Author looks up users in this Model's table but by rep looks at users in Merchant's table which tend to be different.
 *
 * @var bool
 */
	public $searchByAuthor;

/**
 * Author Type const string
 *
 * @var string
 */
	const AUTHOR_TYPE_REP = 'rep';
	const AUTHOR_TYPE_AUTHOR = 'author';

/**
 * General Status const string
 *
 * @var string
 */
	const STATUS_PENDING = 'PEND';
	const STATUS_COMPLETE = 'COMP';
	const STATUS_REJECTED = 'REJEC';

/**
 * General Status if note type is GNR
 *
 * @var string
 */
	const STATUS_GNR_MALE = 'M';
	const STATUS_GNR_FEMALE = 'F';

/**
 * Note types (mimic types from note_types table)
 *
 * @todo: refactor note_types table? we are using logic based on types and is better to use constants
 *
 * @var string
 */
	const TYPE_GENERAL = 'General Note';
	const TYPE_PROGRAMMING = 'Programming Note';
	const TYPE_INSTALLATION = 'Installation & Setup Note';
	const TYPE_CHANGE_REQUEST = 'Change Request';
	// @todo: check if those types are still in use
	const TYPE_GNR = 'GNR';
	const TYPE_GNRSIMPLE = 'GNRSimple';

/**
 * Filter args config for Searchable behavior
 *
 * @var array
 */
	public $filterArgs = array(
		'action_type' => array(
			'type' => 'query',
			'empty' => true,
			'method' => 'requestTypeConditions'
		),
		'general_status' => array(
			'type' => 'query',
			'empty' => true,
			'method' => 'requestStatusConditions'
		),
		'dba_mid' => array(
			'type' => 'query',
			'empty' => true,
			'method' => 'merchantOrConditions'
		),
		'author_type' => array(
			'type' => 'query',
			'empty' => true,
			'method' => '',
		),
		'author_name' => array(
			'type' => 'query',
			'empty' => true,
			'method' => '',
		),
		'from_date' => array(
			'type' => 'query',
			'method' => 'dateRangeStartConditions',
			'empty' => true,
		),
		'end_date' => array(
			'type' => 'query',
			'method' => 'dateRangeEndConditions',
			'empty' => true,
		),
		'user_id' => array(
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"Merchant"."user_id"',
			'searchByMerchantEntity' => true,
		),
	);

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array();

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'note_type_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			)
		),
		'user_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			)
		),
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			)
		),
		'note' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You did not enter a note.',
			)
		),
		'note_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Note title is missing.',
			)
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ApprovingUser' => array(
			'className' => 'User',
			'foreignKey' => 'approved_by_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'NoteType' => array(
			'className' => 'NoteType',
			'foreignKey' => 'note_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'LoggableLog' => array(
			'className' => 'Loggable.LoggableLog',
			'foreignKey' => 'loggable_log_id',
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SystemTransaction' => array(
			'className' => 'SystemTransaction',
			'foreignKey' => 'merchant_note_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'MerchantChange' => array(
			'className' => 'MerchantChange',
			'foreignKey' => 'merchant_note_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * constructor, populate default values for search
 *
 * @param type $id id
 * @param type $table table name
 * @param type $ds datasource
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		// ---- Searchable defaults
		$now = $this->_getNow();
		$this->filterArgs['from_date']['defaultValue'] = array(
			'year' => $now->format('Y'),
			'month' => $now->format('m'),
		);
		$this->filterArgs['end_date']['defaultValue'] = array(
			'year' => $now->format('Y'),
			'month' => $now->format('m'),
		);
	}

/**
 * BeforeSave callback
 *
 * @param array $options Save options
 *
 * @return bool
 */
	public function beforeSave($options = array()) {
		// Update the resolved date based on the note status
		if (isset($this->data[$this->alias])) {
			if (!empty(Hash::get($this->data, "{$this->alias}.note"))) {
				$this->data[$this->alias]['note'] = $this->removeAnyMarkUp($this->data[$this->alias]['note']);
			}
			if (!empty(Hash::get($this->data, "{$this->alias}.note_title"))) {
				$this->data[$this->alias]['note_title'] = $this->removeAnyMarkUp($this->data[$this->alias]['note_title']);
			}
			if (!empty(Hash::get($this->data, "{$this->alias}.general_status"))) {
				$status = Hash::get($this->data, "{$this->alias}.general_status");
			} else {
				$status = $this->field('general_status', ['id' => Hash::get($this->data, "{$this->alias}.id")]);
			}

			if ($status === self::STATUS_PENDING) {
				$this->data[$this->alias]['resolved_date'] = null;
				$this->data[$this->alias]['resolved_time'] = null;
			} elseif ($status === self::STATUS_COMPLETE || self::STATUS_REJECTED) {
				if (empty($this->data[$this->alias]['resolved_date'])) {
					$now = $this->_getNow();
					$this->data[$this->alias]['resolved_date'] = $now->format('Y-m-d');
					$this->data[$this->alias]['resolved_time'] = $now->format('H:i:s');
				}
			}
			//Set node_date if it's a new note
			if (!array_key_exists('id', $this->data[$this->alias])) {
				$this->data[$this->alias]['note_date'] = date("Y-m-d H:i:s");
			}
		}

		return true;
	}

/**
 * Return the general statuses used for merchant notes
 *
 * @param array $options Save options
 *
 * @return bool
 */
	public function getStatusList($options = array()) {
		$generalStatuses = GUIbuilderComponent::getStatusList();

		return array(
			self::STATUS_PENDING => Hash::get($generalStatuses, self::STATUS_PENDING),
			self::STATUS_COMPLETE => Hash::get($generalStatuses, self::STATUS_COMPLETE),
			self::STATUS_REJECTED => Hash::get($generalStatuses, self::STATUS_REJECTED),
		);
	}

/**
 * getUserFilterConditions gets the conditions depending on selected user_id
 *
 * @param array $data Data submitted for search args
 * @return array conditions array
 */
	public function getUserFilterConditions($data) {
		$RolePermissions = new RolePermissions();
		return $RolePermissions->getRepFilterConditions($data['user_id']);
	}

/**
 * Request status conditions for searchable behavior
 * Sets the model and field to be used to search by status since they may differs depending on the
 * selected filter argument.
 *
 * @param array $data Data submitted for search args
 * @return string conditions
 */
	public function requestStatusConditions($data = array()) {
		if (empty($data['action_type'])) {
			return null;
		} elseif (in_array(Hash::get($data, 'action_type'), array_keys($this->Merchant->MerchantAch->invTypes))) {
			return "MerchantAch.status = '{$data['general_status']}'";
		} else {
			return "MerchantNote.general_status = '{$data['general_status']}'";
		}
	}

/**
 * Request type conditions for searchable behavior
 *
 * @param array $data Data submitted for search args
 * @return string conditions
 */
	public function requestTypeConditions($data = array()) {
		if (!empty(Hash::get($data, 'action_type'))) {
			if ($this->isValidUUID(Hash::get($data, 'action_type'))) {
				return "MerchantNote.note_type_id = '{$data['action_type']}'";
			} elseif (Hash::get($data, 'action_type') === MerchantAch::INV_CRT) {
				return "MerchantAch.total_ach < 0";
			} elseif (Hash::get($data, 'action_type') === MerchantAch::INV_DBT) {
				return "MerchantAch.total_ach >= 0";
			}
		}
	}

/**
 * Merchant Or conditions for searchable behavior
 *
 * @param array $data Data submitted for search args
 * @return array conditions array
 */
	public function merchantOrConditions($data = array()) {
		$orConditions = $this->Merchant->orConditions(array("search" => Hash::get($data, 'dba_mid')));
		return array('AND' => $orConditions);
	}

/**
 * Date range conditions for searchable behavior
 *
 * @param array $data Data submitted for search args
 * @return array conditions array
 */
	public function dateRangeStartConditions($data = array(), $merchantAch = false) {
		$fromDate = Hash::get($data, 'from_date.year') . '-' . Hash::get($data, 'from_date.month') . '-' . '01';
		if ($merchantAch === true || in_array(Hash::get($data, 'action_type'), array_keys($this->Merchant->MerchantAch->invTypes))) {
			return "MerchantAch.ach_date >= '$fromDate'";
		} else {
			return "MerchantNote.note_date >= '$fromDate'";
		}
	}

/**
 * Date range conditions for searchable behavior
 *
 * @param array $data Data submitted for search args
 * @return array conditions array
 */
	public function dateRangeEndConditions($data = array(), $merchantAch = false) {
		$endDate = Hash::get($data, 'end_date.year') . '-' . Hash::get($data, 'end_date.month') . '-' . '01';
		$endDateLastDayOfMonth = new DateTime($endDate);
		$endDateLastDayOfMonth->modify('+1 month');
		$endDateLastDayOfMonth->modify('-1 day');
		$date = $endDateLastDayOfMonth->format('Y-m-d');
		if ($merchantAch === true || in_array(Hash::get($data, 'action_type'), array_keys($this->Merchant->MerchantAch->invTypes))) {
			return "MerchantAch.ach_date <= '$date'";
		} else {
			return "MerchantNote.note_date <= '$date'";
		}
	}

/**
 * getIndexData
 *
 * @param array $filterArgs Arguments for search filtering
 * @return array Data resulting from search
 */
	public function getIndexPaginatorSettings($filterArgs) {
		if (empty(Hash::get($filterArgs, 'action_type'))) {
			$dateConditions[] = [
				'OR' => [
					['AND' => [$this->dateRangeStartConditions($filterArgs), $this->dateRangeEndConditions($filterArgs)]],
					['AND' => [$this->dateRangeStartConditions($filterArgs, true), $this->dateRangeEndConditions($filterArgs, true)]]
				]
			];
		} elseif (in_array(Hash::get($filterArgs, 'action_type'), array_keys($this->Merchant->MerchantAch->invTypes))) {
			$dateConditions[] = $this->dateRangeStartConditions($filterArgs, true);
			$dateConditions[] = $this->dateRangeEndConditions($filterArgs, true);
		} else {
			$dateConditions[] = $this->dateRangeStartConditions($filterArgs);
			$dateConditions[] = $this->dateRangeEndConditions($filterArgs);
		}	


		// unset date start and date end from $this->filterArgs before parsing here
		unset($this->filterArgs['from_date'], $this->filterArgs['end_date']);

		$conditions = $this->parseCriteria($filterArgs);
		$conditions = array_merge($conditions, $dateConditions);

		$settings = array(
			'conditions' => $conditions,
		);

		$settings['fields'] = $this->getAliasedFields($filterArgs);
		$settings['joins'] = $this->setJoins($filterArgs);
		$settings['limit'] = 200;

		return $settings;
	}

/**
 * getAliasedFields
 * 
 *
 * @return array joins array
 */
	public function getAliasedFields($filterArgs = array()) {
		$fields = array(
			'Merchant.id',
			'Merchant.user_id',
			'Merchant.merchant_mid',
			'Merchant.merchant_dba',
			'User.id',
			'User.user_first_name',
			'User.user_last_name',
		);
		$requestType = Hash::get($filterArgs, 'action_type');
		$isAchQueryType = in_array($requestType, array_keys($this->Merchant->MerchantAch->invTypes));
		if (empty($filterArgs['action_type']) || $isAchQueryType) {
			$invoiceDebit = $this->Merchant->MerchantAch->invTypes[MerchantAch::INV_DBT];
			$invoiceCredit = $this->Merchant->MerchantAch->invTypes[MerchantAch::INV_CRT];
			$merchantAchFields = array(
				'MerchantAch.id',
				'MerchantAch.user_id',
				'MerchantAch.ach_date',
				"((CASE WHEN \"MerchantAch\".\"total_ach\" >= 0 THEN '$invoiceDebit' ELSE '$invoiceCredit' END)) AS \"MerchantAch__type\"",
				'MerchantAch.status',
				"(SELECT count(*) from invoice_items where merchant_ach_id = \"MerchantAch\".\"id\") AS \"MerchantAch__item_count\"",
				'MerchantAch.total_ach',
			);
			$fields = array_merge($fields, $merchantAchFields);
		}
		if (empty($filterArgs['action_type']) || !$isAchQueryType) {
			$noteFields = array(
				'MerchantNote.id',
				'MerchantNote.user_id',
				'MerchantNote.note_date',
				'MerchantNote.resolved_date',
				'MerchantNote.note_title',
				'MerchantNote.note',
				'MerchantNote.general_status',
				'MerchantNote.loggable_log_id',
				'NoteType.note_type_description'
			);
			$fields = array_merge($fields, $noteFields);
		}
		return $fields;
	}

/**
 * setJoins
 * This method adds a MerchantNote join and therefore should not be used to query MerchantNotes directly.
 *
 * @param array $filterArgs Optional Arguments used for search filtering
 * @return array joins array
 */
	public function setJoins($filterArgs = array()) {
		$merchAchesJoin = array(
			array(
				'table' => 'merchant_aches',
				'alias' => 'MerchantAch',
				'type' => 'LEFT',
				'conditions' => array(
					'Merchant.id = MerchantAch.merchant_id',
				)
			),
		);
		$merchNoteJoins = array(
			array(
				'table' => 'merchant_notes',
				'alias' => 'MerchantNote',
				'type' => 'LEFT',
				'conditions' => array(
					'MerchantNote.merchant_id = Merchant.id'
				)
			),
			array(
				'table' => 'note_types',
				'alias' => 'NoteType',
				'type' => 'LEFT',
				'conditions' => array(
					'MerchantNote.note_type_id = NoteType.id'
				)
			)
		);
		
		$requestType = Hash::get($filterArgs, 'action_type');
		$isAchQueryType = in_array($requestType, array_keys($this->Merchant->MerchantAch->invTypes));

		if ($this->searchByAuthor) {
			$usrConditions = array();
			if (empty($requestType)) {
				$usrConditions['OR'] = array(
					'MerchantNote.user_id = User.id',
					'MerchantAch.user_id = User.id'
				);
			} elseif ($isAchQueryType) {
				$usrConditions[] = 'MerchantAch.user_id = User.id';
			} else {
				$usrConditions[] = 'MerchantNote.user_id = User.id';
			}
		} else {
			$usrConditions = array(
				'Merchant.user_id = User.id'
			);
		}

		$joins = array(
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => $usrConditions
			),
		);
		if (empty($requestType)) {
			//Insert status conditions if not empty
			if (!empty($filterArgs['general_status'])) {
				$merchNoteJoins[0]['conditions'][] = "MerchantNote.general_status = '{$filterArgs['general_status']}'";

				$merchAchesJoin[0]['conditions'][] = "MerchantAch.status = '{$filterArgs['general_status']}'";
			}

			//insert date ranges to both joins
			$merchNoteJoins[0]['conditions'][] = $this->dateRangeStartConditions($filterArgs);
			$merchNoteJoins[0]['conditions'][] = $this->dateRangeEndConditions($filterArgs);
			$merchAchesJoin[0]['conditions'][] = $this->dateRangeStartConditions($filterArgs, true);
			$merchAchesJoin[0]['conditions'][] = $this->dateRangeEndConditions($filterArgs, true);
			//Insert date conditions if not empty
			$joins = array_merge($merchNoteJoins, $merchAchesJoin, $joins);
		} elseif ($isAchQueryType) {
			$joins = array_merge($merchAchesJoin, $joins);
		} else {
			$joins = array_merge($merchNoteJoins, $joins);
		}

		return $joins;
	}
/**
 * Author type join conditions for searchable behavior
 * Search by Author looks up users in this Model's table but by rep looks at users in Merchant's table which tend to be different.
 *
 * @return array joins array
 */
	public function getCurrentUserComplexId() {
		return $this->User->buildComplexId(User::PREFIX_USER, $this->_getCurrentUser('id'));
	}
/**
 * Add one PCI Edit note
 *
 * @param int $userId The current logged user
 * @param int $merchantId Merchant id
 * @param string $note The given note to store
 *
 * @return bool
 */
	public function addPCIEditNote($userId, $merchantId, $note) {
		$this->create();
		return $this->save(
			array(
				'note_title' => __('PCI DSS Compliance Change'),
				'user_id' => $userId,
				'note_date' => date('Y-m-d'),
				'merchant_id' => $merchantId,
				'note_type_id' => NoteType::GENERAL_NOTE_ID,
				'note' => $note,
			)
		);
	}

/**
 * Get all notes for a specific merchant or just a list of notes of the specified note_types_id for and merchant id.
 *
 * @param string $id required Merchant UUID
 * @param string $noteTypeId optional condition the note_type_id if null all notes will be returned
 * @return array
 */
	public function getNotesByMerchantId($id, $noteTypeId = null) {
		$contain = array('MerchantNote' => array('User'));
		if (!empty($noteTypeId)) {
			$contain['MerchantNote']['conditions'] = array('MerchantNote.note_type_id' => $noteTypeId);
		}

		return $this->Merchant->find('first', array(
					'recursive' => -1,
					'conditions' => array('Merchant.id' => $id),
					'contain' => $contain
		));
	}

/**
 * Gets a note by specified id
 *
 * @param string $id a note id
 * @param string $options find array options to overwrite defaults
 * @return array
 */
	public function getNoteById($id, $options = array()) {
		$defaultOptions = array(
			'conditions' => array(
				'MerchantNote.id' => $id
			),
			'contain' => array(
				'Merchant' => array(
					'User' => array(
						'fields' => array('user_first_name', 'user_last_name', 'user_email')
					),
				),
				'User' => array(
					'fields' => array('user_first_name', 'user_last_name')
				),
				'ApprovingUser' => array(
					'fields' => array('user_first_name', 'user_last_name')
				),
				'NoteType'
			),
		);

		return $this->find('first', Hash::merge($defaultOptions, $options));
	}

/**
 * emailNoteToRep method
 *
 * Sets up data for email and triggers readyForEmail event
 *
 * @param array $noteData contains data about the note such as note_title, note, general_status, crittical, user_email , etc.
 * @return void
 */
	public function emailNoteToRep($noteData) {
		$notifier = CakeSession::read('Auth.User.fullname');
		$emailBody = $noteData['MerchantNote']['note_title'] . "\n";
		$emailBody .= "(Do not reply to this message! This is an automated notification. Look below for the name of the user who triggered this notification and reply directly to that user)\n";
		$emailBody .= $noteData['MerchantNote']['note'] . "\n\n";

		$emailBody .= 'Status: ' . GUIbuilderComponent::getStatusLabel($noteData['MerchantNote']['general_status']);

		if ((bool)$noteData['MerchantNote']['critical']) {
			$emailBody .= " - CRITICAL\n";
		}
		if (!empty($notifier)) {
			$emailBody .= "\n";
			$emailBody .= "(This notification was triggered by $notifier, please do not reply to this message.)\n";
		}
		// create a new event
		$event = new CakeEvent('App.Model.readyForEmail', $this, array(
			'to' => $noteData['Merchant']['User']['user_email'],
			'subject' => "Axia Merchant Note for: " . $noteData['Merchant']['merchant_dba'],
			'emailBody' => $emailBody
		));

		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
	}

/**
 * Check if we should save one MerchantNote from PCI
 *
 * @param array $data The pci data
 * @return bool
 */
	public function shouldSave(array $data) {
		return isset($data[$this->alias])
			&& isset($data[$this->alias][0])
			&& isset($data[$this->alias][0]['note'])
			&& !empty(Hash::get($data, "{$this->alias}.0.note"));
	}

/**
 * Return the related data needed for edit form
 *
 * @param array $data Array with the merchantNote information
 * @return array
 */
	public function getEditFormRelatedData($data = array()) {
		$merchantId = Hash::get($data, 'MerchantNote.merchant_id');
		$merchantNoteId = Hash::get($data, 'MerchantNote.id');

		$merchant = array();
		if (!empty($merchantId)) {
			$merchant = $this->Merchant->getSummaryMerchantData($merchantId);
		}

		$isGeneralNote = (Hash::get($data, 'NoteType.id') === NoteType::GENERAL_NOTE_ID);
		$logId = Hash::get($data, 'MerchantNote.loggable_log_id');
		$loggedForeignModel = [];
		$isModal = null;
		if (Hash::get($data, 'NoteType.id') === NoteType::CHANGE_REQUEST_ID) {
			$loggedForeignModel = $this->LoggableLog->find('first', array(
				'conditions' => array('id' => $logId),
				'fields' => array('model', 'action', 'controller_action', 'foreign_key', 'content')));
			$content = !empty($loggedForeignModel)? unserialize(Hash::get($loggedForeignModel, 'LoggableLog.content')) : array();
			$isModal = (bool)Hash::get($content, "Request.is_modal");
			unset($loggedForeignModel['LoggableLog']['content']);
		}
		$needToApproveChanges = (
			!$isGeneralNote &&
			!empty($logId) &&
			(Hash::get($data, 'MerchantNote.general_status') === self::STATUS_PENDING)
		);
		$userCanApproveChanges = $this->MerchantChange->userCanApproveChanges();

		$controllerName = Inflector::pluralize(Hash::get($loggedForeignModel, 'LoggableLog.model'));
		//set controller controller_action to use to edit the change request
		//defaults to edit caction if none was saved in the log
		$actionName = Hash::get($loggedForeignModel, 'LoggableLog.controller_action');
		if ($controllerName === 'Addresses') {
			$actionName = 'business_info_edit';
		}
		$actionName = (empty($actionName))? "edit" : $actionName;
		return compact(
			'isModal',
			'controllerName',
			'actionName',
			'loggedForeignModel',
			'merchant',
			'isGeneralNote',
			'needToApproveChanges',
			'userCanApproveChanges'
		);
	}

/**
 * groupNotes method this method will to organize the merchantNotes by PEND and COMP and it'll facilitate in the view
 *
 * @param array $merchantNotes array of the MerchantNotes
 * @todo: refactor this function using combine and remove magic strings
 * @return array $merchantNotes
 */
	public function groupNotes($merchantNotes) {
		$notes['PEND'] = array();
		$notes['COMP'] = array();

		foreach ($merchantNotes as $key => $note) {
			if ($note['NoteType']['note_type_description'] != self::TYPE_CHANGE_REQUEST) {
				continue;
			}

			if ($note['general_status'] == self::STATUS_PENDING) {
				$notes['PEND'][] = $note;
			} elseif ($note['general_status'] == self::STATUS_COMPLETE) {
				$notes['COMP'][] = $note;
			}
		}

		$merchantNotes = $notes;
		return $merchantNotes;
	}

/**
 * Edit a Merchant Note
 *
 * @param array $data Array with the merchantNote information
 * @param bool $validate To calidate de data before save
 * @param array $fieldList Filter of fields to save
 *
 * @return mixed true if success, error message string in other case
 */
	public function edit($data = array(), $validate = true, $fieldList = array()) {
		$this->set(array('date_changed' => date('Y-m-d')));

		// Check the type of submit
		if (isset($data['approve-change-submit'])) {
			$logId = Hash::get($data, 'MerchantNote.loggable_log_id');
			if (!empty($logId)) {
				$Model = ClassRegistry::init($data['MerchantNote']['dataForModel']);
				if ($Model->approveChange($logId)) {
					if (isset($data['MerchantNote'])) {
						$data['MerchantNote']['general_status'] = self::STATUS_COMPLETE;
						$data['MerchantNote']['approved_by_user_id'] = $this->_getCurrentUser('id');
					}
				} else {
					return __('The merchant changes could not be applied. Please, try again.');
				}
			}
		} elseif (isset($data['reject-change-submit'])) {
			if (isset($data['MerchantNote'])) {
				$data['MerchantNote']['general_status'] = self::STATUS_REJECTED;
			}
		}
		if ($this->save($data, $validate, $fieldList)) {
			if (Hash::get($data, 'MerchantNote.note_sent')) {
				$this->emailNoteToRep($this->getNoteById($this->id));
			}
			return true;
		}

		return __('The merchant note could not be saved. Please, try again.');
	}

/**
 * Check if a user have access to the merchant Note
 *
 * User with access are:
 *  - Admins: All
 *  - Other roles: Note owner
 *
 * @param string $noteId MerchantNote id
 * @param string $userId user id
 * @return bool
 */
	public function userHasAccess($noteId, $userId) {
		if ($this->User->isAdmin($userId)) {
			return true;
		} else {
			$noteOwnerId = $this->field('user_id', array(
				"{$this->alias}.{$this->primaryKey}" => $noteId
			));
			if ($noteOwnerId === $userId) {
				return true;
			}
		}

		return false;
	}

/**
 * getStatusGnrOptions method this method will return the male and female options to add and edit view
 *
 * @return array
 */
	public function getStatusGnrOptions() {
		return array(
			self::STATUS_GNR_MALE => __('Male'),
			self::STATUS_GNR_FEMALE => __('Female')
		);
	}

/**
 * Return Merchant Note types
 *
 * @return array
 */
	public function getTypes() {
		return array(
			self::TYPE_GENERAL => __('General Note'),
			self::TYPE_PROGRAMMING => __('Programming Note'),
			self::TYPE_INSTALLATION => __('Installation & Setup Note'),
			self::TYPE_CHANGE_REQUEST => __('Change Request')
		);
	}
}
