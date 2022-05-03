<?php

App::uses('AppModel', 'Model');

/**
 * ShippingType Model
 *
 */
class ShippingType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'shipping_type_description';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'shipping_type' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
		'shipping_type_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			)
		)
	);

/**
 * Return the list of shipping types
 *
 * @return array
 */
	public function getShippingTypesList() {
		return $this->find('list', array('recursive' => -1, 'fields' => array('id', 'shipping_type_description')));
	}
}
