<?php

App::uses('AppModel', 'Model');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * Order Model
 *
 * @property Order $Order
 * @property User $User
 * @property Merchant $Merchant
 * @property ShippingType $ShippingType
 * @property Vendor $Vendor
 * @property CommissionReport $CommissionReport
 * @property Orderitem $Orderitem
 * @property SystemTransaction $SystemTransaction
 */
class Order extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'user_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'ship_to' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
		'tracking_number' => array(
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => false,
				'allowEmpty' => true,
			)
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User',
		'Merchant',
		'Vendor',
		'ShippingType'
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CommissionReport' => array(
			'className' => 'CommissionReport',
			'foreignKey' => 'order_id',
			'dependent' => false,
		),
		'Orderitem' => array(
			'className' => 'Orderitem',
			'foreignKey' => 'order_id',
			'dependent' => true,
		),
		'SystemTransaction' => array(
			'className' => 'SystemTransaction',
			'foreignKey' => 'order_id',
			'dependent' => false,
		)
	);

/**
 * actsAs
 * @var array
 */
	public $actsAs = array(
		'Search.Searchable',
	);

/**
 * Array of Arguments to be used by the search plugin
 * 'Orderitem.equipment_item_id'
 * 'Merchant.dba_mid'
 * 'Orderitem.orderitem_type_id'
 * 'Orderitem.hardware_sn'
 * 'Orderitem.hardware_replacement_for'
 * 'Order.commission_month'
 * Order.invoice_number
 * Order.status
 * Order.date_ordered_b_month
 * Order.date_ordered_b_year
 * Order.date_ordered_e_month
 * Order.date_ordered_e_year
 */
	public $filterArgs = array(
		//Orderitems model filters----------------------------------------
		'equipment_item_id' => array(
			'type' => 'value',
			'field' => 'Orderitem.equipment_item_id'),
		'dba_mid' => array(
			'type' => 'query',
			'method' => 'merchantOrCndtn',
			'field' => 'Merchant.id'),
		'orderitem_type_id' => array(
			'type' => 'value',
			'field' => 'Orderitem.orderitem_type_id'),
		'hardware_sn' => array(
			'type' => 'value',
			'field' => 'Orderitem.hardware_sn'),
		'hardware_replacement_for' => array(
			'type' => 'value',
			'field' => 'Orderitem.hardware_replacement_for'),
		//Order model filters ---------------------------------------------
		'commission_month' => array(
			'type' => 'query',
			'method' => 'commMonthCondition',
			'field' => 'Order.commission_month'),
		'invoice_number' => array(
			'type' => 'value'),
		'status' => array(
			'type' => 'query',
			'method' => 'orderStatusOrCndtn'),
		'date_ordered_b_month' => array(
			'type' => 'query',
			'method' => 'orderDateRangeCondition'),
		'date_ordered_b_year' => array('type' => 'query', 'method' => 'orderDateRangeCondition'),
		'date_ordered_e_month' => array('type' => 'query', 'method' => 'orderDateRangeCondition'),
		'date_ordered_e_year' => array('type' => 'query', 'method' => 'orderDateRangeCondition')
	);

/**
 * setPaginatorSettings method
 *
 * sets the paginator settings to be used as a list
 *
 * @param string $id merchant_id
 * @return array
 */
	public function merchEqmntPaginatorSettings($id) {
		return array(
			'limit' => 9999,
			'conditions' => array(
				'Orderitem.merchant_id' => $id,
				'Order.status !=' => 'DEL'
			),
			'contain' => array(
				'Order' => array('Vendor', 'ShippingType.shipping_type_description'),
				'OrderitemType.orderitem_type_description'
			)
		);
	}

