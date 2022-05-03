<?php
App::uses('AppModel', 'Model');
/**
 * RateStructure Model
 *
 * @property BetTable $BetTable
 */
class RateStructure extends AppModel {

/**
 * Rate Structure Names
 * @const 
 */
	const PASS_TRU = 'Pass Thru';
	const COST_PLUS = 'Cost Plus';
	const FLAT_RATE = 'Flat Rate';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'structure_name' => [
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			]
		]
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BetTable' => array(
			'className' => 'BetTable',
			'foreignKey' => 'bet_table_id',
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['qual_exemptions'])) {
			$this->data[$this->alias]['qual_exemptions'] = $this->removeAnyMarkUp($this->data[$this->alias]['qual_exemptions']);
		}
		
		return parent::beforeSave($options);
	}
/**
 * getRateStructureBets
 *
 * @param string $structureName an existing rate structure name 
 * @param string $qualExcemptions an existing qualification exeption
 * @return mixed array containing card types as keys ['Visa' 'Mastercard' 'Discover'] or ['Amercian Express'] and the [bet table ids] that correspond to each
 * @throws OutOfBoundsException
 */
	public function getRateStructureBets($structureName, $qualExcemptions) {
		if (empty($structureName) || empty($qualExcemptions)) {
			return [];
		}

		$conditions = ['structure_name' => $structureName, 'qual_exemptions' => $qualExcemptions];
		if (!$this->hasAny($conditions)) {
			throw new OutOfBoundsException(__("Rate structure combination '$structureName' with '$qualExcemptions' does not exist! Please request administrator to create this rate structure."));
		}
		$joins = [
			[
			'table' => 'bet_tables',
			'alias' => 'BetTable',
			'type' => 'LEFT',
			'conditions' => [
				"{$this->alias}.bet_table_id = BetTable.id"]
			],
			[
			'table' => 'card_types',
			'alias' => 'CardType',
			'type' => 'LEFT',
			'conditions' => [
				"BetTable.card_type_id = CardType.id"]
			],

		];

		$structure = $this->find('list', ['conditions' => $conditions, 'joins' => $joins, 'fields' => ['CardType.card_type_description', 'BetTable.id']]);
		//Visa and Mastercard bets must always both be empty or both not empty. They always have paired bet data
		$bothEmpty = (empty(Hash::get($structure, 'Mastercard')) && empty(Hash::get($structure, 'Visa')));
		$bothNotEmpty = (!empty(Hash::get($structure, 'Mastercard')) && !empty(Hash::get($structure, 'Visa')));
		if ($bothEmpty || $bothNotEmpty) {
			return $structure;
		} else {
			//Which one is the empty one?
			$emptyName = empty(Hash::get($structure, 'Mastercard'))? 'Mastercard' : 'Visa';
			$notEmptyName = !empty(Hash::get($structure, 'Mastercard'))? 'Mastercard' : 'Visa';
			$betName = $this->BetTable->field('name', ['id' => Hash::get($structure, $notEmptyName)]);

			$errStr = "A Rate structure with a $notEmptyName BET '$betName' was found but not one with a $emptyName BET. Create the rate stucture \"$structureName\" with \"$qualExcemptions\" and with a $emptyName BET that goes with $notEmptyName BET '$betName'.";
			throw new OutOfBoundsException(__($errStr));
		}
	}

}
