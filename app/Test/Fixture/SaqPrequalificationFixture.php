<?php

/**
 * SaqPrequalificationFixture
 *
 */
class SaqPrequalificationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()',
			'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'result' => array('type' => 'string', 'null' => false, 'length' => 4),
		'date_completed' => array('type' => 'datetime', 'null' => false),
		'control_scan_code' => array('type' => 'integer', 'null' => true),
		'control_scan_message' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 'saq-prequalification-43',
			'old_id' => 43,
			'saq_merchant_id' => 4034,
			'result' => 'A',
			'date_completed' => '2011-01-04 23:24:03.500117',
			'control_scan_code' => null,
			'control_scan_message' => null
		),
		array(
			'id' => 'saq-prequalification-44',
			'old_id' => 44,
			'saq_merchant_id' => 4034,
			'result' => 'B',
			'date_completed' => '2011-01-04 23:27:42.369238',
			'control_scan_code' => null,
			'control_scan_message' => null
		),
		array(
			'id' => 'saq-prequalification-661',
			'old_id' => 661,
			'saq_merchant_id' => 6813,
			'result' => 'D',
			'date_completed' => '2011-02-17 13:58:43',
			'control_scan_code' => 0,
			'control_scan_message' => 'The transaction was successful'
		),
		array(
			'id' => 'saq-prequalification-3380',
			'old_id' => 3380,
			'saq_merchant_id' => 8566,
			'result' => 'B',
			'date_completed' => '2011-06-21 07:59:06',
			'control_scan_code' => null,
			'control_scan_message' => null
		),
		array(
			'id' => 'saq-prequalification-827',
			'old_id' => 827,
			'saq_merchant_id' => 6821,
			'result' => 'C',
			'date_completed' => '2011-02-24 14:25:12',
			'control_scan_code' => 0,
			'control_scan_message' => 'The transaction was successful'
		)
	);

}