/**
 * beforeSave callback
 *
 * @param array $options Save options
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['notes'])) {
			$this->data[$this->alias]['notes'] = $this->removeAnyMarkUp($this->data[$this->alias]['notes']);
		}
		
		return parent::beforeSave($options);
	}

/**
 * groupOrderItemsByOrder
 *
 * @param array $data indexed array of Orders
 * @return array
 * @throws InvalidArgumentException
 */
	public function groupOrderItemsByOrder($data) {
		/* Check for rquired structure */
		if (empty($data[0]['Order']['id']) || empty($data[0]['Orderitem']['order_id'])) {
			throw new InvalidArgumentException('groupOrderItemsByOrder function expects an indexed array containing Orders and Orderitems data on its second dimention.');
		}
		/* Begin grouping data structure to have all Orderitems inside their corresponding Order array */
		$result = array();
		$count = count($data);
		for ($x = 0; $x < $count; $x++) {
			for ($y = 0; $y < $count; $y++) {
				if ($data[$x]['Order']['id'] == $data[$y]['Orderitem']['order_id']) {
					if (empty($result[$x])) {
						$result[$x] = $data[$x];
						if (isset($result[$x]['OrderitemType'])) {
							unset($result[$x]['OrderitemType']); //to avoid duplicates
						}
						unset($result[$x]['Orderitem']); //to avoid duplicates
						$result[$x]['Order']['Orderitem'] = array();
					}
					//Indent OrderitemType into Orderitem then Orderitem into its corresponding parent Order
					if (isset($data[$y]['OrderitemType'])) {
						array_push($result[$x]['Order']['Orderitem'], array_merge($data[$y]['Orderitem'], array(
							'OrderitemType' => $data[$y]['OrderitemType'])));
					} else {
						array_push($result[$x]['Order']['Orderitem'], array_merge($data[$y]['Orderitem'], array(
							'Merchant' => $data[$y]['Merchant']), array('EquipmentItem' => $data[$y]['EquipmentItem'])));
						unset($result[$x]['Merchant']);
						unset($result[$x]['EquipmentItem']);
					}
					$data[$y]['Orderitem']['order_id'] = null; /* remove to avoid duplicate matches */
				}
			}
		}
		return $result;
	}

/**
 * getOrderByOrderId
 *
 * @param string $id Order.id
 * @return array
 */
	public function getOrderByOrderId($id) {
		$contain = array(
			'Vendor',
			'User' => array('fields' => array('User.id')),
			'ShippingType',
		);
		$data = $this->find('first', array('recursive' => -1, 'contain' => $contain, 'conditions' => array(
				'Order.id' => $id)));
		//Create total fields from on all Orderitems in this Order
		$data['Order']['total_true_cost'] = $this->Orderitem->field('sum(equipment_item_true_price * (CASE WHEN quantity =0 OR quantity IS NULL THEN 1 ELSE quantity END))', array(
			'Orderitem.order_id =' => $id));
		$data['Order']['total_rep_cost'] = $this->Orderitem->field('sum(equipment_item_rep_price * (CASE WHEN quantity =0 OR quantity IS NULL THEN 1 ELSE quantity END))', array(
			'Orderitem.order_id =' => $id));

		return $data;
	}

/**
 * orderitemsPaginatorSettings
 *
 * Set paginator settings for Orderitems pagination for curren order
 *
 * @param string $orderId the Order.id foreingkey
 * @return array
 */

	public function orderitemsPaginatorSettings($orderId) {
		$contain = array(
			'Merchant' => array('fields' => array('Merchant.id', 'Merchant.merchant_mid',
					'Merchant.merchant_dba')),
			'EquipmentItem',
			'EquipmentType',
			'Warranty',
			'ShippingTypeItem',
			'OrderitemType',
			'OrderitemsReplacement' => array('AxiaToMerchantShippingType', 'MerchantToVendorShippingType',
				'VendorToAxiaShippingType')
		);

		return array('limit' => 9999, 'conditions' => array('Orderitem.order_id' => $orderId),
			'contain' => $contain);
	}

/**
 * orderStatusOrCndtn
 *
 * @param type $data filter values
 * @return type
 */
	public function orderStatusOrCndtn($data = array()) {
		$selectedStatuses = explode(',', $data['status']);
		if (count($selectedStatuses) > 1) {//if user selects the option that contails dual status on the form
			return array('OR' => array(
					array('Order.status =' => $selectedStatuses[0]),
					array('Order.status =' => $selectedStatuses[1])
			));
		} else {
			return array('Order.status =' => $selectedStatuses[0]);
		}
	}

