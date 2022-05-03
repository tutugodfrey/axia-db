<?php

App::uses('AppModel', 'Model');
App::uses('ResidualParameterType', 'Model');

class UserCompensationProfile extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'UserCompensationProfile';

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'is_profile_option_1' => array(
			'validateProfileOption' => array(
				'rule' => array('validateProfileOption'),
			),
		),
		'is_profile_option_2' => array(
			'validateProfileOption' => array(
				'rule' => array('validateProfileOption'),
			),
		),
	);

/**
 * belongsTo association
 *
 * @var array $belongsTo
 * @access public
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
		),
		'PartnerUser' => array(
			'className' => 'User',
			'foreignKey' => 'partner_user_id',
		),
	);

/**
 * hasOne association
 *
 * @var array $belongsTo
 * @access public
 */
	public $hasOne = array(
		'ResidualVolumeTier' => array(
			'className' => 'ResidualVolumeTier',
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'ResidualTimeFactor' => array(
			'className' => 'ResidualTimeFactor',
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'WebAchRepCost' => array(
			'className' => 'WebAchRepCost',
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'AddlAmexRepCost' => array(
			'className' => 'AddlAmexRepCost',
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
	);

/**
 * hasMany association
 *
 * @var array $belongsTo
 * @access public
 */
	public $hasMany = array(
		'UserParameter' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'Bet' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'ResidualParameter' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'ResidualTimeParameter' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'CommissionFee' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'AppStatus' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'AttritionRatio' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'EquipmentCost' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'AchRepCost' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'RepMonthlyCost' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'GatewayCostStructure' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'RepProductSetting' => array(
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		),
		'UserCompensationAssociation' => array(
			'className' => 'AssociatedUser',
			'foreignKey' => 'user_compensation_profile_id',
			'dependent' => true
		)
	);

/**
 * getCompData
 * Returns usercompensation profile data for a given compensation profile id
 * To 
 *
 * @param array $id compensation profile id
 * @param array $options query options
 * @param boolean $containAllData true to return all Associated Models data. NOTE: $options param must be empty.
 * @param boolean $isBgProcessCall true when this method is called from a process that was initiated as an offline background job
 * @return array all compensation profile data
 */
	public function getCompData($id, $options = array(), $containAllData = false, $isBgProcessCall = false) {
		$defaultOptions = array(
			'conditions' => array(
					"UserCompensationProfile.id" => $id,
				),
				'contain' => array(
					'User' => array(
						'fields' => array('id', 'fullname', 'user_first_name', 'user_last_name')
					),
					'PartnerUser' => array(
						'fields' => array('id', 'fullname'),
					),
				)
		);

		if (!empty($options)) {
			$options = Hash::merge($defaultOptions, $options);
		} else {
			$fullOptionSet = array(
				'contain' => array(
					'UserCompensationAssociation' => array(
						'UserAssociated' => array(
							'fields' => array('id', 'fullname')),
					),
					'UserParameter' => array(
						'UserCompensationAssociation' => array(
							'UserAssociated' => array(
								'fields' => array('id', 'fullname'))
						),
					),
					'ResidualParameter' => array(
						'UserCompensationAssociation' => array(
							'UserAssociated' => array(
								'fields' => array('id', 'fullname')),
						),
					),
					'ResidualTimeParameter' => array(
						'UserCompensationAssociation' => array(
							'UserAssociated' => array(
								'fields' => array('id', 'fullname'))
						),
					),
					'CommissionFee',
					'ResidualVolumeTier',
					'ResidualTimeFactor',
					'AppStatus',
					'WebAchRepCost',
					'AddlAmexRepCost',
					'AttritionRatio' => array(
						'UserCompensationAssociation' => array(
							'UserAssociated' => array(
								'fields' => array('id', 'fullname'))
						),
					),
				),
			);
			$options = Hash::merge($defaultOptions, $fullOptionSet);
		}
		//This method can be called as an off-line background job process,
		//When that's the case RBAC permissions don't matter since there is no logged in user session.
		if ($isBgProcessCall === false) {
			/*Check permissions*/
			if ($this->rbacIsPermitted('app/actions/UserCompensationProfiles/view/module/SMCompModule')) {
				// No need to modify the $options since primary manager/admin can see all UserAssociated data
			} elseif ($this->rbacIsPermitted('app/actions/UserCompensationProfiles/view/module/SM2CompModule')) {
				$conditions = array('associated_user_id' => CakeSession::read('Auth.User.id'));
				if (array_key_exists('UserCompensationAssociation', $options['contain'])) {
					$options['contain']['UserCompensationAssociation']['conditions'] = $conditions;
				}
				if (array_key_exists('UserParameter', $options['contain'])) {
					$options['contain']['UserParameter']['UserCompensationAssociation']['conditions'] = $conditions;
				}
				if (array_key_exists('ResidualParameter', $options['contain'])) {
					$options['contain']['ResidualParameter']['UserCompensationAssociation']['conditions'] = $conditions;
				}
				if (array_key_exists('ResidualTimeParameter', $options['contain'])) {
					$options['contain']['ResidualTimeParameter']['UserCompensationAssociation']['conditions'] = $conditions;
				}
				if (array_key_exists('AttritionRatio', $options['contain'])) {
					$options['contain']['AttritionRatio']['UserCompensationAssociation']['conditions'] = $conditions;
				}
			} else {//Remove all UserAssociated options for anyone else.
				$options = Hash::remove($options, 'contain.{s}.UserCompensationAssociation');
				$options = Hash::remove($options, 'contain.UserCompensationAssociation');
			}
		}

		$userComp = $this->find('first', $options);

		if ($containAllData === true) {
			// Add the EquipmentCost costs sorted provider_name
			$userComp['EquipmentCost'] = $this->EquipmentCost->getByCompProfile($id, $userComp['PartnerUser']['id']);

			// Add the AchRepCost costs sorted provider_name
			$userComp['AchRepCost'] = $this->AchRepCost->getByCompProfile($id);

			// Add the RepMonthlyCost costs sorted provider_name
			$userComp['RepMonthlyCost'] = $this->RepMonthlyCost->getByCompProfile($id);

			// Add the GatewayCostStructure costs sorted provider_name
			$userComp['GatewayCostStructure'] = $this->GatewayCostStructure->find('byCompProfile', array(
					'conditions' => array('GatewayCostStructure.user_compensation_profile_id' => $id)
				));
			// Add the RepProductSetting costs sorted provider_name
			$userComp['RepProductSetting'] = $this->RepProductSetting->find('byCompProfile', array(
					'conditions' => array('RepProductSetting.user_compensation_profile_id' => $id)
				));
			if (empty($userComp['AttritionRatio'])) {
				$userComp['AttritionRatio'] = $this->AttritionRatio->defaultData(Hash::get($userComp, 'User.id'), Hash::extract($userComp, 'UserCompensationAssociation'));
			}
		}

		return $userComp;
	}

/**
 * getAssociatedCompList method
 * Returns UserCompensationProfile data array with permissions.
 * The permissions are those for the current user attempting to view a user comp profile
 * and are mined in self::_insertAssocCompensationPerms()
 *
 * @param string $userId user id
 * @param bool $isPartnerRep whether user a partner rep
 * @param bool $isManager whether user a manager
 * @return array of user compensation data with permissions to view assiciated users profiles data
 */
	public function getAssociatedCompList($userId, $isPartnerRep, $isManager) {
		$list = array();
		//Get DefaultCompensationProfile regardless of user type based on user_id, contain User.fullname
		$list = $this->User->DefaultCompensationProfile->find('first', array(
			'conditions' => array(
				"DefaultCompensationProfile.user_id" => $userId,
				"DefaultCompensationProfile.is_default" => true
				),
			'contain' => array(
				'User' => array('fields' => array('User.fullname')),
				'UserCompensationAssociation')));
		//Only Current logged in manager can see manager compensation in addition to admins allowed to add new compensation profiles
		if (($isManager && CakeSession::read('Auth.User.id') === $userId && !empty($list['DefaultCompensationProfile']['id'])) ||
			($this->RbacIsPermitted('app/actions/UserCompensationProfiles/addCompProfile'))) {
			$mgrRoleIds = $this->Role->find('list', array(
			'conditions' => array(
				'name' => Configure::read('AssociatedUserRoles.SalesManager.roles')
			),
			'fields' => array('id'),
			));
			$list['MgrCompensation'] = $this->find('all', array(
				'conditions' => array(
					"user_id" => $userId,
					"role_id" => $mgrRoleIds
					),
				'contain' => array(
					'Role.name',
					'User' => array('fields' => array('User.fullname')),
				)
			));
		}
		//if partnerRep is false
		if ($isPartnerRep === false) {
			$conditions = array("$this->alias.partner_user_id" => $userId);
			$contain = array('User' => array('fields' => array('User.fullname')),
							'UserCompensationAssociation');
		} elseif ($isPartnerRep === true) {

			$conditions = array(
				"$this->alias.user_id" => $userId,
				"$this->alias.is_default" => false,
				"role_id IS NULL"
				);
			$contain = array(
				'User' => array('fields' => array('User.fullname')),
				'UserCompensationAssociation',
				'PartnerUser' => array('fields' => array('PartnerUser.fullname')));
		} else {
			return null;
		}
		/* Then append to the results the a list any associated PartnerReps and their Comp Profile data as follows
		  find all comp ids where partner_user_id = $user_id
		  Note: if no partner reps are associated the list should be empty! */
		$list += $this->find('all', array('conditions' => $conditions, 'contain' => $contain));

		$listAndPerms = $this->_insertAssocCompensationPerms($list);
		return $listAndPerms;
	}

/**
 * _insertAssocCompensationPerms
 *
 * Determines permission to display the compensation profile link.
 * Appends to the CompensationProfile array the key => value pair that specifies said determination as isDisplayPermitted => true/false
 *
 * @param array $associatedCompList associated users compensation data
 * @return array $associatedCompList modified with the permissions key => value
 */
	protected function _insertAssocCompensationPerms($associatedCompList = array()) {
		if (!empty($associatedCompList)) {
			$isAllowedManger = $this->_currentUserIsAssociatedAndAllowed(Hash::get($associatedCompList, 'UserCompensationAssociation'));

			//Default Compensation profiles can only be seen by admins, it's respective owners and all managers associated with the compensation profile.
			if (!empty($associatedCompList['DefaultCompensationProfile'])) {
				$associatedCompList['DefaultCompensationProfile']['isDisplayPermitted'] = ($this->RbacIsPermitted('app/actions/UserCompensationProfiles/addCompProfile') || CakeSession::read('Auth.User.id') === $associatedCompList['DefaultCompensationProfile']['user_id'] || $isAllowedManger);
			}

			//The same rules apply to any additional comp profile associations that any single user might have
			$addlCompensations = hash::extract($associatedCompList, '{n}');
			foreach ($addlCompensations as $idx => $compData) {
				$isAllowedManger = $this->_currentUserIsAssociatedAndAllowed($compData['UserCompensationAssociation']);
				$associatedCompList[$idx]['UserCompensationProfile']['isDisplayPermitted'] =
						($this->RbacIsPermitted('app/actions/UserCompensationProfiles/addCompProfile') || CakeSession::read('Auth.User.id') === $associatedCompList[$idx]['UserCompensationProfile']['user_id'] || $isAllowedManger);
			}
		}
		return $associatedCompList;
	}

/**
 * currentUserIsAssociatedAndAllowed
 *
 * Checks if current user is a manager associated with the given compensation profile (which is not their own) to determine whether he/she is allowed to see it
 *
 * @param array $associationList an indexed array containing AssociatedUser(s)
 * @return bool true if is allowed to see the profile.
 */
	protected function _currentUserIsAssociatedAndAllowed($associationList = array()) {
		if (empty($associationList)) {
			return false;
		}

		if (array_key_exists('UserCompensationAssociation', $associationList )) {
			$associationList = $associationList['UserCompensationAssociation'];
		}
		foreach ($associationList as $associatedUser) {
			if ($associatedUser['role'] === Configure::read('AssociatedUserRoles.SalesManager.label') && CakeSession::read('Auth.User.id') === $associatedUser['user_id']) {
				return true;
			}
		}
		return false;
	}

/**
 * Custom validation rule, check if both "is_profile_option_1" and "is_profile_option_2" are true
 *
 * @param type $check param to check
 * @return mixed bool or string
 */
	public function validateProfileOption($check) {
		$field = key($check);
		$value = Hash::get($check, $field);

		$comparisonField = 'is_profile_option_2';
		if ($field == 'is_profile_option_2') {
			$comparisonField = 'is_profile_option_1';
		}
		$comparisonValue = null;
		// if the comparison field is not set to be saved, get the current value from the database
		if (isset($this->data[$this->alias][$comparisonField])) {
			$comparisonValue = $this->data[$this->alias][$comparisonField];
		} elseif (isset($this->data[$this->alias][$this->primaryKey])) {
			$comparisonValue = $this->field($comparisonField, array(
				"{$this->alias}.{$this->primaryKey}" => $this->data[$this->alias][$this->primaryKey]
			));
		}

		if (($value == true) && ($comparisonValue == true)) {
			return __('Option 1 and Option 2 cant be checked at the same time');
		}
		return true;
	}

/**
 * addAssociatedCompData method
 *
 * @param type $id the UserCompensationProfile id
 * @return mixed
 */
	public function createInitialCompData($id) {
		$result = $this->EquipmentCost->generateCompData($id);
		return $result;
	}

/**
 * hasDefault method
 *
 * @param type $userId the user id to check if it has a default compensation profile
 * @return bool
 */
	public function hasDefault($userId) {
		return (bool)$this->find('first', array('recursive' => -1, 'conditions' => array('user_id' => $userId, 'is_default' => true)));
	}

/**
 * getUserOrPartnerUserCompProfile method
 *
 * @param string $userId user_id
 * @param string $partnerId partner_id
 * @return array UserCompensationProfile
 */
	public function getUserOrPartnerUserCompProfile($userId, $partnerId = null) {
		return $this->find('first', [
			'conditions' => $this->_getPartnerConditions($userId, $partnerId),
		]);
	}

/**
 * Return the basic conditions based on user and partner.
 *
 * If there is not partner id, return the default profile.
 * If there is partner id, return the profile asociated with said partner
 *
 * @param string $userId user id
 * @param null|string $partnerId partner id
 * @return array
 */
	protected function _getPartnerConditions($userId, $partnerId = null) {
		$conditions = [
			"{$this->alias}.user_id" => $userId
		];

		if (!empty($partnerId)) {
			$conditions["{$this->alias}.partner_user_id"] = $partnerId;
		} else {
			$conditions["{$this->alias}.is_default"] = true;
		}
		return $conditions;
	}

/**
 * setMgrCompProfileList method
 * Builds a list of role id keys and manager role name as values that represents roles for
 * which the provided user_id is allowed to create additional compensation profiles
 *
 * @param string $userId user id
 * @return array, of role id as the heys and role name as the values
 */
	public function getMgrRoleCompProfileList($userId) {
		if ($this->User->roleIs($userId, Configure::read('AssociatedUserRoles.SalesManager.roles')) === false) {
			return array();
		}

		$smRoles = $this->User->Role->find('list', array(
			'conditions' => array(
				'name' => Configure::read('AssociatedUserRoles.SalesManager.roles')
			),
			'fields' => array('id', 'name'),
			));

		$existing = $this->find('list', array(
			'conditions' => array(
				'user_id' => $userId,
				'is_default' => false,
				'role_id IS NOT NULL'
				),
			'fields' => array('id', 'role_id')
		));

		if (empty($existing)) {
			return $smRoles;
		}

		foreach ($existing as $roleId) {
			unset($smRoles[$roleId]);
		}
		return $smRoles;
	}

/**
 * getUserPercentOfGross method
 *
 * By Default returns the provided $userId's percent of gross when assocManagerId and partnerId are null
 * When ManagerId the manager's percent of gross will be returned.
 * When partnerId is passed the partnerRep percent of gross is returned.
 * When both are passed they will be ignored and the default functionality descrived above is returned.
 *
 * @param string $userId required user id
 * @param string $productsServicesTypeId a product id
 * @param string $merchantAquirerId a Merchant acquirer id
 * @param string $assocManagerId the manager associated with the provided userId
 * @param string $partnerId the partner id associated with the userId
 * @return float percent of gross
 * @throws InvalidArgumentException
 */
	public function getRepOrMgrPctOfGross($userId, $productsServicesTypeId, $merchantAquirerId, $assocManagerId = null, $partnerId = null) {
		if (empty($userId) || empty($productsServicesTypeId)) {
			throw new InvalidArgumentException('Finction argument 1 and 2 cannot be empty');
		}

		if (empty($merchantAquirerId)) {
			return 0;
		}
		$conditions = array(
				'UserCompensationProfile.user_id' => $userId,
		);

		if (!empty($partnerId)) {
			$conditions['UserCompensationProfile.partner_user_id'] = $partnerId;
			$conditions['UserCompensationProfile.is_default'] = false;
		} else {
			$conditions['UserCompensationProfile.is_default'] = true;
		}

		$joinCoinditions = array(
			"UserCompensationProfile.id = UserParameter.user_compensation_profile_id",
			"UserParameter.products_services_type_id = '$productsServicesTypeId'",
			"UserParameter.merchant_acquirer_id = '$merchantAquirerId'",
			"UserParameter.user_parameter_type_id = (select id from user_parameter_types where name = '% of Gross Profit')"
		);

		if (empty($assocManagerId)) {
			$joinCoinditions[] = "UserParameter.associated_user_id = '$userId'"; //user is associated to itself
		} else {
			$joinCoinditions[] = "UserParameter.associated_user_id = '$assocManagerId'";
		}

		$results = $this->find('first', array(
				'conditions' => $conditions,
				'fields' => array(
					'((COALESCE("UserParameter"."value", 0))) As "UserParameter__percent_of_gross"'),
				'joins' => array(
						array(
						'alias' => 'UserParameter',
						'table' => 'user_parameters',
						'type' => 'LEFT',
						'conditions' => $joinCoinditions
						),
					),
				)
			);
		return (is_null(Hash::get($results, "UserParameter.percent_of_gross")))? 0 : $results['UserParameter']['percent_of_gross'];
	}

/**
 * getUserResidualPercent
 *
 * @param string $userId a user id
 * @param string $productId a product id
 *
 * @return array $results
 */
	public function getDefaultResidualPct($userId, $productId) {
		if (empty($userId)) {
			return 0;
		}

		$conditions = array(
			'UserCompensationProfile.user_id' => $userId,
			'UserCompensationProfile.is_default' => true
		);

		$results = $this->find('first',
			array(
				'fields' => array(
					'((COALESCE("ResidualParameter"."value", 0))) As "ResidualParameter__residual_percent"',
				),
				'joins' => array(
					array(
						'alias' => 'ResidualParameter',
						'table' => 'residual_parameters',
						'type' => 'LEFT',
						'conditions' => array(
								"UserCompensationProfile.id = ResidualParameter.user_compensation_profile_id",
								"ResidualParameter.products_services_type_id = '$productId'",
								"ResidualParameter.associated_user_id IS NULL",
								"ResidualParameter.tier = 0",
								"ResidualParameter.residual_parameter_type_id = (select id from residual_parameter_types where name = 'Rep %')"
						)
					),
				),
				'conditions' => $conditions
			)
		);

		return (is_null(Hash::get($results, "ResidualParameter.residual_percent")))? 0 : $results['ResidualParameter']['residual_percent'];
	}

/**
 * setHasManyAssociated()
 * Changes the data structure so that UserCompensationProfile's data associated models having hasMany association can be saved properly
 * Changes from: 
 * ['AssocModel' => [n => ['AssocModel' => [<data array>]]]]
 *
 * To
 * ['AssocModel' => [n =>  [<data array>]]]
 *
 * @param array &$data reference to data to be prepared for a saveAssociated operation with associated models having hasMany association.
 * @return array
 */
	public function setHasManyAssocData(&$data) {
		foreach ($data as $key => $val) {
			if (is_string($key) && is_array($val) && !empty($val)) {
				$assocData = Hash::extract($val, "{n}.$key");
				if (!empty($assocData)) {
					$data[$key] = $assocData;
				}
			}
		}
	}
/**
 * Get profit loss fees
 *
 * @param string $userId user id
 * @param null|string $partnerId partner id
 * @return array
 */
	public function getProfitLossFees($userId, $partnerId = null) {
		$conditions = $this->_getPartnerConditions($userId, $partnerId);
		$conditions[] = 'CommissionFee.associated_user_id IS NULL';
		$commissionFees = $this->find('first', [
			'fields' => [
				'id',
				'CommissionFee.app_fee_profit',
				'CommissionFee.app_fee_loss',
				'CommissionFee.non_app_fee_profit',
				'CommissionFee.non_app_fee_loss'
			],
			'joins' => [
				[
					'alias' => 'CommissionFee',
					'table' => 'commission_fees',
					'type' => 'LEFT',
					'conditions' => '"UserCompensationProfile"."id" = "CommissionFee"."user_compensation_profile_id"',
				]
			],
			'conditions' => $conditions
		]);
		return $commissionFees;
	}

/**
 * getUcpResidualPercent
 *
 * @param string $userId user_id
 * @param int $mInstMonths Number of months since the merchant has been live
 * @param string $partnerId partner_id
 * @param string $productsServicesTypeId products_services_type_id
 * @param string $merchantAquirerId a UserParameter.merchant_acquierer_id
 *
 * @return array $results
 */
	public function getUcpResidualData($userId, $mInstMonths, $partnerId = null, $productsServicesTypeId = null, $merchantAquirerId = null) {
		$conditions = [
			'UserCompensationProfile.user_id' => $userId,
		];

		if (!empty($partnerId)) {
			$conditions['UserCompensationProfile.partner_user_id'] = $partnerId;
		} else {
			$conditions['UserCompensationProfile.is_default'] = true;
		}
		$results = [];
		if (!empty($merchantAquirerId)) {
			$results = $this->find('first', [
				'fields' => [
					'UserCompensationProfile.*',
					'UserParameter.value'
				],
				'joins' => [
					[
						'alias' => 'UserParameter',
						'table' => 'user_parameters',
						'type' => 'LEFT',
						'conditions' => [
							'AND' => [
								['"UserCompensationProfile"."id" = "UserParameter"."user_compensation_profile_id"'],
								['UserParameter.products_services_type_id' => $productsServicesTypeId],
								['UserParameter.associated_user_id' => $userId], // user is associated to itself!
								['UserParameter.merchant_acquirer_id' => $merchantAquirerId],
								["UserParameter.user_parameter_type_id = (select id from user_parameter_types where name = '% of Gross Profit')"]
							]
						]
					]
				],
				'conditions' => $conditions
			]);
		}

		$residualResults = $this->find('first', [
			'fields' => [
				'UserCompensationProfile.*',
				'ResidualParameter.value',
				'ResidualTimeParameter.value'
			],
			'joins' => [
				[
					'alias' => 'ResidualParameter',
					'table' => 'residual_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'AND' => [
							['"UserCompensationProfile"."id" = "ResidualParameter"."user_compensation_profile_id"'],
							['ResidualParameter.products_services_type_id' => $productsServicesTypeId],
							['ResidualParameter.tier' => 0],
							["ResidualParameter.residual_parameter_type_id = (select id from residual_parameter_types where name = 'Rep %')"]
						]
					]
				],
				[
					'alias' => 'ResidualTimeFactor',
					'table' => 'residual_time_factors',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualTimeFactor"."user_compensation_profile_id"',
					]
				],
				[
					'alias' => 'ResidualTimeParameter',
					'table' => 'residual_time_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualTimeParameter"."user_compensation_profile_id"',
						'"ResidualTimeParameter"."tier" = ((CASE WHEN "ResidualTimeFactor"."tier1_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier1_end_month" >= ' . $mInstMonths . ' THEN 1 WHEN "ResidualTimeFactor"."tier2_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier2_end_month" >= ' . $mInstMonths . ' THEN 2 WHEN "ResidualTimeFactor"."tier3_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier3_end_month" >= ' . $mInstMonths . ' THEN 3 ELSE 4 END))',
						'ResidualTimeParameter.products_services_type_id' => $productsServicesTypeId,
						"ResidualTimeParameter.residual_parameter_type_id = (select id from residual_parameter_types where name = 'Rep %')"
					]
				]
			],
			'conditions' => $conditions
		]);

		if (empty($results['UserParameter']['value'])) {
			$results['UserParameter']['value'] = 0;
		}

		$results['UserParameter']['percent_of_gross_profit'] = $results['UserParameter']['value'];
		unset($results['UserParameter']['value']);

		if (empty($residualResults['ResidualParameter']['value'])) {
			$residualResults['ResidualParameter']['value'] = 0;
		}

		if ($residualResults['UserCompensationProfile']['is_profile_option_1']) {
			$results['ResidualParameter']['residual_percent'] = $residualResults['ResidualParameter']['value'];
		} elseif ($residualResults['UserCompensationProfile']['is_profile_option_2']) {
			$results['ResidualParameter']['residual_percent'] = $residualResults['ResidualTimeParameter']['value'];
		}

		return $results;
	}

