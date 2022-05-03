<?php

App::uses('AppModel', 'Model');
App::uses('UserParameterType', 'Model');
App::uses('CommissionReport', 'Model');
App::uses('AddressType', 'Model');
App::uses('AxiaCalculate', 'Model');
App::uses('User', 'Model');

class UserCostsArchive extends AppModel {

	const AXIA_USER = 'Axia';
	const FIELD_MAP_NAME = "UserCostArchiveFieldMap";

/**
 * Prefixes alias for user Role joins
 * These prefixes are meant to be used to create aliases for join queries
 *
 * @var string
 */
	const JOIN_PREFIX_REP = 'Rep';
	const JOIN_PREFIX_SM = 'Manager';
	const JOIN_PREFIX_SM2 = 'Manager2';
	const JOIN_PREFIX_PARTNER = 'Partner';
	const JOIN_PREFIX_REFERER = 'Ref';
	const JOIN_PREFIX_RESELLER = 'Res';

/**
 * aliasPrefixes
 * The order of the array should not be altered but additional entires may be inserted as desired
 *
 * @var array
 */
	protected $_joinRolePrefixes = [
		self::JOIN_PREFIX_REP,
		self::JOIN_PREFIX_SM,
		self::JOIN_PREFIX_SM2,
		self::JOIN_PREFIX_PARTNER,
		self::JOIN_PREFIX_REFERER,
		self::JOIN_PREFIX_RESELLER,
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'per_item_cost' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'monthly_statement_cost' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
			'risk_assmnt_per_item' => array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			),
		);

/**
 * fieldMap
 * Array to use for archive field map
 *
 * @var array $name
 * @access public
 */
	public $fieldMap = [];

/**
 * queryMap
 *
 * When set, it will contain fully mapped query joins and corresponding fields both with models prefixed with user roles ready to use in a query.
 * The map will be build based on the configured blue print in the file pricing_archive_field_map_config
 * The data structure will be build as follows: 
 * [
 *	"Product A ID" => [
 *			'fields' => [<the fields with models prefixed with rolePrefixe>]
 *			'joins' => [<the joins with models prefixed with rolePrefixe>]
 *	 	]
 *	<  . . .  More products  . . >
 *	"Product Z ID" => [
 *			'fields' => [<the fields with models prefixed with rolePrefixe>]
 *			'joins' => [<the joins with models prefixed with rolePrefixe>]
 *	 	]
 *	 ]
 * Mapped queries in the array can be accessed directly and quickly O(1) using the the product id as key
 *
 * @var array $name
 * @access public
 */
	public $queryMap = [];

/**
 * queryMapMaxed
 * Set to true when the maximum possible queries have been added to $this->queryMap
 *
 * @var array $name
 * @access public
 */
	public $queryMapMaxed = false;

/**
 * maxAssocUcaRoles
 * Max possible merchant-associated users that could possibly have data in UserCostArchive excluding the rep
 *
 * @var array $name
 * @access public
 */
	public $assocUcaRoles = [
		User::ROLE_SM,
		User::ROLE_SM2,
		User::ROLE_PARTNER,
		User::ROLE_REFERRER,
		User::ROLE_RESELLER
	];

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'UserCostsArchive';

/**
 * _dsSpecialCases
 *
 * These user costs for these products archive conditionally based on special criteria
 *
 * @var array $_dsSpecialCases
 * @access protected
 */
	protected $_dsSpecialCases = ['Discover Settled Items', 'Discover Discount'];

/**
 * belongsTo association
 *
 * @var array $belongsTo
 * @access public
 */
	public $belongsTo = [
		'MerchantPricingArchive' => [
			'className' => 'MerchantPricingArchive',
			'foreignKey' => 'merchant_pricing_archive_id',
		],
		'Merchant' => [
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
		],
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
		'ProductsServicesType' => [
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
			'dependent' => false
		]
	];

/**
 * checkMerchantUserFields
 * Checks whether user compensa
 *
 * @param int $year The year to be archived
 * @return bool
 */
	public function checkMerchantUserFields($data) {
		$merchantId = $this->data['UserCostsArchive']['merchant_id'];
		$hasUserAssigned = $this->Merchant->hasAny([
			'id' => $merchantId,
			'OR' => [
				['user_id' => $data['user_id']],
				['sm_user_id' => $data['user_id']],
				['sm2_user_id' => $data['user_id']],
				['partner_id' => $data['user_id']],
				['referer_id' => $data['user_id']],
				['reseller_id' => $data['user_id']],
			]
		]);
		
		if ($hasUserAssigned === false) {
			$userName = $this->User->field('fullname', ['id' => $data['user_id']]);
			return "$userName is not assigned to this merchant! You must first assign that user to this merchant from the Overview page then try again";
		}
		return true;
	}
