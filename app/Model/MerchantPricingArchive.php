<?php

App::uses('AppModel', 'Model');
App::uses('AxiaCalculate', 'Model');
App::uses('UserCostsArchive', 'Model');

class MerchantPricingArchive extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'MerchantPricingArchive';

/**
 * Member var used to track long running background processes using CakeResque plugin or similar.
 *
 * @var string $bgJobTrackerId
 * @access public
 */
	public $bgJobTrackerId = null;

/**
 * Member var used to pass the result of the completion of a backgrounded archive process to the email event
 *
 * @var string $name
 * @access public
 */
	public $bgArchiveResult = [
		'job_name' => 'Merchant Pricing Archive',
		'result' => false,
		'start_time' => null, // date time format yyyy-mm-dd hh::mm:ss
		'end_time' => null, // date time format yyyy-mm-dd hh::mm:ss
		'log' => [
			'products' => [], //single dimentional array of products selected for archive
			'errors' => [], //single dimentional indexed array errors
			'optional_msgs' => [], //single dimentional indexed array of additional optional messages
		]
	];

/**
 * virtualFuels
 *
 * @var string $name
 * @access public
 */
	public $virtualFields = array(
		'month_year' => "CONCAT(to_char(to_timestamp(MerchantPricingArchive.month::text, 'MM'), 'FMMonth'), ' ', MerchantPricingArchive.year)"
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'year' => [
			'numeric' => [
				'rule' => ['numeric'],
				'message' => 'Please select a year',
				'allowEmpty' => false,
				'required' => true,
			]
		],
		'archive_year' => [
			'numeric' => [
				'rule' => ['numeric'],
				'message' => 'Please select a year',
				'allowEmpty' => false,
				'required' => true,
			],
		],
		'archive_month' => [
			'numeric' => [
				'rule' => ['numeric'],
				'message' => 'Please select a month',
				'allowEmpty' => false,
				'required' => true,
			],
		],
		'products' => [
			'rule' => ['multiple', ['min' => 1]],
			'message' => 'You have to choose at least one product',
			'required' => true,
		]
	];

/**
 * belongsTo association
 *
 * @var array $belongsTo
 * @access public
 */
	public $belongsTo = [
		'Merchant' => [
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
			'dependent' => false
		],
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => false
		],
		'ProductsServicesType' => [
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
			'dependent' => false
		]
	];

/**
 * hasMany association
 *
 * @var array $hasMany
 * @access public
 */
	public $hasMany = [
		'UserCostsArchive' => [
			'className' => 'UserCostsArchive',
			'foreignKey' => 'merchant_pricing_archive_id',
			'dependent' => true, //Delete UserCostArchives when $this' records are deleted
		]
	];

/**
 * Find Method
 *
 * @var array
 */
	public $findMethods = [
		'allPricing' => true,
	];

/**
 * setEditModeValidation
 *
 * @return void
 */
	public function setEditModeValidation() {
		//Remove preset rules since they only apply for creating new archives
		$this->validate = [];

		//Set edit mode rules
		$this->validator()
			->add('m_per_item_fee', array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			))
			->add('m_statement_fee', array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			))
			->add('m_discount_item_fee', array(
				'validAmount' => array(
					'rule' => 'validAmount',
					'message' => ' must be a valid amount number',
					'allowEmpty' => true
				)
			));
		$this->UserCostsArchive->validator()
			->add('user_id', array(
				'isUserAssignedToMerchant' => array(
					'rule' => 'checkMerchantUserFields',
					'allowEmpty' => true
				)
			));
	}

/**
 * archiveExists
 *
 * @param int $year The year to be archived
 * @param int $month the month to be archived
 * @param array $producsToArchive array of strings representing the name(s) of the archives to generate
 * @throws InvalidArgumentException
 * @return bool
 */
	public function archiveExists($year, $month, $producsToArchive = []) {
		if ((empty($producsToArchive) || empty($year) || empty($month)) || (!is_numeric($year) || !is_numeric($month))) {
			throw new InvalidArgumentException('Missing or invalid argument type in archiveExists.');
		}

		return $this->hasAny([
			'year =' => $year,
			'month' => $month,
			'products_services_type_id' => $producsToArchive
		]);
	}
