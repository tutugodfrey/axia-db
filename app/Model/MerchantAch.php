<?php

App::uses('AppModel', 'Model');
App::uses('MerchantAchReason', 'Model');

/**
 * MerchantAch Model
 *
 * @property Merchant $Merchant
 * @property User $User
 * @property MerchantAchAppStatus $MerchantAchAppStatus
 * @property MerchantAchBillingOption $MerchantAchBillingOption
 * @property MerchantAchReason $MerchantAchReason
 */
class MerchantAch extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'merchant_aches';

/**
 * Find Method
 *
 * @var array
 */
	public $findMethods = [
		'accountingReport' => true,
	];

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Search.Searchable',
		'SearchByUserId' => array(
			'userRelatedModel' => 'Merchant'
		),
		'SearchByMonthYear' => array(
			'fulldateFieldName' => 'ach_date',
		),
	);

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
		'user_id' => [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'searchByMerchantEntity' => true,
		],
		'partners' => [
			'type' => 'value',
			'field' => 'Merchant.partner_id'
		],
		'status' => [
			'type' => 'value',
			'field' => '"MerchantAch"."status"'
		],
		'date_type' => [
			'type' => 'value',
			'field' => '"TimelineEntry"."timeline_item_id"'
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
 * Invoice type options
 *
 * @var mixed False or table name
 */
	public $invTypes = array(
		self::INV_CRT => 'Axia Invoice Credit',
		self::INV_DBT  => 'Axia Invoice Debit'
	);

 	const INV_CRT = 'ACR';
 	const INV_DBT = 'ADB';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
		'invoice_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Invoice number is required',
				'required' => true,
			),
		),
		'date_submitted' => array(
			'rule' => array('date', 'ymd'),
			'message' => 'Date submitted invalid format should be YYYY-MM-DD',
			'allowEmpty' => true
		)
	);