/**
 * constructor, initiates $this->queryMap so that it can be used throughout the lifetime of this class instantiation
 *
 * @param boolean $initUcaQueries datasource
 */
	public function __construct($id = false, $table = null, $ds = null, $initUcaQueries = false) {
		parent::__construct($id, $table, $ds);
		if ($initUcaQueries) {
			$this->initQueryMap();
		}
	}

/**
 * getCalculatedData
 *
 * @param array &$uCostDat user costs data
 * @param string $rolePrefix the prefix used as join alias and potentially found in the costs data
 * @return array of cost data
 */
	public function getCalculatedData(&$uCostDat, $rolePrefix) {
		$calcData['wireless_cost'] = Hash::get($uCostDat, 'MerchantPricing.wireless_auth_cost');
		//******* SET USER PERCENTAGE COST **************************//
		$calcData['cost_pct'] = Hash::get($uCostDat, $rolePrefix . 'Bet.pct_cost');
		//******* SET USER PERCENTAGE COST for AchRepCosts**************************//
		if (array_key_exists('rep_rate_pct', (array)Hash::get($uCostDat, $rolePrefix . 'AchRepCost'))) {
			$calcData['cost_pct'] = Hash::get($uCostDat, $rolePrefix . 'AchRepCost.rep_rate_pct');
		}
		//******* SET USER PERCENTAGE COST for GatewayCostStructure**************************//
		if (array_key_exists('rep_rate_pct', (array)Hash::get($uCostDat, $rolePrefix . 'GatewayCostStructure'))) {
			$calcData['cost_pct'] = Hash::get($uCostDat, $rolePrefix . 'GatewayCostStructure.rep_rate_pct');
		}
		//******* CALCULATE/SET USER PER ITEM COST ******************//
		$this->setUPiCost($uCostDat, $calcData, $rolePrefix);
		//******* CALCULATE/SET USER MONTLY COST A.K.A STATEMENT COST//
		$this->setUmonthlyCost($uCostDat, $calcData, $rolePrefix);
		return $calcData;
	}

/**
 * setUmonthlyCost - Sets the mohtly costs.
 * As required, only poducts named Credit, DEBIT, and EBT Monthly as well as any other products that have their own Models will archive monthly costs
 * as defined in the user costs field map for each of those products.
 *
 * @param array &$uCostDat user costs data
 * @param array &$calcData the prefix used as join alias and potentially found in the costs data
 * @param string $rolePrefix the prefix used as join alias and potentially found in the costs data
 * @return void
 */
	public function setUmonthlyCost(&$uCostDat, &$calcData, $rolePrefix) {
		$AxiaCalculate = new AxiaCalculate();
		//Set an initial value for monthly cost
		if (array_key_exists($rolePrefix . 'RepMonthlyCost', $uCostDat)) {
			$calcData['monthly_statement_cost'] = Hash::get($uCostDat, $rolePrefix . 'RepMonthlyCost.credit_cost');
			if (is_null($calcData['monthly_statement_cost'])) {
				$calcData['monthly_statement_cost'] = Hash::get($uCostDat, $rolePrefix . 'RepMonthlyCost.debit_cost');
			}
			if (is_null($calcData['monthly_statement_cost'])) {
				$calcData['monthly_statement_cost'] = Hash::get($uCostDat, $rolePrefix . 'RepMonthlyCost.ebt_cost');
			}

			//Set fields for calculation.
			//The following fields are optionally present in $uCostDat by the current product's fieldmap if they aren't present set then
			//$calcData['monthly_statement_cost'] will not be modified which is fine since for some products we only want that single value
			$calcData['total_month_wireless'] = Hash::get($uCostDat, 'MerchantPricing.total_wireless_term_cost');
			$calcData['gw_month_cost'] = Hash::get($uCostDat, $rolePrefix . 'GatewayCostStructure.rep_monthly_cost');

			//Modify monthly_statement_cost value with the result
			$AxiaCalculate->sumKeyValPairs($calcData, 'monthly_statement_cost', 'total_month_wireless', 'monthly_statement_cost', 'gw_month_cost');
			return;
		} elseif (array_key_exists('rep_monthly_cost', Hash::extract($uCostDat, $rolePrefix . 'RepProductSetting')) && array_key_exists('ProductSetting', $uCostDat)) {
			$moCostTerms['summand'] = Hash::get($uCostDat, $rolePrefix . 'RepProductSetting.rep_monthly_cost');
			$moCostTerms['multiplicand'] = Hash::get($uCostDat, $rolePrefix . 'RepProductSetting.provider_device_cost');
			$moCostTerms['multiplier'] = Hash::get($uCostDat, 'ProductSetting.gral_fee_multiplier'); //Merchant-assiciated ProductSetting field
			$calcData['monthly_statement_cost'] = $AxiaCalculate->multSumEqType1($moCostTerms, 'multiplicand', 'multiplier', 'summand');

		} elseif (array_key_exists('rep_monthly_cost', Hash::extract($uCostDat, $rolePrefix . 'AchRepCost'))) {
			//Check if this is AchRepCost
			$calcData['monthly_statement_cost'] = Hash::get($uCostDat, $rolePrefix . 'AchRepCost.rep_monthly_cost');
		} elseif (array_key_exists('rep_monthly_cost', Hash::extract($uCostDat, $rolePrefix . 'GatewayCostStructure'))) {
			//Check if this is GatewayCostStructure
			$calcData['monthly_statement_cost'] = Hash::get($uCostDat, $rolePrefix . 'GatewayCostStructure.rep_monthly_cost');
			//For gateway 1 product there is an additional rep monthly cost at the merchant level
			if (!empty(Hash::get($uCostDat, 'Gateway1'))) {
				$calcData['monthly_statement_cost'] += Hash::get($uCostDat, 'Gateway1.addl_rep_statement_cost');				
			}

		} elseif (array_key_exists('rep_monthly_cost', Hash::extract($uCostDat, $rolePrefix . 'PaymentFusionRepCost'))) {
			//Check if this is PaymentFusionRepCost
			$calcData['monthly_statement_cost'] = Hash::get($uCostDat, $rolePrefix . 'PaymentFusionRepCost.rep_monthly_cost');
		}
	}
