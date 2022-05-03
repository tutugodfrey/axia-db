<?php

App::uses('AppModel', 'Model');

/**
 * EquipmentProgrammingTypeXref Model
 *
 * @property Programming $Programming
 */
class EquipmentProgrammingTypeXref extends AppModel {

	public $useTable = 'equipment_programming_type_xrefs';

	private static $__ProgrammingTypes = array(
		'PMNTFUSION' => 'Payment Fusion',
		'VP2PE' => 'VP2PE',
		'AVS' => 'AVS',
		'INV' => 'Invoice',
		'SD' => 'Split Dial',
		'TIP' => 'Tips',
		'SRV' => 'Server #s',
		'NPB' => 'No PABX',
		'CVV' => 'CVV',
		'8' => '8',
		'9' => '9',
		'IP' => 'IP',
		'DIAL' => 'DIAL',
		'SSL' => 'SSL',
		'WIRELESS' => 'WIRELESS'
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'programming_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
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
		'EquipmentProgramming' => array(
			'className' => 'EquipmentProgramming',
			'foreignKey' => 'equipment_programming_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * getProgrammingTypes method
 *
 * @return array
 */
	public function getProgrammingTypes() {
		return self::$__ProgrammingTypes;
	}

/**
 * getProgrammingTypes method
 *
 * @return array
 */
	public function getHasManyProgrammingTypesList() {
		$data = array();
		$itter = 0;
		foreach (self::$__ProgrammingTypes as $key => $val) {
			$data['EquipmentProgrammingTypeXref'][$itter] = array($key => $val);
			$itter += 1;
		}
		return $data;
	}

/**
 * checkRemoveProgrammingTypesXref
 * Check if an EquipmentProgrammingTypeXref needs to be deleted based on user submitted data and deletes it.
 * 
 * @param array $requestData the $data['EquipmentProgrammingTypeXref'] which must be at at the top array dimention
 * @return array
 */
	public function checkRemoveProgrammingTypesXref($requestData = array()) {
		if (empty($requestData['EquipmentProgrammingTypeXref'])) {
			return $requestData;
		}

		/* If id is set but programming_type is missing in array then this means the user wants to remove that programming_type from EquipmentProgrammingTypeXref */
		foreach ($requestData['EquipmentProgrammingTypeXref'] as $key => $val) {
			if (array_key_exists('programming_type', $val) === false && !empty($val['id'])) {
				$this->delete($val['id']);
				unset($requestData['EquipmentProgrammingTypeXref'][$key]);
			}
		}
		/* Has every EquipmentProgrammingTypeXref been removed? */
		if (empty($requestData['EquipmentProgrammingTypeXref'])) {
			unset($requestData['EquipmentProgrammingTypeXref']);
		}
		return $requestData;
	}

}
