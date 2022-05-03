<?php

App::uses('AppModel', 'Model');
App::uses('ResidualParameterType', 'Model');

/**
 * ResidualParameter Model
 *
 * @property User $User
 * @property ResidualParameterType $ResidualParameterType
 * @property ProductsServicesType $ProductsServicesType
 */
class ResidualParameter extends AppModel {

	public $validate = array(
		'user_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'user_id is required'
			)
		),
		'residual_parameter_type_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'residual_parameter_type_id is required'
			)
		),
		'products_services_type_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'products_services_type_id is required'
			)
		),
		'value' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'value must be a number'
			),
			'validValue' => array(
				'rule' => 'validValue',
				'message' => 'Invalid value!'
			)
		),
		'tier' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'value must be an integer',
				'last' => true
			),
			'range' => array(
				'rule' => array('range', -1, 5),
				'message' => 'value must be in range 0-4'
			),
		),
		'is_multiple' => array(
			'bool' => array(
				'rule' => 'boolean',
				'message' => 'is_multiple must be a boolean 0,1'
			)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ResidualParameterType',
		'UserAssociated' => array(
			'className' => 'User',
			'foreignKey' => 'associated_user_id',
		),
		'UserCompensationProfile',
		'UserCompensationAssociation' => array(
			'className' => 'AssociatedUser',
			'foreignKey' => 'user_compensation_profile_id',
		),
		'ProductsServicesType',
	);

/**
 * Custom validation rule, check the value type
 *
 * @param array $check Value to check
 * @return bool|string
 */
	public function validValue($check) {
		if (empty($check)) {
			return false;
		}
		$value = reset($check);
		//all current defined ResidualParameterType are percentages except for multiple
		$typeId = Hash::get($this->data, "{$this->alias}.residual_parameter_type_id");
		if ($typeId === ResidualParameterType::R_MULTIPLE || $typeId === ResidualParameterType::PR_MULTIPLE || $typeId === ResidualParameterType::MGR_MULTIPLE) {
			// for multiples allow a maximum precission of 4
			return Validation::numeric($value) && (strlen(strrchr($value, '.')) - 1 <= 4) && $value >= 0;
		}

		return $this->validPercentage(array($value));
	}

}
