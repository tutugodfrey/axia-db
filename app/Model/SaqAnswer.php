<?php

App::uses('AppModel', 'Model');

/**
 * SaqAnswer Model
 *
 * @property SaqMerchantSurveyXref $SaqMerchantSurveyXref
 * @property SaqSurveyQuestionXref $SaqSurveyQuestionXref
 */
class SaqAnswer extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'saq_merchant_survey_xref_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'saq_survey_question_xref_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'answer' => array(
						'boolean' => array(
								  'rule' => array('boolean'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'date' => array(
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
			  'SaqMerchantSurveyXref' => array(
						'className' => 'SaqMerchantSurveyXref',
						'foreignKey' => 'saq_merchant_survey_xref_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  ),
			  'SaqSurveyQuestionXref' => array(
						'className' => 'SaqSurveyQuestionXref',
						'foreignKey' => 'saq_survey_question_xref_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  )
	);

}
