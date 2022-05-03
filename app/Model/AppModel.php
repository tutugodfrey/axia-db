<?php

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Model', 'Model');
App::uses('HttpSocket', 'Network/Http');
App::uses('AuthComponent', 'Controller/Component');
App::uses('AccessControlFactory', 'Rbac.Lib/Auth');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

	const VALID_AMOUNT_MIN = '0.0000';
	const VALID_AMOUNT_MAX = '99999999999.9999';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
	);

/**
 * @var int
 */
	public $recursive = -1;

/**
 * {@inheritDoc}
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->_attachListeners();
	}

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeValidate($options = array()) {
		/**
		 * Add custom validation and input zanitation for fields that do not have Model fiels
		 */
		$ModelsFields = [
			'BackEndNetwork' => 'network_description',
			'CancellationFee' => 'cancellation_fee_description',
			'DebitAcquirer' => 'debit_acquirers',
			'OriginalAcquirer' => 'acquirer',
		];

		if (!empty($ModelsFields[$this->alias])) {
			$inputToValidate = Hash::get($this->data, $this->alias.".". $ModelsFields[$this->alias]);
			//Since we are adding this vaidation rule at the parent model level it will always invalidate the input
			//even when the input is valid, therefore check the input first and add validation rule IFF this is false.
			if (!empty($inputToValidate) && $this->inputHasOnlyValidChars(['val' => $inputToValidate]) == false) {
				$validator = $this->validator();
				$validator[$ModelsFields[$this->alias]] = array(
					'input_has_only_valid_chars' => array(
						'rule' => array('inputHasOnlyValidChars'),
						'message' => 'This field is required and must not contain special characters (i.e "<>`()[]"... etc)!',
						'required' => true,
						'allowEmpty' => false,
					)
				);	
			}
		}
		
		return parent::beforeSave($options);
	}

/**
 * Method removes mark up language from strinc such as html tags or script tags
 *
 * @return void
 */
	public function removeAnyMarkUp($str) {
		return preg_replace('/(<([^>]+)>)/i', "", $str);
	}

/**
 * Resuable custom validation rule
 *
 * @return void
 */
	public function inputHasOnlyValidChars($check) {
		$value = array_pop($check);
		//check if data is pre-encrypted 
		$value = ($this->isEncrypted($value))? $this->decrypt($value, Configure::read('Security.OpenSSL.key')) : $value;
		if (empty($value)){
			return true;
		} else {
			return !(bool)preg_match('/(<|>|`|\(|\)|\[|\]|\{|\}|\\\|\/|\^)/', $value);
		}
	}

/**
 * Attach the listeners set at 'ModelEventListeners' Configuration key
 *
 * @return void
 */
	protected function _attachListeners() {
		$listeners = Configure::read("ModelEventListeners.{$this->name}");
		if (is_array($listeners)) {
			foreach ($listeners as $listener) {
				$this->getEventManager()->attach($listener);
			}
		}
	}

/**
 * Detach a listener from the model
 *
 * @param string $listenerKey Key in the 'ModelEventListeners.{modelName}' Configuration array where the listener is set
 * @return void
 */
	public function detachListener($listenerKey) {
		$listenerInstance = Configure::read("ModelEventListeners.{$this->name}.{$listenerKey}");
		if (!empty($listenerInstance)) {
			$this->getEventManager()->detach($listenerInstance);
		}
	}

/**
 * getNumOfMonthsSince
 *
 * Calculates the number of months until today since a given date
 *
 * @param DateTime|string $date DateTime or compatible-string.
 * @param DateTime $mockNow Current date (used in tests).
 * @return int The number of months that have passed.
 */
	public function getNumOfMonthsSince($date, $mockNow = null) {
		if (empty($date)) {
			return 0;
		}
		$now = $mockNow ?: new DateTime('now');
		$then = ($date instanceof DateTime) ? $date : new DateTime($date);
		$diff = $now->diff($then);
		return ($diff->format('%y') * 12) + $diff->format('%m');
	}

/**
 * Validates a percentage value (float) with a precision of 3
 *
 * @param array $check percentage value
 * @throws \RuntimeException Thrown when the regex had an error, see return values here http://php.net/manual/en/function.preg-match.php
 * @return bool
 */
	public function validPercentage($check) {
		if (empty($check)) {
			return false;
		}

		$value = reset($check);
		$errorMessage = __('Must be a valid percentage value with a maximum precision of 3');

		if ((float)$value > 100.000 ||
			(float)$value < 0 ||
			is_bool($value)
		) {
			return $errorMessage;
		}

		$result = preg_match('/^(?:100|\d{0,3})(?:\.\d{1,3})?$/', $value);
		if ($result === 1) {
			return true;
		}

		return $errorMessage;
	}

