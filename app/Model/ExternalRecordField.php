<?php
App::uses('AppModel', 'Model');
App::uses('SalesForce', 'Model');
/**
 * ExternalRecordField Model
 *
 * @property ExternalRecordField $ExternalRecordField
 */
class ExternalRecordField extends AppModel {
	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
        'value' => array(
        	'isUniqueSfAccountId' => array(
	            'rule' => array('isUniqueSfAccountId'),
	            'message' => 'This Account ID is already assigned to another merchant!',
	            'required' => false,
	            'allowEmpty' => true
        	),
        	'isValidSfId' => array(
	            'rule' => array('isValidSfId'),
	            'message' => 'Invalid ID! No records were found in SalesForce matching this ID.',
	            'required' => false,
	            'allowEmpty' => true
        	)
        )
    );

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'AssociatedExternalRecord' => array(
			'className' => 'AssociatedExternalRecord',
			'foreignKey' => 'associated_external_record_id',
		),
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id'
		)
	);

/**
 * isValidSfId
 * Custom validation rule checks whether salesforce id exists in salesforce by making a API request
 * 
 * @param  array  $check the value entered
 * @return mixed sting|boolean false | true if valid | error string
 */
    public function isValidSfId($check) {
    	$value = array_pop($check);
    	if (empty($value)) {
    		return true;
    	}
    	$sfFieldName = $this->data['ExternalRecordField']['field_name'];
    	if ($sfFieldName !== SalesForce::ACCOUNT_ID && $sfFieldName !== SalesForce::OPPTY_ID) {
    		//we only care about these salesforce fields so unexpected fields are not relevant for this valiation method
    		return true;
    	} 
    	try {
	    	$SalesForce = new SalesForce();
	    	$sObjectName = $SalesForce->fieldNames[$sfFieldName]['sobject'];
	    	$isValidSfId = $SalesForce->vaildateSObjectIdExists($sObjectName, $value);
    	} catch(Exception $e) {
    		return "API error ocurred while validating this ID, try again.";
    	}
    	return $isValidSfId;
    }
/**
 * isUniqueSfAccountId
 * Custom validation rule checks uniqueness of salesforce account id/
 * Each salesforce Account Id can only be assigned to one merchant.
 * 
 * @param  array  $check the value entered
 * @return boolean false | true if unique
 */
    public function isUniqueSfAccountId($check) {
    	$value = array_pop($check);
    	//only check account id fields
        if ($this->data['ExternalRecordField']['field_name'] !== SalesForce::ACCOUNT_ID || empty($value)) {
        	return true;
        }
        $conditions = ['value' => $value, 'field_name' => SalesForce::ACCOUNT_ID];
        if (!empty($this->id)) {
        	$conditions[] = "id != '" . $this->id . "'";
        }

        return ($this->hasAny($conditions) === false);
    }
}
