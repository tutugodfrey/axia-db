<?php
App::uses('AppModel', 'Model');
/**
 * ApiConfiguration Model
 *
 * @property Client $Client
 */
class ApiConfiguration extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'configuration_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'You must enter a unique configuration name.',
				'allowEmpty' => false,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Name is taken, please enter an unique configuration name..',
				'allowEmpty' => false,
			)
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removedP
}
