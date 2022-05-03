<?php

App::uses('AppModel', 'Model');

/**
 * Gateway Model
 *
 * @property EquipmentProgramming $EquipmentProgramming
 */
class Gateway extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Gateway already exists, you must enter an unique gateway name!',
				'allowEmpty' => false,
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
	);

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
	public $displayField = 'name';

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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Bankcard' => array(
			'className' => 'Bankcard',
			'foreignKey' => 'gateway_id',
			'dependent' => false,
		),
		'Discover' => array(
			'className' => 'Discover',
			'foreignKey' => 'disc_gw_gateway_id',
			'dependent' => false,
		),
		'EquipmentProgramming' => array(
			'className' => 'EquipmentProgramming',
			'foreignKey' => 'gateway_id',
			'dependent' => false,
		),
		'Gateway1' => array(
			'className' => 'Gateway1',
			'foreignKey' => 'gateway_id',
			'dependent' => false,
		),
		'Gateway2' => array(
			'className' => 'Gateway2',
			'foreignKey' => 'gw2_gateway_id',
			'dependent' => false,
		),
	);

/**
 * getSortedGatewayList
 *
 * @return array
 */
	public function getSortedGatewayList() {
		return $this->find('list', array('fields' => array('id', 'name'), 'order' => array('name' => 'asc')));
	}

/**
 * Overrides generic method in AppModel to get the model record list
 * Gets Enabled gateways
 *
 * @param array $settings specify costimized setings for list search 
 * @return array by default returns a list of enabled gateways sorted by the "position" column
 */
	public function getList($settings = []) {
		if (!array_key_exists("order", $settings)) {
			$settings = [
			"order" => ["{$this->alias}.position" => 'ASC']];
		}
		$settings["conditions"] = ["{$this->alias}.enabled" => true];
		return $this->find('list', $settings);
	}
}