/**
 * isValidUUID method
 *
 * check is the given string parameter is a valid UUID
 * Implementations should not implement validation, since UUIDs should be in a consistent format across all implementations.
 *
 * @param string $uuid The UUID.
 * @return bool True if valid, false otherwise.
 */
	public static function isValidUUID($uuid) {
		return (bool)preg_match("/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/", $uuid);
	}

/**
 * Custom validation rule, check if field value is equal (===) to another field
 *
 * @param string $check array values
 * @param string $fieldName1 first fieldName
 * @param string $fieldName2 second fieldName
 * @return bool
 */
	public function validateFieldsEqual($check, $fieldName1, $fieldName2) {
		if (!isset($this->data[$this->alias][$fieldName1]) || !isset($this->data[$this->alias][$fieldName2])) {
			return false;
		}
		return $this->data[$this->alias][$fieldName1] === $this->data[$this->alias][$fieldName2];
	}

/**
 * Custom validation rule, check if field can be saved as an amount $$ = DECIMAL 15,4
 *
 * @param array $check The amount value.
 * @return bool
 */
	public function validAmount($check) {
		if (empty($check)) {
			return false;
		}
		$value = reset($check);
		try {
			//using specific ranges given by the Client
			$gtCheck = (bccomp($value, self::VALID_AMOUNT_MIN, 5) >= 0);
			$ltCheck = (bccomp(self::VALID_AMOUNT_MAX, $value, 5) >= 0);
			return Validation::decimal($value) && $gtCheck && $ltCheck;
		} catch(Exception $e) {
			return false;
		}
	}

/**
 * Generic view method for model, throws exception if not found
 *
 * @param mixed $id id of a model
 * @param array $options options values like conditions, limit, joins and so on
 * @throws OutOfBoundsException
 * @return array
 */
	public function view($id, $options = array()) {
		$pkOptions = array(
			'conditions' => array(
				"{$this->alias}.{$this->primaryKey}" => $id,
			),
		);
		$findOptions = Hash::merge($pkOptions, $options);
		$result = $this->find('first', $findOptions);

		if (empty($result)) {
			throw new OutOfBoundsException(__('Invalid %s', $this->alias));
		}

		return $result;
	}

/**
 * checkDecrypPassword method
 * Checks if supplied password matches the one specified in config file
 *
 * @param type $pw user password
 * @return bool
 */
	public function checkDecrypPassword($pw) {
		$curUserId = $this->_getCurrentUser('id');
		if ($this->alias == 'User') {
			$isAllowed = $this->isAllowedSensitiveClientData($curUserId, urldecode(base64_decode($pw)));
		} else {
			$isAllowed = ClassRegistry::init('User')->isAllowedSensitiveClientData($curUserId, urldecode(base64_decode($pw)));
		}
		return $isAllowed;
	}

/**
 * encryptFields method
 *
 * Encrypts the data for all the fields listed in the first parameter
 *
 * @param array $fields single dimentional array containing a list of field names which are also the keys found in $modelData
 * @param array $modelData single dimentional array containing a set of data where the fields to encrypt will be found
 * @return array
 * @throws \InvalidArgumentException
 */
	public function encryptFields($fields, $modelData) {
		if (!is_array($fields) || !is_array($modelData)) {
			throw new InvalidArgumentException("Function expects arguments as arrays");
		}

		foreach ($fields as $key) {
			$modelData[$key] = $this->encrypt($modelData[$key], Configure::read('Security.OpenSSL.key'));
		}
		return $modelData;
	}

