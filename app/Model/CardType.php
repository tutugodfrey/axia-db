<?php

App::uses('AppModel', 'Model');

/**
 * CardType Model
 *
 * @property Merchant $Merchant
 */
class CardType extends AppModel {

	public $name = 'CardType';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'card_type_description' => array(
				'isUnique' => array(
					'rule' => array('isUnique'),
					'message' => 'Card type already exists, you must enter an unique card type.',
					'allowEmpty' => false,
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Bet' => array(
			'dependent' => true,
		),
		'BetTable' => array(
			'className' => 'BetTable',
			'foreignKey' => 'card_type_id',
			'dependent' => true,
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'joinTable' => 'merchant_card_types',
			'foreignKey' => 'card_type_id',
			'associationForeignKey' => 'merchant_id',
			'unique' => 'keepExisting',
		)
	);

/**
 * Returns a list of card types
 *
 * @return array
 */
	public function getList() {
		return $this->find('list', array(
			'contain' => array(),
			'fields' => array(
				'CardType.id', 'CardType.card_type_description'
			)
		));
	}

/**
 * view
 *
 * @param string $cardTypeId a card type id
 * @param array $options Find options
 * @return mixed
 * @throws \NotFoundException
 */
	public function view($cardTypeId = null, $options = array()) {
		$result = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->alias . '.' . $this->primaryKey => $cardTypeId
			)
		));

		if (empty($result)) {
			throw new NotFoundException(__('Invalid Card Type!'));
		}

		return $result;
	}
}
