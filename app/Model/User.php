<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
App::uses('ModernPasswordHasher', 'Shim.Controller/Component/Auth');
App::uses('DigestAuthenticate', 'Controller/Component/Auth');

/**
 * User Model
 */
class User extends AppModel {

/**
 * Prefix for complex user select input values
 *
 * @var string
 */
	const PREFIX_ALL = 'select_all';
	const PREFIX_ENTITY = 'entity_id';
	const PREFIX_PARENT = 'parent_user_id';
	const PREFIX_USER = 'user_id';

/**
 * User roles
 *
 * @var string
 */
	const ROLE_ADMIN_I = 'Admin I';
	const ROLE_ADMIN_II = 'Admin II';
	const ROLE_ADMIN_III = 'Admin III';
	const ROLE_ADMIN_IV = 'Admin IV';
	const ROLE_DEPLOY_ADMIN = 'Deployment Admin';
	const ROLE_SM = 'SM';
	const ROLE_SM2 = 'SM2';
	const ROLE_ACCT_MGR = 'Account Manager';
	const ROLE_INSTALLER = 'Installer';
	const ROLE_PARTNER_REP = 'PartnerRep';
	const ROLE_REP = 'Rep';
	const ROLE_RESELLER = 'Reseller';
	const ROLE_REFERRER = 'Referrer';
	const ROLE_PARTNER = 'Partner';
	const ROLE_API = 'API Consumer';

/**
 * User role groups
 *
 * @var string
 */
	const ROLE_GROUP_ADMIN = 'Admin';
	const ROLE_GROUP_SM = 'SalesManager';
	const ROLE_GROUP_MGR1 = '1stManager';
	const ROLE_GROUP_MGR2 = '2ndManager';
	const ROLE_GROUP_INSTALLER = 'Installer';
	const ROLE_GROUP_PARTNER_REP = 'PartnerRep';
	const ROLE_GROUP_REP = 'Rep';
	const ROLE_GROUP_RESELLER = 'Reseller';
	const ROLE_GROUP_REFERRER = 'Referrer';
	const ROLE_GROUP_PARTNER = 'Partner';

/**
 * User maxximum number of failed log in attempts
 *
 * @var string
 */
	const MAX_LOG_IN_ATTEMPTS = 6;

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'fullname';

/**
 * actsAs
 * @var array
 */
	public $actsAs = array(
		'Rbac.Rbac',
		'Search.Searchable',
	);

/**
 * useRoles
 * @var array
 */
	public $userRoles = array(
		'roles' => array(
			self::ROLE_GROUP_ADMIN => array(
				'label' => 'Admin',
				'roles' => array(
					self::ROLE_ADMIN_I,
					self::ROLE_ADMIN_II,
					self::ROLE_ADMIN_III,
					self::ROLE_ADMIN_IV,
					self::ROLE_DEPLOY_ADMIN,
				)
			),
			self::ROLE_GROUP_SM => array(
				'label' => 'Sales Manager',
				'roles' => array(
					self::ROLE_SM,
					self::ROLE_SM2,
				)
			),
			self::ROLE_GROUP_MGR1 => array(
				'label' => 'Sales Manager',
				'roles' => array(
					self::ROLE_SM,
				)
			),
			self::ROLE_GROUP_MGR2 => array(
				'label' => 'Sales Manager',
				'roles' => array(
					self::ROLE_SM2,
				)
			),
			self::ROLE_GROUP_INSTALLER => array(
				'label' => 'Installer',
				'roles' => array(
					self::ROLE_INSTALLER,
				)
			),
			self::ROLE_GROUP_PARTNER_REP => array(
				'label' => 'Partner Rep',
				'roles' => array(
					self::ROLE_PARTNER_REP,
				)
			),
			self::ROLE_GROUP_REP => array(
				'label' => 'Rep',
				'roles' => array(
					self::ROLE_REP,
				)
			),
			self::ROLE_GROUP_RESELLER => array(
				'label' => 'Reseller',
				'roles' => array(
					self::ROLE_RESELLER,
				)
			),
			self::ROLE_GROUP_REFERRER => array(
				'label' => 'Referrer',
				'roles' => array(
					self::ROLE_REFERRER,
				)
			),
			self::ROLE_GROUP_PARTNER => array(
				'label' => 'Partner',
				'roles' => array(
					self::ROLE_PARTNER,
				)
			)
		),
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'A username is required!',
				'required' => true,
				'allowEmpty' => false,
			),
			'required2' => array(
				'rule' => array('isUnique'),
				'message' => 'username already exists!',
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'user_first_name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'First Name is required!',
				'required' => true,
				'allowEmpty' => false,
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'user_last_name' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'user_email' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Email is required!',
				'required' => true,
				'allowEmpty' => false,
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'entity_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Entity is required!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'password' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Password is required!',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create',
			),
			'matches' => array(
				'rule' => array('validateFieldsEqual', 'password', 'repeat_password'),
				'message' => 'Passwords do not match!',
				'allowEmpty' => true,
			)
		),
		'repeat_password' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Repeat Password is required!',
				'required' => true,
				'last' => false,
				'allowEmpty' => false,
				'on' => 'create',
			),
			'matches' => array(
				'rule' => array('validateFieldsEqual', 'password', 'repeat_password'),
				'message' => 'Passwords do not match!',
				'last' => false,
				'allowEmpty' => true,
			),
			'minLength' => array(
				'rule' => array('minLength', 8),
				'message' => 'Passwords should have 8 characters or more',
				'last' => false,
			),
		),
		'split_commissions' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'bet_extra_pct' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'discover_bet_extra_pct' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'Role' => array(
			'canHaveManyRoles' => array(
				'rule' => array('canHaveManyRoles'),
			),
		),
		'user_phone' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'user_fax' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'bank_name' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'routing_number' => array(
			'input_has_only_valid_chars' => array(
				'rule' => 'numeric',
				'message' => 'Enter only numbers.',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'account_number' => array(
			'input_has_only_valid_chars' => array(
				'rule' => 'numeric',
				'message' => 'Enter only numbers.',
				'required' => false,
				'allowEmpty' => true,
			)
		),
	);
public function canHaveManyRoles($check) {
		if(count($check['Role']) <= 1) {
			return true;
		}
		$isApiUserRoleInList = $this->Role->hasAny([
			'id' => $check['Role'],
			"name LIKE 'API%'"
		]);
		if ($isApiUserRoleInList) {
			return 'Assigning multiple roles is not allowed when the API role is selected!';
		}
		
		return true;
	}

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ResidualOption' => array(
			'className' => 'UserResidualOption',
			'foreignKey' => 'user_residual_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Entity' => array(
			'className' => 'Entity',
			'foreignKey' => 'entity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'RefererOptions' => array(
			'className' => 'Referer',
			'foreignKey' => 'referer_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ResellerOptions' => array(
			'className' => 'Referer',
			'foreignKey' => 'referer_id',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'ResidualTimeFactor' => array(
			'className' => 'ResidualTimeFactor',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ResidualProductControl' => array(
			'className' => 'ResidualProductControl',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'RepCostStructure' => array(
			'className' => 'RepCostStructure',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ResidualVolumeTier',
		'DefaultCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_id',
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UserCostsArchive' => array(
			'className' => 'UserCostsArchive',
			'foreignKey' => 'user_id'
		),
		'UsersProductsRisk' => array(
			'className' => 'UsersProductsRisk',
			'foreignKey' => 'user_id'
		),
		'UsersRole' => array(
			'className' => 'Rbac.UsersRole',
			'foreignKey' => 'user_id'
		),
		'PermissionCache' => array(
			'className' => 'Rbac.PermissionCache',
			'foreignKey' => 'user_id'
		),
		'PermissionConstraint' => array(
			'className' => 'Rbac.PermissionConstraint',
			'foreignKey' => 'user_id'
		),
		'AssociatedUser' => array(
			'dependent' => true,
		),
		'UserParameter' => array(
			'dependent' => true,
		),
		'ResidualParameter' => array(
			'dependent' => true,
		),
		'ResidualTimeParameter' => array(
			'dependent' => true,
		),
		'SalesGoal' => array(
			'dependent' => true,
			'order' => array('SalesGoal.goal_month' => 'asc'),
		),
		'AppStatus' => array(
			'dependent' => true,
		),
		'Bet' => array(
			'dependent' => true,
		),
		'CommissionFee' => array(
			'dependent' => true,
		),
		'EquipmentCost' => array(
			'dependent' => true,
		),
		'AttritionRatio' => array(
			'dependent' => true,
		),
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_id',
		),
		'SystemTransaction' => array(
			'className' => 'SystemTransaction',
			'foreignKey' => 'user_id'
		),
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Role' => array(
			'className' => 'Rbac.Role',
			'joinTable' => 'users_roles',
			'with' => 'Rbac.UsersRole',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'role_id',
			'unique' => 'keepExisting',
		),
	);

/**
 * Custom finders
 *
 * @var array
 */
	public $findMethods = array(
		'entityManagerUsersList' => true,
	);

/**
 * Override virtual fields
 *
 * @param type $id id of the an user
 * @param type $table database table
 * @param type $ds options
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->virtualFields = array(
			'fullname' => $this->getFullNameVirtualField($this->alias),
		);
	}

/**
 * Return the sql for the full name virtual field
 *
 * @param type $modelAlias Model alias to use on the query
 * @return string
 */
	public function getFullNameVirtualField($modelAlias) {
		return "trim(BOTH FROM \"{$modelAlias}\".\"user_first_name\" || ' ' || \"{$modelAlias}\".\"user_last_name\")";
	}

/**
 * Password hashing for User -> add()
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['password'])) {
			$PasswordHasher = $this->_getPasswordHasher();
			$this->data[$this->alias]['password'] = $PasswordHasher->hash($this->data[$this->alias]['password']);
			//update expidation date whenever new password is saved
			if (!isset($this->data[$this->alias]['pw_expiry_date'])) {
				$this->data[$this->alias]['pw_expiry_date'] = $this->newPwExpiration();
			}
		} else {
			unset($this->data[$this->alias]['password']);
			unset($this->data['User']['repeat_password']);
		}
		//Did User.womply_user_enabled status change?
		$this->_setWomplyStatusChange($this->data);
		//Remove traling spaces in some fields
		if (!empty($this->data[$this->alias]['user_first_name'])) {
			$this->data[$this->alias]['user_first_name'] = trim($this->data[$this->alias]['user_first_name']);
		}
		if (!empty($this->data[$this->alias]['user_last_name'])) {
			$this->data[$this->alias]['user_last_name'] = trim($this->data[$this->alias]['user_last_name']);
		}
		if (!empty($this->data[$this->alias]['username'])) {
			$this->data[$this->alias]['username'] = trim($this->data[$this->alias]['username']);
		}
		if (!empty($this->data[$this->alias]['user_email'])) {
			$this->data[$this->alias]['user_email'] = trim($this->data[$this->alias]['user_email']);
		}
		if (!empty($this->data[$this->alias]['initials'])) {
			$this->data[$this->alias]['initials'] = trim($this->data[$this->alias]['initials']);
		}

		if (!empty($this->data[$this->alias]['account_number']) && $this->isEncrypted($this->data[$this->alias]['account_number']) == false) {
			$this->data[$this->alias]['account_number'] = $this->encrypt(trim($this->data[$this->alias]['account_number']), Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($this->data[$this->alias]['routing_number']) && $this->isEncrypted($this->data[$this->alias]['routing_number']) == false) {
			$this->data[$this->alias]['routing_number'] = $this->encrypt(trim($this->data[$this->alias]['routing_number']), Configure::read('Security.OpenSSL.key'));
		}
		return parent::beforeSave($options);
	}

/**
 * generateRandPw
 * Generates a cryptographycally secure pseudo-random password
 *
 * @return string
 */
	public function generateRandPw() {
		$factory = new RandomLib\Factory;
		$generator = $factory->getMediumStrengthGenerator();
		$intList = $generator->generateInt(1000, 99999);
		$alphaList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$unreservedSymbolList = '._+-~';
		$chars = $alphaList . $intList . $unreservedSymbolList;
		
		return $generator->generateString(32, $chars);
	}

/**
 * pwIsDifferent
 * Checks whether the password submitted is different from users current password
 *
 * @param string $id User id
 * @param string $pw a password to compare against the users current password
 * @return bool
 */
	public function pwIsValid($id, $pw) {
		$PasswordHasher = $this->_getPasswordHasher();
		$pw = $PasswordHasher->hash($pw);
		return $this->hasAny(['id' => $id, 'password' => $pw]);
	}

/**
 * setPwFieldsValidation
 * Sets validation rules that apply to the password reset/renewal UI fields
 *
 * @param boolean $validateCurPass whether to add validation for current password
 * @return void
 */
	public function setPwFieldsValidation($validateCurPass = false) {
		//remove all rules since they are not relevant for password resetting
		foreach ($this->validate as $field => $rules) {
			$this->validator()->remove($field);
		}
		$this->validator()->add('password', [
				'required' => [
					'rule' => ['notBlank'],
					'message' => 'Password is required!',
					'required' => true,
					'allowEmpty' => false,
				],
				'matches' => [
					'rule' => ['validateFieldsEqual', 'password', 'repeat_password'],
					'message' => 'Passwords do not match!',
					'allowEmpty' => true,
				]
		])->add('repeat_password', [
				'required' => [
					'rule' => ['notBlank'],
					'message' => 'Repeat Password is required!',
					'required' => true,
					'last' => false,
					'allowEmpty' => false,
				],
				'matches' => [
					'rule' => ['validateFieldsEqual', 'password', 'repeat_password'],
					'message' => 'Passwords do not match!',
					'last' => false,
					'allowEmpty' => true,
				],
				'minLength' => [
					'rule' => ['minLength', 8],
					'message' => 'Passwords should have 8 characters or more',
					'last' => false,
				],
			]
		);
		if ( $validateCurPass) {
			$this->validator()->add('cur_password', [
					'required' => [
						'rule' => ['notBlank'],
						'message' => 'Password is required!',
						'required' => true,
						'allowEmpty' => false,
					]
			]);
		}
	}

/**
 * _setWomplyStatusChange
 * Determines whether the User.womply_user_enabled has changed from value stored in the DB 
 * and if it has, adds an element in the data to indicate so. This indicator can be used in the afterSave to perform other requred tasks.
 *
 * @param array &$data memory reference to the data sumitted from client side form
 * @return bool
 */
	protected function _setWomplyStatusChange(&$data) {
		if (!empty($data[$this->alias]['id']) && $this->roleIs($data[$this->alias]['id'], $this->userRoles['roles'][self::ROLE_GROUP_ADMIN]['roles']) === false) {
			$curWompStat = $this->field('womply_user_enabled', array('User.id' => $data[$this->alias]['id']));

			if (array_key_exists('womply_user_enabled', $data[$this->alias]) && $curWompStat !== Hash::get($data, $this->alias . ".womply_user_enabled")) {
				$data[$this->alias]['womply_status_changed'] = true;
			}
		}
	}
/**
 * After save callback
 *
 * @param bool $created true if the record is being created
 * @param array $options method options
 * @return bool
 */
	public function afterSave($created, $options = array()) {
		if (!$created) {
			$userId = (Hash::get($this->data, 'User.id'))? : $this->id;
			if (!empty($userId)) {
				$this->PermissionCache->deleteAll(['user_id' => $userId]);
			}

			// Check if Womply enabled status was changed
			if (array_key_exists('womply_status_changed', $this->data['User']) && $this->data[$this->alias]['womply_status_changed'] === true) {
				ClassRegistry::init('Merchant')->updateUserMerchantsWomply($this->data['User']['id'], $this->data['User']['womply_user_enabled']);
			}
		}
	}

/**
 * Return the password Hasher
 *
 * @return Object
 */
	protected function _getPasswordHasher() {
		return new ModernPasswordHasher(array(
			'salt' => Configure::read('Security.salt'),
		));
	}

/**
 * return the number of days until a user password expires
 *
 * @param string $id User id
 * @return bool
 */
	public function getDaysTillPwExpires($id) {
		$pwExpDate = $this->field('pw_expiry_date', ['id' => $id]);
		$now = date_create(date('Y-m-d'));
		$pwExpDate = date_create($pwExpDate);
		$diff = date_diff($now, $pwExpDate);
		return (int)$diff->format("%R%a");
	}

/**
 * Check if the user password need to be rehashed with the new system
 *
 * @param string $id User id
 * @return bool
 */
	public function needsPasswordRehash($id) {
		$PasswordHasher = $this->_getPasswordHasher();
		if (!method_exists($PasswordHasher, 'needsRehash')) {
			return false;
		}
		$hash = $this->field('password', array("{$this->alias}.{$this->primaryKey}" => $id));
		return $PasswordHasher->needsRehash($hash);
	}

/**
 * Update the user password with the new hasher
 *
 * @param string $id User id
 * @param string $password User password in plain text
 * @throws RuntimeException
 * @return void
 */
	public function rehashPassword($id, $password) {
		$data = array(
			$this->alias => array(
				'id' => $id,
				'password' => $password,
			)
		);
		// The hash is done at "User::beforeSave()"
		$this->create();
		//Second-field password-matching validation not needed on rehashing.
		$this->validator()->remove('password', 'matches');
		if (!$this->save($data, true, array('id', 'password'))) {
			throw new RuntimeException(__('Unable to rehash the password'));
		}
	}

/**
 * Gets all users with a given role
 *
 * @param string $role role name
 * @param string $order ASC or DESC
 * @return array
 * @optimize using joins
 */
	public function getByRole($role = null, $order = 'ASC') {
		if (empty($role)) {
			return array();
		}
		$roleName = (Configure::read('AssociatedUserRoles.' . str_replace(' ', '', $role) . '.roles') !== null) ? Configure::read('AssociatedUserRoles.' . str_replace(' ', '', $role) . '.roles') : $this->userRoles['roles'][str_replace(' ', '', $role)]['roles'];
		$roleIds = $this->Role->find('list', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'Role.name' => $roleName
				)
			)
		);

		$userIds = $this->UsersRole->find('list', array(
			'fields' => array('UsersRole.user_id'),
			'conditions' => array('UsersRole.role_id' => $roleIds)
		));

		return $this->find('list', array(
			'fields' => array('User.fullname'),
			'order' => array("User.user_first_name $order"),
			'conditions' => array('User.id' => $userIds)
		));
	}

