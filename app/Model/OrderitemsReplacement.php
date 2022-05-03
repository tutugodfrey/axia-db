<?php

App::uses('AppModel', 'Model');

/**
 * OrderitemsReplacement Model
 *
 * @property OrderitemReplacement $OrderitemReplacement
 * @property Orderitem $Orderitem
 * @property ShippingAxiaToMerchant $ShippingAxiaToMerchant
 * @property ShippingMerchantToVendor $ShippingMerchantToVendor
 * @property ShippingVendorToAxia $ShippingVendorToAxia
 */
class OrderitemsReplacement extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'orderitem_replacement_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'orderitem_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Orderitem',
		'AxiaToMerchantShippingType' => array(
			'className' => 'ShippingType',
			'foreignKey' => 'shipping_axia_to_merchant_id',
		),
		'MerchantToVendorShippingType' => array(
			'className' => 'ShippingType',
			'foreignKey' => 'shipping_merchant_to_vendor_id',
		),
		'VendorToAxiaShippingType' => array(
			'className' => 'ShippingType',
			'foreignKey' => 'shipping_vendor_to_axia_id',
		)
	);

}
