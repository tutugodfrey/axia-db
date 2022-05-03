<?php

App::uses('AppModel', 'Model');

class EquipmentCost extends AppModel {

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'rep_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Must be a valid amount number',
				'allowEmpty' => true
			)
		),
	);

/**
 * belongsTo association
 *
 * @var array $belongsTo
 * @access public
 */
	public $belongsTo = array(
		'EquipmentItem' => array(
			'className' => 'EquipmentItem',
			'foreignKey' => 'equipment_item_id',
		),
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id',
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id'
		)
	);

/**
 * Constructor
 *
 * @param mixed $id Model ID
 * @param string $table Table name
 * @param string $ds Datasource
 * @access public
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
			'equipment_item_id' => array(
				'notBlank' => array('rule' => array('notBlank'), 'required' => true, 'allowEmpty' => false,
					'message' => __('Please enter a Equipment Item', true))),
			'user_compensation_profile_id' => array(
				'notBlank' => array('rule' => array('notBlank'), 'required' => true, 'allowEmpty' => false,
					'message' => __('Please enter a User Compensation Profile Id', true))),
			'true_cost' => array(
				'numeric' => array('rule' => array('numeric'), 'required' => false, 'allowEmpty' => true,
					'message' => __('Please enter a numeric True Cost', true))),
			'rep_cost' => array(
				'numeric' => array('rule' => array('numeric'), 'required' => false, 'allowEmpty' => true,
					'message' => __('Please enter a numeric Rep Cost', true))),
		);
	}

/**
 * Adds a new record to the database
 *
 * @param array $data should be Contoller->data
 * @return array
 * @throws OutOfBoundsException
 * @access public
 */
	public function add($data = null) {
		if (!empty($data)) {
			$this->create();
			$result = $this->save($data);
			if ($result !== false) {
				$this->data = array_merge($data, $result);
				return true;
			} else {
				throw new OutOfBoundsException(__('Could not save the equipmentCost, please check your inputs.', true));
			}
			return $return;
		}
	}

/**
 * Edits an existing Equipment Cost
 *
 * @param string $id equipment cost id
 * @param array $data controller post data usually $this->data
 * @return mixed True on successfully save else post data as array
 * @throws OutOfBoundsException If the element does not exists
 * @access public
 */
	public function edit($id = null, $data = null) {
		$equipmentCost = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
		)));

		if (empty($equipmentCost)) {
			throw new OutOfBoundsException(__('Invalid Equipment Cost', true));
		}
		$this->set($equipmentCost);

		if (!empty($data)) {
			$this->set($data);
			$result = $this->save(null, true);
			if ($result) {
				$this->data = $result;
				return true;
			} else {
				return $data;
			}
		} else {
			return $equipmentCost;
		}
	}

/**
 * Validates the deletion
 *
 * @param string $id equipment cost id
 * @param array $data controller post data usually $this->data
 * @return bool True on success
 * @throws OutOfBoundsException If the element does not exists
 * @throws Exception
 * @access public
 */
	public function validateAndDelete($id = null, $data = array()) {
		$equipmentCost = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
		)));

		if (empty($equipmentCost)) {
			throw new OutOfBoundsException(__('Invalid Equipment Cost', true));
		}

		$this->data['equipmentCost'] = $equipmentCost;
		if (!empty($data)) {
			$data['EquipmentCost']['id'] = $id;
			$tmp = $this->validate;
			$this->validate = array(
				'id' => array('rule' => 'notBlank'),
				'confirm' => array('rule' => '[1]')
			);

			$this->set($data);
			if ($this->validates()) {
				return $this->delete($data['EquipmentCost']['id']);
			}
			$this->validate = $tmp;
			throw new Exception(__('You need to confirm to delete this Equipment Cost', true));
		}
	}

/**
 * Return the user equipment costs sorted by 'equipment_item_description'
 *
 * @param string $profileId user compensation profile id
 * @param string $partnerId user id belonging to a partner
 * @return array
 */
	public function getByCompProfile($profileId, $partnerId = null) {
		$options = array(
			'fields' => array(
				"{$this->alias}.id",
				"{$this->alias}.equipment_item_id",
				"{$this->alias}.user_compensation_profile_id",
				"{$this->alias}.rep_cost",
				// Related fields fields
				'EquipmentItem.id',
				'EquipmentItem.equipment_item_true_price',
				'EquipmentItem.equipment_item_description',
			),
			'recursive' => -1,
			'order' => array(
				'EquipmentItem.equipment_item_description' => 'ASC'
			),
			'conditions' => array(
				"{$this->alias}.user_compensation_profile_id" => $profileId,
			),
			'joins' => array(
				array(
					'table' => 'equipment_items',
					'alias' => 'EquipmentItem',
					'type' => 'INNER',
					'conditions' => array(
						"EquipmentItem.id = {$this->alias}.equipment_item_id",
						"{$this->alias}.user_compensation_profile_id" => $profileId,
					),
				)
			),
		);

		if (!empty($partnerId)) {
			$prtrCompId = $this->UserCompensationProfile->field('id', array(
					'UserCompensationProfile.user_id' => $partnerId,
					'UserCompensationProfile.is_default' => true
				));
			$options['fields'][] = "PartnerEquipmentCost.rep_cost";
			$options['joins'][] = array(
				'table' => 'equipment_costs',
				'alias' => 'PartnerEquipmentCost',
				'type' => 'INNER',
				'conditions' => array(
					"PartnerEquipmentCost.equipment_item_id = {$this->alias}.equipment_item_id",
					"PartnerEquipmentCost.user_compensation_profile_id = '$prtrCompId'"
				)
			);
		}
		$equipmentCosts = $this->find('all', $options);
		// The returned array will be formated as a related model
		$result = array();
		foreach ($equipmentCosts as &$equipmentCost) {
			$data = $equipmentCost['EquipmentCost'];
			$data['partner_cost'] = hash::get($equipmentCost, 'PartnerEquipmentCost.rep_cost');
			$data['EquipmentItem'] = $equipmentCost['EquipmentItem'];
			$result[] = $data;
		}
		return $result;
	}

/**
 * generateCompData array
 *
 * @param string $compensationId user compensation profile id
 * @return mixed
 */
	public function generateCompData($compensationId) {
		/* Does any record containing this $compensationId already exists? */
		if (!$this->field('user_compensation_profile_id', array('user_compensation_profile_id' => $compensationId))) {
			$equipmentItems = $this->EquipmentItem->find('all', array('recursive' => -1, 'conditions' => array(
					'active' => 1)));

			$equipmentCosts = array();
			foreach ($equipmentItems as $item) {
				$equipmentCosts[] = array(
					'equipment_item_id' => Hash::get($item, 'EquipmentItem.id'),
					'rep_cost' => Hash::get($item, 'EquipmentItem.equipment_item_rep_price'),
					'user_compensation_profile_id' => $compensationId,
				);
			}

			return $this->saveMany($equipmentCosts);
		}
		//cannot, once again, save many of the same compensation id when a set of that id already exists
		return null;
	}

/**
 * getRepCost array
 *
 * @param string $eqpmntId equipment_item_id
 * @param string $ucpId user compensation profile id
 * @return mixed string|false
 * @throws InvalidArgumentException
 */
	public function getRepCost($eqpmntId, $ucpId) {
		if (empty($eqpmntId) || empty($ucpId)) {
			throw new InvalidArgumentException("getRepCost function arguments are all required");
		}

		return $this->field('rep_cost', ['equipment_item_id' => $eqpmntId, 'user_compensation_profile_id' => $ucpId]);
	}
}
