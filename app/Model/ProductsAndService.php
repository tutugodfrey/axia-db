<?php

App::uses('AppModel', 'Model');
App::uses('ProductsServicesType', 'Model');

/**
 * ProductsAndService Model
 *
 * @property Merchant $Merchant
 * @property ProductsServicesType $ProductsServicesType
 */
class ProductsAndService extends AppModel {

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = [
		'Search.Searchable',
	];

/**
 * Find Method
 *
 * @var array
 */
	public $findMethods = [
		'merchantProducts' => true,
	];

/**
 * Filters
 *
 * @var array
 */
	public $filterArgs = [
		'entity_id' => [
			'type' => 'value',
			'field' => 'Merchant.entity_id'
		],
		'm_active_toggle' => [
			'type' => 'value',
			'field' => 'Merchant.active'
		],
		'dba_mid' => [
			'type' => 'query',
			'method' => 'orConditions',
		],
		'products_services_type_id' => [
			'type' => 'query',
			'method' => 'productConditions'
		]
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			)
		),
		'products_services_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant',
		'ProductsServicesType'
	);

/**
 * productConditions
 * Builds the query conditions to be used when searching by products in the reports
 * using a filter built with the options provided by the self->buildDdOptions(boolean) method
 * 
 * @param string $fiterArg data passed during search
 * @return void
 */
	public function productConditions($fiterArg = []) {
		if ($fiterArg['products_services_type_id'] === ProductsServicesType::ALL_LEGACY_OPTN) {
			return [
				"ProductsAndService.products_services_type_id IN (SELECT id from products_services_types where is_legacy = true)"
			];
		} elseif ($this->isValidUUID($fiterArg['products_services_type_id'])) {
			return ["ProductsAndService.products_services_type_id" => $fiterArg['products_services_type_id']];
		}
	}

/**
 * orConditions for merchant dba/mid search
 *
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function orConditions($data = array()) {
		$data['search'] = $data['dba_mid'];
		$cond = $this->Merchant->orConditions($data);
		return $cond;
	}
/**
 * getProductsAndServicesByMID
 * Gets all the products a merchant has
 *
 * @param string $id merchant id
 * @return array
 */
	public function getProductsAndServicesByMID($id) {
		return $this->find('all', array(
			'fields' => array(
				'ProductsServicesType.id',
				'ProductsServicesType.products_services_description',
				'ProductsServicesType.is_active'
			),
			'order' => array('ProductsServicesType.products_services_description ASC'),
			'joins' => array(
				array(
					'table' => 'products_services_types',
					'alias' => 'ProductsServicesType',
					'type' => 'INNER',
					'conditions' => array(
						'ProductsServicesType.id = ProductsAndService.products_services_type_id',
						'ProductsAndService.merchant_id' => $id
					)
				)
			)
		));
	}

/**
 * hasProduct 
 * Finds out whether a merchant already has a product.
 * If data is found about a centain product the product will be reactivated.
 *
 * @param string $merchantId a merchant id
 * @param string $productId a product id
 * @return boolean
 */
	public function hasProduct($merchantId, $productId) {
		$Model = $this->ProductsServicesType->initProductModel($productId);
		$conditions = ['merchant_id' => $merchantId, 'products_services_type_id' => $productId];

		$thisHasIt = $this->hasAny($conditions);
		if ($Model === false) {
			return $thisHasIt;
		} else {
			//Some models do not have this foreing key
			if (!$Model->hasField('products_services_type_id')) {
				unset($conditions['products_services_type_id']);
			}
			$otherModelHasIt = $Model->hasAny($conditions);
			//If a record exists in the other $Model but not in $this one.
			//The merchant has the product but data isn't syncronized
			if ($otherModelHasIt === true && $thisHasIt === false) {
				$this->create();
				$this->save(['merchant_id' => $merchantId, 'products_services_type_id' => $productId]);
				return true;
			}
		}
		return ($otherModelHasIt && $thisHasIt);
	}

