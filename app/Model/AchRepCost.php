<?php

App::uses('AppModel', 'Model');

/**
 * AchRepCost Model
 *
 * @property User $User
 * @property AchProvider $AchProvider
 */
class AchRepCost extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'ach_provider_id' => array(
			'uuid' => array(
				'rule' => array('notBlank'),
				'allowEmpty' => false,
				'required' => true,
			),
		),
		'user_compensation_profile_id' => array(
			'uuid' => array(
				'rule' => array('notBlank'),
				'allowEmpty' => false,
				'required' => true,
			),
		),
		'rep_rate_pct' => array(
			'validPercentage' => array(
				'rule' => 'validPercentage',
				'allowEmpty' => true
			)
		),
		'rep_per_item' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'rep_monthly_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
			'User' => array(
						'className' => 'User',
						'foreignKey' => 'user_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'AchProvider' => array(
						'className' => 'AchProvider',
						'foreignKey' => 'ach_provider_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'UserCompensationProfile' => array(
						'className' => 'UserCompensationProfile',
						'foreignKey' => 'user_compensation_profile_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),

	);

/**
 *getByCompProfile
 *
 * Returns all records associated with the provided user_compensation_profile_id
 *
 * @param string $profileId compensation profile id
 * @return array
 */
	public function getByCompProfile($profileId) {
		$options = array(
			'conditions' => array("{$this->alias}.user_compensation_profile_id" => $profileId),
			'contain' => array("AchProvider"),
			'order' => array("AchProvider.provider_name ASC"),
		);

		$result = $this->find('all', $options);

		if (empty($result)) {
			foreach ($this->AchProvider->find('list') as $key => $providerName) {
				$result[] = array(
					'AchRepCost' => array(
							'user_compensation_profile_id' => $profileId,
							'ach_provider_id' => $key,
						),
					'AchProvider' => array(
							'provider_name' => $providerName
						)
					);
			}
		}

		return $result;
	}

}