/**
 * Gets all roles from the roles array
 *
 * @param bool $fullList if need to return fullList or no
 * @return array
 */
	public function getRoleNames($fullList = false) {
		return (!$fullList) ? Hash::combine(Configure::read('AssociatedUserRoles'), '{s}.label', '{s}.label') : Hash::combine($this->userRoles['roles'], '{s}.label', '{s}.label');
	}

/**
 * Gets all permissions asociated with a role
 *
 * @param string $roleGroup Role group
 * @return array
 */
	public function getPermissionsByRole($roleGroup = null) {
		$permissions = array();
		$roles = Configure::read('AssociatedUserRoles.' . str_replace(' ', '', $roleGroup) . '.roles');
		if (!empty($roles)) {
			foreach ($roles as $rolePermissions) {
				$permissions[$rolePermissions] = $rolePermissions;
			}
		}
		return $permissions;
	}

/**
 * Override view
 *
 * @param type $id id of the an user
 * @param type $options options values
 * @return User array
 */
	public function view($id, $options = array()) {
		$defaultOptions = array(
			'contain' => array(
				'AssociatedUser.UserAssociated',
				'Entity',
				'SalesGoal',
				'PermissionConstraint.Permission',
				// NOTE: because we have 1 role per user, we can retrieve perms this way
				// if we add multiple roles in the future, we'll need to call Role->calculatePermissions()
				'UsersRole.Role.Permission',
			),
		);
		$options = Hash::merge($defaultOptions, $options);

		$user = parent::view($id, $options);

		if ($this->isEncrypted($user['User']['account_number'])) {
			$user['User']['account_number'] = $this->decrypt($user['User']['account_number'], Configure::read('Security.OpenSSL.key'));
		}
		if ($this->isEncrypted($user['User']['routing_number'])) {
			$user['User']['routing_number'] = $this->decrypt($user['User']['routing_number'], Configure::read('Security.OpenSSL.key'));
		}
		return $user;
	}

