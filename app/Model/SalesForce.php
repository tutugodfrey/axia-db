<?php
App::uses('AppModel', 'Model');
App::uses('HttpSocket', 'Network/Http');
App::uses('Model', 'ApiConfiguration');

/**
 * SalesForce
 * This is an itegration with salesforce's API.
 * 
 * 
 * @property Client $Client
 */
class SalesForce extends AppModel {

/**
 * Array of standardized salesforce system field names for single point access
 * The field names are exacly as they are defined in each salesforce obhect
 * sobject = salesforce object
 * 
 * @var array
 */
	public $fieldNames = [
		self::ACCOUNT_ID => [
			'sobject' => 'Account',
			'field_name' => self::ACCT_ID_API_NAME,
			'data_type' => 'string' 
		],
		self::ACCT_NUM_MID => [
			'sobject' => 'Account',
			'field_name' => 'Account_Number_MID__c',
			'data_type' => 'string' 
		],
		self::EXPECTED_INST_DATE => [
			'sobject' => 'Account',
			'field_name' => 'Expected_Install_Date__c',
			'data_type' => 'string' //string representation of a date formated as YYYY-mm-dd
		],
		self::INST_DATE => [
			'sobject' => 'Account',
			'field_name' => 'Install_Date__c',
			'data_type' => 'string' //string representation of a date formated as YYYY-mm-dd
		],
		self::CANCEL_DATE => [
			'sobject' => 'Account',
			'field_name' => 'Cancellation_Date__c',
			'data_type' => 'string' //string representation of a date formated as YYYY-mm-dd
		],
		self::OPPTY_ID => [
			'sobject' => 'Opportunity',
			'field_name' => self::OPPTY_ID_API_NAME,
			'data_type' => 'string'
		],
		self::OPPTY_NAME => [
			'sobject' => 'Opportunity',
			'field_name' => 'Name',
			'data_type' => 'string'
		],
		self::GLOBAL_CLIENT_ID => [
			'sobject' => 'Account',
			'field_name' => 'Client_ID__c',
			'data_type' => 'string' 
		],
		self::GLOBAL_CLIENT_NAME => [
			'sobject' => 'Account',
			'field_name' => 'Client_Name__c',
			'data_type' => 'string'
		],
	];

/**
 * Standardized salesforce field names
 *
 * @var string
 */
	const ACCOUNT_ID = 'Account ID';
	const ACCT_NUM_MID = 'AccountMID';
	const EXPECTED_INST_DATE = 'ExpectedInstallDate';
	const INST_DATE = 'InstallDate';
	const CANCEL_DATE = 'Cancellation_Date__c';
	const OPPTY_ID = 'Opportunity ID';
	const OPPTY_NAME = 'Opportunity Name';
	const GLOBAL_CLIENT_ID = 'Client_ID__c';
	const GLOBAL_CLIENT_NAME = 'Client_Name__c';
	const ACCT_ID_API_NAME = 'Account_ID_18__c';
	const OPPTY_ID_API_NAME = 'Opportunity_ID_18__c';

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
 * This property will be set from data stroed in ApiConfiguration's table
 *
 * @property host
 */
	public $host = null;

/**
 * Path to the salesforce API endpoints
 *
 * @constant API_PATH
 */
	const API_PATH = '/services/data/v46.0/sobjects';

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
		$configData = $this->getApiConfig();
		if (!empty($configData)) {
			$this->renewAccessTokenIfExpired($configData);
			$this->host = $configData['ApiConfiguration']['instance_url'];
			$this->api = new HttpSocket();
			$this->api->config['request']['header']['Authorization'] = "OAuth " . $configData['ApiConfiguration']['access_token'];
			$this->api->config['request']['header']['Content-Type'] = "application/json";

		} else {
			$this->api = false;
		}
	}

/**
 * This is a stand alone model class requires no table
 *
 * constants
 */
	const SF_CONFIG_NAME = 'salesforce';
	const SF_CONFIG_NAME_DEV = 'salesforce test';

/**
 * getApiConfig
 * Returns API connection configuration metadata.
 * Production config will be returned in production otherwise test configuration will be returned
 *
 * @return array containing ApiConfiguration model metadata about this external API
 */
	public function getApiConfig() {
		$conditions['configuration_name'] = self::SF_CONFIG_NAME;
		if (Configure::read('debug') > 0) {
			$conditions['configuration_name'] = self::SF_CONFIG_NAME_DEV;
		}
		$ApiConfiguration = ClassRegistry::init('ApiConfiguration');
		return $ApiConfiguration->find('first', ['conditions' => $conditions]);
	}

/**
 * renewAccessTokenIfExpired
 * Renews access token
 * The referenced configuration array param will be updated with the latest access token information and saved at the same time
 *
 * @param array &$configData reference to salesforce connection configuration stored in the ApiConfiguration midel table.
 * @return boolean false if failed to renew token
 */
	public function renewAccessTokenIfExpired(&$configData) {
		if (empty($configData)) {
			return false;
		}

		$accessTokenUrl = $configData['ApiConfiguration']['access_token_url'];
		$request['body'] = $this->_getRefreshTokenBodyStr($configData);
		$httpSocket = new HttpSocket();
		$response = $httpSocket->post($accessTokenUrl, null, $request);
		$responseBody = json_decode($response->body, true);

		if ($response->isOk()) {
			$configData['ApiConfiguration']['access_token'] = $responseBody['access_token'];
			$configData['ApiConfiguration']['issued_at'] = $responseBody['issued_at'];
			$configData['ApiConfiguration']['instance_url'] = $responseBody['instance_url'];
			ClassRegistry::init('ApiConfiguration')->save($configData['ApiConfiguration']);
		} else {
			return false;
		}
		return true;
	}

