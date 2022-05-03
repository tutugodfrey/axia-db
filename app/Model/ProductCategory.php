<?php

App::uses('AppModel', 'Model');

/**
 * ProductCategory Model
 *
 */
class ProductCategory extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'category_name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Field required'
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Category name already exists, you must enter an unique one!',
				'allowEmpty' => false,
			)
		),
	);

	public $displayField = 'category_name';

	public $order = [
		'ProductCategory.category_name' => 'ASC'
	];

/**
 * $hasMany Associations
 *
 */
	 public $hasMany = array(
		'ProductsServicesType' => array(
			'className' => 'ProductsServicesType',
			'foreignKey' => 'product_category_id'
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['category_name'])) {
			$this->data[$this->alias]['category_name'] = $this->removeAnyMarkUp($this->data[$this->alias]['category_name']);
		}
		
		return parent::beforeSave($options);
	}
}
