<?php

App::uses('AppModel', 'Model');

/**
 * SaqSurvey Model
 *
 * @property EligibilitySurvey $EligibilitySurvey
 * @property ConfirmationSurvey $ConfirmationSurvey
 * @property SaqSurveyQuestionXref $SaqSurveyQuestionXref
 * @property SaqMerchantSurveyXref $SaqMerchantSurveyXref
 */
class SaqSurvey extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'name' => array(
						'notBlank' => array(
								  'rule' => array('notBlank'),
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
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			  'SaqSurveyQuestionXref' => array(
						'className' => 'SaqSurveyQuestionXref',
						'foreignKey' => 'saq_survey_id',
						'dependent' => false,
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'exclusive' => '',
						'finderQuery' => '',
						'counterQuery' => ''
			  ),
			  'SaqMerchantSurveyXref' => array(
						'className' => 'SaqMerchantSurveyXref',
						'foreignKey' => 'saq_survey_id',
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