/**
* Accounting report labels to use on report content view
* 
* @var array
*/
	public $arLabels = [
		'status' => [
			'text' => 'Status',
			'sortField' => 'MerchantAch.status',
		],
		'accounting_mo_yr' => [
			'text' => 'Accounting Month',
			'sortField' => 'MerchantAch__acctg_month',
		],
		'date_completed' => [
			'text' => 'Date Completed',
			'sortField' => 'MerchantAch.date_completed',
		],
		'merchant_mid' => [
			'text' => 'MID',
			'sortField' => 'Merchant.merchant_mid',
			'sort_options' => [
				'direction' => 'desc'
			]
		],
		'merchant_dba' => [
			'text' => 'DBA',
			'sortField' => 'Merchant.merchant_dba',
		],
		'client_id_global' => [
			'text' => 'Client ID',
			'sortField' => 'Client.client_id_global',
		],
		'PartnerFullName' => [
			'text' => 'Partner',
			'sortField' => 'Partner__PartnerFullName',
		],
		'ach_setups' => [
			'text' => 'ACH Set Ups',
			'sortField' => 'InvoiceItem__ach_setups',
			'mapped_column' => true
		],
		'app_fees' => [
			'text' => 'Application Fees',
			'sortField' => 'InvoiceItem__app_fees',
			'mapped_column' => true
		],
		'equip_repair_fees' => [
			'text' => 'Equipment Repair & Maintenance',
			'sortField' => 'InvoiceItem__equip_repair_fees',
			'mapped_column' => true
		],
		'equip_sales' => [
			'text' => 'Equipment Sales',
			'sortField' => 'InvoiceItem__equip_sales',
			'mapped_column' => true
		],
		'replace_fees' => [
			'text' => 'Replacement Income',
			'sortField' => 'InvoiceItem__replace_fees',
			'mapped_column' => true
		],
		'expedite_fees' => [
			'text' => 'Expedite Fees',
			'sortField' => 'InvoiceItem__expedite_fees',
			'mapped_column' => true
		],
		'software_setup_fees' => [
			'text' => 'Gateway Setup Fees',
			'sortField' => 'InvoiceItem__software_setup_fees',
			'mapped_column' => true
		],
		'licence_setup_fees' => [
			'text' => 'License Setup Fees',
			'sortField' => 'InvoiceItem__licence_setup_fees',
			'mapped_column' => true
		],
		'account_setup_fees' => [
			'text' => 'Account Setup Fees',
			'sortField' => 'InvoiceItem__account_setup_fees',
			'mapped_column' => true
		],
		'client_implement_fees' => [
			'text' => 'Client Implementation Fees',
			'sortField' => 'InvoiceItem__client_implement_fees',
			'mapped_column' => true
		],
		'termination_fees' => [
			'text' => 'Termination Fees',
			'sortField' => 'InvoiceItem__termination_fees',
			'mapped_column' => true
		],
		'shipping_fees' => [
			'text' => 'Shipping',
			'sortField' => 'InvoiceItem__shipping_fees',
			'mapped_column' => true
		],
		'replacement_shipping_fees' => [
			'text' => 'Replacement Shipping',
			'sortField' => 'InvoiceItem__replacement_shipping_fees',
			'mapped_column' => true
		],
		'reject_fees' => [
			'text' => 'Reject Fees',
			'sortField' => 'InvoiceItem__reject_fees',
			'mapped_column' => true
		],
		'rental_fees' => [
			'text' => 'Rental Fees',
			'sortField' => 'InvoiceItem__rental_fees',
			'mapped_column' => true
		],
		'misc_fees' => [
			'text' => 'Miscellaneous Fees',
			'sortField' => 'InvoiceItem__misc_fees',
			'mapped_column' => true
		],
		'ach_amount' => [
			'text' => 'Taxable Total',
			'sortField' => 'MerchantAch__ach_amount',
		],
		'non_taxable_ach_amount' => [
			'text' => 'Non-Taxable Total',
			'sortField' => 'MerchantAch__non_taxable_ach_amount',
		],
		'subtotal' => [
			'text' => 'Subtotal',
			'sortField' => 'MerchantAch__subtotal',
		],
		'tax' => [
			'text' => 'Tax',
			'sortField' => 'MerchantAch__tax',
		],
		'total_ach' => [
			'text' => 'Invoice Total',
			'sortField' => 'MerchantAch__total_ach',
		],
		'billing_option_description' => [
			'text' => 'Bill To:',
			'sortField' => 'MerchantAchBillingOption.billing_option_description',
		],
		'account_number' => [
			'text' => 'Account Number',
			'sortField' => null,
		],
		'routing_number' => [
			'text' => 'Routing Number',
			'sortField' => null,
		],
		'merchant_email' => [
			'text' => 'Client Email',
			'sortField' => null,
		],
		'invoice_number' => [
			'text' => 'Invoice #',
			'sortField' => null,
		],
		'related_foreign_inv_number' => [
			'text' => 'Related External Invoice #',
			'sortField' => null,
		],
		'description' => [
			'text' => 'Description',
			'sortField' => '',
		],
		'tax_state_name' => [
			'text' => 'State',
			'sortField' => 'tax_state_name',
		],
		'tax_amount_state' => [
			'text' => 'State Tax Amount',
			'sortField' => 'MerchantAch__tax_amount_state',
		],
		'tax_county_name' => [
			'text' => 'County',
			'sortField' => 'tax_county_name',
		],
		'tax_amount_county' => [
			'text' => 'County Tax Amount',
			'sortField' => 'MerchantAch__tax_amount_county',
		],
		'tax_city_name' => [
			'text' => 'City',
			'sortField' => 'tax_city_name',
		],
		'tax_amount_city' => [
			'text' => 'City Tax Amount',
			'sortField' => 'MerchantAch__tax_amount_city',
		],
		'tax_amount_district' => [
			'text' => 'District Tax Amount',
			'sortField' => 'MerchantAch__tax_amount_district',
		],
		'non_taxed_reason' => [
			'text' => 'Non-Taxable Reason',
			'sortField' => null,
		],
		'address_street' => [
			'text' => 'Street Address',
			'sortField' => null,
		],
		'address_city' => [
			'text' => 'City',
			'sortField' => null,
		],
		'address_state' => [
			'text' => 'State',
			'sortField' => null,
		],
		'address_zip' => [
			'text' => 'Zip',
			'sortField' => null,
		],
	];

/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		$id = Hash::get($this->data, 'MerchantAch.id');
		if (!empty($id) && Hash::get($this->data, 'MerchantAch.status') === GUIbuilderComponent::STATUS_COMPLETED && 
			empty(Hash::get($this->data, 'MerchantAch.date_completed')) && $this->hasAny(['id' => $id, 'date_completed IS NULL'])) {
			$this->data['MerchantAch']['date_completed'] = date('Y-m-d');
		}
		if (!empty($this->data[$this->alias]['related_foreign_inv_number'])) {
            $this->data[$this->alias]['related_foreign_inv_number'] = $this->removeAnyMarkUp($this->data[$this->alias]['related_foreign_inv_number']);
        }
		if (!empty($this->data[$this->alias]['reason_other'])) {
            $this->data[$this->alias]['reason_other'] = $this->removeAnyMarkUp($this->data[$this->alias]['reason_other']);
        }
		if (!empty($this->data[$this->alias]['tax_city_name'])) {
            $this->data[$this->alias]['tax_city_name'] = $this->removeAnyMarkUp($this->data[$this->alias]['tax_city_name']);
        }
		if (!empty($this->data[$this->alias]['tax_county_name'])) {
            $this->data[$this->alias]['tax_county_name'] = $this->removeAnyMarkUp($this->data[$this->alias]['tax_county_name']);
        }
		if (!empty($this->data[$this->alias]['ship_to_street'])) {
            $this->data[$this->alias]['ship_to_street'] = $this->removeAnyMarkUp($this->data[$this->alias]['ship_to_street']);
        }
		if (!empty($this->data[$this->alias]['ship_to_city'])) {
            $this->data[$this->alias]['ship_to_city'] = $this->removeAnyMarkUp($this->data[$this->alias]['ship_to_city']);
        }
		if (!empty($this->data[$this->alias]['ship_to_zip'])) {
            $this->data[$this->alias]['ship_to_zip'] = $this->removeAnyMarkUp($this->data[$this->alias]['ship_to_zip']);
        }
	}
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Merchant',
		'User',
		'MerchantAchAppStatus',
		'MerchantAchBillingOption',
		'MerchantAchReason' => [
			'foreignKey' => 'merchant_ach_reason_id',
		]
	];

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'InvoiceItem' => array(
			'className' => 'InvoiceItem',
			'foreignKey' => 'merchant_ach_id',
			'dependent' => true
		),
	);