/**
 * commMonthCondition
 *
 * @param array $data filter values
 * @return array
 */
	public function commMonthCondition($data = array()) {
		if (!empty($data['commission_month'])) {
			return array('Order.commission_month ' . $data['commission_month']);
		} else {
			return array();
		}
	}

/**
 * merchantOrCndtn
 *
 * @param array $data filter values
 * @return array
 */
	public function merchantOrCndtn($data = array()) {
		return array(
			'OR' => array(
				'Merchant.merchant_dba ILIKE' => '%' . $data['dba_mid'] . '%',
				'Merchant.merchant_mid LIKE' => '%' . $data['dba_mid'] . '%',
			),
		);
	}

/**
 * orderDateRangeCondition
 *
 * @param array $data filter values
 * @return array
 */
	public function orderDateRangeCondition($data = array()) {
		$conditions = array();
		//Order.date_ordered
		if (!empty($data['date_ordered_b_month']) && !empty($data['date_ordered_b_year'])) {
			$conditions['Order.date_ordered >='] = $data['date_ordered_b_year'] . '-' . $data['date_ordered_b_month'] . '-01';
		}
		//Order.date_ordered
		if (!empty($data['date_ordered_e_month']) && !empty($data['date_ordered_e_year'])) {
			$conditions['Order.date_ordered <='] = $data['date_ordered_e_year'] . '-' . $data['date_ordered_e_month'] . '-' . cal_days_in_month(CAL_GREGORIAN, $data['date_ordered_e_month'], $data['date_ordered_e_year']);
		}

		return $conditions;
	}

/**
 * indexPaginatorSettings
 *
 * @return array
 */
	public function indexPaginatorSettings() {
		$settings = array(
			'order' => array(
				'date_ordered' => 'desc'
			),
			'limit' => 100,
		);

		return $settings;
	}

/**
 * getFilterOptions
 *
 * @return array
 */
	public function getFilterOptions() {
		$options = array();
		$options['EquipmentItem'] = $this->Orderitem->EquipmentItem->getEquipmentList();
		$options['OrderStatuses'] = array(
			'PEND' => 'Pending',
			'INV' => 'Invoiced',
			'PAID' => 'Paid',
			'PEND,INV' => 'Pending & Invoiced');
		$options['OrderitemType'] = $this->Orderitem->OrderitemType->find('list', array(
			'fields' => array('id', 'orderitem_type_description')));

		return $options;
	}

/**
 * getOrderFormMenuData
 *
 * @return array
 */
	public function getOrderFormMenuData() {
		$options = array('Vendor' => $this->Vendor->getVendorsList());
		$options['ShippingType'] = $this->ShippingType->getShippingTypesList();
		$options['ShippingTypeItem'] = $this->Orderitem->ShippingTypeItem->getShippingTypesList();
		$options['OrderitemType'] = $this->Orderitem->OrderitemType->getItemTypesList();
		$options['EquipmentType'] = $this->Orderitem->EquipmentType->getEquipmentTypesIDsList();
		$options['EquipmentItem'] = $this->Orderitem->EquipmentItem->getEquipmentList(true);
		$options['Warranty'] = $this->Orderitem->Warranty->getWarrantiesList();

		return $options;
	}

/**
 * cleanupRequestData
 *
 * @param array $data form data
 * @return array
 */
	public function cleanupRequestData($data) {
		$cleanData = Hash::filter($data);
		if (!empty($cleanData['Orderitem'])) {
			foreach ($cleanData['Orderitem'] as $key => $val) {
				/* Unset Orderitem arrays that contain elements that are set by default on the form iff those are the only ones in the array */
				if (is_array($val) && count($val) <= 1 && array_key_exists('equipment_type_id', $val)) {
					unset($data['Orderitem'][$key]);
				} elseif (is_array($val) && !empty($val['Merchant']['merchant_mid'])) {
					$data['Orderitem'][$key]['Merchant']['merchant_mid'] = trim($val['Merchant']['merchant_mid']);
				}
			}
		}
		return $data;
	}

