<?php

App::uses('AppModel', 'Model');

class UsersProductsRisk extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'UsersProductsRisk';

/**
 * Validation parameters - initialized in constructor
 *
 * @var array
 * @access public
 */
	public $validate = [
			'merchant_id' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'required' => true,
					'allowEmpty' => false,
					'message' => 'Please enter a Merchant',
					true
				]
			],
			'user_id' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'required' => true,
					'allowEmpty' => false,
					'message' => 'Please enter a User',
					true
				]
			],
			'products_services_type_id' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'required' => true,
					'allowEmpty' => false,
					'message' => 'Please enter a Products Services Type',
					true
				]
			],
			'risk_assmnt_pct' => [
				'decimal' => [
					'rule' => ['decimal'],
					'required' => false,
					'allowEmpty' => true,
					'message' => 'Invalid input! Numbers only please',
					true
				]
			],
			'risk_assmnt_per_item' => [
				'money' => [
					'rule' => ['money'],
					'required' => false,
					'allowEmpty' => true,
					'message' => 'Invalid input! Numbers only please'
				],
				'decimal' => [
					'rule' => ['decimal'],
					'required' => false,
					'allowEmpty' => true,
					'message' => 'Invalid input! Numbers only please'
				]
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
		],
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
		'ProductsServicesType' => [
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
		]
	];

/**
 * getByMerchant method
 *
 * @param string $id merchant id
 * @param bool $includeFormData adds data that can be used for hidden fields on forms
 * @return array
 */
	public function getByMerchant($id, $includeFormData = false) {
		$unsorted = $this->find('all', [
			'conditions' => [
				'UsersProductsRisk.merchant_id' => $id
			]
		]);

		$sortedHasMany = $this->_sortRiskDataByProductsAndUsers($unsorted, $id, $includeFormData);
		return $sortedHasMany;
	}

/**
 * _sortRiskDataByProductsAndUsers
 *
 * Creates a two dimentional data structure where the second dimention's indexes are continued at n+1 with each following top dimention entry rather than starting at zero.
 * The second dimention is a subset which contains UsersProductsRisk data ordered by user and the order is detemined by the fields specified in the merchant search query.
 *
 * @param array $data containing UsersProductsRisk data
 * @param string $merchantId a merchant Id
 * @param bool $includeFormData adds data that can be used for hidden fields on forms
 * @return array
 * @throws InvalidArgumentException
 */
	protected function _sortRiskDataByProductsAndUsers($data, $merchantId, $includeFormData = false) {
		if (!$this->isValidUUID($merchantId) || !$this->Merchant->exists($merchantId)) {
			throw new InvalidArgumentException('Function expects parameter 2 to be a valid merchant UUID');
		}

		$products = $this->ProductsServicesType->find('list', [
			'fields' => [
				'ProductsServicesType.products_services_description',
				'ProductsServicesType.id'
			],
			'conditions' => [
				'ProductsServicesType.is_active' => true
			]
		]);

		//Get users associated with this merchant. We want them in this specific field list order
		$merchantUsers = $this->Merchant->find('first', [
			'fields' => [
				'Merchant.user_id',
				'Merchant.sm_user_id',
				'Merchant.sm2_user_id',
				'Merchant.partner_id',
				'Merchant.referer_id',
				'Merchant.reseller_id'
			],
			'conditions' => [
				'Merchant.id' => $merchantId
			]
		]);

		$i = 0;
		$result = [];
		foreach ($products as $pkey => $pId) {
			//Create a subset containing users grouped up by specific product id. This subset will be out of order
			$usersProductsRiskSubset = Hash::extract($data, "{n}.UsersProductsRisk[products_services_type_id=$pId]");

			//Use the ordered merchant users array as a sorting model by which to sort users within each subset
			foreach ($merchantUsers['Merchant'] as $uid) {
				if (empty($uid)) {
					$result[$pkey][$i] = null;
					$i++;
					continue;
				}
				//Extract users in order from each subset. Subsets contain maximum one user matching the given Id so extract will always return one item at index zero.
				$result[$pkey][$i] = Hash::get(Hash::extract($usersProductsRiskSubset, "{n}[user_id=$uid]"), '0');//this is powerful!
				if ($includeFormData) {
					if (empty($result[$pkey][$i])) {
						$result[$pkey][$i] = [
							'merchant_id' => $merchantId,
							'user_id' => $uid,
							'products_services_type_id' => $pId
						];
					}
				}
				$i++;
			}//inner foreach
		}
		return $result;
	}

/**
 * cleanSaveData Method
 *
 * Used before a save operation (beforeSave)
 *
 * @param array $dirtyDat data from which empty array items are to be removed
 * @return array
 */
	public function cleanSaveData($dirtyDat) {
		$cleanDat = [];
		foreach ($dirtyDat['UsersProductsRisk'] as $key => $data) {
			if (!(empty($data['id']) && empty($data['risk_assmnt_pct']) && empty($data['risk_assmnt_per_item']))) {
				$cleanDat[] = $dirtyDat['UsersProductsRisk'][$key];
			}
		}
		return $cleanDat;
	}
}