/**
 * orConditions for merchant dba/mid search
 *
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function orConditions($data = array()) {
		$filter = $data['dba_mid'];
		$cond = array(
			'OR' => array(
				'Merchant.merchant_mid ILIKE' => '%' . $filter . '%',
				'Merchant.merchant_dba ILIKE' => '%' . $filter . '%',
		));
		return $cond;
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
 * Custom finder query for Residual Report multiple
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findAccountingReport($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = array(
					"Merchant.merchant_mid",
					"Merchant.merchant_dba",
					"Client.client_id_global",
					'((' . $this->getReportVirtualFields("Partner__PartnerFullName") . ')) as "Partner__PartnerFullName"',
					/*Aggregated Invoice Item Fields*/
					//ACH Set Ups
					'((' . $this->getReportVirtualFields("InvoiceItem__ach_setups") . ')) as "InvoiceItem__ach_setups"',
					//Application Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__app_fees") . ')) as "InvoiceItem__app_fees"',
					//Equipment Repair & Maintenance
					'((' . $this->getReportVirtualFields("InvoiceItem__equip_repair_fees") . ')) as "InvoiceItem__equip_repair_fees"',
					//Equipment Sales
					'((' . $this->getReportVirtualFields("InvoiceItem__equip_sales") . ')) as "InvoiceItem__equip_sales"',
					//Replacement Income
					'((' . $this->getReportVirtualFields("InvoiceItem__replace_fees") . ')) as "InvoiceItem__replace_fees"',
					//Expedite Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__expedite_fees") . ')) as "InvoiceItem__expedite_fees"',
					//Gateway/software Setup Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__software_setup_fees") . ')) as "InvoiceItem__software_setup_fees"',
					//License Setup Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__licence_setup_fees") . ')) as "InvoiceItem__licence_setup_fees"',
					//Account Setup Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__account_setup_fees") . ')) as "InvoiceItem__account_setup_fees"',
					//Client Implementation Fee
					'((' . $this->getReportVirtualFields("InvoiceItem__client_implement_fees") . ')) as "InvoiceItem__client_implement_fees"',
					//Termination Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__termination_fees") . ')) as "InvoiceItem__termination_fees"',
					//Shipping
					'((' . $this->getReportVirtualFields("InvoiceItem__shipping_fees") . ')) as "InvoiceItem__shipping_fees"',
					//Replacement shipping fees
					'((' . $this->getReportVirtualFields("InvoiceItem__replacement_shipping_fees") . ')) as "InvoiceItem__replacement_shipping_fees"',
					//Reject Fee
					'((' . $this->getReportVirtualFields("InvoiceItem__reject_fees") . ')) as "InvoiceItem__reject_fees"',
					//Rental Fee
					'((' . $this->getReportVirtualFields("InvoiceItem__rental_fees") . ')) as "InvoiceItem__rental_fees"',
					//Miscellaneous Fees
					'((' . $this->getReportVirtualFields("InvoiceItem__misc_fees") . ')) as "InvoiceItem__misc_fees"',
					'((' . $this->getReportVirtualFields("MerchantAch__subtotal") . ')) as "MerchantAch__subtotal"',
					'((' . $this->getReportVirtualFields("MerchantAch__tax") . ')) as "MerchantAch__tax"',
					'((' . $this->getReportVirtualFields("MerchantAch__total_ach") . ')) as "MerchantAch__total_ach"',
					"MerchantAchBillingOption.billing_option_description",
					'((' . $this->getReportVirtualFields("account_number") . ')) as "MerchantAch__account_number"',
					'((' . $this->getReportVirtualFields("routing_number") . ')) as "MerchantAch__routing_number"',
					"Merchant.merchant_email",
					"MerchantAch.invoice_number",
					"MerchantAch.related_foreign_inv_number",
					//Description:
					'((' . $this->getReportVirtualFields("InvoiceItem__description") . ')) as "InvoiceItem__description"',
					'((' . $this->getReportVirtualFields("MerchantAch__acctg_month") . ')) as "MerchantAch__acctg_month"',
					"MerchantAch.id",
					"MerchantAch.merchant_id",
					"MerchantAch.date_completed",
					"MerchantAch.ach_amount",
					"MerchantAch.non_taxable_ach_amount",
					"MerchantAch.tax_state_name",
					"MerchantAch.tax_amount_state",
					"MerchantAch.tax_county_name",
					"MerchantAch.tax_amount_county",
					"MerchantAch.tax_city_name",
					"MerchantAch.tax_amount_city",
					"MerchantAch.tax_amount_district",
					"MerchantAch.status",
					//Non-Taxable Reasons
					'((' . $this->getReportVirtualFields("InvoiceItem__non_taxed_reason") . ')) as "InvoiceItem__non_taxed_reason"',
					'((' . $this->getReportVirtualFields("AddressBus__address_street") . ')) as "AddressBus__address_street"',
					'((' . $this->getReportVirtualFields("AddressBus__address_city") . ')) as "AddressBus__address_city"',
					'((' . $this->getReportVirtualFields("AddressBus__address_state") . ')) as "AddressBus__address_state"',
					'((' . $this->getReportVirtualFields("AddressBus__address_zip") . ')) as "AddressBus__address_zip"',
				);
			$query['joins'] = array(
					array(
						'table' => 'merchants',
						'alias' => 'Merchant',
						'type' => 'INNER',
						'conditions' => array(
							"{$this->alias}.merchant_id = Merchant.id",
						)
					),
					array(
						'table' => 'clients',
						'alias' => 'Client',
						'type' => 'LEFT',
						'conditions' => array(
							'Merchant.client_id = Client.id'
						)
					),
					array(
						'table' => 'addresses',
						'alias' => 'AddressBus',
						'type' => 'LEFT',
						'conditions' => array(
							"AddressBus.merchant_id = Merchant.id",
							"AddressBus.address_type_id = '" . AddressType::BUSINESS_ADDRESS . "'",
						)	
					),
					array(
						'table' => 'merchant_ach_billing_options',
						'alias' => 'MerchantAchBillingOption',
						'type' => 'LEFT',
						'conditions' => array(
							"{$this->alias}.merchant_ach_billing_option_id = MerchantAchBillingOption.id",
						)
					),
					array(
						'table' => 'merchant_banks',
						'alias' => 'MerchantBank',
						'type' => 'LEFT',
						'conditions' => array(
							"MerchantBank.merchant_id = Merchant.id",
						)
					),
					array(
						'table' => 'users',
						'alias' => 'Partner',
						'type' => 'LEFT',
						'conditions' => array(
							"Partner.id = Merchant.partner_id",
						)
					),
					array(
						'table' => 'invoice_items',
						'alias' => 'InvoiceItem',
						'type' => 'LEFT',
						'conditions' => array(
							"InvoiceItem.merchant_ach_id = {$this->alias}.id",
						)
					),
					array(
						'table' => 'merchant_ach_reasons',
						'alias' => 'MerchantAchReason',
						'type' => 'LEFT',
						'conditions' => array(
							"InvoiceItem.merchant_ach_reason_id = MerchantAchReason.id",
						)
					),
					array(
						'table' => 'non_taxable_reasons',
						'alias' => 'NonTaxableReason',
						'type' => 'LEFT',
						'conditions' => array(
							"InvoiceItem.non_taxable_reason_id = NonTaxableReason.id",
						)
					),
				);
			$query['group'] = array(
					'Merchant.merchant_mid',
					'Merchant.merchant_dba',
					'Merchant.merchant_email',
					'Client.client_id_global',
					'Partner.user_first_name',
					'Partner.user_last_name',
					'Partner.account_number',
					'Partner.routing_number',
					'MerchantBank.fees_dda_number',
					'MerchantBank.fees_routing_number',
					'MerchantBank.bank_dda_number',
					'MerchantBank.bank_routing_number',
					'MerchantAchBillingOption.billing_option_description',
					'MerchantAch.id',
					'MerchantAch.merchant_id',
					'InvoiceItem.merchant_ach_id',
					'AddressBus.address_street',
					'AddressBus.address_city',
					'AddressBus.address_state',
					'AddressBus.address_zip',
				);
			//Always exclude deleted invoices
			$query['conditions'][] = "MerchantAch.status != '" . GUIbuilderComponent::STATUS_DELETED . "'";
			$query['conditions'][] = "(MerchantAch.total_ach > 0 OR  MerchantAch.ach_amount > 0 OR MerchantAch.non_taxable_ach_amount > 0 OR MerchantAch.total_ach < 0 OR  MerchantAch.ach_amount < 0 OR MerchantAch.non_taxable_ach_amount < 0)";
			return $query;
		}
		return $results;
	}

