<?php

App::uses('AppModel', 'Model');

/**
 * EquipmentType Model
 *
 */
class EquipmentType extends AppModel {

/**
 * Type ids
 *
 * @var string
 */
	const HARDWARE_ID = '1dd22698-efa4-43ee-80cc-5ca62e732708';
	const SUPPLIES_ID = '4f15efcf-4df8-47c8-b7b3-e1268f5e7923';

/**
 * Type old codes
 *
 * @var string
 */
	const HARDWARE_OLD = 'HW';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'equipment_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

/**
 * getEquipmentTypesIDsList
 *
 * @return array containing equipment_type_description as keys and id as values
 */
	public function getEquipmentTypesIDsList() {
		return $this->find('list', array('recursive' => -1, 'fields' => array('equipment_type_description', 'id')));
	}

}
