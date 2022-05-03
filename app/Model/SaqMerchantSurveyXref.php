<?php

App::uses('AppModel', 'Model');

/**
 * SaqMerchantSurveyXref Model
 *
 * @property SaqMerchant $SaqMerchant
 * @property SaqSurvey $SaqSurvey
 * @property SaqEligibilitySurvey $SaqEligibilitySurvey
 * @property SaqConfirmationSurvey $SaqConfirmationSurvey
 * @property SaqAnswer $SaqAnswer
 */
class SaqMerchantSurveyXref extends AppModel {

	public $useTable = "saq_merchant_survey_xrefs";

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'saq_merchant_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'saq_survey_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'datestart' => array(
						'datetime' => array(
								  'rule' => array('datetime'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			  'SaqMerchant' => array(
						'className' => 'SaqMerchant',
						'foreignKey' => 'saq_merchant_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  ),
			  'SaqSurvey' => array(
						'className' => 'SaqSurvey',
						'foreignKey' => 'saq_survey_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  )/* ,
			  'SaqMerchantSurveyXref' => array(
			  'className' => 'SaqMerchantSurveyXref',
			  'foreignKey' => 'saq_eligibility_survey_id',
			  'conditions' => '',
			  'fields' => '',
			  'order' => ''
			  ),
			  'SaqMerchantSurveyXref' => array(
			  'className' => 'SaqMerchantSurveyXref',
			  'foreignKey' => 'saq_confirmation_survey_id',
			  'conditions' => '',
			  'fields' => '',
			  'order' => ''
			  ) */
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			  'SaqAnswer' => array(
						'className' => 'SaqAnswer',
						'foreignKey' => 'saq_merchant_survey_xref_id',
						'dependent' => false,
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'exclusive' => '',
						'finderQuery' => '',
						'counterQuery' => ''
			  )
	);

}