/**
 * getReportFieldAliases
 * The elements in the array returned by this method must be used consistently throughout as the virtual fields.
 * If column sorting is desired on the report view, sortField names in the report view must exactly match the keys defined in the array returned by this method.
 * 
 * @param string $fieldName the name of the ResidualReport field
 * @return Mixed string | array containing all if no params are passed
 */
	public function getReportVirtualFields($fieldName = '') {
		$mappedColQueries = [];
		if (empty($fieldName)) {
			//build all mapped fields
			foreach ($this->arLabels as $colAlias => $colMetaData) {
				if (Hash::get($colMetaData, 'mapped_column')) {
					$mappedColQueries[$colMetaData['sortField']] = $this->buildMappedColumnQuery($colAlias);
				}
			}
		} elseif (strpos($fieldName, 'InvoiceItem__') !== false) {
			$colAlias =  str_replace('InvoiceItem__', '' , $fieldName);
			if (Hash::get($this->arLabels, "$colAlias.mapped_column")) {
				return $this->buildMappedColumnQuery($colAlias);
			}
		}
		$billOptQueryPart = '((CASE WHEN "MerchantAch"."merchant_ach_billing_option_id" = (Select id from merchant_ach_billing_options where billing_option_description ilike \'%partner%\' limit 1)';
		$billOptQueryPart2 = ' WHEN "MerchantAch"."merchant_ach_billing_option_id" = (Select id from merchant_ach_billing_options where billing_option_description ilike \'%client%\' limit 1)';
		$actNumPartnerQueryPart = ' THEN "Partner"."account_number"';
		$routNumPartnerQueryPart = ' THEN "Partner"."routing_number"';
		$actNumClientQueryPart = ' THEN COALESCE("MerchantBank"."fees_dda_number", "MerchantBank"."bank_dda_number")';
		$routNumClientQueryPart = ' THEN COALESCE("MerchantBank"."fees_routing_number", "MerchantBank"."bank_routing_number")';
		
		//Description:
		$descQuery = 'string_agg(\'$\' || COALESCE("InvoiceItem"."amount",0) || \' \' || COALESCE("MerchantAchReason"."reason",\'\') || \' \' || COALESCE("InvoiceItem"."reason_other", \'\'), \', \')';
		//Non-Taxable Reasons
		$whyNoTaxQuery = 'string_agg((CASE WHEN "InvoiceItem"."non_taxable_reason_id" is not null THEN COALESCE("MerchantAchReason"."reason",\'\') || \': \' || COALESCE("NonTaxableReason"."reason",\'\') ELSE NULL END), \', \')';
		$fields = [
			'MerchantAch__acctg_month' => 'make_date("MerchantAch"."acctg_year", "MerchantAch"."acctg_month", 1)',
			'MerchantAch__tax_amount_state' => '(COALESCE("MerchantAch"."tax_amount_state", 0))',
			'MerchantAch__tax_amount_county' => '(COALESCE("MerchantAch"."tax_amount_county", 0))',
			'MerchantAch__tax_amount_city' => '(COALESCE("MerchantAch"."tax_amount_city", 0))',
			'MerchantAch__tax_amount_district' => '(COALESCE("MerchantAch"."tax_amount_district", 0))',
			'MerchantAch__subtotal' => '(TRUNC(COALESCE("MerchantAch"."ach_amount", 0) + COALESCE("MerchantAch"."non_taxable_ach_amount", 0), 2))',
			'MerchantAch__tax' => '(COALESCE("MerchantAch"."tax", 0))',
			'MerchantAch__total_ach' => '(COALESCE("MerchantAch"."total_ach", 0))',
			'AddressBus__address_street' => 'AddressBus.address_street',
			'AddressBus__address_city' => 'AddressBus.address_city',
			'AddressBus__address_state' => 'AddressBus.address_state',
			'AddressBus__address_zip' => 'AddressBus.address_zip',
			'Partner__PartnerFullName' => $this->User->getFullNameVirtualField('Partner'),
			'account_number' => $billOptQueryPart . $actNumPartnerQueryPart . $billOptQueryPart2 . $actNumClientQueryPart . ' ELSE null END))',
			'routing_number' => $billOptQueryPart . $routNumPartnerQueryPart . $billOptQueryPart2 . $routNumClientQueryPart . ' ELSE null END))',
			'InvoiceItem__description' => $descQuery,
			'InvoiceItem__non_taxed_reason' => $whyNoTaxQuery,
		];
		$fields = array_merge($fields, $mappedColQueries);
		return (!empty($fieldName))? Hash::get($fields, $fieldName) : $fields;
	}

