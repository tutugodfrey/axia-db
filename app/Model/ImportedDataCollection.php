<?php

App::uses('AppModel', 'Model');
App::uses('SalesForce', 'Model');
App::uses('AddressType', 'Model');
App::uses('TimelineItem', 'Model');

class ImportedDataCollection extends AppModel {
/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = [
		'Search.Searchable',
		'SearchByMonthYear' => [
			'yearFieldName' => 'year',
			'monthFieldName' => 'month',
		],
		'UploadedFile'
	];

/**
 * Custom Find Methods
 *
 * @var array
 */
	public $findMethods = [
		'collection' => true,
	];

/**
 * Filters
 *
 * @var array
 */
	public $filterArgs = [
		'dba_mid' => [
			'type' => 'query',
			'method' => 'orConditions',
		],
		'from_date' => [
			'type' => 'query',
			'method' => 'dateStartConditions',
			'empty' => true,
		],
		'end_date' => [
			'type' => 'query',
			'method' => 'dateEndConditions',
			'empty' => true,
		],
		'partners' => [
			'type' => 'value',
			'field' => '"Merchant"."partner_id"'
		],
		'organization_id' => [
			'type' => 'value',
			'field' => '"Merchant"."organization_id"',
		],
		'region_id' => [
			'type' => 'value',
			'field' => '"Merchant"."region_id"',
		],
		'subregion_id' => [
			'type' => 'value',
			'field' => '"Merchant"."subregion_id"',
		],		
		'location_description' => [
			'type' => 'subquery',
			'method' => 'searchByLocation',
			'field' => '"Merchant"."id"'
		],
	];

/**
 * Denotes whether uploaded CSV data should be assumed to be processed as an asynchronous job
 * Most jobs will be handled asynchrounously by a background job unless uploaded data is small
 * @var boolean default true
 */
	private $asycJob = true;

/**
 * The maximum number of rows allowed to synchronously process an uploaded CSV file
 * @var const integer
 */
	const SYNC_JOB_MAX_ROWS = 1000;

/**
 * $fieldsMeta 
 * Report fields meta information
 * 
 * @var array
 */
	public $_fieldsMeta = [
		'merchant_mid' => [
			'text' => 'MID',
			'sortField' => 'Merchant.merchant_mid',
			'model_alias' => 'Merchant',
			'virtual_field' => null,
			'sort_options' => [
				'direction' => 'desc'
			]
		],
		'merchant_dba' => [
			'text' => 'DBA',
			'sortField' => 'Merchant.merchant_dba',
			'model_alias' => 'Merchant',
			'virtual_field' => null,
		],
		'client_id_global' => [
			'text' => 'Client ID',
			'sortField' => 'Client.client_id_global',
			'model_alias' => 'Client',
			'virtual_field' => null,
		],
		'month' => [
			'text' => 'Month',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'year' => [
			'text' => 'Year',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'PartnerName' => [
			'text' => 'Partner',
			'sortField' => null,
			'model_alias' => 'Partner',
			'virtual_field' => 'trim(BOTH FROM "Partner"."user_first_name" || \' \' || "Partner"."user_last_name") AS "Partner__PartnerName"',
		],
		'RepName' => [
			'text' => 'Rep',
			'sortField' => null,
			'model_alias' => 'Rep',
			'virtual_field' => 'trim(BOTH FROM "Rep"."user_first_name" || \' \' || "Rep"."user_last_name") AS "Rep__RepName"',
		],
		'OrgName' => [
			'text' => 'Organization',
			'sortField' => null,
			'model_alias' => 'Organization',
			'virtual_field' => '("Organization"."name") AS "Organization__OrgName"',
		],
		'address_state' => [
			'text' => 'State',
			'sortField' => null,
			'model_alias' => 'AddressBus',
		],
		'RegionName' => [
			'text' => 'Region',
			'sortField' => null,
			'model_alias' => 'Region',
			'virtual_field' => '("Region"."name") AS "Region__RegionName"',
		],
		'datetime' => [
			'text' => 'Created Date',
			'sortField' => null,
			'model_alias' => 'UwStatusApproved',
		],
		'timeline_date_completed' => [
			'text' => 'Go-Live',
			'sortField' => null,
			'model_alias' => 'GoLiveTimeEntry',
		],
		'date_completed' => [
			'text' => 'Cancellation Date',
			'sortField' => null,
			'model_alias' => 'MerchantCancellation',
		],
		'cancelled' => [
			'text' => 'Cancelled?',
			'sortField' => null,
			'model_alias' => 'MerchantCancellation',
			'virtual_field' => '((CASE WHEN "MerchantCancellation"."date_completed" IS NOT NULL then \'YES\' ELSE \'NO\' END)) AS "MerchantCancellation__cancelled"',
		],
		'r_profit_amount' => [
			'text' => 'PF Rep Residual',
			'sortField' => null,
			'model_alias' => 'ResidualReport',
			'virtual_field' => '((TRUNC("ResidualReport"."r_profit_amount" + "ResidualReport"."partner_rep_profit_amount", 4))) AS "ResidualReport__r_profit_amount"',
		],
		'manager_profit_amount' => [
			'text' => 'PF Mgr Residual',
			'sortField' => null,
			'model_alias' => 'ResidualReport',
			'virtual_field' => '((TRUNC("ResidualReport"."manager_profit_amount", 4))) AS "ResidualReport__manager_profit_amount"',
		],
		'manager_profit_amount_secondary' => [
			'text' => 'PF Mgr 2 Residual',
			'sortField' => null,
			'model_alias' => 'ResidualReport',
			'virtual_field' => '((TRUNC("ResidualReport"."manager_profit_amount_secondary", 4))) AS "ResidualReport__manager_profit_amount_secondary"',
		],
		'merchant_sic' => [
			'text' => 'MCC',
			'sortField' => null,
			'model_alias' => 'Merchant',
		],
		'merchant_bustype' => [
			'text' => 'Business Type',
			'sortField' => null,
			'model_alias' => 'Merchant',
		],
		'gw1_name' => [
			'text' => 'Gateway 1',
			'sortField' => null,
			'model_alias' => 'FirstGateway',
			'virtual_field' => '("FirstGateway"."name") AS "FirstGateway__gw1_name"',
		],
		'gw_n1_id_seq' => [
			'text' => 'Gateway 1 ID',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gw_n1_item_count' => [
			'text' => 'Gateway 1 Item Count',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gw_n1_vol' => [
			'text' => 'Gateway 1 Volume',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gw2_name' => [
			'text' => 'Gateway 2',
			'sortField' => null,
			'model_alias' => 'SecondGateway',
			'virtual_field' => '("SecondGateway"."name") AS "SecondGateway__gw2_name"',
		],
		'gw_n2_id_seq' => [
			'text' => 'Gateway 2 ID',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gw_n2_item_count' => [
			'text' => 'Gateway 2 Item Count',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gw_n2_vol' => [
			'text' => 'Gateway 2 Volume',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_total_gw_item_count' => [
			'text' => 'Total PF Item Count',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_total_gw_vol' => [
			'text' => 'Total PF Volume',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'oppty_id' => [
			'text' => 'Opportunity ID',
			'sortField' => null,
			'model_alias' => 'SfOpportinityId',
			'virtual_field' => '("SfOpportinityId"."value") AS "SfOpportinityId__oppty_id"',
		],
		'devices_billed_count' => [
			'text' => 'Devices Billed For',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_recurring_rev' => [
			'text' => 'PF Recurring Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_recurring_item_rev' => [
			'text' => 'PF Recurring Item Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_recurring_device_lic_rev' => [
			'text' => 'PF Recurring Device Licence Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_recurring_gw_rev' => [
			'text' => 'PF Recurring Gateway Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_recurring_acct_rev' => [
			'text' => 'PF Recurring Account Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_one_time_rev' => [
			'text' => 'PF One Time Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'acquiring_one_time_rev' => [
			'text' => 'Acquiring One Time Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_one_time_cost' => [
			'text' => 'PF One Time Cost',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'acquiring_one_time_cost' => [
			'text' => 'Acquiring One Time Cost',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'ach_recurring_rev' => [
			'text' => 'ACH Recurring Rev',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'ach_recurring_gp' => [
			'text' => 'ACH Recurring GP',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_rev_share' => [
			'text' => 'PF Revenue Share',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'is_usa_epay' => [
			'text' => 'USAePay',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
			'virtual_field' => '((CASE WHEN "ImportedDataCollection"."is_usa_epay" = true then \'YES\' WHEN "ImportedDataCollection"."is_usa_epay" = false then \'NO\' ELSE NULL END)) AS "ImportedDataCollection__is_usa_epay"',
		],
		'is_pf_gw' => [
			'text' => 'PF Gateway',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
			'virtual_field' => '((CASE WHEN "ImportedDataCollection"."is_pf_gw" = true then \'YES\' WHEN "ImportedDataCollection"."is_pf_gw" = false THEN \'NO\' ELSE NULL END)) AS "ImportedDataCollection__is_pf_gw"',
		],
		'is_hw_as_srvc' => [
			'text' => 'HaaS/Non HaaS',
			'sortField' => null,
			'model_alias' => 'PaymentFusion',
			'virtual_field' => '((CASE WHEN "PaymentFusion"."is_hw_as_srvc" = true then \'HaaS\' ELSE \'Non HaaS\' END)) AS "PaymentFusion__is_hw_as_srvc"',
		],
		'source_of_sale' => [
			'text' => 'Reseller/Direct',
			'sortField' => null,
			'model_alias' => 'Merchant',
		],
		'is_acquiring_only' => [
			'text' => 'Acquiring Only',
			'sortField' => null,
			'model_alias' => 'Merchant',
			'virtual_field' => '((CASE WHEN "Merchant"."is_acquiring_only" = true then \'YES\' WHEN "Merchant"."is_acquiring_only" = false THEN \'NO\' ELSE NULL END)) AS "Merchant__is_acquiring_only"',
		],
		'is_pf_only' => [
			'text' => 'PF Only',
			'sortField' => null,
			'model_alias' => 'Merchant',
			'virtual_field' => '((CASE WHEN "Merchant"."is_pf_only" = true then \'YES\' WHEN "Merchant"."is_pf_only" = false then \'NO\' ELSE NULL END)) AS "Merchant__is_pf_only"',
		],
		'general_practice_type' => [
			'text' => 'General Practice Type Category',
			'sortField' => null,
			'model_alias' => 'Merchant',
		],
		'specific_practice_type' => [
			'text' => 'Specific Practice Type',
			'sortField' => null,
			'model_alias' => 'Merchant',
		],
		'oppty_name' => [
			'text' => 'Opportunity Name',
			'sortField' => null,
			'model_alias' => 'SfOpportinityName',
			'virtual_field' => '("SfOpportinityName"."value") AS "SfOpportinityName__oppty_name"',
		],
		'sf_closed_date' => [
			'text' => 'Opportunity Close Date',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_opportunity_won' => [
			'text' => 'Opportunity Won/Lost?',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
			'virtual_field' => '((CASE WHEN "ImportedDataCollection"."sf_opportunity_won" = true THEN \'WON\' WHEN "ImportedDataCollection"."sf_opportunity_won" = false THEN \'LOST\' ELSE NULL END)) AS "ImportedDataCollection__sf_opportunity_won"',
		],
		'sf_projected_accounts' => [
			'text' => 'Projected Accounts',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_devices' => [
			'text' => 'Projected Devices',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_acq_tans' => [
			'text' => 'Projected Acquiring Transactions',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_acq_vol' => [
			'text' => 'Projected Acquiring Volume',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_pf_trans' => [
			'text' => 'Projected PF Transactions',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_pf_revenue' => [
			'text' => 'PF Projected Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_pf_recurring_ach_revenue' => [
			'text' => 'PF Projected Recurring ACH Revenue',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_projected_pf_recurring_ach_gp' => [
			'text' => 'PF Projected Recurring ACH GP',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sf_support_cases_count' => [
			'text' => 'Tech Support Cases',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'mo_gateway_cost' => [
			'text' => 'Monthly Gateway Costs',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'profit_loss_amount' => [
			'text' => 'Profit/Loss',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'net_sales_count' => [
			'text' => 'Number of Net Sales',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'net_sales' => [
			'text' => 'Amount of Net Sales',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gross_sales_count' => [
			'text' => '# of Gross Sales',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gross_sales' => [
			'text' => 'Amount of Gross Sales',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'discount' => [
			'text' => 'Discount',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'interchng_income' => [
			'text' => 'InterchangeIncome',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'interchng_expense' => [
			'text' => 'InterchangeExpense',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'other_income' => [
			'text' => 'Other Income',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'other_expense' => [
			'text' => 'Other Expense',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'total_income' => [
			'text' => 'Total Income',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'total_expense' => [
			'text' => 'Total Expense',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'gross_profit' => [
			'text' => 'GP',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_per_item_fee' => [
			'text' => 'PF P/I Fee',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_item_fee_total' => [
			'text' => 'PF Item Fee Total',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_monthly_fees' => [
			'text' => 'Monthly PF Fees',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'revised_gp' => [
			'text' => 'Revised GP',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'total_income_minus_pf' => [
			'text' => 'Total Income minus PF',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'card_brand_expenses' => [
			'text' => 'Card Brand',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'processor_expenses' => [
			'text' => 'Processor',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'sponsor_cogs' => [
			'text' => 'Sponsor COGS',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'ithree_monthly_cogs' => [
			'text' => 'i3 Monthly COGS',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		],
		'pf_actual_mo_vol' => [
			'text' => 'Actual PF Mo Vol',
			'sortField' => null,
			'model_alias' => 'ImportedDataCollection',
		]
	];

/**
 * Map of CSV to DB fields
 *
 * @var array
 */
	public $csvFieldMap = [
		'Merchant' => [
			'MID' => 'id',
			'Business Type' => 'merchant_bustype',
			'Reseller/Direct' => 'source_of_sale',
			'Acquiring Only' => 'is_acquiring_only',
			'PF Only' => 'is_pf_only',
			'General Practice Type Category' => 'general_practice_type',
			'Specific Practice type' => 'specific_practice_type',
		],
		'ImportedDataCollection' => [
			'Gateway 1' => 'gw_n1_id',
			'Gateway 1 ID' => 'gw_n1_id_seq',
			'Gateway 1 Item Count' => 'gw_n1_item_count',
			'Gateway 1 Volume' => 'gw_n1_vol',
			'Gateway 2' => 'gw_n2_id',
			'Gateway 2 ID' => 'gw_n2_id_seq',
			'Gateway 2 Item Count' => 'gw_n2_item_count',
			'Gateway 2 Volume' => 'gw_n2_vol',
			'Total PF Item Count' => 'pf_total_gw_item_count',
			'Total PF Volume' => 'pf_total_gw_vol',
			'Devices Billed For' => 'devices_billed_count',
			'PF Recurring Revenue' => 'pf_recurring_rev',
			'PF Recurring Item Revenue' => 'pf_recurring_item_rev',
			'PF Recurring Device License Revenue' => 'pf_recurring_device_lic_rev',
			'PF Recurring Gateway Revenue' => 'pf_recurring_gw_rev',
			'PF Recurring Account Revenue' => 'pf_recurring_acct_rev',
			'PF One Time Revenue' => 'pf_one_time_rev',
			'PF One Time Cost' => 'pf_one_time_cost',
			'PF Revenue Share' => 'pf_rev_share',
			'Acquiring One Time Revenue' => 'acquiring_one_time_rev',
			'Acquiring One time Cost' => 'acquiring_one_time_cost',
			'ACH Recurring Rev' => 'ach_recurring_rev',
			'ACH Recurring GP' => 'ach_recurring_gp',
			'USAePay' => 'is_usa_epay',
			'PF Gateway' => 'is_pf_gw',
			'Closed/Won Date' => 'sf_closed_date', //special case field: excluded from file if 'Closed/Lost Date' is present
			'Closed/Lost Date' => 'sf_closed_date', //special case field: excluded from file if 'Closed/Won Date' is present
			'Projected Accounts' => 'sf_projected_accounts',
			'Projected Devices' => 'sf_projected_devices',
			'Projected Acquiring Transactions' => 'sf_projected_acq_tans',
			'Projected Acquiring Volume' => 'sf_projected_acq_vol',
			'Projected PF Transactions' => 'sf_projected_pf_trans',
			'PF Projected Revenue' => 'sf_projected_pf_revenue',
			'PF Projected Recurring ACH Revenue' => 'sf_projected_pf_recurring_ach_revenue',
			'PF Projected Recurring ACH GP' => 'sf_projected_pf_recurring_ach_gp',
			'Tech Support Cases' => 'sf_support_cases_count',
			'Monthly Gateway Costs' => 'mo_gateway_cost',
			'Profit/Loss' => 'profit_loss_amount',
			'Number of Net Sales' => 'net_sales_count',
			'Amount of Net Sales' => 'net_sales',
			'# of Gross Sales' => 'gross_sales_count',
			'Amount of Gross Sales' => 'gross_sales',
			'Discount' => 'discount',
			'InterchangeIncome' => 'interchng_income',
			'InterchangeExpense' => 'interchng_expense',
			'Other Income' => 'other_income',
			'Other Expense' => 'other_expense',
			'Total Income' => 'total_income',
			'Total Exp.' => 'total_expense',
			'GP' => 'gross_profit',
			'PF P/I Fee' => 'pf_per_item_fee',
			'PF Item Fee Total' => 'pf_item_fee_total',
			'Monthly PF Fees' => 'pf_monthly_fees',
			'Monthly Volume' => 'pf_actual_mo_vol',
			'Revised GP' => 'revised_gp',
			'Total Income minus PF' => 'total_income_minus_pf',
			'Card Brand' => 'card_brand_expenses',
			'Processor' => 'processor_expenses',
			'Sponsor COGS' => 'sponsor_cogs',
			'i3 Monthly COGS' => 'ithree_monthly_cogs'
		],
		'ExternalRecordField' => [
			SalesForce::OPPTY_ID => 'value',
			SalesForce::OPPTY_NAME => 'value'
		],
		'PaymentFusion' => [
			'HaaS/Non HaaS' => 'is_hw_as_srvc'
		],
	];

	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id'
		)
	);

/**
 * orConditions for merchant dba/mid search
 *
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function orConditions($data = []) {
		$data['search'] = $data['dba_mid'];
		return $this->Merchant->orConditions($data);
	}

/**
 * Search conditions by location_description
 *
 * @param array $data
 * @return array
 */
	public function searchByLocation($data) {
		$query = $this->Merchant->searchByLocation($data);
		return $query;
	}

/**
 * delegateUpsertJob
 * Delegates the processing of the uploaded CSV data as synchronously or asynchronously.
 * Most jobs will be handled asynchrounously by a background job unless rows in CSV file < SYNC_JOB_MAX_ROWS
 * The expected contents of the associative $params array parameter are:
 * $params = [
 * 		"month" => <[month number 1-12]>
 * 		"year" => <yyyy>
 * 		"file_path" => <full path to CSV file>
 * ]
 * 
 * @param  array  $params parameters required to process data
 * @param  boolean $isAsyncJob Whether this method was called synchronously or asynchronously default true
 * @return array only when errors ocurr during synchronous processing
 */
	public function delegateUpsertJob(array $params, $isAsyncJob = true) {
		if ($isAsyncJob) {
			$params['is_queued_job'] = true;
			$params['user_email'] = CakeSession::read('Auth.User.user_email');
			CakeResque::enqueue(
				'genericAxiaDbQueue',
				'BackgroundJobShell',
				['importedDataCollectionUpsert', $params]
			);
		} else {
			$this->asycJob = false;
			return $this->upsertUpload($params);
		}
	}

/**
 * upsertUpload
 * Upserts data from an uploaded CSV file containing the expected columns defined in $this->csvFieldMap
 * 
 * The expected contents of the associative $params array parameter are:
 * $params = [
 * 		"month" => <[month number 1-12]>
 * 		"year" => <yyyy>
 * 		"file_path" => <full path to CSV file>
 * 		"is_queued_job" => (optional) true if this method was queued and called from a BackGroundJobShell
 * 		"user_email" => required if executing from a BackGroundJobShell to notify user who initiated the upsert when it is completed.
 * ]
 * 
 * @param  array  $params parameters required to process data
 * @param  boolean $isAsyncJob Whether this method was called synchronously or asynchronously default true
 * @return array $response
 */
	public function upsertUpload(array $params) {
		$response = [
			'job_name' => 'Data Collection CSV Upload',
			'result' => true,
			'start_time' => date('Y-m-d H:i:s'), // date time format yyyy-mm-dd hh::mm:ss
			'end_time' => null, // date time format yyyy-mm-dd hh::mm:ss
			'recordsAdded' => false,
			'log' => [
				'products' => [], //single dimentional array of products selected for archive
				'errors' => [], //single dimentional indexed array errors
				'optional_msgs' => [], //single dimentional indexed array of additional optional messages
			]
		];
		try {
			$inputData = $this->extractCsvToArray($params['file_path'], true, false);
		} catch (Exception $e) {
			$response['result'] = false;
			$response['log']['errors'][] = $e->getMessage();
			$response['log']['optional_msgs'][] = 'An unexpected error has ocurred! Sorry data could not be uploaded, try again later.';
			return $response;
		}
		//Get field map using csv headers and removing them from data at the same time
		$fieldMap = $this->getFieldMap(array_shift($inputData));
		//The length and order of the $inputData and the $fieldMap the same we can therefore locate and 
		//memoize the index position of this file's MID column to access it directly later
		$midIdxPos = 0;
		foreach ($fieldMap as $header => $fieldName) {
			if (strtolower($header) == 'mid') {
				//found it!
				break;
			}
			$midIdxPos += 1 ;
		}

		foreach ($inputData as $data) {
			//In some cases the CSV file may contain blank rows at the very bottom,
			//checking for athis will skip empty rows
			$mid = $this->sanitizeNumStr($data[$midIdxPos]);
			if (!empty($mid) && $this->Merchant->hasAny(['merchant_mid' => $mid])) {
				$dataToSave = $this->getAssociatedModelData($mid, $data, $fieldMap, $params['month'], $params['year'], $response);
				$saveSuccess = false;
				$exceptionMsg = '';
				try {
				//Save current associated data
					$saveSuccess = $this->Merchant->saveAll($dataToSave, ['validate' => false]);
				} catch (Exception $e) {
					$exceptionMsg = $e->getMessage();
				}
				if (!$saveSuccess) {
					$response['log']['errors'][] = "Failed to save data for MID $mid, unexpected error ocurred. $exceptionMsg";
				} elseif ($response['recordsAdded'] == false) {
					$response['recordsAdded'] = true;
				}
			} else {
				$errStr = (empty($mid))? 'Blank MID value detected, row skipped.' : "Merchant $mid not found in DB and was skipped from upload.";
				$response['log']['errors'][] = $errStr;
			}
		}

		if (!empty($response['log']['errors'])) {
			$response['log']['optional_msgs'][] = 'Upload completed but errors occurred, data was partially saved but some was skipped to avoid interrupting the process.';
			$response['log']['optional_msgs'][] = 'Review errors and if applicable, upload a CSV file contaning only data in which those errors are corrected (no need to re-upload the same full CSV file).';
		}
		$response['end_time'] = date('Y-m-d H:i:s');
		if (Hash::get($params, 'is_queued_job') == true && !empty($params['user_email'])) {
			$emailSettings = [
				'template' => Configure::read('App.bgJobEmailTemplate'),
				'from' => Configure::read('App.defaultSender'),
				'to' => $params['user_email'],
				'subject' => 'New CSV data collection upload job finished.',
				'emailBody' => $response
			];
			$this->Merchant->User->emailUser($params['user_email'], $response, $emailSettings);

		} else {
			return $response;
		}
	}

/**
 * getAssociatedModelData
 * Sets associated model primary and foreing key data if needed i.e. Model.id, AssocModel.foreign_key.
 * Analyzes key => value pairs in the mappedCsvFileHeadersToDbFields parameter by comparing them to this->csvFieldMap and checks if the folloing keys are present and maps them to a foreing Gateway.id if a matching the Gateway.name is found:
 * this->csvFieldMap['ImportedDataCollection']['Gateway 1']
 * this->csvFieldMap['ImportedDataCollection']['Gateway 2']
 *
 * Furthermore if in addition to the MID any of the keys in this->csvFieldMap['Merchat'] are present then associated merchant data and foreing keys will be added to the data
 * 
 * @param string $merchantMID a valid Merchant.merchant_mid
 * @param array $csvData array of new uploaded CSV data to be set into the returned data.
 * @param array $mappedCsvFileHeadersToDbFields associative array of the uploaded file's csvHeaders mapped to DB fields as their values.
 * @param string|int $month a month number 
 * @param string|int $year a 4 digit year
 * @param array &$uploadResponse reference to array containing response that will be returnened at the end of the whole process
 * @return array
 */
	public function getAssociatedModelData($merchantMID, array $csvData, array $mappedCsvFileHeadersToDbFields, $month, $year, &$uploadResponse) {
		//Retrieve all existing data
		$modelData = $this->getExistingData($merchantMID, $month, $year);
		$newData = [];
		$ModelObj = [];
		//The length and the order is expected to be the same between the entries in the $csvData and the $mappedCsvFileHeadersToDbFields map.
		//This allows direct access to the values in the $csvData O(1) by keeping track of the iterations in the outtermost loop.
		$idx = 0;
		$Gateway = ClassRegistry::init('Gateway');

		foreach ($mappedCsvFileHeadersToDbFields as $header => $fielName)  {
			$curModel = null;
			//skip iteration for CSV column headers that could not be mapped to any of the expected columns
			if (empty($fielName)) {
				$idx += 1;
				continue;
			}
			foreach ($this->csvFieldMap as $modelName => $fields) {
				if (empty($ModelObj[$modelName])) {
					$ModelObj[$modelName] = ClassRegistry::init($modelName);
				}

				//Find the Model to which this fielName belongs and memoize it
				if ($ModelObj[$modelName]->hasField($fielName)) {
					$curModel = $modelName;
					break;
				}
			}
			//Add only the Model data that exists in the imported set
			if (empty($newData[$curModel])) {
				$newData[$curModel] = $modelData[$curModel];
			}
			//skip MID csv column
			if (strtolower($header) == 'mid') {
				$idx += 1;
				continue;
			}
			$dataType = $ModelObj[$curModel]->getColumnType($fielName);
			if ($dataType == 'boolean') {
				$newData[$curModel][$fielName] = (strtolower($csvData[$idx]) == 'true');
			} elseif ($dataType == 'decimal' || $dataType == 'integer') {
				$newData[$curModel][$fielName] = $this->sanitizeNumStr($csvData[$idx]);
			} elseif ($dataType == 'date') {
				if (!empty($csvData[$idx])) {
					$dt = new DateTime($csvData[$idx]);
					$newData[$curModel][$fielName] = $dt->format('Y-m-d');
				}
			} else {
				if ($fielName == 'source_of_sale') {
					$newData[$curModel][$fielName] = Hash::get($this->Merchant->saleResourceOrigin, ucfirst(strtolower($csvData[$idx])));
				} elseif ($fielName == 'gw_n1_id' || $fielName == 'gw_n2_id' ) {
					$tmpVal = $Gateway->field('id', ['lower(name)' => strtolower($csvData[$idx])]);
					if ($tmpVal) {
						$newData[$curModel][$fielName] = $tmpVal;
					} else {
						$uploadResponse['log']['errors'][] = "Could not save $header \"$tmpVal\" for merchant $merchantMID because is not a known Gateway name, $header omitted.";
					}
				} elseif ($header == SalesForce::OPPTY_ID || $header == SalesForce::OPPTY_NAME) {
					//$curModel = ExternalRecordField
					//set hasMany data structure
					$tmpNewData = [
						'field_name' => $header,
						'api_field_name' => ClassRegistry::init('SalesForce')->fieldNames[$header]['field_name'],
						'value' => trim($csvData[$idx], '\'"')
					];
					$idxLocation = null;
					//iterate through existing ExternalRecordField records and try to find existing matching record
					foreach ($newData[$curModel] as $thisIdx => $extRecData) {
						if ($newData[$curModel][$thisIdx]['field_name'] == $header) {
							$idxLocation = $thisIdx;
							break;
						}
					}

					if ($idxLocation === null) {
						$newData[$curModel][] = $tmpNewData;
					} else {
						$newData[$curModel][$idxLocation] = array_merge($newData[$curModel][$idxLocation], $tmpNewData);
					}
				} else {
					$newData[$curModel][$fielName] = trim($csvData[$idx], '\'"');
				}
			}
			//handle special case fields
			if ($fielName == 'sf_closed_date') {
				//If the sf_closed_date corresponds to the win date header then the oppotinity was won
				$newData[$curModel]['sf_opportunity_won'] = (stripos($header, 'Won Date') !== false);
			}
			$idx += 1;
		}

		//Remove payment fusion product data if the merchant never had it, this process should not add the product
		if (!empty($newData['PaymentFusion']) && empty($newData['PaymentFusion']['merchant_id'])) {
			unset($newData['PaymentFusion']);
		}

		//Set month/year in ImportedDataCollection
		$newData['ImportedDataCollection']['month'] = $month;
		$newData['ImportedDataCollection']['year'] = $year;
		return $newData;
	}

/**
 * getExistingData
 * Returns an ImportedDataCollection record along with it's associated merchant data
 * This method will return data that will have a structure identical to $this->csvFieldMap
 * even if there is no current data, in which case the fields will be null.
 * 
 * @param  string $id a Merchant.id
 * @param  integer $month a month in number form
 * @param  integer $month a 4 digit year yyyy
 * @return array 
 */
	public function getExistingData($merchantMid, $month, $year) {
		//For efficiency build query fields as a Class member var so that when this function
		//is called in a loop from the same class instance the fields are already available
		if (!isset($this->existingDataQueryFields)) {
			$this->existingDataQueryFields = [
				'Merchant.id',
				'ImportedDataCollection.id',
				'ImportedDataCollection.merchant_id',
				'PaymentFusion.id',
				'PaymentFusion.merchant_id'
			];
			foreach ($this->csvFieldMap as $ModelName => $modelFields) {
				foreach ($modelFields as $fieldName) {
					if ($ModelName != 'ExternalRecordField') {
						$this->existingDataQueryFields[] = "$ModelName.$fieldName";
					}
				}
			}
		}
		return $this->Merchant->find('first', [
			'fields' => $this->existingDataQueryFields,
			'contain' => [
				'ExternalRecordField'
			],
			'conditions' => [
				'Merchant.merchant_mid' => $merchantMid,
			],
			'joins' => [
				[
					'alias' => 'ImportedDataCollection',
					'table' => 'imported_data_collections',
					'type' => 'LEFT',
					'conditions' => [
						'"Merchant"."id"  = "ImportedDataCollection"."merchant_id"',
						'"ImportedDataCollection"."year"' => $year,
						'"ImportedDataCollection"."month"' => $month
					]
				],
				[
					'table' => 'payment_fusions',
					'alias' => 'PaymentFusion',
					'type' => 'LEFT',
					'conditions' => [
						'"Merchant"."id" = "PaymentFusion"."merchant_id"'
					]
				],
			]
		]);
	}


/**
 * validUploadFile
 * Checks whether file extension is *.csv,
 * Checks that least one of the csv headers (besides the MID) is recorginzed
 * Counts the number of records
 *
 * @param string $phpTmpName the file path with the file name generated by PHP during upload from a browser form and placed in the request data array "tmp_name" => "php[aA-zZ]{6}"
 * @param string $fileName the name of the file (with expected *.csv file extension)
 * @return array containing validation errors if any or the number of rows when file is valid.
 */
	public function validUploadFile($phpTmpName, $fileName) {
		$validation = [
			'errors' => [],
			'row_count' => null
		];
		$hasValidHeader = false;
		$hasMidCol = false;
		//Return error when preg_match == false or == 0
		if (empty($phpTmpName) || empty($fileName) || preg_match('/\.csv$/', $fileName) == 0) {
			$validation['errors'][] = 'Invalid file! Verify file extension is .csv';
			return $validation;
		}
		try {
			$fh = @fopen($phpTmpName, 'rb');
			if ($fh === false) {
				throw new Exception ('ERROR: Unable to open file!');
			}
			$headers = array_map('trim', fgetcsv($fh, 10000));
			fclose($fh);
		} catch (Exception $e) {
			$validation['errors'][] = $e->getMessage();
			return $validation;
		}

		foreach ($this->csvFieldMap as $expected) {
			foreach ($headers as $header) {
				if (strtolower($header) == 'mid') {
					$hasMidCol = true;
					continue;
				}
				if (!empty($expected[$header])) {
					$hasValidHeader = true;
					break;
				}
			}
		}
		if (!$hasValidHeader || !$hasMidCol) {
			$validation['errors'][] = (!$hasMidCol)? 'The "MID" column is missing from this file!' : 'Invalid file content! The headers do not match any of the expected headers.';
		}
		if (empty($validation['errors'])) {
			$file = new \SplFileObject($phpTmpName, 'r');
			$file->seek(PHP_INT_MAX);
			//Total row count is $file->key() 
			$validation['row_count'] = $file->key();
		}
		return $validation;
	}

/**
 * getFieldMap
 * Returns a single-dimension associative array mapping csv headers to corresponding database fields.
 * When passing an array containng a subset of csv headers, the map returned will be built based on that subset. The order or the key=>val pairs is not be changed.
 * In both cases the returned array is built using $this->csvFieldMap
 * 
 * @param  array  $csvHeaders optional array of csv headers to map
 * @return array  a single-dimension associative array mapping csv headers to corresponding database fields
 */
	public function getFieldMap($csvHeaders = []) {
		$fMap = [];
		if (!empty($csvHeaders)) {
			foreach ($csvHeaders as $val) {
				foreach ($this->csvFieldMap as $arr) {
					if (!empty($arr[$val])) {
						$fMap[$val] = $arr[$val];
						break;
					} 
					else {
						//add a null-space mapping for any unrecognized headers so that it can be skipped
						$fMap[$val] = Hash::get($arr, $val);
					}
				}
			}
		} else {
			foreach ($this->csvFieldMap as $arr) {
				$fMap = array_merge($fMap, $arr);
			}
		}
		return $fMap;
	}

/**
 * getReportUIData 
 * Returns report data and data for report search filters on client side
 * Determines whether to use roll up layout to diplay the report which will be the case when the request is not
 * to export the report as CSV and a range of months is selected
 * 
 * @param array $reportData report data found by searching $filterParams 
 * @param array $filterParams search filters selected on client side
 * @param array $isExportRequest whether the client requested to export the report data as CSV
 * @return array
 */
	public function getData($reportData, $filterParams, $isExportRequest) {
		$organizations = $this->Merchant->Organization->find('list');
		$regions = $this->Merchant->Region->find('list');
		$subregions = $this->Merchant->Subregion->find('list');
		$partners = $this->Merchant->User->getByRole(User::ROLE_PARTNER);
		//use roll up layout when this is true
		$rollUpLayout = (!$isExportRequest && Hash::get($filterParams, 'from_date') && $filterParams['from_date'] !== $filterParams['end_date']);
		$headersMeta = $this->_fieldsMeta;
		$subsetIndices = null;
		if ($rollUpLayout && !empty($reportData)) {
			$subsetIndices = $this->getOrderedSameDataSubsetIndexes($reportData);
		}
		return compact(
			'rollUpLayout',
			'partners',
			'organizations',
			'regions',
			'subregions',
			'reportData',
			'headersMeta',
			'subsetIndices'
		);
	}

/**
 * getOrderedSameDataSubsetIndexes
 * This method iterates through the report data to locate index position of same-merchant data subsets
 * and places their index locations in an array in which they are grouped in a subsequent order.
 * An '<END>' marker is added after the very last same-merchant data subset index is found which can be used
 * to identify when one subset group ends and another begins.
 * The returned array of same-merchant-subset index locations can be used to directly access the same
 * merchant data subsets in the report data array as an O(n) operation.
 * Example return: 
 * $subsetLocs = [
 *      //Each sequence represents the index locations of same-Merchant data subsets
 *      //In this example the report data contains only 2 merchants with 3 and 2 subsets of data respectively
 *		0, 2, 4, '<END>', 1 , 3, '<END>'
 * ]
 * 
 * @param array $reportData [description]
 * @return array
 */
	public function getOrderedSameDataSubsetIndexes(array $reportData) {
		$indices = [];
		while (!empty($reportData)) {
			$curMid = null;
			foreach ($reportData as $i => $subset) {
				if (empty($curMid)) {
					$curMid = $subset['Merchant']['merchant_mid'];
				}
				
				if ($subset['Merchant']['merchant_mid'] == $curMid) {
					$indices[] = $i;
					unset($reportData[$i]);
				}
			}
			$indices[] = '<END>';
		}
		return $indices;
	}

/**
 * _findCollection
 * Custom finder query to retrieve report data
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findCollection($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = $this->getReportQueryFields();
			$query['joins'] =[
					[
						'table' => "merchants",
						'alias' => "Merchant",
						'type' => 'INNER',
						'conditions' => [
							'Merchant.id = ImportedDataCollection.merchant_id'
						]
					],
					[
						'table' => 'clients',
						'alias' => 'Client',
						'type' => 'LEFT',
							'conditions' => [
								"Merchant.client_id = Client.id",
							]
					],
					[
						'table' => "users",
						'alias' => "Partner",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.partner_id = Partner.id'
						]
					],
					[
						'table' => "users",
						'alias' => "Rep",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.user_id = Rep.id'
						]
					],
					[
						'table' => "organizations",
						'alias' => "Organization",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.organization_id = Organization.id'
						]
					],
					[
						'table' => "addresses",
						'alias' => "AddressBus",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = AddressBus.merchant_id',
							'AddressBus.address_type_id' => AddressType::BUSINESS_ADDRESS
						]
					],
					[
						'table' => "regions",
						'alias' => "Region",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.region_id = Region.id',
						]
					],
					[
						'table' => "uw_status_merchant_xrefs",
						'alias' => "UwStatusApproved",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = UwStatusApproved.merchant_id',
							"UwStatusApproved.uw_status_id = (SELECT id from uw_statuses where name = 'Approved')",
						]
					],
					[
						'table' => "timeline_entries",
						'alias' => "GoLiveTimeEntry",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = GoLiveTimeEntry.merchant_id',
							"GoLiveTimeEntry.timeline_item_id" => TimelineItem::GO_LIVE_DATE,
						]
					],
					[
						'table' => "merchant_cancellations",
						'alias' => "MerchantCancellation",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = MerchantCancellation.merchant_id',
						]
					],
					[
						'table' => "residual_reports",
						'alias' => "ResidualReport",
						'type' => 'LEFT',
						'conditions' => [
							'ImportedDataCollection.merchant_id = ResidualReport.merchant_id',
							"ResidualReport.products_services_type_id = (SELECT id from products_services_types where products_services_description = 'Payment Fusion')",
							'ImportedDataCollection.year = ResidualReport.r_year',
							'ImportedDataCollection.month = ResidualReport.r_month',
						]
					],
					[
						'table' => "gateways",
						'alias' => "FirstGateway",
						'type' => 'LEFT',
						'conditions' => [
							'ImportedDataCollection.gw_n1_id = FirstGateway.id',
						]
					],
					[
						'table' => "gateways",
						'alias' => "SecondGateway",
						'type' => 'LEFT',
						'conditions' => [
							'ImportedDataCollection.gw_n2_id = SecondGateway.id',
						]
					],
					[
						'table' => "external_record_fields",
						'alias' => "SfOpportinityId",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = SfOpportinityId.merchant_id',
							'SfOpportinityId.field_name' => SalesForce::OPPTY_ID,
						]
					],
					[
						'table' => "external_record_fields",
						'alias' => "SfOpportinityName",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = SfOpportinityName.merchant_id',
							'SfOpportinityName.field_name' => SalesForce::OPPTY_NAME,
						]
					],
					[
						'table' => "payment_fusions",
						'alias' => "PaymentFusion",
						'type' => 'LEFT',
						'conditions' => [
							'Merchant.id = PaymentFusion.merchant_id',
						]
					],
				];
			return $query;
		}
			return $results;
	}

/**
 * getReportQueryFields
 * Build array of fields based on $this->csvFieldMap to be used to build a report query
 * 
 * @return array
 */
	public function getReportQueryFields() {
		foreach ($this->_fieldsMeta as $fName => $meta) {
			if (!empty(Hash::get($meta, 'virtual_field'))) {
				$field = $meta['virtual_field'];
			} else {
				$field =  $meta['model_alias'] . "." . $fName;
			}
			$fields[] = $field;
		}
		
		return $fields;
	}
}