/**
 * Get the user related columns for the User Parameters
 * We'll return all the associated: Myself, Managers, Partner Rep
 *
 * @param string $userId user id
 * @param string $compensationId compensation id
 * @todo optimize query
 * @return array
 */
	public function getAssociatedUsersUserParameters($userId, $compensationId) {
		$rolesConfig = Configure::read('AssociatedUserRoles');
		$roles = Hash::merge(
			Hash::extract($rolesConfig, 'SalesManager.label'), Hash::extract($rolesConfig, 'PartnerRep.label')
		);

		$options = array(
			'conditions' => array(
				'AssociatedUser.user_compensation_profile_id' => $compensationId,
				'AssociatedUser.role' => $roles,
			),
			'contain' => array(
				'UserAssociated',
			),
		);
		//this is a fake associated user, used to display my own parameters
		$me = array(
			'AssociatedUser' => array(
				//setting associated_user_id to my own id for my parameters
				'associated_user_id' => $userId,
			)
		);

		$users = array($me);
		$associatedUsers = $this->AssociatedUser->find('all', $options);

		//Check permissions to see which additional associated users' data can be included
		//Note: Since SM inherits SM2's permissions we must check SM permissions first for currently logged in user
		//Only Primary managers and admins are permitted to see all associated users
		if ($this->RbacIsPermitted('app/actions/UserCompensationProfiles/view/module/SMCompModule')) {
			foreach ($associatedUsers as $associatedUser) {
				$users[] = $associatedUser;
			}
		} elseif ($this->RbacIsPermitted('app/actions/UserCompensationProfiles/view/module/SM2CompModule')) {
			//If allowed include only the currenly logged in UserAssociated as long as they are a secondary manager in addition to me
			foreach ($associatedUsers as $associatedUser) {
				if (CakeSession::read('Auth.User.id') === $associatedUser['UserAssociated']['id']) {
					$users[] = $associatedUser;
				}
			}
		}
		return $users;
	}