/**
 * _getRefreshTokenBodyStr
 * Buids a string for the body of a refresh token request as specified in salesforce documentation:
 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_refresh_token_oauth.htm
 *
 * @param int $configData original timestamp in miliseconds when the access token was issued should be contained in ApiConfigurations data array .
 * @return string
 */
	protected function _getRefreshTokenBodyStr(&$configData) {
		$body = 'grant_type=refresh_token&';
		$body .= 'client_id=' . $configData['ApiConfiguration']['client_id'] . '&';
		$body .= 'client_secret=' . $configData['ApiConfiguration']['client_secret'] . '&';
		$body .= 'refresh_token=' . $configData['ApiConfiguration']['refresh_token'];
		return $body;
	}

/**
 * isExpiredToken
 * Checks if current access token is expired based on configuration parameters which originate from salesforce internal session settings
 * The original access token issued timestamp is included in the access token request response from salesforce as a unix epoch in milliseconds
 *
 * @param int $issued original timestamp in miliseconds when the access token was issued.
 * @param int $hoursValid the number of hours after which the token expires, if ommitted or empty then it is assument the access token never expires.
 * @return boolean false if failed to renew token
 */
	public function isExpiredToken($issued, $hoursValid = null) {
		if (empty($issued)) {
			return true;
		} elseif (empty($hoursValid)) {
			return false;
		}
		//microtime returns seconds and millionth of a second part but we only want milliseconds 
		$hourDiff = (int)(( (round(microtime(true) *1000)) - $issued) /1000);

		//Calculate the number of validity seconds minus half hour in seconds as a safety time buffer
		$secondsValid = ($hoursValid * 3600) - 1800;
		return ($hourDiff >= $secondsValid);
	}


/**
 * updateSalesforceField
 * Updates data via API for specified field IFF there is no data already present in that field.
 *
 * @param string $stdFieldName the aliased or standardized salesforce field name as defined in the SalesForce->fieldNames class variable
 * @param string $val the value to save
 * @param string $externalId the id of the external salesforce record to be updated
 * @param boolean $updateIFFEmpty if true salesforce field will only be updated if it is blank, otherwise it will always be updated
 */
	public function updateSalesforceField($stdFieldName, $val, $externalId, $updateIFFEmpty = true) {
		if (!empty($stdFieldName) && !empty($val)) {
			$SalesForce = new SalesForce();
			if ($SalesForce->api != false) {
				//we can update if it doesn't matter whether is empty and vice-versa
				$canUpdate = !$updateIFFEmpty;
				$sfFieldName = $this->fieldNames[$stdFieldName]['field_name'];
				$url = $SalesForce->host . SalesForce::API_PATH . '/' . $SalesForce->fieldNames[$stdFieldName]['sobject'] .'/' . $externalId;
				//check whether field is blank if needed
				if ($updateIFFEmpty){
					$query = '?fields=' . $sfFieldName;
					$response = $SalesForce->api->get($url.$query);
					if ($response->isOk()) {
						$responseBody = json_decode($response->body, true);
						// we can update iff empty
						$canUpdate = empty($responseBody[$sfFieldName]);
					} else {
						$this->log("Salesforce API ERROR:\nResponse Body:" . $response->body ."\nEdpoint URL: $url.$query", 'error');
						return;
					}
				}

				if ($canUpdate) {
					$SalesForce->api->reset(false);
					$response = $SalesForce->api->patch($url, json_encode([$sfFieldName => $val]));
					//Nothing is returned when update request is successful so if not empty we got bad news
					if (!empty($response->body)) {
						$this->log("Salesforce API ERROR:\nResponse Body:" . $response->body ."\nEdpoint URL: $url", 'error');
					}
				}
			} else {
				$this->log("Salesforce API connection configuration not found! Cannot connect to salesforce!", 'error');
			}
		} else {
			$this->log("Invalid or empty arguments passed to function updateSalesforceField", 'error');
		}
	}

/**
 * vaildateSObjectIdExists
 * Performs a simple query using provided sObject name and id and if an exact match is found true will be returned.
 *
 * @param string $sObjectName name of a salesforce object i.e.: Account, or Opportunity to use for search along with the provided sObjectId
 * @param string $sObjectId the id associated with the $sObjectName provided and to use for the search
 * @return boolean true will be returned if match found, false otherwise
 * @throws Exception
 */
	public function vaildateSObjectIdExists($sObjectName, $sObjectId) {
		if (!empty($sObjectName) && !empty($sObjectId)) {
			$SalesForce = new SalesForce();
			if ($SalesForce->api != false) {
				$url = $SalesForce->host . "/services/data/v46.0/query/?q=SELECT id from $sObjectName where id='$sObjectId'";
				$response = $SalesForce->api->get($url);
				if ($response->isOk()) {
					$responseBody = json_decode($response->body, true);
					return (boolean)Hash::get($responseBody, 'totalSize', false);
				} else {
					$this->log("Salesforce API ERROR:\nResponse Body:" . $response->body ."\nEdpoint URL: $url", 'error');
					throw new Exception('Unexpected SalesForce API Error!');
				}
			} else {
				$this->log("Salesforce API connection configuration not found! Cannot connect to salesforce!", 'error');
				throw new Exception('Salesforce API connection configuration not found!');
			}
		} else {
			return false;
		}
	}
}
