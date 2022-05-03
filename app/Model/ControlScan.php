<?php
App::uses('AppModel', 'Model');
App::uses('HttpSocket', 'Network/Http');
App::uses('ApiConfiguration', 'Model');
App::uses('Merchant', 'Model');
App::uses('AddressType', 'Model');
App::uses('MerchantPci', 'Model');

/**
 * ControlScan
 * This is an itegration with ControlScan's SecureEdge 2.05 API.
 * 2020-08-11
 * 
 */
class ControlScan extends AppModel {
/**
 * Path to the ControlScan API endpoints
 *
 * @constant API_PATH
 */
	const API_PATH = '/papi/v1';
/**
 * ControlScan Boarding Statuses returned from
 * Merchant Boarding Status Response 
 *
 * @constant
 */
	const BOARD_PEND = 'Processing';
	const BOARD_DONE = 'Success';
	const BOARD_ERR = 'Error';
/**
 * This is a stand alone model class requires no table
 *
 * @property useTable
 */
	public $useTable = false;

/**
 * This property will be set to become HttpSocket object
 * to be used like this $this->api->get/post/[...](...)
 *
 * @property request
 */
	public $api = null;

/**
 * This property will be set from data stored in ApiConfiguration's table
 *
 * @property host
 */
	public $host = null;

/**
 * This property will be set from data stored in ApiConfiguration's table
 * 
 * @property host
 */
	private $__secretKey = null;

/**
 * This property will be set from data stored in ApiConfiguration's table
 * 
 * @property host
 */
	public $apiConfig = null;

/**
 * Name of API configuration
 *
 * constants
 */
	const CSCAN_CONFIG_NAME = 'controlscan';
	const CSCAN_CONFIG_NAME_DEV = 'controlscan sandbox';

/**
 * Constructor
 *
 * @param mixed $id Model ID
 * @param string $table Table name
 * @param string $ds Datasource
 * @access public
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->setApiConfig();
		if (!empty($this->apiConfig)) {
			$this->api = new HttpSocket();
			$this->setRequestHeaders();
			$this->setHost();
			$this->setSecretKey();
		} else {
			$this->api = false;
		}
	}
/**
 * setApiConfig
 * Sets $apiConfig member @var depends on ApiConfiguration record for ControlScan to exist
 * @return void
 */
	public function setApiConfig(){
		$this->apiConfig = $this->getApiConfig();
	}
/**
 * setApiConfig
 * Sets $host member @var depends on ApiConfiguration record for ControlScan to exist
 * @return void
 */
	public function setHost(){
		$this->host = $this->apiConfig['ApiConfiguration']['instance_url'];
	}
/**
 * setApiConfig
 * Sets $__secretKey member @var depends on ApiConfiguration record for ControlScan to exist
 * @return void
 */
	public function setSecretKey(){
		$this->__secretKey = $this->apiConfig['ApiConfiguration']['client_secret'];
	}
/**
 * setApiConfig
 * Sets $api config request headers unique to and required by ControlScan API
 * @return void
 */
	public function setRequestHeaders(){
		$this->api->config['request']['header']['CS-Access-Key'] = $this->apiConfig['ApiConfiguration']['client_id'];
		$this->api->config['request']['header']['CS-Nonce'] = $this->generateNonce();
		$this->api->config['request']['header']['CS-DateTime'] = gmdate("Y-m-d H:i:s");
	}
/**
 * setApiConfig
 * Returns $__secretKey member @var
 * @return string
 */
	public function getSecretKey() {
		return $this->__secretKey;
	}

/**
 * generateNonce
 * Generates a unique pseudo-random alphanumeric string less than 128 bytes long
 * to be use for CS-Noonce header
 * 
 * @return string
 */
	public function generateNonce() {
		$alphanum = str_replace('-', '', CakeText::uuid() . CakeText::uuid() . CakeText::uuid());
		return str_shuffle($alphanum);
	}

/**
 * getApiConfig
 * Returns API connection configuration metadata.
 * Production config will be returned in production otherwise sandbox configuration will be returned
 *
 * @return array containing ApiConfiguration model metadata about this external API
 */
	public function getApiConfig() {
		$conditions['configuration_name'] = self::CSCAN_CONFIG_NAME;
		if (Configure::read('debug') > 0) {
			$conditions['configuration_name'] = self::CSCAN_CONFIG_NAME_DEV;
		}
		$ApiConfiguration = ClassRegistry::init('ApiConfiguration');
		return $ApiConfiguration->find('first', ['conditions' => $conditions]);
	}

/**
 * getSignatureParams
 * Adds CS-Nonce and CS-DateTime request headers to the request parameters and returns it.
 * The returned array should be used as the parameters part of the request signature creation.
 * 
 * @param  array $requestHeaders Array of HTTP request headers which must include CS-Nonce and CS-DateTime 
 * @param  array  $requestParams Array of HTTP request parameters
 * @return array the request paramsmerged 
 */
	public function createSignatureParams($requestHeaders, $requestParams = []) {
		if (empty($requestHeaders['CS-Nonce']) || empty($requestHeaders['CS-DateTime'])) {
			return null;
		}
		$params['CS-Nonce'] = $requestHeaders['CS-Nonce'];
		$params['CS-DateTime'] = $requestHeaders['CS-DateTime'];
		return array_merge($params, $requestParams);
	}
/**
 * Sign the incoming request to generate a signature
 *
 * @param array $params Array of HTTP request parameters
 * @param string $secret Secret Key
 * @param string $method the HTTP method uses: GET, POST, PUT, DELETE
 * @param string $path The URI of the api path
 * @return string
 */
	public function signRequest($params,$secret,$method,$path) {
		/**
		* passes the input form and get parameters, the path and the
		* HTTP METHOD to the generateParamString method and gets in
		* return the signature string that is signed with the partner's
		* Secret Key to produce a signature string
		*/
		$sig_str = $this->generateParamString($params,$path,$method);

		//Check whether this is a request to test validity of signature
		//and if so add required header.
		if (strpos($path, 'test_signature') !== false) {
			$this->api->config['request']['header']['Cs-Sig-String'] = base64_encode($sig_str);
		}
		/**
		* The signature string is signed with the key using the hash_hmac
		* method, using * the 'sha256' algorithm.
		*/
		$signature = hash_hmac('sha256',$sig_str, $secret);
		return $signature;
	}

/**
 * Takes the Parameters of the request, the path and the method and
 * generates a signature string that will be encoded with the secret key
 *
 * @param array $params An array of HTTP request parameters
 * @param string $path The URI of the request
 * @param string $method The HTTP VERB (POST, GET, PUT, DELETE)
 * @return string
 */
	public function generateParamString($params,$path,$method){
		/**
		* First thing we do is set every key of the input form
		* parameters to lowercase. The values can stay as they are.
		*/
		$new_params = [];
		foreach($params as $k => $v){
			$new_params[strtolower($k)]=$v;
		}

		/**
		* Now we sort the elements of this array by their key
		*/
		ksort($new_params);

		//We concatenate each form parameter with an "="
		$param_str = "";
		foreach($new_params as $p => $pv){
			$param_str .=$p."=".$pv;
		}
		
		// If there is not a starting slash, we add it
		if (substr($path, 0, 1) !== '/') {
			$path = "/".$path;
		}
		/**
		* Everything gets concatenated in its proper order
		*/
		$sig_str = $method . $path. $param_str;
		/**
		* To keep things manageable, we cap the signature string
		* at 1000 characters
		*/
		$signature_length = 1000;
		if(strlen($sig_str)> $signature_length){
			$str = substr($sig_str,0,$signature_length);
		} else {
			$str = $sig_str;
		}
		
		//Return this string for signing.
		return $str;
	}

/**
 * boardMerchant
 * Make POST request to board a single merchant
 * Endpoint: /papi/v1/merchant
 * 
 * @param  string $merchantId is the UUID of the merchant to find and sent to ControlScan
 * @return void
 */
	public function boardMerchant($merchantId) {
		//Check api var was properly set. This could be empty if no ApiConfiguration exists
		if (empty($this->api)) {
			return;
		}
		$Merchant = ClassRegistry::init('Merchant');
		$data = $Merchant->find('first', [
			'fields' => [
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Merchant.merchant_contact',
				'((CASE WHEN "Merchant"."merchant_email" NOT LIKE \'%@%\' THEN null ELSE "Merchant"."merchant_email" END))as "Merchant__merchant_email"',
				$Merchant->User->getFullNameVirtualField('User') . ' as "User__fullname"',
				'AddressBus.address_street',
				'AddressBus.address_city',
				'AddressBus.address_state',
				'AddressBus.address_zip',
				'AddressBus.address_phone',
				'AddressCorp.address_phone',
				'MerchantOwner.owner_name',
			],
			'conditions' => ['Merchant.id' => $merchantId],
			'joins' => [
				[
					'alias' => 'User',
					'table' => 'users',
					'type' => 'LEFT',
					'conditions' => [
						'"Merchant"."user_id" = "User"."id"'
					]
				],
				[
					'alias' => 'MerchantOwner',
					'table' => 'merchant_owners',
					'type' => 'LEFT',
					'conditions' => [
						'"Merchant"."id" = "MerchantOwner"."merchant_id"'
					]
				],
				[
					'alias' => 'AddressBus',
					'table' => 'addresses',
					'type' => 'LEFT',
					'conditions' => [
						'"Merchant"."id" = "AddressBus"."merchant_id"',
						'AddressBus.address_type_id' => AddressType::BUSINESS_ADDRESS
					]
				],
				[
					'alias' => 'AddressCorp',
					'table' => 'addresses',
					'type' => 'LEFT',
					'conditions' => [
						'"Merchant"."id" = "AddressCorp"."merchant_id"',
						'AddressCorp.address_type_id' => AddressType::CORP_ADDRESS
					]
				],
			]
		]);

		$phone = null;
		if (!empty($data['AddressBus']['address_phone'])) {
			$phone = $data['AddressBus']['address_phone'];
		} elseif (!empty($data['AddressCorp']['address_phone'])) {
			$phone = $data['AddressCorp']['address_phone'];
		}
		// parse the person's name; strip titles and such
		$name = trim(preg_replace(array(
			'/(?<=\b)[A-Z]r\s?\.?(?=[^\w]|$)/', // gets rid of Jrs, Srs, Mrs, Dr
			'/(?<=\b)[A-Z]{2,}(?=[^\w]|$)/', // gets rid of III, LLC
			'/(?<=\s)[A-Z]\.?\s/', // gets rid of middle initials
		), '', $data['MerchantOwner']['owner_name']), ' .,');

		$firstnameRegex = '/^([A-Z]\.?)*\s?\w+/';

		preg_match($firstnameRegex, $name, $matches);
		$firstname = trim($matches[0]);
		$lastname = trim(preg_replace($firstnameRegex, '', $name));
		$params = [
			'mid' => $data['Merchant']['merchant_mid'],
			'company_name' => $data['Merchant']['merchant_dba'],
			'agent_name' => $data['User']['fullname'],
			'address' => $data['AddressBus']['address_street'],
			'city' => $data['AddressBus']['address_city'],
			'state_province' => $data['AddressBus']['address_state'],
			'postal_code' => $data['AddressBus']['address_zip'],
			'contact_first_name' => $firstname,
			'contact_last_name' => $lastname,
			'contact_phone' => $phone,
		];
		//Sending empty elements will cause signature to fail because
		//control scan removes/ignores empty elements on their side to check
		//validity of signature
		$params = array_filter($params);
		$signParams = $this->createSignatureParams($this->api->config['request']['header'], $params);
		$signature = $this->signRequest($signParams, $this->getSecretKey(), 'POST', self::API_PATH . '/merchant');
		$this->api->config['request']['header']['CS-Signature'] = $signature;
		$this->api->config['request']['header']['Content-Type'] = "application/json; charset=UTF-8";
        $response = $this->api->post($this->host . self::API_PATH . '/merchant', json_encode($params));
		$resData = json_decode($response->body, true);
		$hasError = false;

		if ($response->isOk() && !empty($resData['guid'])) {
			$MerchantPci = ClassRegistry::init('MerchantPci');
			$id = $MerchantPci->field('id', ['merchant_id' => $merchantId]);
			$merchPcidata = ['cs_board_request_guid' => $resData['guid']];
			if (empty($id)) {
				$MerchantPci->create();
				$merchPcidata['merchant_id'] = $merchantId;
				$merchPcidata['merchant_id_old'] = $data['Merchant']['merchant_mid'];
			} else {
				$merchPcidata['id'] = $id;
			}
			if (!$MerchantPci->save($merchPcidata, ['validate' => false])) {
				$hasError = true;
				$msg = "Falied to save returned ControlScan GUID in MerchantPci table after successful Board Merchant API call.\n";
				$msg .= "In Function: MerchantPci->boardMerchant()\n";
				$msg .= "Merchant: " . $data['Merchant']['merchant_mid'] . "\n";
				$msg .= "Returned GUID: " . $resData['guid'] . "\n";
			} else {
				$this->queueCsBoardOrCancelCompletionJob();
			}
		} else {
			if (Configure::read('debug') > 0) {
				return;
			}
			$hasError = true;
			//Capture falure notification
			$msg = "Failed Boarding Merchant To ControlScan!\nAPI Request returned the following response:\n\n";
			$msg .= print_r($response, true);
		}

		if ($hasError) {
			$email = array_keys(Configure::read('App.defaultSender'));
			$Merchant->User->emailUser($email[0], $msg);
		}
	}

/**
 * getBoardingStatus
 * Make GET request to retrieve the boarding status of a single merchant using the GUID
 * that was returned by the original Board Merchant request.
 * Endpoint: /papi/v1/boarding_status/{guid}
 * 
 * @param  string $guid The GUID returned from a merchant board call
 * @throws Exception when request results in error
 * @return string
 */
	public function getBoardingStatus($guid) {
		//Check api var was properly set. This could be empty if no ApiConfiguration exists
		if (empty($this->api)) {
			return;
		}
		$signParams = $this->createSignatureParams($this->api->config['request']['header']);
		$signature = $this->signRequest($signParams, $this->getSecretKey(), 'GET', self::API_PATH ."/merchant/boarding_status/$guid");
		$this->api->config['request']['header']['CS-Signature'] = $signature;
		$response = $this->api->get($this->host . self::API_PATH . "/merchant/boarding_status/$guid");
		$resData = json_decode($response->body, true);
		if ($response->isOk()) {
			return $resData['boarding_status'];
		} else {
			throw new Exception('API error: ' . $response->body);
		}
	}

/**
 * getCancellingStatus
 * Make GET request to retrieve the cancelling status of a single merchant using the GUID
 * that was returned by the original Cancell Merchant request.
 * Endpoint: /papi/v1/boarding_status/{guid}
 * 
 * @param  string $guid The GUID returned from a merchant board call
 * @throws Exception when request results in error
 * @return string
 */
	public function getCancellingStatus($guid) {
		//Check api var was properly set. This could be empty if no ApiConfiguration exists
		if (empty($this->api)) {
			return;
		}
		$signParams = $this->createSignatureParams($this->api->config['request']['header']);
		$signature = $this->signRequest($signParams, $this->getSecretKey(), 'GET', self::API_PATH ."/merchant/cancel_status/$guid");
		$this->api->config['request']['header']['CS-Signature'] = $signature;
		$response = $this->api->get($this->host . self::API_PATH . "/merchant/cancel_status/$guid");
		$resData = json_decode($response->body, true);
		if ($response->isOk()) {
			return $resData['cancel_status'];
		} else {
			throw new Exception('API error: ' . $response->body);
		}
	}

/**
 * cancelMerchant
 * Make PUT request to Cancel a single merchant from ControlScan using the Merchant.merchant_mid.
 * Endpoint: /papi/v1/merchant/{mid}/cancel
 * 
 * @param  string $merchantMID The merchant MID
 * @throws Exception when request results in error
 * @return void
 */
	public function cancelMerchant($merchantMID) {
		//Check api var was properly set. This could be empty if no ApiConfiguration exists
		if (empty($this->api)) {
			return;
		}
		$MerchantPci = ClassRegistry::init('MerchantPci');
		$merchantId = $MerchantPci->Merchant->field('id', ['merchant_mid' => $merchantMID]);
		$signParams = $this->createSignatureParams($this->api->config['request']['header']);
		$signature = $this->signRequest($signParams, $this->getSecretKey(), 'PUT', self::API_PATH ."/merchant/$merchantMID/cancel");
		$this->api->config['request']['header']['CS-Signature'] = $signature;
		$response = $this->api->put($this->host . self::API_PATH . "/merchant/$merchantMID/cancel");
		$resData = json_decode($response->body, true);
		$hasError = false;

		if ($response->isOk() && !empty($resData['guid'])) {
			if (!empty($merchantId)) {
				$id = $MerchantPci->field('id', ['merchant_id' => $merchantId]);
				$merchPcidata = ['cs_cancel_request_guid' => $resData['guid']];
				if (empty($id)) {
					$MerchantPci->create();
					$merchPcidata['merchant_id'] = $merchantId;
				} else {
					$merchPcidata['id'] = $id;
				}
				if (!$MerchantPci->save($merchPcidata, ['validate' => false])) {
					$hasError = true;
					$msg = "Falied to save returned ControlScan GUID in MerchantPci table after successful Cancel Merchant API call.\n";
					$msg .= "In Function: MerchantPci->cancelMerchant()\n";
					$msg .= "Merchant: " . $merchantMID . "\n";
					$msg .= "Returned GUID: " . $resData['guid'] . "\n";
				} else {
					$this->queueCsBoardOrCancelCompletionJob(false);
				}
			}
		} else {
			if (Configure::read('debug') > 0) {
				return;
			}
			$hasError = true;
			//Capture falure notification
			$msg = "Failed Requiest To Cancel Merchant from ControlScan!\nAPI Request returned the following response:\n\n";
			$msg .= print_r($response, true);
		}
		if ($hasError) {
			$email = array_keys(Configure::read('App.defaultSender'));
			$MerchantPci->Merchant->User->emailUser($email[0], $msg);
		}
	}

/**
 * queueCsBoardOrCancelCompletionJob
 * Initiates a CakeResque job which checks and completes pending boaring and cancel merchant 
 * requests made previously to ControlScan
 * 
 * @param  boolean $processBoardJob when true will schedule processing of pending Board merchant
 *                                  if false will schedule processing the pending cancel requests
 * @return void
 */
	public function queueCsBoardOrCancelCompletionJob($processBoardJob = true) {
		$method = 'processPendingBoardings';
		if (!$processBoardJob) {
			$method = 'processPendingCancellations';
		}

		CakeResque::enqueue(
			'genericAxiaDbQueue',
			'BackgroundJobShell',
			['processControlScanBoardAndCancelRequests', $method]
		);
	}
}
