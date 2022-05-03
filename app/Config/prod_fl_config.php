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

$commonSet1 = array(
	array('tstField1', 'tstField2')
);
$maConstrainSet = array(
	'id',
	'merchant_id',
	'user_id',
	'products_services_type_id',
	'month',
	'year'
);
$ucConstrainSet = array(
	'id',
	'merchant_pricing_archive_id',
	'merchant_id',
	'user_id'
);
$config = array(
	//Debit
	'72a445f3-3937-4078-8631-1f569d6a30ed' => array(
		'MerchantPricingArchive' => array_merge($maConstrainSet,array(
			'm_rate_pct',
			'm_per_item_fee',
			'm_discount_item_fee',
			'm_statement_fee',
			'acquirer_id'
			)
		),
		'UserCostsArchive'=> array(
			'ucFieldn'
		)
	)
);
