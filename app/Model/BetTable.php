<?php

App::uses('AppModel', 'Model');

/**
 * BetTable Model
 *
 * @property CardType $CardType
 */
class BetTable extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must enter a name',
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'You must enter an unique name',
				'allowEmpty' => false,
			),
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
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'RateStructure' => array(
			'className' => 'RateStructure',
			'foreignKey' => 'bet_table_id',
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CardType' => array(
			'className' => 'CardType',
			'foreignKey' => 'card_type_id',
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['name'])) {
			$this->data[$this->alias]['name'] = $this->removeAnyMarkUp($this->data[$this->alias]['name']);
		}
		
		return parent::beforeSave($options);
	}

/**
 * getAllGroupedByCardType
 *
 * Finds all bet tables grouped by CardType.
 *
 * @return array() containing full associations. Card types with no bet tables will be filtered out
 */
	public function getAllGroupedByCardType() {
		$data = $this->CardType->find('all', array(
			'fields' => array(
				'CardType.id',
				'CardType.card_type_description',
				),
			'contain' => array(
				'BetTable' => array(
					'conditions' => array('BetTable.is_enabled' => true),
					'fields' => array('BetTable.id', 'BetTable.name'),
					'order' => array('BetTable.name' => 'ASC'))
				),
			'order' => array('CardType.card_type_description' => 'DESC')
		));

		$result = Hash::filter(Hash::combine($data, '{n}.CardType.card_type_description', '{n}.BetTable'));

		return $result;
	}

/**
 * getListGroupedByCardType
 *
 * Finds list of enabled bet tables grouped by CardType.
 *
 * @return array() containing full associations. Card types with no bet tables will be filtered out
 */
	public function getListGroupedByCardType() {
		return $this->Bet->BetTable->find('list', [
				'fields' => ['BetTable.id', 'BetTable.name', 'CardType.card_type_description'],
				'conditions' => ['BetTable.is_enabled' => true],
				'contain' => ['CardType'],
				'order' => ['CardType.card_type_description DESC', 'BetTable.name DESC'],
			]
		);
	}

}
