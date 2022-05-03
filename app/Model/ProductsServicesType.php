<?php

App::uses('AppModel', 'Model');

/**
 * ProductsServicesType Model
 *
 */
class ProductsServicesType extends AppModel {
	const ALL_LEGACY_OPTN = 'all_legacy';
	const AMEX_DISCOUNT_NAME = 'American Express Discount';
	public $displayField = 'products_services_description';

	public $order = [
		'ProductsServicesType.products_services_description' => 'ASC'
	];

/**
 * $belongsTo Associations
 *
 */
public $belongsTo = array(
        'ProductCategory' => array(
            'className' => 'ProductCategory',
            'foreignKey' => 'product_category_id'
        )
    );

/**
 * $hasOne Associations
 *
 */
	public $hasOne = array(
		'ProductSetting' => array(
			'className' => 'ProductSetting',
		)
	);

/**
 * List of product names that are used for monthly cost archiving.
 *
 * @var array $monthlyCost
 * @access public
 */
	public $monthlyCost = [
		'Credit Monthly',
		'Debit Monthly',
		'EBT Monthly',
	];

/**
 * List of product names that have their own Models.
 *
 * @var array $pModelNames
 * @access public
 */
	public $pModelNames = [
		'ACH' => 'Ach',
		'ACH - Web Based' => 'WebBasedAch',
		'Check Guarantee' => 'CheckGuarantee',
		'Gateway 1' => 'Gateway1',
		'Gift & Loyalty' => 'GiftCard',
	];

/**
 * Custom finders
 * @var array
 */
	public $findMethods = [
		'legacy' => true,
		'active' => true,
		'creditProductsList' => true]
	;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'is_active' => [
			'boolean' => [
				'rule' => ['boolean'],
				'message' => 'is_active should have a boolean value',
				'allowEmpty' => true,
				'required' => false
			]
		],
		'products_services_description' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'Product/Service should have a description'
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'Product already exists, you must enter an unique one!',
				'allowEmpty' => false,
			],
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			]
		],
		'product_category_id' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'allowEmpty' => false,
				'required' => true,
				'message' => 'Product category is required'
			]
		],
		'class_identifier' => [
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			]
		]
	];

/**
 * afterFind callback
 *
 * @param array $results query results
 * @param boolean $primary whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 */
	public function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (is_numeric($key)) {
				if (!empty($val[$this->alias]['custom_labels'])) {
					$results[$key][$this->alias]['custom_labels'] = unserialize($val[$this->alias]['custom_labels']);
				}
			} else {
				if (!empty($results['custom_labels'])) {
					$results['custom_labels'] = unserialize($results['custom_labels']);
				}
				break;
			}
		}
		return $results;
	}

/**
 * getAllProductsList
 *
 * @return void
 */
	public function getAllProductsList() {
		return $this->find('list', [
			'fields' => [
				'id', 'products_services_description'
			]
		]);
	}

/**
 * getList
 *
 * @return array list of all active ProductsServicesTypes
 */
	public function getList() {
		return $this->find('list', [
			'conditions' => [
				"{$this->alias}.is_active" => true,
			]
		]);
	}

/**
 * Get active products
 *
 * @param string $state state
 * @param bool $query query settings
 * @param mixed $results list of active products or null
 * @return array
 */
	protected function _findActive($state, $query, $results = []) {
		if ($state === 'before') {
			$query['conditions']["{$this->alias}.is_active"] = true;
			return $query;
		}
		return $results;
	}

/**
 * getNameById
 * Returns the name of a single product given its id
 *
 * @param string $id of the product
 * @return string
 */
	public function getNameById($id) {
		return $this->field('products_services_description', ["{$this->alias}.id" => $id]);
	}

/**
 * getIdByName
 * Returns the id of a single product given its name
 *
 * @param string $name of the product
 * @return string
 */
	public function getIdByName($name) {
		return $this->field('id', ["{$this->alias}.products_services_description" => $name]);
	}

/**
 * initProductModel
 * Returns an instance of the product Model if exists
 *
 * @param string $id a product id
 * @return mixed  Model | false if product does not have a model
 */
	public function initProductModel($id) {
		$classId = $this->field('class_identifier', ['id' => $id]);
		if (!empty($classId)) {
			return ClassRegistry::init(Configure::read("App.productClasses.$classId.className"));
		}
		return false;
	}

/**
 * _findLegacy
 * Custom find method to retrieve legacy products
 *
 * @param string $state state
 * @param bool $query query settings
 * @param mixed $results list of active products or null
 * @return array
 */
	protected function _findLegacy($state, $query, $results = []) {
		if ($state === 'before') {
			if (!isset($query['order'])) {
				$query['order'] = "products_services_description ASC";
			}
			$query['conditions']["{$this->alias}.is_legacy"] = true;
			return $query;
		}
		return $results;
	}

/**
 * _findCreditProductsList
 * Custom find method to retrieve list of Credit-Only products. 
 * Visa|MasterCard|Discover|American Express
 *
 * @param string $state state
 * @param bool $query query settings
 * @param mixed $results list of active products or null
 * @return array
 */
	protected function _findCreditProductsList($state, $query, $results = []) {
		if ($state === 'before') {
			if (!isset($query['order'])) {
				$query['order'] = "products_services_description ASC";
			}
			$query['conditions']["{$this->alias}.is_active"] = true;
			$query['conditions'][] = "{$this->alias}.products_services_description ~* '(Visa|MasterCard|Discover|American Express)'";
			$query['fields'] = ["id", "products_services_description"];
			return $query;
		} elseif ($state === 'after') {
			if (!empty($results)) {
				$results = Hash::combine($results, '{n}.ProductsServicesType.id', '{n}.ProductsServicesType.products_services_description');
			}
		}
		return $results;
	}

/**
 * buildDdOptionGroups
 * Builds a list useful for dropdowns containing two product subsets.
 * The First option group contains a set of products are non legacy. 
 * The second option group contains a set of legacy ones.
 * The top option within the legacy subset is an All Legacy products option
 *
 * @param boolean filterArchivable if true the subset list of non-legacy products 
 * will contain only products that are archivable.
 * @return array
 */
	public function buildDdOptions($filterArchivable = false) {
		$conditions = [
			'is_legacy' => false,
			'is_active' => true,
		];
		if ($filterArchivable) {
			$archivable = Hash::extract(Configure::read("ArchiveFieldMap"), "{n}.CommonDataProductNames.{n}");
			$conditions['products_services_description'] = $archivable;
		}
		$nonLegacy = $this->find('list', ['conditions' => $conditions]);
		$currentProducts = [
			'Current Products' => $nonLegacy
		];
		$topLegacyElement = [self::ALL_LEGACY_OPTN => 'All Legacy Products'];
		$oldProducts = $this->find('list', ['conditions' => ['is_legacy' => true]]);
		$legacyProducts = [
			'Legacy Products' => array_merge($topLegacyElement, $oldProducts)
		];

		$list = array_merge($currentProducts, $legacyProducts);
		return $list;
	}
}