/**
 * checkExistingArchiveByMonthYear method
 *
 * @param int $year (required)
 * @param int $month month
 * @throws InvalidArgumentException
 * @return array()
 */
	public function checkExistingArchiveByMonthYear($year = null, $month = null) {
		if (!is_numeric($year) || empty($year)) {
			throw new InvalidArgumentException('Function checkExistingArchiveByMonthYear requires the first integer argument but ' . gettype($year) . ' was provided.');
		}
		$conditions = (!empty($month)) ? ['year =' => $year, 'month' => $month] : ['year =' => $year];
		/* Set search settings */
		$settings = [
				'recursive' => -1,
				'fields' => ['DISTINCT (MerchantPricingArchive.month)'],
				'order' => ['MerchantPricingArchive.month', 'ProductsServicesType.products_services_description'],
				'conditions' => $conditions,
				'contain' => ['ProductsServicesType' => 'products_services_description']
				];
		/* Find data */
		$data = $this->find('all', $settings);
		return $data;
	}

/**
 * setMpArchiveData savesAssociated pricing archive data
 *
 * @param string $merchantId merchant id
 * @param string $productId products id
 * @param int $month month
 * @param int $year year
 * @param int $userEmail email address
 * @throws InvalidArgumentException
 * @return bool
 */
	public function setMpArchiveData($merchantId, $productId, $month, $year, $userEmail) {
		if ((empty($productId) || empty($merchantId) || empty($year) || empty($month)) || (!is_numeric($year) || !is_numeric($month))) {
			throw new InvalidArgumentException('Missing or invalid argument type in setMpArchiveData.');
		}

		$data = $this->getPricingArchiveData($merchantId, $productId, $userEmail, 'PricingArchiveFieldMap');
		if (empty($data['Merchant']['id'])) {
			return [];
		}

		$AxiaCalculate = new AxiaCalculate();

		//dscnt_proc_rate is an aliased field optionally present in $data and set depending on the current product's fieldmap, if it isn't present set then
		//m_rate_pct will not be modified/set which is fine since for some products we may or may not want m_rate_pct value to be arhived
		//This implicit logic depends on what is defined is the pricing archive fieldMap config for each product.
		$AxiaCalculate->sumKeyValPairs($data['NewArchiveData'], 'dscnt_proc_rate', 'bet_extra_pct', 'm_rate_pct');

		return [
			'MerchantPricingArchive' => [
				'merchant_id' => Hash::get($data, 'Merchant.id'),
				'user_id' => Hash::get($data, 'Merchant.user_id'),
				'acquirer_id' => Hash::get($data, 'NewArchiveData.acquirer_id'),
				'products_services_type_id' => $productId,
				'month' => $month,
				'year' => $year,
				'm_rate_pct' => Hash::get($data, 'NewArchiveData.m_rate_pct'),
				'original_m_rate_pct' => Hash::get($data, 'NewArchiveData.original_m_rate_pct'),
				'bet_extra_pct' => Hash::get($data, 'NewArchiveData.bet_extra_pct'),
				'm_per_item_fee' => Hash::get($data, 'NewArchiveData.m_per_item_fee'),
				'm_discount_item_fee' => Hash::get($data, 'NewArchiveData.m_discount_item_fee'),
				'm_statement_fee' => Hash::get($data, 'NewArchiveData.m_statement_fee'),
				'bet_table_id' => Hash::get($data, 'NewArchiveData.bet_table_id'),
				'bet_network_id' => Hash::get($data, 'Merchant.bet_network_id'),
				'months_processing' => $this->getNumOfMonthsSince(Hash::get($data, 'Merchant.active_date')),
				'interchange_expense' => Hash::get($data, 'NewArchiveData.interchange_expense'),
				'product_profit' => Hash::get($data, 'NewArchiveData.product_profit'),
				'provider_id' => Hash::get($data, 'NewArchiveData.provider_id'),
				'gateway_mid' => Hash::get($data, 'NewArchiveData.gateway_mid'),
				'generic_product_mid' => Hash::get($data, 'NewArchiveData.generic_product_mid')
			]
		];
	}