/**
 * getMultiple
 * Returns the multiple in the rep/partner rep UCP
 * If no manager id parameter is passed the rep multiple will be returned, otherwise the manager's multiple of the manager associated with the rep will be returned.
 * NOTICE:
 * Always check manager id parameter value before calling this function.
 * Passing an empty $assocManagerId variable will be interpreted as no manager and therefore the rep multiple will be returned when the manager's was expected.
 *
 * @param string $userId user_id
 * @param string $partnerId a partner user id
 * @param string $productsServicesTypeId products_services_type_id
 * @param integer $mInstMonths Number of months since the merchant has been live
 * @param string $assocManagerId the manager associated with the userId
 * @return float the value set as multiple in percentage form
 */
	public function getMultiple($userId, $partnerId, $productsServicesTypeId, $mInstMonths, $assocManagerId = null) {
		$conditions = [
			'UserCompensationProfile.user_id' => $userId,
			'UserCompensationProfile.is_default' => true,
		];
		if (is_null($mInstMonths)) {
			$mInstMonths = 0;
		}
		if (!empty($partnerId)) {
			$conditions['UserCompensationProfile.partner_user_id'] = $partnerId;
			$conditions['UserCompensationProfile.is_default'] = false;
		}

		if (!empty($assocManagerId)) {
			$paramTypeId = ResidualParameterType::MGR_MULTIPLE;
		} else {
			$paramTypeId = ResidualParameterType::R_MULTIPLE;
		}

		$data = $this->User->UserCompensationProfile->find('first', [
			'fields' => [
				'UserCompensationProfile.is_profile_option_1',
				'UserCompensationProfile.is_profile_option_2',
				'ResidualParameter.value',
				'ResidualTimeParameter.value'
			],
			'conditions' => $conditions,
			'joins' => [
				[
					'alias' => 'ResidualParameter',
					'table' => 'residual_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualParameter"."user_compensation_profile_id"',
						"ResidualParameter.products_services_type_id = '$productsServicesTypeId'",
						'ResidualParameter.tier = 0',
						'ResidualParameter.associated_user_id' => $assocManagerId,
						'ResidualParameter.is_multiple = 1',
						"ResidualParameter.residual_parameter_type_id" => $paramTypeId,
					]
				],
				[
					'alias' => 'ResidualTimeFactor',
					'table' => 'residual_time_factors',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualTimeFactor"."user_compensation_profile_id"',
					]
				],
				[
					'alias' => 'ResidualTimeParameter',
					'table' => 'residual_time_parameters',
					'type' => 'LEFT',
					'conditions' => [
						'"UserCompensationProfile"."id" = "ResidualTimeParameter"."user_compensation_profile_id"',
						'"ResidualTimeParameter"."tier" = ((CASE WHEN "ResidualTimeFactor"."tier1_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier1_end_month" >= ' . $mInstMonths . ' THEN 1 WHEN "ResidualTimeFactor"."tier2_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier2_end_month" >= ' . $mInstMonths . ' THEN 2 WHEN "ResidualTimeFactor"."tier3_begin_month" <=' . $mInstMonths . ' AND "ResidualTimeFactor"."tier3_end_month" >= ' . $mInstMonths . ' THEN 3 ELSE 4 END))',
						'ResidualTimeParameter.is_multiple = 1',
						'ResidualTimeParameter.products_services_type_id' => $productsServicesTypeId,
						"ResidualTimeParameter.associated_user_id" => $assocManagerId,
						"ResidualTimeParameter.residual_parameter_type_id" => $paramTypeId
					]
				]
			],
		]);

		if ($data['UserCompensationProfile']['is_profile_option_1']) {
			$multiple = $data['ResidualParameter']['value'];
		} elseif ($data['UserCompensationProfile']['is_profile_option_2']) {
			$multiple = $data['ResidualTimeParameter']['value'];
		}
		return (isset($multiple))? $multiple: 0;
	}

