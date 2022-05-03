<?php

App::uses('AppModel', 'Model');

/**
 * SalesGoal Model
 *
 * @property User $User
 */
class SalesGoal extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'user_id' => array(
						'numeric' => array(
								  'rule' => array('notBlank'),
								  'message' => 'Required user_id',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'goal_month' => array(
						'numeric' => array(
								  'rule' => array('range', 0, 13),
								  'message' => 'Please enter a valid month, starting 1 for Jan, 12 for Dec',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'goal_accounts' => array(
						'numeric' => array(
								  'rule' => array('validAmount'),
								  'message' => 'Please enter a numeric value',
								  'allowEmpty' => true,
						),
			  ),
			  'goal_volume' => array(
						'numeric' => array(
								  'rule' => array('validAmount'),
								  'message' => 'Please enter a numeric value',
								  'allowEmpty' => true,
						),
			  ),
			  'goal_profits' => array(
						'numeric' => array(
								  'rule' => array('validAmount'),
								  'message' => 'Please enter a numeric value',
								  'allowEmpty' => true,
						),
			  ),
			  'goal_statements' => array(
						'numeric' => array(
								  'rule' => array('validAmount'),
								  'message' => 'Please enter a numeric value',
								  'allowEmpty' => true,
						),
			  ),
			  'goal_calls' => array(
						'numeric' => array(
								  'rule' => array('validAmount'),
								  'message' => 'Please enter a numeric value',
								  'allowEmpty' => true,
						),
			  ),
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			  'User'
	);

}