/**
* buildMappedColumnQuery
* Builds sspecial aggregate queries for columns marked as mapped in this->arLabels member variable
* Mapped accounting report columns are set in MerchantAchReason.accounting_report_col_alias so this function searches only 
* MerchantAchReason records that have accounting_report_col_alias set and matches it with existing columns in this->arLabels
* 
* @param string $accountingReportColAlias a column alias matching one of the mapped columns in $this->arLabels member variable
* @return string
*/
	public function buildMappedColumnQuery($accountingReportColAlias) {
		$mappedReasonIds = $this->InvoiceItem->MerchantAchReason->find('list', [
			'fields' => ['id'],
			'conditions' => ['accounting_report_col_alias' => $accountingReportColAlias]
		]);
		$beginQueryPart = 'TRUNC(SUM((CASE WHEN "InvoiceItem"."merchant_ach_reason_id" = ';
		$middleQueryParts = '';
		$lastQueryPart = ' THEN COALESCE("InvoiceItem"."amount", 0) ELSE 0 END)), 2)';

		foreach ($mappedReasonIds as $reasonId) {
			//Append first id
			if (empty($middleQueryParts)) {
				$middleQueryParts .= "'$reasonId'";
			} else {
				$middleQueryParts .= ' OR "InvoiceItem"."merchant_ach_reason_id" = ' . "'$reasonId'";
			}
		}
		if (empty(($mappedReasonIds))) {
			return 'NULL'; //return SQL NULL when no reasons are mapped to the current column
		}
 		return $beginQueryPart . $middleQueryParts . $lastQueryPart;
	}
