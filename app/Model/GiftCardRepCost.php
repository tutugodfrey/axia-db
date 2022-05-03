<?php

App::uses('AppModel', 'Model');

/**
 * GiftCardRepCost Model
 *
 * @property User $User
 * @property GiftCardProvider $GiftCardProvider
 */
class GiftCardRepCost extends AppModel {

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
			'gift_card_provider_id' => array(
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
			'GiftCardProvider' => array(
						'className' => 'GiftCardProvider',
						'foreignKey' => 'gift_card_provider_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			)
	);

}
