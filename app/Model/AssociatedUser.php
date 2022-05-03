<?php

App::uses('AppModel', 'Model');

/**
 * AssociatedUser Model
 *
 * @property User $User
 * @property Role $Role
 */
class AssociatedUser extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'role';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'role' => [
			'maxRoleAssociation' => [
				'rule' => ['maxRoleAssociation'],
				'message' => 'You can not associate more users with the role selected'
			]
		],
		'associated_user_id' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'You must select an associated user'
			],
			'checkUniqueAssociation' => [
				'rule' => ['checkUniqueAssociation'],
				'message' => 'This user is already associated',
			]
		],
		'permission_level' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'You must select a permission level'
			]
		]
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'UserAssociated' => [
			'className' => 'User',
			'foreignKey' => 'associated_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = [
		'UserParameter' => [
			'dependent' => true,
		],
		'ResidualParameter' => [
			'dependent' => true,
		],
		'ResidualTimeParameter' => [
			'dependent' => true,
		],
	];

/**
 * Custom validation rule. Check if the user associated is unique.
 *
 * @return boolean
 */
	public function checkUniqueAssociation() {
		$condition = [
			"{$this->alias}.user_id" => $this->data[$this->alias]['user_id'],
			"{$this->alias}.associated_user_id" => $this->data[$this->alias]['associated_user_id'],
			"{$this->alias}.user_compensation_profile_id" => $this->data[$this->alias]['user_compensation_profile_id']
		];

		$result = $this->find('count', ['conditions' => $condition]);
		return ($result === 0);
	}

/**
 * maxRoleAssociation
 * Custom validation rule. Check max amount of users with a role within the same association.
 * 
 * @param string $check value to check
 * @return boolean
 */
	public function maxRoleAssociation($check) {
		$max = Configure::read('AssociatedUserRoles.' . str_replace(' ', '', $this->data[$this->alias]['role']) . '.maxAssociated');
		//check unlimited maxAssociated
		if ($max === null) {
			return true;
		}
		$condition = [
			"{$this->alias}.user_id" => $this->data[$this->alias]['user_id'],
			"{$this->alias}.role" => $this->data[$this->alias]['role'],
			"{$this->alias}.user_compensation_profile_id" => $this->data[$this->alias]['user_compensation_profile_id'],
		];
		$result = $this->find('count', ['conditions' => $condition]);
		return ($result < $max);
	}

/**
 * Get list of names and roles for associated users
 *
 * @param int $userId ID of user to get associated users for
 * @param string $compensationId UserCompensationProfile.id
 * @param bool $includeSelf True to also associate user to themself, otherwise false returns associations from database only.
 * @return array
 */
	public function getAssociatedUsers($userId = null, $compensationId = null, $includeSelf = false) {
		$users = $this->find('all', [
			'conditions' => [
				$this->alias . '.user_id' => $userId,
				$this->alias . '.user_compensation_profile_id' => $compensationId
			],
			'contain' => [
				'UserAssociated' => [
					'fields' => ['UserAssociated.user_first_name', 'UserAssociated.user_last_name']
				],
			],
			'fields' => [$this->alias . '.role'],
		]);

		$userList = [];
		foreach ($users as $user) {
			$userList[Hash::get($user, 'UserAssociated.id')] = [
				'role' => __(Hash::get($user, $this->alias . '.role')),
				'name' => CakeText::insert(':user_first_name :user_last_name', $user['UserAssociated']),
			];
		}
		if ($includeSelf) {
			$self = ClassRegistry::init('User')->find('list', [
				'conditions' => ['User.id' => $userId],
			]);
			$id = key($self);
			$userList[$id] = [
				'role' => __('Merchant'),
				'name' => $self[$id],
			];
		}
		return $userList;
	}

/** getUsersByAssociatedUser method
 *
 * Users have associated users, this method finds and returns the original users (not the associated users).<br />
 * In other words the user_id (the assignee) parameter will return a list containing all users to which said user_id was assigned as the associated user.<br />
 *
 * @param string $id the assignee user id
 * @return array $users The original users to whom the assignee has been assigned to as associated user
 */
	public function getUsersByAssociatedUser($id) {
		$users = $this->find('all', [
			'conditions' => [$this->alias . '.associated_user_id' => $id],
			'contain' => ['User' => ['fields' => ['id', 'fullname']]]
		]);
		return $users;
	}

/** toggleMainAssocUser method
 *
 * Sets the specified user as the main associated user among all users associated with the user who owns the given compensation id.
 * There can only be one main associated user at a time so this is a toggle functionality.
 *
 * @param string $userId the associated_user_id to be assigned as the main associated user
 * @param string $compensationId the a compensation id 
 * @return boolean false on failure, true on success
 */
	public function toggleMainAssocUser($userId, $compensationId) {
		//get the record id
		$id = $this->field('id', ['associated_user_id' => $userId, 'user_compensation_profile_id' => $compensationId, 'permission_level' => User::ROLE_SM]);
		if ($id === false) {
			return false;
		}
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		//toggle any current main associated users off
		$updated = $this->updateAll(
			['main_association' => false],
			['user_compensation_profile_id' => $compensationId, 'main_association' => true]
		);

		$this->id = $id;
		$saved = $this->save(['main_association' => true]);

		if ($updated && $saved) {
			$dataSource->commit();
			return true;
		} else {
			$dataSource->rollback();
			return false;
		}
	}
/**
 * deleteAllAssocData
 * Removes user association between the owner of the passed user compensation profile id param and the associated user.
 * Also removes all compensation data about the associated user from the owner of the compensation profile.
 *
 * @param string $id a record id for $this
 * @param string $idOfAssociatedUser the User.id that is associated with the owner of the comepensation id
 * @param string $compensationId a user compensation id that is owned by the user with whom the $idOfAssociatedUser is associated
 * @return boolean
 */
	public function deleteAllAssocData($id, $idOfAssociatedUser, $compensationId) {
		if (empty($id) || empty($idOfAssociatedUser) || empty($compensationId)) {
			return false;
		}
		$allModels = App::objects('model');
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		if (!$this->delete($id)) {
			return false;
		};
		foreach ($allModels as $modelName) {
			$model = ClassRegistry::init($modelName);
			$isTargetModel = false;
			try {
				$isTargetModel = ($model->hasField('associated_user_id') && $model->hasField('user_compensation_profile_id'));
			} catch (Exception $e) {
				continue;
			}
			if ($isTargetModel && $modelName !== $this->alias) {
				if (!$model->deleteAll(['associated_user_id' => $idOfAssociatedUser, 'user_compensation_profile_id' => $compensationId], false, false)) {
					$dataSource->rollback();
					return false;
				}
			}
		}
		$dataSource->commit();
		return true;
	}
}
