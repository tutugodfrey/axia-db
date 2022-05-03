<?php

App::uses('AppModel', 'Model');

/**
 * ResidualTimeFactor Model
 *
 * @property User $User
 */
class ResidualTimeFactor extends AppModel {

	public $name = 'ResidualTimeFactor';

/**
 * Number of tiers defined in the table "residual_time_factors"
 */
	const DEFINED_TIERS = 4;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		// Tier 1
		'tier1_begin_month' => array(
			'rule' => array('checkBeginMonth', 'tier1_end_month', null),
		),
		'tier1_end_month' => array(
			'rule' => array('checkEndMonth', 'tier1_begin_month', 'tier2_begin_month'),
		),
		// Tier 2
		'tier2_begin_month' => array(
			'rule' => array('checkBeginMonth', 'tier2_end_month', 'tier1_end_month'),
		),
		'tier2_end_month' => array(
			'rule' => array('checkEndMonth', 'tier2_begin_month', 'tier3_begin_month'),
		),
		// Tier 3
		'tier3_begin_month' => array(
			'rule' => array('checkBeginMonth', 'tier3_end_month', 'tier2_end_month'),
		),
		'tier3_end_month' => array(
			'rule' => array('checkEndMonth', 'tier3_begin_month', 'tier4_begin_month'),
		),
		// Tier 4
		'tier4_begin_month' => array(
			'rule' => array('checkBeginMonth', 'tier4_end_month', 'tier3_end_month'),
		),
		'tier4_end_month' => array(
			'rule' => array('checkEndMonth', 'tier4_begin_month', null),
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

/**
 * Custom validation rule to check if the value of a tier "beginMonth" field is less or equal than the "endMonth" field and greater than the previous tier "endMonth"
 *
 * @param array $check value to check
 * @param string $endMonthField table field to store the end month of the current tier
 * @param string $previousEndMonthField table field to store the end month of the previous tier
 *
 * @return bool|string
 */
	public function checkBeginMonth($check, $endMonthField, $previousEndMonthField = null) {
		$field = key($check);
		$value = (int)Hash::get($check, $field);

		if (!is_null($value) && $value < 0) {
			return __('The value must be a valid positive integer');
		}

		//the beginMonth must be greater than the previous tier endMonth
		if (($previousEndMonthField !== null) && isset($this->data[$this->alias][$previousEndMonthField])) {
			$previousEndMonthValue = Hash::get($this->data, "{$this->alias}.{$previousEndMonthField}");
			if (Validation::naturalNumber($previousEndMonthValue) === false) {
				return true;
			}
			if (Validation::comparison($value, '<=', $previousEndMonthValue)) {
				return __('The begin month must be greater than the previous tier end month');
			}
		}

		$endMonthValue = Hash::get($this->data, "{$this->alias}.{$endMonthField}");
		if (Validation::naturalNumber($endMonthValue) === false) {
			return true;
		}
		if (!Validation::comparison($value, '<=', $endMonthValue)) {
			return __('The begin month must be less or equal than the end month');
		}

		return true;
	}

/**
 * Custom validation rule to check if the value of a tier "endMonth" field is greater or equal than the "beginMonth" field and less than the next tier "beginMonth"
 *
 * @param array $check value to check
 * @param string $beginMonthField table field to store the start month of the current tier
 * @param string $nextBeginMonthField table field to store the start month of the next tier
 *
 * @return bool|string
 */
	public function checkEndMonth($check, $beginMonthField, $nextBeginMonthField = null) {
		$field = key($check);
		$value = (int)Hash::get($check, $field);

		if (!is_null($value) && $value < 0) {
			return __('The value must be a valid positive integer');
		}

		//the endMonth must be less than the next tier beginMonth
		if (($nextBeginMonthField !== null) && isset($this->data[$this->alias][$nextBeginMonthField])) {
			$nextBeginMonthValue = Hash::get($this->data, "{$this->alias}.{$nextBeginMonthField}");
			if (Validation::naturalNumber($nextBeginMonthValue) === false) {
				return true;
			}
			if (Validation::comparison($value, '>=', $nextBeginMonthValue)) {
				return __('The end month must be less than the next tier begin month');
			}
		}

		$beginMonthValue = Hash::get($this->data, "{$this->alias}.{$beginMonthField}");
		if (Validation::naturalNumber($beginMonthValue) === false) {
			return true;
		}
		if (!Validation::comparison($value, '>=', $beginMonthValue)) {
			return __('The end month must be greater or equal than the begin month');
		}

		return true;
	}
}
