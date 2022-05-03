<?php

App::uses('AppModel', 'Model');

/**
 * SaqMerchantPciEmailSent Model
 *
 * @property SaqMerchant $SaqMerchant
 * @property SaqMerchantPciEmail $SaqMerchantPciEmail
 */
class SaqMerchantPciEmailSent extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'saq_merchant_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'saq_merchant_pci_email_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'date_sent' => array(
						'datetime' => array(
								  'rule' => array('datetime'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
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
			  'SaqMerchant' => array(
						'className' => 'SaqMerchant',
						'foreignKey' => 'saq_merchant_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  ),
			  'SaqMerchantPciEmail' => array(
						'className' => 'SaqMerchantPciEmail',
						'foreignKey' => 'saq_merchant_pci_email_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  )
	);

}
