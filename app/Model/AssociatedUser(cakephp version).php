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
	public $validate = array(
		'role' => array(
			'maxRoleAssociation' => array(
				'rule' => array('maxRoleAssociation'),
				'message' => 'You can not associate more users with the role selected'
			)
		),
		'associated_user_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must select an associated user'
			),
			'checkUniqueAssociation' => array(
				'rule' => array('checkUniqueAssociation'),
				'message' => 'This user is already associated',
			)
		),
		'permission_level' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must select a permission level'
			)
		)
	);

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
		'UserAssociated' => array(
			'className' => 'User',
			'foreignKey' => 'associated_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UserParameter' => array(
			'dependent' => true,
		),
		'ResidualParameter' => array(
			'dependent' => true,
		),
		'ResidualTimeParameter' => array(
			'dependent' => true,
		),
	);

/**
 * Custom validation rule. Check if the user associated is unique.
 * @return boolean
 */
	public function checkUniqueAssociation($check) {
		$condition = array(
			"{$this->alias}.user_id" => $this->data[$this->alias]['user_id'],
			"{$this->alias}.associated_user_id" => $this->data[$this->alias]['associated_user_id']
		);
		$result = $this->find('count', array('conditions' => $condition));
		return ($result === 0);
	}

/**
 * Custom validation rule. Check max amount of users with a role within the same association.
 * @return boolean
 */
	public function maxRoleAssociation($check) {
		$max = Configure::read('AssociatedUserRoles.' . str_replace(' ', '', $this->data[$this->alias]['role']) . '.maxAssociated');
		//check unlimited maxAssociated
		if ($max === null) {
			return true;
		}

		$condition = array(
			"{$this->alias}.user_id" => $this->data[$this->alias]['user_id'],
			"{$this->alias}.role" => $this->data[$this->alias]['role']
		);
		$result = $this->find('count', array('conditions' => $condition));
		return ($result < $max);
	}

	/**
	 * Get list of names and roles for associated users
	 *
	 * @param int $userId ID of user to get associated users for
	 * @param bool $includeSelf True to also associate user to themself, otherwise false returns associations from database only.
	 */
	public function getAssociatedUsers($userId = null, $includeSelf = false) {
		$users = $this->find('all', array(
			'conditions' => array('AssociatedUser.user_id' => $userId),
			'contain' => array(
				'UserAssociated' => array(
					'fields' => array('UserAssociated.user_first_name', 'UserAssociated.user_last_name')
				),
			),
			'fields' => array('AssociatedUser.role'),
		));
		$userList = array();
		foreach ($users as $user) {
			$userList[Hash::get($user, 'UserAssociated.id')] = array(
				'role' => __(Hash::get($user, 'AssociatedUser.role')),
				'name' => String::insert(':user_first_name :user_last_name', $user['UserAssociated']),
			);
		}
		if ($includeSelf) {
			$self = ClassRegistry::init('User')->find('list', array(
				'conditions' => array('User.id' => $userId),
			));
			$id = key($self);
			$userList[$id] = array(
				'role' => __('Merchant'),
				'name' => $self[$id],
			);
		}
		return $userList;
	}
}
