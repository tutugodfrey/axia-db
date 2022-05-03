<?php

App::uses('AppModel', 'Model');

/**
 * UserParameterType Model
 *
 */
class UserParameterType extends AppModel {

/**
 * Simple types are values not depending on other dimensions
 */
	const TYPE_SIMPLE = 0;

/**
 * Complex types depend on the MerchantAcquirer and the AssociatedUser (Dynamic)
 * They have a specific logic for header generation and saving
 */
	const TYPE_COMPLEX_GROSS_PROFIT = 1;
	const TYPE_COMPLEX_MANUAL_OVERRIDE = 2;

/**
 * Value types determine how we should manage the value for a given UserParameter
 */
	const VALUE_TYPE_CHECKBOX = 0;
	const VALUE_TYPE_NUMBER = 1;

	public $order = array(
		'UserParameterType.order' => 'asc',
	);

/**
 * The UUIDs of all types
 */
	const DONT_DISPLAY = '81dbd2e6-21a2-4958-bd85-815c70f7d90a';
	const TIER_PROD = '7fe729ff-036a-47e5-adda-156ce727da0d';
	const ENABLED_FOR_REP = 'a8268c33-2e9f-4bc0-b482-82fdbcc0cd69';
	const PCT_OF_GROSS = 'd78ed6bf-739f-43b9-a262-dd343d3bcb5b';
	const OVERRIDE = 'f21e606e-e320-4f5a-b0ad-2842078eb0d8';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name is required'
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Name must be unique'
			)
		)
	);

/**
 * hasMany Relation
 *
 * @var array
 */
	public $hasMany = array(
		'UserParameter'
	);

/**
 * Get the array of headers to generate the user parameters list
 *
 * @param string $userId user Id
 * @param string $compensationId compensation id
 * @todo sql optimize
 * @return array
 */
	public function getHeaders($userId, $compensationId) {
		$headers = array();
		$userParameterTypes = $this->find('all', array(
			'contain' => array(
				'UserParameter' => array(
					'conditions' => array(
						'UserParameter.user_compensation_profile_id' => $compensationId
						),
					)
				)
			)
		);

		foreach ($userParameterTypes as $userParameterType) {
			$type = Hash::get($userParameterType, "{$this->alias}.type");
			if ($type == self::TYPE_SIMPLE) {
				//simple types, add the column
				$headers[] = $userParameterType;
			} elseif ($type == self::TYPE_COMPLEX_GROSS_PROFIT) {
				$this->_getGrossProfitHeaders($userId, $compensationId, $userParameterType, $headers);
			}
		}
		return $headers;
	}

/**
 * Parse complex userParameterType and add all associated columns based on current user assigned users
 *
 * @param string $userId user id
 * @param string $compensationId compensation id
 * @param array $userParameterType users parameter values
 * @param array &$headers header values by reference
 * @return bool
 */
	protected function _getGrossProfitHeaders($userId, $compensationId, $userParameterType, &$headers) {
		$userParameterTypes = array();
		//we need to iterate on users and merchant_acquirers
		//get the associated users
		//get merchant acquirers
		$associatedUsers = $this->_getAssociatedUsers($userId, $compensationId);
		//@todo optimize
		$merchantAcquirers = ClassRegistry::init('MerchantAcquirer')->find('all', array(
			'recursive' => -1, 'conditions' => array('reference_only' => false)
		));
		foreach ($associatedUsers as $associatedUser) {
			foreach ($merchantAcquirers as $merchantActquirer) {
				$headers[] = Hash::merge($userParameterType, $associatedUser, $merchantActquirer);
			}
		}
		return true;
	}

/**
 * Method to mock on unit tests since it uses Rbac permission calls
 *
 * @param type $userId user id
 * @param type $compensationId compsensation id
 * @return array
 */
	protected function _getAssociatedUsers($userId, $compensationId) {
		return $this->UserParameter->UserCompensationProfile->User->getAssociatedUsersUserParameters($userId, $compensationId);
	}
}