/**
 * Get the associated users
 *
 * @param string $userId user id
 * @param string $compensationId compensation id
 * @param array $roles to filter
 * @param array $permissionLvl to filter
 * @return array
 */
	public function getAssociatedUsers($userId, $compensationId, $roles = array(), $permissionLvl = array()) {
		$options = array(
			'conditions' => array(
				'AssociatedUser.user_id' => $userId,
				'AssociatedUser.user_compensation_profile_id' => $compensationId,
			),
			'contain' => array(
				'UserAssociated',
			),
		);
		if (!empty($roles)) {
			$options['conditions']['AssociatedUser.role'] = $roles;
		}
		if (!empty($permissionLvl)) {
			$options['conditions']['AssociatedUser.permission_level'] = $roles;
		}

		return $this->AssociatedUser->find('all', $options);
	}

/**
 * Get list of the user(s) who are associated with the given user id
 * Example: Jon Doe is associated with Jane Doe but Jane is also the associated user to many other users
 * this method will return those other users including Jon Doe
 *
 * @param string $userId the id of the associated user
 * @param mixed string|array $permissionLvl to filter
 * @return array
 */
	public function getUsersByAssociatedUser($userId, $permissionLvl = null) {
		$options = array(
			'conditions' => array(
				'AssociatedUser.associated_user_id' => $userId,
			),
			'fields' => ["User.id", "User.user_first_name", "User.user_last_name"],
			'joins' => [
				[
					'table' => 'users',
					'alias' => $this->alias,
					'type' => 'INNER',
					'conditions' => [
						'AssociatedUser.user_id = User.id'
					]

				]
			]
		);

		if (!empty($permissionLvl)) {
			$options['conditions']['AssociatedUser.permission_level'] = $permissionLvl;
		}
		$data = $this->AssociatedUser->find('all', $options);
		if (!empty($data)) {
			return Hash::combine($data, '{n}.User.id', ['%s %s', '{n}.User.user_first_name', '{n}.User.user_last_name']);
		} else {
			return [];
		}
	}

/**
 * Return the user information needed for the Breadcrum helper
 *
 * @param string $userId user id
 * @return array
 */
	public function getBreadcrumbUserInfo($userId) {
		$options = array(
			'fields' => array('id', 'user_first_name', 'user_last_name'),
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $userId
			),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

/**
 * Return options array to be used for custom pagination and filtering
 *
 * @return array
 */
	public function getUserIndexInfo() {
		$options = array(
			'fields' => array(
				'id',
				'string_agg("Role"."name", \', \')  AS "Role__name"',
				'Entity.entity_name',
				'user_first_name',
				'user_last_name',
				'username',
				'initials',
				'user_email',
				'active',
				'is_blocked'
			),
			'recursive' => -1,
			'joins' => array(
				array('table' => 'users_roles',
					'alias' => 'UsersRole',
					'type' => 'LEFT',
					'conditions' => array(
						'User.id = UsersRole.user_id',
					)
				),
				array('table' => 'roles',
					'alias' => 'Role',
					'type' => 'LEFT',
					'conditions' => array(
						'UsersRole.role_id = Role.id',
					)
				),
				array('table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => array(
						'User.entity_id = Entity.id',
					)
				),
			),
			'group' => array('User.id', 'Entity.entity_name'),
			'order' => array(
				'user_first_name' => 'ASC'
			),
			'limit' => 30
		);

		return $options;
	}

/**
 * setExportStructure
 * Rearranges the array data structure into a two-dimentional array which can be used to export user data
 * 
 * @param array $data original user data structure returned by querying the data using the query in Self::getUserIndexInfo method
 * @return array
 */
	public function setExportStructure($data) {
		$result = [];
		if (!empty($data)) {
			foreach ($data as $userData) {
				$result[] = [
					'user_last_name' => Hash::get($userData, 'User.user_last_name'),
					'user_first_name' => Hash::get($userData, 'User.user_first_name'),
					'role_name' => Hash::get($userData, 'Role.name'),
					'username' => Hash::get($userData, 'User.username'),
					'initials' => Hash::get($userData, 'User.initials'),
					'user_email' => Hash::get($userData, 'User.user_email'),
					'entity' => Hash::get($userData, 'Entity.entity_name'),
					'active' => Hash::get($userData, 'User.active'),
					'is_blocked' => Hash::get($userData, 'User.is_blocked')
				];
			}
		}
		return $result;
	}

/**
 * Array of Arguments to be used by the search plugin
 */
	public $filterArgs = array(
		'active' => array('type' => 'value'),
		'search' => array('type' => 'query', 'method' => 'orConditions')
	);

/**
 * Return conditions to be used when searching for users
 *
 * @param array $data options values
 * @return array
 */
	public function orConditions($data = array()) {
		$filter = Hash::get($data, 'search');
		$conditions = array(
			'OR' => array(
				'Role.name ILIKE' => '%' . $filter . '%',
				$this->alias . '.user_first_name ILIKE' => '%' . $filter . '%',
				$this->alias . '.user_last_name ILIKE' => '%' . $filter . '%',
				$this->alias . '.username ILIKE' => '%' . $filter . '%',
				$this->alias . '.user_email ILIKE' => '%' . $filter . '%',
				$this->alias . '.fullname ILIKE' => '%' . $filter . '%',
			)
		);
		return $conditions;
	}

