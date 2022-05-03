<?php

App::uses('AppModel', 'Model');

/**
 * PciCompliance Model
 *
 * @property PciComplianceDateType $PciComplianceDateType
 * @property SaqMerchant $SaqMerchant
 */
class PciCompliance extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'pci_compliance_date_type_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
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
			  'date_complete' => array(
						'date' => array(
								  'rule' => array('date'),
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
			  'PciComplianceDateType' => array(
						'className' => 'PciComplianceDateType',
						'foreignKey' => 'pci_compliance_date_type_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  ),
			  'SaqMerchant' => array(
						'className' => 'SaqMerchant',
						'foreignKey' => 'saq_merchant_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  )
	);

}
