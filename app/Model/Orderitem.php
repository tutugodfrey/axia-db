<?php

App::uses('AppModel', 'Model');

/**
 * Orderitem Model
 *
 * @property Orderitem $Orderitem
 * @property Order $Order
 * @property ItemMerchant $ItemMerchant
 * @property Type $Type
 * @property Orderitem $Orderitem
 * @property Replacement $Replacement
 */
class Orderitem extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'order_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'equipment_type_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'equipment_item_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Item cannot be blank.',
			),
		),
		'equipment_item_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Item cannot be blank.'
			),
		),
		'hardware_replacement_for' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'hardware_sn' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'OrderitemsReplacement' => array(
			'className' => 'OrderitemsReplacement',
			'foreignKey' => 'orderitem_id',
			'dependent' => true //Delete OrderitemsReplacement when Orderitem is deleted
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Order' => array(
			'className' => 'Order',
			'foreignKey' => 'order_id',
		),
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
		),
		'OrderitemType' => array(
			'className' => 'OrderitemType',
			'foreignKey' => 'orderitem_type_id',
		),
		'MerchantAch' => array(
			'className' => 'MerchantAch',
			'foreignKey' => 'merchant_ach_id',
		),
		'ShippingTypeItem' => array(
			'className' => 'ShippingTypeItem',
			'foreignKey' => 'shipping_type_item_id',
		),
		'EquipmentType' => array(
			'className' => 'EquipmentType',
			'foreignKey' => 'equipment_type_id',
		),
		'Warranty' => array(
			'className' => 'Warranty',
			'foreignKey' => 'warranty_id',
		),
		'EquipmentItem' => array(
			'className' => 'EquipmentItem',
			'foreignKey' => 'equipment_item_id',
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['equipment_item_description'])) {
			$this->data[$this->alias]['equipment_item_description'] = $this->removeAnyMarkUp($this->data[$this->alias]['equipment_item_description']);
		}
		
		return parent::beforeSave($options);
	}
/**
 * orderitemsPaginatorSettings
 *
 * Set paginator settings for Orderitems pagination for curren order
 *
 * @param string $orderId the Order.id foreingkey
 * @return array
 */
	public function getSearchSettings($orderId) {
		$contain = array(
			'Merchant' => array(
				'fields' => array(
					'Merchant.id',
					'Merchant.merchant_mid',
					'Merchant.merchant_dba'
				)
			),
			'EquipmentItem',
			'EquipmentType',
			'Warranty',
			'ShippingTypeItem',
			'OrderitemType',
			'OrderitemsReplacement' => array(
				'AxiaToMerchantShippingType',
				'MerchantToVendorShippingType',
				'VendorToAxiaShippingType'
			)
		);

		return array(
			'limit' => 9999,
			'conditions' => array('Orderitem.order_id' => $orderId),
			'contain' => $contain
		);
	}

/**
 * getAllOrderitemsByOrderId
 *
 * @param type $orderId orderId
 * @return array
 */
	public function getAllOrderitemsByOrderId($orderId) {
		$settings = $this->getSearchSettings($orderId);
		$items = $this->find('all', $settings);
		if (empty($items)) {
			return array('Orderitem' => null);
		}
		/* Put Associated model data within each Orderitem subset */
		foreach ($items as $key => $val) {
			$result['Orderitem'][$key] = $val['Orderitem'];

			if (!empty($val['Merchant'])) {
				$result['Orderitem'][$key]['Merchant'] = $val['Merchant'];
			}
			if (!empty($val['EquipmentItem'])) {
				$result['Orderitem'][$key]['EquipmentItem'] = $val['EquipmentItem'];
			}
			if (!empty($val['EquipmentType'])) {
				$result['Orderitem'][$key]['EquipmentType'] = $val['EquipmentType'];
			}
			if (!empty($val['Warranty'])) {
				$result['Orderitem'][$key]['Warranty'] = $val['Warranty'];
			}
			if (!empty($val['ShippingTypeItem'])) {
				$result['Orderitem'][$key]['ShippingTypeItem'] = $val['ShippingTypeItem'];
			}
			if (!empty($val['OrderitemType'])) {
				$result['Orderitem'][$key]['OrderitemType'] = $val['OrderitemType'];
			}
			if (!empty($val['OrderitemsReplacement'])) {
				$result['Orderitem'][$key]['OrderitemsReplacement'] = $val['OrderitemsReplacement'];
			}
		}

		return $result;
	}

