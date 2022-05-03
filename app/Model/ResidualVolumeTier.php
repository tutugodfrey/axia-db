<?php

App::uses('AppModel', 'Model');

/**
 * ResidualVolumeTier Model
 *
 * @property User $User
 */
class ResidualVolumeTier extends AppModel {

	public $name = 'ResidualVolumeTier';

/**
 * Number of tiers defined in the table "residual_volume_tiers"
 */
	const DEFINED_TIERS = 4;

/**
 * Tier number for the override parameters
 */
	const OVERRIDE_TIER = 0;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_compensation_profile_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			)
		),
		// Tier 1
		'tier1_minimum_volume' => array(
			'rule' => array('validateTierMinimum', 'tier1_maximum_volume'),
			'allowEmpty' => true
		),
		'tier1_minimum_gp' => array(
			'rule' => array('validateTierMinimum', 'tier1_maximum_gp'),
			'allowEmpty' => true
		),
		'tier1_maximum_volume' => array(
			'rule' => array('validateTierMaximum', 'tier1_minimum_volume'),
			'allowEmpty' => true
		),
		'tier1_maximum_gp' => array(
			'rule' => array('validateTierMaximum', 'tier1_minimum_gp'),
			'allowEmpty' => true
		),
		// Tier 2
		'tier2_minimum_volume' => array(
			'rule' => array('validateTierMinimum', 'tier2_maximum_volume'),
			'allowEmpty' => true
		),
		'tier2_minimum_gp' => array(
			'rule' => array('validateTierMinimum', 'tier2_maximum_gp'),
			'allowEmpty' => true
		),
		'tier2_maximum_volume' => array(
			'rule' => array('validateTierMaximum', 'tier2_minimum_volume'),
			'allowEmpty' => true
		),
		'tier2_maximum_gp' => array(
			'rule' => array('validateTierMaximum', 'tier2_minimum_gp'),
			'allowEmpty' => true
		),
		// Tier 3
		'tier3_minimum_volume' => array(
			'rule' => array('validateTierMinimum', 'tier3_maximum_volume'),
			'allowEmpty' => true
		),
		'tier3_minimum_gp' => array(
			'rule' => array('validateTierMinimum', 'tier3_maximum_gp'),
			'allowEmpty' => true
		),
		'tier3_maximum_volume' => array(
			'rule' => array('validateTierMaximum', 'tier3_minimum_volume'),
			'allowEmpty' => true
		),
		'tier3_maximum_gp' => array(
			'rule' => array('validateTierMaximum', 'tier3_minimum_gp'),
			'allowEmpty' => true
		),
		// Tier 4
		'tier4_minimum_volume' => array(
			'rule' => array('validateTierMinimum', 'tier4_maximum_volume'),
			'allowEmpty' => true
		),
		'tier4_minimum_gp' => array(
			'rule' => array('validateTierMinimum', 'tier4_maximum_gp'),
			'allowEmpty' => true
		),
		'tier4_maximum_volume' => array(
			'rule' => array('validateTierMaximum', 'tier4_minimum_volume'),
			'allowEmpty' => true
		),
		'tier4_maximum_gp' => array(
			'rule' => array('validateTierMaximum', 'tier4_minimum_gp'),
			'allowEmpty' => true
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'UserCompensationProfile'
	);

/**
 * Custom validation rule to check if the value of a tier "minimum" field is less or equal than the "maximum" field
 *
 * @param array $check check values
 * @param string $comparisonField comparison field value
 * @param bool $allowEmptyComparison boolean to check if comparison can be empty
 *
 * @return bool|string
 */
	public function validateTierMinimum($check, $comparisonField, $allowEmptyComparison = true) {
		$field = key($check);
		$value = Hash::get($check, $field);
		$comparisonValue = Hash::get($this->data, "{$this->alias}.{$comparisonField}");

		if (!$this->validAmount($check)) {
			return __('The value must be a valid amount');
		}

		if (empty($comparisonValue)) {
			if ($allowEmptyComparison) {
				return true;
			} else {
				$this->invalidate($comparisonField, __('The maximum cannot be empty'));
				return __('The minimum must be less or equal than the maximum');
			}
		} elseif (!Validation::comparison($value, '<=', $comparisonValue)) {
			return __('The minimum must be less or equal than the maximum');
		}

		return true;
	}

/**
 * Custom validation rule to check if the value of a tier "maximum" field is greater or equal than the "minimum" field
 *
 * @param array $check check values
 * @param string $comparisonField comparison field value
 * @param bool $allowEmptyComparison boolean to check if comparison can be empty
 *
 * @return bool|string
 */
	public function validateTierMaximum($check, $comparisonField, $allowEmptyComparison = true) {
		$field = key($check);
		$value = Hash::get($check, $field);
		$comparisonValue = Hash::get($this->data, "{$this->alias}.{$comparisonField}");

		if (!$this->validAmount($check)) {
			return __('The value must be a valid amount');
		}

		if (empty($comparisonValue)) {
			if ($allowEmptyComparison) {
				return true;
			} else {
				$this->invalidate($comparisonField, __('The minimum cannot be empty'));
				return __('The maximum must be greater or equal than the minimum');
			}
		} elseif (!Validation::comparison($value, '>=', $comparisonValue)) {
			return __('The maximum must be greater or equal than the minimum');
		}

		return true;
	}
}
