<?php

App::uses('AppModel', 'Model');

/**
 * MerchantUwFinalApproved Model
 *
 */
class MerchantUwFinalApproved extends AppModel {

/**
 * displayField
 *
 * @var
 */
	public $displayField = 'name';

/**
 * Default sort order
 *
 */
	public $order = [
		"MerchantUwFinalApproved.name" => 'ASC'
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Approver name is required!',
				'required' => true,
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'That name already exists, you must enter an unique one!',
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
 * getActivePlus() method
 * 
 * @param string $includeId and id to include in the list of active approvers. This could be one inactive approver.
 * @return array
 */
	public function getActivePlus($includeId = '') {
		$conditions['active'] = true;
		if (!empty($includeId)) {
			$conditions = ['OR' => array_merge(['id' => $includeId], $conditions)];
		}
		return $this->find('list', array('conditions' => $conditions, 'fields' => array('id', 'name')));
	}
}
