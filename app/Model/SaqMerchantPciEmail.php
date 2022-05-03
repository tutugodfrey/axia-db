<?php

App::uses('AppModel', 'Model');

/**
 * SaqMerchantPciEmail Model
 *
 * @property SaqMerchantPciEmailSent $SaqMerchantPciEmailSent
 */
class SaqMerchantPciEmail extends AppModel {

/**
 * Count number of labels
 * 
 * @var array
 */
	private static $__labelsCount = array();

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'priority' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'interval' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			)
		),
		'filename_prefix' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			)
		),
		'visible' => array(
			'boolean' => array(
				'rule' => array('boolean')
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SaqMerchantPciEmailSent'
	);

/**
 * Get the email label
 * 
 * @param int $id label id
 * @return mixed string or false
 */
	public static function getLabelById($id) {
		$labels = array(
			'90170996-5681-4ee2-9b7e-219f06c0e9b0' => 'Introdutory Email',
			'1e8b0888-a7d8-4a99-bd36-7a0841c568eb' => 'First Reminder',
			'daa48863-cf8b-425b-8bca-03bafe9ceb2b' => 'Secondary Reminder',
			'd27454cb-da68-409b-8784-b1ac561655ef' => 'Third Reminder',
			'11bb3864-6009-49c3-a1e5-b6fd9294068e' => 'Non-Compliance Reminder %s',
			'8c773d51-feee-4bc2-afe5-80b1c08edf9a' => 'Third Reminder',
			'ea4328ee-2334-4edd-ac7c-c12c212f144d' => 'Annual Reminder %s',
			'34662ac9-25a5-4de5-88b2-95a30b8cd2a1' => 'Annual Reminder %s',
		);

		if (isset($labels[$id])) {
			$index = $labels[$id];

			if (!isset(self::$__labelsCount[$index])) {
				self::$__labelsCount[$index] = 0;
			}

			return sprintf($labels[$id], ++self::$__labelsCount[$index]);
		}

		return false;
	}
}