/**
 * saveNewArchives savesAssociated pricing archive data
 *
 * @param array $prodIds products id
 * @param int $month month
 * @param int $year year
 * @param int $userEmail email address
 * @param int $bgJobTrackerId the BackgroundJob.id used to track this process if being processed separate from the clident using CakeResque
 * @throws InvalidArgumentException
 * @throws Exception
 * @return bool
 */
	public function saveNewArchives($prodIds, $month, $year, $userEmail, $bgJobTrackerId = null) {
		try {
			$this->bgJobTrackerId = $bgJobTrackerId;
			$dataSource = $this->getDataSource();
			$dataSource->begin();
			if ((empty($prodIds) || empty($year) || empty($month)) || (!is_numeric($year) || !is_numeric($month))) {
				throw new InvalidArgumentException('Missing or invalid argument for function saveNewArchives.');
			}
			
			$this->bgArchiveResult['start_time'] = date('Y-m-d H:i:s');
			$this->validator()->remove('products');
			$this->validator()->remove('archive_month');
			$this->validator()->remove('archive_year');
			$UserCostsArchive = new UserCostsArchive(false, null, null, true); //instantiate UserCostsArchive->queryMap member var
			foreach ($prodIds as $pId) {
				/*Get Merchants who have current product*/
				$merchantIds = $this->Merchant->getListByProductId($pId, false, 1);
				/* Data for active Merchants who have current product*/
				$allMerchantsResults = $UserCostsArchive->getUserCostArchiveData(null, $pId);
				// add merchant Id to the array keys to have direct access while iterating the exising merchants
				$allMerchantsResults = Hash::combine($allMerchantsResults, '{n}.Merchant.id', '{n}');
				foreach ($merchantIds as $mrchntId => $val) {
					$newArchiveData = $this->setMpArchiveData($mrchntId, $pId, $month, $year, $userEmail);
					$ucaDat = $UserCostsArchive->formatUserCostArchiveData(Hash::get($allMerchantsResults, $mrchntId, []));
					if (!empty($ucaDat)) {
						$newArchiveData['UserCostsArchive'] = $ucaDat;
					}
					if (!empty(Hash::get($newArchiveData, 'MerchantPricingArchive.merchant_id'))) {
						if ($this->saveAssociated($newArchiveData) === false) {
							throw new Exception("Failed to save Pricing Archive data");
						}
					}
				}
			}
			$dataSource->commit();
		} catch (Exception $e) {
			$dataSource->rollback();
			$this->bgArchiveResult['log']['errors'][] = $e->getMessage();
		} catch (InvalidArgumentException $e) {
			$dataSource->rollback();
			$this->bgArchiveResult['log']['errors'][] = $e->getMessage();
		} catch (OutOfBoundsException $e) {
			$dataSource->rollback();
			$this->bgArchiveResult['log']['errors'][] = $e->getMessage();
		} finally {
			if (isset($e)) {
				$this->bgArchiveResult['result'] = false;
			} else {
				$this->bgArchiveResult['result'] = true;
			}
		}
		$this->bgArchiveResult['log']['products'] = $this->ProductsServicesType->find('list', ['conditions' => ['id' => $prodIds]]);
		$this->bgArchiveResult['log']['optional_msgs'][] = __("Selected month/year: $month/$year");
		$this->bgArchiveResult['end_time'] = date('Y-m-d H:i:s');
		$this->sendCompletionSatusEmail($this->bgArchiveResult, $userEmail);
	}

