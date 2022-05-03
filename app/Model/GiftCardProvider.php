<?php

App::uses('AppModel', 'Model');

/**
 * GiftCardProvider Model
 *
 * @property GiftCard $belonging
 * @property GiftCard $GiftCard
 */
class GiftCardProvider extends AppModel {

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'provider_name';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'belonging' => array(
					'className' => 'GiftCard',
					'foreignKey' => 'id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'GiftCard' => array(
					'className' => 'GiftCard',
					'foreignKey' => 'gift_card_provider_id',
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
