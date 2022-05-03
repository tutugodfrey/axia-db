<?php

App::uses('AppModel', 'Model');

/**
 * MerchantPricing Model
 *
 * @property Gateway $Gateway
 * @property Merchant $Merchant
 */
class MerchantPricing extends AppModel {

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
			'ChangeRequest'
		);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'processing_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'Visa'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have Visa, first add the product (empty field if not needed).',
					'on' => 'update'
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'MasterCard'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have MasterCard, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'ds_processing_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'Discover'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have Discover, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'mc_vi_auth' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'V/MC Auth Fee must be a valid amount number',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'Visa'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have Visa, first add the product (empty field if not needed).',
					'on' => 'update'
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'MasterCard'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have MasterCard, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'ds_auth_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Discover Auth Fee must be a valid amount number',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'Discover'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have Discover, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'discount_item_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Discount Item Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'amex_auth_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Amex Auth Fee	must be a valid amount number',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'American Express'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have American Express, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'aru_voice_auth_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'ARU/Voice Auth Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'wireless_auth_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Wireless Auth Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'statement_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Statement Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'min_month_process_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Monthly Minimum Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'debit_access_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Debit Access Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ebt_access_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'EBT Access Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'gateway_access_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Gateway Access Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'wireless_access_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Wireless Access Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'annual_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Annual Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'chargeback_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Chargeback Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'billing_mc_vi_auth' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Billing V/MC Auth Fee	must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'billing_discover_auth' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Billing Discover Auth Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'billing_amex_auth' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Billing Amex Auth Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'billing_debit_auth' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Billing Debit Auth Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'billing_ebt_auth' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Billing EBT Auth Fee must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'wireless_auth_cost' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Wireless Per Item Cost must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'num_wireless_term' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Please supply a valid number of terminals.',
				'allowEmpty' => true
			),
			'per_wireless_term_cost' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Per Wireless Terminal Cost must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'total_wireless_term_cost' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Total Monthly Wireless Cost must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'debit_auth_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Pin Debit Authorization must be a valid amount number',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'Debit'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have Debit, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'ebt_auth_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'EBT Authorization must be a valid amount number',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'EBT'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have EBT, first add the product (empty field if not needed).',
					'on' => 'update'
				)
			),
			'debit_discount_item_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'Debit Discount P/I must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'ebt_discount_item_fee' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => 'EBT Discount P/I must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'debit_processing_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'Debit'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have Debit, first add the product (empty field if not needed).',
					'on' => 'update'
				),
			),
			'ebt_processing_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'EBT'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have EBT, first add the product (empty field if not needed).',
					'on' => 'update'
				),
			),
			'amex_processing_rate' => array(
				'validPercentage' => array(
					'rule' => 'validPercentage',
					'allowEmpty' => true
				),
				'pricingMatchesProducts' => array(
					'rule' => array('pricingMatchesProducts', 'American Express'),
					'allowEmpty' => true,
					'required' => false,
					'message' => 'Merchant doesn\'t have American Express, first add the product (empty field if not needed).',
					'on' => 'update'
				),
			),	
		);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
			'Merchant' => array(
						'className' => 'Merchant',
						'foreignKey' => 'merchant_id',
			),
			'DebitAcquirer' => array(
						'className' => 'DebitAcquirer',
						'foreignKey' => 'debit_acquirer_id',
			),
			'EbtAcquirer' => array(
						'className' => 'DebitAcquirer',
						'foreignKey' => 'ebt_acquirer_id',
			),
			'Gateway' => array(
						'className' => 'Gateway',
						'foreignKey' => 'gateway_id',
			),
			'McBetTable' => array(
						'className' => 'BetTable',
						'foreignKey' => 'mc_bet_table_id',
			),
			'VisaBetTable' => array(
						'className' => 'BetTable',
						'foreignKey' => 'visa_bet_table_id',
			),
			'DiscoverBetTable' => array(
						'className' => 'BetTable',
						'foreignKey' => 'ds_bet_table_id',
			),
			'DebitBetTable' => array(
						'className' => 'BetTable',
						'foreignKey' => 'db_bet_table_id',
			),
			'EbtBetTable' => array(
						'className' => 'BetTable',
						'foreignKey' => 'ebt_bet_table_id',
			),
			'AmexBetTable' => array(
						'className' => 'BetTable',
						'foreignKey' => 'amex_bet_table_id',
			),

	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		//sanitize specific fields
		if (!empty($this->data[$this->alias]['features'])) {
			$this->data[$this->alias]['features'] = $this->removeAnyMarkUp($this->data[$this->alias]['features']);
		}
		
		return parent::beforeSave($options);
	}