/**
 * getArchiveFieldMap gets fields needed to compile pricing archive data from Configure::read("ArchiveFieldMap")
 *
 * @param string $prodDescription description of the product for which to get the field map
 * @param string $fieldMapName name of fieldMap to use in Configure::read("ArchiveFieldMap")
 * @return mixed returns false if fieldMapName and prodDescription are valid but no field map is found otherwise the field map array
 * @throws \Exception if fieldMapName is not found for product
 */
	public function getArchiveFieldMap($prodDescription, $fieldMapName) {
		if (empty(Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.$fieldMapName"))) {
			throw new Exception("Field Map named $fieldMapName does not exist in pricing archive field map config");
		}

		if (!in_array($prodDescription, Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}"))) {
			throw new Exception("Field Map for $prodDescription does not exist in pricing archive field map config");
		}

		foreach (Configure::read("ArchiveFieldMap") as $fieldMap) {
			if (in_array($prodDescription, $fieldMap['CommonDataProductNames'])) {
				return $fieldMap[$fieldMapName];
			}
		}
		//No field map found for given product description
		return false;
	}

/**
 * getPricingArchiveData compile all data needed to create merchant pricing archives for a given merchant id
 *
 * @param string $merchantId a merchant id
 * @param string $productId a product id
 * @param string $userEmail a user email
 * @param string $fieldMapName the name of the fieldMap to use to retrieve data
 * @throws \NotFoundException
 * @return array
 */
	public function getPricingArchiveData($merchantId, $productId, $userEmail, $fieldMapName) {
		/*Get current product's description*/
		$prodDescription = $this->ProductsServicesType->field('products_services_description', ['id' => $productId]);
		if ($prodDescription === false) {
			$this->bgArchiveResult['result'] = false;
			$this->bgArchiveResult['log']['errors'][] = 'Product Not Found in Database!';
			$this->bgArchiveResult['end_time'] = date('Y-m-d H:i:s');
			$this->sendCompletionSatusEmail($this->bgArchiveResult, $userEmail);
			throw new NotFoundException("Product Not Found in Database!");
		}

		/*Get Field Map for this product*/
		$archiveModelsFieldsMap = $this->getArchiveFieldMap($prodDescription, $fieldMapName);

		if ($archiveModelsFieldsMap === false) {
			return [];
		}

		$requiredfields = [
			'Merchant.id',
			'Merchant.user_id',
			'Merchant.partner_id',
			'Merchant.active_date'
		];

		$fields = array_merge($requiredfields, Hash::extract($archiveModelsFieldsMap, 'sourceModels.{s}.{n}'));
		try {
			$joins = $this->setJoins($prodDescription, $userEmail, $fieldMapName);
		} catch(NotFoundException $e) {
			$this->bgArchiveResult['result'] = false;
			$this->bgArchiveResult['log']['errors'][] = $e->getMessage();
			$this->bgArchiveResult['end_time'] = date('Y-m-d H:i:s');
			$this->sendCompletionSatusEmail($this->bgArchiveResult, $userEmail);
			//rethrow exception again to end process
			throw $e;
		}

		$conditions = ['Merchant.id' => $merchantId, 'Merchant.active' => 1];

		$pClassId = $this->ProductsServicesType->field('class_identifier', ['id' => $productId]);
		$modelName = Configure::read("App.productClasses.$pClassId.className");
		//If the product model specified in Config is ProductSetting add condition since merchant hasMany ProductSetting
		if ($modelName === Configure::read("App.productClasses.p_set.className")) {
			$conditions['ProductsServicesType.id'] = $productId;
		}

		$data = $this->Merchant->find('first', [
			'conditions' => $conditions,
			'fields' => $fields,
			'joins' => $joins
		]);

		//Insert the product description to use later on
		$data['ProductsServicesType']['products_services_description'] = $prodDescription;
		return $data;
	}

/**
 * sendCopletionSatusEmail method
 *
 * Triggers an email even to the user who initiated the archiving process whether the mass archive was successfull
 *
 * @param string $prodDescription a product description that matches the desciption in the database
 * @param string $userEmail a user email address
 * @param string $fieldMapName the name of the fieldMap to use to retrieve data
 * @return array
 * @throws NotFoundException when a model in not found or the conditions for a join are not found in the field map
 */
	public function setJoins($prodDescription, $userEmail, $fieldMapName) {
		$fieldMap = $this->getArchiveFieldMap($prodDescription, $fieldMapName);
		$joins = [];
		foreach (Hash::extract($fieldMap, 'sourceModels') as $modelName => $val) {

			$jConditions = Hash::extract($fieldMap, "joinConditions.$modelName");
			if (!$this->modelExists($modelName)) {
				throw new NotFoundException("Model $modelName Not Found!");
			}
			if (empty($jConditions)) {
				throw new NotFoundException("Join conditions were not specified in Pricing Archive Config file for Model $modelName!");
			}

			//We don't want to create a join for Merchant model since we are basing the search on it
			if ($modelName === 'Merchant') {
				continue;
			}

			$joins[] = [
							'table' => Inflector::tableize($modelName),
							'alias' => $modelName,
							'type' => 'INNER',
							'conditions' => $jConditions
						];
		}//foreach
		$prodDescription = addslashes($prodDescription);
		$joins[] = [
					'table' => 'products_and_services',
					'alias' => 'ProductsAndService',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.id = ProductsAndService.merchant_id'
					]
				];
		$joins[] = [
					'table' => 'products_services_types',
					'alias' => 'ProductsServicesType',
					'type' => 'LEFT',
					'conditions' => [
						"ProductsServicesType.id = ProductsAndService.products_services_type_id",
						"ProductsServicesType.products_services_description = '$prodDescription'"
					]
				];

		return $joins;
	}

