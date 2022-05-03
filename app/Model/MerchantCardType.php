<?php

App::uses('AppModel', 'Model');

/**
 * MerchantCardType Model
 *
 * @property Merchant $Merchant
 */
class MerchantCardType extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'merchant_id' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
		'card_type' => [
			'notBlank' => [
				'rule' => ['notBlank'],
			],
		],
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Merchant',
		'CardType',
	];

/**
 * getMerchantCardTypesByMerchantId
 *
 * @param sting $id Merchant id
 * @return array
 */
	public function getMerchantCardTypesByMerchantId($id) {
		return $this->find('all', [
			'contain' => ['CardType'],
			'conditions' => ["{$this->alias}.merchant_id" => $id]
		]);
	}

/**
 * updateWithProductId
 * Update merchant card types
 * When adding New card types:
 * 	-Checks id the merchant doesn't already have the CardType to avoid duplication
 * When removing CardTypes:
 *	-Verifies that all products (in param 2) that belong to a CardType have been removed from the merchant before removing the card type
 *
 * @param sting $merchantId a Merchant.id
 * @param mixed sting|array $productId a ProductsServicesType.id or an array of product ids
 * @param boolean $addNew if true will add a new cardtype (iff merchant doesnt have it). If false will remove the cardtypes if found
 * @return mixed boolean true on success | false on failure | null if nothing changed
 */
	public function updateWithProductId($merchantId, $productId, $addNew = true) {
		if (!is_array($productId)) {
			$productId = [$productId];
		}
		$cardTypes = [];
		foreach ($productId as $pId) {
			$cardTypeId = $this->CardType->field('id', [
				//the products belonging to a cardtype have the card type description within their name so inverse comparison yields the fastest result 
				"(SELECT products_services_description from products_services_types where id = '". $pId ."') ~* (card_type_description)"
			]);

			if ($cardTypeId !== false) {
				if ($addNew && !$this->hasAny(['merchant_id' => $merchantId, 'card_type_id' => $cardTypeId])) {
					//Build distinct list of cardtype IDs to remove or add
					$cardTypes[$cardTypeId] = $cardTypeId;					
				} elseif ($addNew == false) {
					//Add cardtypes to be deleted IFF the merchant does not have any of the set of products that belong to that card_type
					if (!$this->Merchant->ProductsAndService->hasAny(['merchant_id' => $merchantId,
							"products_services_type_id IN (SELECT id from products_services_types where products_services_description ~* ((select card_type_description from card_types where id ='$cardTypeId')))"
						])) {
						$cardTypes[$cardTypeId] = $cardTypeId;
					}
				}
			}
		}

		if (!empty($cardTypes)) {
			if ($addNew) {
				foreach ($cardTypes as $value) {
					$updates[] = [
						'merchant_id' => $merchantId,
						'card_type_id' => $value
					];
				}
				return $this->saveMany($updates);
			} else {
				return $this->deleteAll(['merchant_id' => $merchantId, 'card_type_id' => $cardTypes]);
			}
		}
	}

/**
 * getListMerchantCardTypesByMerchantId
 *
 * @param sting $id Merchant id
 * @return array
 */
	public function getListMerchantCardTypesByMerchantId($id) {
		$cardTypes = $this->getMerchantCardTypesByMerchantId($id);
		return Hash::combine($cardTypes, '{n}.CardType.id', '{n}.CardType.card_type_description');
	}
}