/**
 * buildUcpList
 * Builds a list of user compensation information
 * The list includes data about all compensation profiles from $sourceUserId that $targetUserId does not already have.
 * For example if $targetUserId already has a default or main UCP then it will be excluded and the same will apply with any other UCP types. 
 *
 * @param string $sourceUserId a user id from which to build the list
 * @param string $targetUserId a user id for whom the list will be build
 * @return array containing Compensation profile ids as keys and a description of the type of UCP as the value 
 */
	public function buildUcpList($sourceUserId, $targetUserId) {
		$ucpList = [];
		//if the source user has no compensations we can't do anything
		if ($this->hasAny(['user_id' => $sourceUserId])) {
			$targetIsPartnerRep = $this->User->roleIs($targetUserId, User::ROLE_PARTNER_REP);
			$targetIsManager = $this->User->roleIs($targetUserId, [User::ROLE_SM, User::ROLE_SM2]);
			$targetUcps = $this->find('all', ['conditions' => ['user_id' => $targetUserId]]);
			//conditions to retreive source user's UCPs
			$conditions = [
				'user_id' => $sourceUserId
			];
			$mgrRoles = $this->User->Role->find('list', [
				'fields' => ['Role.id', 'Role.name'],
				'conditions' => ['name' => [User::ROLE_SM, User::ROLE_SM2]],
			]);
			if (!empty($targetUcps)) {
				foreach ($targetUcps as $tUcpData) {
					//Exclude default UCP if exists
					if ($tUcpData['UserCompensationProfile']['is_default']) {
						$conditions['is_default'] = false;
					}

					//Exclude any existing Manager UCPs
					if (!empty($tUcpData['UserCompensationProfile']['role_id'])) {
						if (in_array($tUcpData['UserCompensationProfile']['role_id'], array_keys($mgrRoles))) {
							$conditions['AND']['OR'] = [
								'role_id NOT IN' => array_keys($mgrRoles),
								'role_id IS NULL'
							];
						}
					}
				}
			}
			$sourceUcps = $this->find('all', ['conditions' => $conditions]);

			//Result in a neat consistent order
			$orderedList = [
				'default' => [],
				'mgrs' => [],
				'partner_rep' => []
			];

			foreach ($sourceUcps as $sUcpData) {
				if ($sUcpData['UserCompensationProfile']['is_default']) {
					$orderedList['default'][$sUcpData['UserCompensationProfile']['id']] = 'Default UCP';
				}
				//Only include SM UCP's iff the target user is a manager
				if (!empty($sUcpData['UserCompensationProfile']['role_id']) && $targetIsManager) {
					$orderedList['mgrs'][$sUcpData['UserCompensationProfile']['id']] = Hash::get($mgrRoles, $sUcpData['UserCompensationProfile']['role_id']) . ' UCP';
				}
				//Add partner rep copensation profiles iff the target is a PartnerRep
				if ($targetIsPartnerRep && !empty($sUcpData['UserCompensationProfile']['partner_user_id'])) {
					$partnerName = $this->User->field('fullname', ['id' => $sUcpData['UserCompensationProfile']['partner_user_id']]);
					$orderedList['partner_rep'][$sUcpData['UserCompensationProfile']['id']] = "Partner-Rep UCP associated with $partnerName";
				}
			}
			//sort partner rep list
			asort($orderedList['partner_rep']);
			$ucpList = array_merge($ucpList, $orderedList['default'], $orderedList['mgrs'], $orderedList['partner_rep']);
		}

		return $ucpList;
	}