/**
 * Custom finder
 *
 * Returns all archived months and years for a given merchant and product
 *
 * @param string $merchantId a merchant id
 * @param string $productId  a product id
 * @return array containing the MerchantPricingArchive id as the key and the month/year as the value for that archive
 */
	public function getArchivedMoYears($merchantId, $productId) {
		$results = $this->find('list', [
			'fields' => ['id', "month_year"],
			'conditions' => ['merchant_id' => $merchantId, 'products_services_type_id' => $productId],
			'order' => ['year DESC', 'month ASC']
		]);
		return $results;
	}

/**
 * listArchivedProducts method
 *
 * Returns a unique list of all the products ('id' => 'product name') that have been archived for a given merchant id
 *
 * @param string $merchantId a merchant id
 * @return array
 * @throws \InvalidArgumentException
 */
	public function listArchivedProducts($merchantId) {
		if (empty($merchantId)) {
			throw new InvalidArgumentException('Function listArchivedProducts() argument 1 is required.');
		}
		$pIds = $this->find('all', ['conditions' => ['merchant_id' => $merchantId], 'fields' => ['DISTINCT(MerchantPricingArchive.products_services_type_id) As "products_services_type_id"']]);
		if (empty($pIds)) {
			return array();
		} else {
			return $this->ProductsServicesType->find('list', ['conditions' => ['id' => Hash::extract($pIds, '{n}.{n}.{s}')], 'fields' => ['id', 'products_services_description']]);
		}
	}

/**
 * sendCopletionSatusEmail method
 *
 * Triggers an email even to the user who initiated the archiving process whether the mass archive was successfull
 *
 * @param bool $archiveSuccess a boolean value that indicated whether archive succeded or not
 * @param String $email a user email address
 * @return void
 */
	public function sendCompletionSatusEmail($archiveSuccess, $email) {
		if (!empty($this->bgJobTrackerId)) {
			$BackgroundJob = ClassRegistry::init('BackgroundJob');
			$BackgroundJob->finished($this->bgJobTrackerId, $archiveSuccess['result']);
		}
		if (!empty($email)) {
			$event = new CakeEvent('App.Model.readyForEmail', $this, [
					'template' => Configure::read('App.bgJobEmailTemplate'),
					'from' => Configure::read('App.defaultSender'),
					'to' => $email,
					'subject' => Configure::read('App.pricingArhiveEmailSubject'),
					'emailBody' => $archiveSuccess
				]
			);
			// dispatch event to the local event manager
			$this->getEventManager()->dispatch($event);
		}
	}

/**
 * getDataById method
 * Returns archived pricing and UserCostsArchive associated data sorted by each user roles defined in Merchant (not the actual Role.name)
 *
 * @param string $id a pricing archive id
 * @return array
 */
	public function getDataById($id) {
		$result = $this->find('first', [
			'conditions' => ['MerchantPricingArchive.id' => $id],
			'contain' => [
				'Merchant' => ['fields' => ['user_id', 'sm_user_id', 'sm2_user_id', 'partner_id', 'referer_id', 'reseller_id']],
				'UserCostsArchive' => ['User' => 'Role'],
				'ProductsServicesType'
			],
		]);
		return $result;
	}

