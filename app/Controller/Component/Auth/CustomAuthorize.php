<?php
App::uses('RbacAuthorize', 'Rbac.Controller/Component/Auth');

/**
 * An authorization adapter for AuthComponent.	Provides the ability to authorize using the AccessControl
 */
class CustomAuthorize extends RbacAuthorize {

/**
 * Authorize a user using the AccessControl.
 *
 * @param array $user The user to authorize
 * @param CakeRequest $request The request needing authorization.
 * @return boolean
 */
	public function authorize($user, CakeRequest $request) {
		$enabled = Configure::read('Rbac.enabled');
		if (!isset($enabled) || $enabled === false) {
			return true;
		}
		$AccessControl = $this->_getAccessControlFactory($user);
		if (method_exists($AccessControl, 'isPermitted')) {
			return $AccessControl->isPermitted($this->action($request));
		}
		return false;
	}

/**
 * For testing
 *
 * @param type $user
 */
	protected function _getAccessControlFactory($user) {
		if (isset($this->AccessControl)) {
			return $this->AccessControl;
		} elseif (empty($user)) {
			return null;
		}
		return AccessControlFactory::get($user);
	}
}