/**
 * getUsersWithComps
 * Returns a list of users that already have compensation profiles with id as the key and full name as the value
 * If a user id parameter is passed the list will only include users whose roles match that of the passed user id param
 * 
 * @param string $userId an optional user id
 * @return array
 */
	public function getUsersWithComps($userId = '', $activeUsers = false) {
		$roles = $this->User->getUserRolesList($userId);

		$settings = [
			'fields' => [
				'User.id',
				'((' . $this->User->getFullNameVirtualField('User') . ')) AS "User__name"',
			],
			'joins' => [
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => [
						"{$this->alias}.user_id = User.id",
					]
				]
			],
			'order' => ['User.user_first_name ASC'],
			'group' => ['User.id']
		];
		if ($activeUsers) {
			$settings['conditions'][] = 'User.active = 1';
		}
		if (!empty($roles)) {
			$settings['joins'][] = [
				'table' => 'users_roles',
				'alias' => 'UserRole',
				'type' => 'INNER',
				'conditions' => [
					"UserRole.user_id = User.id",
					"UserRole.role_id" => $roles
				]
			];
		}
		//observation: Using 'all' as findType instead of 'list' since
		//concatenated aliased field User.name does not work with list type
		$list = $this->find('all', $settings);

		return Hash::combine($list, '{n}.User.id', '{n}.User.name');
	}
