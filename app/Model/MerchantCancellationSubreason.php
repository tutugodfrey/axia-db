<?php

App::uses('AppModel', 'Model');

/**
 * MerchantCancellationSubreason Model
 *
 */
class MerchantCancellationSubreason extends AppModel {

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
		'visible' => array(
					'boolean' => array(
					'rule' => array('boolean'),
			),
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
			'MerchantCancellation' => array(
				'className' => 'MerchantCancellation',
				'foreignKey' => 'merchant_cancellation_subreason_id',
				'dependent' => false,
			)
	);

}
