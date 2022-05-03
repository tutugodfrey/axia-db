<?php

App::uses('FormAuthenticate', 'Controller/Component/Auth');

class CustomAuthenticate extends FormAuthenticate {

/**
 * Settings for this object.
 *
 * - `fields` The fields to use to identify a user by.
 * - `userModel` The model name of the User, defaults to User.
 * - `scope` Additional conditions to use when looking up and authenticating users,
 *    i.e. `array('User.is_active' => 1).`
 * - `recursive` The value of the recursive key passed to find(). Defaults to 0.
 * - `contain` Extra models to contain and store in session.
 *
 * @var array
 */
	public $settings = array(
		'fields' => array(
			'username' => 'username',
			'password' => 'password'
		),
		'userModel' => 'User',
		'scope' => array(
			'User.active' => 1,
			'User.is_blocked' => false,
		//'User.email_verified' => true,
		),
		'recursive' => -1,
		'contain' => array(
		),
		'userFields' => null,
		'passwordHasher' => array(
			'className' => 'Shim.Fallback',
			'hashers' => array(
				'Shim.Modern',
				'Simple' => array(
					'className' => 'Simple',
					'hashType' => 'sha1',
				),
				'Md5',
			),
		),
	);

/**
 * Custom authentication override, use email / username for login
 *
 * @param CakeRequest $request request
 * @param CakeResponse $response response
 * @return boolean
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$userModel = $this->settings['userModel'];
		list(, $model) = pluginSplit($userModel);

		$fields = $this->settings['fields'];
		if (!$this->_checkFields($request, $model, $fields)) {
			return false;
		}

		$username = array(
			'OR' => array(
				$model . '.username' => $request->data[$model][$fields['username']],
				$model . '.user_email' => $request->data[$model][$fields['username']]
			),
		);
		$user = $this->_findUser($username, $request->data[$model][$fields['password']]);
		return $user;
	}

}
