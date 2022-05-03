<?php
App::uses('AppModel', 'Model');
/**
 * WebAchRepCost Model
 *
 * @property User $User
 * @property UserCompensationProfiles $UserCompensationProfiles
 */
class WebAchRepCost extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_compensation_profile_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
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
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * getByCompProfile
 *
 * Returns all records associated with the provided user_compensation_profile_id
 *
 * @param string $profileId a user compensation profile id
 * @return array
 */
	public function getByCompProfile($profileId) {
		$options = array(
			'conditions' => array("{$this->alias}.user_compensation_profile_id" => $profileId),
		);
		$result = $this->find('first', $options);
		//Set Default form data if there is no data already stored
		if (empty($result)) {
			$result['WebAchRepCost']['user_compensation_profile_id'] = $profileId;
		}
		return $result;
	}
}
