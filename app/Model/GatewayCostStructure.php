<?php
App::uses('AppModel', 'Model');
/**
 * GatewayCostStructure Model
 *
 * @property UserCompensationProfile $UserCompensationProfile
 * @property Gateway $Gateway
 */
class GatewayCostStructure extends AppModel {

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'rep_monthly_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'rep_rate_pct' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
		'rep_per_item' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array('byCompProfile' => true);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id',
		),
		'Gateway' => array(
			'className' => 'Gateway',
			'foreignKey' => 'gateway_id',
		)
	);

/**
 * Custom finder query for Gateway Cost Structures
 *
 * Returns all records associated with the provided user_compensation_profile_id
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 * @throws Exception
 * @return array Modified query OR results of query
 */
	protected function _findByCompProfile($state, $query, $results = array()) {
		if (empty($query['conditions']['GatewayCostStructure.user_compensation_profile_id']) && empty($query['conditions']['user_compensation_profile_id'])) {
			throw new Exception(__('User Compensation Profile Id in conditions is required for custom find byCompProfile.'));
		}

		if ($state === 'before') {
			$query['contain'] = array('Gateway');
			$query['order'] = array("Gateway.name ASC");
			return $query;
		}

		if ($state === 'after' && empty($results)) {
			$gwsList = $this->Gateway->find('list', array('order' => array("Gateway.name ASC")));
			foreach ($gwsList as $key => $gwName) {
				$results[] = array(
					'GatewayCostStructure' => array(
							'user_compensation_profile_id' => $query['conditions']['GatewayCostStructure.user_compensation_profile_id'],
							'gateway_id' => $key,
						),
					'Gateway' => array(
							'name' => $gwName
						)
				);
			}
		}
		return $results;
	}

}
