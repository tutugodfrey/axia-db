<?php

App::uses('AppModel', 'Model');
App::uses('ResidualVolumeTier', 'Model');
App::uses('ResidualTimeFactor', 'Model');

/**
 * ResidualParameterType Model
 *
 */
class ResidualParameterType extends AppModel {

/**
 * Simple types are values not depending on other dimensions
 */
	const TYPE_SIMPLE = 0;

/**
 * Complex types depend on the the AssociatedUser (Dynamic)
 */
	const TYPE_MANAGER = 1;
	const TYPE_PARTNER = 2;

/**
 * Value types determine how we should manage the value for a given UserParameter
 */
	const VALUE_TYPE_CHECKBOX = 0;
	const VALUE_TYPE_NUMBER = 1;
	const VALUE_TYPE_PERCENTAGE = 2;

/**
 * Predefined type ids
 */
 	const R_MULTIPLE = '75adbe8c-7a0c-4ad3-92aa-faa3b234a003';
 	const PR_MULTIPLE = '06c32977-07e1-4727-badc-653facdc858d';
 	const MGR_MULTIPLE = '3254ae37-0a0a-411c-8c26-dc5fa8244088';

/**
 * Model default order
 *
 * @var array
 */
	public $order = array(
		'ResidualParameterType.order' => 'asc',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name is required',
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Name must be unique',
			),
		),
	);

/**
 * hasMany relations
 *
 * @var array
 */
	public $hasMany = array(
		'ResidualParameter',
		'ResidualTimeParameter'
	);

/**
 * Get the array of headers to generate the residual parameters list
 *
 * @param string $userId User id
 * @param string $compensationId Compensation id
 * @param string $model Parameter model
 * @todo sql optimize
 * @throws InvalidArgumentException
 * @throws NotFoundException
 * @return array
 */
	public function getHeaders($userId, $compensationId, $model = 'ResidualParameter') {
		$definedTiers = null;
		$overrideTier = null;
		switch ($model) {
			case 'ResidualParameter':
				$definedTiers = ResidualVolumeTier::DEFINED_TIERS;
				$overrideTier = ResidualVolumeTier::OVERRIDE_TIER;
				break;

			case 'ResidualTimeParameter':
				$definedTiers = ResidualTimeFactor::DEFINED_TIERS;
				break;

			default:
				throw new InvalidArgumentException(__('Invalid model for getHeaders method'));
		}

		if (!$this->{$model}->UserCompensationProfile->User->exists($userId)) {
			throw new NotFoundException(__('User not found for getHeaders method'));
		}

		if (!$this->{$model}->UserCompensationProfile->exists($compensationId)) {
			throw new NotFoundException(__('Compensation profile not found for getHeaders method'));
		}

		$residualParameterTypes = $this->find('all', array(
			'contain' => array(
				$model => array(
					'conditions' => array(
						"{$model}.user_compensation_profile_id" => $compensationId,
					),
				)
			)
		));

		$associatedUsers = array();
		$rolesConfig = Configure::read('AssociatedUserRoles');

		//Check permissions to see which additional associated users' data can be included
		//Note: Since SM inherits SM2's permissions we must check SM permissions first for currently logged in user
		//Only Primary managers and admins are permitted to see all associated users
		if ($this->RbacIsPermitted('app/actions/UserCompensationProfiles/view/module/SMCompModule')) {
			$associatedUsers[self::TYPE_MANAGER] = $this->{$model}->UserCompensationProfile->User->getAssociatedUsers($userId, $compensationId, Hash::extract($rolesConfig, 'SalesManager.label'));
			$associatedUsers[self::TYPE_PARTNER] = $this->{$model}->UserCompensationProfile->User->getAssociatedUsers($userId, $compensationId, Hash::extract($rolesConfig, 'PartnerRep.label'));
		} elseif ($this->RbacIsPermitted('app/actions/UserCompensationProfiles/view/module/SM2CompModule')) {
			//If allowed include only the currenly logged in UserAssociated as long as they are a secondary manager in addition to me
			$options = array(
				'conditions' => array(
					'AssociatedUser.user_id' => $userId,
					'AssociatedUser.associated_user_id' => CakeSession::read('Auth.User.id'),
					'AssociatedUser.user_compensation_profile_id' => $compensationId,
					'AssociatedUser.permission_level' => $this->{$model}->UserCompensationProfile->User->userRoles['roles']['2ndManager']['roles']
				),
				'contain' => array(
					'UserAssociated',
				),
			);
			$associatedUsers[self::TYPE_MANAGER] = $this->{$model}->UserCompensationProfile->User->AssociatedUser->find('all', $options);
		}

		$headers = array();
		for ($tier = 1; $tier <= $definedTiers; $tier++) {
			$this->_addTierHeaders($headers, $tier, $model, $residualParameterTypes, $associatedUsers);
		}
		if ($overrideTier !== null) {
			$this->_addTierHeaders($headers, $overrideTier, $model, $residualParameterTypes, $associatedUsers);
		}

		return $headers;
	}

/**
 * Add the headers of a tier to the result array
 *
 * @param array &$headers Headers array
 * @param int $tier Tier number
 * @param string $model Parameter model
 * @param array $residualParameterTypes Parameter types data
 * @param array $associatedUsers Associated Managers and Partner reps
 * @return void
 */
	protected function _addTierHeaders(&$headers, $tier, $model, $residualParameterTypes, $associatedUsers) {
		$tierInfo = array('Tier' => $tier);
		foreach ($residualParameterTypes as $residualParameterType) {
			$type = Hash::get($residualParameterType, "{$this->alias}.type");
			$typeId = Hash::get($residualParameterType, "{$this->alias}.id");
			$isMultiple = Hash::get($residualParameterType, "{$this->alias}.is_multiple");
			$extractPath = "{$model}.{n}";
			$baseConditions = "[tier={$tier}]]";
			switch ($type) {
				case self::TYPE_SIMPLE:
					$filteredData = array(
						$this->alias => Hash::get($residualParameterType, "{$this->alias}"),
						$model => Hash::extract($residualParameterType, $extractPath . $baseConditions),
					);
					$headers[] = Hash::merge($filteredData, $tierInfo);
					break;

				case self::TYPE_PARTNER:
				case self::TYPE_MANAGER:
					if (!empty($associatedUsers[$type])) {
						foreach ($associatedUsers[$type] as $associatedUser) {
							$userId = Hash::get($associatedUser, 'UserAssociated.id');
							$filteredData = array(
								$this->alias => Hash::get($residualParameterType, "{$this->alias}"),
								$model => Hash::extract($residualParameterType, $extractPath . $baseConditions . "[associated_user_id={$userId}]"),
							);
							$headers[] = Hash::merge($filteredData, $associatedUser, $tierInfo);
						}
					}
					break;
			}
		}
	}
}
