<?php

App::uses('AppModel', 'Model');

/**
 * ShippingTypeItem Model
 *
 */
class ShippingTypeItem extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			  'shipping_type' => array(
						'numeric' => array(
								  'rule' => array('numeric'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
			  'shipping_type_description' => array(
						'notBlank' => array(
								  'rule' => array('notBlank'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
						),
			  ),
	);

	/*	 * getShippingTypesList
	 * 
	 * @return array
	 */

	public function getShippingTypesList() {
		return $this->find('list', array('recursive' => -1, 'fields' => array('id', 'shipping_type_description')));
	}

}