/**
 * getMerchantPricingByMerchantId
 *
 * @param string $id a merchant id
 * @return array merchant pricing data
 */
	public function getMerchantPricingByMerchantId($id) {
		$contain = array('MerchantCardType' => array('CardType'),
				'MerchantPricing' => array(
					'Gateway',
					'DebitAcquirer',
					'EbtAcquirer',
					'McBetTable' => array('fields' => array('name')),
					'VisaBetTable' => array('fields' => array('name')),
					'DiscoverBetTable' => array('fields' => array('name')),
					'DebitBetTable' => array('fields' => array('name')),
					'EbtBetTable' => array('fields' => array('name')),
					'AmexBetTable' => array('fields' => array('name'))
				),
				'ProductsAndService' => array(
					'ProductsServicesType' => array('ProductSetting')
					),
				'Gateway1' => array('Gateway'),
				'PaymentFusion.PaymentFusionsProductFeature.ProductFeature',
				'User',
				'MerchantUwVolume',
				'ProductSetting',
				'MerchantPci',
				'Ach' => array('AchProvider'),
				'CheckGuarantee' => array('CheckGuaranteeProvider', 'CheckGuaranteeServiceType'),
				'GiftCard' => array('GiftCardProvider'),
				'WebBasedAch');

		$merchant = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $id), 'contain' => $contain));

		if (!empty($merchant['Ach']['id'])) {
			$merchant['Ach'] = $this->decryptFields($merchant['Ach']);
			$this->Merchant->Ach->truncateAccounts($merchant);
		}
		if (!empty($merchant['ProductSetting'])) {
			$merchant['ProductFeature'] = $this->Merchant->ProductSetting->ProductFeature->find('list',
				[
				'fields' => ['id', 'feature_name', 'products_services_type_id'],
				'order' => ["feature_name" => 'ASC']
				]);
		}

		return $merchant;
	}

/**
 * setDynamicValidation
 *
 * @param array $data data equivalent to $this->data
 * @return void
 */
	public function setDynamicValidation($data) {
		//Set conditional Bet and product validation rules
		$validator = $this->validator();
		if (!empty(Hash::get($data, 'MerchantPricing.merchant_id'))) {
			$merchantMid = $this->Merchant->field("merchant_mid", array("id" => Hash::get($data, 'MerchantPricing.merchant_id')));
			//Nonaquiring merchants are excempt from this set of validation rules
			if ($this->Merchant->isNonAcquiringMid($merchantMid)) {
				return;
			}

			$validator['visa_bet_table_id'] = [
				'isRequiredBetOrProduct' => [
					'rule' => 'isRequiredBetOrProduct',
					'allowEmpty' => !$this->Merchant->ProductsAndService->hasAny([
						'merchant_id' => $data['MerchantPricing']['merchant_id'],
						"products_services_type_id in (select id from products_services_types where products_services_description ILIKE 'Visa%')"
					]),
					'message' => "BET is required! This merchant has a Visa product"
				],
			];
			$validator['mc_bet_table_id'] = [
				'isRequiredBetOrProduct' => [
					'rule' => 'isRequiredBetOrProduct',
					'allowEmpty' => !$this->Merchant->ProductsAndService->hasAny([
						'merchant_id' => $data['MerchantPricing']['merchant_id'],
						"products_services_type_id in (select id from products_services_types where products_services_description ILIKE 'MasterCard%')"
					]),
					'message' => "BET is required! This merchant has a MasterCard product"
				],
			];
			$validator['ds_bet_table_id'] = [
				'isRequiredBetOrProduct' => [
					'rule' => 'isRequiredBetOrProduct',
					'allowEmpty' => !$this->Merchant->ProductsAndService->hasAny([
						'merchant_id' => $data['MerchantPricing']['merchant_id'],
						"products_services_type_id in (select id from products_services_types where products_services_description ILIKE 'Discover%')"
					]),
					'message' => "BET is required! This merchant has Discover products"
				],
			];
			$validator['amex_bet_table_id'] = [
				'isRequiredBetOrProduct' => [
					'rule' => 'isRequiredBetOrProduct',
					'allowEmpty' => !$this->Merchant->ProductsAndService->hasAny([
						'merchant_id' => $data['MerchantPricing']['merchant_id'],
						"products_services_type_id in (select id from products_services_types where products_services_description ILIKE 'American Express%')"
					]),
					'message' => "BET is required! This merchant has American Express products"
				],
			];
			$validator['db_bet_table_id'] = [
				'isRequiredBetOrProduct' => [
					'rule' => 'isRequiredBetOrProduct',
					'allowEmpty' => !$this->Merchant->ProductsAndService->hasAny([
						'merchant_id' => $data['MerchantPricing']['merchant_id'],
						"products_services_type_id in (select id from products_services_types where products_services_description ILIKE 'Debit%')"
					]),
					'message' => "BET is required! This merchant has Debit products"
				],
			];
			$validator['ebt_bet_table_id'] = [
				'isRequiredBetOrProduct' => [
					'rule' => 'isRequiredBetOrProduct',
					'allowEmpty' => !$this->Merchant->ProductsAndService->hasAny([
						'merchant_id' => $data['MerchantPricing']['merchant_id'],
						"products_services_type_id in (select id from products_services_types where products_services_description LIKE 'EBT %')"
					]),
					'message' => "BET is required! This merchant has EBT products"
				],
			];
		}
	}

