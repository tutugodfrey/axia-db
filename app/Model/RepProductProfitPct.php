<?php

App::uses('AppModel', 'Model');

class RepProductProfitPct extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'RepProductProfitPct';

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = array();

/**
 * belongsTo association
 *
 * @var array $belongsTo
 * @access public
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ProductsServicesType' => array(
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
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
			'user_id' => array(
				'uuid' => array('rule' => array('uuid'), 'required' => true, 'allowEmpty' => false, 'message' => __('Please enter a User', true))),
			'products_services_type_id' => array(
				'uuid' => array('rule' => array('uuid'), 'required' => true, 'allowEmpty' => false, 'message' => __('Please enter a Products Services Type', true))),
		);
	}

/**
 * Adds a new record to the database
 *
 * @param array $data should be Contoller->data
 * @throws OutOfBoundsException
 * @return array
 * @access public
 */
	public function add($data = null) {
		if (!empty($data)) {
			$this->create();
			$result = $this->save($data);
			if ($result !== false) {
				$this->data = array_merge($data, $result);
			} else {
				throw new OutOfBoundsException(__('Could not save the repProductProfitPct, please check your inputs.', true));
			}
			return true;
		} else {
			return false;
		}
	}

/**
 * Edits an existing Rep Product Profit Pct.
 *
 * @param string $id rep product profit pct id
 * @param array $data controller post data usually $this->data
 * @return mixed True on successfully save else post data as array
 * @throws OutOfBoundsException If the element does not exists
 * @access public
 */
	public function edit($id = null, $data = null) {
		$repProductProfitPct = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
				)));

		if (empty($repProductProfitPct)) {
			throw new OutOfBoundsException(__('Invalid Rep Product Profit Pct', true));
		}
		$this->set($repProductProfitPct);

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
			return $repProductProfitPct;
		}
	}

/**
 * Validates the deletion
 *
 * @param string $id rep product profit pct id
 * @param array $data controller post data usually $this->data
 * @return bool True on success
 * @throws OutOfBoundsException If the element does not exists
 * @throws Exception if it doesn't validate
 * @access public
 */
	public function validateAndDelete($id = null, $data = array()) {
		$repProductProfitPct = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
				)));

		if (empty($repProductProfitPct)) {
			throw new OutOfBoundsException(__('Invalid Rep Product Profit Pct', true));
		}

		$this->data['repProductProfitPct'] = $repProductProfitPct;
		if (!empty($data)) {
			$data['RepProductProfitPct']['id'] = $id;
			$tmp = $this->validate;
			$this->validate = array(
				'id' => array('rule' => 'notBlank'),
				'confirm' => array('rule' => '[1]'));

			$this->set($data);
			if ($this->validates()) {
				if ($this->delete($data['RepProductProfitPct']['id'])) {
					return true;
				}
			}
			$this->validate = $tmp;
			throw new Exception(__('You need to confirm to delete this Rep Product Profit Pct', true));
		}
	}
}