/**
 * Return list of user_id and username for use in ajax searches
 *
 * @param string $searchStr string to filter
 * @param bool $active true or false values
 * @return array
 */
	public function findUserByUsername($searchStr, $active) {
		$dbCol = 'username';
		$searchStr = '%' . $searchStr;

		return $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$dbCol . ' ILIKE' => $searchStr,
				'active' => $active
			),
				'fields' => array($this->alias . '.' . $dbCol),
				'limit' => 10
			)
		);
	}

/**
 * isAllowedSecretData
 *
 * Checks if a user belongs to the specified role
 *
 * @param string $id the user id
 * @param string $$password the user password
 * @return bool true or false whether the user has permissions to see sensitive client data and the password is correct
 * @throws InvalidArgumentException
 */
	public function isAllowedSensitiveClientData($id, $password) {
		return ($this->rbacIsPermitted('app/actions/Merchants/ajaxDisplayDecryptedVal') && $this->pwIsValid($id, $password));
	}

/**
 * roleIs
 *
 * Checks if a user belongs to the specified role
 *
 * @param string $id the user id
 * @param mixed $roles a string or a numerically index array set of roles to check whether a user belongs to.
 * @return bool true or false whether the user has the $role
 * @throws InvalidArgumentException
 */
	public function roleIs($id, $roles) {
		if (empty($roles) || empty($id)) {
			throw new InvalidArgumentException('User::role method is missing required parameters');
		}
		return (bool)$this->UsersRole->find('count', array(
			'conditions' => array(
				'UsersRole.user_id' => $id,
				'Role.name' => $roles
			),
			'contain' => 'Role.name'
		));
	}

/**
 * getUserRolesList
 *
 * Returns a list of all the roles assigned to a user with the role name as the key and role id as the value
 *
 * @param string $id the user id
 * @return array
 */
	public function getUserRolesList($id) {
		if (empty($id)) {
			return [];
		}
		return $this->UsersRole->find('list', array(
			'fields' => ['Role.name', 'Role.id'],
			'conditions' => array(
				'UsersRole.user_id' => $id,
			),
			'contain' => 'Role.name'
		));
	}

/**
 * getPartnerReps method
 *
 * @param string $partnerId partner id
 * @return array
 */
	public function getPartnerReps($partnerId = null) {
		$fullList = $this->getByRole(Configure::read('AssociatedUserRoles.PartnerRep.label'));

		if (!empty($partnerId)) {
			$assignedIds = $this->UserCompensationProfile->find('list', array('fields' => array(
					'UserCompensationProfile.user_id'), 'conditions' => array('UserCompensationProfile.partner_user_id' => $partnerId)));
			//Remove Users that are already assigned to partners
			foreach ($assignedIds as $id) {
				unset($fullList[$id]);
			}

			//Now remove users that do not have a default compensation profile yet
			foreach ($fullList as $key => $val) {
				if (!$this->UserCompensationProfile->hasDefault($key)) {
					unset($fullList[$key]);
				}
			}
		}

		return $fullList;
	}

/**
 * emailUser function triggers userPasswordReset event  after password has been reset
 *
 * @param string $email an email
 * @param string $bodyMsg the message for the email body
 * @param array $settings a custom set of email settings.
 * @return void
 */
	public function emailUser($email, $bodyMsg, $settings = []) {
		if (!empty($email)) {
			if (empty($settings)) {
				$settings = [
					'template' => Configure::read('App.defaultTemplate'),
					'from' => Configure::read('App.defaultSender'),
					'to' => $email,
					'subject' => 'Axia Database Notice',
				];
			}
			$settings['emailBody'] = $bodyMsg;

			$event = new CakeEvent('App.Model.readyForEmail', $this, $settings);
			// dispatch event to the local event manager
			$this->getEventManager()->dispatch($event);
		}
	}

/**
 * newPwExpiration
 * Generates a date to be used as new user password expiration based on App's Configured pw validity age in bootstrap.
 * 
 * @return string representation of new password expiration date.
 */
	public function newPwExpiration() {
		$date = new DateTime(date('Y-m-d'));
		$interval = "P" . Configure::read('App.pw_validity_age') . "D";
		$date->add(new DateInterval($interval));
		return $date->format('Y-m-d');
	}
