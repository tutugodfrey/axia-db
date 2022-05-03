<?php
App::uses('AppModel', 'Model');
App::uses('RolePermissions', 'Lib');
App::uses('ProductsServicesType', 'Model');
App::uses('TimelineItem', 'Model');
App::uses('AxiaCalculate', 'Model');
App::uses('AddressType', 'Model');

/**
 * CommissionPricing Model
 *
 * @property Merchant $Merchant
 * @property User $User
 */
class CommissionPricing extends AppModel {

/**
 * Commission pricing based reports
 *
 * @var string
 */
	//Commission Multiple Report type
	const REPORT_COMMISION_MULTIPLE = 'commissionMultiple';
	//Gros Profit Report type
	const REPORT_GROSS_PROFIT_ESTIMATE = 'grossProfitEstimate';
	//Gross Profit Analysis report type
	const REPORT_GP_ANALYSIS = 'gpAnalysis';
	//Commission Multiple Analysis report type
	const REPORT_CM_ANALYSIS = 'cmAnalysis';

/**
 * Model behaviors
 *
 * @var array
 */
	public $gprColLabels = [
		'mid' => [
			'text' => 'MID',
			'sortField' => 'Merchant.merchant_mid',
		],
		'dba' => [
			'text' => 'DBA',
			'sortField' => 'Merchant.merchant_dba',
		],
		'bet' => [
			'text' => 'Bet',
			'sortField' => 'Bet.name',
		],
		'client_id_global' => [
			'text' => 'Client ID',
			'sortField' => null,
		],
		'bus_level' => [
			'text' => 'Bus Level',
			'sortField' => 'Merchant.merchant_buslevel',
		],
		'product' => [
			'text' => 'Product',
			'sortField' => 'ProductsServicesType.products_services_description',
		],
		'sub_date' => [
			'text' => 'Sub. Date',
			'sortField' => 'TimelineSub.timeline_date_completed',
		],
		'days_to_approved' => [
			'text' => 'Days to Approved',
			'sortField' => 'days_to_approved',
		],
		'app_date' => [
			'text' => 'App. Date',
			'sortField' => 'TimelineApp.timeline_date_completed',
		],
		'days_to_installed' => [
			'text' => 'Days to Go-Live',
			'sortField' => 'days_to_installed',
		],
		'days_app_to_inst' => [
			'text' => 'Days Appr to Go-Live',
			'sortField' => 'days_app_to_inst',
		],
		'install_date' => [
			'text' => 'Go-Live Date',
			'sortField' => 'TimelineIns.timeline_date_completed',
		],
		'recd_install_sheet' => [
			'text' => "Rec'd Install Sheet",
			'sortField' => 'TimelineSis.timeline_date_completed',
		],
		'com_month' => [
			'text' => 'Com. Month',
			'sortField' => 'TimelineInc.timeline_date_completed',
		],
		'volume' => [
			'text' => 'Volume',
			'sortField' => 'CommissionPricing__m_monthly_volume',
		],
		'avg_tkt' => [
			'text' => 'Avg Tkt',
			'sortField' => 'CommissionPricing__m_avg_ticket',
		],
		'items' => [
			'text' => 'Items',
			'sortField' => 'CommissionPricing__num_items',
		],
		'original_merch_rate' => [
			'text' => 'Merch Rate',
			'sortField' => 'CommissionPricing__original_merch_rate',
		],
		'bet_extra_pct' => [
			'text' => 'Bet Extra %',
			'sortField' => 'CommissionPricing__bet_extra_pct',
		],
		'merch_rate' => [
			'text' => 'Merch Rate + Bet %',
			'sortField' => 'CommissionPricing__m_rate_pct',
		],
		'merch_p_i' => [
			'text' => 'Merch P/I',
			'sortField' => 'CommissionPricing__m_per_item_fee',
		],
		'merch_stmt' => [
			'text' => 'Merch Stmt',
			'sortField' => 'CommissionPricing__m_statement_fee',
		],
		'multiple' => [
			'text' => 'Multiple',
			'sortField' => 'CommissionPricing__multiple',
		],
		'multiple_amount' => [
			'text' => 'Multiple Amount',
			'sortField' => 'CommissionPricing__multiple_amount',
		],
		'resid_gross_profit' => [
			'text' => 'Resid. Gross Profit',
			'sortField' => null,
		],
		'rep' => [
			'text' => 'Rep',
			'sortField' => 'Rep__fullname',
		],
		'rep_rate' => [
			'text' => 'Rep Rate',
			'sortField' => 'CommissionPricing__r_rate_pct',
		],
		'rep_p_i' => [
			'text' => 'Rep P/I',
			'sortField' => 'CommissionPricing__r_per_item_fee',
		],
		'rep_stmt' => [
			'text' => 'Rep Stmt/Gtwy',
			'sortField' => 'CommissionPricing__r_statement_fee',
		],
		'rep_pct_of_gross' => [
			'text' => 'Rep % of Gross',
			'sortField' => 'CommissionPricing__rep_pct_of_gross',
		],
		'rep_gross_profit' => [
			'text' => 'Projected Rep Gross Profit',
			'sortField' => 'CommissionPricing__rep_gross_profit',
		],
		'rep_residual_gross_profit' => [
			'text' => 'Projected Rep Residual Gross Profit',
			'sortField' => null,
		],
		'rep_profit_pct' => [
			'text' => 'Rep Profit %',
			'sortField' => 'CommissionPricing.r_profit_pct',
		],
		'rep_profit' => [
			'text' => 'Rep Profit',
			'sortField' => 'CommissionPricing.r_profit_amount',
		],
		'partner_rep' => [
			'text' => 'Partner Rep',
			'sortField' => 'PartnerRep__fullname',
		],
		'partner_rep_rate' => [
			'text' => 'Partner Rep Rate',
			'sortField' => 'CommissionPricing__partner_rep_rate',
		],
		'partner_rep_p_i' => [
			'text' => 'Partner Rep P/I',
			'sortField' => 'CommissionPricing__partner_rep_per_item_fee',
		],
		'partner_rep_stmt' => [
			'text' => 'Partner Rep Stmt/Gtwy',
			'sortField' => 'CommissionPricing__partner_rep_statement_fee',
		],
		'partner_rep_pct_of_gross' => [
			'text' => 'Partner Rep % of Gross',
			'sortField' => 'CommissionPricing__partner_rep_pct_of_gross',
		],
		'partner_rep_gross_profit' => [
			'text' => 'Partner Rep Gross Profit',
			'sortField' => 'CommissionPricing__partner_rep_gross_profit',
		],
		'partner_rep_residual_gross_profit' => [
			'text' => 'Partner Rep Residual Gross Profit',
			'sortField' => null,
		],
		'partner_rep_profit_pct' => [
			'text' => 'Partner Rep Profit %',
			'sortField' => 'CommissionPricing.partner_rep_profit_pct',
		],
		'partner_rep_profit' => [
			'text' => 'Partner Rep Profit',
			'sortField' => 'CommissionPricing.partner_rep_profit_amount',
		],
		'manager_multiple' => [
			'text' => 'SM Multiple %',
			'sortField' => 'CommissionPricing.manager_multiple',
		],
		'manager_multiple_amount' => [
			'text' => 'SM Multiple Amount',
			'sortField' => 'CommissionPricing.manager_multiple_amount',
		],
		'manager2_multiple' => [
			'text' => 'SM2 Multiple %',
			'sortField' => 'CommissionPricing.manager2_multiple',
		],
		'manager2_multiple_amount' => [
			'text' => 'SM2 Multiple Amount',
			'sortField' => 'CommissionPricing.manager2_multiple_amount',
		],
		'ent_name' => [
			'text' => 'Company',
			'sortField' => 'Entity__entity_name',
		],
		'partner_name' => [
			'text' => 'Partner',
			'sortField' => 'Partner__fullname',
		],
		'partner_pct_of_gross' => [
			'text' => 'Partner % of Gross',
			'sortField' => 'CommissionPricing.partner_pct_of_gross',
		],
		'partner_profit_pct' => [
			'text' => 'Partner Profit %',
			'sortField' => 'CommissionPricing.partner_profit_pct',
		],
		'organization' => [
			'text' => 'Organization',
			'sortField' => 'Organization.name',
		],
		'region' => [
			'text' => 'Region',
			'sortField' => 'Region.name',
		],
		'subregion' => [
			'text' => 'Subregion',
			'sortField' => 'Subregion.name',
		],
		'location' => [
			'text' => 'Location',
			'sortField' => 'Address.address_street',
		],
	];

/**
 * Model behaviors
 *
 * @var array
 */
	public $actsAs = [
		'Search.Searchable',
		'SearchByUserId',
		'SearchByMonthYear' => [
			'yearFieldName' => 'c_year',
			'monthFieldName' => 'c_month',
		],
	];

/**
 * Searchable behavior filter arguments
 *
 * @var array
 */
	public $filterArgs = [
		'merchant_dba' => [
			'type' => 'like',
			'field' => '"Merchant"."merchant_dba"'
		],
		'user_id' => [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"Merchant"."user_id"',
			'searchByMerchantEntity' => true,
		],
		'partners' => [
			'type' => 'value',
			'field' => 'CommissionPricing.partner_id'
		],
		'from_date' => [
			'type' => 'query',
			'method' => 'timelineStartConditions',
		],
		'end_date' => [
			'type' => 'query',
			'method' => 'timelineEndConditions',
		],
		'products_services_type_id' => [
			'type' => 'query',
			'method' => 'productsServicesTypeConditions',
		],
		//Gross Profit Analysis filter
		'date_type' => [
			'type' => 'value',
			'field' => '"TimelineInc"."timeline_item_id"'
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
 * Find methods
 *
 * @var array
 */
	public $findMethods = [
		'commissionMultiple' => true,
		'grossProfitEstimate' => true,
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'merchant_id' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
		'user_id' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
		'products_services_type' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Merchant',
		'User',
		'ProductsServicesType',
	];

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
 * Custom finder query for commission multiple report
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findCommissionMultiple($state, $query, $results = []) {
		if ($state === 'before') {
			if (!empty($query['report_type'])) {
				$reportType = $query['report_type'];				
			} else {
				$reportType = CommissionPricing::REPORT_COMMISION_MULTIPLE;
			}
			$query = $this->_makeReportQuery($query, $reportType);
			return $query;
		}

		return $results;
	}

/**
 * Custom finder query for gross profit estimate report
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 *
 * @return array Modified query OR results of query
 */
	protected function _findGrossProfitEstimate($state, $query, $results = []) {
		if ($state === 'before') {

			$query = $this->_makeReportQuery($query, CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE);

			return $query;
		}

		if ($query['roll_up_data']) {
			return $this->_rollUpReportData($results);
		} else {

			return $this->_processCommissionPricingData($results, CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE);
		}
	}

/**
 * Return the commission pricing query to use on commission multiple
 * and gross profit estimate reports
 *
 * @param array $query base query from the custom finder
 * @param string $reportType Report type to run
 * @return array query for commission pricing based reports
 * @throws InvalidArgumentException
 */
	protected function _makeReportQuery($query, $reportType) {
		$cpcJoinConditions = [
			'Merchant.id = Cpc.merchant_id',
			"{$this->alias}.products_services_type_id = Cpc.products_services_type_id",
		];
		$tlIncJoinCond = [
			'Merchant.id = TimelineInc.merchant_id',
		];

		switch ($reportType) {
			case CommissionPricing::REPORT_COMMISION_MULTIPLE:
			case CommissionPricing::REPORT_CM_ANALYSIS:
			case CommissionPricing::REPORT_GP_ANALYSIS:
				$tlIncJoinType = "INNER";
				if ($reportType === CommissionPricing::REPORT_COMMISION_MULTIPLE) {
					$query['conditions']['TimelineInc.timeline_item_id'] = TimelineItem::INSTALL_COMMISSIONED;
				} elseif ($reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
					$query['conditions']['TimelineInc.timeline_item_id'] = TimelineItem::GO_LIVE_DATE;
				}
				$cpcJoinConditions[] = "Cpc.id = {$this->alias}.id";
				if ($reportType !== CommissionPricing::REPORT_GP_ANALYSIS) {
					$query['conditions'][]['OR'] = [
						'Cpc.multiple >' => 0,
						'Cpc.manager_multiple >' => 0,
						'Cpc.manager2_multiple >' => 0
					];
				}
				$query['conditions'][] = "CAST(TimelineInc.timeline_date_completed as text) LIKE {$this->alias}.c_year || '-' || LPAD(CAST({$this->alias}.c_month as text), 2, '0') || '-%'";
				break;

			case CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE:
				$tlIncJoinType = "LEFT";
				$cpcJoinConditions[] = "CAST(TimelineSub.timeline_date_completed as text) LIKE Cpc.c_year || '-' || LPAD(CAST(Cpc.c_month as text), 2, '0') || '-%'";
				$query['conditions'][] = "CAST(TimelineSub.timeline_date_completed as text) LIKE {$this->alias}.c_year || '-' || LPAD(CAST({$this->alias}.c_month as text), 2, '0') || '-%'";
				$tlIncJoinCond[] = 'TimelineInc.timeline_item_id = ' . "'" . TimelineItem::INSTALL_COMMISSIONED. "'";
				break;

			default:
				throw new InvalidArgumentException(__('Invalid report type for commission pricing query'));

		}

		$grossProfitFormula = "\"{$this->alias}\".\"partner_gross_profit\" - \"{$this->alias}\".\"referrer_gross_profit\" - \"{$this->alias}\".\"reseller_gross_profit\"";
		$residualGrossProfit = "\"{$this->alias}\".\"rep_gross_profit\" - $grossProfitFormula";
		$managerResidualGrossProfit = "\"{$this->alias}\".\"manager_gross_profit\" - $grossProfitFormula";
		$secondaryManagerResidualGrossProfit = "\"{$this->alias}\".\"manager2_gross_profit\" - $grossProfitFormula";
		// @todo: commented fields and joins dependent of other branches. Uncomment when branch is merged
		$query['fields'] = [
			'Merchant.merchant_mid',
			'Merchant.merchant_dba',
			'Client.client_id_global',
			'Merchant.merchant_buslevel',
			"Bet.name",
			"ProductsServicesType.products_services_description",
			"TimelineSub.timeline_date_completed",
			"TimelineApp.timeline_date_completed",
			"TimelineIns.timeline_date_completed",
			"TimelineIns.action_flag",
			"TimelineSis.timeline_date_completed",
			"TimelineInc.timeline_date_completed",
			$this->_formatCommissionPricingField('m_monthly_volume'),
			$this->_formatCommissionPricingField('m_avg_ticket'),
			$this->_formatCommissionPricingField('num_items'),
			$this->_formatCommissionPricingField('original_m_rate_pct'),
			$this->_formatCommissionPricingField('m_rate_pct'),
			$this->_formatCommissionPricingField('m_per_item_fee'),
			$this->_formatCommissionPricingField('m_statement_fee'),
			$this->_formatCommissionPricingField('multiple_amount'),
			'((CASE WHEN "Cpc"."merchant_id" IS NOT NULL ' .
				'THEN "Cpc"."multiple" ' .
				"ELSE 0 " .
				"END)) as \"{$this->alias}__multiple\"",
			"(($residualGrossProfit)) as \"{$this->alias}__residual_gross_profit\"",
			"(($managerResidualGrossProfit)) as \"{$this->alias}__manager_residual_gross_profit\"",
			"(($secondaryManagerResidualGrossProfit)) as \"{$this->alias}__secundary_manager_residual_gross_profit\"",
			// Rep data
			'((' . $this->User->getFullNameVirtualField('Rep') . ')) as "Rep__fullname"',
			$this->_formatCommissionPricingField('r_rate_pct'),
			$this->_formatCommissionPricingField('r_per_item_fee'),
			$this->_formatCommissionPricingField('r_statement_fee'),
			$this->_formatCommissionPricingField('rep_gross_profit'),
			$this->_formatCommissionPricingField('rep_pct_of_gross'),
			$this->_formatCommissionPricingField('rep_product_profit_pct'),
			$this->_formatCommissionPricingField('r_profit_amount'),
			// Partner data
			'((' . $this->User->getFullNameVirtualField('PartnerRep') . ')) as "PartnerRep__fullname"',
			$this->_formatCommissionPricingField('partner_rep_rate'),
			$this->_formatCommissionPricingField('partner_rep_per_item_fee'),
			$this->_formatCommissionPricingField('partner_rep_statement_fee'),
			$this->_formatCommissionPricingField('partner_rep_gross_profit'),
			$this->_formatCommissionPricingField('partner_rep_pct_of_gross'),
			$this->_formatCommissionPricingField('partner_rep_profit_pct'),
			$this->_formatCommissionPricingField('partner_rep_profit_amount'),
			// Manager data
			'((' . $this->User->getFullNameVirtualField('Manager') . ')) as "Manager__fullname"',
			$this->_formatCommissionPricingField('manager_rate'),
			$this->_formatCommissionPricingField('manager_per_item_fee'),
			$this->_formatCommissionPricingField('manager_statement_fee'),
			$this->_formatCommissionPricingField('manager_gross_profit'),
			$this->_formatCommissionPricingField('manager_pct_of_gross'),
			$this->_formatCommissionPricingField('manager_profit_pct'),
			$this->_formatCommissionPricingField('manager_profit_amount'),
			$this->_formatCommissionPricingField('manager_multiple_amount'),
			'((CASE WHEN "Cpc"."merchant_id" IS NOT NULL ' .
				'THEN "Cpc"."manager_multiple" ' .
				"ELSE 0 " .
				"END)) as \"{$this->alias}__manager_multiple\"",
			// Secondary Manager data
			'((' . $this->User->getFullNameVirtualField('SecondaryManager') . ')) as "SecondaryManager__fullname"',
			$this->_formatCommissionPricingField('manager2_rate'),
			$this->_formatCommissionPricingField('manager2_per_item_fee'),
			$this->_formatCommissionPricingField('manager2_statement_fee'),
			$this->_formatCommissionPricingField('manager2_gross_profit'),
			$this->_formatCommissionPricingField('manager2_pct_of_gross'),
			$this->_formatCommissionPricingField('manager2_profit_pct'),
			$this->_formatCommissionPricingField('manager2_profit_amount'),
			$this->_formatCommissionPricingField('manager2_multiple_amount'),
			'((CASE WHEN "Cpc"."merchant_id" IS NOT NULL ' .
				'THEN "Cpc"."manager2_multiple" ' .
				"ELSE 0 " .
				"END)) as \"{$this->alias}__manager2_multiple\"",
			// Partner data
			'((' . $this->User->getFullNameVirtualField('Partner') . ')) as "Partner__fullname"',
			$this->_formatCommissionPricingField('partner_id'),
			$this->_formatCommissionPricingField('partner_rate'),
			$this->_formatCommissionPricingField('partner_per_item_fee'),
			$this->_formatCommissionPricingField('partner_statement_fee'),
			$this->_formatCommissionPricingField('partner_gross_profit'),
			$this->_formatCommissionPricingField('partner_pct_of_gross'),
			$this->_formatCommissionPricingField('partner_profit_pct'),
			$this->_formatCommissionPricingField('partner_profit_amount'),
			// Referer data
			'((' . $this->User->getFullNameVirtualField('Referrer') . ')) as "Referrer__fullname"',
			$this->_formatCommissionPricingField('referrer_rate'),
			$this->_formatCommissionPricingField('referrer_per_item_fee'),
			$this->_formatCommissionPricingField('referrer_statement_fee'),
			$this->_formatCommissionPricingField('referrer_gross_profit'),
			$this->_formatCommissionPricingField('referrer_pct_of_gross'),
			$this->_formatCommissionPricingField('referrer_profit_pct'),
			$this->_formatCommissionPricingField('referrer_profit_amount'),
			// Reseller data
			'((' . $this->User->getFullNameVirtualField('Reseller') . ')) as "Reseller__fullname"',
			$this->_formatCommissionPricingField('reseller_rate'),
			$this->_formatCommissionPricingField('reseller_per_item_fee'),
			$this->_formatCommissionPricingField('reseller_statement_fee'),
			$this->_formatCommissionPricingField('reseller_gross_profit'),
			$this->_formatCommissionPricingField('reseller_pct_of_gross'),
			$this->_formatCommissionPricingField('reseller_profit_pct'),
			$this->_formatCommissionPricingField('reseller_profit_amount'),
			"Entity.entity_name",
		];
		if ($reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE) {
			$query['fields'][] = 'ProductCategory.category_name';
			$query['fields'][] = "((\"TimelineApp\".\"timeline_date_completed\" - \"TimelineSub\".\"timeline_date_completed\")) as \"days_to_approved\"";
			$query['fields'][] = "((\"TimelineIns\".\"timeline_date_completed\" - \"TimelineSub\".\"timeline_date_completed\")) as \"days_to_installed\"";
			$query['fields'][] = "((\"TimelineIns\".\"timeline_date_completed\" - \"TimelineApp\".\"timeline_date_completed\")) as \"days_app_to_inst\"";
		}
		if ($reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE || $reportType === CommissionPricing::REPORT_CM_ANALYSIS || $reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
			$query['fields'][] = 'Organization.name';
			$query['fields'][] = 'Region.name';
			$query['fields'][] = 'Subregion.name';
			$query['fields'][] = 'Address.address_street';
		}

		$query['joins'] = [
			[
				'table' => 'merchants',
				'alias' => 'Merchant',
				'type' => 'INNER',
				'conditions' => [
					"{$this->alias}.merchant_id = Merchant.id",
				],
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
				'table' => 'addresses',
				'alias' => 'Address',
				'type' => 'LEFT',
				'conditions' => [
					"Address.merchant_id = Merchant.id",
					'Address.address_type_id' => AddressType::BUSINESS_ADDRESS
				]
			],
			[
				'table' => 'organizations',
				'alias' => 'Organization',
				'type' => 'LEFT',
				'conditions' => [
					"Organization.id = Merchant.organization_id"
				]
			],
			[
				'table' => 'regions',
				'alias' => 'Region',
				'type' => 'LEFT',
				'conditions' => [
					"Region.id = Merchant.region_id"
				]
			],
			[
				'table' => 'subregions',
				'alias' => 'Subregion',
				'type' => 'LEFT',
				'conditions' => [
					"Subregion.id = Merchant.subregion_id"
				]
			],
			[
				'table' => 'timeline_entries',
				'alias' => 'TimelineSub',
				'type' => 'INNER',
				'conditions' => [
					'Merchant.id = TimelineSub.merchant_id',
					'TimelineSub.timeline_item_id' => TimelineItem::SUBMITTED,
				],
			],
			[
				'table' => 'timeline_entries',
				'alias' => 'TimelineApp',
				'type' => 'LEFT',
				'conditions' => [
					'Merchant.id = TimelineApp.merchant_id',
					'TimelineApp.timeline_item_id' => TimelineItem::APPROVED,
				],
			],
			[
				'table' => 'timeline_entries',
				'alias' => 'TimelineIns',
				'type' => 'LEFT',
				'conditions' => [
					'Merchant.id = TimelineIns.merchant_id',
					'TimelineIns.timeline_item_id' => TimelineItem::GO_LIVE_DATE,
				],
			],
			[
				'table' => 'timeline_entries',
				'alias' => 'TimelineSis',
				'type' => 'LEFT',
				'conditions' => [
					'Merchant.id = TimelineSis.merchant_id',
					'TimelineSis.timeline_item_id' => TimelineItem::RECIEVED_SIGNED_INSTALL_SHEET,
				],
			],
			[	//Merchants cannot be Install commissioned without being approved
				//for commission multiple report INNER join and LEFT join for gross profit report
				'table' => 'timeline_entries',
				'alias' => 'TimelineInc',
				'type' => $tlIncJoinType,
				'conditions' => $tlIncJoinCond
			],
			[
				'table' => 'commission_pricings',
				'alias' => 'Cpc',
				'type' => 'LEFT',
				'conditions' => $cpcJoinConditions
			],
			[
				'table' => 'products_and_services',
				'alias' => 'ProductsAndService',
				'type' => 'INNER',
				'conditions' => [
					'Merchant.id = ProductsAndService.merchant_id',
					'ProductsAndService.products_services_type_id = CommissionPricing.products_services_type_id',
				],
			],
			[
				'table' => 'products_services_types',
				'alias' => 'ProductsServicesType',
				'type' => 'INNER',
				'conditions' => [
					'ProductsAndService.products_services_type_id = ProductsServicesType.id',
				],
			],
			[
				'table' => 'entities',
				'alias' => 'Entity',
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.entity_id = Entity.id",
				],
			],
			[
				'table' => 'bet_tables',
				'alias' => 'Bet',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.bet_table_id = Bet.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Rep',
				'type' => 'INNER',
				'conditions' => [
					"{$this->alias}.user_id = Rep.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'PartnerRep',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.user_id = PartnerRep.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Manager',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.manager_id = Manager.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'SecondaryManager',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.secondary_manager_id = SecondaryManager.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Partner',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.partner_id = Partner.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Referrer',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.referer_id = Referrer.id",
				],
			],
			[
				'table' => 'users',
				'alias' => 'Reseller',
				'type' => 'LEFT',
				'conditions' => [
					"{$this->alias}.reseller_id = Reseller.id",
				],
			],
		];
		if ($reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE) {
			$query['joins'][] = [
				'table' => 'product_categories',
				'alias' => 'ProductCategory',
				'type' => 'LEFT',
				'conditions' => [
					"ProductsServicesType.product_category_id = ProductCategory.id",
				],
			];
		}
		if (!empty($query['sort'])) {
			$query['order'] = [
				$query['sort'] => $query['direction']
			];
		}

		if (empty($query['order'])) {
			$query['order'] = [
				'Merchant.merchant_mid' => 'ASC'
			];
		}
		return $query;
	}

/**
 * Note: Method to help creating commissionMultiple query
 *
 * Given a commission pricing field, create the sql to get the field from
 * CommissionPricingIns or CommissionPricingInc depending on merchant_id
 *
 * @param array $fieldName Field to format
 * @return array conditions array
 */
	protected function _formatCommissionPricingField($fieldName) {
		return "((CASE WHEN \"Cpc\".\"merchant_id\" IS NOT NULL " .
				"THEN \"Cpc\".\"{$fieldName}\" " .
				"ELSE \"{$this->alias}\".\"{$fieldName}\" " .
				"END)) as \"{$this->alias}__{$fieldName}\"";
	}

/**
 * getDateTypeOptions
 *
 * @return array containing values of date types constants
 */
	public function getDateTypeOptions() {
		return [TimelineItem::INSTALL_COMMISSIONED => 'Commission Date', TimelineItem::GO_LIVE_DATE => 'Go-Live Date'];
	}
/**
 * Date start conditions for TimelineEntry
 *
 * @param array $data Data submitted for search args
 * @return array conditions array
 * @throws BadMethodCallException
 */
	public function timelineStartConditions($data = []) {
		$reportType = Hash::get($data, 'report_type');
		if (empty($reportType)) {
			throw new BadMethodCallException(__('Missing report type'));
		}

		if ($reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE) {
			$modelAlias = 'TimelineSub';
		} else {
			$modelAlias = 'TimelineInc';
		}
		return $this->Merchant->TimelineEntry->dateStartConditions($data, $modelAlias);
	}

/**
 * Date end conditions for TimelineEntry
 *
 * @param array $data Data submitted for search args
 * @return array conditions array
 * @throws BadMethodCallException
 */
	public function timelineEndConditions($data = []) {
		$reportType = Hash::get($data, 'report_type');
		if (empty($reportType)) {
			throw new BadMethodCallException(__('Missing report type'));
		}

		if ($reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE) {
			$modelAlias = 'TimelineSub';
		} else {
			$modelAlias = 'TimelineInc';
		}

		return $this->Merchant->TimelineEntry->dateEndConditions($data, $modelAlias);
	}

/**
 * productConditions
 * Builds the query conditions to be used when searching by products in the reports
 * using a filter built with the options provided by the self->buildDdOptions(boolean) method
 * 
 * @param string $fiterArg data passed during search
 * @return void
 */
	public function productsServicesTypeConditions($fiterArg = []) {
		if ($fiterArg['products_services_type_id'] === ProductsServicesType::ALL_LEGACY_OPTN) {
			$legacyIds = $this->ProductsServicesType->find('list', ['conditions' => ['is_legacy' => true], 'fields' => ['id']]);
			return [
				['OR' => [
								"{$this->alias}.products_services_type_id" => $legacyIds,
								"Cpc.products_services_type_id" => $legacyIds,
								]
				]
			];
		} elseif ($this->isValidUUID($fiterArg['products_services_type_id'])) {
			$productId = Hash::get($fiterArg, 'products_services_type_id');
			return [
				['OR' => [
									"{$this->alias}.products_services_type_id" => $productId,
									'Cpc.products_services_type_id' => $productId,
								]
				]
			];
		}
	}

/**
 * Return the values for the commission multiple report
 *
 * @param array $filterParams Filter parameters
 * @return array
 */
	public function getCommissionMultipleData($filterParams = []) {
		$complexUserIdArgs = $this->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$this->filterArgs['user_id'] = $this->getUserSearchFilterArgs($complexUserIdArgs);
		$filterParams['report_type'] = (isset($filterParams['report_type']))? $filterParams['report_type']: CommissionPricing::REPORT_COMMISION_MULTIPLE;
		//Change filter args to commission pricing's method to filter by month and year range
		$this->filterArgs['from_date']['method'] = 'dateStartConditions';
		$this->filterArgs['end_date']['method'] = 'dateEndConditions';
		if (isset($filterParams['date_type'])) {
			$this->filterArgs['gpa_timeline_item_id']['value'] = $filterParams['date_type'];
		}

		$commissionMultiples = $this->find('commissionMultiple', [
			'conditions' => $this->parseCriteria($filterParams),
			'report_type' => $filterParams['report_type'],
			'order' => Hash::get($filterParams, 'order')
		]);

		return $this->_processCommissionPricingData($commissionMultiples, $filterParams['report_type'], $filterParams);
	}


	protected function _getThirdPartiesProfitAmnt($data) {
			$AxiaCalculate = ClassRegistry::init('AxiaCalculate');

			$profitAmounts['partner_profit_amount'] = $AxiaCalculate->thirdPartyProfitAmnt(
				[
					'u_gross_profit' => (is_numeric(Hash::get($data, 'CommissionPricing.partner_gross_profit')))? Hash::get($data, 'CommissionPricing.partner_gross_profit') : 0,
					'u_pct_of_gross' => (is_numeric(Hash::get($data, 'CommissionPricing.partner_pct_of_gross')))? Hash::get($data, 'CommissionPricing.partner_pct_of_gross') : 0,
					'u_residual_pct' => (is_numeric(Hash::get($data, 'CommissionPricing.partner_profit_pct')))? Hash::get($data, 'CommissionPricing.partner_profit_pct') : 0,
				]
			);
			$profitAmounts['referrer_profit_amount'] = $AxiaCalculate->thirdPartyProfitAmnt(
				[
					'u_gross_profit' => (is_numeric(Hash::get($data, 'CommissionPricing.referrer_gross_profit')))? Hash::get($data, 'CommissionPricing.referrer_gross_profit') : 0,
					'u_pct_of_gross' => (is_numeric(Hash::get($data, 'CommissionPricing.referrer_pct_of_gross')))? Hash::get($data, 'CommissionPricing.referrer_pct_of_gross') : 0,
					'u_residual_pct' => (is_numeric(Hash::get($data, 'CommissionPricing.referrer_profit_pct')))? Hash::get($data, 'CommissionPricing.referrer_profit_pct') : 0
				]
			);
			$profitAmounts['reseller_profit_amount'] = $AxiaCalculate->thirdPartyProfitAmnt(
				[
					'u_gross_profit' => (is_numeric(Hash::get($data, 'CommissionPricing.reseller_gross_profit')))? Hash::get($data, 'CommissionPricing.reseller_gross_profit') : 0,
					'u_pct_of_gross' => (is_numeric(Hash::get($data, 'CommissionPricing.reseller_pct_of_gross')))? Hash::get($data, 'CommissionPricing.reseller_pct_of_gross') : 0,
					'u_residual_pct' => (is_numeric(Hash::get($data, 'CommissionPricing.reseller_profit_pct')))? Hash::get($data, 'CommissionPricing.reseller_profit_pct') : 0
				]
			);
			unset($AxiaCalculate);
			return $profitAmounts;
	}
/**
 * Format the commission princing data from the reports to match the permission matrix
 * and create the calculated fields
 *
 * @param array $commissionPricings Commission pricings data
 * @param string $reportType Commission pricing report type
 * @param array $filterParams filter parameters selected by user during search
 * @throws InvalidArgumentException
 * @return array
 */
	protected function _processCommissionPricingData($commissionPricings, $reportType, $filterParams = []) {
		if (!is_array($commissionPricings)) {
			throw new InvalidArgumentException(__('"commissionPricings" must be an array'));
		}

		$formatedData = [];
		$totals = [
			'volume' => 0,
			'items' => 0,
			'avg_tkt' => 0,
			'multiple_amount' => 0,
			'manager_multiple_amount' => 0,
			'manager2_multiple_amount' => 0,
			'rep_gross_profit' => 0,
			'rep_residual_gp' => 0,
		];
		foreach ($commissionPricings as $commissionPricing) {
			//The action flag indicates exclude from multple, if set then skip
			if ($reportType === CommissionPricing::REPORT_COMMISION_MULTIPLE && Hash::get($commissionPricing, 'TimelineIns.action_flag') == true) {
				continue;
			}
			$hasPartner = (Hash::get($commissionPricing, 'CommissionPricing.partner_id') !== null);
			$productName = Hash::get($commissionPricing, 'ProductsServicesType.products_services_description');

			if ($reportType === CommissionPricing::REPORT_CM_ANALYSIS || $reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
				$volume = Hash::get($commissionPricing, 'CommissionPricing.m_monthly_volume');
			} else {
				//Show/calculate volume only for products matching this logic
				if (stripos($productName, "Discount") !== false || $productName === 'ACH') {
					$volume = Hash::get($commissionPricing, 'CommissionPricing.m_monthly_volume');
				} else {
					$volume = null;
				}
			}
			
			$multipleAmount = Hash::get($commissionPricing, 'CommissionPricing.multiple_amount');
			$mgrMultipleAmount = Hash::get($commissionPricing, 'CommissionPricing.manager_multiple_amount');
			$mgr2MultipleAmount = Hash::get($commissionPricing, 'CommissionPricing.manager2_multiple_amount');
			$residGrossProfit = Hash::get($commissionPricing, 'CommissionPricing.residual_gross_profit');
			$repGrossProfit = !$hasPartner ? Hash::get($commissionPricing, 'CommissionPricing.rep_gross_profit') : Hash::get($commissionPricing, 'CommissionPricing.partner_rep_gross_profit');
			$ratePlusBetPcts = Hash::get($commissionPricing, 'CommissionPricing.m_rate_pct');
			$originalMerchPct = Hash::get($commissionPricing, 'CommissionPricing.original_m_rate_pct');
			$betExtraPct = ($originalMerchPct > 0)? abs(bcsub($ratePlusBetPcts, $originalMerchPct)) : 0;
			$thirPartyProfitAmounts = $this->_getThirdPartiesProfitAmnt($commissionPricing);

			$data = [
				'mid' => Hash::get($commissionPricing, 'Merchant.merchant_mid'),
				'dba' => Hash::get($commissionPricing, 'Merchant.merchant_dba'),
				'bet' => Hash::get($commissionPricing, 'Bet.name'),
				'client_id_global' => Hash::get($commissionPricing, 'Client.client_id_global'),
				'bus_level' => Hash::get($commissionPricing, 'Merchant.merchant_buslevel'),
				'product' => $productName,
				'category_name' => Hash::get($commissionPricing, 'ProductCategory.category_name'),
				'sub_date' => Hash::get($commissionPricing, 'TimelineSub.timeline_date_completed'),
				'days_to_approved' => Hash::get($commissionPricing, '0.days_to_approved'),
				'app_date' => Hash::get($commissionPricing, 'TimelineApp.timeline_date_completed'),
				'days_to_installed' => Hash::get($commissionPricing, '0.days_to_installed'),
				'days_app_to_inst' => Hash::get($commissionPricing, '0.days_app_to_inst'),
				'install_date' => Hash::get($commissionPricing, 'TimelineIns.timeline_date_completed'),
				'recd_install_sheet' => Hash::get($commissionPricing, 'TimelineSis.timeline_date_completed'),
				'com_month' => Hash::get($commissionPricing, 'TimelineInc.timeline_date_completed'),
				'avg_tkt' => Hash::get($commissionPricing, 'CommissionPricing.m_avg_ticket'),
				'items' => Hash::get($commissionPricing, 'CommissionPricing.num_items'),
				'volume' => $volume,
				'original_merch_rate' => Hash::get($commissionPricing, 'CommissionPricing.original_m_rate_pct'),
				'bet_extra_pct' => $betExtraPct, //value calculated on the fly, not stored in DB
				'merch_rate' => Hash::get($commissionPricing, 'CommissionPricing.m_rate_pct'),
				'merch_p_i' => Hash::get($commissionPricing, 'CommissionPricing.m_per_item_fee'),
				'merch_stmt' => Hash::get($commissionPricing, 'CommissionPricing.m_statement_fee'),
				// Consolidate Rep data and PartnerRep Data
				'rep' => !$hasPartner ? Hash::get($commissionPricing, 'Rep.fullname') : Hash::get($commissionPricing, 'PartnerRep.fullname'),
				'rep_rate' => !$hasPartner ? Hash::get($commissionPricing, 'CommissionPricing.r_rate_pct') : Hash::get($commissionPricing, 'CommissionPricing.partner_rep_rate'),
				'rep_p_i' => !$hasPartner ? Hash::get($commissionPricing, 'CommissionPricing.r_per_item_fee') : Hash::get($commissionPricing, 'CommissionPricing.partner_rep_per_item_fee'),
				'rep_stmt' => !$hasPartner ? Hash::get($commissionPricing, 'CommissionPricing.r_statement_fee') : Hash::get($commissionPricing, 'CommissionPricing.partner_rep_statement_fee'),
				'rep_gross_profit' => $repGrossProfit,
				'partner_profit_amount' => $thirPartyProfitAmounts['partner_profit_amount'],
				'rep_residual_gp' => $repGrossProfit - $thirPartyProfitAmounts['partner_profit_amount'] - $thirPartyProfitAmounts['referrer_profit_amount'] - $thirPartyProfitAmounts['reseller_profit_amount'],
				'rep_pct_of_gross' => !$hasPartner ? Hash::get($commissionPricing, 'CommissionPricing.rep_pct_of_gross') : Hash::get($commissionPricing, 'CommissionPricing.partner_rep_pct_of_gross'),
				'rep_product_profit_pct' => Hash::get($commissionPricing, 'CommissionPricing.rep_product_profit_pct'),
				'multiple' => Hash::get($commissionPricing, 'CommissionPricing.multiple'),
				'multiple_amount' => $multipleAmount,
				'manager_multiple' => Hash::get($commissionPricing, 'CommissionPricing.manager_multiple'),
				'manager_multiple_amount' => $mgrMultipleAmount,
				'manager2_multiple' => Hash::get($commissionPricing, 'CommissionPricing.manager2_multiple'),
				'manager2_multiple_amount' => $mgr2MultipleAmount,
				'ent_name' => Hash::get($commissionPricing, 'Entity.entity_name'),
				'partner_name' => Hash::get($commissionPricing, 'Partner.fullname'),
				'partner_pct_of_gross' => Hash::get($commissionPricing, 'CommissionPricing.partner_pct_of_gross'),
				'partner_profit_pct' => Hash::get($commissionPricing, 'CommissionPricing.partner_profit_pct'),
			];
			if ($reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE  || $reportType === CommissionPricing::REPORT_CM_ANALYSIS || $reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
				$data['organization'] = Hash::get($commissionPricing, 'Organization.name');
				$data['region'] = Hash::get($commissionPricing, 'Region.name');
				$data['subregion'] = Hash::get($commissionPricing, 'Subregion.name');
				$data['location'] = Hash::get($commissionPricing, 'Address.address_street');
			}
			//remove fields that should not show in these reports
			if ($reportType === CommissionPricing::REPORT_COMMISION_MULTIPLE || $reportType === CommissionPricing::REPORT_CM_ANALYSIS || $reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
				unset($data['days_to_approved']);
				unset($data['days_to_installed']);
				unset($data['days_app_to_inst']);
			}
			if ($reportType === CommissionPricing::REPORT_COMMISION_MULTIPLE || $reportType === CommissionPricing::REPORT_GROSS_PROFIT_ESTIMATE) {
				unset($data['rep_residual_gp'], $data['partner_profit_amount']);
			}
			if ($reportType === CommissionPricing::REPORT_GP_ANALYSIS) {
				unset($data['rep_pct_of_gross'], $data['multiple_amount'], $data['com_month']);
				$totals['rep_residual_gp'] += $data['rep_residual_gp'];
				$totals['items'] += Hash::get($commissionPricing, 'CommissionPricing.num_items');
			}
			if ($reportType === CommissionPricing::REPORT_CM_ANALYSIS) {
				$totals['rep_residual_gp'] += $data['rep_residual_gp'];
				unset($totals['items']);
				unset($totals['avg_tkt']);
				unset($data['partner_profit_amount']);
			}
			//These fields should only be in the gp analysis report
			if ($reportType !== CommissionPricing::REPORT_CM_ANALYSIS || $reportType !== CommissionPricing::REPORT_GP_ANALYSIS) {
				unset($data['rep_product_profit_pct']);
				
			}
			$formatedData[] = $data;
			$totals['volume'] += $volume;
			$totals['multiple_amount'] += $multipleAmount;
			$totals['rep_gross_profit'] += $repGrossProfit;
		}
		if ($reportType === CommissionPricing::REPORT_GP_ANALYSIS && $totals['items'] != 0) {
			$totals['avg_tkt'] = bcdiv($totals['volume'], $totals['items'], 2);
		}
		if ($reportType === CommissionPricing::REPORT_COMMISION_MULTIPLE) {
			$totals['manager_multiple_amount'] = $this->getTotalMultipleAmnt(User::ROLE_SM, $filterParams);
			$totals['manager2_multiple_amount'] = $this->getTotalMultipleAmnt(User::ROLE_SM2, $filterParams);
		}
		$userId = $this->_getCurrentUser('id');
		$commissionsDataVarName = 'commissionMultiples';
		$commissionsTotalsVarName = 'commissionMultipleTotals';
		if ($reportType === self::REPORT_GROSS_PROFIT_ESTIMATE) {
			$commissionsDataVarName = 'grossProfitEstimates';
			$commissionsTotalsVarName = 'grossProfitEstimateTotals';
		}

		return [
			$commissionsDataVarName => RolePermissions::filterReportData(self::REPORT_COMMISION_MULTIPLE, $userId, $formatedData),
			$commissionsTotalsVarName => RolePermissions::filterReportData(self::REPORT_COMMISION_MULTIPLE, $userId, $totals),
		];
	}

/**
 * _rollUpReportData
 * Structures the data as follows
 * Data is grouped by merchant, then within each merchant group the products data is groupped by Product Category.
 * The resulting structure is
 * [ 
 *		'<mid 1>' => [
 *		 		'Product category A' => [
 *					'<commissions_data>' => [
 *						[<report data>]
 *						[<report data>]
 *		 		],
 *		 		'Product category Z' => [
 *					'<commissions_data>' => [
 *						[<report data>]
 *						[<report data>]
 *		 		]
 *	 		],
 *		'<mid n>' => [...]
 *	]
 *
 * @param array $data Residual Report data retreived using custom find method _findResidualReport
 * @return array
 */
	protected function _rollUpReportData($data) {
		$mGroups = Hash::combine($data, '{n}.ProductsServicesType.products_services_description', '{n}', '{n}.Merchant.merchant_mid');
		$totals= [];
		$result = [];
		foreach ($mGroups as $mid => $mGroup) {
			$categorizedProds = Hash::combine($mGroup, '{s}.ProductsServicesType.products_services_description', '{s}', '{s}.ProductCategory.category_name');
			foreach ($categorizedProds as $categoryName => $subset) {
				$result[$mid][$categoryName] = $this->_processCommissionPricingData($subset, self::REPORT_GROSS_PROFIT_ESTIMATE);

				//Add up all subset totals to get the grand total
				if (array_key_exists('volume', $result[$mid][$categoryName]['grossProfitEstimateTotals'])) {
					$totals['volume'] = bcadd(Hash::get($totals, 'volume'), $result[$mid][$categoryName]['grossProfitEstimateTotals']['volume'], 4);
				}
				if (array_key_exists('multiple_amount', $result[$mid][$categoryName]['grossProfitEstimateTotals'])) {
					$totals['multiple_amount'] = bcadd(Hash::get($totals, 'multiple_amount'), $result[$mid][$categoryName]['grossProfitEstimateTotals']['multiple_amount'], 4);
				}
				if (array_key_exists('rep_gross_profit', $result[$mid][$categoryName]['grossProfitEstimateTotals'])) {
					$totals['rep_gross_profit'] = bcadd(Hash::get($totals, 'rep_gross_profit'), $result[$mid][$categoryName]['grossProfitEstimateTotals']['rep_gross_profit'], 4);
				}
			}
		}

		return [
			'reportRollUpData' => $result,
			'totals' => $totals
		];
	}
/**
 * Return the default search form values
 *
 * @return array
 */
	public function getDefaultSearchValues() {
		$now = $this->_getNow();
		return [
			'from_date' => [
				'year' => $now->format('Y'),
				'month' => $now->format('m')
			],
			'end_date' => [
				'year' => $now->format('Y'),
				'month' => $now->format('m')
			],
			'user_id' => $this->getDefaultUserSearch(),
		];
	}

/**
 * getDefaultUserSearch
 * Returns the current user as the default user for search
 *
 * @return array
 */
	public function getDefaultUserSearch() {
		return $this->Merchant->User->buildComplexId(User::PREFIX_USER, $this->_getCurrentUser('id'));
	}

/**
 * getAnalysisFilterParams
 *
 * @param array $filterParams contains original search filterArgs submitted by the user
 * @param boolean $projectedArgs if true, will return filter parameters that are applicable to search for the projected residuals
 * @return array
 */
	public function getAnalysisFilterParams($filterParams, $projectedArgs = false, $reportType = null) {
		if (empty($filterParams['res_months'])) {
			$filterParams['res_months'] = 1;
		}
		if ($reportType === CommissionPricing::REPORT_CM_ANALYSIS && empty($filterParams['date_type'])) {
			$filterParams['date_type'] = TimelineItem::INSTALL_COMMISSIONED;
		}
		$searchableParams = $filterParams;
		if ($projectedArgs) {
			//Gross Profit Analysis - Projected residuals params
			if (!empty($filterParams['from_date'])) {
				$searchableParams['end_date'] = $filterParams['from_date'];
			}
		} else {
			//Gross Profit Analysis - Actual residuals params ResidualReport params
			$year = $filterParams['from_date']['year'];
			$month = $filterParams['from_date']['month'];
			$date = new DateTime("$year-$month-01");
			//The residual month is the month after the month selected so add one month as the from_date
			$date->add(new DateInterval('P1M'));
			$searchableParams['from_date']['year'] = $date->format('Y');
			$searchableParams['from_date']['month'] = $date->format('m');
			//$filterParams['res_months'] is not zero based therefore if user wants only one residual
			//month then don't add any months to from_date for the end_date
			$numOfResMonths = $filterParams['res_months'] - 1;
			$date->add(new DateInterval('P' . $numOfResMonths . 'M'));
			$searchableParams['end_date']['year'] = $date->format('Y');
			$searchableParams['end_date']['month'] = $date->format('m');
			$searchableParams['products'] = Hash::get($filterParams, 'products_services_type_id');
		}
		return $searchableParams;
	}

/**
 * Calculate total Multiple amounts based on filtering options
 *
 * @param string $userRole User role on the residual report operation
 * @param array $filterParams Filter parameters
 * @throws InvalidArgumentException
 *
 * @return string with the decimal numer
 */
	public function getTotalMultipleAmnt($userRole, $filterParams = []) {
		$amountField = null;
		$userField = null;
		$parsedId = $this->User->extractComplexId(Hash::get($filterParams, 'user_id'));
		$filterParams['report_type'] = CommissionPricing::REPORT_COMMISION_MULTIPLE;
		$this->filterArgs['from_date']['method'] = 'dateStartConditions';
		$this->filterArgs['end_date']['method'] = 'dateEndConditions';
		switch ($userRole) {
			case User::ROLE_REP:
				$amountField = 'multiple_amount';
				$userField = 'user_id';
				break;
			case User::ROLE_SM:
				$amountField = 'manager_multiple_amount';
				$userField = 'manager_id';
				break;

			case User::ROLE_SM2:
				$amountField = 'manager2_multiple_amount';
				$userField = 'secondary_manager_id';
				break;
			default:
				throw new InvalidArgumentException(__('Invalid user role'));
		}
		$options = [
			'fields' => ["SUM({$this->alias}.{$amountField}) as total_multiple_amount"],
			'contain' => [],
			'joins' => [
				[
					'table' => 'merchants',
					'alias' => 'Merchant',
					'type' => 'INNER',
					'conditions' => [
						"Merchant.id = {$this->alias}.merchant_id",
					]
				],
				[
					'table' => 'timeline_entries',
					'alias' => 'TimelineSub',
					'type' => 'INNER',
					'conditions' => [
						'Merchant.id = TimelineSub.merchant_id',
						'TimelineSub.timeline_item_id' => TimelineItem::SUBMITTED,
					],
				],
			],
			'conditions' => [],
		];
		// Conditions for filter parameters
		if (!empty($filterParams['merchant_dba'])) {
			$options['conditions'] = $this->parseCriteria(['merchant_dba' => $filterParams['merchant_dba']]);
		}
		$options['joins'][] = [
			'table' => 'timeline_entries',
			'alias' => 'TimelineInc',
			'type' => 'INNER',
			'conditions' => [
				"{$this->alias}.merchant_id = TimelineInc.merchant_id",
				'TimelineInc.timeline_item_id' => TimelineItem::INSTALL_COMMISSIONED,
			],
		];

		$fromConditions = $this->dateStartConditions($filterParams);
		$toConditions = $this->dateEndConditions($filterParams);
		$options['conditions'][] = $fromConditions;
		$options['conditions'][] = $toConditions;
		$options['conditions'][]['OR'] = [
			"{$this->alias}.multiple >" => 0,
			"{$this->alias}.manager_multiple >" => 0,
			"{$this->alias}.manager2_multiple >" => 0
		];
		$options['conditions'][] = "CAST(TimelineInc.timeline_date_completed as text) LIKE {$this->alias}.c_year || '-' || LPAD(CAST({$this->alias}.c_month as text), 2, '0') || '-%'";

		if (!empty($parsedId['id'])) {
			if ($parsedId['prefix'] == User::PREFIX_USER) {
				$options['conditions'][]['OR'] = [
					"{$this->alias}.user_id" => $parsedId['id'],
					"{$this->alias}.{$userField}" => $parsedId['id'],
				];
			} elseif ($parsedId['prefix'] == User::PREFIX_PARENT) {
				$options['conditions']["{$this->alias}.{$userField}"] = $parsedId['id'];
			} elseif ($parsedId['prefix'] == User::PREFIX_ENTITY) {
				$options['conditions']["Merchant.entity_id"] = $parsedId['id'];
			}
		}
		$result = $this->find('all', $options);

		return Hash::get($result, '0.0.total_multiple_amount');
	}
}