/**
 * decryptFields method
 *
 * Searches for any encrypted data in the provided array argument and decrypts it.
 *
 * @param array $modelData single dimentional array containing a subset of encrypted data
 * @return array with original data and unencripted fields and a new array entry containing a comma delimeted string of the fields that were decrypted
 * @throws InvalidArgumentException
 */
	public function decryptFields($modelData) {
		if (!is_array($modelData)) {
			throw new InvalidArgumentException("Function expects argument as array");
		}
		//extract fields to decrypt
		$arrKeys = $this->extractEncryptedFieldsKeys($modelData);
		if (!empty($arrKeys)) {
			foreach ($arrKeys as $val) {
				if (empty($modelData[$val])) {
					continue;
				}
				$decryptedVal = @$this->decrypt($modelData[$val], Configure::read('Security.OpenSSL.key'));
				if ($decryptedVal !== false) {//check of descripting is successful 
					$modelData[$val] = $decryptedVal;
				}
			}
			$modelData['encrypt_fields'] = implode(',', $arrKeys);
		}
		return $modelData;
	}

/**
 * isEncrypred method
 * Attempts to decrypt on success true will be returned
 * If decryption attempt fails false will be returned
 *
 * @param string $val value suspected to be encrypted
 * @return bool On success true will be returned false descryption fails.
 */
	public function isEncrypted($val) {
		//must use === in case non boolean zero is returned
		if (@$this->decrypt($val, Configure::read('Security.OpenSSL.key')) === false) {
			return false;
		}
		return true;
	}

/**
 * extractEncryptedFieldsKeys method
 *
 * Extract the array keys of any encrypted values in the provided single dimentional array.
 * This method assumes the encrypted data is in hexadecimal format.
 *
 * @param array $modelData single dimentional array containing a subset of encrypted data
 * @return array containing the keys of the encrypted fields
 * @throws InvalidArgumentException
 */
	public function extractEncryptedFieldsKeys($modelData) {
		if (!is_array($modelData)) {
			throw new InvalidArgumentException("Function expects argument as array");
		}

		$encryptFields = array_intersect_key($modelData, Configure::read('App.encDbFields'));
		$keysArr = array_keys($encryptFields);
		return $keysArr;
	}

/**
 * Encrypt method used for highly sensitive customer data (Method imported from legacy database)
 *
 * @param string $data data that will be encrypt
 * @param string of bytes $key  key
 * @return string $edata
 */
	public function encrypt($data, $key) {
		$key = base64_decode($key);
		$plaintext = $data;
		$cipher = Configure::read('Security.OpenSSL.cipher');
		$iv = base64_decode(Configure::read('Security.OpenSSL.iv'));
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
		$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);

		return $ciphertext;
	}

/**
 * Method to decrypt data using local encryption method and $salt
 *
 * @param string $ecryptedData data that will be decrypted
 * @param string of bytes $key  key
 * @return mixed decrypted data | boolean false when decryption fails
 */
	public function decrypt($ecryptedData, $key) {
		$key = base64_decode($key);
		$c = base64_decode($ecryptedData);
		$cipher = Configure::read('Security.OpenSSL.cipher');
		$ivlen = openssl_cipher_iv_length($cipher);
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$decryptedData = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);

		if ($decryptedData === false) {
			return false;
		}
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		//PHP 5.6+ timing attack safe comparison
		if (hash_equals($hmac, $calcmac)) {
		    return $decryptedData;
		}
		return false;
	}

/**
 * Make a tax rate request to API  http://api.zip-tax.com/request/v10
 *
 * @param string $zipCode zipCode value
 * @return string $response JSON enconded string
 */
	public function requestAPITaxRate($zipCode, $state = null, $city = null) {
		//We only want tax rates for us zip codes
		if (Validation::postal($zipCode, null, 'us')) {
			$reqParams = array('key' => 'T3PG49H', 'postalcode' => $zipCode, 'state' => $state, 'city' => $city, 'format' => 'JSON');
			$response = $this->_getHttpSocket()->get("https://api.zip-tax.com/request/v20", $reqParams);
		} else {
			//Set emtpy JSON array
			$response = '{}';
		}
		return $response;
	}

/**
 * HttpSocket mocker
 *
 * @return HttpSocket
 */
	protected function _getHttpSocket() {
		return new HttpSocket();
	}

/**
 * csvPrepString
 * Prepares the string with specified delimited for a csv data export
 *
 * @param string $str string value
 * @param string $comma option value
 * @return string
 */
	public function csvPrepString($str, $comma = ' ') {
		$temp = preg_replace('/\s+/', ' ', trim($str));//replace any \s \n or \r with just spaces
		$temp = str_replace(',', $comma, $temp);
		return $temp;
	}