/**
 * Returns an grouped array of entity, active managers and users excluding Admins
 *
 * @param bool $filterByRole Filter the user list by the current user role
 *	- The filter is based on GUIbuilderComponent::buildRepFilterOptns method
 *
 * @return array Entity > Manager > User (id, name)
 */
	public function getEntityManagerUsersList($filterByRole = false) {
		$currentUserId = $this->_getCurrentUser('id');
		//Allow ROLE_ACCT_MGR as Admin so that they can pull all report results
		$isAdmin = $this->roleIs($currentUserId, array_merge($this->userRoles['roles'][self::ROLE_GROUP_ADMIN]['roles'], [self::ROLE_ACCT_MGR]));
		$isSalesManager = $this->roleIs($currentUserId, $this->userRoles['roles'][self::ROLE_GROUP_SM]['roles']);
		$isInstaller = $this->roleIs($currentUserId, $this->userRoles['roles'][self::ROLE_GROUP_INSTALLER]['roles']);

		$options = array(
			'conditions' => array(
				'User.active =' => 1,
				),
			'fields' => array(
				'User.id',
				'User.user_first_name',
				'User.user_last_name',
				'string_agg(distinct("Role"."name"), \'/\')  AS "Role__name"',
				'ParentUser.id',
				'ParentUser.user_first_name',
				'ParentUser.user_last_name',
			),
			'contain' => array(
				'Entity' => array(
					'fields' => array(
						'Entity.id',
						'Entity.entity_name',
					)
				),
			),
			'order' => array(
				'Entity.entity_name' => 'ASC NULLS LAST',
				'ParentUser.user_first_name' => 'ASC NULLS LAST',
				'User.user_first_name' => 'ASC',
			),
			'group' => array('User.id', 'ParentUser.id', 'Entity.id'),
			);

			$assocUsersJoin = array('table' => 'associated_users',
				'alias' => 'AssociatedUser',
				'type' => 'left',
				'conditions' => array(
					'User.id = AssociatedUser.user_id',
				)
			);

			if ($isAdmin || $this->roleIs($currentUserId, self::ROLE_SM)) {
				$assocUsersJoin['conditions']['AssociatedUser.permission_level'] = self::ROLE_SM;
			} elseif ($this->roleIs($currentUserId, self::ROLE_SM2)) {
				$assocUsersJoin['conditions']['AssociatedUser.permission_level'] = self::ROLE_SM2;
			} elseif ($isInstaller) {
				$assocUsersJoin['conditions']['AssociatedUser.permission_level'] = self::ROLE_INSTALLER;
			}

			$options['joins'] = array(
				$assocUsersJoin,
				array('table' => 'users',
					'alias' => 'ParentUser',
					'type' => 'left',
					'conditions' => array(
						'ParentUser.id = ' . (($isAdmin || $isSalesManager || $isInstaller)? "AssociatedUser.associated_user_id" : "'$currentUserId'")
					)
				),
				array('table' => 'users_roles',
					'alias' => 'UsersRole',
					'type' => 'left',
					'conditions' => array(
						'User.id = UsersRole.user_id'
					)
				),
				array('table' => 'roles',
					'alias' => 'Role',
					'type' => 'inner', //Completely exlude all Admins
					'conditions' => array(
						'Role.id = UsersRole.role_id',
						"Role.name NOT ILIKE '" . self::ROLE_GROUP_ADMIN . "%'"
					)
				)
			);

		if ($filterByRole) {
			if ($isSalesManager || $isInstaller) {
				// Managers and Installer can see his associated users
				$options = Hash::merge($options, array(
					'conditions' => array(
						array(
							'AND' => array(
								array(
									'OR' => array(
										array('User.id' => $currentUserId),
										array('AssociatedUser.associated_user_id' => $currentUserId)
									)
								)
							)
						)
					)
				));
			} elseif (!$isAdmin) {
				// Others not admin users can only see themself
				$options = Hash::merge($options, array(
					'conditions' => array('User.id' => $currentUserId)
				));
			}
		}

		$users = $this->find('all', $options);

		$groupedList = array();
		foreach ($users as $user) {
			if (!$filterByRole || $isAdmin) {
				$groupedList = $this->_addComplexIdOption(self::PREFIX_ENTITY, $user, $groupedList);
				$groupedList = $this->_addComplexIdOption(self::PREFIX_PARENT, $user, $groupedList);
			} elseif ($isSalesManager || $isInstaller) {
				// Avoid to include the parent of the current user
				$parentUserId = Hash::get($user, 'ParentUser.id');
				if ($parentUserId === $currentUserId) {
					$groupedList = $this->_addComplexIdOption(self::PREFIX_PARENT, $user, $groupedList);
				}
			}
			$groupedList = $this->_addComplexIdOption(self::PREFIX_USER, $user, $groupedList);
		}
		//Prepend a Select all option at the top of the array
		if (!$filterByRole || $isAdmin) {
			$entityId = $this->buildComplexId(self::PREFIX_ALL, self::PREFIX_ALL);
			$entityEntry = ['Select all' => [$entityId => "All Entities/Users "]];
			$groupedList = array_merge($entityEntry, $groupedList);
		}
		return $groupedList;
	}

/**
 * Add the user as a complex Id to a grouped list array
 *
 * @param string $prefix Prefix for complex user ids
 * @param array $user User data
 * @param array $groupedList Array to insert the new element
 * @return array
 */
	protected function _addComplexIdOption($prefix, $user, $groupedList) {
		switch ($prefix) {
			case self::PREFIX_ENTITY:
				$entityId = $this->buildComplexId(self::PREFIX_ENTITY, Hash::get($user, 'Entity.id'));
				$entityName = Hash::get($user, 'Entity.entity_name');
				if (empty($entityName)) {
					$entityName = __('No Entity');
				}
				if (empty($groupedList[$entityId])) {
					$entityEntry = [$entityName => [$entityId => "$entityName "]];
					$groupedList = array_merge($entityEntry, $groupedList);
				}
				break;

			case self::PREFIX_PARENT:
				$parentId = Hash::get($user, 'ParentUser.id');
				$parentComplexId = $this->buildComplexId(self::PREFIX_PARENT, $parentId);
				$parentUserName = trim(Hash::get($user, 'ParentUser.user_first_name') . ' ' . Hash::get($user, 'ParentUser.user_last_name'));
				if (empty($parentUserName)) {
					$parentUserName = __('No Manager');
				}

				if (empty($groupedList[$parentComplexId])) {
					// $groupedList[$parentComplexId] = "-- $parentUserName";
					$groupedList[$parentUserName][$parentComplexId] = "$parentUserName (All in this group)";
					if (!empty($parentId)) {
						//adding the manager as a user too to filter by manager only
						$managerAsUserId = $this->buildComplexId(self::PREFIX_USER, $parentId);
						$groupedList[$parentUserName][$managerAsUserId] = "$parentUserName ";
					}
				}
				break;

			default:
				$id = $this->buildComplexId(self::PREFIX_USER, Hash::get($user, 'User.id'));
				$name = Hash::get($user, 'User.user_first_name') . ' ' . Hash::get($user, 'User.user_last_name');
				$parentUserName = trim(Hash::get($user, 'ParentUser.user_first_name') . ' ' . Hash::get($user, 'ParentUser.user_last_name'));
				if (empty($parentUserName)) {
					if (Hash::get($user, 'Role.name') === self::ROLE_RESELLER || Hash::get($user, 'Role.name') === self::ROLE_REFERRER) {
						$parentUserName = __('Referrers/Resellers');
					} elseif (Hash::get($user, 'Role.name') === self::ROLE_PARTNER) {
						$parentUserName = __('Partners');
					} else {
						$parentUserName = __('No Manager');
					}
				}

				$role = Hash::get($user, 'Role.name');
				if (!empty($role)) {
					$groupedList[$parentUserName][$id] = "$name ($role)";
				} else {
					$groupedList[$parentUserName][$id] = "$name ";
				}
		}
		return $groupedList;
	}

/**
 * Return the list of prefix for complex user select input values
 *
 * @return array
 */
	public function getPrefixes() {
		return array(
			self::PREFIX_USER => __('User'),
			self::PREFIX_PARENT => __('Parent user'),
			self::PREFIX_ENTITY => __('Entity'),
			self::PREFIX_ALL => __('All Entities/Users'),
		);
	}

/**
 * Check if a prefix for complex select is valid
 *
 * @param string $prefix Prefix for complex select input
 *
 * @return bool
 */
	public function isValidPrefix($prefix) {
		return in_array($prefix, array_keys($this->getPrefixes()));
	}

/**
 * Build a userId value for complex user inputs
 *
 * @param string $prefix Prefix for complex user ids
 * @param string $userId User id
 * @throws OutOfBoundsException
 *
 * @return bool
 */
	public function buildComplexId($prefix, $userId) {
		if (!$this->isValidPrefix($prefix)) {
			throw new OutOfBoundsException(__('Invalid prefix'));
		}

		return "{$prefix}:{$userId}";
	}

