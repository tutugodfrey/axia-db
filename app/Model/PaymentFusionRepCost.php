<?php
App::uses('AppModel', 'Model');
/**
 * PaymentFusionRepCost Model
 *
 * @property User $User
 * @property UserCompensationProfiles $UserCompensationProfiles
 */
class PaymentFusionRepCost extends AppModel {

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
		'standard_device_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'vp2pe_device_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'pfcc_device_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'Cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'vp2pe_pfcc_device_cost' => array(
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
 * getCosts
 *
 * Returns all records associated with the provided user_compensation_profile_id
 *
 * @param string $userId a user id
 * @param string $partnerId a partner id
 * @param boolean $isManager1 whether the $userId param belongs to a user with SM role
 * @param boolean $isManager2 a partner id $userId param belongs to a user with SM2 role
 * @return array one dimmensional array
 */
	public function getCosts($userId, $partnerId = null, $isManager1 = false, $isManager2 = false) {
		$conditions = array(
			"UserCompensationProfile.user_id" => $userId,
		);
		if ($isManager1 || $isManager2) {
			$roleName = ($isManager1)? User::ROLE_SM : User::ROLE_SM2;
			$roleId = $this->UserCompensationProfile->Role->field('id', ['name' => $roleName]);
			$conditions['role_id'] = $roleId;
		} else {
			$conditions['is_default'] = empty($partnerId);
			$conditions['partner_user_id'] = $partnerId;
		}

		$ucpId = $this->UserCompensationProfile->field('id', $conditions);

		if ($ucpId === false) {
			return [];
		}

		return Hash::get($this->getByCompProfile($ucpId), 'PaymentFusionRepCost');
	}
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
			$result['PaymentFusionRepCost']['user_compensation_profile_id'] = $profileId;
		}
		return $result;
	}
}