/**
 * Generic method to get the model record without related models information
 *
 * @param mixed $id id of a model
 * @param array $options options values like conditions, limit, joins and so on
 * @return array
 */
	public function get($id, $options = array()) {
		$defaultOptions = array(
			'contain' => false,
		);
		$findOptions = Hash::merge($defaultOptions, $options);
		return $this->view($id, $findOptions);
	}

/**
 * Generic method to get the model record list
 *
 * @return array
 */
	public function getList() {
		return $this->find('list', array(
			'order' => array("{$this->alias}.{$this->displayField}" => 'ASC')
		));
	}

/**
 * RbacIsPermitted
 * Provides access to RBAC's isPermitted method from all Models. <br />
 * No second parameter exists to check modules permissions therefore the full path is required.
 *
 * @param string $actionPath full path to the action <br />
 *		'app/action/[Controller]/[actionName]/'<br />
 *And to the module i.e.:<br/>
 *		'app/action/[Controller]/view/module/[moduleName]
 * @return bool
 */
	public function rbacIsPermitted($actionPath) {
		// if (!Configure::read('Rbac.enabled')) {
		// 	return true;
		// }
		$Rbac = AccessControlFactory::get($this->_getCurrentUser('id'));
		return $Rbac->isPermitted($actionPath);
	}

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
 * Utility function to get the "now" date (mocked in unit tests)
 *
 * @return \DateTime
 */
	protected function _getNow() {
		return new DateTime('now');
	}

/**
 * Function check if the string param is the name an existing model object
 *
 * @param string $modelName name of a model to check
 * @return bool
 */
	public function modelExists($modelName) {
		return in_array($modelName, App::objects('model'));
	}

/**
 * Function checks if every element of a one dimentional array is a number
 *
 * @param array $numsArray name of a model to check
 * @return bool
 */
	public function arrayIsNumeric($numsArray) {
		return (array_sum(array_map("is_numeric", $numsArray)) === count($numsArray));
	}

/**
 * percentToDec method
 * 
 * Function converts percentages to its decimal form.
 * 
 * @param float $number a number that may be a percentage
 * @return bool
 */
	public function percentToDec($number) {
		if (!empty($number)) {
			$number /= 100;
		}
		return $number;
	}

/**
 * getRandHash method
 * Generates a pseudorandom md5 hash string which can be used as a unique idenfier.
 * Should not to be used as a password!
 * 
 * @return string md5 hash
 */
	public function getRandHash() {
		$alphaNum = implode('', array_merge(range('a', 'z'), range(0, 9)));
		return md5(str_shuffle($alphaNum));
	}

/**
 * getUserSearchFilterArgs method
 * 
 * In order to search by manager/partner/referrer/reseller the user id foreing key must change
 * dynamically since each type of user has its own.
 * This method allows for dynamic search filter arguments. Determines the proper search filter arguments 
 * that should be used by the searchByUserIdBehavior such as which field should be used and what type.
 * The Model inheriting and calling this method must implement searchByUserIdBehavior.
 * 
 * @param array $complexUserIdArray array containing User::<PREFIX_TYPE> and a user id
 * @return string Possible return values: user_id, partner_id, referer_id/referrer_id, reseller_id
 */
	public function getUserSearchFilterArgs($complexUserIdArray) {
		$User = ClassRegistry::init('User');
		$prefix = Hash::get($complexUserIdArray, 'prefix');
		$userId = Hash::get($complexUserIdArray, 'id');
		$curUserId = $this->_getCurrentUser('id');
		$curUserRoles = $User->getUserRolesList($curUserId);

		//by default use searByUserId behavior method
		$filterArgs = [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"' . $this->alias . '"."user_id"'
		];

		switch ($prefix) {
			case User::PREFIX_ENTITY:
				if ($this->filterArgs['user_id']['searchByMerchantEntity']) {
				 	$filterArgs = [
						'type' => 'subquery',
						'method' => 'searchByUserId',
						'field' => '"Merchant"."id"',
						'searchByMerchantEntity' => true
					];
				}
				break;
			case User::PREFIX_PARENT:
				$filterArgs = [
					'type' => 'query',
					'method' => 'searchByParentUser',
				];
				break;
			case User::PREFIX_USER:
				$role = $User->getUserRolesList($userId);

				if (!empty($role[User::ROLE_PARTNER]) || !empty($role[User::ROLE_REFERRER]) || !empty($role[User::ROLE_RESELLER])) {
					if (!empty($curUserRoles[User::ROLE_SM]) || !empty($curUserRoles[User::ROLE_SM2])) {
						$filterArgs = [
							'type' => 'query',
							'method' => 'searchByUserAndManager',
						];
					} else {
						$filterArgs = [
							'type' => 'query',
							'method' => 'searchByExternalUser',
						];
					}
				} else {
					if ($curUserId !== $userId && (!empty($curUserRoles[User::ROLE_SM]) || !empty($curUserRoles[User::ROLE_SM2]))) {
						//This if block will execute for non admin users logged in
						$filterArgs = [
							'type' => 'query',
							'method' => 'searchByUserAndManager',
						];
					}
				}
				break;
		} //switch

		return $filterArgs;
	}

