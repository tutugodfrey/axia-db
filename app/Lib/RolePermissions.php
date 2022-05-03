<?php
App::uses('ResidualReport', 'Model');
App::uses('CommissionReport', 'Model');
App::uses('CommissionPricing', 'Model');
App::uses('User', 'Model');

/**
 * Permission utilities to be used later in helper and/model
 */
class RolePermissions extends CakeObject {

/**
 * Posible statuses for Commission and Residual reports
 *
 * @var string
 */
	const REPORT_STATUS_ACTIVE = 'A';
	const REPORT_STATUS_INACTIVE = 'I';

/**
 * Utility function to get the user id (mocked in unit tests)
 *
 * @param string $key key to retrieve from current logged in user
 * @return string
 */
	protected function _getCurrentUser($key = null) {
		return AuthComponent::user($key);
	}

/**
 * getRepFilterConditions method
 *
 * Note: Moved from AppModel
 *
 * This method works with the values set by the GUIbuilderComponent::buildRepFilterOptns
 * Sets the conditions of a query based on the user_id or group option passed in the parameter.
 * The conditions are intended to filter the search to show only the owners of a certain dataset.
 * Depending on the current user role the associated users will also be inserted into the conditions array.
 * - For Admins: no conditions will be made, allowing access to all content.
 * - For Rep/PartnerRep: conditions will only include this user (current user).
 * - For Managers: conditions will allow themselves and all the users he/she manages
 * - For Installers: same as manager.
 *
 * The Model implementing the returned conditions must implement an INNER JOIN where User.id = Model.user_id,
 * so that results are actually limited.
 *
 * @param string $userId User id
 * @throws InvalidArgumentException
 * @throws NotFoundException
 *
 * @return array $conditions
 */
	public function getRepFilterConditions($userId = null) {
		$UserModel = ClassRegistry::init('User');
		$groupSelected = false;
		if (!empty($userId)) {
			if (!is_string($userId)) {
				throw new InvalidArgumentException('Invalid user id');
			}
			// Check if group prefix exists
			if (substr_compare($userId, GUIbuilderComponent::GRP_PREFIX, 0, 2) === 0) {
				$userId = substr($userId, 2);
				$groupSelected = true;
			}

			$UserModel->id = $userId;
			if (!$UserModel->exists()) {
				throw new NotFoundException(__('User not found'));
			}
		}

		$currentUserId = $this->_getCurrentUser('id');
		$isAdmin = $UserModel->isAdmin($currentUserId);
		$conditions = array();
		if (empty($userId) && !$isAdmin) {
			// By default use currently logged in user's id
			$conditions['User.id'] = $currentUserId;
		} elseif (!empty($userId)) {
			$conditions['User.id'] = $userId;
			if ($groupSelected) {
				// Overwrite conditions and include the parent user
				$conditions = array(
					array(
						'AND' => array(
							array(
								'OR' => array(
									array('User.id' => $userId),
									array('AssociatedUser.associated_user_id' => $userId)
								)
							)
						)
					)
				);
			}
		}
		// For security, always include these in case user tries to spoof the URL query to access data he's not permitted
		if (!$isAdmin) {
			$conditions[0]['AND'][] = array(
				'OR' => array(
					array('User.id' => $currentUserId),
					array('AssociatedUser.associated_user_id' => $currentUserId)
				)
			);
		}

		return $conditions;
	}

/**
 * Get the statuses that the user is allowed to use on the reports
 *
 * @param string $userId User Id
 * @throws InvalidArgumentException
 *
 * @return array with the allowed status list
 */
	public static function getAllowedReportStatuses($userId) {
		if (empty($userId)) {
			throw new InvalidArgumentException(__('Invalid user Id'));
		}

		$UserModel = ClassRegistry::init('User');
		if ($UserModel->roleIs($userId, array(User::ROLE_ADMIN_I, User::ROLE_ADMIN_II, User::ROLE_ADMIN_III))) {
			return array(
				self::REPORT_STATUS_ACTIVE,
				self::REPORT_STATUS_INACTIVE
			);
		} else {
			return array(self::REPORT_STATUS_ACTIVE);
		}
	}

/**
 * Return the commission report permission matrix for the user.
 * The array have the values which the user have access to
 *
 * @param string $userId User Id
 * @param string $reportType different reports have different permissons.
 * @throws InvalidArgumentException
 * @throws ForbiddenException
 * @return array
 */
	public static function reportPermissionsMatrix($userId, $reportType = '') {
		if (empty($userId)) {
			throw new InvalidArgumentException(__('Invalid user Id'));
		}
		$UserModel = ClassRegistry::init('User');
		if ($reportType === ResidualReport::SECTION_RESIDUAL_REPORTS) {
			$permissions = Configure::read('ResidualReportPermissions');
		} else {
			$permissions = Configure::read('CommissionReportPermissions');
		}
		$roles = $UserModel->getRoles();
		$userPermissions = array();
		foreach ($roles as $role => $name) {
			if ($UserModel->roleIs($userId, $role)) {
				$userPermissions = Hash::get($permissions, $role);
				break;
			}
		}
		if (empty($userPermissions)) {
			throw new ForbiddenException(__('This user can not access the commission report'));
		}
		return $userPermissions;
	}

/**
 * Filter the data to be returned to the commission report based on the current user role
 * Permission file can be found at "app/Config/commission_report_permissions.php"
 *
 * @param string $reportType Report type (check constants in CommissionReport and CommissionPricing)
 * @param string $userId User Id
 * @param array $data Report section data
 * @return array with the allowed data
 */
	public static function filterReportData($reportType, $userId, $data) {
		$permissions = self::reportPermissionsMatrix($userId, $reportType);

		$sectionPermissions = Hash::get($permissions, $reportType);
		if (empty($sectionPermissions)) {
			return [];
		}

		switch ($reportType) {
			case ResidualReport::SECTION_RESIDUAL_REPORTS:
			case CommissionReport::SECTION_COMMISION_REPORTS:
			case CommissionPricing::REPORT_COMMISION_MULTIPLE:
				// Check if the array data is associative or sequential
				if (count(array_filter(array_keys($data), 'is_string')) > 0) {
					$data = self::_filterRow($data, $sectionPermissions);
				} else {
					foreach ($data as $index => $row) {
						$data[$index] = self::_filterRow($row, $sectionPermissions);
					}
				}
				break;

			case CommissionReport::SECTION_INCOME:
			case CommissionReport::SECTION_ADJUSTMENTS:
			case CommissionReport::SECTION_NET_INCOME:
				$data = self::_filterRow($data, $sectionPermissions);
				break;
		}

		return $data;
	}

/**
 * Removes the columns from the data row that the user dont have access to
 *
 * @param string $rowData Values from a report row (or single row section)
 * @param string $sectionPermissions Section permissions
 * @return array with the allowed data
 */
	protected static function _filterRow($rowData, $sectionPermissions) {
		foreach ($rowData as $key => $value) {
			if (is_numeric($key)) {
				continue;
			}
			if (!in_array($key, $sectionPermissions)) {
				unset($rowData[$key]);
			}
		}
		return $rowData;
	}
}
