<?php

App::uses('Component', 'Controller');

class OwnerAccessControlComponent extends Component {

/**
 * Constructor
 *
 * @param ComponentCollection $collection component collection
 */
	public function __construct(ComponentCollection $collection) {
		$this->controller = $collection->getController();
		$this->Model = $this->controller->modelClass;
	}

/**
 * isAuthorized method
 *
 * Checks current user permission to access specific records, based on whether the content belongs to the current user.
 * This Authorization object issues a redirect on access denied.
 *
 * @return boolean
 * @throws BadFunctionCallException
 */
	public function isAuthorizedOwner() {
		if (!isset($this->controller)) {
			throw new BadFunctionCallException('OwnerAccessControlComponent::isAuthorized() is not callable from outside a Controller Class');
		}

		//If no user is logged in then return, we can't check permissions on no one...
		if (!$this->controller->Auth->user('id')) {
			return true;
		}

		/* Allow admings full access */
		if ($this->userIsAdmin($this->controller->Auth->user('id'))) {
			return true;
		}

		/* Check permissions */
		if ($this->_isAllowedOwner() === false) {
			$this->controller->Session->setFlash($this->controller->Auth->authError, 'default', array(
				'class' => 'text-center'));
				$this->controller->redirect($this->controller->Auth->unauthorizedRedirect);

		}
		return true; //Allow access and let other Auth components to do their job.
	}

/**
 * _isAllowedOwner() method
 *
 * Checks if current user is considered an owner of the specific record being accessed.
 * Authorization is based on content ownership.
 *
 * @return boolean true if user is an owner, false if not.
 */
	protected function _isAllowedOwner() {
		$passedParams = $this->controller->request->params['pass'];
		//If no parameters then this is not an access attempt to any specific records.
		//Additionally records are accessed synchronously; therefore ajax requests are always allowed.
		if ($this->controller->request->is("ajax") || empty($passedParams)) {
			return null;
		}
		//Check if Model has a table associated and is not just a stand alone model
		try {
			$this->controller->{$this->Model}->schema();
		} catch (Exception $e) {
			return null;
		}
		//Check if request params contains current user id
		$key = array_search($this->controller->Auth->user('id'), $passedParams);

		//If no user id was found
		if ($key === false) {
			//then check if merchant id is in the params and whether access is granted based on ownership
			if ($this->__canAccessMerchantData($passedParams)) {
				return true;
			} elseif ($this->__canAccessCompData($passedParams)) { //Otherwise if compensation profile access attempted is being made
				return true;
			}
		} elseif ($key !== false) { //If the current user id is present in the parameters
			//Then allow them to see their stuff
			return true;
		}

		return false;
	}
/**
 * __canAccessCompData
 * Checks the passed params as conditions to search for a user_compensation_profile owned by the current user.
 *
 * @param mixed $params params from request
 * @return boolean
 */
	private function __canAccessCompData($params) {
		$UserCompensationProfile = ClassRegistry::init('UserCompensationProfile');
		$result = $UserCompensationProfile->find('count', array(
			'conditions' => array(
				'UserCompensationProfile.id' => $params,
				'UserCompensationProfile.user_id' => $this->controller->Auth->user('id'),
			)
		));
		return (boolean)$result;
	}

/**
 * __canAccessMerchantData method
 *
 * Checks the passed params as conditions to search for a merchant owned by the current user.
 * The current user is considered an "owner" or stakeholder of Merchant if its user id is located in
 * Merchant.user_id
 * Merchant.partner_id
 * Merchant.sm2_user_id
 * Merchant.sm_user_id
 * The params could be a string or an array of ids containing the id of the merchant that the current user is attempting to access.
 *
 * @param mixed $params string or single dimmentional array of UUIDs which may or may not be merchant id's
 * @return boolean True if merchant exists and user is related to it based on conditions; False otherwise.
 */
	private function __canAccessMerchantData($params) {
		$Merchant = ClassRegistry::init('Merchant');
		$CurrModel = ClassRegistry::init($this->Model);
		if ($CurrModel->hasField("merchant_id")) {
			$assocData = $CurrModel->find('first', array(
				'conditions' => array(
					'OR' => array(
						'id' => $params,
						'merchant_id' => $params
					)
				),
				'fields' => array('merchant_id')
			));
			$merchantId = Hash::get($assocData, "{$this->Model}.merchant_id");
		}
		$merchConditions = [];
		if (!isset($merchantId)) {
			foreach ($params as $param) {
				//check for UUIDs
				if ($Merchant->isValidUUID($param)) {
					//potential Merchant id
					$merchantId[] = $param;
					//Check for MIDs
				} elseif (is_numeric($param) && (int)$param > 1) {
					$midDBAs = ['Merchant.merchant_mid LIKE' => '%' . $param];
				} elseif (!is_numeric($param)) {
					$midDBAs = ['Merchant.merchant_dba ILIKE' => '%' . $param . '%'];
				}
			}
			if (!empty($merchantId)) {
				$merchConditions['Merchant.id'] = $merchantId;
			}
			if (!empty($midDBAs)) {
				$merchConditions = array_merge($merchConditions, $midDBAs);
			}
		} else {
			$merchConditions = ['Merchant.id' => $merchantId];
		}
		$merchant = $Merchant->find('first', array(
			'fields' => array(
				'Merchant.user_id',
				'Merchant.sm_user_id',
				'Merchant.sm2_user_id',
				'Merchant.partner_id',
			),
			'conditions' => $merchConditions
		));
		if (empty($merchant)) {
			return false;
		}
		$merchAssocUsers = Hash::filter(Hash::get($merchant, 'Merchant'));
		unset($merchAssocUsers['id']);
		//Check if current user is associated with current merchant
		if (!in_array($this->controller->Auth->user('id'), $merchAssocUsers)) {
			//Check permissions based on associations
			$AssociatedUser = ClassRegistry::init('AssociatedUser');
			//These users are allowed to access this merchant if their associated permission level is this
			$permissionLevels = array(User::ROLE_SM, User::ROLE_SM2, User::ROLE_INSTALLER, User::ROLE_ACCT_MGR);
			$conditions = array(
				'AssociatedUser.user_id' => $merchAssocUsers,
				'AssociatedUser.associated_user_id' => $this->controller->Auth->user('id'),
				'AssociatedUser.role' => $permissionLevels,
			);
			return $AssociatedUser->hasAny($conditions);
		} else {
			return true;
		}
	}

/**
 * userIsAdmin method
 *
 * Checks if a user is an administrator.
 *
 * @param string $userId a user id
 * @return boolean
 */
	public function userIsAdmin($userId) {
		$User = ClassRegistry::init('User');
		return $User->roleIs($userId, $User->userRoles['roles']['Admin']['roles']);
	}

}