/**
 * setFormData method
 * Modifies associated user cost archive data structure so there is as much elements in the array as there can be users associated
 * with the merchant.
 *
 * @param string $data a pricing archive data
 * @return array the original data with associated UserCostsArchive sorted hirearchicaly
 */
	public function setFormData($data) {
		$assocUcaRoles = $this->UserCostsArchive->assocUcaRoles;
		//Sort UserCostsArchive data hirearchicaly and insert initial data to fill in the gaps where user cost is not present
		//Every merchant has a rep and the rep is always the MerchantPricingArchive.user_id
		$sortedByRole = Hash::extract($data, "UserCostsArchive.{n}[user_id=" . $data['MerchantPricingArchive']['user_id'] . "]");
		$data = Hash::remove($data, "UserCostsArchive.{n}[user_id=" . $data['MerchantPricingArchive']['user_id'] . "]");

		foreach ($assocUcaRoles as $assocRole) {
			$tmpHolder = [];
			foreach (Hash::extract($data, "UserCostsArchive") as $idx => $ucpData) {
				//if multiple roles then check if current user is associated with the merchant as a manager
				if ($assocRole === User::ROLE_SM && Hash::get($ucpData, "user_id") === $data['Merchant']['sm_user_id']) {
					$tmpHolder = $ucpData;
					break;
				} elseif ($assocRole === User::ROLE_SM2 && Hash::get($ucpData, "user_id") === $data['Merchant']['sm2_user_id']) {
					$tmpHolder = $ucpData;
					break;
				} elseif ($assocRole === User::ROLE_PARTNER && Hash::get($data, 'Merchant.partner_id') === Hash::get($ucpData, "user_id")) {
					$tmpHolder = $ucpData;
					break;
				} elseif ($assocRole === User::ROLE_REFERRER && Hash::get($data, 'Merchant.referer_id') === Hash::get($ucpData, "user_id")) {
					$tmpHolder = $ucpData;
					break;
				} elseif ($assocRole === User::ROLE_RESELLER && Hash::get($data, 'Merchant.reseller_id') === Hash::get($ucpData, "user_id")) {
					$tmpHolder = $ucpData;
					break;
				}
			}//foreach inner
			if (empty($tmpHolder)) {
					//set default initial data
					$sortedByRole[] = [
						'data_for_role' => $assocRole,
						'merchant_id' => $data['MerchantPricingArchive']['merchant_id']
					];
			} else {
				//set data that matched role
				$tmpHolder['data_for_role'] = $assocRole;
				$sortedByRole[] = $tmpHolder;
			}
		}// end outter foreach

		$data['UserCostsArchive'] = $sortedByRole;
		return $data;
	}

/**
 * getEditViewVars method
 * Compiles all variables needed on the edit view
 *
 * @return array
 */
	public function getEditViewVars() {
		$assocRoles = [
			'mgr1' => User::ROLE_SM,
			'mgr2' => User::ROLE_SM2,
			'partner' => User::ROLE_PARTNER,
			'referrer' => User::ROLE_REFERRER,
			'reseller' => User::ROLE_RESELLER
			];

		$usersOptions[User::ROLE_SM] = $this->UserCostsArchive->User->getByRole(User::ROLE_GROUP_MGR1);
		$usersOptions[User::ROLE_SM2] = $this->UserCostsArchive->User->getByRole(User::ROLE_GROUP_MGR2);
		$usersOptions[User::ROLE_PARTNER] = $this->UserCostsArchive->User->getByRole(User::ROLE_GROUP_PARTNER);
		$usersOptions[User::ROLE_REFERRER] = $this->UserCostsArchive->User->getByRole(User::ROLE_GROUP_REFERRER);
		$usersOptions[User::ROLE_RESELLER] = $this->UserCostsArchive->User->getByRole(User::ROLE_GROUP_RESELLER);

		return compact('assocRoles', 'usersOptions', 'firstMgrs', 'secondMgrs');
	}

