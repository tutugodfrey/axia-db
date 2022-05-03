<?php

App::uses('AppModel', 'Model');

/**
 * CommissionFee Model
 *
 * @property User $User
 */
class CommissionFee extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'user_id' => array(
			'rule' => array('notBlank'),
		),
		'user_compensation_profile_id' => array(
			'rule' => array('notBlank'),
		),
		'app_fee_profit' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
		'app_fee_loss' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
		'non_app_fee_profit' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
		'non_app_fee_loss' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			'UserCompensationProfile'
	);
}