/**
 * init method
 * Saves initial product data for products that have their own Models and returns the url of that product so the user can add data through the view
 *
 * @param string $merchantId a merchant id
 * @param string $productId a product id
 * @return string url
 * @throws Exception
 */
	public function add($merchantId, $productId) {
		$defaultUrl = Router::url(['controller' => 'MerchantPricings', 'action' => 'products_and_services', $merchantId]);
		//check if there is any existing product data
		if ($this->hasProduct($merchantId, $productId)) {
			return $defaultUrl;
		}
		$hasMerchantPricing = $this->Merchant->MerchantPricing->hasAny(array('merchant_id' => $merchantId));
		$Model = $this->ProductsServicesType->initProductModel($productId);
		//If the product doess not have its own model and merchant does not have any MerchantPricing
		//Then we can assume this product is part or MerchantPricing since all other products without models are part of MerchantPricing.
		if ($Model === false && $hasMerchantPricing === false) {
			$Model = $this->Merchant->MerchantPricing;
		}
		$data = [
			'merchant_id' => $merchantId,
			'products_services_type_id' => $productId,
		];
		$dataSource = $this->getDataSource();
		//Create record for $this model
		$dataSource->begin();
		$this->create();
		if ($this->save($data) === false) {
			$dataSource->rollback();
			$errors = $this->_validationErrorsString($this);
			throw new Exception("Could not save {$this->alias} $errors");
		}
		//Update MerchantCardType as needed
		if ($this->Merchant->MerchantCardType->updateWithProductId($merchantId, $productId) === false) {
			$dataSource->rollback();
			throw new Exception("Error: failed to update merchant card types! Try again.");
		}
		if ($Model !== false) {
			if ($Model->alias === "Gateway1") {
				//set initial gateway of no gateway
				$data['gateway_id'] = $Model->Gateway->field('id', ['name' => 'No Gateway']);
				if ($data['gateway_id'] === false) {
					throw new Exception("Error creating initial product data, could not set {$Model->alias}.gateway_id");
				}
			}
			$Model->create();

			if ($Model->save($data, false) === false) {
				$dataSource->rollback();
				$errors = $this->_validationErrorsString($Model);
				throw new Exception("Failed to save initial {$Model->alias} data. $errors");
			}
			$dataSource->commit();
			$id = $Model->id;

			$controllers = App::objects('controller');
			if (in_array(Inflector::pluralize($Model->alias) . 'Controller', $controllers)) {
				$controller = Inflector::pluralize($Model->alias);
				$url = Router::url(['controller' => $controller, 'action' => 'edit', $id]);
			} else {
				$url = $defaultUrl;
			}
		} else {
			$dataSource->commit();
			return $defaultUrl;
		}
		return $url;
	}

/**
 * validationErrorsString
 *
 * @param Model $Model the model to check for validation errors
 * @return string of errors separated by periods.
 */
	protected function _validationErrorsString(Model $Model) {
		if (!empty(Hash::extract($Model->validationErrors, "{s}.{n}"))) {
			$errors = join(Hash::extract($Model->validationErrors, "{s}.{n}"), ". ");
		} else {
			$errors = '';
		}
		return $errors;
	}