/**
 * setPaginatorVirtualFields
 * In order for column sorting to work on the report view, the paginator requires virtual fields to be set
 * before paginate method is called in the controller. 
 * The sortField names are defined as the keys in the array returned by the ResidualReport->getReportVirtualFields()
 * and those must sortfield names must be used on the report view for sort to work.
 *
 * @return void
 */
	public function setPaginatorVirtualFields() {
		$this->virtualFields = $this->getReportVirtualFields();
	}
/**
 * createInvoiceNum method
 *
 * Generates partially random invoice numbers as follows:
 * First four digits are current year and month: yymm
 * The remaining 7 digits are random within the range of 1 - 9999999 and a min-length of 7 digits
 * If ((log10($radNumber)) + 1 < 7) the missing digits are filled with zeroes on the left to maintain 11 digits total.
 *
 * @return int
 */
	public function createInvoiceNum() {
		//generate a random number between given min/max
		$randInt = rand(1, 9999999);

		/* Make the randmon integer 7 digits long and append current year/month */
		$newInvoice = date("ym") . str_pad($randInt, 7, '0', STR_PAD_LEFT);

		return $newInvoice;
	}

/**
 * setPaginatorSettings method
 *
 * @param array $params of conditions to pass as part of the conditions array
 * @return array $settings for search
 * @throws BadMethodCallException
 */
	public function setPaginatorSettings($params) {
		if (!Hash::check($params, 'merchant_id')) {
			throw new BadMethodCallException('Missing \'merchant_id\' from params data');
		}
		$contain = [
			'MerchantAchAppStatus',
			'MerchantAchBillingOption',
			'InvoiceItem.MerchantAchReason' => [
				'order' => ['MerchantAchReason.position ASC']
			],
			'InvoiceItem.NonTaxableReason'
		];
		$settings = [
			'limit' => 500,
			'contain' => $contain,
			'conditions' => [
				'MerchantAch.merchant_id' => $params['merchant_id'],
				'MerchantAch.status !=' => GUIbuilderComponent::STATUS_DELETED
			],
			'order' => ['MerchantAch.date_submitted DESC']
		];
		return $settings;
	}

/**
 * getExpeditedInfoByMerchandId
 *
 * @param string $id merchant id
 * @return string $csvStr
 */
	public function getExpeditedInfoByMerchandId($id) {
		$data = $this->Merchant->MerchantUw->find('first', array(
			'conditions' => array('merchant_id' => $id),
			'fields' => array('id', 'merchant_id', 'expedited')
		));
		if (!empty($data['MerchantUw']['id'])) {
			return $data;
		} else {
			return array();
		}
	}

