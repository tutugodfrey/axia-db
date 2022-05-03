<?php
App::uses('AppModel', 'Model');
/**
 * AttritionRatio Model
 *
 * @property User $User
 */
class AttritionRatio extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_compensation_profile_id' => array(
			'rule' => array('notBlank'),
		),
		'associated_user_id' => array(
				'rule' => array('notBlank'),
		),
		'percentage' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		)
	);

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
		'UserCompensationProfile',
		'UserAssociated' => array(
			'className' => 'User',
			'foreignKey' => 'associated_user_id',
		),
		'UserCompensationAssociation' => array(
				'className' => 'AssociatedUser',
				'foreignKey' => 'user_compensation_profile_id',
			)
	);

/**
 * Default data
 * Sets an array of empty data which can be used to render an empty form
 * 
 * @param string $userId a user id
 * @param array $associatedUsers users associated with userId. Self association in userId is allowed.
 * @return arrray
 */
	public function defaultData($userId, $associatedUsers) {
		//Check if self association is set
		if (!isset($associatedUsers[$userId])) {
			$data[] = array(
				'AttritionRatio' => array(
					'associated_user_id' => $userId,
					'percentage' => '0',
				)
			);
		}
		foreach ($associatedUsers as $assocUserId => $associatedUser) {
			$data[] = array(
				'AttritionRatio' => array(
					'associated_user_id' => $assocUserId,
					'percentage' => '0',
				)
			);
		}
		return $data;
	}

/**
 * Custom finder query for Atttrition ratios
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
		if (empty($query['conditions']['AttritionRatio.user_compensation_profile_id']) && empty(Hash::get($query, 'conditions.user_compensation_profile_id'))) {
			throw new Exception(__('User Compensation Profile Id in conditions is required for custom find byCompProfile.'));
		}

		if ($state === 'before') {
			$query['contain'] = array(
				'UserCompensationProfile',
				'UserCompensationAssociation',
				'UserAssociated' => array(
						'fields' => array('id', 'fullname'))
			);
			return $query;
		}

		return $results;
	}

}