/**
 * Return the variables for the report view
 *
 * @return array
 */
	public function getReportViewData() {
		$productsServicesTypes = $this->ProductsServicesType->buildDdOptions(true);
		$entities = $this->Merchant->Entity->getList();
		return compact('productsServicesTypes', 'entities');
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
	protected function _findMerchantProducts($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = [
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Client.client_id_global',
				'((' . $this->Merchant->User->getFullNameVirtualField('User') . ')) AS User__full_name',
				'UwStatusMerchantXref.datetime',
				'MerchantPricing.ds_user_not_paid',
				//Aggregated categories
				'string_agg("VisaProduct"."products_services_description", \', \' ORDER BY "VisaProduct"."products_services_description" ASC)  AS "VisaProduct__names"',
				'string_agg("MasterCardProduct"."products_services_description", \', \' ORDER BY "MasterCardProduct"."products_services_description" ASC)  AS "MasterCardProduct__names"',
				'string_agg("DiscoverProduct"."products_services_description", \', \' ORDER BY "DiscoverProduct"."products_services_description" ASC)  AS "DiscoverProduct__names"',
				'string_agg("AmexProduct"."products_services_description", \', \' ORDER BY "AmexProduct"."products_services_description" ASC)  AS "AmexProduct__names"',
				'string_agg("DebitProduct"."products_services_description", \', \' ORDER BY "DebitProduct"."products_services_description" ASC)  AS "DebitProduct__names"',
				'string_agg("EbtProduct"."products_services_description", \', \' ORDER BY "EbtProduct"."products_services_description" ASC)  AS "EbtProduct__names"',
				'string_agg("OtherProduct"."products_services_description", \', \' ORDER BY "OtherProduct"."products_services_description" ASC)  AS "OtherProduct__names"',
				'VisaBet.name',
				'MasterCardBet.name',
				'DiscoverBet.name',
				'AmexBet.name',
				'DebitBet.name',
				'EbtBet.name',
				'((regexp_replace("Entity"."entity_name", \'[a-z \s]+\', \'\', \'g\'))) AS "Entity__entity_acronym"'
			];
			$query['joins'] = [
				[
					'table' => 'merchants',
					'alias' => 'Merchant',
					'type' => 'left',
					'conditions' => [
						'Merchant.id = ProductsAndService.merchant_id'
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
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'left',
					'conditions' => [
						'Merchant.entity_id = Entity.id'
					],
				],
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => [
						'Merchant.user_id = User.id'
					],
				],
				[
					'table' => 'uw_status_merchant_xrefs',
					'alias' => 'UwStatusMerchantXref',
					'type' => 'left',
					'conditions' => [
						'Merchant.id = UwStatusMerchantXref.merchant_id',
						"UwStatusMerchantXref.uw_status_id = (SELECT id from uw_statuses where name ='Approved')",

					],
				],
				[
					'table' => 'merchant_pricings',
					'alias' => 'MerchantPricing',
					'type' => 'left',
					'conditions' => [
						'MerchantPricing.merchant_id = Merchant.id'
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'VisaProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = VisaProduct.id',
						"VisaProduct.products_services_description ilike 'Visa%'",
						"VisaProduct.is_active = true"
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'MasterCardProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = MasterCardProduct.id',
						"MasterCardProduct.products_services_description ilike 'MasterCard%'",
						"MasterCardProduct.is_active = true"
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'DiscoverProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = DiscoverProduct.id',
						"DiscoverProduct.products_services_description ilike 'Discover%'",
						"DiscoverProduct.is_active = true"
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'AmexProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = AmexProduct.id',
						"AmexProduct.products_services_description ilike 'American Express%'",
						"AmexProduct.is_active = true"
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'DebitProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = DebitProduct.id',
						"DebitProduct.products_services_description ilike 'Debit%'",
						"DebitProduct.is_active = true"
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'EbtProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = EbtProduct.id',
						"EbtProduct.products_services_description like 'EBT%'",
						"EbtProduct.is_active = true"
					],
				],
				[
					'table' => 'products_services_types',
					'alias' => 'OtherProduct',
					'type' => 'left',
					'conditions' => [
						'ProductsAndService.products_services_type_id = OtherProduct.id',
						"OtherProduct.products_services_description NOT ilike 'Visa%'",
						"OtherProduct.products_services_description NOT ilike 'MasterCard%'",
						"OtherProduct.products_services_description NOT ilike 'Discover%'",
						"OtherProduct.products_services_description NOT ilike 'American Express%'",
						"OtherProduct.products_services_description NOT ilike 'Debit%'",
						"OtherProduct.products_services_description NOT like 'EBT%'",
						"OtherProduct.is_active = true"
					],
				],
				[
					'table' => 'bet_tables',
					'alias' => 'VisaBet',
					'type' => 'left',
					'conditions' => [
						'VisaBet.id = MerchantPricing.visa_bet_table_id'
					],
				],
				[
					'table' => 'bet_tables',
					'alias' => 'MasterCardBet',
					'type' => 'left',
					'conditions' => [
						'MasterCardBet.id = MerchantPricing.mc_bet_table_id'
					],
				],
				[
					'table' => 'bet_tables',
					'alias' => 'DiscoverBet',
					'type' => 'left',
					'conditions' => [
						'DiscoverBet.id = MerchantPricing.ds_bet_table_id'
					],
				],
				[
					'table' => 'bet_tables',
					'alias' => 'AmexBet',
					'type' => 'left',
					'conditions' => [
						'AmexBet.id = MerchantPricing.amex_bet_table_id'
					],
				],
				[
					'table' => 'bet_tables',
					'alias' => 'DebitBet',
					'type' => 'left',
					'conditions' => [
						'DebitBet.id = MerchantPricing.db_bet_table_id'
					],
				],
				[
					'table' => 'bet_tables',
					'alias' => 'EbtBet',
					'type' => 'left',
					'conditions' => [
						'EbtBet.id = MerchantPricing.ebt_bet_table_id'
					],
				],
			];
			$query['group'] = [
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Client.client_id_global',
				$this->Merchant->User->getFullNameVirtualField('User'),
				'UwStatusMerchantXref.datetime',
				'MerchantPricing.ds_user_not_paid',
				'VisaBet.name',
				'MasterCardBet.name',
				'DiscoverBet.name',
				'AmexBet.name',
				'DebitBet.name',
				'EbtBet.name',
				'Entity.entity_name'
				];
			return $query;
		}

		return $results;
	}
}
