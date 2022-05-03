<?php

App::uses('AppModel', 'Model');

/**
 * MerchantRejectStatus Model
 *
 */
class MerchantRejectStatus extends AppModel {

/**
 * Name of table column used for alternate sorting of records
 *
 */
	const SORT_FIELD = 'priority';

/**
 * Status received const
 *
 * @var UUID
 */
	const STATUS_RECEIVED = 'a4961985-c384-43df-b03d-b269f560d1f5';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_ADDITIONAL_REJECT = 'e683ee7e-97d2-4882-a5d5-bd79d5246a9e';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_COLLECTED_OTHER = 'c109f205-9d85-445d-8010-c0013b8f6141';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_COLLECTED_RESERVE = 'dd2123a3-aa3a-4747-bcfb-9b7641e48bc7';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_UPDATED_BANKING = 'c0a5e7f2-5634-4d8d-81a2-ff43a33abe03';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_RE_REJECTED = 'afa257c3-9dcb-4661-8946-91805171510c';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_NOT_COLLECTED = '87828851-cde7-4ddc-b0b9-213435caf2e8';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_ON_RESERVE = '79d18238-e403-4596-a37b-d7d4468795df';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_WRITTEN_OFF = '505e6e00-eda4-4b0e-b48e-00f1827655ec';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_RE_SUBMITTED = '4c82df9f-403b-4a8d-83e0-f85ed4b074d5';

/**
 * Status
 *
 * @var UUID
 */
	const STATUS_RE_SUBMITTED_CONFIRMED = '03fcd6ef-726b-40c4-8942-764d8bf6bc46';

/**
 * Status collected general
 *
 * @var int
 */
	const STATUS_COLLECTED_GENERAL = 1;

/**
 * Status not collected general
 *
 * @var int
 */
	const STATUS_NOT_COLLECTED_GENERAL = 0;

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Utils.List' => array(
			'positionColumn' => self::SORT_FIELD,
		)
	);

/**
 * displayField
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			),
		),
		'priority' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		)
	);

/**
 * List statues with collected opt
 *
 * @return array
 */
	public function listStatusesWithCollectedOpt() {
		$statuses = array(
			self::STATUS_RECEIVED => __('Received'),
			self::STATUS_RE_SUBMITTED => __('Re-Submitted'),
			self::STATUS_ADDITIONAL_REJECT => __('Additional Reject'),
			self::STATUS_UPDATED_BANKING => __('Updated Banking (NOC)'),
			__('COLLECTED') => array(
				self::STATUS_COLLECTED_GENERAL => __('Select this to search all COLLECTED'),
				self::STATUS_RE_SUBMITTED_CONFIRMED => __('Re-submitted - Confirmed'),
				self::STATUS_COLLECTED_RESERVE => __('Collected - Reserve'),
				self::STATUS_COLLECTED_OTHER => __('Collected - Other'),
			),
			__('NOT COLLECTED') => array(
				self::STATUS_NOT_COLLECTED_GENERAL => __('Select this to search all NOT COLLECTED'),
				self::STATUS_ON_RESERVE => __('On Reserve'),
				self::STATUS_RE_REJECTED => __('Re-Rejected'),
				self::STATUS_NOT_COLLECTED => __('Not Collected'),
				self::STATUS_WRITTEN_OFF => __('Written Off')
			),
		);

		return $statuses;
	}
}
