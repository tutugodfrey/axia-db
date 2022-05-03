<?php

App::uses('AppModel', 'Model');
App::uses('UserParameterType', 'Model');

/**
 * UserParameter Model
 *
 * @property User $User
 * @property UserParameterType $UserParameterType
 * @property MerchantAcquirer $MerchantAcquirer
 */
class UserParameter extends AppModel {

	public $validate = array(
		'user_parameter_type_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'user_parameter_type_id is required'
			)
		),
		'products_services_type_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'products_services_type_id is required'
			)
		),
		'user_compensation_profile_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'user_compensation_profile_id is required'
			)
		),
		'value' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'value must be a number'
			),
			'validValue' => array(
				'rule' => 'validValue',
				'message' => 'value type is incorrect'
			)
		),
		'is_multiple' => array(
			'bool' => array(
				'rule' => 'boolean',
				'message' => 'is_multiple must be a boolean 0,1'
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'UserParameterType',
		'ProductsServicesType',
		'MerchantAcquirer',
		'UserCompensationProfile',
		'UserAssociated' => array(
			'className' => 'User',
			'foreignKey' => 'associated_user_id',
		),
		'UserCompensationAssociation' => array(
			'className' => 'AssociatedUser',
			'foreignKey' => 'user_compensation_profile_id',
		)
	);

/**
 * Custom validation rule, check the value type
 *
 * @param array $check check values
 *
 * @return bool
 */
	public function validValue($check) {
		if (empty($check)) {
			return false;
		}
		$value = reset($check);
		$userParameterTypeId = Hash::get($this->data, "{$this->alias}.user_parameter_type_id");

		//we should check the value type, but this could lead to performance issues, maybe we could introduce some caching later
		if ($userParameterTypeId === UserParameterType::DONT_DISPLAY ||
			$userParameterTypeId === UserParameterType::ENABLED_FOR_REP ||
			$userParameterTypeId === UserParameterType::TIER_PROD) {
			return Validation::boolean($value);
		}

		return $this->validPercentage(array($value));
	}

/**
 * getEnabledProducts
 * Returns a list of products that are enabled on the given user compensation profile id
 *
 * @param string $uCompProfId a user compensation profile id
 *
 * @return array
 */
	public function getEnabledProductsList($uCompProfId) {
		$result = $this->find('list', array(
			'contain' => array(
				'ProductsServicesType'
			),
			'fields' => array('ProductsServicesType.id', 'ProductsServicesType.products_services_description'),
			'conditions' => array(
				//retrieve only enabled products
				'UserParameter.user_compensation_profile_id' => $uCompProfId,
				'UserParameter.user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP,
				'UserParameter.value' => 1,

			),
			'order' => array(
				'ProductsServicesType.products_services_description'
			)
		));
		return !empty($result)? $result: [];
	}

}