/**
 * copyMany
 * Creates copies of each passed user compesnation profile id for the target user.
 * This process can take very long and may be processed in the back-end using a worker when many source UCP ids are passed.
 *
 * @param string $targetUserId the id of the user for whom the new copy is being created
 * @param array $sourceUcpIds single dimentional array of UserCompensationProfile.id's to create copies from 
 * @param string $newPartnerIdForNewCopy optional parameter to specify the id of a partner with wich to associate all the copies that will be created
 * @param boolean $processInBackGround true when this method is called from process was initiated as an offline background job
 * @return boolean true on success false on failure
 * @throws Exception
 */
	public function copyMany($targetUserId, $sourceUcpIds, $newPartnerIdForNewCopy = null, $processInBackGround = false) {
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		foreach ($sourceUcpIds as $sourceId) {
			$sourceTmp = $this->getCompData($sourceId, [], true, $processInBackGround);
			$targetHasSameUcp = $this->hasAny([
				'user_id' => $targetUserId,
				'is_default' => $sourceTmp['UserCompensationProfile']['is_default'],
				'partner_user_id' => (empty($newPartnerIdForNewCopy))? $sourceTmp['UserCompensationProfile']['partner_user_id'] : $newPartnerIdForNewCopy,
				'role_id' => $sourceTmp['UserCompensationProfile']['role_id'],
				]
			);
			if ($targetHasSameUcp) {
				$dataSource->rollback();
				throw new Exception("Target user alreay has a UCP that matches one of the UCPs to be copied with id = '$sourceId'" );
			}
			//Get source UCP Bets data since it's not returned in previous method
			$sourceTmp['Bet'] = $this->Bet->getUserBets($sourceId);
			$selectedUserId = $sourceTmp['UserCompensationProfile']['user_id'];

			//If creating a manager copy remove any unnessesary data
			if (!empty($sourceTmp['UserCompensationProfile']['role_id'])) {
				$mgrRoles = $this->User->Role->find('list', [
					'fields' => ['Role.id', 'Role.name'],
					'conditions' => ['name' => [User::ROLE_SM, User::ROLE_SM2]],
				]);
				if (in_array($sourceTmp['UserCompensationProfile']['role_id'], array_keys($mgrRoles))) {
					unset($sourceTmp['UserParameter']);
					unset($sourceTmp['ResidualParameter']);
					unset($sourceTmp['ResidualVolumeTier']);
					unset($sourceTmp['ResidualTimeFactor']);
					unset($sourceTmp['ResidualTimeParameter']);
				}
			}
			$associatedUsers = $sourceTmp['UserCompensationAssociation'];
			foreach ($associatedUsers as $key => $val) {
				unset($associatedUsers[$key]['id']);
				unset($associatedUsers[$key]['user_compensation_profile_id']);
				unset($associatedUsers[$key]['UserAssociated']);
				$associatedUsers[$key]['user_id'] = $targetUserId;
			}
			unset($sourceTmp['User']);
			unset($sourceTmp['PartnerUser']);
			$source = $this->__modifyUserCompensationProfileData($sourceTmp, $selectedUserId, $targetUserId);
			unset($sourceTmp);

			$isDefault = $source['UserCompensationProfile']['is_default'];
			//Remove empty values
			$source = Hash::filter($source);
			if (!empty($newPartnerIdForNewCopy) && !$isDefault) {
				$source['UserCompensationProfile']['partner_user_id'] = $newPartnerIdForNewCopy;
			}
			//Re-insert any lost boolean false values removed by filter
			$source['UserCompensationProfile']['is_default'] = $isDefault;
			$source['UserCompensationProfile']['is_partner_rep'] = !empty($source['UserCompensationProfile']['partner_user_id']);
			foreach ($associatedUsers as $associatedUser) {
				$source['UserCompensationAssociation'][] = $associatedUser;
			}

			$this->setHasManyAssocData($source);
			$result = $this->saveAssociated($source, array('validate' => false));
			if (!$result) {
				$dataSource->rollback();
				return false;
			}

		}
		$dataSource->commit();
		return true;
	}

