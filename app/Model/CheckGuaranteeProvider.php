<?php

App::uses('AppModel', 'Model');

/**
 * CheckGuaranteeProvider Model
 *
 * @property CheckGuarantee $CheckGuarantee
 */
class CheckGuaranteeProvider extends AppModel {

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'provider_name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'provider_name' => array(
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
			'CheckGuarantee' => array(
						'className' => 'CheckGuarantee',
						'foreignKey' => 'check_guarantee_provider_id',
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
