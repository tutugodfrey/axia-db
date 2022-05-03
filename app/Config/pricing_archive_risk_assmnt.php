<?php
/**
 * These joins are used for archiving UserCostsArchive.
 * The aliases used for each user are prefixed the with each users' role and it must match UserCostsArchive->_joinRolePrefixes
 * otherwise an error may ocurr.
 * Each of the users involed are the ones associated with the merchant by the 
 * user_id, sm_user_id, sm2_user_id, parther_id, rerefer_id, reseller_id
 */
$config = array(
		'URiskAssmntArchive' => array(
			'fields' => array(
				'RepProductsRisk.risk_assmnt_pct',
				'RepProductsRisk.risk_assmnt_per_item',
				'ManagerProductsRisk.risk_assmnt_pct',
				'ManagerProductsRisk.risk_assmnt_per_item',
				'Manager2ProductsRisk.risk_assmnt_pct',
				'Manager2ProductsRisk.risk_assmnt_per_item',
				'PartnerProductsRisk.risk_assmnt_pct',
				'PartnerProductsRisk.risk_assmnt_per_item',
				'RefProductsRisk.risk_assmnt_pct',
				'RefProductsRisk.risk_assmnt_per_item',
				'ResProductsRisk.risk_assmnt_pct',
				'ResProductsRisk.risk_assmnt_per_item',
			),
			'joins' => array(
				array(
					'table' => 'users_products_risks',
					'alias' => 'RepProductsRisk', //Prepending user role prefix and subfix CompProfile to ensure this is archived
					'type' => 'LEFT',
					'conditions' => array(
							'RepProductsRisk.merchant_id = Merchant.id',
							'RepProductsRisk.user_id = Merchant.user_id',
							'RepProductsRisk.products_services_type_id = ProductsServicesType.id',
						)
					),
				array(
					'table' => 'users_products_risks',
					'alias' => 'ManagerProductsRisk', //Prepending user role prefix and subfix CompProfile to ensure this is archived
					'type' => 'LEFT',
					'conditions' => array(
							'ManagerProductsRisk.merchant_id = Merchant.id',
							'ManagerProductsRisk.user_id = Merchant.sm_user_id',
							'ManagerProductsRisk.products_services_type_id = ProductsServicesType.id',
						)
					),
				array(
					'table' => 'users_products_risks',
					'alias' => 'Manager2ProductsRisk', //Prepending user role prefix and subfix CompProfile to ensure this is archived
					'type' => 'LEFT',
					'conditions' => array(
							'Manager2ProductsRisk.merchant_id = Merchant.id',
							'Manager2ProductsRisk.user_id = Merchant.sm2_user_id',
							'Manager2ProductsRisk.products_services_type_id = ProductsServicesType.id',
						)
					),
				array(
					'table' => 'users_products_risks',
					'alias' => 'PartnerProductsRisk', //Prepending user role prefix and subfix CompProfile to ensure this is archived
					'type' => 'LEFT',
					'conditions' => array(
							'PartnerProductsRisk.merchant_id = Merchant.id',
							'PartnerProductsRisk.user_id = Merchant.partner_id',
							'PartnerProductsRisk.products_services_type_id = ProductsServicesType.id',
						)
					),
				array(
					'table' => 'users_products_risks',
					'alias' => 'RefProductsRisk', //Prepending user role prefix and subfix CompProfile to ensure this is archived
					'type' => 'LEFT',
					'conditions' => array(
							'RefProductsRisk.merchant_id = Merchant.id',
							'RefProductsRisk.user_id = Merchant.referer_id',
							'RefProductsRisk.products_services_type_id = ProductsServicesType.id',
						)
					),
				array(
					'table' => 'users_products_risks',
					'alias' => 'ResProductsRisk', //Prepending user role prefix and subfix CompProfile to ensure this is archived
					'type' => 'LEFT',
					'conditions' => array(
							'ResProductsRisk.merchant_id = Merchant.id',
							'ResProductsRisk.user_id = Merchant.reseller_id',
							'ResProductsRisk.products_services_type_id = ProductsServicesType.id',
					)
				),
		)
	)
);