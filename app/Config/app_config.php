<?php
/**
 * App Config store all configuration params that does NOT depend on the
 * environment
 *
 * All params go here, with default values.
 * Environment params will be overwritten in app_config_environment.php file
 * This file is stored in the repo
 */
Configure::write('Security.csrfExpires', '+1 hour');

/**
 * Main app config file
 */
$config = array(
	'ApiFieldNames' => array(
		"merchant_mid" => array(
			'model_name' => 'Merchant',
			'field_name' => 'merchant_mid'
		),
		"application_id" => array(
			'model_name' => 'Merchant',
			'field_name' => 'onlineapp_application_id'
		),
		"merchant_dba" => array(
			'model_name' => 'Merchant',
			'field_name' => 'merchant_dba'
		),
		"contact" => array(
			'model_name' => 'Merchant',
			'field_name' => 'merchant_contact'
		),
		"is_active" => array(
			'model_name' => 'Merchant',
			'field_name' => 'active'
		),
		"contact_email" => array(
			'model_name' => 'Merchant',
			'field_name' => 'merchant_email'
		),
		"external_record_id" => array(
			'model_name' => 'SalesForceAccount',
			'field_name' => 'value'
		),
		"corp_name" => array(
			'model_name' => 'Address',
			'field_name' => 'address_title'
		),
		"corp_address" => array(
			'model_name' => 'Address',
			'field_name' => 'address_street'
		),
		"corp_city" => array(
			'model_name' => 'Address',
			'field_name' => 'address_city'
		),
		"corp_state" => array(
			'model_name' => 'Address',
			'field_name' => 'address_state'
		),
		"corp_zip" => array(
			'model_name' => 'Address',
			'field_name' => 'address_zip'
		),
		"corp_phone" => array(
			'model_name' => 'Address',
			'field_name' => 'address_phone'
		),
		"business_address" => array(
			'model_name' => 'Address',
			'field_name' => 'address_street'
		),
		"business_city" => array(
			'model_name' => 'Address',
			'field_name' => 'address_city'
		),
		"business_state" => array(
			'model_name' => 'Address',
			'field_name' => 'address_state'
		),
		"business_zip" => array(
			'model_name' => 'Address',
			'field_name' => 'address_zip'
		),
		"business_phone" => array(
			'model_name' => 'Address',
			'field_name' => 'address_phone'
		),
		"rep_full_name" => array(
			'model_name' => 'User',
			'field_name' => 'fullname'
		),
		"setup_referrer" => array(
			'model_name' => 'User',
			'field_name' => 'fullname'
		),
		"setup_reseller" => array(
			'model_name' => 'User',
			'field_name' => 'fullname'
		),
		"setup_partner" => array(
			'model_name' => 'User',
			'field_name' => 'fullname'
		),
		"org_name" => array(
			'model_name' => 'Organization',
			'field_name' => 'name'
		),
		"region_name" => array(
			'model_name' => 'Region',
			'field_name' => 'name'
		),
		"subregion_name" => array(
			'model_name' => 'Subregion',
			'field_name' => 'name'
		),
		"expected_install_date" => array(
			'model_name' => 'TimelineEntry',
			'field_name' => 'timeline_date_completed'
		),
	),
	'App' => array(
		'axia360Email' => 'billing@axia360.com',
		'userClass' => 'User',
		'files' => array(
			'path' => 'files',
		),
		'productClasses' => array(
			'pst_1' => array(
				'className' => 'Ach',
				'classId' => 'pst_1',
			),
			'pst_2' => array(
				'className' => 'WebBasedAch',
				'classId' => 'pst_2',
			),
			'pst_3' => array(
				'className' => 'CheckGuarantee',
				'classId' => 'pst_3',
			),
			'pst_4' => array(
				'className' => 'Gateway1',
				'classId' => 'pst_4',
			),
			'pst_5' => array(
				'className' => 'GiftCard',
				'classId' => 'pst_5',
			),
			'p_set' => array(
				'className' => 'ProductSetting',
				'classId' => 'p_set',
			),
			'pst_6' => array(
				'className' => 'PaymentFusion',
				'classId' => 'pst_6',
				//This monthly fee threshold designates when this product is setup as hardware as a sevice HAAS
				'min_haas_threshold' => 30
			),
		),
		'encDbFields' => array(
			'ach_mi_w_dsb_routing_number' => 'ach_mi_w_dsb_routing_number',
			'ach_mi_w_dsb_account_number' => 'ach_mi_w_dsb_account_number',
			'ach_mi_w_fee_routing_number' => 'ach_mi_w_fee_routing_number',
			'ach_mi_w_fee_account_number' => 'ach_mi_w_fee_account_number',
			'ach_mi_w_rej_routing_number' => 'ach_mi_w_rej_routing_number',
			'ach_mi_w_rej_account_number' => 'ach_mi_w_rej_account_number',
			'merchant_tin' => 'merchant_tin',
			'merchant_d_and_b' => 'merchant_d_and_b',
			'bank_routing_number' => 'bank_routing_number',
			'bank_dda_number' => 'bank_dda_number',
			'fees_routing_number' => 'fees_routing_number',
			'fees_dda_number' => 'fees_dda_number',
			'owner_social_sec_no' => 'owner_social_sec_no',
			'te_amex_number' => 'te_amex_number',
			'te_diners_club_number' => 'te_diners_club_number',
			'te_discover_number' => 'te_discover_number',
			'te_jcb_number' => 'te_jcb_number',
			)
	),
	'Rbac' => array(
		'enabled' => true
	),
	'AssociatedUserRoles' => array(
		'SalesManager' => array(
			'label' => 'Sales Manager',
			'maxAssociated' => 3,
			'roles' => array(
				'SM',
				'SM2'
			)
		),
		'Installer' => array(
			'label' => 'Installer',
			'maxAssociated' => 3,
			'roles' => array(
				'Installer'
			)
		),
		'PartnerRep' => array(
			'label' => 'Partner Rep',
			//unlimited
			'maxAssociated' => null,
			'roles' => array(
				'PartnerRep',
			)
		),
		'Rep' => array(
			'label' => 'Rep',
			'maxAssociated' => 1,
			'roles' => array(
				'Rep'
			)
		),
		'Partner' => array(
			'label' => 'Partner',
			//unlimited
			'maxAssociated' => null,
			'roles' => array(
				'Partner'
			)
		),
		'AccountManager' => array(
			'label' => 'Account Manager',
			//unlimited
			'maxAssociated' => null,
			'roles' => array(
				'Account Manager'
			)
		),
		'Referrer' => array(
			'label' => 'Referrer',
			//unlimited
			'maxAssociated' => null,
			'roles' => array(
				'Referrer'
			)
		),
		'Reseller' => array(
			'label' => 'Reseller',
			//unlimited
			'maxAssociated' => null,
			'roles' => array(
				'Reseller'
			)
		),

	),
	//List of products that should activate on merchant upload contingent upon specified conditions
	'OnUploadMerchantProducts' => array(
		"OtherGroup" => array(
			array(
				//Must Match value in DB products_services_types
				'product_name' => 'Credit Monthly',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '', //array|string leave blank if not applicable
						'bet_table_is' => '', //array|string leave blank if not applicable
						'always' => true,
						'enabled_for_rep' => null, //null|FALSE if not applicable (will only be evaluated when true)
				)
			),
		),
		"AmexGroup" => array(
			array(
				'product_name' => 'American Express Discount',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Non Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Non Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Settled Items',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'American Express Flat Rate',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '3203',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
		),
		"VisaGroup" => array(
			array(
				'product_name' => 'Visa Discount',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => null,
				)
			),
			array(
				'product_name' => 'Visa Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Visa Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Visa Non Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Visa Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Visa Non Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Visa Settled Items',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Visa Flat Rate',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '4203',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
		),
		"MasterCardGroup" => array(
			array(
				'product_name' => 'MasterCard Discount',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => null,
				)
			),
			array(
				'product_name' => 'MasterCard Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'MasterCard Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'MasterCard Non Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'MasterCard Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'MasterCard Non Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'MasterCard Settled Items',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'MasterCard Flat Rate',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '5203',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
		),
		"DiscoverGroup" => array(
			array(
				'product_name' => 'Discover Discount',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Non Dial Authorizations',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Non Dial Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Flat Rate',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '6203',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),
			array(
				'product_name' => 'Discover Settled Items',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => false,
						'enabled_for_rep' => true,
				)
			),

		),
		"DebitGroup" => array(
			array(
				'product_name' => 'Debit Monthly',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => false,
				)
			),
			array(
				'product_name' => 'Debit Discount',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => false,
				)
			),
			array(
				'product_name' => 'Debit Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => false,
				)
			),
		),
		"EbtGroup" => array(
			array(
				'product_name' => 'EBT Monthly',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => false,
				)
			),
			array(
				'product_name' => 'EBT Discount',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => false,
				)
			),
			array(
				'product_name' => 'EBT Sales',
				//Conditions that indicate when a product should be activated upon merchant upload
				'activate_when' => array(
						'bet_table_is_not' => '',
						'bet_table_is' => '',
						'always' => true,
						'enabled_for_rep' => false,
				)
			),
		),
	)
);