/**
 * Parse a complex Id and return the values of the prefix type and the id.
 * If it is not a complexId, the result "prefix" will be null
 *
 * @param string $complexId Complex user ids
 * @throws OutOfBoundsException
 * @return bool
 */
	public function extractComplexId($complexId) {
		$parts = explode(':', $complexId);

		if (count($parts) === 2) {
			$prefix = Hash::get($parts, '0');
			if ($this->isValidPrefix($prefix)) {
				return array(
					'prefix' => $prefix,
					'id' => Hash::get($parts, '1'),
				);
			} else {
				throw new OutOfBoundsException(__('Invalid complex user id prefix'));
			}
		}

		return array(
			'prefix' => null,
			'id' => $complexId,
		);
	}

/**
 * Return an array with the users related to a complexId.
 * For example the users that belongs to an entity or the users that share the same manager
 *
 * @param string $complexId Complex user ids
 *
 * @return array with the users related to the complex id
 */
	public function getRelatedUsersByComplexId($complexId) {
		$parsedId = $this->extractComplexId($complexId);

		$id = Hash::get($parsedId, 'id');
		switch (Hash::get($parsedId, 'prefix')) {
			case self::PREFIX_ALL:
				return $this->find('list', array(
					'fields' => array($this->primaryKey, $this->primaryKey)
				));
			case self::PREFIX_ENTITY:
				$conditions = array();
				if (empty($id)) {
					// Users with no entity
					$conditions = array(
						"{$this->alias}.entity_id IS NULL"
					);
				} else {
					$conditions = array(
						"{$this->alias}.entity_id" => $id
					);
				}
				return $this->find('list', array(
					'fields' => array($this->primaryKey, $this->primaryKey),
					'conditions' => $conditions,
				));

			case self::PREFIX_PARENT:
				$conditions = array();
				if (empty($id)) {
					// Users with no managers
					$conditions = array(
						"{$this->alias}.parent_user_id IS NULL"
					);
				} else {
					$conditions = array(
						"{$this->alias}.parent_user_id" => $id
					);
				}
				return $this->find('list', array(
					'fields' => array($this->primaryKey, $this->primaryKey),
					'conditions' => $conditions,
				));
		}

		return array($id);
	}

/**
 * Return the User or Entity name given a complex user id
 *
 * @param string $complexId Complex user id
 * @return string
 */
	public function getUserNameByComplexId($complexId) {
		$parsedId = $this->extractComplexId($complexId);

		$id = Hash::get($parsedId, 'id');
		$prefix = Hash::get($parsedId, 'prefix');
		if ($prefix === self::PREFIX_ENTITY) {
			return $this->Entity->field('entity_name', [
				'Entity.id' => $id
			]);
		}
		if ($prefix === self::PREFIX_ALL) {
			return null; //no condition should be returned when all is needed
		}
		$user = $this->read(['user_last_name', 'fullname'], $id);
		if ($prefix === self::PREFIX_PARENT) {
			return $user['User']['user_last_name'] . ' ' . __('Group');
		}

		return $user['User']['fullname'];
	}

/**
 * Convenience method to check if the user is admin
 *
 * @param string $userId User Id
 * @return bool
 */
	public function isAdmin($userId) {
		return $this->roleIs($userId, $this->userRoles['roles'][self::ROLE_GROUP_ADMIN]['roles']);
	}

/**
 * Return the list user roles
 *
 * @return array
 */
	public function getRoles() {
		return [
			self::ROLE_ADMIN_I => __('Admin I'),
			self::ROLE_ADMIN_II => __('Admin II'),
			self::ROLE_ADMIN_III => __('Admin III'),
			self::ROLE_ADMIN_IV => __('Admin IV'),
			self::ROLE_SM => __('Sales Manager 1'),
			self::ROLE_SM2 => __('Sales Manager 2'),
			self::ROLE_ACCT_MGR => __('Account Manager'),
			self::ROLE_REP => __('Rep'),
			self::ROLE_PARTNER_REP => __('Partner Rep'),
			self::ROLE_PARTNER => __('Partner'),
			self::ROLE_INSTALLER => __('Installer'),
			self::ROLE_RESELLER => __('Reseller'),
			self::ROLE_REFERRER => __('Referrer'),
		];
	}

/**
 * getDefaultUserSearch
 * Returns the current user as the default user for search
 *
 * @return array
 */
	public function getDefaultUserSearch() {
		return $this->buildComplexId(User::PREFIX_USER, $this->_getCurrentUser('id'));
	}

/**
 * womplyEnabled($id)
 * Returns the current user as the default user for search
 *
 * @param string $id a user id
 * @return array
 */
	public function womplyEnabled($id) {
		$this->id = $id;
		return (bool)$this->field('womply_user_enabled');
	}

/**
 * getByNameAndRole()
 * Returns the user that matches passed parameters
 *
 * @param string $fullname a user full name (first name and last name)
 * @param mixed array|string $roleName a valid role name or an array of role names
 * @param integer $activeUser 1 to indicate find active users 0 to find inactive users.
 * @return array
 */
	public function getByNameAndRole($fullname, $roleName, $activeUser = null) {
		if (empty($fullname) || empty($roleName)) {
			return [];
		}
		$roleJoinCond = array(
			'UsersRole.role_id = Role.id',
		);
		if (is_array($roleName)) {
			$roleJoinCond['Role.name'] = $roleName;
		} else {
			$roleJoinCond[] = "Role.name = '$roleName'";
		}
		$userConditions = array(
			'OR' => array(
				'User.fullname' => $fullname,
				'trim(BOTH FROM "User"."user_first_name" || "User"."user_last_name") = ' . "'$fullname'"
			),
			'User.username IS NOT NULL',
			"User.username != ''",
		);
		if (!is_null($activeUser)) {
			$userConditions['User.active'] = $activeUser;
		}
		$user = $this->find('first', array(
			'fields' => array('distinct(User.id) AS "User__id"', 'User.fullname'),
			'conditions' => $userConditions,
			'joins' => array(
				array('table' => 'users_roles',
					'alias' => 'UsersRole',
					'type' => 'LEFT',
					'conditions' => array(
						'User.id = UsersRole.user_id',
					)
				),
				array('table' => 'roles',
					'alias' => 'Role',
					'type' => 'INNER',
					'conditions' => $roleJoinCond
				),
			)
		));
		return $user;
	}

