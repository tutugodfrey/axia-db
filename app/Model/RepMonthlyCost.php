<?php

App::uses('AppModel', 'Model');

class RepMonthlyCost extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'RepMonthlyCost';

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'credit_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'debit_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'ebt_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
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
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id',
		),
		'BetNetwork' => array(
			'className' => 'BetNetwork',
			'foreignKey' => 'bet_network_id',
		)
	);

/**
 * getByCompProfile
 *
 * Returns all records associated with the provided user_compensation_profile_id
 *
 * @param string $profileId a profile id
 * @return array
 */
	public function getByCompProfile($profileId) {
		$options = array(
			'conditions' => array("{$this->alias}.user_compensation_profile_id" => $profileId),
			'contain' => array("BetNetwork"),
			'order' => array("BetNetwork.name ASC"),
		);
		$result = $this->find('all', $options);

		if (empty($result)) {
			$betNetList = $this->BetNetwork->find('list', array('order' => array("BetNetwork.name ASC")));
			foreach ($betNetList as $key => $bNetworkName) {
				$result[] = array(
					'RepMonthlyCost' => array(
							'user_compensation_profile_id' => $profileId,
							'bet_network_id' => $key,
						),
					'BetNetwork' => array(
							'name' => $bNetworkName
						)
					);
			}
		}
		return $result;
	}

}
