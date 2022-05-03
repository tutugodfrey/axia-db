<?php
App::uses('AppModel', 'Model');
/**
 * AddlAmexRepCost Model
 *
 * @property UserCompensationProfile $UserCompensationProfile
 */
class AddlAmexRepCost extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_compensation_profile_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Missing user compensation profile id',
			),
		),
		'conversion_fee' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'sys_processing_fee' => array(
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
 * @param string $profileId compensation profile id
 * @return array
 */
	public function getByCompProfile($profileId) {
		$options = array(
			'conditions' => array("{$this->alias}.user_compensation_profile_id" => $profileId),
		);
		$result = $this->find('first', $options);
		//Set Default form data if there is none
		if (empty($result)) {
			$result['AddlAmexRepCost']['user_compensation_profile_id'] = $profileId;
		}
		return $result;
	}
}
