<?php

App::uses('AppModel', 'Model');

/**
 * PciComplianceStatusLog Model
 *
 * @property PciComplianceDateType $PciComplianceDateType
 * @property SaqMerchant $SaqMerchant
 */
class PciComplianceStatusLog extends AppModel {
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