/**
 * isRequiredBetOrProduct
 * Custom validation rule
 * Sets requirement for bet and/or product.
 * If a BET is seleted but corresponding product has not been added to the merchant an error will be returned
 * If the inverse ocurrs the BET will be required and an error will be returned
 *
 * @param array $check associative array containing one element: field => value
 * @return array merchant pricing data
 */
	public function isRequiredBetOrProduct($check) {
		$key = key($check);
		$bet = Hash::get($check, $key);
		
		switch ($key) {
			case 'mc_bet_table_id':
					$productName = 'MasterCard';
				break;
			case 'visa_bet_table_id':
					$productName = 'Visa';
				break;
			case 'ds_bet_table_id':
					$productName = 'Discover';
				break;
			case 'db_bet_table_id':
					$productName = 'Debit';
				break;
			case 'ebt_bet_table_id':
					$productName = 'EBT';
				break;
			case 'amex_bet_table_id':
					$productName = 'American Express';
				break;
		}

		$hasProduct = $this->Merchant->ProductsAndService->hasAny([
			'merchant_id' => $this->data['MerchantPricing']['merchant_id'],
			"products_services_type_id in (select id from products_services_types where products_services_description ILIKE '" . $productName . "%')"
		]);
		$message = null;
		if (!empty($bet) && $hasProduct === false) {
			$message = "Merchant doesn't have $productName! Add product first. Leave blank if not needed.";
		}
		if (empty($bet) && $hasProduct === true) {
			$message = "BET is required! This merchant has a $productName product";
		}

		if (!is_null($message)) {
			return $message;
		}
		return true;	
	}

/**
 * pricingMatchesProducts
 * Custom validation rule
 * Checks whether field is not null. If not, then checks whether merchan has the product that corresponds to the pricing in the field in question.
 *
 * @param array $check associative array containing one element: field => value
 * @param string $productName The name of the product for which the pricing value in the field is being added.
 *							  To match many products that share a similar name the part of the name that is common to those products may be passed. Otherwise the full product name.
 * @return array merchant pricing data
 */
	public function pricingMatchesProducts($check, $productName) {
		$value = array_pop($check);
		if (is_numeric($value)) {
			return $this->Merchant->ProductsAndService->hasAny([
				'merchant_id' => $this->data['MerchantPricing']['merchant_id'],
				"products_services_type_id in (select id from products_services_types where products_services_description ILIKE '" . $productName. "%')"
			]);
		}
		return true;
	}

/**
 * getMerchantPricingByMerchantId
 *
 * @param string $id MerchantPricing id
 * @return array merchant pricing data
 */
	public function getEditViewData($id) {
		$options = array(
			'conditions' => array('MerchantPricing.id' => $id));
		$data = $this->find('first', $options);
		return $data;
	}