/**
 * __modifyUserCompensationProfileData
 *
 * @param array &$array source data
 * @param string $sourceUserId source user_id
 * @param string $targetUserId target user_id
 *
 * @return array modified data
 */
	private function __modifyUserCompensationProfileData(&$array, $sourceUserId, $targetUserId) {
		foreach ($array as $key => $arr) {
			if ((!is_numeric($key)) && ($key == 'id' || $key == 'user_compensation_profile_id' || $key == 'EquipmentItem' ||
				$key == 'UserCompensationAssociation' || $key == 'AchProvider' || $key == 'BetNetwork' || $key == 'AttritionRatio')) {
				// don't do anything
			} else {
				if (!empty($arr)) {
					if (is_array($arr)) {
						$res[$key] = $this->__modifyUserCompensationProfileData($arr, $sourceUserId, $targetUserId);
					} else {
						if ($key == 'user_id') {
							$res[$key] = $targetUserId;
						} elseif ($key == 'associated_user_id') {
							if ($arr == $sourceUserId) {
								$res[$key] = $targetUserId;
							} else {
								$res[$key] = $arr;
							}
						} else {
							$res[$key] = $arr;
						}
					}
				} else {
					$res[$key] = $arr;
				}
			}
		}

		return $res;
	}

/**
 * sendCompletionStatusEmail
 *
 * @param array $response job status info
 * @param string $email email address
 *
 * @return void
 */
	public function sendCompletionStatusEmail($response, $email) {
		$event = new CakeEvent('App.Model.readyForEmail', $this, [
			'template' => Configure::read('App.bgJobEmailTemplate'),
			'from' => Configure::read('App.defaultSender'),
			'to' => $email,
			'subject' => 'User Compensation Profile Process Completion Notice',
			'emailBody' => $response
		]);

		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
	}

