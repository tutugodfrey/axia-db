<?php
App::uses('AppModel', 'Model');
/**
 * BetNetwork Model
 *
 * @property Bet $Bet
 */
class BetNetwork extends AppModel {

/**
 * @const string PIVOTAL_NW
 *
 */
	const PIVOTAL_NW = 'Pivotal';

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
				'last' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'You must enter an unique name, this one already exists!',
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Bet' => array(
			'dependent' => true,
		)
	);

/** 
 * getList overrides AppModel
 * 
 * @param array $options search options
 * @throws \InvalidArgumentException
 * @return array
 */
	public function getList($options = array()) {
		if (!is_array($options)) {
			throw new InvalidArgumentException("Function expects array but " . gettype($options) . " was provided");
		}

		$defaultOptions = array(
			'fields' => array('id', 'name'),
			'conditions' => array('is_active' => 1),
			'order' => array('name ASC')
			);

		return $this->find('list', (empty($options))?$defaultOptions:$options);
	}

}