/**
 * setItemRepCost
 * If a merchant is assigned to an order item set the rep cost in the rep’s default compensation profile.
 * If there is a partner assigned to that merchant set the rep cost from the partner-rep compensation profile
 * and the partner costs from partner's default UCP
 * Defaults to the EquipmentItem.equipment_item_rep_price if no merchant is assigned to the order item.
 *
 * @param array $data data submitted from the client side form
 * @return array
 */
	public function setItemRepPartnerCost($data) {
		foreach ($data['Orderitem'] as $key => $val) {
			if (!empty($val['equipment_item_id']) && isset($val['equipment_item_true_price'], $val['equipment_item_rep_price']) === false) {
				$useDefalt = true;
				//Merchant case
				if (!empty($val['Merchant']['merchant_mid'])) {
					$merchData = $this->Merchant->find('first', array(
							'conditions' => array(
								'OR' => array(
									'Merchant.id' => Hash::get($val, "Merchant.id"),
									'Merchant.merchant_mid' => Hash::get($val, "Merchant.merchant_mid"),
								)
							),
							'fields' => array('id', 'user_id', 'partner_id')
						));
					if (!empty($merchData['Merchant']['id'])) {
						$partnerId = Hash::get($merchData, "Merchant.partner_id");
						$repUcp = $this->Merchant->User->UserCompensationProfile->getUserOrPartnerUserCompProfile($merchData['Merchant']['user_id'], $partnerId);
						//Set Rep/PatnerRep costs
						if (!empty($repUcp)) {
							$ucpEquipmentCost = $this->Orderitem->EquipmentItem->EquipmentCost->getRepCost($val['equipment_item_id'], Hash::get($repUcp, "UserCompensationProfile.id"));
							$useDefalt = false;
						}
						//Set partner costs if not set already
						if (!empty($partnerId) && !isset($val['equipment_item_partner_price'])) {
							$partnerUcp = $this->Merchant->User->UserCompensationProfile->getUserOrPartnerUserCompProfile($partnerId);
							if (!empty($partnerUcp)) {
								$partnerEqpmntCost = $this->Orderitem->EquipmentItem->EquipmentCost->getRepCost($val['equipment_item_id'], Hash::get($partnerUcp, "UserCompensationProfile.id"));
								$data['Orderitem'][$key]['equipment_item_partner_price'] = is_numeric($partnerEqpmntCost)? $partnerEqpmntCost: null;
							}
						}
					}
				}

				$eqItemPrice = $this->Orderitem->EquipmentItem->find('first', array('recursive' => -1,
					'conditions' => array('id' => $val['equipment_item_id']), 'fields' => array(
						'equipment_item_true_price', 'equipment_item_rep_price')));

				$data['Orderitem'][$key]['equipment_item_true_price'] = is_numeric($eqItemPrice['EquipmentItem']['equipment_item_true_price'])? $eqItemPrice['EquipmentItem']['equipment_item_true_price']: null;
				if ($useDefalt) {
					$data['Orderitem'][$key]['equipment_item_rep_price'] = is_numeric($eqItemPrice['EquipmentItem']['equipment_item_rep_price'])? $eqItemPrice['EquipmentItem']['equipment_item_rep_price']: null;
				} else {
					$data['Orderitem'][$key]['equipment_item_rep_price'] = is_numeric($ucpEquipmentCost)? $ucpEquipmentCost: null;
				}
			}
			//Set tax amount if needed based on true price
			if ($data['Order']['add_item_tax'] == 1 && empty($data['Orderitem'][$key]['item_tax'])) {
				$data['Orderitem'][$key]['item_tax'] = $this->Orderitem->getItemTaxAmount($data['Orderitem'][$key]['equipment_item_true_price']);
			}
		}
		return $data;
	}

