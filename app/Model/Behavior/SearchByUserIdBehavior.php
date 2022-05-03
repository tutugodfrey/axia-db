<?php
App::uses('ModelBehavior', 'Model');

class SearchByUserIdBehavior extends ModelBehavior {

/**
 * settings indexed by model name.
 *
 * @var array
 */
	public $settings = array(
		'ManagerFields' => array(
			'ResidualReport' => array(
				'smFieldName' => 'manager_id',
				'sm2FieldName' => 'manager_id_secondary'
				),
			'CommissionPricing' => array(
				'smFieldName' => 'manager_id',
				'sm2FieldName' => 'secondary_manager_id'
			),
			'Merchant' => array(
				'smFieldName' => 'sm_user_id',
				'sm2FieldName' => 'sm2_user_id'
			),
		),
	);

/**
 * Default settings
 * - string 'userRelatedModel': The associated Model related to Users that will be used on the query.
 *	If empty, the behavior's Model will be used instead.
 *
 * - string 'userFieldName': the user field name to use on querys
 *
 * @var array
 */
	protected $_defaults = array(
		'userRelatedModel' => null,
		'userFieldName' => 'user_id',
	);

/**
 * Configuration of model
 *
 * @param Model $Model the current model inheriting $this behavior
 * @param array $config configuration params
 * @throws OutOfBoundsException
 * @throws InvalidArgumentException
 * @return void
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $config);
		$this->User = ClassRegistry::init('User');
		// Config parameters validation
		$userRelatedModel = Hash::get($this->settings, "{$Model->alias}.userRelatedModel");
		if ($userRelatedModel === $Model->alias) {
			$this->settings[$Model->alias]['userRelatedModel'] = $userRelatedModel = null;
		}
		if (!empty($userRelatedModel)) {
			if (empty($Model->{$userRelatedModel}->User->Entity)) {
				throw new OutOfBoundsException(__("Model %s is not associated with User or Entity", "{$Model->alias}.{$userRelatedModel}"));
			}
		} else {
			if (empty($Model->User->Entity)) {
				throw new OutOfBoundsException(__("Model %s is not associated with User or Entity", $Model->alias));
			}
		}

		if (empty(Hash::get($this->settings, "{$Model->alias}.userFieldName"))) {
			throw new InvalidArgumentException(__("userFieldName can not be empty"));
		}
	}

/**
 * Search by user id complex search based on Entity/Manager/User dropdown
 *
 * @param Model $Model Model class
 * @param array $data Submitted data args
 * @throws OutOfBoundsException
 * @return mixed
 */
	public function searchByUserId(Model $Model, $data = array()) {
		$UserModel = ClassRegistry::init('User');
		$extracted = $UserModel->extractComplexId(Hash::get($data, 'user_id'));

		$prefix = Hash::get($extracted, 'prefix');
		$userId = Hash::get($extracted, 'id');

		switch ($prefix) {
			case User::PREFIX_ALL:
				return $this->_searchAllUsers($UserModel);
			case User::PREFIX_PARENT:
				return $this->_searchByParentUserId($UserModel, $userId);

			case User::PREFIX_ENTITY:
				 if (Hash::get($Model->filterArgs, 'user_id.searchByMerchantEntity')) {
				 	$Merchant = ClassRegistry::init('Merchant');
					return $this->_searchByMerchantEntityId($Merchant, $userId);
				 } else {
					return $this->_searchByEntityId($UserModel, $userId);
				 }
			case User::PREFIX_USER:
			default:
				return $this->_searchByUserId($UserModel, $userId, $Model);
		}
	}

/**
 * Search conditions by user_id
 *
 * @param Model $Model model
 * @param string $userId userId
 * @param Model $AssociatedModel the current model inheriting $this behavior
 * @return array
 */
	protected function _searchByUserId($Model, $userId, $AssociatedModel) {
		$settings = array(
			'conditions' => array(
				'User.id' => $userId,
			),
			'fields' => array(
				'User.id'
			)
		);
		//Limit the result set for partners referrers and resellers
		if (!empty($conditionKey)) {
			$settings['conditions'][$conditionKey] = $userId;
		}
		$query = $Model->getQuery('all', $settings);

		return $query;
	}

/**
 * Search conditions by parent_user_id
 *
 * @param Model $Model model
 * @param string $parentUserId userId
 * @return array
 */
	protected function _searchByParentUserId($Model, $parentUserId) {
		$query = $Model->getQuery('all', array(
			'conditions' => array("OR" => array(
				'User.parent_user_id' => $parentUserId,
				'User.id' => $parentUserId
				)
			),
			'fields' => array(
				'User.id'
			)
		));
		return $query;
	}

/**
 * Search conditions by entity_id
 *
 * @param Model $Model model
 * @return array
 */
	protected function _searchAllUsers($Model) {
		$query = $Model->getQuery('all', array(
			'fields' => array(
				'User.id'
			)
		));
		return $query;
	}

/**
 * Search conditions by entity_id
 *
 * @param Model $Model model
 * @param string $entityId entityId
 * @return array
 */
	protected function _searchByEntityId($Model, $entityId) {
		$query = $Model->getQuery('all', array(
			'conditions' => array(
				'User.entity_id' => $entityId
			),
			'fields' => array(
				'User.id'
			)
		));
		return $query;
	}

/**
 * Search conditions by entity_id
 *
 * @param Model $Model model
 * @param string $entityId entityId
 * @return array
 */
	protected function _searchByMerchantEntityId($Model, $entityId) {
		$query = $Model->getQuery('all', array(
			'conditions' => array(
				'Merchant.entity_id' => $entityId
			),
			'fields' => array(
				'Merchant.id'
			)
		));
		return $query;
	}

/**
 * Return the array with the join configuration to the users table
 *
 * @param Model $Model the current model inheriting $this behavior
 * @return array
 */
	protected function _getUsersJoin(Model $Model) {
		$userRelatedModel = Hash::get($this->settings, "{$Model->alias}.userRelatedModel");
		$userRelatedModel = !empty($userRelatedModel) ? $userRelatedModel : $Model->alias;
		$userFieldName = Hash::get($this->settings, "{$Model->alias}.userFieldName");

		return array(
			'table' => 'users',
			'alias' => 'User',
			'type' => 'INNER',
			'conditions' => array(
				"{$userRelatedModel}.{$userFieldName} = User.id"
			)
		);
	}

/**
 * Utility function to get the user id (mocked in unit tests)
 *
 * @param string $key key to retrieve from current logged in user
 * @return string
 */
	protected function _getLoggedInUser($key = null) {
		return AuthComponent::user($key);
	}

/**
 * searchByExternalUser 
 * Builds search conditions for a search in which the user being searched for is an external user (Partner/Referer/Reseller).
 * This method is dynamically set as the method element in $this->filterArgs[method] member variable when current user is an external user
 *
 * @param Model $Model the current model implementing $this behavior
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function searchByExternalUser(Model $Model, $data = array()) {
		$complexUserId = $this->User->extractComplexId(Hash::get($data, 'user_id'));
		$searchId = $complexUserId['id'];
		$modelAlias = null;
		//Check if model has any of the foreign key user ids required for search
		if ($Model->hasField(['partner_id', 'referer_id', 'reseller_id']) === false) {
			$modelAlias = Hash::get($this->settings, "{$Model->alias}.userRelatedModel");
		}
		if (empty($modelAlias)) {
			$modelAlias = $Model->alias;
		}

		$cond = array(
			'AND' => array('OR' => array(
				"$modelAlias.partner_id" => $searchId,
				"$modelAlias.referer_id" => $searchId,
				"$modelAlias.reseller_id" => $searchId,
			))
		);
		return $cond;
	}

/**
 * searchByParentUser 
 * Builds search conditions for a search in which the user being searched for is the parent of a group of users with which it is associated.
 * This method is dynamically set as the method element in $this->filterArgs[method] parent class member variable when current user is a manager
 *
 * @param Model $Model the current model implementing $this behavior
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function searchByParentUser(Model $Model, $data = array()) {
		$complexUserId = $this->User->extractComplexId(Hash::get($data, 'user_id'));
		$parentUserId = $complexUserId['id'];
		$parentUserRoles = $this->User->getUserRolesList($parentUserId);
		$modelAlias = $Model->alias;
		if ($modelAlias !== 'CommissionReport') {
			$smField = Hash::get($this->settings, "ManagerFields.$modelAlias.smFieldName");
			$sm2Field = Hash::get($this->settings, "ManagerFields.$modelAlias.sm2FieldName");
			if (empty($smField) || empty($sm2Field)) {
				$modelAlias = Hash::get($this->settings, "$modelAlias.userRelatedModel");
				$smField = Hash::get($this->settings, "ManagerFields.$modelAlias.smFieldName");
				$sm2Field = Hash::get($this->settings, "ManagerFields.$modelAlias.sm2FieldName");
			}
		}
		if (empty($parentUserId)) {
			//When parentUserId is empty we want to get records of users that don't have a manager association
			$cond = array(
				"$modelAlias.user_id NOT IN (SELECT distinct(user_id) from associated_users where permission_level = '" . User::ROLE_SM . "' OR permission_level = '" . User::ROLE_SM2 . "')",
			);
			//CommissionReport's table does not have any manager user foreingKeys
			if ($modelAlias !== 'CommissionReport') {
				$cond[] = "$modelAlias.$smField IS NULL";
				$cond[] = "$modelAlias.$sm2Field IS NULL";
			}
		} else {
			$cond = array(
				'OR' => array(
					"$modelAlias.user_id" => $parentUserId,
				),
			);
			//CommissionReport's table does not have any manager user foreingKeys
			if ($modelAlias !== 'CommissionReport') {
				if (!empty($parentUserRoles[User::ROLE_SM])) {
					$cond['OR'][] = "$modelAlias.user_id IN (SELECT distinct(user_id) from associated_users where associated_user_id = '" . $parentUserId . "' AND (permission_level = '" . User::ROLE_SM . "' OR permission_level = '" . User::ROLE_SM2 . "'))";

					$cond['AND']['OR'] = [
						'OR' => [
							"$modelAlias.$smField" => $parentUserId,
							"$modelAlias.$sm2Field" => $parentUserId
						],
						[
							'AND' => [
								"$modelAlias.$smField IS NULL",
								"$modelAlias.user_id" => $parentUserId
							]
						]
					];
					
				} elseif (!empty($parentUserRoles[User::ROLE_SM2])) {
					$cond['OR']["$modelAlias.$sm2Field"] = $parentUserId;
				}
			} else {
				$cond['OR'][] = "$modelAlias.user_id IN (SELECT distinct(user_id) from associated_users where associated_user_id = '" . $parentUserId . "' AND (permission_level = '" . User::ROLE_SM . "' OR permission_level = '" . User::ROLE_SM2 . "'))";
				$smField = $this->settings['ManagerFields']['Merchant']['smFieldName'];
				$sm2Field = $this->settings['ManagerFields']['Merchant']['sm2FieldName'];
				//Since commission_reports table does not have manager user id FKs
				//we must use associated model Merchant manager ids in order to narrow down the results properly.
				//We want users associated with $parentUserId AND only records where $parentUserId is a manager under the
				//associated Merchant
				//another AND OR block
				$cond[]['OR'] = array(
					"Merchant.user_id" => $parentUserId,
					"Merchant.$smField" => $parentUserId,
					"Merchant.$sm2Field" => $parentUserId,
				);
			}
		}

		return $cond;
	}

/**
 * searchByUserAndManager 
 * Builds search conditions for a search in which the user performing the search is a manager and the user being searched for is any
 * This method is dynamically set as the method element in $this->filterArgs[method] member variable when current user is a manager
 *
 * @param Model $Model the current model implementing $this behavior
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function searchByUserAndManager(Model $Model, $data = array()) {
		$complexUserId = $this->User->extractComplexId(Hash::get($data, 'user_id'));
		$searchId = $complexUserId['id'];
		$curUserId = $this->_getLoggedInUser('id');
		$modelAlias = $Model->alias;
		if ($Model->hasField(['partner_id', 'referer_id', 'reseller_id']) === false) {
			$modelAlias = Hash::get($this->settings, "{$Model->alias}.userRelatedModel");
		}
		$smField = $this->settings['ManagerFields'][$modelAlias]['smFieldName'];
		$sm2Field = $this->settings['ManagerFields'][$modelAlias]['sm2FieldName'];
		$cond = array(
			'AND' => array(
				array(
					'OR' => array(
						"$modelAlias.$smField" => $curUserId,
						"$modelAlias.$sm2Field" => $curUserId,
					),
				),
				//That And this..(another OR)
				array(
					'OR' => array(
						"$modelAlias.user_id" => $searchId,
						"$modelAlias.partner_id" => $searchId,
						"$modelAlias.referer_id" => $searchId,
						"$modelAlias.reseller_id" => $searchId,
					)
				)
			)
		);
		return $cond;
	}
}
