<?php

App::uses('AppModel', 'Model');

/**
 * MerchantAchAppStatus Model
 *
 * @property MerchantAch $MerchantAch
 */
class MerchantAchAppStatus extends AppModel {

/**
 * Order
 *
 * @var array
 */
	public $order = array(
		'MerchantAchAppStatus.rank' => 'asc'
	);

/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'rank';

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
 * Custom finders
 * @var array
 */
	public $findMethods = ['enabled' => true];

/**
 * Display Field
 *
 * @var string
 */
	public $displayField = 'app_status_description';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'app_status_id_old' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true,
			),
		),
		'app_status_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $hasMany = array(
		'MerchantAch' => array(
			'className' => 'MerchantAch',
			'foreignKey' => 'merchant_ach_app_status_id',
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['app_status_description'])) {
			$this->data[$this->alias]['app_status_description'] = $this->removeAnyMarkUp($this->data[$this->alias]['app_status_description']);
		}
		
		return parent::beforeSave($options);
	}
	
/**
 * getAchAppStatusList method
 *
 * @return array
 */
	public function getAchAppStatusList() {
		return $this->find('list', array('fields' => array('id', 'app_status_description')));
	}

/**
 * Get list of enabled app status
 *
 * @param string $state state
 * @param bool $query query settings
 * @param mixed $results list of enabled app statuses with same structure as find('list') or null 
 * @return array
 */
	protected function _findEnabled($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = ['id', 'app_status_description'];
			$query['conditions']['enabled'] = true;
			return $query;
		} elseif ($state === 'after') {
			$results = array_combine(Hash::extract($results, "{n}.MerchantAchAppStatus.id"), Hash::extract($results, "{n}.MerchantAchAppStatus.app_status_description"));
		}
		return $results;
	}

}