/**
 * extractCsvToArray 
 * Extracts CSV data.
 * Returns a 2D array containig each row of CSV data in arrays at every index entry of the top dimension
 * 
 * @param string $filePath to csv file to extract data from
 * @param boolean $deleteFile whether to keep the file for later use or delete it
 * @return array
 * @throws Exception
 */
	public function extractCsvToArray($filePath, $deleteFile = true, $excludeHeaders = true) {
		$fh = @fopen($filePath, 'rb');
		if ($fh === false) {
			throw new Exception ('ERROR: Unable to open file!');
		}
		if ($excludeHeaders) {
			$indices = fgetcsv($fh, 10000);
		}
		while ($row = fgetcsv($fh, 10000)) {
			$dataArray[] = array_map('trim', $row);
		}
		fclose($fh);

		if ($deleteFile) {
			unlink($filePath);
		}
		return $dataArray;
	}

/**
 * setDecimalsNoRounding 
 * Changes the number of decimals in a float/double to the supplied precicion without rounding
 * Example for two decimals: 0.01 * (int)($value*100) 
 * 
 * @param float $number a numbe with arbitrary number of decimals
 * @param int $precision defaults to 2 decimals
 * @return float
 * @throws InvalidArgumentException
 */
	public function setDecimalsNoRounding($number, $precision = 2) {
		if (!is_numeric($precision) || $precision < 0) {
			throw new InvalidArgumentException('Invalid argument! Must be a number greater than or equal to zero');
		}
		$decimalToRight = '1' . str_repeat('0', $precision);
		if ($precision == 0) {
			$decimalToLeft = '1.0';
		} elseif ($precision > 0) {
			$decimalToLeft = '0.'. str_repeat('0', $precision - 1) . '1';
		}
		
		return  $decimalToLeft * (int)($number*$decimalToRight);
	}


/**
 * refreshAxiaApiJsonForSwagger
 * Creates OpenAPI definitions file in JSON and saves it in the swagger UI folder
 * which will be used to autogenerate AxiaMed's API documentation.
 * The scanner searches all files for Doctrine annotations in the specified directory and generates a json file.
 *
 * @param boolean $refreshNow
 * @return void
 */
	public function refreshAxiaApiJsonForSwagger() {
		require(APP . "Vendor/autoload.php");
		//Including paths/files known to have Doctrine annotations to avoid too many uncessesary scans
		$includePaths = [
			APP.'Model/Merchant.php',
			APP.'Controller',
		];

		$openapi = \OpenApi\scan($includePaths);
		$jsonData = $openapi->toJson();

		$path = WWW_ROOT . 'AxiaApiDocs' . DS;
		$fp = @fopen($path . 'openapi_axia.json', 'w');
		if ($fp === false) {
			throw new Exception("Internal Error: Unable to generate JSON definitin for swagger --cannot open file openapi_axia.json");
		}
		fwrite($fp, $jsonData);
		fclose($fp);
	}
	/**
	 * sanitizeNumericStr 
	 * Removes non digits from string representation of a numeric float or integer
	 * while making sure any negtive sign or decimal points, which are non digits are preserved
	 * @param  string $val the string representation of a float or 
	 * @return string
	 */
	public function sanitizeNumStr($val) {
		if ($val == '') {
			//since 0 == '' and we want 0 returned rather than null do a second check
			return ($val === 0)? 0: null;
		}
		//replace parenthetical negative with nevative sign and just in case both
		$val = preg_replace("/[\(|-]+/", "-", $val);
		$val = preg_replace("/[$|%|,|'|\"|=\)]+/", "", $val);
		//only return numeric values that result from sanitazing parameter
		return (is_numeric($val))? $val : null;
	}
}
