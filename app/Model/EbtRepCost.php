<?php

App::uses('AppModel', 'Model');

/**
 * EbtRepCost Model
 *
 * @property User $User
 * @property DebitAcquirer $DebitAcquirer
 */
class EbtRepCost extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'user_id' => array(
						'uuid' => array(
								'rule' => array('notBlank'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			),
			'debit_acquirer_id' => array(
						'uuid' => array(
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
			'User' => array(
						'className' => 'User',
						'foreignKey' => 'user_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'DebitAcquirer' => array(
						'className' => 'DebitAcquirer',
						'foreignKey' => 'debit_acquirer_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			)
	);

}
