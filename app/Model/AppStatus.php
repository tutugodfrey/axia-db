<?php

App::uses('AppModel', 'Model');

class AppStatus extends AppModel {

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
		'MerchantAchAppStatus' => array(
			'className' => 'MerchantAchAppStatus',
			'foreignKey' => 'merchant_ach_app_status_id',
		),
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id',
		)
	);

/**
 * hasMany association
 *
 * @var array $hasMany
 * @access public
 */
	public $hasMany = array(
		'MerchantAch' => array(
			'className' => 'MerchantAch',
			'foreignKey' => 'app_status_id',
			'dependent' => false,
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
			'merchant_ach_app_status_id' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'required' => true,
					'allowEmpty' => false,
					'message' => __('Please enter a Merchant Ach App Status', true)
				)
			),
			'user_compensation_profile_id' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'required' => true,
					'allowEmpty' => false,
					'message' => __('Please enter a Compensation Profile Id', true)
				)
			),
			'rep_cost' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'required' => false,
					'allowEmpty' => true,
					'message' => __('Please enter a numeric Rep Cost', true)
				)
			),
			'axia_cost' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'required' => false,
					'allowEmpty' => true,
					'message' => __('Please enter a numeric Axia Cost', true)
				)
			),
			'rep_expedite_cost' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'required' => false,
					'allowEmpty' => true,
					'message' => __('Please enter a numeric Rep Expedite Cost', true)
				)
			),
			'axia_expedite_cost_tsys' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'required' => false,
					'allowEmpty' => true,
					'message' => __('Please enter a numeric Axia Expedite Cost Tsys', true)
				)
			),
			'axia_expedite_cost_sage' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'required' => false,
					'allowEmpty' => true,
					'message' => __('Please enter a numeric Axia Expedite Cost Sage', true)
				)
			),
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
				return true;
			} else {
				throw new OutOfBoundsException(__('Could not save the appStatus, please check your inputs.', true));
			}
			return $return;
		}
	}

/**
 * Edits an existing App Status.
 *
 * @param string $id app status id
 * @param array $data controller post data usually $this->data
 * @return mixed True on successfully save else post data as array
 * @throws OutOfBoundsException If the element does not exists
 * @access public
 */
	public function edit($id = null, $data = null) {
		$appStatus = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
			)
		));

		if (empty($appStatus)) {
			throw new OutOfBoundsException(__('Invalid App Status', true));
		}
		$this->set($appStatus);

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
			return $appStatus;
		}
	}

/**
 * Returns the record of a App Status.
 *
 * @param string $id app status id.
 * @param array $options Find options
 * @return array
 * @throws OutOfBoundsException If the element does not exists
 * @access public
 */
	public function view($id = null, $options = array()) {
		$appStatus = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id
			)
		));

		if (empty($appStatus)) {
			throw new OutOfBoundsException(__('Invalid App Status', true));
		}

		return $appStatus;
	}

/**
 * Validates the deletion
 *
 * @param string $id app status id
 * @param array $data controller post data usually $this->data
 * @return bool true on success
 * @throws OutOfBoundsException If the element does not exists
 * @throws Exception
 * @access public
 */
	public function validateAndDelete($id = null, $data = array()) {
		$appStatus = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
			)
		));

		if (empty($appStatus)) {
			throw new OutOfBoundsException(__('Invalid App Status', true));
		}

		$this->data['appStatus'] = $appStatus;
		if (!empty($data)) {
			$data['AppStatus']['id'] = $id;
			$tmp = $this->validate;
			$this->validate = array(
				'id' => array('rule' => 'notBlank'),
				'confirm' => array('rule' => '[1]')
			);

			$this->set($data);
			if ($this->validates()) {
				return $this->delete($data['AppStatus']['id']);
			}
			$this->validate = $tmp;
			throw new Exception(__('You need to confirm to delete this App Status', true));
		}
	}
}
