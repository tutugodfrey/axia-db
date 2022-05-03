<?php
App::uses('AppModel', 'Model');
/**
 * PaymentFusionsProductFeature Model
 *
 */
class PaymentFusionsProductFeature extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'payment_fusion_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Payment fusion id is required',
			),
		),
		'product_feature_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Product Feature id is required',
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ProductFeature' => array(
			'className' => 'ProductFeature',
			'foreignKey' => 'product_feature_id',
			'type' => 'inner',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PaymentFusion' => array(
			'className' => 'PaymentFusion',
			'foreignKey' => 'payment_fusion_id',
			'type' => 'inner',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
