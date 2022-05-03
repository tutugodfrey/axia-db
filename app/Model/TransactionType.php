<?php

App::uses('AppModel', 'Model');

/**
 * TransactionType Model
 *
 */
class TransactionType extends AppModel {

/**
 * Transaction types ids
 *
 * @var string
 */
	const USER_LOGIN = "efc19985-cb87-452f-890b-1878c867f973";
	const CHANGE_REQUEST = "6df61985-0b71-4493-a758-967d3d2f549c";
	const MERCHANT_NOTE = "9e2a7eca-38f8-4177-b294-aeaf9ae30af1";
	const ACH_ENTRY = "7f37ce66-1865-4100-9ce0-4b0d316fe11c";
	const RECORD_UPDATED = "b5d908cb-9730-4778-93b8-c17b53e4450e";
	const EQUIPMENT_ORDER = "fab6bf5b-366c-4e37-9840-3f77fd58c6aa";
	const PROGRAMMING_CHANGE = "05956718-fffc-4d8f-b01a-b113f3a1f419";
	const UNSECURED_DATA_EXPORTED = "372be967-ea21-4ab8-87ce-7049d1cc6814";

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'system_transaction_description';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'transaction_type_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
}