/**
 * filterData method
 * Removes empty associated UserCostArchive datasets based on whether a user_id is set.
 *
 * @param array &$requestData data submited from edit view form
 * @return void
 */
	public function filterData(&$requestData) {
		if (!empty($requestData)) {
			foreach (Hash::extract($requestData, "UserCostsArchive") as $idx => $dat) {
				if (empty($dat['user_id'])) {
					unset($requestData['UserCostsArchive'][$idx]);
				}
			}
		}
	}

/**
 * createByMerchant method
 * Compiles and persists Merchant Pricing data for a specific merchant, product(s), month and year.
 * Any existing data will be replaced with the latest compilation.
 *
 * @param string $merchantId
 * @param array $productIds
 * @param integer $month
 * @param integer $year
 * @throws Exception
 */
	public function createByMerchant($merchantId, $productIds, $month, $year) {
		if (empty($merchantId) || empty($productIds) || empty($year) || empty($month)) {
			throw new Exception("Error! Missing required parameter to create archive by merchant.");
		}
		//Delete any existing data
		$UserCostsArchive = new UserCostsArchive(false, null, null, true); //instantiate UserCostsArchive->queryMap member var
		$dataSource = $this->getDataSource();
		try {
			$dataSource->begin();
			$this->deleteAll(array('merchant_id' => $merchantId, 'year' => $year, 'month' => $month, 'products_services_type_id' => $productIds), true);
			foreach ($productIds as $pId) {
				/* Data for active Merchants who have current product*/
				$allMerchantsResults = $UserCostsArchive->getUserCostArchiveData($merchantId, $pId);
				// add merchant Id to the array keys to have direct access while iterating the exising merchants
				$allMerchantsResults = Hash::combine($allMerchantsResults, '{n}.Merchant.id', '{n}');
				//Email is not needed
				$newArchiveData = $this->setMpArchiveData($merchantId, $pId, $month, $year, 'noemail@nowhere.com');
				$ucaDat = $UserCostsArchive->formatUserCostArchiveData(Hash::get($allMerchantsResults, $merchantId, []));
				if (!empty($ucaDat)) {
					$newArchiveData['UserCostsArchive'] = $ucaDat;
				}
				if (!empty(Hash::get($newArchiveData, 'MerchantPricingArchive.merchant_id'))) {
					$this->validator()->remove('products');
					$this->validator()->remove('archive_month');
					$this->validator()->remove('archive_year');
					if ($this->saveAssociated($newArchiveData) === false) {
						throw new Exception("Error: Failed to save Pricing Archive data!");
					}
				}
			}
		} catch (Exception $e) {
			$dataSource->rollback();
			throw $e;
		}
		$dataSource->commit();
		return true;
	}
/**
 * getArchivableProducts method
 * Builds a list of products services types that are able to be artchived.
 * If a Merchant.id parameter is passed then all products that belong to that merchant and that are able to be archived will be returned
 * 
 * @param string $merchantId a Merchant.id uuid
 * @return array
 */
	public function getArchivableProducts($merchantId = null) {
		$archivable = Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}");
		$joins = [];
		if (!empty($merchantId)) {
			$joins = [
				[
					'table' => 'products_and_services',
					'alias' => 'PnS',
					'type' => 'INNER',
					'conditions' => [
						"PnS.products_services_type_id = ProductsServicesType.id",
						"PnS.merchant_id = '{$merchantId}'"
					]
				]
			];
		}
		$finalList = $this->ProductsServicesType->find('list', [
			'fields' => ['ProductsServicesType.id', 'ProductsServicesType.products_services_description'],
			'conditions' => [
				'products_services_description' => $archivable,
				'is_active' => true,
			],
			'joins' => $joins
		]);

		return $finalList;
	}

