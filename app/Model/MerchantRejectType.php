<?php

App::uses('AppModel', 'Model');

/**
 * MerchantRejectType Model
 *
 */
class MerchantRejectType extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		  'name' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
				),
				'input_has_only_valid_chars' => array(
					'rule' => array('inputHasOnlyValidChars'),
					'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
					'required' => true,
					'allowEmpty' => false,
				)	
		  ),
	);

}
