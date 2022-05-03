<?php

/**
 * ResidualProductControl.
 *
 * @property ProductsServicesType $ProductServiceType
 */
class ResidualProductControl extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = [
		/* Foreign keys must be present */
		'user_id' => [
			'notBlank' => [
				'rule' => 'notBlank'
			]
		],
		'product_service_type_id' => [
			'notBlank' => [
				'rule' => 'notBlank'
			]
		],
		/* Checkboxes */
		'do_no_display' => [
			'boolean' => [
				'rule' => 'boolean',
				'empty' => false,
			]
		],
		'tier_product' => [
			'boolean' => [
				'rule' => 'boolean',
				'empty' => false,
			]
		],
		'enabled_for_rep' => [
			'boolean' => [
				'rule' => 'boolean',
				'empty' => false,
			]
		],
		/* Rep Parameters % of Gross Profit */
		'rep_params_gross_profit_tsys' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'rep_params_gross_profit_sage' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'rep_params_gross_profit_dc' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		/* Manager 1 Params % of Gross Profit */
		'manager1_params_gross_profit_tsys' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'manager1_params_gross_profit_sage' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'manager1_params_gross_profit_dc' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		/* Manager 2 Params % of Gross Profit */
		'manager2_params_gross_profit_tsys' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'manager2_params_gross_profit_sage' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'manager2_params_gross_profit_dc' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		/* Manual Override */
		'override_rep_percentage' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'override_multiple' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'override_manager1' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
		'override_manager2' => [
			'percent' => [
				'rule' => ['validPercentage'],
				'empty' => false,
				'message' => 'Must be a valid percentage value with a maximum precision of 3',
			]
		],
	];

	/**
	 * Fields to strip the % symbol from
	 *
	 * It was requested to have the % in the input fields but we just want to save
	 * the float value. This list of fields contains all fields that need to strip
	 * the % and must be type cased to float.
	 *
	 * @var array
	 */
	public $stripPercentageFromFields = [
		'rep_params_gross_profit_tsys',
		'rep_params_gross_profit_sage',
		'rep_params_gross_profit_dc',
		/* Manager 1 Params % of Gross Profit */
		'manager1_params_gross_profit_tsys',
		'manager1_params_gross_profit_sage',
		'manager1_params_gross_profit_dc',
		/* Manager 2 Params % of Gross Profit */
		'manager2_params_gross_profit_tsys',
		'manager2_params_gross_profit_sage',
		'manager2_params_gross_profit_dc',
		/* Manual Override */
		'override_rep_percentage',
		'override_multiple',
		'override_manager1',
		'override_manager2'
	];

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = [
		'User',
		'ProductServiceType' => [
			'className' => 'ProductsServicesType'
		]
	];

	/**
	 * beforeValidate callback
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function beforeValidate($options = []) {
		$this->stripPercentageFromFields();

		return parent::beforeValidate();
	}

	/**
	 * Removes the percentage symbol that was added in the view for visual UI purpose only
	 * and type casts the string to float
	 *
	 * Called in ResidualProductControl::beforeValidate()
	 *
	 * @return void
	 */
	public function stripPercentageFromFields() {
		foreach ($this->stripPercentageFromFields as $field) {
			if (isset($this->data[$this->alias][$field])) {
				$this->data[$this->alias][$field] = str_replace('%', '', $this->data[$this->alias][$field]);
			}
		}
	}

	/**
	 * Creates all required rows for all products if they're not already present
	 *
	 * Use this to sync the fields with new products and to create the fields for
	 * a new user when its created.
	 *
	 * @param integer|string $userId
	 * @return void
	 */
	public function synchronizeProductServiceTypes($userId = null) {
		$result = $this->ProductServiceType->find('all', [
			'fields' => [
				'ProductServiceType.id',
			],
			'order' => false,
		]);

		$productServiceTypeIds = Hash::extract($result, '{n}.ProductServiceType.id');

		$result = $this->find('all', [
			'contain' => [],
			'fields' => [
				$this->alias . '.product_service_type_id',
			],
			'conditions' => [
				$this->alias . '.user_id' => $userId,
				$this->alias . '.product_service_type_id' => $productServiceTypeIds
			],
		]);

		$existingProductServiceTypeIds = Hash::extract($result, "{n}.{$this->alias}.product_service_type_id");
		$newProductServiceTypeIds = array_diff($productServiceTypeIds, $existingProductServiceTypeIds);

		if (!empty($newProductServiceTypeIds)) {
			foreach ($newProductServiceTypeIds as $productServiceTypeId) {
				$this->create();
				$this->save([
					$this->alias => [
						'user_id' => $userId,
						'product_service_type_id' => $productServiceTypeId,
						/* Rep Parameters % of Gross Profit */
						'rep_params_gross_profit_tsys' => 80,
						'rep_params_gross_profit_sage' => 80,
						'rep_params_gross_profit_dc' => 65,
						/* Manager 1 Params % of Gross Profit */
						'manager1_params_gross_profit_tsys' => 80,
						'manager1_params_gross_profit_sage' => 80,
						'manager1_params_gross_profit_dc' => 65,
						/* Manager 2 Params % of Gross Profit */
						'manager2_params_gross_profit_tsys' => 80,
						'manager2_params_gross_profit_sage' => 80,
						'manager2_params_gross_profit_dc' => 65,
						/* Manual Override */
						'override_rep_percentage' => 25,
						'override_multiple' => 3,
						'override_manager1' => 25,
						'override_manager2' => 10,
					]
				]);
			}
		}
	}

	/**
	 * Gets all the control data for a given user id ordered by products_services_type
	 *
	 * @param string $userId User UUID
	 * @return array
	 */
	public function allDataByUserId($userId = null) {
		$this->synchronizeProductServiceTypes($userId);

		return $this->find('all', [
			'contain' => [
				'ProductServiceType'
			],
			'conditions' => [
				$this->alias . '.user_id' => $userId,
			],
			'order' => [
				'ProductServiceType.products_services_description'
			]
		]);
	}

	/**
	 * Clones fields for another user
	 *
	 * @throws RuntimeException
	 * @param string $userFrom User to copy from, UUID
	 * @param string $userTo User to copy the fields to, UUID
	 * @return void
	 */
	public function cloneDataToUser($userFrom, $userTo) {
		$results = $this->find('all', [
			'contain' => [],
			'conditions' => [
				$this->alias . '.user_id' => $userFrom,
			]
		]);

		if (empty($results)) {
			throw new \RuntimeException(__('The user does not have any settings yet or does not exist!'));
		}

		$count = $this->find('count', [
			'contain' => [],
			'conditions' => [
				$this->alias . '.user_id' => $userTo,
			]
		]);

		if ($count > 0) {
			throw new \RuntimeException(__('The user you are trying to clone to already has settings!'));
		}

		foreach ($results as $record) {
			unset(
				$record[$this->alias]['id'],
				$record[$this->alias]['user_id']
			);
			$record[$this->alias]['user_id'] = $userTo;
			$this->create();
			$this->save($record);
		}
	}

}
