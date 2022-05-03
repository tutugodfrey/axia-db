<?php
App::uses('AppModel', 'Model');
App::uses('SalesForce', 'Model');
/**
 * Client Model
 *
 * @property Merchant $Merchant
 */
class Client extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'client_name_global';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id_global' => array(
			'isValidClientId' => array(
				'rule' => array('isValidClientId'),
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'This Client ID has already beed added, please enter a unique non-empty value',
				'allowEmpty' => false,
			),			
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'client_id',
			'dependent' => false,
		)
	);

/**
 * Custom validation rule, check if client id value is valid by checking its length = 8
 * and whether it matches a client id in salesforce by making an API call
 * 
 * @param array $check The client_id_global value.
 * @return bool
 */
	public function isValidClientId($check) {
		$val = reset($check);
		if (strlen($val) !== 8 || !is_numeric($val)) {
			return 'Client id is invalid! Enter a unique 8 digit Client ID that was generated from SalesForce.';
		}

		try {
			$result = $this->getSfClientNameByClientId($val);
			if (empty($result) || $result === false) {
				return 'The client id is invalid! It does not match any known id.';
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
		return true;
	}


/**
 * getSfClientNameByClientId
 * Makes GET request to find a SalesForce account using provided Client ID.
 * Returns an array cotaining Client ID and client name.
 * In salesforce the Client ID/Name comes from the overarching parent company not from it's children locations.
 * Children accounts all have a reference to the parent Client ID/Name in SF
 * 
 * @param string $clientId an 8 digit client id numbers or string representation of numbers
 * @return mixed array | boolean false when nothing is found or array containing matching result
 * @throws Exception
 */
	public function getSfClientNameByClientId($clientId) {
		if (!empty($clientId)) {
			$SalesForce = new SalesForce();
			if ($SalesForce->api != false) {
				$sfObjAcctName = $SalesForce->fieldNames[SalesForce::GLOBAL_CLIENT_ID]['sobject'];
				$params = "/?q=$clientId&";
				$params .= "sobject=$sfObjAcctName&";
				$params .= "$sfObjAcctName.where=" . SalesForce::GLOBAL_CLIENT_ID . "='$clientId'&";
				$params .= "$sfObjAcctName.fields=" . SalesForce::GLOBAL_CLIENT_ID . "," . SalesForce::GLOBAL_CLIENT_NAME ."&";
				$params .= "$sfObjAcctName.limit=1";
				//we need to use a different enpoint url
				$altApiPath = str_replace('/sobjects', '', SalesForce::API_PATH);
				$url = $SalesForce->host . "$altApiPath/parameterizedSearch$params";
				
				$response = $SalesForce->api->get($url);
				if ($response->isOk()) {
					$responseBody = json_decode($response->body, true);
					$result = Hash::get($responseBody, 'searchRecords.0', false);
					return $result;
				} else {
					throw new Exception("Salesforce API ERROR:Response Body: " . $response->body);
					return;
				}
			} else {
				throw new Exception("Salesforce API connection configuration not found! Cannot connect to salesforce!");
			}
		} else {
			return false;
		}
	}

	/**
	 * getAutocompleteMatches
	 * Searches for clients that contain or are similar to the searchTerm
	 * 
	 * @param  string $searchTerm can be al or part of a client id or a client name
	 * @return string JSON encoded string with results
	 */
	public function getAutocompleteMatches($searchTerm) {
		if (empty($searchTerm)) {
			return '[]';
		}
		$results = $this->find('all', [
			//fields aliased with the keys expected by the client-side autocomplete function
			'fields' => [
				'id',
				'(trim(BOTH FROM "Client"."client_id_global" || \' - \' || "Client"."client_name_global")) as "Client__value"'
			],
			'conditions' => [
				'OR' => [
					"Client.client_id_global::varchar(8) LIKE '$searchTerm%'",
					"Client.client_name_global ILIKE '%$searchTerm%'",
				]
			],
			'limit' => 20,
			'order' => 'Client.client_id_global ASC'
		]);
		//the client-side autocomplete function requires array to be structured in a specific way
		if (!empty($results)) {
			foreach ($results as $record) {
				$formattedResult[] = ['value' => $record['Client']['value'], 'id' => $record['Client']['id']];
			}
			return json_encode($formattedResult);
		} else {
			return '[]';
		}
	}
}
