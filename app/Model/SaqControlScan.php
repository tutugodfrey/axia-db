<?php

App::uses('AppModel', 'Model');

/**
 * SaqControlScan Model
 *
 * @property Merchant $Merchant
 */
class SaqControlScan extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'merchant_id' => array(
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
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			  'Merchant' => array(
						'className' => 'Merchant',
						'foreignKey' => 'merchant_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  )
	);

}