/**
 * makeCsvString
 * Builds a csv formatted string
 *
 * @param array $data data to save as csv
 * @return string $csvStr
 */
	public function makeCsvString($data) {
		$Number = new CakeNumber;
		$csvStr = "Ach Date,Reason,Credit Amount,Debit Amount,Tax,Total,Rejected?,Resubmit Date,Invoice Number\n";
		foreach ($data as $val) {
			$csvStr .= (!empty($val['MerchantAch']['ach_date'])) ? ',' . date('m/d/Y', strtotime($val['MerchantAch']['ach_date'])) : '';
			if (!empty($val['MerchantAch']['reason_other'])) {
				$csvStr .= ',' . $this->csvPrepString($val['MerchantAchReason']['reason'] . ": " . $val['MerchantAch']['reason_other']);
			} else {
				$csvStr .= ',' . $this->csvPrepString($val['MerchantAchReason']['reason']);
			}
			$csvStr .= ($val['MerchantAch']['is_debit'] === false) ? ',' . $Number->currency($val['MerchantAch']['ach_amount'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-')) : ',';
			$csvStr .= ($val['MerchantAch']['is_debit'] === true) ? ',' . $Number->currency($val['MerchantAch']['ach_amount'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-')) : ',';
			$csvStr .= ',' . $Number->currency($val['MerchantAch']['tax'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-'));
			$csvStr .= ',' . $Number->currency($val['MerchantAch']['total_ach'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-'));
			$csvStr .= ($val['MerchantAch']['rejected'] === true) ? ',YES' : ',NO';
			$csvStr .= (!empty($val['MerchantAch']['resubmit_date'])) ? ',' . date('m/d/Y', strtotime($val['MerchantAch']['resubmit_date'])) : ',';
			$csvStr .= ',' . $val['MerchantAch']['invoice_number'];
			$csvStr .= "\n";
		}
		return $csvStr;
	}

	public function getEditViewData($merchantId) {
		$merchant = $this->Merchant->getSummaryMerchantData($merchantId);
		$achReasons = $this->MerchantAchReason->getAchReasonsList();
		$billingOptns = $this->MerchantAchBillingOption->find('list', array(
			'fields' => array('id', 'billing_option_description'),
			'conditions' => array("billing_option_description NOT ILIKE '%rep%'"),
			'order' => array('billing_option_description ASC')
		));
		$appStatusOptns = $this->MerchantAchAppStatus->find('enabled');
		$defaultReasonNoTax = json_encode($this->MerchantAchReason->find(
			'list', array(
				'fields' => array('id', 'non_taxable_reason_id'),
				'conditions' => array('non_taxable_reason_id is not null'),
				)));
		$merchBusAddss = $this->Merchant->Address->getBusinessAddressByMerchantId($merchantId);
		$merchBusinessZip = (strlen(Hash::get($merchBusAddss, 'Address.address_zip')) === 4) ? '0' . Hash::get($merchBusAddss, 'Address.address_zip') : Hash::get($merchBusAddss, 'Address.address_zip');
		$merchBusinessState = Hash::get($merchBusAddss, 'Address.address_state');
		$merchBusinessCity = Hash::get($merchBusAddss, 'Address.address_city');
		if (Validation::postal($merchBusinessZip, null, 'us') === false) {
			$merchBusinessZip = '';
		}
		$nonTaxableReasons = $this->InvoiceItem->NonTaxableReason->getList();
		$outOfStateNonTaxId = $this->InvoiceItem->NonTaxableReason->field('id', ["reason ILIKE 'out of state'"]);// no wild card search, we want exact text but case insensitive
		$defaultBillOptnStr = MerchantAchBillingOption::CLIENT;
		$eqpmntFeeAchReasonId = MerchantAchReason::EQ_FEE;
		return compact(
			'eqpmntFeeAchReasonId',
			'outOfStateNonTaxId',
			'nonTaxableReasons',
			'merchant',
			'defaultReasonNoTax',
			'merchBusAddss',
			'merchBusinessZip',
			'merchBusinessState',
			'merchBusinessCity',
			'achReasons',
			'billingOptns',
			'appStatusOptns',
			'defaultBillOptnStr'
		);
	}

/**
 * processAccountingData
 * 
 * @param array $data report data retuned by find method _findAccountingReport
 * @param $forCsvExport
 * @return array Data ready to be displayed on accounting report view
 */
	public function processAccountingData($data = [], $forCsvExport = false) {
		$acctData = [];
		if (!empty($data)) {
			$totals['grand_total'] = 0;
			foreach ($data as $record) {
				$re = '/\d{4}$/';
				$bankAccount = $this->isEncrypted($record['MerchantAch']['account_number'])? $this->decrypt($record['MerchantAch']['account_number'], Configure::read('Security.OpenSSL.key')) : null;
				$routingNum = null;
				if (!empty($record['MerchantAch']['routing_number'])) {
					$routingNum = $this->isEncrypted($record['MerchantAch']['routing_number'])? $this->decrypt($record['MerchantAch']['routing_number'], Configure::read('Security.OpenSSL.key')) : $record['MerchantAch']['routing_number'];
				}

				if(!empty($bankAccount)) {
					if (!$forCsvExport) {
						preg_match($re, $bankAccount , $matches);
						$bankAccount = (!empty($matches[0]))?"******{$matches[0]}" : null;

						preg_match($re, $routingNum , $matches);
						$routingNum = (!empty($matches[0]))?"******{$matches[0]}" : null;
					}
				}
				$totals['grand_total'] = bcadd($totals['grand_total'], Hash::get($record, 'MerchantAch.total_ach'), 2);
				$acctData[] = [
					'id' => Hash::get($record, 'MerchantAch.id'),
					'merchant_id' => Hash::get($record, 'MerchantAch.merchant_id'),
					'status' => Hash::get($record, 'MerchantAch.status'),
					'accounting_mo_yr' => Hash::get($record, 'MerchantAch.acctg_month'),
					'date_completed' => Hash::get($record, 'MerchantAch.date_completed'),
					'merchant_mid' => Hash::get($record, 'Merchant.merchant_mid'),
					'merchant_dba' => Hash::get($record, 'Merchant.merchant_dba'),
					'client_id_global' => Hash::get($record, 'Client.client_id_global'),
					'PartnerFullName' => Hash::get($record, 'Partner.PartnerFullName'),
					'total_ach' => Hash::get($record, 'MerchantAch.total_ach'),
					'ach_amount' => Hash::get($record, 'MerchantAch.ach_amount'),
					'non_taxable_ach_amount' => Hash::get($record, 'MerchantAch.non_taxable_ach_amount'),
					'subtotal' => Hash::get($record, 'MerchantAch.subtotal'),
					'tax' => Hash::get($record, 'MerchantAch.tax'),
					'ach_setups' => Hash::get($record, 'InvoiceItem.ach_setups'),
					'app_fees' => Hash::get($record, 'InvoiceItem.app_fees'),
					'equip_repair_fees' => Hash::get($record, 'InvoiceItem.equip_repair_fees'),
					'equip_sales' => Hash::get($record, 'InvoiceItem.equip_sales'),
					'replace_fees' => Hash::get($record, 'InvoiceItem.replace_fees'),
					'expedite_fees' => Hash::get($record, 'InvoiceItem.expedite_fees'),
					'software_setup_fees' => Hash::get($record, 'InvoiceItem.software_setup_fees'),
					'licence_setup_fees' => Hash::get($record, 'InvoiceItem.licence_setup_fees'),
					'account_setup_fees' => Hash::get($record, 'InvoiceItem.account_setup_fees'),
					'client_implement_fees' => Hash::get($record, 'InvoiceItem.client_implement_fees'),
					'termination_fees' => Hash::get($record, 'InvoiceItem.termination_fees'),
					'shipping_fees' => Hash::get($record, 'InvoiceItem.shipping_fees'),
					'replacement_shipping_fees' => Hash::get($record, 'InvoiceItem.replacement_shipping_fees'),
					'reject_fees' => Hash::get($record, 'InvoiceItem.reject_fees'),
					'rental_fees' => Hash::get($record, 'InvoiceItem.rental_fees'),
					'misc_fees' => Hash::get($record, 'InvoiceItem.misc_fees'),
					'billing_option_description' => Hash::get($record, 'MerchantAchBillingOption.billing_option_description'),
					'account_number' => $bankAccount,
					'routing_number' => $routingNum,
					'merchant_email' => Hash::get($record, 'Merchant.merchant_email'),
					'invoice_number' => Hash::get($record, 'MerchantAch.invoice_number'),
					'related_foreign_inv_number' => Hash::get($record, 'MerchantAch.related_foreign_inv_number'),
					'description' => Hash::get($record, 'InvoiceItem.description'),
					'tax_state_name' => Hash::get($record, 'MerchantAch.tax_state_name'),
					'tax_amount_state' => Hash::get($record, 'MerchantAch.tax_amount_state'),
					'tax_county_name' => Hash::get($record, 'MerchantAch.tax_county_name'),
					'tax_amount_county' => Hash::get($record, 'MerchantAch.tax_amount_county'),
					'tax_city_name' => Hash::get($record, 'MerchantAch.tax_city_name'),
					'tax_amount_city' => Hash::get($record, 'MerchantAch.tax_amount_city'),
					'tax_amount_district' => Hash::get($record, 'MerchantAch.tax_amount_district'),
					'non_taxed_reason' => Hash::get($record, 'InvoiceItem.non_taxed_reason'),
					'address_street' => Hash::get($record, 'AddressBus.address_street'),
					'address_city' => Hash::get($record, 'AddressBus.address_city'),
					'address_state' => Hash::get($record, 'AddressBus.address_state'),
					'address_zip' => Hash::get($record, 'AddressBus.address_zip'),
				];
			}
			$acctData['totals'] = $totals;
		}
		return $acctData;
	}
/**
 * Return the view values for the accounting  report
 *
 * @param array $residualReports array with the residual reports to process
 * @param boolean $summarized if true a summarized version containing only totals will be returned. 
 * @return array
 */
	public function getReportData($reportData = [], $forCsvExport = false) {
		$users = $this->User->getEntityManagerUsersList(true);
		$organizations = $this->Merchant->Organization->find('list');
		$regions = $this->Merchant->Region->find('list');
		$subregions = $this->Merchant->Subregion->find('list');
		$partners = $this->User->getByRole(User::ROLE_PARTNER);
		$reportData = $this->processAccountingData($reportData, $forCsvExport);
		$labels = $this->arLabels;
		return compact('users', 'partners', 'organizations', 'regions', 'subregions', 'reportData', 'labels');
	}
}
