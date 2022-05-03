<?php
App::uses('AppModel', 'Model');
/**
 * ProductFeatures Model
 *
 * @property ProductSetting $ProductSetting
 */
class ProductFeature extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'feature_name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'feature_name' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'Product Feature should have a name'
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'Feature name already exists, you must enter an unique one!',
				'allowEmpty' => false,
			],
			'input_has_only_valid_chars' => [
				'rule' => ['inputHasOnlyValidChars'],
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			]
		],
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ProductsServicesType'
	);

/**
 * Generic method to get the model record list
 * 
 * @Override AppModel
 * @return array
 */
	public function getList($options = array()) {
		$defaultOptns = array(
			'order' => array("{$this->alias}.{$this->displayField}" => 'ASC')
		);
		$options = array_merge($defaultOptns, $options);
		return $this->find('list', $options);
	}
}