/**
 * setFormMenuData
 * Gathers all data required for menus on the edit form	
 *
 * @param array $reqData request data
 * @return array
 */
	public function setFormMenuData($reqData) {
		$allBetTables = $this->VisaBetTable->getAllGroupedByCardType();
		$visaBetTables = array_combine(Hash::extract($allBetTables, 'Visa.{n}.id'), Hash::extract($allBetTables, 'Visa.{n}.name'));
		$mcBetTables = array_combine(Hash::extract($allBetTables, 'Mastercard.{n}.id'), Hash::extract($allBetTables, 'Mastercard.{n}.name'));
		$dsBetTables = array_combine(Hash::extract($allBetTables, 'Discover.{n}.id'), Hash::extract($allBetTables, 'Discover.{n}.name'));
		$amexBetTables = array_combine(Hash::extract($allBetTables, 'American Express.{n}.id'), Hash::extract($allBetTables, 'American Express.{n}.name'));
		$dbBetTables = array_combine(Hash::extract($allBetTables, 'Debit.{n}.id'), Hash::extract($allBetTables, 'Debit.{n}.name'));
		$ebtBetTables = array_combine(Hash::extract($allBetTables, 'EBT.{n}.id'), Hash::extract($allBetTables, 'EBT.{n}.name'));
		$debitAcquirers = $this->DebitAcquirer->find('list', array('fields' => array('debit_acquirers')));
		$gateways = $this->Gateway->find('list');
		$merchant = $this->Merchant->getSummaryMerchantData($reqData['MerchantPricing']['merchant_id']);
		$isEditLog = !empty(Hash::get($reqData, 'MerchantNote.0.loggable_log_id'));
		$userCanApproveChanges = ClassRegistry::init('MerchantChange')->userCanApproveChanges();
		return compact(
			'merchant', 'visaBetTables', 'mcBetTables', 'dsBetTables', 'amexBetTables',
			'dbBetTables', 'ebtBetTables', 'debitAcquirers', 'gateways', 'isEditLog', 'userCanApproveChanges');
	}

/**
 * getViewData
 *
 * @param string $id Merchant id
 * @return array merchant pricing variables for read mode view
 */
	public function getViewData($id) {
		/* Define array containing all active products, then update it from the view to contain
		 * only products not used by this merchant */
		$merchant = $this->getMerchantPricingByMerchantId($id);
		$enabledProds = [];
		$archivedProds = [];
		$disabledProducts = [];
		if ($this->rbacIsPermitted("app/actions/MerchantPricings/view/module/psModule1")) {
			$mProdId = Hash::extract($merchant, "ProductsAndService.{n}.products_services_type_id");

			$conditions = array('is_active' => true);
			if (!empty($mProdId)) {
				$conditions['id NOT IN'] = $mProdId;
				$enabledProds = Hash::combine($merchant, "ProductsAndService.{n}.id", "ProductsAndService.{n}.ProductsServicesType.products_services_description");
				$enabledProds = Hash::sort($enabledProds, "{s}", "asc", "natural");
			}
			$ProductsServicesType = ClassRegistry::init('ProductsServicesType');
			$disabledProducts = $ProductsServicesType->find('list', array(
				'conditions' => $conditions,
				'fields' => array('id', 'products_services_description')));
			$archivedProds = $this->Merchant->MerchantPricingArchive->listArchivedProducts($id);
		}
		$merchant['PaymentFusionFeatures'] = Hash::extract($merchant, 'PaymentFusion.PaymentFusionsProductFeature.{n}.ProductFeature.feature_name');
		$profitProjections = $this->Merchant->ProfitProjection->getGroupedByProductCategory($id);
		return compact('enabledProds', 'merchant', 'archivedProds', 'disabledProducts', 'profitProjections');
	}

/**
 * Retrive a list of all Merchant rate structures with the BetTable.id that the rate stucture is associated with as the key
 * and the Qualification Excemptions description as the value.
 *
 * @param int|string $id The merchant id
 * @return array 
 */
	public function getRateStructuresList($id) {
		return $this->find('list', [
			'fields' => [
				'RateStructure.bet_table_id',
				'RateStructure.qual_exemptions',
			],
			'conditions' => [
				'MerchantPricing.merchant_id' => $id,
			],
			'joins' => [
				[
					'table' => 'rate_structures',
					'alias' => 'RateStructure',
					'type' => 'INNER',
					'conditions' => [
						'OR' => [
							//Visa, MC and Discover use the same Rate Structures in most cases
							//therefore we only need to check for one in the conditions
							['RateStructure.bet_table_id = MerchantPricing.visa_bet_table_id'],
							['RateStructure.bet_table_id = MerchantPricing.amex_bet_table_id'],
						]
					]
				]
			]
		]);
	}
}