/**
 * getSimilarNames()
 * Returns a list of users whose names are similar to the full name parameter and match given role.
 * A full list of users with the given role will be retuned if no name parameter is passed
 *
 * @param mixed array|string $roleName a valid role name or an array of role names
 * @param string $fullname a user full name (first name and last name) if empty all active users will be returned
 * @param integer $activeUser 1 to indicate find active users 0 to find inactive users.
 * @return array
 */
	public function getSimilarNames($roleName, $fullname = null, $activeUser = null) {
		$conditions = [];
		if (!empty($fullname)) {
			//Split full name by its components using space as delimeter
			$n = substr_count($fullname, ' '); //n= num of components-1
			$nameParts = explode(' ', $fullname);
			//Create mulitple OR conditions to search for the components of the user's full name
			for ($x = 0; $x < $n+1; $x++) {
				//skip parts of the names that are too short
				if (strlen($nameParts[$x]) > 1) {
					$conditions['OR'][] = [ 
					'OR' => [
						$this->alias . '.user_first_name ILIKE' => '%' . $nameParts[$x] . '%',
						$this->alias . '.user_last_name ILIKE' => '%' . $nameParts[$x] . '%',
					]];
				}
			}
		}

		$roleJoinCond = array(
			'UsersRole.role_id = Role.id',
			'Role.name' => $roleName
		);

		$conditions[] = 'User.username IS NOT NULL';
		$conditions[] = "User.username != ''";
		if (!is_null($activeUser)) {
			$conditions['User.active'] = $activeUser;
		}

		$users = $this->find('all', array(
			'fields' => array('distinct(User.id) AS "User__id"', 'User.user_first_name', 'User.user_last_name', 'User.active'),
			'conditions' => $conditions,
			'order' => 'User.user_first_name ASC',
			'joins' => array(
				array('table' => 'users_roles',
					'alias' => 'UsersRole',
					'type' => 'LEFT',
					'conditions' => array(
						'User.id = UsersRole.user_id',
					)
				),
				array('table' => 'roles',
					'alias' => 'Role',
					'type' => 'INNER',
					'conditions' => $roleJoinCond
				),
			)
		));
		return $users;
	}

/**
 * Create and Manage API Tokens
 *
 * @param string $id User.id
 * @return boolean true on success
 * @throws Exception
 */

	public function create_token($id) {
		if ($this->roleIs($id, self::ROLE_API) === false) {
			throw new Exception('Cannot generate API token! User does not have an ' . self::ROLE_API . ' role');
		}
		if (!$this->save(array('id' => $id, 'access_token' => sha1(CakeText::uuid())), array('validate' => false))) {
			throw new Exception('There was an unexpected error saving access token');
		}
		return true;
	}

/**
 * Creates a new API password and saves it
 * Returns unecrypted API password string so that it can be provided to the API consumer for future API access.
 * 
 * @param string $id User.id
 * @return boolean true on success
 * @throws Exception
 */

	public function create_api_password($id) {
		$apiUser = $this->find('first', array(
			'fields' => array('access_token'),
			'conditions' => array('id' => $id, "access_token !=''")
		));
		if ($this->roleIs($id, self::ROLE_API) === false || empty($apiUser)) {
			throw new Exception('Cannot generate API password! User is not an ' . self::ROLE_API . ' with an an assigned access token');
		}
		$DigestAuthenticate = new DigestAuthenticate(new ComponentCollection(), []);
		$pass = $this->generateRandPw();
		
		$apiPass = $DigestAuthenticate->password($apiUser['User']['access_token'], $pass, env('SERVER_NAME'));
		if (!$this->save(array('id' => $id, 'api_password' => $apiPass),  array('validate' => false))) {
			throw new Exception('There was an unexpected error saving api password');
		}
		return $pass;
	}

/**
 * revokeApiAccess
 * Revokes api access by deleting API credentials.
 *
 * @param string $id User.id
 * @return void
 */

	public function revokeApiAccess($id) {
		return $this->save(array('id' => $id, 'access_token' => null, 'api_password' => null), array('validate' => false));
	}

/**
 * revokeApiAccess
 * Revokes api access by deleting API credentials.
 *
 * @param string $id User.id
 * @return void
 */

	public function updateLastApiRequest($id) {
		return $this->save(array('id' => $id, 'api_last_request' => date('Y-m-d H:i:s')), array('validate' => false));
	}

/**
 * trackIncorrectLogIn
 * tracks the current user inccorrect log in attemts
 *
 * @param string $id User.id
 * @return integer the current count of failed attempts if an active user exists or 0 if user not found
 */
	public function trackIncorrectLogIn($username_or_email, $reset = false) {
		$userData = $this->find('first', array(
			'fields' => array('id', 'wrong_log_in_count', 'is_blocked', 'active', 'user_email'),
			'conditions' => array(
				'OR' => array(
					'username' => $username_or_email,
					'user_email' => $username_or_email
				),
				'active' => 1 //must be active users inacvitve users are always blocked users
			)
		));
		if(!empty($userData) && $reset) {
			//reset
			$userData['User']['wrong_log_in_count'] = 0;
			$userData['User']['is_blocked'] = false;
			$this->clear();
			$this->save($userData, array('validate' => false));
		} elseif (!empty($userData) && $userData['User']['is_blocked'] == false) {
			if ($userData['User']['wrong_log_in_count'] < self::MAX_LOG_IN_ATTEMPTS) {
				$userData['User']['wrong_log_in_count'] += 1;
				$this->save($userData, array('validate' => false));
		 		//increase count
		 	} 
		 	if ($userData['User']['wrong_log_in_count'] >= self::MAX_LOG_IN_ATTEMPTS) {
				//block user and notify send email
				$userData['User']['pw_reset_hash'] = $this->getRandHash();
				$userData['User']['password'] = $this->generateRandPw();
				$this->clear();
				$this->save($userData, array('validate' => false));
				$this->notifyUserBlockedFailedLogInAttempts($userData);
		 		$this->toggleBlockUser($userData['User']['id'], true);
			}
		}
		
		return Hash::get($userData, 'User.wrong_log_in_count', 0);
	}

/**
 * notifyUserBlockedFailedLogInAttempts
 * Send email to user when account is blocked due to too many failed log in attempts
 *
 * @param array $user data about the user; must contain a new pw_reset_hash, new temporary password and the user_email
 * @return void
 */
	public function notifyUserBlockedFailedLogInAttempts($user) {
		if (empty($user['User']['user_email'])) {
			return null;
		}
		
		$msg = "Your account has been locked due to excessive incorrect log in attempts.\n";
		$msg .= "The following temporary password has been set as your current password on your account:\n {$user['User']['password']}\n";
		$msg .= "To unlock your account you must change the temporary password with a new password using this url:\n";
		$msg .= Router::url(['controller' => 'Users', 'action' => 'change_pw', true, $user['User']['pw_reset_hash']], true) . "\n";
		$msg .= "\n If this account is intended for API access, access has also been revoked to the API for security.\n";
		$msg .= "You may regenerate API access credentials from your user profile after you unlock your account.\n";
		$this->emailUser($user['User']['user_email'], $msg);
	}

/**
 * toggleBlockUser
 * Block or unblock users
 *
 * @param string $id User.id
 * @param boolean $isBlocked true|false to block|unblock
 * @return void
 */
	public function toggleBlockUser($id, $isBlocked) {
		$this->save(['id' => $id, 'is_blocked' => $isBlocked], array('validate' => false));
		if ($isBlocked && $this->roleIs($id, User::ROLE_API)) {
			$this->revokeApiAccess($id);
		}
	}

}
