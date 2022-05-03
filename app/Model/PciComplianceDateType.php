<?php

App::uses('AppModel', 'Model');

/**
 * PciComplianceDateType Model
 *
 * @property PciComplianceStatusLog $PciComplianceStatusLog
 * @property PciCompliance $PciCompliance
 */
class PciComplianceDateType extends AppModel {
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			  'PciComplianceStatusLog' => array(
						'className' => 'PciComplianceStatusLog',
						'foreignKey' => 'pci_compliance_date_type_id',
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
			  'PciCompliance' => array(
						'className' => 'PciCompliance',
						'foreignKey' => 'pci_compliance_date_type_id',
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

}
