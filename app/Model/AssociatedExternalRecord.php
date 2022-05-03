<?php
App::uses('AppModel', 'Model');
App::uses('SalesForce', 'Model');
/**
 * AssociatedExternalRecord Model
 *
 * @property External $External
 * @property Merchant $Merchant
 */
class AssociatedExternalRecord extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'external_system_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'The name of the system is required',
			),
		)
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id'
		)
	);
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ExternalRecordField' => array(
			'className' => 'ExternalRecordField',
			'foreignKey' => 'associated_external_record_id',
			'dependent' => true
		)
	);

/**
 * cleanUpEmptyValue
 * Removes records with empty values submotted from the form request data.
 * Specifically an empty opportunity id record will be removed
 * 
 * @param array $requestData the request data submitted from the client side
 * @return array the cleaned up AssociatedExternalRecord data along with associated ExternalRecordField
 */
	public function cleanUpEmptyValue($requestData) {
		foreach($requestData['ExternalRecordField'] as $idx => $record) {
			if ($record['field_name'] === SalesForce::OPPTY_ID) {
				if (empty($record['id']) && empty($record['value'])) {
					unset($requestData['ExternalRecordField'][$idx]);
				} elseif (!empty($record['id']) && empty($record['value'])) {
					$this->ExternalRecordField->delete($record['id'], false);
					unset($requestData['ExternalRecordField'][$idx]);
				}
			}
		}
		return $requestData;
	}

/**
 * getSalesforceRecordsByMerchantId
 * Finds records related to salesforce and associated ExternalRecordField containing the merchantId provided
 * If nothing is found an array containing minimum default data will be returned.
 * 
 * 
 * @param string $merchantId the id of the merchant record to search for
 * @return array the AssociatedExternalRecord data along with associated ExternalRecordField
 */
	public function getSalesforceUpsertViewDataByMerchantId($merchantId) {
		$data = $this->find('first', [
				'conditions' => ["AssociatedExternalRecord.merchant_id" => $merchantId],
				'contain' => [
					'ExternalRecordField'
				]
			]);
		if (empty(Hash::get($data, 'AssociatedExternalRecord.id'))) {
			$data = [
				'AssociatedExternalRecord' => [
					'id' => CakeText::uuid(),
					'external_system_name' => 'salesforce',
					'merchant_id' => $merchantId
				],
				'ExternalRecordField' => [
					[
						'merchant_id' => $merchantId,
						'field_name' => SalesForce::ACCOUNT_ID,
						'api_field_name' => SalesForce::ACCT_ID_API_NAME,
						'value' => ''
					],
					[
						'merchant_id' => $merchantId,
						'field_name' => SalesForce::OPPTY_ID,
						'api_field_name' => SalesForce::OPPTY_ID_API_NAME,
						'value' => ''
					],
				]
			];
		} elseif (Hash::check($data, 'ExternalRecordField.{n}[field_name='.SalesForce::OPPTY_ID.']') === false) {
			$data['ExternalRecordField'][] = [
				'merchant_id' => $merchantId,
				'field_name' => SalesForce::OPPTY_ID,
				'api_field_name' => SalesForce::OPPTY_ID_API_NAME,
				'value' => ''
			];
		}
		
		return $data;
	}
}