/**
 * beforeValidate
 *
 * @param array $options options
 * @return bool
 */
	public function beforeValidate($options = array()) {
		// Add validation rule for the merchant_id UUID
		$orderitemValidator = $this->validator();
		if (empty($this->data['Orderitem']['merchant_id']) && !empty($this->data['Merchant']['merchant_mid'])) {
			$merchantId = (string)$this->Merchant->field('Merchant.id', array(
				'Merchant.merchant_mid' => $this->data['Merchant']['merchant_mid']
			));
			$this->data['Orderitem']['merchant_id'] = $merchantId;
			$orderitemValidator->add('merchant_id', 'valid_merchant', [
				'rule' => 'isValidMerchantUUID',
				'message' => 'MID matched no existing merchant!'
			]);
			unset($this->data['Merchant']); // Remove to avoid saving to the Merchant model
		} else {
			if (isset($orderitemValidator)) {
				$orderitemValidator->remove('merchant_id');
			}
			if (empty($this->data['Merchant']['merchant_mid'])) {
				$this->data['Orderitem']['merchant_id'] = '';
			}
			unset($this->data['Merchant']); // Remove to avoid saving to the Merchant model
		}
		/* Allow creating associated OrderitemsReplacement only when the orderitem_type_id is the correct one
		 * and If type was changed from replacement to anything else then the pre-existing replacement item needs to be deleted
		 */
		if (!empty($this->data['Orderitem']['orderitem_type_id'])) {
			$itemTypes = $this->OrderitemType->getItemTypesList();
			if ($itemTypes[$this->data['Orderitem']['orderitem_type_id']] !== 'Replacement') {

				if (!empty($this->data['OrderitemsReplacement']['id'])) {
					$this->OrderitemsReplacement->delete($this->data['OrderitemsReplacement']['id']);
				}
				unset($this->data['OrderitemsReplacement']);
			}
		} else {
			/* If type was changed from replacement to anything else then the replacement item needs to be deleted */
			if (!empty($this->data['OrderitemsReplacement']['id'])) {
				$this->OrderitemsReplacement->delete($this->data['OrderitemsReplacement']['id']);
			}
			unset($this->data['OrderitemsReplacement']);
		}
		return true;
	}

/**
 * isValidMerchantUUID
 *
 * @param array $uuidCheck uuidCheck
 * @return bool
 * @throws BadFunctionCallException
 */
	public function isValidMerchantUUID($uuidCheck) {
		if (!isset($uuidCheck['merchant_id'])) {
			throw new BadFunctionCallException('Missing \'merchant_id\' from the uuidCheck data');
		}
		return $this->isValidUUID($uuidCheck['merchant_id']);
	}

/**
 * Calculates amount of tax in dollars based on the amount
 *
 * @param float $amount amount
 * @throws HttpException
 * @return mixed
 */
	public function getItemTaxAmount($amount) {
		if (!empty($amount)) {
			$apiResponse = $this->requestAPITaxRate('93101', 'CA', 'Santa Barbara');
			$responseBody = json_decode($apiResponse, true);
			//rCode == 100 means SUCCESS
			if ($apiResponse->isOk() && $responseBody['rCode'] == 100) {
				$taxRate = $responseBody['results'][0]['taxSales'];
				$taxAmount = bcmul($amount, $taxRate, 2);
				return $taxAmount;
			} else {
				throw new HttpException('TAX API responded with error: ' . $responseBody['rCode']);
			}
		} else {
			return 0;
		}
	}

}