/**
 * getListGroupedByUser
 * Returns a comprehensive list of user compensation profiles that belong to the given user ids
 * The data structure will be grouped by user with the user name as the Key at the first dimmension and an
 * array containing all of the users comp profiles as the value.
 * i.e:
 * [
 *  'Full Name' => [UUID => Default UCP , UUID => PartnerRep UCP (Partner Name), UUID => SM UCP, UUID => SM2 UCP]
 * ]
 *
 * @param array $userIds
 * @return array
 */
	public function getListGroupedByUser($userIds) {
		$ucpList = $this->find('all', [
			'conditions' => [
				"{$this->alias}.user_id" => $userIds
			],
			'fields' => [
				"{$this->alias}.id",
				'((' . $this->User->getFullNameVirtualField('User') . ')) AS "'. $this->alias . '__user_name"',
				"((CASE when \"{$this->alias}\".\"is_default\" = true THEN 'Default Rep UCP' ELSE (CASE WHEN \"{$this->alias}\".\"partner_user_id\" IS NOT NULL THEN 'PartnerRep UCP (' || \"Partner\".\"user_first_name\" || ' ' || \"Partner\".\"user_last_name\" || ')' ELSE \"Role\".\"name\" || ' UCP'  END) END)) AS \"{$this->alias}__type\""
			],
			'joins' => [
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => [
						"{$this->alias}.user_id = User.id"
					],
				],
				[
					'table' => 'users',
					'alias' => 'Partner',
					'type' => 'left',
					'conditions' => [
						"{$this->alias}.partner_user_id = Partner.id"
					],
				],
				[
					'table' => 'roles',
					'alias' => 'Role',
					'type' => 'left',
					'conditions' => [
						"{$this->alias}.role_id = Role.id"
					],
				],
			],
			'order' => ['"'. $this->alias . '__user_name" ASC', "{$this->alias}.is_default DESC"]
		]);
		return Hash::combine($ucpList, '{n}.UserCompensationProfile.id', '{n}.UserCompensationProfile.type', '{n}.UserCompensationProfile.user_name');
	}

/**
 * getAssociatedPartners
 * Returns a comprehensive list of partners that are associated with each user id passed.
 * The data structure will be the partner user id as the key and the name as the value
 *
 * @param mixed array|string $userIds a string of a list of user ids of the users whom may or may not have a partner-rep UCP
 * @return array
 */
	public function getAssociatedPartners($userIds) {
		if (empty($userIds)) {
			return [];
		}
		$data = $this->find('all', [
			'fields' => [
				'User.id',
				'((' . $this->User->getFullNameVirtualField('User') . ')) AS "User__full_name"'
			],
			'conditions' => [
				$this->alias . '.user_id' => $userIds,
				$this->alias . '.partner_user_id IS NOT NULL',
			],
			'joins' => [
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => [
						$this->alias . '.partner_user_id = User.id'
					],
				]
			],
			'order' => ['User.user_first_name'],
		]);
		return array_map('trim', Hash::combine($data, '{n}.User.id', '{n}.User.full_name'));
	}
}
