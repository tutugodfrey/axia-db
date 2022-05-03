<?php

App::uses('AppModel', 'Model');

/**
 * MerchantChange Model
 *
 * @property Change $Change
 * @property Merchant $Merchant
 * @property User $User
 * @property MerchantNote $MerchantNote
 * @property Programming $Programming
 */
class MerchantChange extends AppModel {

	public $name = 'MerchantChange';
/**
 * Type of merchant edit process
 *
 * @var string
 */
	const EDIT_APPROVED = 'edit-approved';
	const EDIT_PENDING = 'edit-pending';
	const EDIT_LOG = 'edit-log';
	const PEND_MSG = "There are pending changes on this merchant which must be processed before making new changes.";

	public $actsAs = array(
		'ChangeRequest'
	);

/**
 * Implemented events
 *
 */
	const WOMPLY_MAR_EVENT = "Model.MerchantChange.editChange";

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'merchant_change_id' => array(
						'numeric' => array(
								'rule' => array('numeric'),
						),
			),
			'change_type' => array(
						'numeric' => array(
								'rule' => array('numeric'),
						),
			),
			'merchant_id' => array(
						'notBlank' => array(
								'rule' => array('notBlank'),
						),
			),
			'user_id' => array(
						'numeric' => array(
								'rule' => array('numeric'),
						),
			),
			'status' => array(
						'notBlank' => array(
								'rule' => array('notBlank'),
						),
			),
			'date_entered' => array(
						'date' => array(
								'rule' => array('date'),
						),
			),
			'time_entered' => array(
						'time' => array(
								'rule' => array('time'),
						),
			),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
			'Merchant' => array(
						'className' => 'Merchant',
						'foreignKey' => 'merchant_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'User' => array(
						'className' => 'User',
						'foreignKey' => 'user_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'MerchantNote' => array(
						'className' => 'MerchantNote',
						'foreignKey' => 'merchant_note_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			)
	);

/**
 * Save the merchant changes in a log. If the changes are appoved, the changes
 * are also applied to the model record
 *
 * @param array $data merchant data to save
 * @param string $ModelName name of model to which the changes are to being made/logged
 * @param array $options saveAll options
 * @param string $editType Edit type
 * @param string $viewAction name of the Controller's action where the change request originated
 * @throws InvalidArgumentException
 * @return bool
 */
	public function editChange($data, $ModelName, $options = array(), $editType = self::EDIT_PENDING, $viewAction = null) {
		//Instantiate caller Model so that the correct ModelName and corresponding changes are loged
		$Model = ClassRegistry::init($ModelName);

		// Setup ChangeRequestBehavior to save the log id at MerchantNote model
		$defaultOptions = array(
			'associateLog' => array(
				'model' => 'MerchantNote',
				'field' => 'loggable_log_id'
			),
		);
		$options = Hash::merge($defaultOptions, $options);

		switch ($editType) {
			case self::EDIT_APPROVED:
				if (isset($data['MerchantNote'][0])) {
					$data['MerchantNote'][0]['general_status'] = MerchantNote::STATUS_COMPLETE;
					$data['MerchantNote'][0]['approved_by_user_id'] = $this->_getCurrentUser('id');
				}
				$isSaved = $Model->newChangeRequest($data, $options, $viewAction);
				if ($isSaved) {
					//Whenever a change is approved trigger Womply Merchant Action Report event
					$this->triggerWomplyMar();
					return $Model->approveChange($Model->LoggableLog->id);
				}
				return false;

			case self::EDIT_PENDING:
				if (isset($data['MerchantNote'][0])) {
					$data['MerchantNote'][0]['general_status'] = MerchantNote::STATUS_PENDING;
				}
				return $Model->newChangeRequest($data, $options, $viewAction);

			case self::EDIT_LOG:
				$logId = Hash::get($data, 'MerchantNote.0.loggable_log_id');
				return $Model->editChangeRequest($logId, $data);

			default:
				throw new InvalidArgumentException(__('Invalid edit type'));
		}
	}

/**
 * Check if the given merchant has pending change requests
 *
 * @param string|int $id The merchant id
 * @return bool
 */
	public function isPending($id) {
		return (bool)$this->MerchantNote->find(
			'count', array(
				'conditions' => array(
					'MerchantNote.merchant_id' => $id,
					'MerchantNote.general_status' => MerchantNote::STATUS_PENDING,
					'MerchantNote.note_type_id' => NoteType::CHANGE_REQUEST_ID,
				)
			)
		);
	}

/**
 * Check if a user can approve the changes requested to the merchant
 *
 * @return bool
 */
	public function userCanApproveChanges() {
		$userId = $this->_getCurrentUser('id');
		return $this->User->isAdmin($userId);
	}

/**
 * foreignExists
 *
 * @param string $id a LoggableLog.foreignKey id
 * @param string $modelName LoggableLog.model --the model that owns the LoggableLog.foreignKey id
 * @return bool
 */
	public function foreignExists($id, $modelName) {
		return (bool)ClassRegistry::init('LoggableLog')->find('count', array(
				'conditions' => array(
					'foreign_key' => $id,
					'model' => $modelName,
					)
				)
			);
	}

/**
 * triggerWomplyMar method
 *
 * Triggers a womply event to send merchant action report MAR
 *
 * @return void
 */
	public function triggerWomplyMar() {
			$event = new CakeEvent(self::WOMPLY_MAR_EVENT, $this);
			$this->getEventManager()->dispatch($event);
	}
}
