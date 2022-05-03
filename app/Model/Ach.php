<?php

App::uses('AppModel', 'Model');

/**
 * Ach Model
 *
 * @property Merchant $Merchant
 * @property AchAddBankOrigFee $AchAddBankOrigFee
 */
class Ach extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'aches';

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
			'ChangeRequest'
		);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'ach_mid' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_provider_id' => array(
					'notBlank' => array(
					'rule' => 'notBlank',
					'allowEmpty' => false,
					'required' => true,
					'message' => 'ACH Provider is required'
				),
			),
			'ach_nonwritten_pre_auth' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
			'ach_written_pre_auth' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
			'ach_merchant_initiated_perc' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
			'ach_consumer_initiated_perc' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
			'ach_expected_annual_sales' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_average_transaction' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_estimated_max_transaction' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_application_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_expedite_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_tele_training_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				)
			),
			'ach_monthly_gateway_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_monthly_minimum_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_statement_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_batch_upload_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_reject_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_per_item_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_eft_ccd_nw' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_eft_ccd_w' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_eft_ppd_nw' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_eft_ppd_w' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ach_mi_w_dsb_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_dsb_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_dsb_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_fee_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_fee_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_fee_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_rej_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_rej_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_w_rej_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_dsb_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_dsb_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_dsb_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_fee_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_fee_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_fee_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_rej_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_rej_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_mi_nw_rej_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_dsb_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_dsb_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_dsb_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_fee_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_fee_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_fee_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_rej_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_rej_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_w_rej_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_dsb_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_dsb_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_dsb_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_fee_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_fee_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_fee_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_rej_bank_name' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_rej_routing_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
		        ),
			),
			'ach_ci_nw_rej_account_number' => array(
		        'input_has_only_valid_chars' => array(
		            'rule' => array('inputHasOnlyValidChars'),
		            'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
		            'required' => false,
		            'allowEmpty' => true,
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
			'AchProvider' => array(
						'className' => 'AchProvider',
						'foreignKey' => 'ach_provider_id',
			)
	);

/**
 * getDataById
 *
 * @param string $id an Ach id
 * @param boolean $truncate indicate whether to truncate bank account numbers
 * @return array
 */
	public function getDataById($id, $truncate = true) {
		$data = $this->find('first', array(
			'contain' => array('AchProvider'),
			'conditions' => array('Ach.id' => $id)));
		$data['Ach'] = $this->decryptFields($data['Ach']);
		if ($truncate) {
			$this->truncateAccounts($data);
		}

		return $data;
	}

/**
 * truncateAccounts
 *
 * @param array &$data Ach data
 * @return void
 */
	public function truncateAccounts(&$data) {
		//The majority of the fields below are not being used but some still store secured data that must be truncated
		$data['Ach']['ach_ci_nw_rej_account_number'] = (!empty($data['Ach']['ach_ci_nw_rej_account_number']))? "xxxxx" . substr($data['Ach']['ach_ci_nw_rej_account_number'], -4) : null;
		$data['Ach']['ach_ci_nw_rej_routing_number'] = (!empty($data['Ach']['ach_ci_nw_rej_routing_number']))? "xxxxx" . substr($data['Ach']['ach_ci_nw_rej_routing_number'], -4) : null;
		$data['Ach']['ach_ci_nw_fee_account_number'] = (!empty($data['Ach']['ach_ci_nw_fee_account_number']))? "xxxxx" . substr($data['Ach']['ach_ci_nw_fee_account_number'], -4) : null;
		$data['Ach']['ach_ci_nw_fee_routing_number'] = (!empty($data['Ach']['ach_ci_nw_fee_routing_number']))? "xxxxx" . substr($data['Ach']['ach_ci_nw_fee_routing_number'], -4) : null;
		$data['Ach']['ach_ci_nw_dsb_account_number'] = (!empty($data['Ach']['ach_ci_nw_dsb_account_number']))? "xxxxx" . substr($data['Ach']['ach_ci_nw_dsb_account_number'], -4) : null;
		$data['Ach']['ach_ci_nw_dsb_routing_number'] = (!empty($data['Ach']['ach_ci_nw_dsb_routing_number']))? "xxxxx" . substr($data['Ach']['ach_ci_nw_dsb_routing_number'], -4) : null;
		$data['Ach']['ach_ci_w_rej_account_number'] = (!empty($data['Ach']['ach_ci_w_rej_account_number']))? "xxxxx" . substr($data['Ach']['ach_ci_w_rej_account_number'], -4) : null;
		$data['Ach']['ach_ci_w_rej_routing_number'] = (!empty($data['Ach']['ach_ci_w_rej_routing_number']))? "xxxxx" . substr($data['Ach']['ach_ci_w_rej_routing_number'], -4) : null;
		$data['Ach']['ach_ci_w_fee_account_number'] = (!empty($data['Ach']['ach_ci_w_fee_account_number']))? "xxxxx" . substr($data['Ach']['ach_ci_w_fee_account_number'], -4) : null;
		$data['Ach']['ach_ci_w_fee_routing_number'] = (!empty($data['Ach']['ach_ci_w_fee_routing_number']))? "xxxxx" . substr($data['Ach']['ach_ci_w_fee_routing_number'], -4) : null;
		$data['Ach']['ach_ci_w_dsb_account_number'] = (!empty($data['Ach']['ach_ci_w_dsb_account_number']))? "xxxxx" . substr($data['Ach']['ach_ci_w_dsb_account_number'], -4) : null;
		$data['Ach']['ach_ci_w_dsb_routing_number'] = (!empty($data['Ach']['ach_ci_w_dsb_routing_number']))? "xxxxx" . substr($data['Ach']['ach_ci_w_dsb_routing_number'], -4) : null;
		$data['Ach']['ach_mi_nw_rej_account_number'] = (!empty($data['Ach']['ach_mi_nw_rej_account_number']))? "xxxxx" . substr($data['Ach']['ach_mi_nw_rej_account_number'], -4) : null;
		$data['Ach']['ach_mi_nw_rej_routing_number'] = (!empty($data['Ach']['ach_mi_nw_rej_routing_number']))? "xxxxx" . substr($data['Ach']['ach_mi_nw_rej_routing_number'], -4) : null;
		$data['Ach']['ach_mi_nw_fee_account_number'] = (!empty($data['Ach']['ach_mi_nw_fee_account_number']))? "xxxxx" . substr($data['Ach']['ach_mi_nw_fee_account_number'], -4) : null;
		$data['Ach']['ach_mi_nw_fee_routing_number'] = (!empty($data['Ach']['ach_mi_nw_fee_routing_number']))? "xxxxx" . substr($data['Ach']['ach_mi_nw_fee_routing_number'], -4) : null;
		$data['Ach']['ach_mi_nw_dsb_routing_number'] = (!empty($data['Ach']['ach_mi_nw_dsb_routing_number']))? "xxxxx" . substr($data['Ach']['ach_mi_nw_dsb_routing_number'], -4) : null;
		$data['Ach']['ach_mi_nw_dsb_account_number'] = (!empty($data['Ach']['ach_mi_nw_dsb_account_number']))? "xxxxx" . substr($data['Ach']['ach_mi_nw_dsb_account_number'], -4) : null;
		$data['Ach']['ach_mi_w_rej_account_number'] = (!empty($data['Ach']['ach_mi_w_rej_account_number']))? "xxxxx" . substr($data['Ach']['ach_mi_w_rej_account_number'], -4) : null;
		$data['Ach']['ach_mi_w_rej_routing_number'] = (!empty($data['Ach']['ach_mi_w_rej_routing_number']))? "xxxxx" . substr($data['Ach']['ach_mi_w_rej_routing_number'], -4) : null;
		$data['Ach']['ach_mi_w_fee_account_number'] = (!empty($data['Ach']['ach_mi_w_fee_account_number']))? "xxxxx" . substr($data['Ach']['ach_mi_w_fee_account_number'], -4) : null;
		$data['Ach']['ach_mi_w_fee_routing_number'] = (!empty($data['Ach']['ach_mi_w_fee_routing_number']))? "xxxxx" . substr($data['Ach']['ach_mi_w_fee_routing_number'], -4) : null;
		$data['Ach']['ach_mi_w_dsb_account_number'] = (!empty($data['Ach']['ach_mi_w_dsb_account_number']))? "xxxxx" . substr($data['Ach']['ach_mi_w_dsb_account_number'], -4) : null;
		$data['Ach']['ach_mi_w_dsb_routing_number'] = (!empty($data['Ach']['ach_mi_w_dsb_routing_number']))? "xxxxx" . substr($data['Ach']['ach_mi_w_dsb_routing_number'], -4) : null;
	}

/**
 * beforeSave
 *
 * @param array $options options array
 * @return boolean
 */
	public function beforeSave($options = array()) {
		$this->data['Ach'] = array_map('trim', $this->data['Ach']);
		$this->encryptData($this->data);
		return true;
	}

/**
 * encryptData
 *
 * @param type &$data save data reference with bank account fields to encrypt if they aren't already
 * @return boolean
 */
	public function encryptData(&$data) {
		//check if data already encrypted first
		if (!empty($data['Ach']['ach_mi_w_dsb_routing_number']) && !$this->isEncrypted($data['Ach']['ach_mi_w_dsb_routing_number'])) {
			$data['Ach']['ach_mi_w_dsb_routing_number'] = $this->encrypt($data['Ach']['ach_mi_w_dsb_routing_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['Ach']['ach_mi_w_dsb_account_number']) && !$this->isEncrypted($data['Ach']['ach_mi_w_dsb_account_number'])) {
			$data['Ach']['ach_mi_w_dsb_account_number'] = $this->encrypt($data['Ach']['ach_mi_w_dsb_account_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['Ach']['ach_mi_w_fee_routing_number']) && !$this->isEncrypted($data['Ach']['ach_mi_w_fee_routing_number'])) {
			$data['Ach']['ach_mi_w_fee_routing_number'] = $this->encrypt($data['Ach']['ach_mi_w_fee_routing_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['Ach']['ach_mi_w_fee_account_number']) && !$this->isEncrypted($data['Ach']['ach_mi_w_fee_account_number'])) {
			$data['Ach']['ach_mi_w_fee_account_number'] = $this->encrypt($data['Ach']['ach_mi_w_fee_account_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['Ach']['ach_mi_w_rej_routing_number']) && !$this->isEncrypted($data['Ach']['ach_mi_w_rej_routing_number'])) {
			$data['Ach']['ach_mi_w_rej_routing_number'] = $this->encrypt($data['Ach']['ach_mi_w_rej_routing_number'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['Ach']['ach_mi_w_rej_account_number']) && !$this->isEncrypted($data['Ach']['ach_mi_w_rej_account_number'])) {
			$data['Ach']['ach_mi_w_rej_account_number'] = $this->encrypt($data['Ach']['ach_mi_w_rej_account_number'], Configure::read('Security.OpenSSL.key'));
		}
	}

}
