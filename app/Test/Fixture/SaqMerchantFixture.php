<?php
/**
 * SaqMerchantFixture
 *
 */
class SaqMerchantFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_name' => array('type' => 'string', 'null' => false),
		'merchant_email' => array('type' => 'string', 'null' => false, 'length' => 100),
		'password' => array('type' => 'string', 'null' => false),
		'email_sent' => array('type' => 'datetime', 'null' => true),
		'billing_date' => array('type' => 'datetime', 'null' => true),
		'next_billing_date' => array('type' => 'datetime', 'null' => true),
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
			'id' => 'c053a959-5627-4635-b156-3ae213abf818',
			'merchant_id' => 'e3c717f4-4238-4856-96f6-107c7ec9381a',
			'merchant_name' => 'Mary F. Frese',
			'merchant_email' => 'creamnsugarinc@gmail.com',
			'password' => 'LMCKJ6fOMPc=',
			'email_sent' => null,
			'billing_date' => null,
			'next_billing_date' => null
		)
	);

}
