<?php

App::uses('AppModel', 'Model');

/**
 * Partner Model
 *
 * @property Partner $Partner
 * @property CommissionReport $CommissionReport
 * @property Merchant $Merchant
 * @property Partner $Partner
 * @property RepPartnerXref $RepPartnerXref
 * @property ResidualReport $ResidualReport
 */
class Partner extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'partner_id' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'partner_name' => array(
						'notBlank' => array(
								  'rule' => array('notBlank'),
								  'message' => 'This is a required field.',
						//'allowEmpty' => false,
						//'required' => true,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'active' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
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
//		'Partners' => array(
//			'className' => 'Partner',
//			'foreignKey' => 'id',
//			'conditions' => '',
//			'fields' => '',
//			'order' => ''
//		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			  'CommissionReport' => array(
						'className' => 'CommissionReport',
						'foreignKey' => 'id',
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
			  'Merchant' => array(
						'className' => 'Merchant',
						'foreignKey' => 'id',
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
//		'Partner' => array(
//			'className' => 'Partner',
//			'foreignKey' => 'id',
//			'dependent' => false,
//			'conditions' => '',
//			'fields' => '',
//			'order' => '',
//			'limit' => '',
//			'offset' => '',
//			'exclusive' => '',
//			'finderQuery' => '',
//			'counterQuery' => ''
//		),
			  'RepPartnerXref' => array(
						'className' => 'RepPartnerXref',
						'foreignKey' => 'id',
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
			  'ResidualReport' => array(
						'className' => 'ResidualReport',
						'foreignKey' => 'id',
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
