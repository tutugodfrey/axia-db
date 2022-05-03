<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $ach_providers = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'provider_name' => array('type' => 'string', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $aches = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'ach_mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'ach_expected_annual_sales' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_average_transaction' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_estimated_max_transaction' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_written_pre_auth' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_nonwritten_pre_auth' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_merchant_initiated_perc' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_consumer_initiated_perc' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_monthly_gateway_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_monthly_minimum_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_batch_upload_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_reject_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_add_bank_orig_ident_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_file_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_ccd_nw' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_ccd_w' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_ppd_nw' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_ppd_w' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_application_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_expedite_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_tele_training_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_mi_w_dsb_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_mi_w_dsb_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_w_dsb_account_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_w_fee_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_mi_w_fee_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_w_fee_account_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_w_rej_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_mi_w_rej_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_w_rej_account_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_nw_dsb_account_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_nw_dsb_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_nw_dsb_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_mi_nw_fee_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_mi_nw_fee_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_nw_fee_account_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_nw_rej_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_mi_nw_rej_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_mi_nw_rej_account_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_w_dsb_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_ci_w_dsb_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_w_dsb_account_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_w_fee_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_ci_w_fee_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_w_fee_account_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_w_rej_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_ci_w_rej_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_w_rej_account_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_nw_dsb_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_ci_nw_dsb_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_nw_dsb_account_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_nw_fee_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_ci_nw_fee_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_nw_fee_account_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_nw_rej_bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ach_ci_nw_rej_routing_number' => array('type' => 'integer', 'null' => true),
		'ach_ci_nw_rej_account_number' => array('type' => 'integer', 'null' => true),
		'ach_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_per_item_fee' => array('type' => 'float', 'null' => true),
		'ach_provider_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ach_risk_assessment' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ach_merchantid' => array('unique' => false, 'column' => 'merchant_id'),
			'ach_mid' => array('unique' => false, 'column' => 'ach_mid'),
			'achs_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $achs_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'ach_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'ach_provider_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_cs_rate_pct' => array('type' => 'float', 'null' => true),
		'r_cs_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_sage_statement_fee' => array('type' => 'float', 'null' => true),
		'r_check_gateway_statement_fee' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_ach_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_ach_eft_ccd_nw' => array('type' => 'float', 'null' => true),
		'm_statement_fee' => array('type' => 'float', 'null' => true),
		'r_cs_ach_statement' => array('type' => 'float', 'null' => true),
		'r_ach_risk_assessment' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ach_pricing_index' => array('unique' => false, 'column' => 'ach_pricing_id'),
			'achs_archives_ach_providers_index' => array('unique' => false, 'column' => 'ach_provider_id'),
			'achs_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'achs_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true),
		'model' => array('type' => 'string', 'null' => true, 'default' => null),
		'foreign_key' => array('type' => 'integer', 'null' => true),
		'alias' => array('type' => 'string', 'null' => true, 'default' => null),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $address_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'address_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'address_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $addresses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'address_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'address_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_owner_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'address_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'address_street' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'address_city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'address_state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2),
		'address_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'address_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'address_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'address_phone2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'address_phone_ext' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'address_phone2_ext' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'address_owner_uidx' => array('unique' => true, 'column' => 'merchant_owner_id'),
			'address_merchant_type_idx' => array('unique' => false, 'column' => array('address_type_id', 'merchant_id')),
			'addresses_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $adjustments = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'adj_seq_number' => array('type' => 'integer', 'null' => false),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'adj_date' => array('type' => 'date', 'null' => true),
		'adj_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'adj_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'adjustments_user_idx' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $admin_entity_views = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'entity_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'admin_entity_view_user_idx' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $amex_auths_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'billing_amex_auth' => array('type' => 'float', 'null' => true),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'amex_auths_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'amex_auths_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'amex_auths_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $amexes = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'amex_processing_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'amex_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'amexes_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $app_statuses = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_ach_app_status_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_cost' => array('type' => 'float', 'null' => true),
		'axia_cost' => array('type' => 'float', 'null' => true),
		'rep_expedite_cost' => array('type' => 'float', 'null' => true),
		'axia_expedite_cost_tsys' => array('type' => 'float', 'null' => true),
		'axia_expedite_cost_sage' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'app_statuses_merchant_ach_app_status_id_idx' => array('unique' => false, 'column' => 'merchant_ach_app_status_id'),
			'app_statuses_user_id_idx' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $aros = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true),
		'model' => array('type' => 'string', 'null' => true, 'default' => null),
		'foreign_key' => array('type' => 'integer', 'null' => true),
		'alias' => array('type' => 'string', 'null' => true, 'default' => null),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $aros_acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false),
		'aco_id' => array('type' => 'integer', 'null' => false),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'aro_aco_key' => array('unique' => true, 'column' => array('aro_id', 'aco_id'))
		),
		'tableParameters' => array()
	);

	public $associated_users = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'associated_user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'role' => array('type' => 'string', 'null' => false, 'length' => 36),
		'permission_level' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $attrition_ratios = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'associated_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'percentage' => array('type' => 'float', 'null' => true),
		'statement_fee' => array('type' => 'float', 'null' => true),
		'virtual_terminal_gateway_fee' => array('type' => 'float', 'null' => true),
		'multiple' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $authorizes = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'transaction_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'authorizes_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $bankcards = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bc_mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'bet_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15),
		'bc_processing_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_average_ticket' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_max_transaction_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_card_present_swipe' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_card_not_present' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_card_present_imprint' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_direct_to_consumer' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_business_to_business' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_government' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_annual_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_min_month_process_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_chargeback_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_aru_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_voice_auth_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_te_amex_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'bc_te_amex_auth_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_te_diners_club_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'bc_te_diners_club_auth_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_te_discover_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'bc_te_discover_auth_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_te_jcb_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'bc_te_jcb_auth_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_monthly_support_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_online_mer_report_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_mobile_access_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_mobile_transaction_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_application_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_expedite_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_mobile_setup_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_equip_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_phys_prod_tele_train_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_pt_equip_reprog_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_vt_monthly_support_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_vt_gateway_access_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_vt_application_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_vt_expedite_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_vt_prod_tele_train_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_vt_lease_rental_deposit' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_hidden_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_te_amex_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'bc_te_diners_club_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'bc_te_discover_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'bc_te_jcb_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'bc_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_eft_secure_flag' => array('type' => 'integer', 'null' => true),
		'bc_pos_partner_flag' => array('type' => 'integer', 'null' => true),
		'bc_pt_online_rep_report_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_micros_ip_flag' => array('type' => 'integer', 'null' => true),
		'bc_micros_dialup_flag' => array('type' => 'integer', 'null' => true),
		'bc_micros_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_te_num_items' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_wireless_flag' => array('type' => 'integer', 'null' => true),
		'bc_wireless_terminals' => array('type' => 'integer', 'null' => true),
		'bc_usaepay_flag' => array('type' => 'integer', 'null' => true),
		'bc_epay_retail_flag' => array('type' => 'integer', 'null' => true),
		'bc_usaepay_rep_gtwy_cost_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'bc_usaepay_rep_gtwy_add_cost_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'bc_card_not_present_internet' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_tgate_flag' => array('type' => 'integer', 'null' => true),
		'bc_petro_flag' => array('type' => 'integer', 'null' => true),
		'bc_risk_assessment' => array('type' => 'float', 'null' => true, 'default' => null),
		'gateway_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'bc_gw_rep_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_gw_rep_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_gw_rep_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'bc_gw_rep_features' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'bankcard_bcmid' => array('unique' => false, 'column' => 'bc_mid'),
			'bankcard_bet' => array('unique' => false, 'column' => 'bet_code'),
			'bankcard_urgac_index' => array('unique' => false, 'column' => 'bc_usaepay_rep_gtwy_add_cost_id'),
			'bankcard_urgc_index' => array('unique' => false, 'column' => 'bc_usaepay_rep_gtwy_cost_id'),
			'bankcards_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $bet_tables = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 64),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'product_name_index' => array('unique' => true, 'column' => 'name')
		),
		'tableParameters' => array()
	);

	public $bet_tables_card_types = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'bet_table_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'card_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'unique_table_card_type' => array('unique' => true, 'column' => array('bet_table_id', 'card_type_id'))
		),
		'tableParameters' => array()
	);

	public $bets = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'network_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bet_tables_card_types_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bc_cost' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'bc_pi' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'csv_cost' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'csv_pi' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'add_pct' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'unique_network_bet_tables_card_types' => array('unique' => true, 'column' => array('network_id', 'bet_tables_card_types_id', 'user_id'))
		),
		'tableParameters' => array()
	);

	public $cancellation_fees = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'cancellation_fee_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'cancellation_fee_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $card_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'card_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'card_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $change_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'change_type_old_id' => array('type' => 'integer', 'null' => true),
		'change_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $check_guarantee_providers = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'provider_name' => array('type' => 'string', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $check_guarantee_service_types = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'service_type' => array('type' => 'string', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $check_guarantees = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'cg_mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'cg_station_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'cg_account_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'cg_transaction_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'cg_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'cg_monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'cg_monthly_minimum_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'cg_application_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'check_guarantee_provider_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'check_guarantee_service_type_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'rep_processing_rate_pct' => array('type' => 'float', 'null' => true),
		'rep_per_item_cost' => array('type' => 'float', 'null' => true),
		'rep_monthly_cost' => array('type' => 'float', 'null' => true),
		'cg_risk_assessment' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'check_guarantees_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $check_guarantees_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'check_guarantee_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'check_guarantee_provider_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'check_guarantee_service_type_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_processing_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_cost' => array('type' => 'float', 'null' => true),
		'r_monthly_cost' => array('type' => 'float', 'null' => true),
		'r_risk_assessment' => array('type' => 'float', 'null' => true),
		'r_cs_gc_volume' => array('type' => 'integer', 'null' => true),
		'm_transaction_rate' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_monthly_fee' => array('type' => 'float', 'null' => true),
		'm_monthly_minimum_fee' => array('type' => 'float', 'null' => true),
		'm_application_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'check_guarantee_provider_index' => array('unique' => false, 'column' => 'check_guarantee_provider_id'),
			'check_guarantee_service_types_index' => array('unique' => false, 'column' => 'check_guarantee_service_type_id'),
			'check_guarantees_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'check_guarantees_archives_user_index' => array('unique' => false, 'column' => 'user_id'),
			'check_guarantees_pricing_index' => array('unique' => false, 'column' => 'check_guarantee_pricing_id')
		),
		'tableParameters' => array()
	);

	public $commission_fees = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'is_do_not_display_user' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'is_do_not_display_partner' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'user_app_fee' => array('type' => 'float', 'null' => true),
		'partner_app_fee' => array('type' => 'float', 'null' => true),
		'user_app_fee_profit' => array('type' => 'float', 'null' => true),
		'partner_app_fee_profit' => array('type' => 'float', 'null' => true),
		'user_app_fee_loss' => array('type' => 'float', 'null' => true),
		'partner_app_fee_loss' => array('type' => 'float', 'null' => true),
		'user_non_app_fee' => array('type' => 'float', 'null' => true),
		'partner_non_app_fee' => array('type' => 'float', 'null' => true),
		'user_non_app_fee_profit' => array('type' => 'float', 'null' => true),
		'partner_non_app_fee_profit' => array('type' => 'float', 'null' => true),
		'user_non_app_fee_loss' => array('type' => 'float', 'null' => true),
		'partner_non_app_fee_loss' => array('type' => 'float', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_index_1' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $commission_pricings = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'c_month' => array('type' => 'float', 'null' => false),
		'c_year' => array('type' => 'float', 'null' => false),
		'multiple' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_avg_ticket' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_bet_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 35),
		'ref_seq_number' => array('type' => 'integer', 'null' => true),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_seq_number' => array('type' => 'integer', 'null' => true),
		'products_services_type_id' => array('type' => 'string', 'null' => false, 'default' => 'BC', 'length' => 36),
		'num_items' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_risk_assessment' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'res_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'ref_p_pct' => array('type' => 'integer', 'null' => true),
		'res_p_pct' => array('type' => 'integer', 'null' => true),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'commission_pricing_index1' => array('unique' => false, 'column' => 'merchant_id'),
			'commission_pricing_index2' => array('unique' => false, 'column' => 'user_id'),
			'commission_pricing_index3' => array('unique' => false, 'column' => 'c_month'),
			'commission_pricing_index4' => array('unique' => false, 'column' => 'c_year'),
			'commission_pricing_products_services_type' => array('unique' => false, 'column' => 'products_services_type_id'),
			'commission_pricings_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $commission_reports = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'c_month' => array('type' => 'integer', 'null' => true),
		'c_year' => array('type' => 'integer', 'null' => true),
		'c_retail' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_rep_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_shipping' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_app_rtl' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_app_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_install' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_expecting' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_from_nw1' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_other_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_from_other' => array('type' => 'float', 'null' => true, 'default' => null),
		'c_business' => array('type' => 'float', 'null' => true, 'default' => null),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'order_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'axia_invoice_number' => array('type' => 'biginteger', 'null' => true),
		'ach_seq_number' => array('type' => 'integer', 'null' => true),
		'description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'split_commissions' => array('type' => 'boolean', 'null' => false),
		'ref_seq_number' => array('type' => 'integer', 'null' => true),
		'res_seq_number' => array('type' => 'integer', 'null' => true),
		'partner_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'partner_exclude_volume' => array('type' => 'boolean', 'null' => true),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_ach_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'commission_reports_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'cr_partner_id_index' => array('unique' => false, 'column' => 'partner_id'),
			'crnew_mid_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $credit_auths_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_all_auths' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_dial' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_non_dial' => array('type' => 'float', 'null' => true),
		'm_billing_mc_vi_auth' => array('type' => 'float', 'null' => true),
		'm_billing_discover_auth' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'credit_auths_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'credit_auths_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'credit_auths_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $credit_sales_volume_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_statement_fee' => array('type' => 'float', 'null' => true),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_mc_vi_rate_pct' => array('type' => 'float', 'null' => true),
		'm_ds_rate_pct' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'm_statement_fee' => array('type' => 'float', 'null' => true),
		'mc_vi_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'credit_sales_volume_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'credit_sales_volume_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'credit_sales_volume_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $debit_acquirers = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'debit_acquirer_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'debit_acquirers' => array('type' => 'string', 'null' => false, 'length' => 137),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $debit_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'acquirer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_cs_rate_pct' => array('type' => 'float', 'null' => true),
		'r_cs_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_cs_debit_monthly_fee' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_debit_discount_item_fee' => array('type' => 'float', 'null' => true),
		'm_statement_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'debit_archives_acquirer_index' => array('unique' => false, 'column' => 'acquirer_id'),
			'debit_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'debit_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'debit_archives_networks_index' => array('unique' => false, 'column' => 'network_id'),
			'debit_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $debit_auths_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_all_auths' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_dial' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_non_dial' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'debit_auths_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'debit_auths_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'debit_auths_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $debit_sales_volume_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_statement_fee' => array('type' => 'float', 'null' => true),
		'm_mc_vi_rate_pct' => array('type' => 'float', 'null' => true),
		'm_ds_rate_pct' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'm_statement_fee' => array('type' => 'float', 'null' => true),
		'mc_vi_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'debit_sales_volume_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'debit_sales_volume_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'debit_sales_volume_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $debits = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'transaction_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'monthly_num_items' => array('type' => 'float', 'null' => true, 'default' => null),
		'rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'acquirer_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'debits_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $discover_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'ds_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'bet_extra_pct' => array('type' => 'float', 'null' => true),
		'm_wireless_terminals' => array('type' => 'integer', 'null' => true),
		'refer_profit_pct' => array('type' => 'float', 'null' => true),
		'res_profit_pct' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'discover_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'discover_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'discover_archives_networks_index' => array('unique' => false, 'column' => 'network_id'),
			'discover_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $discover_auths_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'billing_discover_auth' => array('type' => 'float', 'null' => true),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'discover_auths_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'discover_auths_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'discover_auths_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $discover_bets = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'bet_code' => array('type' => 'string', 'null' => false, 'length' => 15),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $discover_sales_volume_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_ds_rate_pct' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'ds_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'discover_sales_volume_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'discover_sales_volume_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'discover_sales_volume_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $discover_user_bet_tables = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'bet_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'bet_code' => array('type' => 'string', 'null' => false, 'length' => 15),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network' => array('type' => 'string', 'null' => false, 'length' => 20),
		'network_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'pi' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'discover_user_bet_table_index1' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $discovers = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bet_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15),
		'disc_processing_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_average_ticket' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_risk_assessment' => array('type' => 'float', 'null' => true, 'default' => null),
		'gateway_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'disc_gw_rep_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_gw_rep_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'disc_gw_rep_features' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'discover_bet_index' => array('unique' => false, 'column' => 'bet_code'),
			'discovers_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $ebt_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'acquirer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_cs_rate_pct' => array('type' => 'float', 'null' => true),
		'r_cs_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_cs_statement_fee' => array('type' => 'float', 'null' => true),
		'ebt_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_ebt_auth_fee' => array('type' => 'float', 'null' => true),
		'm_ebt_discount_item_fee' => array('type' => 'float', 'null' => true),
		'm_ebt_access_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ebt_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'ebt_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'ebt_archives_networks_index' => array('unique' => false, 'column' => 'network_id'),
			'ebt_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $ebt_auths_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_all_auths' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_dial' => array('type' => 'float', 'null' => true),
		'r_per_item_fee_non_dial' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ebt_auths_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'ebt_auths_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'ebt_auths_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $ebts = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'transaction_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ebts_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $entities = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'entity' => array('type' => 'string', 'null' => false, 'length' => 36),
		'entity_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $equipment_items = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'equipment_item_old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'equipment_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'equipment_item_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'equipment_item_true_price' => array('type' => 'float', 'null' => true, 'default' => null),
		'equipment_item_rep_price' => array('type' => 'float', 'null' => true, 'default' => null),
		'active' => array('type' => 'integer', 'null' => true, 'default' => '1'),
		'warranty' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'equipment_item_active' => array('unique' => false, 'column' => 'active'),
			'equipment_item_type' => array('unique' => false, 'column' => 'equipment_type'),
			'equipment_item_warranty' => array('unique' => false, 'column' => 'warranty')
		),
		'tableParameters' => array()
	);

	public $equipment_programming_type_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'equipment_programming_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'programming_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'eptx_programming_idx' => array('unique' => false, 'column' => 'equipment_programming_id')
		),
		'tableParameters' => array()
	);

	public $equipment_programmings = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'programming_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'terminal_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'hardware_serial' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'terminal_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'network' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'provider' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'app_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'date_entered' => array('type' => 'date', 'null' => true),
		'date_changed' => array('type' => 'date', 'null' => true),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'serial_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'pin_pad' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'printer' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'auto_close' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'chain' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 6),
		'agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 6),
		'gateway_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'version' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'equipment_programming_appid' => array('unique' => false, 'column' => 'app_type'),
			'equipment_programming_merch_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'equipment_programming_serialnum' => array('unique' => false, 'column' => 'serial_number'),
			'equipment_programming_status' => array('unique' => false, 'column' => 'status'),
			'equipment_programming_userid' => array('unique' => false, 'column' => 'user_id'),
			'equipment_programmings_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $equipment_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'equipment_type_old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'equipment_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $gateway0s = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gw_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw_epay_retail_num' => array('type' => 'integer', 'null' => true),
		'gw_usaepay_rep_gtwy_cost_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'gw_usaepay_rep_gtwy_add_cost_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'gateway0s_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'gateway_gw_usaepay_add_cost' => array('unique' => false, 'column' => 'gw_usaepay_rep_gtwy_add_cost_id'),
			'gateway_gw_usaepay_cost' => array('unique' => false, 'column' => 'gw_usaepay_rep_gtwy_cost_id')
		),
		'tableParameters' => array()
	);

	public $gateway1s = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gw1_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_rep_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_rep_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_rep_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_rep_features' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'gateway_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gw1_monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw1_monthly_num_items' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'gateway1s_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'gw1_gatewayid' => array('unique' => false, 'column' => 'gateway_id')
		),
		'tableParameters' => array()
	);

	public $gateway2s = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gw2_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_rep_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_rep_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_rep_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_rep_features' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'gateway_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gw2_monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw2_monthly_num_items' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'gateway2s_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'gw2_gatewayid' => array('unique' => false, 'column' => 'gateway_id')
		),
		'tableParameters' => array()
	);

	public $gateways = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 57),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $gift_card_providers = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'provider_name' => array('type' => 'string', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $gift_cards = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gc_mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'gc_loyalty_item_fee' => array('type' => 'float', 'null' => true),
		'gc_gift_item_fee' => array('type' => 'float', 'null' => true),
		'gc_chip_card_one_rate_monthly' => array('type' => 'float', 'null' => true),
		'gc_chip_card_gift_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_chip_card_loyalty_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_smart_card_printing' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_card_reorder_fee' => array('type' => 'float', 'null' => true),
		'gc_loyalty_mgmt_database' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_application_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_equipment_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_misc_supplies' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_merch_prov_art_setup_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_news_provider_artwork_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_training_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gc_plan' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8),
		'gift_card_provider_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'gift_cards_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $giftcard_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'giftcard_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gift_card_provider_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_cs_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_cs_statement' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_statement_fee' => array('type' => 'float', 'null' => true),
		'm_gc_plan' => array('type' => 'string', 'null' => true, 'length' => 25),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'giftcard_archives_giftcard_providers_index' => array('unique' => false, 'column' => 'gift_card_provider_id'),
			'giftcard_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'giftcard_archives_user_index' => array('unique' => false, 'column' => 'user_id'),
			'giftcard_pricing_index' => array('unique' => false, 'column' => 'giftcard_pricing_id')
		),
		'tableParameters' => array()
	);

	public $groups = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'group_old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'group_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $last_deposit_reports = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_dba' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'last_deposit_date' => array('type' => 'date', 'null' => true),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'last_deposit_reports_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_ach_app_statuses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'app_status_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'app_status_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'rank' => array('type' => 'integer', 'null' => true),
		'app_true_price' => array('type' => 'float', 'null' => true, 'default' => null),
		'app_rep_price' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_ach_billing_options = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'billing_option_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'billing_option_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_aches = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'ach_seq_number' => array('type' => 'integer', 'null' => false),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'date_submitted' => array('type' => 'date', 'null' => true),
		'date_completed' => array('type' => 'date', 'null' => true),
		'reason' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'credit_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'reason_other' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'invoice_number' => array('type' => 'biginteger', 'null' => true),
		'app_status_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'billing_option_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'commission_month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8),
		'tax' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_ach_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_achs_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_acquirers = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'acquirer_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'acquirer' => array('type' => 'string', 'null' => false, 'length' => 21),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_banks = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bank_routing_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_dda_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 60),
		'bank_routing_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'bank_dda_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'fees_routing_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'fees_dda_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'fees_routing_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'fees_dda_number_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_banks_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_bins = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'bin_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bin' => array('type' => 'string', 'null' => false, 'length' => 21),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_cancellation_subreasons = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 65),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_cancellations = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'date_submitted' => array('type' => 'date', 'null' => true),
		'date_completed' => array('type' => 'date', 'null' => true),
		'fee_charged' => array('type' => 'float', 'null' => true, 'default' => null),
		'reason' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'PEND', 'length' => 5),
		'axia_invoice_number' => array('type' => 'biginteger', 'null' => true),
		'date_inactive' => array('type' => 'date', 'null' => true),
		'merchant_cancellation_subreason' => array('type' => 'string', 'null' => true, 'default' => null),
		'subreason_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_cancellation_index1' => array('unique' => false, 'column' => 'date_submitted'),
			'merchant_cancellation_index2' => array('unique' => false, 'column' => 'date_completed'),
			'merchant_cancellation_index3' => array('unique' => false, 'column' => 'status'),
			'merchant_cancellation_index9' => array('unique' => false, 'column' => 'date_inactive'),
			'merchant_cancellation_index99' => array('unique' => false, 'column' => 'subreason_id'),
			'merchant_cancellations_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_card_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'card_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'mct_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_card_types_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_changes = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'change_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'change_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'status' => array('type' => 'string', 'null' => false, 'length' => 5),
		'date_entered' => array('type' => 'date', 'null' => false),
		'time_entered' => array('type' => 'time', 'null' => false),
		'date_approved' => array('type' => 'date', 'null' => true),
		'time_approved' => array('type' => 'time', 'null' => true),
		'change_data' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'merchant_note_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'approved_by_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'programming_id' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_change_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_changes_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_gateways_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'gateway1_pricing_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gateway_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'r_statement_fee' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_per_item' => array('type' => 'float', 'null' => true),
		'm_statement' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'gateway1_pricing_index' => array('unique' => false, 'column' => 'gateway1_pricing_id'),
			'merchant_gateways_archives_gateways_index' => array('unique' => false, 'column' => 'gateway_id'),
			'merchant_gateways_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_gateways_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $merchant_notes = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_note_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'note_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'note_date' => array('type' => 'date', 'null' => true),
		'note' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'note_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200),
		'general_status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'date_changed' => array('type' => 'date', 'null' => true),
		'critical' => array('type' => 'integer', 'null' => true),
		'note_sent' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_note_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_notes_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_owners = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'owner_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'owner_social_sec_no' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner_equity' => array('type' => 'integer', 'null' => true),
		'owner_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'owner_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'owner_social_sec_no_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_owner_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_owners_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_pcis = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'compliance_level' => array('type' => 'integer', 'null' => false, 'default' => '4'),
		'saq_completed_date' => array('type' => 'date', 'null' => true),
		'compliance_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'insurance_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'last_security_scan' => array('type' => 'date', 'null' => true),
		'scanning_company' => array('type' => 'string', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_pcis_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'mp_compliance_fee' => array('unique' => false, 'column' => 'compliance_fee'),
			'mp_insurance_fee' => array('unique' => false, 'column' => 'insurance_fee'),
			'mp_saq_completed_date' => array('unique' => false, 'column' => 'saq_completed_date')
		),
		'tableParameters' => array()
	);

	public $merchant_pcis_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pci_pricing_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'insurance_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_pcis_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_pcis_pricing_index' => array('unique' => false, 'column' => 'merchant_pci_pricing_id')
		),
		'tableParameters' => array()
	);

	public $merchant_pricings = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'gateway_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'mc_vi_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'ds_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'ds_processing_rate' => array('type' => 'float', 'null' => true),
		'processing_rate' => array('type' => 'float', 'null' => true),
		'mc_vi_auth' => array('type' => 'float', 'null' => true),
		'ds_auth_fee' => array('type' => 'float', 'null' => true),
		'billing_mc_vi_auth' => array('type' => 'float', 'null' => true),
		'billing_discover_auth' => array('type' => 'float', 'null' => true),
		'billing_amex_auth' => array('type' => 'float', 'null' => true),
		'billing_debit_auth' => array('type' => 'float', 'null' => true),
		'discount_item_fee' => array('type' => 'float', 'null' => true),
		'amex_auth_fee' => array('type' => 'float', 'null' => true),
		'aru_voice_auth_fee' => array('type' => 'float', 'null' => true),
		'wireless_auth_fee' => array('type' => 'float', 'null' => true),
		'wireless_auth_cost' => array('type' => 'float', 'null' => true),
		'num_wireless_term' => array('type' => 'integer', 'null' => true),
		'per_wireless_term_cost' => array('type' => 'float', 'null' => true),
		'total_wireless_term_cost' => array('type' => 'float', 'null' => true),
		'statement_fee' => array('type' => 'float', 'null' => true),
		'min_month_process_fee' => array('type' => 'float', 'null' => true),
		'ebt_auth_fee' => array('type' => 'float', 'null' => true),
		'ebt_access_fee' => array('type' => 'float', 'null' => true),
		'ebt_processing_rate' => array('type' => 'float', 'null' => true),
		'gateway_access_fee' => array('type' => 'float', 'null' => true),
		'wireless_access_fee' => array('type' => 'float', 'null' => true),
		'debit_auth_fee' => array('type' => 'float', 'null' => true),
		'debit_processing_rate' => array('type' => 'float', 'null' => true),
		'debit_access_fee' => array('type' => 'float', 'null' => true),
		'debit_acquirer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'annual_fee' => array('type' => 'float', 'null' => true),
		'chargeback_fee' => array('type' => 'float', 'null' => true),
		'amex_processing_rate' => array('type' => 'float', 'null' => true),
		'amex_per_item_fee' => array('type' => 'float', 'null' => true),
		'cr_gateway_fees_separate' => array('type' => 'boolean', 'null' => true),
		'cr_gateway_rate_cost' => array('type' => 'float', 'null' => true),
		'cr_gateway_item_cost' => array('type' => 'float', 'null' => true),
		'cr_gateway_monthly_cost' => array('type' => 'float', 'null' => true),
		'features' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'pyc_charge_to_merchant' => array('type' => 'float', 'null' => true),
		'pyc_merchant_rebate' => array('type' => 'float', 'null' => true),
		'syc_charge_to_merchant' => array('type' => 'float', 'null' => true),
		'debit_discount_item_fee' => array('type' => 'float', 'null' => true),
		'ebt_discount_item_fee' => array('type' => 'float', 'null' => true),
		'ebt_acquirer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_reference_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_ref_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_ref_type_desc' => array('type' => 'string', 'null' => false, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_references = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_ref_seq_number' => array('type' => 'integer', 'null' => false),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_ref_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bank_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'person_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_reference_mid' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_references_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchant_reject_lines = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reject_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'status_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'status_date' => array('type' => 'date', 'null' => true),
		'notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'mr_status_date_index1' => array('unique' => false, 'column' => 'status_date'),
			'mrl_reject_id_index1' => array('unique' => false, 'column' => 'reject_id'),
			'mrl_status_id_index1' => array('unique' => false, 'column' => 'status_id')
		),
		'tableParameters' => array()
	);

	public $merchant_reject_recurrances = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 57),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_reject_statuses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 57),
		'collected' => array('type' => 'boolean', 'null' => true),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'mrs_collected_index1' => array('unique' => false, 'column' => 'collected')
		),
		'tableParameters' => array()
	);

	public $merchant_reject_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 57),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_rejects = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'trace' => array('type' => 'string', 'null' => false, 'length' => 30),
		'reject_date' => array('type' => 'date', 'null' => false),
		'type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'code' => array('type' => 'string', 'null' => false, 'length' => 9),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'recurrance_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'open' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'loss_axia' => array('type' => 'float', 'null' => true, 'default' => null),
		'loss_mgr1' => array('type' => 'float', 'null' => true, 'default' => null),
		'loss_mgr2' => array('type' => 'float', 'null' => true, 'default' => null),
		'loss_rep' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'mr_trace_index1' => array('unique' => true, 'column' => 'trace'),
			'merchant_rejects_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'mr_merchant_id_index1' => array('unique' => false, 'column' => 'merchant_id'),
			'mr_open_index1' => array('unique' => false, 'column' => 'open'),
			'mr_recurrance_id_index1' => array('unique' => false, 'column' => 'recurrance_id'),
			'mr_reject_date_index1' => array('unique' => false, 'column' => 'reject_date'),
			'mr_type_id_index1' => array('unique' => false, 'column' => 'type_id')
		),
		'tableParameters' => array()
	);

	public $merchant_uw_final_approveds = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_uw_final_approved_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 21),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_uw_final_statuses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 21),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $merchant_uws = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'tier_assignment' => array('type' => 'integer', 'null' => true),
		'credit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'chargeback_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'final_status_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'final_approved_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'final_date' => array('type' => 'date', 'null' => true),
		'final_notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'mcc' => array('type' => 'string', 'null' => true, 'default' => null),
		'expedited' => array('type' => 'boolean', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchant_uw_expedited_index' => array('unique' => false, 'column' => 'expedited'),
			'merchant_uw_final_approved_id_index' => array('unique' => false, 'column' => 'final_approved_id'),
			'merchant_uw_final_date_index' => array('unique' => false, 'column' => 'final_date'),
			'merchant_uw_final_status_id_index' => array('unique' => false, 'column' => 'final_status_id'),
			'merchant_uw_tier_assignment_index' => array('unique' => false, 'column' => 'tier_assignment'),
			'merchant_uws_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $merchants = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_mid' => array('type' => 'string', 'null' => false, 'length' => 20),
		'merchant_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'merchant_dba' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'merchant_contact' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'merchant_email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'merchant_ownership_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30),
		'merchant_tin' => array('type' => 'string', 'null' => true, 'default' => null),
		'merchant_d_and_b' => array('type' => 'string', 'null' => true, 'default' => null),
		'inactive_date' => array('type' => 'date', 'null' => true),
		'active_date' => array('type' => 'date', 'null' => true),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'network_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_buslevel' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'merchant_sic' => array('type' => 'integer', 'null' => true),
		'entity_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'group_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_bustype' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'merchant_url' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'merchant_tin_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'merchant_d_and_b_disp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'active' => array('type' => 'integer', 'null' => true),
		'cancellation_fee_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_contact_position' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'merchant_mail_contact' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'res_seq_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_bin_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_acquirer_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'reporting_user' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 65),
		'merchant_ps_sold' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'ref_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'ref_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'res_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_p_pct' => array('type' => 'integer', 'null' => true),
		'res_p_pct' => array('type' => 'integer', 'null' => true),
		'app_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'partner_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'partner_exclude_volume' => array('type' => 'boolean', 'null' => true),
		'aggregated' => array('type' => 'boolean', 'null' => true),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'merchants_merchant_id_idx' => array('unique' => true, 'column' => 'merchant_id'),
			'merchant_active' => array('unique' => false, 'column' => 'active'),
			'merchant_dba_idx' => array('unique' => false, 'column' => 'merchant_dba'),
			'merchant_dbalower_idx' => array('unique' => false, 'column' => 'merchant_dba'),
			'merchant_entity' => array('unique' => false, 'column' => 'entity_id'),
			'merchant_groupid' => array('unique' => false, 'column' => 'group_id'),
			'merchant_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'merchant_netid' => array('unique' => false, 'column' => 'network_id'),
			'merchant_userid' => array('unique' => false, 'column' => 'user_id'),
			'mpid_index1' => array('unique' => false, 'column' => 'partner_id')
		),
		'tableParameters' => array()
	);

	public $networks = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'network_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'position' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $note_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'note_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'note_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_api_logs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'user_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'ip_address' => array('type' => 'inet', 'null' => true),
		'request_string' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'request_url' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'request_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true),
		'auth_status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 7),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_apips = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'ip_address' => array('type' => 'inet', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_applications = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'hash' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32),
		'rs_document_guid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'ownership_type' => array('type' => 'string', 'null' => true, 'default' => null),
		'legal_business_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'mailing_address' => array('type' => 'string', 'null' => true, 'default' => null),
		'mailing_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'mailing_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'mailing_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'mailing_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'mailing_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'federal_taxid' => array('type' => 'string', 'null' => true, 'default' => null),
		'corp_contact_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'corp_contact_name_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'corporate_email' => array('type' => 'string', 'null' => true, 'default' => null),
		'loc_same_as_corp' => array('type' => 'boolean', 'null' => true),
		'dba_business_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'location_address' => array('type' => 'string', 'null' => true, 'default' => null),
		'location_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'location_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'location_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'location_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'location_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'customer_svc_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'loc_contact_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'loc_contact_name_title' => array('type' => 'string', 'null' => true, 'default' => null),
		'location_email' => array('type' => 'string', 'null' => true, 'default' => null),
		'website' => array('type' => 'string', 'null' => true, 'default' => null),
		'bus_open_date' => array('type' => 'string', 'null' => true, 'default' => null),
		'length_current_ownership' => array('type' => 'string', 'null' => true, 'default' => null),
		'existing_axia_merchant' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'current_mid_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'general_comments' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'location_type' => array('type' => 'string', 'null' => true, 'default' => null),
		'location_type_other' => array('type' => 'string', 'null' => true, 'default' => null),
		'merchant_status' => array('type' => 'string', 'null' => true, 'default' => null),
		'landlord_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'landlord_phone' => array('type' => 'string', 'null' => true, 'default' => null),
		'business_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'products_services_sold' => array('type' => 'string', 'null' => true, 'default' => null),
		'return_policy' => array('type' => 'string', 'null' => true, 'default' => null),
		'days_until_prod_delivery' => array('type' => 'string', 'null' => true, 'default' => null),
		'monthly_volume' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'average_ticket' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'highest_ticket' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'current_processor' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'card_present_swiped' => array('type' => 'integer', 'null' => true),
		'card_present_imprint' => array('type' => 'integer', 'null' => true),
		'card_not_present_keyed' => array('type' => 'integer', 'null' => true),
		'card_not_present_internet' => array('type' => 'integer', 'null' => true),
		'method_total' => array('type' => 'integer', 'null' => true),
		'direct_to_customer' => array('type' => 'integer', 'null' => true),
		'direct_to_business' => array('type' => 'integer', 'null' => true),
		'direct_to_govt' => array('type' => 'integer', 'null' => true),
		'products_total' => array('type' => 'integer', 'null' => true),
		'high_volume_january' => array('type' => 'boolean', 'null' => true),
		'high_volume_february' => array('type' => 'boolean', 'null' => true),
		'high_volume_march' => array('type' => 'boolean', 'null' => true),
		'high_volume_april' => array('type' => 'boolean', 'null' => true),
		'high_volume_may' => array('type' => 'boolean', 'null' => true),
		'high_volume_june' => array('type' => 'boolean', 'null' => true),
		'high_volume_july' => array('type' => 'boolean', 'null' => true),
		'high_volume_august' => array('type' => 'boolean', 'null' => true),
		'high_volume_september' => array('type' => 'boolean', 'null' => true),
		'high_volume_october' => array('type' => 'boolean', 'null' => true),
		'high_volume_november' => array('type' => 'boolean', 'null' => true),
		'high_volume_december' => array('type' => 'boolean', 'null' => true),
		'moto_storefront_location' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_orders_at_location' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_inventory_housed' => array('type' => 'string', 'null' => true, 'default' => null),
		'moto_outsourced_customer_service' => array('type' => 'boolean', 'null' => true),
		'moto_outsourced_shipment' => array('type' => 'boolean', 'null' => true),
		'moto_outsourced_returns' => array('type' => 'boolean', 'null' => true),
		'moto_outsourced_billing' => array('type' => 'boolean', 'null' => true),
		'moto_sales_methods' => array('type' => 'string', 'null' => true, 'default' => null),
		'moto_billing_monthly' => array('type' => 'boolean', 'null' => true),
		'moto_billing_quarterly' => array('type' => 'boolean', 'null' => true),
		'moto_billing_semiannually' => array('type' => 'boolean', 'null' => true),
		'moto_billing_annually' => array('type' => 'boolean', 'null' => true),
		'moto_policy_full_up_front' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_policy_days_until_delivery' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_policy_partial_up_front' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_policy_partial_with' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_policy_days_until_final' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_policy_after' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'bank_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_contact_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'bank_address' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'bank_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'depository_routing_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'depository_account_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'same_as_depository' => array('type' => 'boolean', 'null' => true),
		'fees_routing_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'fees_account_number' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade1_business_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade1_contact_person' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade1_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'trade1_acct_num' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade1_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade1_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade2_business_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade2_contact_person' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade2_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'trade2_acct_num' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade2_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'trade2_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'currently_accept_amex' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'existing_se_num' => array('type' => 'string', 'null' => true, 'default' => null),
		'want_to_accept_amex' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'want_to_accept_discover' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'term1_quantity' => array('type' => 'integer', 'null' => true),
		'term1_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30),
		'term1_provider' => array('type' => 'string', 'null' => true, 'default' => null),
		'term1_use_autoclose' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'term1_what_time' => array('type' => 'string', 'null' => true, 'default' => null),
		'term1_programming_avs' => array('type' => 'boolean', 'null' => true),
		'term1_programming_server_nums' => array('type' => 'boolean', 'null' => true),
		'term1_programming_tips' => array('type' => 'boolean', 'null' => true),
		'term1_programming_invoice_num' => array('type' => 'boolean', 'null' => true),
		'term1_programming_purchasing_cards' => array('type' => 'boolean', 'null' => true),
		'term1_accept_debit' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'term1_pin_pad_type' => array('type' => 'string', 'null' => true, 'default' => null),
		'term1_pin_pad_qty' => array('type' => 'integer', 'null' => true),
		'term2_quantity' => array('type' => 'integer', 'null' => true),
		'term2_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30),
		'term2_provider' => array('type' => 'string', 'null' => true, 'default' => null),
		'term2_use_autoclose' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'term2_what_time' => array('type' => 'string', 'null' => true, 'default' => null),
		'term2_programming_avs' => array('type' => 'boolean', 'null' => true),
		'term2_programming_server_nums' => array('type' => 'boolean', 'null' => true),
		'term2_programming_tips' => array('type' => 'boolean', 'null' => true),
		'term2_programming_invoice_num' => array('type' => 'boolean', 'null' => true),
		'term2_programming_purchasing_cards' => array('type' => 'boolean', 'null' => true),
		'term2_accept_debit' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'term2_pin_pad_type' => array('type' => 'string', 'null' => true, 'default' => null),
		'term2_pin_pad_qty' => array('type' => 'integer', 'null' => true),
		'owner1_percentage' => array('type' => 'integer', 'null' => true),
		'owner1_fullname' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_title' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_address' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'owner1_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'owner1_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'owner1_email' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_ssn' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner1_dob' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'owner2_percentage' => array('type' => 'integer', 'null' => true),
		'owner2_fullname' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_title' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_address' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_city' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_state' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'owner2_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'owner2_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'owner2_email' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_ssn' => array('type' => 'string', 'null' => true, 'default' => null),
		'owner2_dob' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'referral1_business' => array('type' => 'string', 'null' => true, 'default' => null),
		'referral1_owner_officer' => array('type' => 'string', 'null' => true, 'default' => null),
		'referral1_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'referral2_business' => array('type' => 'string', 'null' => true, 'default' => null),
		'referral2_owner_officer' => array('type' => 'string', 'null' => true, 'default' => null),
		'referral2_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'referral3_business' => array('type' => 'string', 'null' => true, 'default' => null),
		'referral3_owner_officer' => array('type' => 'string', 'null' => true, 'default' => null),
		'referral3_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'rep_contractor_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'fees_rate_discount' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_rate_structure' => array('type' => 'string', 'null' => true, 'default' => null),
		'fees_qualification_exemptions' => array('type' => 'string', 'null' => true, 'default' => null),
		'fees_startup_application' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_auth_transaction' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_monthly_statement' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_misc_annual_file' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_equipment' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_auth_amex' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_monthly_minimum' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_misc_chargeback' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_expedite' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_auth_aru_voice' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_monthly_debit_access' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_reprogramming' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_auth_wireless' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_monthly_ebt' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_training' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_monthly_gateway_access' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_wireless_activation' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_monthly_wireless_access' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_tax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_startup_total' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_pin_debit_auth' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_ebt_discount' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_pin_debit_discount' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fees_ebt_auth' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'rep_discount_paid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_amex_discount_rate' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'rep_business_legitimate' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_photo_included' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_inventory_sufficient' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_goods_delivered' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_bus_open_operating' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_visa_mc_decals_visible' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'rep_mail_tel_activity' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'moto_inventory_owned' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'moto_outsourced_customer_service_field' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'moto_outsourced_shipment_field' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'moto_outsourced_returns_field' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'moto_sales_local' => array('type' => 'boolean', 'null' => true),
		'moto_sales_national' => array('type' => 'boolean', 'null' => true),
		'site_survey_signature' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'api' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'var_status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'install_var_rs_document_guid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32),
		'tickler_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'callback_url' => array('type' => 'string', 'null' => true),
		'guid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'redirect_url' => array('type' => 'string', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_coversheets = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'app_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'status' => array('type' => 'string', 'null' => false, 'length' => 10),
		'setup_existing_merchant' => array('type' => 'boolean', 'null' => true),
		'setup_banking' => array('type' => 'boolean', 'null' => true),
		'setup_statements' => array('type' => 'boolean', 'null' => true),
		'setup_drivers_license' => array('type' => 'boolean', 'null' => true),
		'setup_new_merchant' => array('type' => 'boolean', 'null' => true),
		'setup_business_license' => array('type' => 'boolean', 'null' => true),
		'setup_other' => array('type' => 'boolean', 'null' => true),
		'setup_field_other' => array('type' => 'string', 'null' => true, 'length' => 20),
		'setup_tier_select' => array('type' => 'string', 'null' => true, 'length' => 1),
		'setup_tier3' => array('type' => 'boolean', 'null' => true),
		'setup_tier4' => array('type' => 'boolean', 'null' => true),
		'setup_tier5_financials' => array('type' => 'boolean', 'null' => true),
		'setup_tier5_processing_statements' => array('type' => 'boolean', 'null' => true),
		'setup_tier5_bank_statements' => array('type' => 'boolean', 'null' => true),
		'setup_equipment_terminal' => array('type' => 'boolean', 'null' => true),
		'setup_equipment_gateway' => array('type' => 'boolean', 'null' => true),
		'setup_install' => array('type' => 'string', 'null' => true, 'length' => 10),
		'setup_starterkit' => array('type' => 'string', 'null' => true, 'length' => 10),
		'setup_equipment_payment' => array('type' => 'string', 'null' => true, 'length' => 10),
		'setup_lease_price' => array('type' => 'float', 'null' => true),
		'setup_lease_months' => array('type' => 'float', 'null' => true),
		'setup_debit_volume' => array('type' => 'float', 'null' => true),
		'setup_item_count' => array('type' => 'float', 'null' => true),
		'setup_referrer' => array('type' => 'string', 'null' => true, 'length' => 20),
		'setup_referrer_type' => array('type' => 'string', 'null' => true, 'length' => 2),
		'setup_referrer_pct' => array('type' => 'float', 'null' => true),
		'setup_reseller' => array('type' => 'string', 'null' => true, 'length' => 20),
		'setup_reseller_type' => array('type' => 'string', 'null' => true, 'length' => 2),
		'setup_reseller_pct' => array('type' => 'float', 'null' => true),
		'setup_notes' => array('type' => 'string', 'null' => true),
		'cp_encrypted_sn' => array('type' => 'string', 'null' => true, 'length' => 12),
		'cp_pinpad_ra_attached' => array('type' => 'boolean', 'null' => true),
		'cp_giftcards' => array('type' => 'string', 'null' => true, 'length' => 10),
		'cp_check_guarantee' => array('type' => 'string', 'null' => true, 'length' => 10),
		'cp_check_guarantee_info' => array('type' => 'string', 'null' => true, 'length' => 50),
		'cp_pos' => array('type' => 'string', 'null' => true, 'length' => 10),
		'cp_pos_contact' => array('type' => 'string', 'null' => true, 'length' => 50),
		'micros' => array('type' => 'string', 'null' => true, 'length' => 10),
		'micros_billing' => array('type' => 'string', 'null' => true, 'length' => 10),
		'gateway_option' => array('type' => 'string', 'null' => true, 'length' => 10),
		'gateway_package' => array('type' => 'string', 'null' => true, 'length' => 10),
		'gateway_gold_subpackage' => array('type' => 'string', 'null' => true, 'length' => 10),
		'gateway_epay' => array('type' => 'string', 'null' => true, 'length' => 10),
		'gateway_billing' => array('type' => 'string', 'null' => true, 'length' => 10),
		'moto_online_chd' => array('type' => 'string', 'null' => true, 'length' => 10),
		'moto_developer' => array('type' => 'string', 'null' => true, 'length' => 40),
		'moto_company' => array('type' => 'string', 'null' => true, 'length' => 40),
		'moto_gateway' => array('type' => 'string', 'null' => true, 'length' => 40),
		'moto_contact' => array('type' => 'string', 'null' => true, 'length' => 40),
		'moto_phone' => array('type' => 'string', 'null' => true, 'length' => 40),
		'moto_email' => array('type' => 'string', 'null' => true, 'length' => 40),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_email_timeline_subjects = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'subject' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_email_timelines = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'app_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'date' => array('type' => 'datetime', 'null' => true),
		'subject_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'recipient' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_epayments = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'epayment_id' => array('type' => 'integer', 'null' => true),
		'pin' => array('type' => 'integer', 'null' => false),
		'application_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'app_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'date_boarded' => array('type' => 'datetime', 'null' => false),
		'date_retrieved' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'onlineapp_epayments_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_groups = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_multipasses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16),
		'device_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 14),
		'username' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'pass' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'in_use' => array('type' => 'boolean', 'null' => true),
		'application_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_settings = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false),
		'value' => array('type' => 'string', 'null' => true, 'default' => null),
		'description' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $onlineapp_users = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'email' => array('type' => 'string', 'null' => false),
		'password' => array('type' => 'string', 'null' => false, 'length' => 40),
		'group_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'token_used' => array('type' => 'datetime', 'null' => true),
		'token_uses' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'firstname' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'lastname' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 40),
		'extension' => array('type' => 'integer', 'null' => true),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => true),
		'api_password' => array('type' => 'string', 'null' => true, 'length' => 50),
		'api_enabled' => array('type' => 'boolean', 'null' => true),
		'api' => array('type' => 'boolean', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $orderitem_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'orderitem_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'orderitem_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 55),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $orderitems = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'orderitem_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'order_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'equipment_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'equipment_item_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'quantity' => array('type' => 'integer', 'null' => true),
		'equipment_item_true_price' => array('type' => 'float', 'null' => true, 'default' => null),
		'equipment_item_rep_price' => array('type' => 'float', 'null' => true, 'default' => null),
		'equipment_item_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'hardware_sn' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'hardware_replacement_for' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'warranty' => array('type' => 'integer', 'null' => true),
		'item_merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'item_tax' => array('type' => 'float', 'null' => true, 'default' => null),
		'item_ship_type' => array('type' => 'integer', 'null' => true),
		'item_ship_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'item_commission_month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8),
		'item_ach_seq' => array('type' => 'integer', 'null' => true),
		'item_date_ordered' => array('type' => 'date', 'null' => true),
		'type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'orderitems_eqtype' => array('unique' => false, 'column' => 'equipment_type_id'),
			'orderitems_itemmerchid' => array('unique' => false, 'column' => 'item_merchant_id'),
			'orderitems_order_idx' => array('unique' => false, 'column' => 'order_id'),
			'orderitems_type_id' => array('unique' => false, 'column' => 'type_id')
		),
		'tableParameters' => array()
	);

	public $orderitems_replacements = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'orderitem_replacement_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'orderitem_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'shipping_axia_to_merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'shipping_axia_to_merchant_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'shipping_merchant_to_vendor_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'shipping_merchant_to_vendor_cos' => array('type' => 'float', 'null' => true, 'default' => null),
		'shipping_vendor_to_axia_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'shipping_vendor_to_axia_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'ra_num' => array('type' => 'integer', 'null' => true),
		'tracking_num_old' => array('type' => 'integer', 'null' => true),
		'date_shipped_to_vendor' => array('type' => 'date', 'null' => true),
		'date_arrived_from_vendor' => array('type' => 'date', 'null' => true),
		'amount_billed_to_merchant' => array('type' => 'float', 'null' => true, 'default' => null),
		'tracking_num' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 25),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'orderitems_replacement_oid' => array('unique' => false, 'column' => 'orderitem_id')
		),
		'tableParameters' => array()
	);

	public $orders = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'order_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'status' => array('type' => 'string', 'null' => false, 'length' => 5),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'date_ordered' => array('type' => 'date', 'null' => true),
		'date_paid' => array('type' => 'date', 'null' => true),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'shipping_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'tax' => array('type' => 'float', 'null' => true, 'default' => null),
		'ship_to' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'tracking_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'invoice_number' => array('type' => 'integer', 'null' => true),
		'shipping_type' => array('type' => 'integer', 'null' => true),
		'display_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'ach_seq_number' => array('type' => 'integer', 'null' => true),
		'commission_month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8),
		'vendor_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'add_item_tax' => array('type' => 'integer', 'null' => true),
		'commission_month_nocharge' => array('type' => 'integer', 'null' => true),
		'merchant_ach_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'orders_commissionmonth' => array('unique' => false, 'column' => 'commission_month'),
			'orders_invnum' => array('unique' => false, 'column' => 'invoice_number'),
			'orders_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'orders_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'orders_shiptype' => array('unique' => false, 'column' => 'shipping_type'),
			'orders_status' => array('unique' => false, 'column' => 'status'),
			'orders_user_idx' => array('unique' => false, 'column' => 'user_id'),
			'orders_vendorid' => array('unique' => false, 'column' => 'vendor_id')
		),
		'tableParameters' => array()
	);

	public $partners = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'partner_old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'partner_name' => array('type' => 'string', 'null' => false),
		'active' => array('type' => 'integer', 'null' => false, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'pactive_index1' => array('unique' => false, 'column' => 'active')
		),
		'tableParameters' => array()
	);

	public $pci_billing_histories = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'pci_billing_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'billing_date' => array('type' => 'date', 'null' => true),
		'date_change' => array('type' => 'date', 'null' => false),
		'operation' => array('type' => 'string', 'null' => false, 'length' => 25),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $pci_compliance_date_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $pci_compliance_status_logs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'pci_compliance_date_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'date_complete' => array('type' => 'date', 'null' => true),
		'date_change' => array('type' => 'datetime', 'null' => true),
		'operation' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 25),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $pci_compliances = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'pci_compliance_date_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'date_complete' => array('type' => 'date', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $permission_caches = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'permission_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'permission_name' => array('type' => 'string', 'null' => false, 'length' => 200),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uniq_permission_cache' => array('unique' => true, 'column' => array('user_id', 'permission_id'))
		),
		'tableParameters' => array()
	);

	public $permission_constraints = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'permission_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $permissions = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 200),
		'description' => array('type' => 'string', 'null' => false, 'length' => 200),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uniq_permission' => array('unique' => true, 'column' => 'name')
		),
		'tableParameters' => array()
	);

	public $permissions_roles = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'role_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'permission_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'pk_group_id' => array('unique' => false, 'column' => array('role_id', 'permission_id'))
		),
		'tableParameters' => array()
	);

	public $pricing_matrices = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'matrix_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'matrix_profit_perc' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_new_monthly_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_new_monthly_profit' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_view_conjunction' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'matrix_total_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_total_accounts' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_total_profit' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_draw' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_new_accounts_min' => array('type' => 'float', 'null' => true, 'default' => null),
		'matrix_new_accounts_max' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'pricing_matrix_user_idx' => array('unique' => false, 'column' => 'user_id'),
			'pricing_matrix_usertype' => array('unique' => false, 'column' => 'user_type_id')
		),
		'tableParameters' => array()
	);

	public $products_and_services = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'p_and_s_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'products_and_services_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $products_services_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'products_services_type_old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'products_services_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'products_services_rppp' => array('type' => 'boolean', 'null' => true, 'default' => true),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $referer_products_services_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'ref_seq_number' => array('type' => 'string', 'null' => false, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $referers = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'ref_seq_number' => array('type' => 'string', 'null' => false, 'length' => 36),
		'ref_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ref_ref_perc' => array('type' => 'float', 'null' => true, 'default' => null),
		'active' => array('type' => 'integer', 'null' => true),
		'ref_username' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ref_password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'ref_residuals' => array('type' => 'boolean', 'null' => false),
		'ref_commissions' => array('type' => 'boolean', 'null' => false),
		'is_referer' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'referers_active' => array('unique' => false, 'column' => 'active')
		),
		'tableParameters' => array()
	);

	public $referers_bets = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'referers_bet_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'ref_seq_number' => array('type' => 'string', 'null' => false, 'length' => 36),
		'bet_code' => array('type' => 'string', 'null' => false, 'length' => 15),
		'pct' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'referers_bet_index1' => array('unique' => false, 'column' => 'ref_seq_number'),
			'referers_bet_index2' => array('unique' => false, 'column' => 'bet_code')
		),
		'tableParameters' => array()
	);

	public $rep_cost_structures = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'debit_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gift_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gift_magstripe_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gift_magstripe_loyalty_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gift_chipcard_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gift_chipcard_loyalty_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'gift_chipcard_onerate_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'cg_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_web_based_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_web_based_pi' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_gateway_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_merchant_based' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_file_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_ccd_w' => array('type' => 'float', 'null' => true),
		'ach_eft_ppd_nw' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_ppd_w' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_eft_rck' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_reject_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'ach_secure_gateway_eft' => array('type' => 'float', 'null' => true, 'default' => null),
		'main_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'main_vrt_term_gwy_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_web_based_monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_web_based_gateway_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'te_transaction_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'af_annual_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'multiple' => array('type' => 'float', 'null' => true, 'default' => null),
		'auth_monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'auth_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_rate_rep_cost_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'gw_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_per_item_fee_tsys' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_rate_rep_cost_pct_tsys' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_per_item_fee_tsys_new' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_rate_rep_cost_pct_tsys_new' => array('type' => 'float', 'null' => true, 'default' => null),
		'discover_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_per_item_fee_directconnect' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_rate_rep_cost_pct_directconnect' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_per_item_fee_sagepti' => array('type' => 'float', 'null' => true, 'default' => null),
		'debit_rate_rep_cost_pct_sagepti' => array('type' => 'float', 'null' => true, 'default' => null),
		'tg_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'tg_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'wp_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'wp_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'tg_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'amex_auths_per_item' => array('type' => 'float', 'null' => true),
		'ach_rep_rate_pct_sage' => array('type' => 'float', 'null' => true),
		'ach_rep_per_item_sage' => array('type' => 'float', 'null' => true),
		'ach_rep_monthly_cost_sage' => array('type' => 'float', 'null' => true),
		'ach_rep_rate_pct_check_gateway' => array('type' => 'float', 'null' => true),
		'ach_rep_per_item_check_gateway' => array('type' => 'float', 'null' => true),
		'ach_rep_monthly_cost_check_gateway' => array('type' => 'float', 'null' => true),
		'credit_auths_all' => array('type' => 'float', 'null' => true),
		'credit_auths_dial' => array('type' => 'float', 'null' => true),
		'credit_auths_non_dial' => array('type' => 'float', 'null' => true),
		'credit_auths_ssl' => array('type' => 'float', 'null' => true),
		'credit_auths_ip' => array('type' => 'float', 'null' => true),
		'credit_sales_vol_statement_fee' => array('type' => 'float', 'null' => true),
		'debit_tsys_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_sage_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_sage_pti_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_directconnect_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_tsys_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_sage_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_sage_pti_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_directconnect_monthly_fee' => array('type' => 'float', 'null' => true),
		'debit_auths_all' => array('type' => 'float', 'null' => true),
		'debit_auths_dial' => array('type' => 'float', 'null' => true),
		'debit_auths_non_dial' => array('type' => 'float', 'null' => true),
		'debit_auths_ssl' => array('type' => 'float', 'null' => true),
		'debit_auths_ip' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_per_item_fee_tsys_new' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_rate_rep_cost_pct_tsys_new' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_per_item_fee_tsys' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_rate_rep_cost_pct_tsys' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_per_item_fee' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_rate_rep_cost_pct' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_per_item_fee_sagepti' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_rate_rep_cost_pct_sagepti' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_per_item_fee_directconnect' => array('type' => 'float', 'null' => true),
		'debit_sales_vol_rate_rep_cost_pct_directconnect' => array('type' => 'float', 'null' => true),
		'discover_auths_per_item_fee' => array('type' => 'float', 'null' => true),
		'discover_auths_all' => array('type' => 'float', 'null' => true),
		'discover_auths_dial' => array('type' => 'float', 'null' => true),
		'discover_auths_non_dial' => array('type' => 'float', 'null' => true),
		'discover_auths_ssl' => array('type' => 'float', 'null' => true),
		'discover_auths_ip' => array('type' => 'float', 'null' => true),
		'ebt_rep_rate_pct_tsys' => array('type' => 'float', 'null' => true),
		'ebt_rep_per_item_tsys' => array('type' => 'float', 'null' => true),
		'ebt_rep_monthly_cost_tsys' => array('type' => 'float', 'null' => true),
		'ebt_rep_rate_pct_sage' => array('type' => 'float', 'null' => true),
		'ebt_rep_per_item_sage' => array('type' => 'float', 'null' => true),
		'ebt_rep_monthly_cost_sage' => array('type' => 'float', 'null' => true),
		'ebt_rep_rate_pct_sage_pti' => array('type' => 'float', 'null' => true),
		'ebt_rep_per_item_sage_pti' => array('type' => 'float', 'null' => true),
		'ebt_rep_monthly_cost_sage_pti' => array('type' => 'float', 'null' => true),
		'ebt_rep_rate_pct_directconnect' => array('type' => 'float', 'null' => true),
		'ebt_rep_per_item_directconnect' => array('type' => 'float', 'null' => true),
		'ebt_rep_monthly_cost_directconnect' => array('type' => 'float', 'null' => true),
		'ebt_auths_all' => array('type' => 'float', 'null' => true),
		'ebt_auths_dial' => array('type' => 'float', 'null' => true),
		'ebt_auths_non_dial' => array('type' => 'float', 'null' => true),
		'ebt_auths_ssl' => array('type' => 'float', 'null' => true),
		'ebt_auths_ip' => array('type' => 'float', 'null' => true),
		'ebt_sales_vol_rep_rate_pct' => array('type' => 'float', 'null' => true),
		'ebt_sales_vol_rep_per_item' => array('type' => 'float', 'null' => true),
		'ebt_sales_vol_rep_monthly' => array('type' => 'float', 'null' => true),
		'gift_valutec_statement_fee' => array('type' => 'float', 'null' => true),
		'gift_valutec_item_fee' => array('type' => 'float', 'null' => true),
		'gift_valutec_loyalty_item_fee' => array('type' => 'float', 'null' => true),
		'gift_valutec_onerate_fee' => array('type' => 'float', 'null' => true),
		'pyc_rep_rate_pct' => array('type' => 'float', 'null' => true),
		'syc_rep_rate_pct' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $rep_partner_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'partner_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'mgr1_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'mgr2_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'multiple' => array('type' => 'integer', 'null' => true),
		'mgr1_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'mgr2_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'rpxref_index1' => array('unique' => false, 'column' => 'mgr1_id'),
			'rpxref_index2' => array('unique' => false, 'column' => 'mgr2_id')
		),
		'tableParameters' => array()
	);

	public $rep_product_profit_pcts = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'pct' => array('type' => 'float', 'null' => true),
		'multiple' => array('type' => 'float', 'null' => true),
		'pct_gross' => array('type' => 'float', 'null' => true),
		'do_not_display' => array('type' => 'boolean', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $residual_parameter_types = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
		'order' => array('type' => 'integer', 'null' => false),
		'type' => array('type' => 'integer', 'null' => false),
		'value_type' => array('type' => 'integer', 'null' => false),
		'is_multiple' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $residual_parameters = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'residual_parameter_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'associated_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'type' => array('type' => 'string', 'null' => true, 'default' => null),
		'value' => array('type' => 'integer', 'null' => true),
		'is_multiple' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'tier' => array('type' => 'integer', 'null' => false, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $residual_pricings = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'r_network' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'r_month' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_year' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'refer_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_seq_number' => array('type' => 'integer', 'null' => true),
		'm_hidden_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_eft_secure_flag' => array('type' => 'integer', 'null' => true),
		'm_gc_plan' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8),
		'm_pos_partner_flag' => array('type' => 'integer', 'null' => true),
		'bet_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'pct_volume' => array('type' => 'boolean', 'null' => true),
		'res_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_seq_number' => array('type' => 'integer', 'null' => true),
		'm_micros_ip_flag' => array('type' => 'integer', 'null' => true),
		'm_micros_dialup_flag' => array('type' => 'integer', 'null' => true),
		'm_micros_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_wireless_flag' => array('type' => 'integer', 'null' => true),
		'm_wireless_terminals' => array('type' => 'integer', 'null' => true),
		'r_usaepay_flag' => array('type' => 'integer', 'null' => true),
		'r_epay_retail_flag' => array('type' => 'integer', 'null' => true),
		'r_usaepay_gtwy_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_usaepay_gtwy_add_cost' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_tgate_flag' => array('type' => 'integer', 'null' => true),
		'm_petro_flag' => array('type' => 'integer', 'null' => true),
		'ref_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'ref_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'res_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_risk_assessment' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_p_pct' => array('type' => 'integer', 'null' => true),
		'res_p_pct' => array('type' => 'integer', 'null' => true),
		'r_network_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'residual_pricing_mid' => array('unique' => false, 'column' => 'merchant_id'),
			'residual_pricing_month' => array('unique' => false, 'column' => 'r_month'),
			'residual_pricing_network' => array('unique' => false, 'column' => 'r_network'),
			'residual_pricing_pst' => array('unique' => false, 'column' => 'products_services_type_id'),
			'residual_pricing_userid' => array('unique' => false, 'column' => 'user_id'),
			'residual_pricing_year' => array('unique' => false, 'column' => 'r_year'),
			'residual_pricings_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'residual_pricings_r_year_idx' => array('unique' => false, 'column' => 'r_year'),
			'residual_pricings_ref_seq_number' => array('unique' => false, 'column' => 'ref_seq_number')
		),
		'tableParameters' => array()
	);

	public $residual_product_controls = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'product_service_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'do_no_display' => array('type' => 'boolean', 'null' => true, 'default' => false),
		'tier_product' => array('type' => 'boolean', 'null' => true, 'default' => false),
		'enabled_for_rep' => array('type' => 'boolean', 'null' => true, 'default' => false),
		'rep_params_gross_profit_tsys' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'rep_params_gross_profit_sage' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'rep_params_gross_profit_dc' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'manager1_params_gross_profit_tsys' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'manager1_params_gross_profit_sage' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'manager1_params_gross_profit_dc' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'manager2_params_gross_profit_tsys' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'manager2_params_gross_profit_sage' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'manager2_params_gross_profit_dc' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'override_rep_percentage' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'override_multiple' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'override_manager1' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'override_manager2' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'unique_user_product_service_type' => array('unique' => true, 'column' => array('user_id', 'product_service_type_id')),
			'product_service_type_index' => array('unique' => false, 'column' => 'product_service_type_id'),
			'user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $residual_reports = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'r_network' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'r_month' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_year' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_profit_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_rate_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'm_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'refer_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'refer_profit_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_seq_number' => array('type' => 'integer', 'null' => true),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'm_statement_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_avg_ticket' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_items' => array('type' => 'float', 'null' => true, 'default' => null),
		'r_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'total_profit' => array('type' => 'float', 'null' => true, 'default' => null),
		'manager_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'manager_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'manager_profit_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_profit_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_profit_amount' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_seq_number' => array('type' => 'integer', 'null' => true),
		'manager_id_secondary' => array('type' => 'string', 'null' => true, 'length' => 36),
		'manager_profit_pct_secondary' => array('type' => 'float', 'null' => true, 'default' => null),
		'manager_profit_amount_secondary' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'ref_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'res_p_type' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'res_p_value' => array('type' => 'float', 'null' => true, 'default' => null),
		'ref_p_pct' => array('type' => 'integer', 'null' => true),
		'res_p_pct' => array('type' => 'integer', 'null' => true),
		'partner_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'partner_exclude_volume' => array('type' => 'boolean', 'null' => true),
		'r_network_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'residual_report_merchantid' => array('unique' => false, 'column' => 'merchant_id'),
			'residual_report_month' => array('unique' => false, 'column' => 'r_month'),
			'residual_report_pst' => array('unique' => false, 'column' => 'products_services_type_id'),
			'residual_report_seqnum' => array('unique' => false, 'column' => 'ref_seq_number'),
			'residual_report_status' => array('unique' => false, 'column' => 'status'),
			'residual_report_userid' => array('unique' => false, 'column' => 'user_id'),
			'residual_report_year' => array('unique' => false, 'column' => 'r_year'),
			'residual_reports_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'rr_manager_id' => array('unique' => false, 'column' => 'manager_id'),
			'rr_manager_id_secondary' => array('unique' => false, 'column' => 'manager_id_secondary'),
			'rr_partner_id_index' => array('unique' => false, 'column' => 'partner_id')
		),
		'tableParameters' => array()
	);

	public $residual_time_factors = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'tier1_begin_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier1_end_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier2_begin_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier2_end_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier3_begin_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier3_end_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier4_begin_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'tier4_end_month' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'time_factors_user_index' => array('unique' => true, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $residual_volume_tiers = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'product_service_type_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'tier1_minimum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier1_minimum_gp' => array('type' => 'float', 'null' => true),
		'tier1_maximum_volume' => array('type' => 'float', 'null' => true),
		'tier1_maximum_gp' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier2_minimum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier2_minimum_gp' => array('type' => 'float', 'null' => true),
		'tier2_maximum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier2_maximum_gp' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier3_minimum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier3_minimum_gp' => array('type' => 'float', 'null' => true),
		'tier3_maximum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier3_maximum_gp' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier4_minimum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier4_minimum_gp' => array('type' => 'float', 'null' => true),
		'tier4_maximum_volume' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'tier4_maximum_gp' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'volume_tier_unique_user_product_service_type' => array('unique' => true, 'column' => array('user_id', 'product_service_type_id')),
			'volume_tier_product_service_type_index' => array('unique' => false, 'column' => 'product_service_type_id'),
			'volume_tier_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $roles = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'parent_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 50),
		'lft' => array('type' => 'integer', 'null' => false),
		'rght' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'parent_idx' => array('unique' => false, 'column' => 'parent_id')
		),
		'tableParameters' => array()
	);

	public $sales_goal_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'goal_month' => array('type' => 'integer', 'null' => true),
		'goal_year' => array('type' => 'integer', 'null' => true),
		'goal_accounts' => array('type' => 'integer', 'null' => true),
		'goal_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_profits' => array('type' => 'float', 'null' => true, 'default' => null),
		'actual_accounts' => array('type' => 'float', 'null' => true, 'default' => null),
		'actual_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'actual_profits' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_statements' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_calls' => array('type' => 'float', 'null' => true, 'default' => null),
		'actual_statements' => array('type' => 'float', 'null' => true, 'default' => null),
		'actual_calls' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'sg_archive_user_idx' => array('unique' => true, 'column' => array('user_id', 'goal_month', 'goal_year'))
		),
		'tableParameters' => array()
	);

	public $sales_goals = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'goal_accounts' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_volume' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_profits' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_statements' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_calls' => array('type' => 'float', 'null' => true, 'default' => null),
		'goal_month' => array('type' => 'integer', 'null' => false, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'sales_goal_userid' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $saq_answers = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_merchant_survey_xref_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_survey_question_xref_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'answer' => array('type' => 'boolean', 'null' => false),
		'date' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'sa_saq_merchant_survey_xref_id' => array('unique' => false, 'column' => 'saq_merchant_survey_xref_id'),
			'sa_saq_survey_question_xref_id' => array('unique' => false, 'column' => 'saq_survey_question_xref_id')
		),
		'tableParameters' => array()
	);

	public $saq_control_scan_unboardeds = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'date_unboarded' => array('type' => 'date', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'saq_control_scan_unboardeds_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'scsu_date_unboarded' => array('unique' => false, 'column' => 'date_unboarded')
		),
		'tableParameters' => array()
	);

	public $saq_control_scans = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'first_scan_date' => array('type' => 'date', 'null' => true),
		'first_questionnaire_date' => array('type' => 'date', 'null' => true),
		'scan_status' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'questionnaire_status' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'creation_date' => array('type' => 'date', 'null' => true),
		'dba' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 80),
		'quarterly_scan_fee' => array('type' => 'float', 'null' => false, 'default' => '(11.95)'),
		'sua' => array('type' => 'datetime', 'null' => true),
		'pci_compliance' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 3),
		'offline_compliance' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 3),
		'compliant_date' => array('type' => 'date', 'null' => true),
		'host_count' => array('type' => 'integer', 'null' => true),
		'submids' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'saq_control_scans_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'scs_creation_date' => array('unique' => false, 'column' => 'creation_date'),
			'scs_first_questionnaire_date' => array('unique' => false, 'column' => 'first_questionnaire_date'),
			'scs_first_scan_date' => array('unique' => false, 'column' => 'first_scan_date'),
			'scs_pci_compliance' => array('unique' => false, 'column' => 'pci_compliance'),
			'scs_quarterly_scan_fee' => array('unique' => false, 'column' => 'quarterly_scan_fee'),
			'scs_questionnaire_status' => array('unique' => false, 'column' => 'questionnaire_status'),
			'scs_saq_type' => array('unique' => false, 'column' => 'saq_type'),
			'scs_scan_status' => array('unique' => false, 'column' => 'scan_status'),
			'scs_sua' => array('unique' => false, 'column' => 'sua')
		),
		'tableParameters' => array()
	);

	public $saq_merchant_pci_email_sents = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_merchant_pci_email_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'date_sent' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'smpes_date_sent' => array('unique' => false, 'column' => 'date_sent'),
			'smpes_saq_merchant_id' => array('unique' => false, 'column' => 'saq_merchant_id'),
			'smpes_saq_merchant_pci_email_id' => array('unique' => false, 'column' => 'saq_merchant_pci_email_id')
		),
		'tableParameters' => array()
	);

	public $saq_merchant_pci_emails = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'priority' => array('type' => 'integer', 'null' => false),
		'interval' => array('type' => 'integer', 'null' => false),
		'title' => array('type' => 'string', 'null' => false),
		'filename_prefix' => array('type' => 'string', 'null' => false),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $saq_merchant_survey_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_survey_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_eligibility_survey_old_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'saq_confirmation_survey_old_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'datestart' => array('type' => 'datetime', 'null' => false),
		'datecomplete' => array('type' => 'datetime', 'null' => true),
		'ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15),
		'acknowledgement_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'acknowledgement_title' => array('type' => 'string', 'null' => true, 'default' => null),
		'acknowledgement_company' => array('type' => 'string', 'null' => true, 'default' => null),
		'resolution' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'saq_confirmation_survey_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_eligibility_survey_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'smsx_acknowledgement_name' => array('unique' => false, 'column' => 'acknowledgement_name'),
			'smsx_datecomplete' => array('unique' => false, 'column' => 'datecomplete'),
			'smsx_saq_confirmation_survey_id' => array('unique' => false, 'column' => 'saq_confirmation_survey_old_id'),
			'smsx_saq_eligibility_survey_id' => array('unique' => false, 'column' => 'saq_eligibility_survey_old_id'),
			'smsx_saq_merchant_id' => array('unique' => false, 'column' => 'saq_merchant_id'),
			'smsx_saq_survey_id' => array('unique' => false, 'column' => 'saq_survey_id')
		),
		'tableParameters' => array()
	);

	public $saq_merchants = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_name' => array('type' => 'string', 'null' => false),
		'merchant_email' => array('type' => 'string', 'null' => false, 'length' => 100),
		'password' => array('type' => 'string', 'null' => false),
		'email_sent' => array('type' => 'datetime', 'null' => true),
		'billing_date' => array('type' => 'datetime', 'null' => true),
		'next_billing_date' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'saq_merchants_merchant_id_idx' => array('unique' => true, 'column' => 'merchant_id'),
			'saw_merchant_idx' => array('unique' => true, 'column' => 'merchant_id'),
			'sm_billing_date' => array('unique' => false, 'column' => 'billing_date'),
			'sm_email_sent' => array('unique' => false, 'column' => 'email_sent'),
			'sm_merchant_email' => array('unique' => false, 'column' => 'merchant_email'),
			'sm_merchant_name' => array('unique' => false, 'column' => 'merchant_name'),
			'sm_next_billing_date' => array('unique' => false, 'column' => 'next_billing_date'),
			'sm_password' => array('unique' => false, 'column' => 'password')
		),
		'tableParameters' => array()
	);

	public $saq_prequalifications = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'result' => array('type' => 'string', 'null' => false, 'length' => 4),
		'date_completed' => array('type' => 'datetime', 'null' => false),
		'control_scan_code' => array('type' => 'integer', 'null' => true),
		'control_scan_message' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'sp_saq_merchant_id' => array('unique' => false, 'column' => 'saq_merchant_id')
		),
		'tableParameters' => array()
	);

	public $saq_questions = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'question' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $saq_survey_question_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'saq_survey_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'saq_question_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'priority' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ssqx_saq_question_id' => array('unique' => false, 'column' => 'saq_question_id'),
			'ssqx_saq_survey_id' => array('unique' => false, 'column' => 'saq_survey_id')
		),
		'tableParameters' => array()
	);

	public $saq_surveys = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false),
		'saq_level' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4),
		'eligibility_survey_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'confirmation_survey_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ss_confirmation_survey_id' => array('unique' => false, 'column' => 'confirmation_survey_id'),
			'ss_eligibility_survey_id' => array('unique' => false, 'column' => 'eligibility_survey_id')
		),
		'tableParameters' => array()
	);

	public $schema_migrations = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'class' => array('type' => 'string', 'null' => false),
		'type' => array('type' => 'string', 'null' => false, 'length' => 50),
		'created' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $shipping_type_items = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'shipping_type' => array('type' => 'integer', 'null' => false),
		'shipping_type_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'cost' => array('type' => 'float', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $shipping_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'shipping_type' => array('type' => 'integer', 'null' => false),
		'shipping_type_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $system_transactions = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'system_transaction_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'transaction_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'session_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'client_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'system_transaction_date' => array('type' => 'date', 'null' => true),
		'system_transaction_time' => array('type' => 'time', 'null' => true),
		'merchant_note_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'change_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'ach_seq_number' => array('type' => 'integer', 'null' => true),
		'order_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'programming_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'sys_trans_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'sys_trans_session_idx' => array('unique' => false, 'column' => 'session_id'),
			'system_transaction_chnageid' => array('unique' => false, 'column' => 'change_id'),
			'system_transaction_noteid' => array('unique' => false, 'column' => 'merchant_note_id'),
			'system_transaction_orderid' => array('unique' => false, 'column' => 'order_id'),
			'system_transaction_programmingi' => array('unique' => false, 'column' => 'programming_id'),
			'system_transaction_userid' => array('unique' => false, 'column' => 'user_id'),
			'system_transactions_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $t_and_e_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'tne_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'tne_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $tgates = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'tg_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'tg_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'tg_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'tgates_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $tickler_availabilities = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_availabilities_leads = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'availability_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'lead_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_companies = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_equipments = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_followups = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_leads = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'business_name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'business_address' => array('type' => 'string', 'null' => false, 'length' => 100),
		'city' => array('type' => 'string', 'null' => false, 'length' => 60),
		'state_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'zip' => array('type' => 'string', 'null' => true, 'length' => 12),
		'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'mobile' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'decision_maker' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'decision_maker_title' => array('type' => 'string', 'null' => true, 'length' => 50),
		'other_contact' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'other_contact_title' => array('type' => 'string', 'null' => true, 'length' => 50),
		'email' => array('type' => 'string', 'null' => false, 'length' => 100),
		'website' => array('type' => 'string', 'null' => false, 'length' => 2008),
		'company_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'equipment_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'referer_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'user_id' => array('type' => 'integer', 'null' => true),
		'status_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'likes1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'likes2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'likes3' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'dislikes1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'dislikes2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'dislikes3' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'call_date' => array('type' => 'date', 'null' => true),
		'meeting_date' => array('type' => 'date', 'null' => true),
		'sign_date' => array('type' => 'date', 'null' => true),
		'app_created' => array('type' => 'integer', 'null' => true),
		'nlp_id' => array('type' => 'integer', 'null' => true),
		'volume' => array('type' => 'float', 'null' => true),
		'lat' => array('type' => 'float', 'null' => true),
		'lng' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_loggable_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'action' => array('type' => 'string', 'null' => false, 'length' => 150),
		'model' => array('type' => 'string', 'null' => false, 'length' => 150),
		'foreign_key' => array('type' => 'string', 'null' => false, 'length' => 150),
		'source_id' => array('type' => 'integer', 'null' => false),
		'content' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'created' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_referers = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_resellers = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_schema_migrations = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'class' => array('type' => 'string', 'null' => false),
		'type' => array('type' => 'string', 'null' => false, 'length' => 50),
		'created' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_states = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 2),
		'state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 30),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $tickler_statuses = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'order' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

	public $timeline_entries = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'timeline_item' => array('type' => 'string', 'null' => false, 'length' => 50),
		'timeline_date_completed' => array('type' => 'date', 'null' => true),
		'action_flag' => array('type' => 'boolean', 'null' => true),
		'timeline_item_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'timeline_entries_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'tl_ent_merchant_idx' => array('unique' => false, 'column' => 'merchant_id'),
			'tl_timeline_date_completed' => array('unique' => false, 'column' => 'timeline_date_completed'),
			'tl_timeline_item' => array('unique' => false, 'column' => 'timeline_item')
		),
		'tableParameters' => array()
	);

	public $timeline_items = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'timeline_item' => array('type' => 'string', 'null' => false, 'length' => 50),
		'timeline_item_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $transaction_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'transaction_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'transaction_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $underwritings = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'mo_volume' => array('type' => 'float', 'null' => true),
		'average_ticket' => array('type' => 'float', 'null' => true),
		'max_transaction_amount' => array('type' => 'float', 'null' => true),
		'sales' => array('type' => 'integer', 'null' => true),
		'mc_vi_volume' => array('type' => 'float', 'null' => true),
		'ds_volume' => array('type' => 'float', 'null' => true),
		'pin_debit_volume' => array('type' => 'float', 'null' => true),
		'pin_debit_avg_ticket' => array('type' => 'float', 'null' => true),
		'amex_volume' => array('type' => 'float', 'null' => true),
		'amex_avg_ticket' => array('type' => 'float', 'null' => true),
		'card_present_swiped' => array('type' => 'float', 'null' => true),
		'card_present_imprint' => array('type' => 'float', 'null' => true),
		'card_not_present_keyed' => array('type' => 'float', 'null' => true),
		'card_not_present_internet' => array('type' => 'float', 'null' => true),
		'direct_to_consumer' => array('type' => 'float', 'null' => true),
		'direct_to_business' => array('type' => 'float', 'null' => true),
		'direct_to_government' => array('type' => 'float', 'null' => true),
		'te_amex_number' => array('type' => 'string', 'null' => true),
		'te_diners_club_number' => array('type' => 'string', 'null' => true),
		'te_discover_number' => array('type' => 'string', 'null' => true),
		'te_jcb_number' => array('type' => 'string', 'null' => true),
		'te_amex_number_disp' => array('type' => 'string', 'null' => true, 'length' => 4),
		'te_diners_club_number_disp' => array('type' => 'string', 'null' => true, 'length' => 4),
		'te_discover_number_disp' => array('type' => 'string', 'null' => true, 'length' => 4),
		'te_jcb_number_disp' => array('type' => 'string', 'null' => true, 'length' => 4),
		'ebt_risk_assessment' => array('type' => 'float', 'null' => true),
		'debit_risk_assessment' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $usaepay_rep_gtwy_add_costs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false),
		'cost' => array('type' => 'float', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $usaepay_rep_gtwy_costs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false),
		'cost' => array('type' => 'float', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $user_bet_tables = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'bet_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'bet_code' => array('type' => 'string', 'null' => false, 'length' => 15),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'network' => array('type' => 'string', 'null' => false, 'length' => 20),
		'rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'pi' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_bet_table_pk' => array('unique' => true, 'column' => array('bet_id', 'bet_code', 'user_id', 'network')),
			'ubt_user_idx' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $user_parameter_types = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
		'order' => array('type' => 'integer', 'null' => false),
		'type' => array('type' => 'integer', 'null' => false),
		'value_type' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_parameter_types_name_key' => array('unique' => true, 'column' => 'name')
		),
		'tableParameters' => array()
	);

	public $user_parameters = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'user_parameter_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'merchant_acquirer_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'products_services_type_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'associated_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'type' => array('type' => 'string', 'null' => true, 'default' => null),
		'value' => array('type' => 'integer', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'is_multiple' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $user_residual_options = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 64),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $user_types = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_type' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_type_description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $users = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_type_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'user_first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'user_last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'username' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'password_2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'user_email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'user_phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'user_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'user_admin' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1),
		'parent_user_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'user_commission' => array('type' => 'integer', 'null' => true),
		'inactive_date' => array('type' => 'date', 'null' => true),
		'last_login_date' => array('type' => 'date', 'null' => true),
		'last_login_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 12),
		'entity_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'active' => array('type' => 'integer', 'null' => true),
		'initials' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 5),
		'manager_percentage' => array('type' => 'float', 'null' => true, 'default' => null),
		'date_started' => array('type' => 'date', 'null' => true),
		'split_commissions' => array('type' => 'boolean', 'null' => false),
		'bet_extra_pct' => array('type' => 'boolean', 'null' => false),
		'secondary_parent_user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'manager_percentage_secondary' => array('type' => 'float', 'null' => true, 'default' => null),
		'discover_bet_extra_pct' => array('type' => 'boolean', 'null' => false),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'user_residual_option_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36),
		'is_blocked' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_email_idx' => array('unique' => false, 'column' => 'user_email'),
			'user_lastname_idx' => array('unique' => false, 'column' => 'user_last_name'),
			'user_parent_user_idx' => array('unique' => false, 'column' => 'parent_user_id'),
			'user_secondary_parent_user_idx' => array('unique' => false, 'column' => 'secondary_parent_user_id'),
			'user_username_idx' => array('unique' => false, 'column' => 'username')
		),
		'tableParameters' => array()
	);

	public $users_roles = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'role_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'pk_roles_users' => array('unique' => false, 'column' => array('role_id', 'user_id'))
		),
		'tableParameters' => array()
	);

	public $uw_approvalinfo_merchant_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'approvalinfo_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'verified_option_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_amx_verified_option_id_index' => array('unique' => false, 'column' => 'verified_option_id'),
			'uw_approvalinfo_merchant_xrefs_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $uw_approvalinfos = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 99),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'verified_type' => array('type' => 'text', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_approvalinfo_priority_index' => array('unique' => false, 'column' => 'priority'),
			'uw_approvalinfo_verified_type_index' => array('unique' => false, 'column' => 'verified_type')
		),
		'tableParameters' => array()
	);

	public $uw_infodoc_merchant_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'infodoc_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'received_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_imx_received_id_index' => array('unique' => false, 'column' => 'received_id'),
			'uw_infodoc_merchant_xrefs_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $uw_infodocs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 99),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'required' => array('type' => 'boolean', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_infodoc_priority_index' => array('unique' => false, 'column' => 'priority'),
			'uw_infodoc_required_index' => array('unique' => false, 'column' => 'required')
		),
		'tableParameters' => array()
	);

	public $uw_receiveds = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 11),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $uw_status_merchant_xrefs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'status_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'datetime' => array('type' => 'datetime', 'null' => false),
		'notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_smx_datetime_index' => array('unique' => false, 'column' => 'datetime'),
			'uw_status_merchant_xrefs_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $uw_statuses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 99),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_status_priority_index' => array('unique' => false, 'column' => 'priority')
		),
		'tableParameters' => array()
	);

	public $uw_verified_options = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'old_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'name' => array('type' => 'string', 'null' => false, 'length' => 37),
		'verified_type' => array('type' => 'text', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'uw_verified_option_verified_type_index' => array('unique' => false, 'column' => 'verified_type')
		),
		'tableParameters' => array()
	);

	public $vendors = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'vendor_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'vendor_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'rank' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'vendor_rank' => array('unique' => false, 'column' => 'rank')
		),
		'tableParameters' => array()
	);

	public $virtual_check_webs = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'vcweb_mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'vcweb_web_based_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'vcweb_web_based_pi' => array('type' => 'float', 'null' => true, 'default' => null),
		'vcweb_monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'vcweb_gateway_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'virtual_check_webs_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $virtual_checks = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'vc_mid' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'vc_web_based_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_web_based_pi' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_monthly_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'vc_gateway_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'virtual_check_mid' => array('unique' => false, 'column' => 'vc_mid'),
			'virtual_checks_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

	public $visa_bets = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'bet_code' => array('type' => 'string', 'null' => false, 'length' => 15),
		'bet_processing_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_processing_rate_2' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_processing_rate_3' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_per_item_fee' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_per_item_fee_2' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_per_item_fee_3' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_business_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_business_rate_2' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_per_item_fee_bus' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_per_item_fee_bus_2' => array('type' => 'float', 'null' => true, 'default' => null),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $visa_mc_archives = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'merchant_pricing_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'network_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'month' => array('type' => 'integer', 'null' => false),
		'year' => array('type' => 'integer', 'null' => false),
		'referer_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'reseller_id' => array('type' => 'string', 'null' => true, 'length' => 36),
		'ref_p_type' => array('type' => 'string', 'null' => true),
		'ref_p_value' => array('type' => 'float', 'null' => true),
		'res_p_type' => array('type' => 'string', 'null' => true),
		'res_p_value' => array('type' => 'float', 'null' => true),
		'ref_p_pct' => array('type' => 'float', 'null' => true),
		'res_p_pct' => array('type' => 'float', 'null' => true),
		'r_rate_pct' => array('type' => 'float', 'null' => true),
		'r_per_item_fee' => array('type' => 'float', 'null' => true),
		'm_rate_pct' => array('type' => 'float', 'null' => true),
		'm_per_item_fee' => array('type' => 'float', 'null' => true),
		'billing_mc_vi_auth' => array('type' => 'float', 'null' => true),
		'r_cs_statement_fee' => array('type' => 'float', 'null' => true),
		'm_statement_fee' => array('type' => 'float', 'null' => true),
		'm_hidden_per_item_fee_cross' => array('type' => 'float', 'null' => true),
		'mc_vi_bet' => array('type' => 'string', 'null' => true, 'length' => 15),
		'bet_extra_pct' => array('type' => 'float', 'null' => true),
		'mc_vi_ds_risk_assessment' => array('type' => 'float', 'null' => true),
		'm_wireless_terminals' => array('type' => 'integer', 'null' => true),
		'refer_profit_pct' => array('type' => 'float', 'null' => true),
		'res_profit_pct' => array('type' => 'float', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'visa_mc_archives_merchant_pricing_index' => array('unique' => false, 'column' => 'merchant_pricing_id'),
			'visa_mc_archives_merchants_index' => array('unique' => false, 'column' => 'merchant_id'),
			'visa_mc_archives_networks_index' => array('unique' => false, 'column' => 'network_id'),
			'visa_mc_archives_referer_index' => array('unique' => false, 'column' => 'referer_id'),
			'visa_mc_archives_user_index' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $warranties = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'warranty' => array('type' => 'integer', 'null' => false),
		'warranty_description' => array('type' => 'string', 'null' => false, 'length' => 50),
		'cost' => array('type' => 'float', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $webpasses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => 'uuid_generate_v4()', 'length' => 36, 'key' => 'primary'),
		'merchant_id' => array('type' => 'string', 'null' => false, 'length' => 36),
		'wp_rate' => array('type' => 'float', 'null' => true, 'default' => null),
		'wp_per_item' => array('type' => 'float', 'null' => true, 'default' => null),
		'wp_statement' => array('type' => 'float', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'webpasses_merchant_id_idx' => array('unique' => false, 'column' => 'merchant_id')
		),
		'tableParameters' => array()
	);

}