/**
 * makeCsvString
 *
 * @param array $orders Orders data
 * @return array
 */
	public function makeCsvString($orders) {
		$status = GUIbuilderComponent::getStatusList();
		$Number = new CakeNumber;
		//Initialize RBAC
		$Rbac = AccessControlFactory::get($this->_getCurrentUser('id'));
		$csvStr = "Order Date,Item,Item Date,Merchant,Qty,New S/N,Broken S/N,Invoice #,Rep Cost,True Cost,Tax,Shipping,Warranty,Order Total,Vendor,Status\n";

		foreach ($orders as $order) {
			$echoOrder = true;
			$totalCost = 0;
			$totalItemsCost = 0;
			foreach ($order['Orderitem'] as $item) {
				if ($Rbac->isPermitted('app/actions/Orders/view/module/orderDate')) {
					$csvStr .= (!empty($order['Order']['date_ordered']) && $echoOrder) ? date('m/d/Y', strtotime($order['Order']['date_ordered'])) : '';
				}
				$csvStr .= ',' . $this->csvPrepString($item['equipment_item_description']);
				$csvStr .= (!empty($item['item_date_ordered'])) ? ',' . date('m/d/Y', strtotime($item['item_date_ordered'])) : ',';
				$csvStr .= (!empty($item['Merchant'])) ? ',' . $this->csvPrepString($item['Merchant']['merchant_dba']) : ',';
				$csvStr .= ',' . $item['quantity'];
				$csvStr .= ',="' . $item['hardware_sn'] . '"';
				$csvStr .= ',="' . $item['hardware_replacement_for'] . '"';
				if ($Rbac->isPermitted('app/actions/Orders/view/module/invoices')) {
					$csvStr .= ($echoOrder && !empty($order['Order']['invoice_number'])) ? ',' . $order['Order']['invoice_number']: ',';
				}
				if ($Rbac->isPermitted('app/actions/Orders/view/module/repCost')) {
					$csvStr .= ',' . $Number->currency($item['equipment_item_rep_price'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-'));
				}
				if ($Rbac->isPermitted('app/actions/Orders/view/module/trueCost')) {
					$csvStr .= ',' . $Number->currency($item['equipment_item_true_price'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-'));
				}
				$csvStr .= ($echoOrder && !empty($order['Order']['tax'])) ? ',' . $Number->currency($order['Order']['tax'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-')) : ',';
				if ($Rbac->isPermitted('app/actions/Orders/view/module/shipCost')) {
					$csvStr .= ($echoOrder && !empty($order['Order']['shipping_cost'])) ? ',' . $Number->currency($order['Order']['shipping_cost'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-')) : ',';
				}
				$csvStr .= (!empty($item['Warranty']['cost'])) ? ',' . $Number->currency($item['Warranty']['cost'], 'USD', array('after' => false, 'thousands' => '', 'negative' => '-')) : ',';

				if ($echoOrder) {
					//Calculate total
					foreach ($order['Orderitem'] as $iVal) {
						$quantity = ($iVal['quantity'] === 0 || empty($iVal['quantity'])) ? 1 : $iVal['quantity'];
						$totalItemsCost += ($iVal['equipment_item_true_price'] * $quantity);
					}
					$totalCost += $order['Order']['shipping_cost'] + $order['Order']['tax'] + $totalItemsCost;
					$csvStr .= ',' . $Number->currency($totalCost, 'USD', array('after' => false, 'thousands' => '', 'negative' => '-'));
				}
				if ($Rbac->isPermitted('app/actions/Orders/view/module/invoices')) {
					$csvStr .= ($echoOrder && !empty($order['Vendor']['vendor_description'])) ? ',' . $this->csvPrepString($order['Vendor']['vendor_description']) : ',';
				}
				if ($echoOrder) {
					$csvStr .= ',' . $status[$order['Order']['status']];
				}
				$echoOrder = false; /* Do not display the same order data again */
				$csvStr .= "\n";
			}
		}
		return $csvStr;
	}

}
