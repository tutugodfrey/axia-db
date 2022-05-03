<?php
App::uses('AppModel', 'Model');
/**
 * NonTaxableReason Model
 *
 * @property InvoiceItem $InvoiceItem
 */
class NonTaxableReason extends AppModel {


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'reason';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'reason' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must enter a unique ach reason',
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Reason already exists, you must enter an unique reason.',
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
		'InvoiceItem' => array(
			'className' => 'InvoiceItem',
			'foreignKey' => 'non_taxable_reason_id',
			'dependent' => false,
		)
	);
/**
 * Method to get the model record list overrices AppModel
 *
 * @return array
 */
	public function getList() {
		return $this->find('list', array(
			'order' => array("{$this->alias}.{$this->displayField}" => 'DESC')
		));
	}

}
