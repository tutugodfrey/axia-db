<?php
/*
*
* Configuration file for mapping fields to create Pricing Archives data
*
* This configuration is a map of fields used to save product pricing data into pricing archives which may vary widely from product to product.
* If the set of products for which you need data is already defined in CommonDataProductNames, but the data you need is not the same as
* the fieldmaps already defined, you can define your own fieldmap within the array index where those existing CommonDataProductNames are.
* To do so, add a field map array with a unique field map name as the key (i.e. 'PricingArchiveFieldMap') and fill it up with your field map making
* sure to follow the same structure and naming convention as all other field maps. However, of both, the products and the data you are daling with is diferent from
* anything already defined you may add an entirely new index to the ArchiveFieldMap and define both your CommonDataProductNames and your fieldMap.
*
* ************ - Map Description:
* 1) fields array must be in dot syntax "Model.field" to prevent ambiguitiy in any queries being geneated with that information.
* 2) Joins for 'UserCostsModels' which are associated with each UserCompensationProfiles are generated automatically in the UserCostArchive
*	 model. Any other needed joins should be specified in the UserCostArchiveFieldMap joins.
* 3) 'UserCostArchiveFieldMap' Model fields:
*		- 'fields': Place model fields here that are independent from users (i.e. MerchantPricing.<field_name>)
*		- 'userCostsFields': THE FIELD NAMED 'CompProfile.user_id' IS REQUIRED AND MUST EXIST INSIDE EVERY userCostsFields
				The UserCostsArchive aliases all joins for each user, the fields placed here
*				will be used to get data for each one of those joins. Insert the field name with the usual model name
*				dot syntax (i.e Model.field) DO NOT USE ANY MODEL ALIASES.
*				The userCostsFields fields will be automatically modified to match its corresponding unique model alias which
*				was generated for the join.
*		- 'joinConditions': These conditions are for UserCostsModels joins. If 'userCostsFields' are specified, then 'joinConditions'
*				must also be specified (or viceversa) for each UserCostsModels specified in this array.
*		- 'joins': Insert any aditional needed joins in this array diferent from UserCostsModels.
*				For example if UserParameter data is needed add a join array here and in the 'fields' array insert
*				the fields that correspond to this join. Do not insert the fields in 'userCostsFields' in this case.
*		Importan Note: Data resulting from this non-UserCostsModels query will not be archived unless the following name convesion is followed for the model alias:
*					[UserRole][ModelName]CompProfile
*					The UserRole must match those defined un UserCostsArchives::$joinPrefixes[]
*					CompProfile is the subfix that tells the model the archive the data
*
*
*/
$config = array(
	// Number of merchants to paginate in theuser cost query
	'UserCostQueryPagination' => 2000,
	'UserCostsModels' => array(
			'GatewayCostStructure',
			'Bet',
			'AchRepCost',
			'AddlAmexRepCost',
			'RepMonthlyCost',
			'WebAchRepCost',
			'RepProductSetting',
			'PaymentFusionRepCost'
	),
	'ArchiveFieldMap' => array(
		array(
			//This field map is for ALL products that belong to the ProductSetting model Configure::read("App.productClasses.p_set.className")
			'CommonDataProductNames' => array(
				'Corral License Fee'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'ProductSetting' => array(
						'(("ProductSetting"."monthly_total")) As "NewArchiveData__m_statement_fee"',
						'(("ProductSetting"."rate")) As "NewArchiveData__m_rate_pct"',
						'(("ProductSetting"."rate")) As "NewArchiveData__original_m_rate_pct"',

					),
				),
				'joinConditions' => array(
					'ProductSetting' => array(
						'Merchant.id = ProductSetting.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				//No user costs archive for this product
				'fields' => array(),
				'userCostsFields' => array(
					'CompProfile.user_id'
				),
				'joinConditions' => array(),
				'joins' => array(),
			)
		),
		array(
			'CommonDataProductNames' => array(
				'Payment Fusion'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'PaymentFusion' => array(
						'(("PaymentFusion"."monthly_total")) As "NewArchiveData__m_statement_fee"',
						'(("PaymentFusion"."per_item_fee")) As "NewArchiveData__m_per_item_fee"',
						'(("PaymentFusion"."generic_product_mid")) As "NewArchiveData__generic_product_mid"',
						'(("PaymentFusion"."rate")) As "NewArchiveData__m_rate_pct"',
						'(("PaymentFusion"."rate")) As "NewArchiveData__original_m_rate_pct"',
					),
				),
				'joinConditions' => array(
					'PaymentFusion' => array(
						'Merchant.id = PaymentFusion.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'PaymentFusionRepCost.rep_per_item',
						'PaymentFusionRepCost.rep_monthly_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(),
				'joins' => array(),
			)
		),
		array(
			'CommonDataProductNames' => array(
				'ACH'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'Ach' => array(
						'(("Ach"."ach_rate")) As "NewArchiveData__m_rate_pct"',
						'(("Ach"."ach_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("Ach"."ach_statement_fee")) As "NewArchiveData__m_statement_fee"',
						'(("Ach"."ach_per_item_fee")) As "NewArchiveData__m_per_item_fee"',
						'(("Ach"."ach_provider_id")) As "NewArchiveData__provider_id"',
					),
				),
				'joinConditions' => array(
					'Ach' => array(
						'Merchant.id = Ach.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => Configure::read('URiskAssmntArchive.fields'),
				'userCostsFields' => array(
						'AchRepCost.rep_per_item',
						'AchRepCost.rep_rate_pct',
						'AchRepCost.rep_monthly_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'AchRepCost' => array(
							'AchRepCost.ach_provider_id = Ach.ach_provider_id'
						)
					),
				'joins' => Configure::read('URiskAssmntArchive.joins')
			)
		),
		array(
			'CommonDataProductNames' => array(
				'Gateway 1'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'Gateway1' => array(
						'(("Gateway1"."gw1_rate")) As "NewArchiveData__m_rate_pct"',
						'(("Gateway1"."gw1_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("Gateway1"."gw1_per_item")) As "NewArchiveData__m_per_item_fee"',
						'(("Gateway1"."gw1_statement")) As "NewArchiveData__m_statement_fee"',
						'(("Gateway1"."gw1_mid")) As "NewArchiveData__gateway_mid"',
					),
				),
				'joinConditions' => array(
					'Gateway1' => array(
						'Merchant.id = Gateway1.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array_merge(Configure::read('URiskAssmntArchive.fields'), 
					array(
						'Gateway1.addl_rep_statement_cost',
						)
				),
				'userCostsFields' => array(
						'GatewayCostStructure.rep_per_item',
						'GatewayCostStructure.rep_rate_pct',
						'GatewayCostStructure.rep_monthly_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'GatewayCostStructure' => array(
							'GatewayCostStructure.gateway_id = Gateway1.gateway_id'
						)
					),
				'joins' => Configure::read('URiskAssmntArchive.joins')
			)
		),
		array(
			'CommonDataProductNames' => array(
				'Visa Sales'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
		),
		array(
			'CommonDataProductNames' => array(
				'MasterCard Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
		),
		array(
			'CommonDataProductNames' => array(
				'Discover Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						//Use billing_discover_auth unless is null
						'((CASE WHEN "MerchantPricing"."billing_discover_auth" >=0 AND "MerchantPricing"."billing_discover_auth" != "MerchantPricing"."billing_mc_vi_auth" THEN "MerchantPricing"."billing_discover_auth" ELSE "MerchantPricing"."billing_mc_vi_auth" END)) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
				),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
		),
	array(
			'CommonDataProductNames' => array(
				'American Express Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_amex_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
				),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Debit Sales',
				'Debit Discount',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."debit_processing_rate")) As "NewArchiveData__m_rate_pct"',
						'(("MerchantPricing"."debit_processing_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("MerchantPricing"."debit_auth_fee")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."debit_access_fee")) As "NewArchiveData__m_statement_fee"',
						'(("MerchantPricing"."db_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'Bet.pct_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.db_bet_table_id'
						),
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'EBT Sales',
				'EBT Discount',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."ebt_processing_rate")) As "NewArchiveData__m_rate_pct"',
						'(("MerchantPricing"."ebt_processing_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("MerchantPricing"."ebt_auth_fee")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ebt_access_fee")) As "NewArchiveData__m_statement_fee"',
						'(("MerchantPricing"."ebt_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'Bet.pct_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ebt_bet_table_id'
						),
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((CASE WHEN "MerchantPricing"."billing_discover_auth" >=0 AND "MerchantPricing"."billing_discover_auth" != "MerchantPricing"."billing_mc_vi_auth" THEN "MerchantPricing"."billing_discover_auth" ELSE "MerchantPricing"."billing_mc_vi_auth" END)) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_amex_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Non Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.non_dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Non Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.non_dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Non Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((CASE WHEN "MerchantPricing"."billing_discover_auth" >=0 AND "MerchantPricing"."billing_discover_auth" != "MerchantPricing"."billing_mc_vi_auth" THEN "MerchantPricing"."billing_discover_auth" ELSE "MerchantPricing"."billing_mc_vi_auth" END)) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.non_dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Non Dial Sales',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_amex_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.pi_cost',
						'Bet.non_dial_sales_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((CASE WHEN "MerchantPricing"."billing_discover_auth" >=0 AND "MerchantPricing"."billing_discover_auth" != "MerchantPricing"."billing_mc_vi_auth" THEN "MerchantPricing"."billing_discover_auth" ELSE "MerchantPricing"."billing_mc_vi_auth" END)) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_amex_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((CASE WHEN "MerchantPricing"."billing_discover_auth" >=0 AND "MerchantPricing"."billing_discover_auth" != "MerchantPricing"."billing_mc_vi_auth" THEN "MerchantPricing"."billing_discover_auth" ELSE "MerchantPricing"."billing_mc_vi_auth" END)) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_amex_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Non Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.non_dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Non Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_mc_vi_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.non_dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Non Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((CASE WHEN "MerchantPricing"."billing_discover_auth" >=0 AND "MerchantPricing"."billing_discover_auth" != "MerchantPricing"."billing_mc_vi_auth" THEN "MerchantPricing"."billing_discover_auth" ELSE "MerchantPricing"."billing_mc_vi_auth" END)) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.non_dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Non Dial Authorizations',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."billing_amex_auth")) As "NewArchiveData__m_per_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
					'MerchantPricing.wireless_auth_cost'
					),
				'userCostsFields' => array(
						'Bet.non_dial_auth_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Credit Monthly',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((COALESCE("MerchantPricing"."statement_fee", 0) + COALESCE("MerchantPricing"."gateway_access_fee", 0) + COALESCE("MerchantPricing"."wireless_access_fee", 0))) As "NewArchiveData__m_statement_fee"'
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(
						'MerchantPricing.total_wireless_term_cost',
					),
				'userCostsFields' => array(
						'RepMonthlyCost.credit_cost',
						'GatewayCostStructure.rep_monthly_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'RepMonthlyCost' => array(
							'RepMonthlyCost.bet_network_id = Merchant.bet_network_id',
						),
					'GatewayCostStructure' => array(
							'GatewayCostStructure.gateway_id = MerchantPricing.gateway_id',
						),
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Debit Monthly',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."debit_access_fee")) As "NewArchiveData__m_statement_fee"'
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'RepMonthlyCost.debit_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'RepMonthlyCost' => array(
							'RepMonthlyCost.bet_network_id = Merchant.bet_network_id',
						),
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'EBT Monthly',
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."ebt_access_fee")) As "NewArchiveData__m_statement_fee"'
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'RepMonthlyCost.ebt_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'RepMonthlyCost' => array(
							'RepMonthlyCost.bet_network_id = Merchant.bet_network_id',
						),
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Discount'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."processing_rate")) As "NewArchiveData__dscnt_proc_rate"',
						'(("MerchantPricing"."processing_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("MerchantPricing"."discount_item_fee")) As "NewArchiveData__m_discount_item_fee"',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
					'BetTable' => array(
						'(("BetTable"."bet_extra_pct")) As "NewArchiveData__bet_extra_pct"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						),
					'BetTable' => array(
						'BetTable.id = MerchantPricing.visa_bet_table_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => Configure::read('URiskAssmntArchive.fields'),
				'userCostsFields' => array(
						'Bet.pct_cost',
						'Bet.pi_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => Configure::read('URiskAssmntArchive.joins')
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Discount'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."processing_rate")) As "NewArchiveData__dscnt_proc_rate"',
						'(("MerchantPricing"."processing_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("MerchantPricing"."discount_item_fee")) As "NewArchiveData__m_discount_item_fee"',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
					'BetTable' => array(
						'(("BetTable"."bet_extra_pct")) As "NewArchiveData__bet_extra_pct"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						),
					'BetTable' => array(
						'BetTable.id = MerchantPricing.mc_bet_table_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => Configure::read('URiskAssmntArchive.fields'),
				'userCostsFields' => array(
						'Bet.pct_cost',
						'Bet.pi_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => Configure::read('URiskAssmntArchive.joins')
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Discount',
				'American Express Discount (Converted Merchants)'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."amex_processing_rate")) As "NewArchiveData__dscnt_proc_rate"',
						'(("MerchantPricing"."amex_processing_rate")) As "NewArchiveData__original_m_rate_pct"',
						'(("MerchantPricing"."discount_item_fee")) As "NewArchiveData__m_discount_item_fee"',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
					'BetTable' => array(
						'(("BetTable"."bet_extra_pct")) As "NewArchiveData__bet_extra_pct"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						),
					'BetTable' => array(
						'BetTable.id = MerchantPricing.amex_bet_table_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => Configure::read('URiskAssmntArchive.fields'),
				'userCostsFields' => array(
						'Bet.pct_cost',
						'Bet.pi_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => Configure::read('URiskAssmntArchive.joins')
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Discount'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'((CASE WHEN "MerchantPricing"."ds_processing_rate" IS NOT NULL AND "MerchantPricing"."ds_processing_rate" != "MerchantPricing"."processing_rate" THEN "MerchantPricing"."ds_processing_rate" ELSE "MerchantPricing"."processing_rate" END)) As "NewArchiveData__dscnt_proc_rate"',
						'((CASE WHEN "MerchantPricing"."ds_processing_rate" IS NOT NULL AND "MerchantPricing"."ds_processing_rate" != "MerchantPricing"."processing_rate" THEN "MerchantPricing"."ds_processing_rate" ELSE "MerchantPricing"."processing_rate" END)) As "NewArchiveData__original_m_rate_pct"',
						'(("MerchantPricing"."discount_item_fee")) As "NewArchiveData__m_discount_item_fee"',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
					'BetTable' => array(
						'(("BetTable"."bet_extra_pct")) As "NewArchiveData__bet_extra_pct"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						),
					'BetTable' => array(
						'BetTable.id = MerchantPricing.ds_bet_table_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array_merge(
						Configure::read('URiskAssmntArchive.fields'),
						array('MerchantPricing.ds_user_not_paid')
					),
				'userCostsFields' => array(
						'Bet.pct_cost',
						'Bet.pi_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => Configure::read('URiskAssmntArchive.joins')
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Visa Settled Items'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."visa_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'Bet.settlement_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.visa_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'MasterCard Settled Items'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."mc_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'Bet.settlement_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.mc_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'Discover Settled Items'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."ds_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array('MerchantPricing.ds_user_not_paid'),
				'userCostsFields' => array(
						'Bet.settlement_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.ds_bet_table_id'
						)
					),
				'joins' => array(),
			)
	),
	array(
			'CommonDataProductNames' => array(
				'American Express Settled Items'
			),
			'PricingArchiveFieldMap' => array(
				'sourceModels' => array(
					'MerchantPricing' => array(
						'Merchant.bet_network_id',
						'(("MerchantPricing"."amex_bet_table_id")) As "NewArchiveData__bet_table_id"',
					),
				),
				'joinConditions' => array(
					'MerchantPricing' => array(
						'Merchant.id = MerchantPricing.merchant_id'
						)
					),
			),
			'UserCostArchiveFieldMap' => array(
				'fields' => array(),
				'userCostsFields' => array(
						'Bet.settlement_cost',
						'CompProfile.user_id',
					),
				'joinConditions' => array(
					'Bet' => array(
							'Bet.bet_table_id = MerchantPricing.amex_bet_table_id'
						)
					),
				'joins' => array(),
			)
		),
	)
);