/**
 * setUPiCost
 *
 * @param array &$uCostDat user costs data
 * @param array &$calcData the prefix used as join alias and potentially found in the costs data
 * @param string $rolePrefix the prefix used as join alias and potentially found in the costs data
 * @return void
 */
	public function setUPiCost(&$uCostDat, &$calcData, $rolePrefix) {
		$AxiaCalculate = new AxiaCalculate();
		//Products that require user per item costs calculation
		// Dial Sales dial_sales_cost
		//'Non Dial Sales' non_dial_sales_cost
		//'Sales', sales_cost This field should already be set and present in uCostDat acording to spec
		if (array_key_exists('sales_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
			$calcData['sales_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.sales_cost');
			$calcData['pi_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.pi_cost');
		} elseif (array_key_exists('non_dial_sales_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
			$calcData['sales_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.non_dial_sales_cost');
			$calcData['pi_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.pi_cost');
		} elseif (array_key_exists('dial_sales_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
			$calcData['sales_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.dial_sales_cost');
			$calcData['pi_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.pi_cost');
		}
		//Set UserCostsArchive.per_item_cost for Sales, Dial Sales, Non Dial Sales related products
		//The assumption is that given that the data that corresponds to one these products was set in $uCostDat using the field map
		//then these keys are assigned therefore the calculation will happen since all the keys are present
		$AxiaCalculate->sumKeyValPairs($calcData, 'sales_cost', 'pi_cost', 'per_item_cost', 'wireless_cost');

		//Check if UserCostsArchive.per_item_cost calculation was not set and if not then calculate per item cost for
		//'Authorizations', 'Dial Authorizations', 'Non Dial Authorizations' related products
		if (!array_key_exists('per_item_cost', $calcData)) {
			if (array_key_exists('auth_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
				$calcData['sales_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.auth_cost');
			} elseif (array_key_exists('dial_auth_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
				$calcData['sales_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.dial_auth_cost');
			} elseif (array_key_exists('non_dial_auth_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
				$calcData['sales_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.non_dial_auth_cost');
			}
			$AxiaCalculate->sumKeyValPairs($calcData, 'sales_cost', 'wireless_cost', 'per_item_cost');
		}

		//if UserCostsArchive.per_item_cost calculation was still not set then it needs to be set for
		//'Discount' and 'Settled Items' related products (NO CALCULATIONS NEEDED)
		if (!array_key_exists('per_item_cost', $calcData)) {
			if (array_key_exists('settlement_cost', (array)Hash::get($uCostDat, $rolePrefix . 'Bet'))) {
				$calcData['per_item_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.settlement_cost');
			} elseif (!empty(Hash::get($uCostDat, $rolePrefix . 'Bet.pi_cost'))) {
				$calcData['per_item_cost'] = Hash::get($uCostDat, $rolePrefix . 'Bet.pi_cost');
			}
		}
		//Set per_item_cost if is still not set using RepProductSetting
		if (!array_key_exists('per_item_cost', $calcData)) {
			if (array_key_exists("rep_per_item", (array)Hash::get($uCostDat, $rolePrefix . 'RepProductSetting'))) {
				$calcData['per_item_cost'] = Hash::get($uCostDat, $rolePrefix . 'RepProductSetting.rep_per_item');
			}
		}
		//Set per_item_cost if is still not set using RepProductSetting
		if (!array_key_exists('per_item_cost', $calcData)) {
			if (array_key_exists("rep_per_item", (array)Hash::get($uCostDat, $rolePrefix . 'PaymentFusionRepCost'))) {
				$calcData['per_item_cost'] = Hash::get($uCostDat, $rolePrefix . 'PaymentFusionRepCost.rep_per_item');
			}
		}
		//Set per_item_cost if is still not set using UserCostsModels
		if (!array_key_exists('per_item_cost', $calcData)) {
			if (array_key_exists("rep_per_item", (array)Hash::get($uCostDat, $rolePrefix . 'AchRepCost'))) {
				$calcData['per_item_cost'] = Hash::get($uCostDat, $rolePrefix . 'AchRepCost.rep_per_item');
			} elseif (array_key_exists("rep_per_item", (array)Hash::get($uCostDat, $rolePrefix . 'GatewayCostStructure'))) {
				$calcData['per_item_cost'] = Hash::get($uCostDat, $rolePrefix . 'GatewayCostStructure.rep_per_item');
			}
		}
	}

/**
 * setUserCostArchiveData
 * compile all data needed to create user cost archives for a given merchant id
 * Important: This method does not check if a merchant actually has a product. It assumes in the passed params
 *  that the merchant has the product.
 *
 * @param string $merchantId a merchant id
 * @param string $productId a product id
 * @param string $fieldMapName name of fieldMap to use in Configure::read("ArchiveFieldMap")
 * @return array
 * @throws \OutOfBoundsException
 */
	public function setUserCostArchiveData($merchantId, $productId, $fieldMapName) {
		$uCostDat = $this->getUserCostArchiveData($merchantId, $productId);
		if (count($uCostDat) === 1) {
			$uCostDat = $uCostDat[0];
		}

		if (empty($uCostDat)) {
			return [];
		}
		// refactor format logic into another function because this method is beeen called on multiple places
		return $this->formatUserCostArchiveData($uCostDat);
	}

/**
 * formatUserCostArchiveData
 * compile all data needed to create user cost archives for a given merchant id
 *
 * @param array $uCostData user cost data for one merchant
 * @return array
 * @throws OutOfBoundsException
 */
	public function formatUserCostArchiveData($uCostData) {
		$data = [];
		if (!empty($uCostData)) {
			//one archive per role
			$roleCnt = count($this->_joinRolePrefixes);
			//Check if array keys are numbers
			if (array_keys($uCostData) === range(0, count($uCostData) - 1)) {
				//there should only be one entry in the array
				$uCostData = Hash::get($uCostData, '0');
			}
			for ($x = 0; $x < $roleCnt; $x++) {
				if (array_key_exists("user_id", Hash::get($uCostData, $this->_joinRolePrefixes[$x] . 'CompProfile')) === false) {
					throw new OutOfBoundsException("UserCostsArchive::setUserCostArchiveData() expects the compensation profile user_id key to be present in the costs archive data structure but was not found for user {$this->_joinRolePrefixes[$x]}");
				}

				//Do not create data for current user if the user_id is empty. This means the CompProfile join returned empty due to a lack of user association with the merchant
				if (empty(Hash::get($uCostData, $this->_joinRolePrefixes[$x] . 'CompProfile.user_id'))) {
					continue;
				}

				//If this modeName corresponds to a referer then it is not a reseller modelName
				$isRef = ($this->_joinRolePrefixes[$x] === self::JOIN_PREFIX_REFERER);
				$isRes = ($this->_joinRolePrefixes[$x] === self::JOIN_PREFIX_RESELLER);

				$isHidden = false;
				if (Hash::get($uCostData, 'MerchantPricing.ds_user_not_paid') === true) {
					if (in_array($this->_joinRolePrefixes[$x], [self::JOIN_PREFIX_REP, self::JOIN_PREFIX_SM, self::JOIN_PREFIX_SM2])) {
						$u1stName = $this->User->field('user_first_name', ['id' => Hash::get($uCostData, 'Merchant.user_id')]);
						//never hide costs for user Axia
						if (strpos($u1stName, self::AXIA_USER) !== false && $this->_joinRolePrefixes[$x] === self::JOIN_PREFIX_REP) {
							$isHidden = false;
						} else {
							$isHidden = true;
						}
					} else {
						$isHidden = (Hash::get($uCostData, $this->_joinRolePrefixes[$x] . 'UserParameter.value') == false); //if not enabled then is hidden
					}
				}

				//Perform calculations only if costs arent hidden from user
				if (!$isHidden) {
					$calcData = $this->getCalculatedData($uCostData, $this->_joinRolePrefixes[$x]);
				}

				$data[] = [
						'merchant_id' => Hash::get($uCostData, 'Merchant.id'),
						'user_id' => Hash::get($uCostData, $this->_joinRolePrefixes[$x] . 'CompProfile.user_id'),
						'cost_pct' => ($isHidden)? null : Hash::get($calcData, 'cost_pct'),
						'per_item_cost' => ($isHidden)? null : Hash::get($calcData, 'per_item_cost'),
						'monthly_statement_cost' => ($isHidden)? null : Hash::get($calcData, 'monthly_statement_cost'),
						'risk_assmnt_pct' => ($isHidden)? null : Hash::get($uCostData, $this->_joinRolePrefixes[$x] . 'ProductsRisk.risk_assmnt_pct'),
						'risk_assmnt_per_item' => ($isHidden)? null : Hash::get($uCostData, $this->_joinRolePrefixes[$x] . 'ProductsRisk.risk_assmnt_per_item'),
						'ref_p_type' => ($isRef && !$isHidden)? $uCostData['Merchant']['ref_p_type'] : null,
						'ref_p_value' => ($isRef && !$isHidden)? $uCostData['Merchant']['ref_p_value'] : null,
						'ref_p_pct' => ($isRef && !$isHidden)? $uCostData['Merchant']['ref_p_pct'] : null,
						'res_p_type' => ($isRes && !$isHidden)? $uCostData['Merchant']['res_p_type'] : null,
						'res_p_value' => ($isRes && !$isHidden)? $uCostData['Merchant']['res_p_value'] : null,
						'res_p_pct' => ($isRes && !$isHidden)? $uCostData['Merchant']['res_p_pct'] : null,
						'is_hidden' => $isHidden
				];
			}
		}

		return $data;
	}

/**
 * getUserCostArchiveData
 * Compile all data needed to create user cost archives for a given merchant id.
 * The 
 *
 * @param string|array $merchantId a merchant if string or an index array of many merchant ids. When empty, the data for all merchants is obtained
 * @param string $productId required single product id string
 * @return array
 */
	public function getUserCostArchiveData($merchantId, $productId) {
		if (empty($productId)) {
			return [];
		}

		$conditions = [
			'Merchant.active' => true
		];
		if (!empty($merchantId)) {
			$conditions['Merchant.id'] = $merchantId;
		}
		$conditions['ProductsAndServices.products_services_type_id'] = $productId;
		$fields = $this->getMappedQuery('fields', $productId);
		$joins = $this->getMappedQuery('joins', $productId);

		$data = $this->Merchant->find('all', [
				'conditions' => $conditions,
				'fields' => $fields,
				'joins' => $joins,
			]);

		return $data;
	}

/**
 * setJoins compile all needed joins for archive
 *
 * @param string $productId a product id
 * @param string $fieldMapName name of fieldMap to use in Configure::read("ArchiveFieldMap")
 * @return array
 * @throws Exception
 */
	public function setJoins($productId, $fieldMapName) {
		//Check fieldMap class var is set
		if (empty($this->fieldMap)) {
			$this->_setFieldMap($productId, $fieldMapName);
		}
		$fieldMap = $this->fieldMap;
		$mgrRoleIds['sm_role_id'] = $this->User->Role->field('id', ['name' => User::ROLE_SM]);
		$mgrRoleIds['sm2_role_id'] = $this->User->Role->field('id', ['name' => User::ROLE_SM2]);
		//Set model aliases
		$rAlias = $this->_joinRolePrefixes[0] . 'CompProfile';
		$mAlias = $this->_joinRolePrefixes[1] . 'CompProfile';
		$m2Alias = $this->_joinRolePrefixes[2] . 'CompProfile';
		$pAlias = $this->_joinRolePrefixes[3] . 'CompProfile';
		$rfAlias = $this->_joinRolePrefixes[4] . 'CompProfile';
		$rsAlias = $this->_joinRolePrefixes[5] . 'CompProfile';
		$repCompCond = [
				"Merchant.user_id = $rAlias.user_id",
				//if there is no Merchant.partner_id then we want the default UCP otherwise we want the Partner-Rep UCP
				"$rAlias.is_default = (CASE WHEN Merchant.partner_id IS NULL THEN true ELSE false END)",
				//To get the Parter-Rep UCP that matches the Merchant.partner_id
				//When Merchant.partner_id IS null then left side condition will be false and the Right hand side will be true and we'll get the Rep's Default UCP
				//Otherwise when Merchant.partner_id is not null left side condition will be evaulated and Right hand side will be ignored thus obtaining the correct partner-rep comp profile.
				"($rAlias.partner_user_id = Merchant.partner_id OR Merchant.partner_id is NULL)"
			];

		/*First add user joins*/
		$userJoins = [
			[
				'table' => 'user_compensation_profiles',
				'alias' => $rAlias,
				'type' => 'INNER',
				'conditions' => $repCompCond
			],
			[
				'table' => 'user_compensation_profiles',
				'alias' => $mAlias,
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.sm_user_id = $mAlias.user_id",
					"$mAlias.role_id = '{$mgrRoleIds['sm_role_id']}'"
				]
			],
			[
				'table' => 'user_compensation_profiles',
				'alias' => $m2Alias,
				'type' => 'LEFT', // Not all users have a second manager
				'conditions' => [
					"Merchant.sm2_user_id = $m2Alias.user_id",
					"$m2Alias.role_id = '{$mgrRoleIds['sm2_role_id']}'"
				]
			],
			[
				'table' => 'user_compensation_profiles',
				'alias' => $pAlias,
				'type' => 'LEFT',
				'conditions' => [
					"Merchant.partner_id = $pAlias.user_id",
					"$pAlias.is_default = true"
				]
			],
			[
				'table' => 'user_compensation_profiles',
				'alias' => $rfAlias,
				'type' => 'LEFT', // some users of this type might not have a compensation profile
				'conditions' => [
					"Merchant.referer_id = $rfAlias.user_id",
					"$rfAlias.is_default = true"
				]
			],
			[
				'table' => 'user_compensation_profiles',
				'alias' => $rsAlias,
				'type' => 'LEFT', // some users of this type might not have a compensation profile
				'conditions' => [
					"Merchant.reseller_id = $rsAlias.user_id",
					"$rsAlias.is_default = true"
				]
			],
		];

		// Build user cost related models Joins for each users in the userJoins
		$assocJoins = [];
		$userCostsModels = Configure::read("UserCostsModels");
		foreach ($userJoins as $userJoin) {
			$rolePrefix = str_replace('CompProfile', '', $userJoin['alias']);
			foreach ($userCostsModels as $model) {
				$thisAlias = $rolePrefix . $model;
				$jConditions = Hash::extract($fieldMap, "joinConditions.$model");

				if (!empty($jConditions)) {
					$jConditions = $this->_replaceConditionsModelName($jConditions, $model, $thisAlias);
				} elseif ($model !== 'RepProductSetting' && $model !== 'PaymentFusionRepCost') {
					//Products that don't have $jConditions and are not any of the above models do not have a Bet.bet_table_id condition
					continue;
				}
				$jConditions[] = $thisAlias . '.user_compensation_profile_id = ' . $userJoin['alias'] . '.id';
				// add extra conditions based on the model
				switch ($model) {
					case 'AchRepCost':
						$jConditions[] = "$thisAlias.ach_provider_id = Ach.ach_provider_id";
						break;
					case 'Bet':
						if (count($jConditions) < 2) {
							throw new Exception('Missing "Bet.bet_table_id" condition');
						}
						$jConditions[] = "$thisAlias.bet_network_id = Merchant.bet_network_id";
						break;
					case 'RepMonthlyCost':
						$jConditions[] = "$thisAlias.bet_network_id = Merchant.bet_network_id";
						break;
					case 'RepProductSetting':
						$jConditions[] = "$thisAlias.products_services_type_id = '$productId'";
						break;
				}

				$assocJoins[] = [
						'table' => ClassRegistry::init($model)->table,
						'alias' => $thisAlias,
						'type' => 'LEFT', // some users might not have a compensation profile
						'conditions' => $jConditions
					];
			}
		}

		ClassRegistry::init('UserParameterType');
		$uPTypeId = UserParameterType::ENABLED_FOR_REP;
		//UserParameters are required for partner referer and reseler to handle the Discover Discount/Settle items special case
		$pParamAlias = "{$this->_joinRolePrefixes[3]}UserParameter";
		$rfParamAlias = "{$this->_joinRolePrefixes[4]}UserParameter";
		$rsParamAlias = "{$this->_joinRolePrefixes[5]}UserParameter";
		array_push($userJoins,
				[
					'table' => 'user_parameters',
					'alias' => $pParamAlias,
					'type' => 'LEFT',
					'conditions' => [
						"$pParamAlias.user_compensation_profile_id = $pAlias.id",
						"$pParamAlias.products_services_type_id = '$productId'",
						"$pParamAlias.user_parameter_type_id = '$uPTypeId'",
						"$pParamAlias.value = 1", // enabled
					]
				],
				[
					'table' => 'user_parameters',
					'alias' => $rfParamAlias,
					'type' => 'LEFT',
					'conditions' => [
						"$rfParamAlias.user_compensation_profile_id = $rfAlias.id",
						"$rfParamAlias.products_services_type_id = '$productId'",
						"$rfParamAlias.user_parameter_type_id = '$uPTypeId'",
						"$rfParamAlias.value = 1", // enabled
					]
				],
				[
					'table' => 'user_parameters',
					'alias' => $rsParamAlias,
					'type' => 'LEFT',
					'conditions' => [
						"$rsParamAlias.user_compensation_profile_id = $rsAlias.id",
						"$rsParamAlias.products_services_type_id = '$productId'",
						"$rsParamAlias.user_parameter_type_id = '$uPTypeId'",
						"$rsParamAlias.value = 1", // enabled
					]
				]
			);

		/*Prepend joins that must to be at the begining*/
		array_unshift($userJoins,
				[
					'table' => 'merchant_pricings',
					'alias' => 'MerchantPricing',
					'type' => 'LEFT',
					'conditions' => [
						"MerchantPricing.merchant_id = Merchant.id",
					]
				],
				[
					'table' => 'products_and_services',
					'alias' => 'ProductsAndServices',
					'type' => 'INNER', // the product MUST be assigned to the merchant
					'conditions' => [
						"ProductsAndServices.products_services_type_id = '$productId'",
						"ProductsAndServices.merchant_id = Merchant.id",
					]
				],
				[
					'table' => 'products_services_types',
					'alias' => 'ProductsServicesType',
					'type' => 'LEFT',
					'conditions' => [
						"ProductsServicesType.id = '$productId'",
					]
				],
				[
					'table' => 'merchant_uw_volumes',
					'alias' => 'MerchantUwVolume',
					'type' => 'LEFT',
					'conditions' => [
						'MerchantUwVolume.merchant_id = Merchant.id',
					],
				],
				[
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'LEFT',
					'conditions' => [
						'Address.merchant_id = Merchant.id',
						"Address.address_type_id = " . "'" . AddressType::BUSINESS_ADDRESS . "'",
					],
				],
				// needed for conditions on ach_rep_cost joins
				[
					'table' => 'aches',
					'alias' => 'Ach',
					'type' => 'LEFT',
					'conditions' => [
						'Ach.merchant_id = Merchant.id',
					],
				],
				[
					'table' => 'gateway1s',
					'alias' => 'Gateway1',
					'type' => 'LEFT',
					'conditions' => [
						'Gateway1.merchant_id = Merchant.id',
					],
				]
			);
		//merge all joins
		$joins = array_merge($userJoins, $assocJoins, Hash::extract($fieldMap, "joins"));

		return $joins;
	}

/**
 *	_replaceConditionsModelName
 *	Replaces the model name in the join conditions with the alias being used for that join.
 *	This function will search and replace in both keys (if string keys) and in the values of the array
 *	wherever the model name is found.
 *
 * @param array $conditions conditions for queries
 * @param string $modelName name of the model to replace with the new alias
 * @param string $alias string to use to replace the model name to use a unique alias
 * 						for that join. Must match the alias used for that same join
 * @return array
 */
	protected function _replaceConditionsModelName($conditions, $modelName, $alias) {
		$updated = [];
		foreach ($conditions as $key => $val) {
			if (is_numeric($key)) {
				$updated[$key] = str_replace($modelName . ".", $alias . ".", $val);
			} else {
				$updatedKey = str_replace($modelName . ".", $alias . ".", $key);
				$updatedVal = str_replace($modelName . ".", $alias . ".", $val);
				$updated[$updatedKey] = $updatedVal;
			}
		}

		return $updated;
	}

/**
 * _setFieldMap method
 * sets member var $this->fieldMap with fields needed to compile pricing archive data from Configure::read(<field map name>)
 *
 * @param string $productId a product id
 * @param string $fieldMapName name of fieldMap to use in Configure::read("ArchiveFieldMap")
 * @return void
 */
	protected function _setFieldMap($productId, $fieldMapName) {
		$prodDescription = ClassRegistry::init('ProductsServicesType')->getNameById($productId);
		$fieldMap = $this->MerchantPricingArchive->getArchiveFieldMap($prodDescription, $fieldMapName);

		if ($fieldMap === false) {
			return false;
		}

		//Modify fields with prefixes and set fielMap member variable
		$fieldMap['userCostsFields'] = $this->_setFieldsPrefixes(Hash::get($fieldMap, 'userCostsFields'));
		$this->fieldMap = $fieldMap;
	}

/**
 * _setFieldsPrefixes method
 * Prepends the Model Names in the $fields param with the prefixes defined in _joinRolePrefixes.
 * The resulting model aliases will consistently match the aliases used in the join queries needed to compile pricing archive data
 *
 * @param array $fields the fields must be in a single dimensional array in the 'Model.field' syntax
 * @return array containing sets of fields prefixed with the each prefix defined in self::$_joinRolePrefixes
 * @throws \InvalidArgumentException when a field is not in the Model.fiel syntax
 */
	protected function _setFieldsPrefixes($fields) {
		if (empty($fields)) {
			return $fields;
		}
		if (!is_array($fields)) {
			throw new InvalidArgumentException("Function expects a single-dimensional array but " . gettype($fields) . " was provided");
		}

		foreach ($this->_joinRolePrefixes as $prefix) {
			foreach ($fields as $field) {
				if (strpos($field, '.') === false) {
					throw new InvalidArgumentException("Function expects array argument elements to be in the 'ModelName.field_name' syntax");
				}
				$result[] = $prefix . $field;
			}
		}

		//Add UserParameters alias fields for partner, referer and reseller
		$result[] = self::JOIN_PREFIX_PARTNER . 'UserParameter.value';
		$result[] = self::JOIN_PREFIX_REFERER . 'UserParameter.value';
		$result[] = self::JOIN_PREFIX_RESELLER . 'UserParameter.value';

		return $result;
	}

/**
 * getUCAFieldData
 * Utility method to get data out of a specific user and field within the array that was generated using
 * self::setUserCostArchiveData() without having to iterate through the array
 *
 * @param array $userCostArchivedata data generated/returned by self::setUserCostArchiveData(). Caller must fist make that call to get the data
 * @param string $userId the id of the user for which the field value is to be extracted
 * @param string $fieldName the field name containing the data in question. (Must be a field that of UserCostsArchive)
 * @return mixed the value stored in that field or null
 * @throws \OutOfBoundsException
 */

	public function getUCAFieldData($userCostArchivedata, $userId, $fieldName) {
		if (empty($userId)) {
			return null;
		}
		foreach ($userCostArchivedata as $data) {
			if ($data['user_id'] === $userId) {
				return Hash::get($data, $fieldName);
			}
		}
		return null;
	}

/**
 * initQueryMap method
 * Initializes the User Cost query map.
 * The field map is built and set to the memeber variable $this->queryMap which can be accessed directly.
 *
 * @param array $products a single dimentional array containing products ids as values. If empty all products will be mapped
 * @return void
 */
	public function initQueryMap($products = []) {
		if (empty($products)) {
			$products = $this->MerchantPricingArchive->getArchivableProducts();
			$products = array_keys($products);
			$this->queryMapMaxed = true;
		}
		$requiredfields = [
			'Merchant.id',
			'Merchant.merchant_acquirer_id',
			'Merchant.merchant_dba',
			'Merchant.merchant_mid',
			'Merchant.user_id',
			'Merchant.sm_user_id',
			'Merchant.sm2_user_id',
			'Merchant.partner_id',
			'Merchant.reseller_id',
			'Merchant.referer_id',
			'Merchant.res_p_pct',
			'Merchant.res_p_value',
			'Merchant.res_p_type',
			'Merchant.ref_p_pct',
			'Merchant.ref_p_value',
			'Merchant.ref_p_type',
			'MerchantUwVolume.average_ticket',
			'MerchantUwVolume.mo_volume',
			'MerchantUwVolume.sales',
			'MerchantUwVolume.visa_volume',
			'MerchantUwVolume.mc_volume',
			'MerchantUwVolume.ds_volume',
			'MerchantUwVolume.amex_volume',
			'MerchantUwVolume.amex_avg_ticket',
			'MerchantUwVolume.pin_debit_volume',
			'MerchantUwVolume.pin_debit_avg_ticket',
			'Ach.ach_expected_annual_sales',
			'Ach.ach_average_transaction',
			'Gateway1.gw1_monthly_volume',
			'Gateway1.gw1_monthly_num_items',
			'Address.address_state'
		];
		foreach ($products as $productId) {
			$this->_setFieldMap($productId, self::FIELD_MAP_NAME);
			$this->queryMap[$productId] = [
				'fields' => array_merge($requiredfields, Hash::get($this->fieldMap, 'fields'), Hash::get($this->fieldMap, 'userCostsFields')),
				'joins' => $this->setJoins($productId, self::FIELD_MAP_NAME)
			];
		}
	}

/**
 * getMappedQuery
 * Returns the mapped query fields|joins in member variable $this->queryMap.
 * if $this->queryMap has not been initialized this method calls Self::initQueryMap()
 *
 * @param string $key only two keys accepted 'fields' or 'joins'
 * @param string $productId a single product id used to access the array mapped 
 * @return array
 */
	public function getMappedQuery($key, $productId) {
		if (empty($this->queryMap[$productId])) {
			$this->initQueryMap([$productId]);
		}
		return $this->queryMap[$productId][$key];
	}
}
