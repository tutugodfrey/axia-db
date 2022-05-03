<?php

App::uses('AppModel', 'Model');

/**
 * MerchantAchReason Model
 *
 */
class MerchantAchReason extends AppModel {

/**
 * constant preset ids
 */
	const ACH_SETUP = '5cba5b96-16f8-4f0b-bc8e-78f80a0101f3'; // ACH Setup
	const ADD_EQ_FEE = '59b8777a-7670-4521-a3ee-1c7f34627ad4'; // Additional Equipment Fee
	const ANNUAL_FEE = '59b8777a-a234-4ffe-bc4d-1c7f34627ad4'; // Annual Fees
	const APPFOLIO_FEE = '59b8777a-122c-45c1-813e-1c7f34627ad4'; // AppFolio Billing
	const APP_FEE = '59b8777a-483c-46b7-a6ee-1c7f34627ad4'; // Application Fee
	const AUTH_DOT_NET = '59b8777a-3148-408e-85f0-1c7f34627ad4'; // Authorize.net Set Up
	const AX_EPAY = '59b8777a-3f98-46b9-bbff-1c7f34627ad4'; // Axia ePay Set Up
	const BILL_REJECTS = '59b8777a-516c-4466-812b-1c7f34627ad4'; // Billing for Rejects
	const CNL = '59b8777a-ec60-490a-ab85-1c7f34627ad4'; // Cancellation Fee
	const CONVERSION = '59b8777a-6214-4be0-ba0b-1c7f34627ad4'; // Conversion
	const BREACH_INS = '59b8777a-6f38-4635-a570-1c7f34627ad4'; // Data Breach Ins.
	const DDS_CRED = '59b8777a-5cf8-47f3-bd0d-1c7f34627ad4'; // DDS Credit
	const ENCRYPTION = '59b8777a-80a8-4f7f-b936-1c7f34627ad4'; // Encryption
	const EQ_FEE = '59b8777a-9ab0-4b39-9255-1c7f34627ad4'; // Equipment Fee
	const EXACT_SHIP = '59b8777a-36b4-4004-b906-1c7f34627ad4'; // Exact Shipping (No Tax)
	const GRAL_SHIP = '59b8777a-91a4-4723-ab2d-1c7f34627ad4'; // General Shipping Fee (CA - Taxed)
	const G_CARD_CA = '59b8777a-a6a8-4dc0-a0af-1c7f34627ad4'; // Giftcards (CA - Taxed)
	const G_CARD_OUT = '59b8777a-b304-49be-8031-1c7f34627ad4'; // Giftcards (Outside CA)
	const INSTALL = '59b8777a-14b8-4ed8-8393-1c7f34627ad4'; // Install
	const LEASE = '59b8777a-2d94-42df-8997-1c7f34627ad4'; // Lease Funding
	const MICROS_BILL = '59b8777a-c7f8-47a4-abb2-1c7f34627ad4'; // Micros Billing
	const MISC = '59b8777a-d580-41ce-924b-1c7f34627ad4'; // Misc Fees
	const PF_TERM_SETUP_FEE = '5aecc84f-91d8-4de9-9613-3b2b0a0101f3'; // Payment Fusion Terminal Setup Fee
	const PCI_NON_COMPLIANCE = '59b8777a-ea74-4215-8eb9-1c7f34627ad4'; // PCI Non-Compliance
	const PRECIDIA_BILL = '59b8777a-0288-4b12-b2ce-1c7f34627ad4'; // Precidia Billing
	const PROCESSING = '59b8777a-2dd0-4378-bd95-1c7f34627ad4'; // Processing Fees
	const REINJECT_FEE = '5cba5bdc-7778-4b66-8275-13f40a0101f3'; // Reinjection Fees
	const REJECT = '59b8777a-0e80-4352-bc53-1c7f34627ad4'; // Reject Fees
	const RENTALS_CA = '59b8777a-6974-4b33-bb72-1c7f34627ad4'; // Rentals (CA - Taxed)
	const RENTALS_OUT = '59b8777a-1d34-413a-8876-1c7f34627ad4'; // Rental Fees (Outside CA)
	const REPLACEMENT = '59b8777a-54e4-4fc9-88c5-1c7f34627ad4'; // Replacement Fees
	const SOFTWARE = '59b8777a-2be8-4f26-931f-1c7f34627ad4'; // Gateway/Software
	const SUP_CA = '59b8777a-b890-4ec2-8311-1c7f34627ad4'; // Supplies Fee (CA - Taxed)
	const SUP_OUT = '59b8777a-6290-4a70-8fed-1c7f34627ad4'; // Supplies (Outside CA)
	const TAX = '59b8777a-ffc4-40d6-b328-1c7f34627ad4'; // Tax
	const USAEPAY = '59b8777a-707c-4f7c-8c0b-1c7f34627ad4'; // USAePay Billing
	const VOIDED_W_FEE = '59b8777a-7ae4-496c-8d5f-1c7f34627ad4'; // Voided Warrenty Fee
	const WIRELESS_ACTIVATE = '59b8777a-854c-450c-9559-1c7f34627ad4'; // Wireless Activation
	const REASON_OTHER = '59b8777a-8444-4c8a-adfb-1c7f34627ad4'; // 'Other'

/***Deprecated and will be removed in the near future**/
/***@TODO remove after 2019-09-01**/
const EQ_CA = '59b8777a-5d94-460b-8ade-1c7f34627ad4'; // Equipment (CA - Taxed)
/************************************/
/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'position';

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'reason';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Utils.List' => array(
			'positionColumn' => self::SORT_FIELD,
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'NonTaxableReason' => array(
			'className' => 'NonTaxableReason',
			'foreignKey' => 'non_taxable_reason_id',
		)
	);
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'reason' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must enter a unique ach reason',
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Reason already exists, you must enter an unique reason.',
				'allowEmpty' => false,
			)
		),
		'position' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter number by which this reason will be ordered in the list of all ach reasons',
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'That spot is taken by another ach reason, enter another number.',
				'allowEmpty' => false,
			)
		),
		'accounting_report_col_alias' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
	);


/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['reason'])) {
			$this->data[$this->alias]['reason'] = $this->removeAnyMarkUp($this->data[$this->alias]['reason']);
		}
		
		return parent::beforeSave($options);
	}

/**
 * getAchReasonsList method
 * Returns a list of ach reasons, by default enabled reasons ordered in ascending order by the position column is returned.
 * 
 * @param array $settings Settings for list search can be specified except for fields
 * @return array
 */
	public function getAchReasonsList($settings = []) {
		$settings = Hash::merge([
			'order' => "{$this->alias}.position ASC",
			'conditions' => ["{$this->alias}.enabled" => true]
		], $settings);
		$settings['fields'] = ['id', 'reason'];

		return $this->find('list', $settings);
	}
}
