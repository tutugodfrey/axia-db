<?php

App::uses('AppModel', 'Model');

/**
 * OrderitemType Model
 *
 * @property OrderitemType $OrderitemType
 * @property Orderitem $Orderitem
 */
class OrderitemType extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'orderitem_type_id' => array(
			'numeric' => array(
				'rule' => array('notBlank'),
			),
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Orderitem' => array(
			'className' => 'Orderitem',
			'foreignKey' => 'orderitem_type_id',
			'dependent' => false,
		)
	);

/**
 * getItemTypesList
 *
 * @return array
 */
	public function getItemTypesList() {
		return $this->find('list', array('fields' => array('id', 'orderitem_type_description')));
	}

}