/**
 * getCreateManyViewData method
 * Builds an array of metadata containing archivable products that specifically belong to a Merchant.id.
 * The returned array structure is as follows:
 * array(
 * 	//if at least one product has been archived for curr month / year we allow. If nothing has been archived for curr month / year we do not allow.
 *	'allow_create' => true / false 
 *	array(
 *		'ProductsServicesType' => array(
 *			'id' => [....],
 *			'products_services_description' => [....]
 *		),
 *			'MerchantPricingArchive' => array(
 *					'id' => [....]
 *		)
 *	),
 *	array(another product),
 * )
 *
 * @return array
 */
	public function getCreateManyViewData($requestData) {
		$archivable = $this->getArchivableProducts();
		$data = $this->Merchant->ProductsAndService->find('all', [
			'fields' => [
				'ProductsServicesType.id',
				'ProductsServicesType.products_services_description',
				'MerchantPricingArchive.id',
			],
			'conditions' => [
				'ProductsAndService.merchant_id' => $requestData['MerchantPricingArchive']['merchant_id'],
				'ProductsAndService.products_services_type_id' => array_keys($archivable)
			],
			'joins' => [
				[
					'table' => 'merchant_pricing_archives',
					'alias' => 'MerchantPricingArchive',
					'type' => 'LEFT',
					'conditions' => [
						'MerchantPricingArchive.products_services_type_id = ProductsAndService.products_services_type_id',
						'MerchantPricingArchive.merchant_id = ProductsAndService.merchant_id',
						"MerchantPricingArchive.month = {$requestData['MerchantPricingArchive']['archive_month']}",
						"MerchantPricingArchive.year = {$requestData['MerchantPricingArchive']['archive_year']}"
					]
				],
				[
					'table' => 'products_services_types',
					'alias' => 'ProductsServicesType',
					'type' => 'LEFT',
					'conditions' => [
						'ProductsServicesType.id = ProductsAndService.products_services_type_id',
					]
				]
			],
			'order' => 'ProductsServicesType.products_services_description ASC'
		]);

		if ((int)$requestData['MerchantPricingArchive']['archive_month'] >= (int)date('m') && (int)$requestData['MerchantPricingArchive']['archive_year'] >= (int)date('Y')) {
			$data['allow_create'] = false;
		} else {
			$data['allow_create'] = true;
		}

		return $data;
	}

/**
 * _findAllPricing
 *
 * @param string $state operational state
 * @param array $query query
 * @param array $results result set
 *
 * @return array $results
 */
	protected function _findAllPricing($state, $query, $results = []) {
		if ($state === 'before') {
			if (empty(Hash::extract($query, 'contain.{n}.UserCostsArchive'))) {
				$query['contain'][] = 'UserCostsArchive';
			}

			return $query;
		}
		return $results;
	}

/**
 * deleteMany
 * Deletes many records matching conditions.
 * Expected $conditions param data structure is as follows (All keys must have values)
 * $conditions = [
 * 		'year' => yyyy, //only one year per deletion
 * 		'months' => [single dimentional indexed array of months as numbers mm],
 * 		'products' => [
 *			[single dimentional indexed array of product ids]
 * 		]
 *	]
 *
 * @param array $conditions result set
 * @return boolean true on success or false on falure
 * @throws InvalidArgumentException
 */
	public function deleteMany($conditions) {
		if (empty(Hash::get($conditions, 'year')) || !is_array(Hash::get($conditions, 'months_products')) || empty(Hash::get($conditions, 'months_products')) || !is_array(Hash::get($conditions, 'months_products')) || empty(Hash::extract($conditions, 'months_products.{n}'))) {
			throw new InvalidArgumentException('Missing or invalid entry in conditions array parameter');
		}
		$year = Hash::get($conditions, 'year');

		$orConditions = '';
		foreach (Hash::get($conditions, 'months_products') as $month => $productIds) {
			array_walk($productIds, function(&$item, $key){
				$item = "'$item'";
			});
			$productIds = implode(',', $productIds);
			if (!empty($orConditions)) {
				$orConditions = "$orConditions OR";
			}
			$orConditions .= "(month = $month AND products_services_type_id in ($productIds))";
		}
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		//Using $this->query() is much faster than $this->deleteAll() when deleting thousands of records
		//Associated user_costs_archive.merchant_pricing_archive_id foreign-key has constraint ON DELETE CASCADE, so referenced records will also be deleted.
		$this->query("DELETE FROM merchant_pricing_archives where year = $year and ($orConditions)");
		$stillHasRecords = $this->hasAny(["year = $year AND ($orConditions)"]);

		if ($stillHasRecords) {
			$dataSource->rollback();
			return false;
		}

		$dataSource->commit();
		return true;
	}

}
