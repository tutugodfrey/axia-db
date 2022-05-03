<?php

App::uses('AppModel', 'Model');

/**
 * EquipmentItem Model
 *
 */
class EquipmentItem extends AppModel {

	public $displayField = 'equipment_item_description';

	public $order = array(
		'EquipmentItem.equipment_item_description' => 'ASC',
	);

/**
 * belongsTo association
 *
 * @var array $belongsTo
 */
	public $belongsTo = array(
		'EquipmentType' => array(
			'className' => 'EquipmentType',
			'foreignKey' => 'equipment_type_id',
		)
	);

/**
 * hasMany association
 *
 * @var array $hasMany
 */
	public $hasMany = array(
		'EquipmentCost' => array(
			'className' => 'EquipmentCost',
			'foreignKey' => 'equipment_item_id',
			'dependent' => true,
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'equipment_item_description' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('notBlank'),
				'message' => 'Description is required',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'equipment_item_true_price' => array(
			'validAmount' => array(
				'rule' => array('numeric'),
				'message' => 'true_price must be a valid amount',
				'allowEmpty' => true,
			),
		),
		'equipment_item_rep_price' => array(
			'validAmount' => array(
				'rule' => array('numeric'),
				'message' => 'rep_price must be a valid amount',
				'allowEmpty' => true,
			),
		),
	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['equipment_item_description'])) {
			$this->data[$this->alias]['equipment_item_description'] = $this->removeAnyMarkUp($this->data[$this->alias]['equipment_item_description']);
		}
		
		return parent::beforeSave($options);
	}
/**
 * afterSave Callback
 *
 * @param bool $created record created
 * @param array $options optional options for additional queries
 * @return void
 */
	public function afterSave($created, $options = array()) {
		// Update equipment costs for all users on equipment item creation
		if ($created) {
			$compensationProfiles = $this->EquipmentCost->UserCompensationProfile->find('list');
			$costs = array(
				'equipment_item_id' => $this->id,
				'true_cost' => Hash::get($this->data, 'EquipmentItem.equipment_item_true_price'),
				'rep_cost' => Hash::get($this->data, 'EquipmentItem.equipment_item_rep_price'),
				'partner_cost' => Hash::get($this->data, 'EquipmentItem.equipment_item_partner_price'),
				'partner_rep_cost' => Hash::get($this->data, 'EquipmentItem.equipment_item_partner_rep_price'),
			);
			$equipmentCosts = array();
			foreach ($compensationProfiles as $compensationProfileId => $displayValue) {
				$costs['user_compensation_profile_id'] = $compensationProfileId;
				$equipmentCosts[] = $costs;
			}

			if (!$this->EquipmentCost->saveMany($equipmentCosts)) {
				$this->log('The equipment costs could not be updated on the creation of the equipment item #' . $this->id, 'EquipmentCost');
			}
		}
	}

/**
 * Return the equipment type for the equipment_items table
 * equipment_type value for equipment items is HW (hardware) by default.
 * SP (supplies) items are added from SB inventory but that data is stored into the orderitems table
 *
 * @return string
 */
	public function getEquipmentType() {
		return $this->EquipmentType->field('id', array('EquipmentType.equipment_type_old' => EquipmentType::HARDWARE_OLD));
	}

/**
 * getEquipmentList method
 *
 * returns a list of all existing equipment in Ascending order
 *
 * @param bool $active if set returns active or inactive equipment else returns all equipment.
 * @throws InvalidArgumentException
 * @return array
 */
	public function getEquipmentList($active = null) {
		if (!empty($active) && is_bool($active) === false) {
			throw new InvalidArgumentException('getEquipmentList method expects null or boolean but ' . gettype($active) . ' was provided.');
		}
		$settings = array('fields' => array('id', 'equipment_item_description'), 'order' => array('equipment_item_description' => 'ASC'));
		if ($active !== null) {
			$settings['conditions'] = array('active' => (int)$active); //this field is integer not boolean in the db
		}
		return $this->find('list', $settings);
	}
}
