<?php
class CreatePaymentFusionTables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_payment_fusion_tables';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'payment_fusions' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary'
					),
					'merchant_id' => array(
						'type' => 'uuid',
						'null' => false,
					),
					'generic_product_mid' => array(
						'type' => 'string',
						'length' => '20',
					),
					'account_fee' => array(
						'type' => 'number',
						'default' => null
					),
					'rate' => array(
						'type' => 'number',
						'default' => null
					),
					'monthly_total' => array(
						'type' => 'number',
						'default' => null
					),
					'per_item_fee' => array(
						'type' => 'number',
						'default' => null
					),
					'other_features' => array(
						'type' => 'text',
						'default' => null
					),
					'standard_num_devices' => array(
						'type' => 'integer',
						'default' => null
					),
					'standard_device_fee' => array(
						'type' => 'number',
						'default' => null
					),
					'vp2pe_num_devices' => array(
						'type' => 'integer',
						'default' => null
					),
					'vp2pe_device_fee' => array(
						'type' => 'number',
						'default' => null
					),
					'pfcc_num_devices' => array(
						'type' => 'integer',
						'default' => null
					),
					'pfcc_device_fee' => array(
						'type' => 'number',
						'default' => null
					),
					'vp2pe_pfcc_num_devices' => array(
						'type' => 'integer',
						'default' => null
					),
					'vp2pe_pfcc_device_fee' => array(
						'type' => 'number',
						'default' => null
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
					)
				),
				'payment_fusions_product_features' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary'
					),
					'payment_fusion_id' => array(
						'type' => 'uuid',
						'null' => false,
					),
					'product_feature_id' => array(
						'type' => 'uuid',
						'null' => false,
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
					)
				),
				'payment_fusion_rep_costs' => array(
					'id' => array(
						'type' => 'uuid',
						'null' => false,
						'key' => 'primary'
					),
					'user_compensation_profile_id' => array(
						'type' => 'uuid',
						'null' => false,
					),
					'rep_monthly_cost' => array(
						'type' => 'number',
						'default' => null
					),
					'rep_per_item' => array(
						'type' => 'number',
						'default' => null
					),
					'standard_device_cost' => array(
						'type' => 'number',
						'default' => null
					),
					'vp2pe_device_cost' => array(
						'type' => 'number',
						'default' => null
					),
					'pfcc_device_cost' => array(
						'type' => 'number',
						'default' => null
					),
					'vp2pe_pfcc_device_cost' => array(
						'type' => 'number',
						'default' => null
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
					)
				),
			)
		),
		'down' => array(
			'drop_table' => array(
				'payment_fusions',
				'payment_fusions_product_features',
				'payment_fusion_rep_costs',
			)
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		if ($direction === 'down') {
			$PaymentFusion = $this->generateModel('PaymentFusion');
			$PaymentFusionsProductFeature = $this->generateModel('PaymentFusionsProductFeature');
			$ProductSetting = $this->generateModel('ProductSetting');
			$ProductsServicesType = $this->generateModel('ProductsServicesType');
			$pfProdId = $ProductsServicesType->field('id', ['products_services_description' => 'Payment Fusion']);
			$pfData = $PaymentFusion->find('all');
			foreach ($pfData as $data) {
				$pfFeatures = $PaymentFusionsProductFeature->find('list', ['conditions' => ['payment_fusion_id' => $data['PaymentFusion']['id']], 'fields' => ['product_feature_id']]);
				$pfProdSettings[] = [
					'merchant_id' => $data['PaymentFusion']['merchant_id'],
					'products_services_type_id' => $pfProdId,
					'product_feature_id' => (count($pfFeatures) === 1)? array_pop($pfFeatures) : null,
					'rate' => $data['PaymentFusion']['rate'],
					'monthly_fee' => $data['PaymentFusion']['account_fee'],
					'monthly_total' => $data['PaymentFusion']['monthly_total'],
					'per_item_fee' => $data['PaymentFusion']['per_item_fee'],
					'gral_fee' => $data['PaymentFusion']['standard_device_fee'] + $data['PaymentFusion']['vp2pe_device_fee'] + $data['PaymentFusion']['pfcc_device_fee'] + $data['PaymentFusion']['vp2pe_pfcc_device_fee'],
					'gral_fee_multiplier' => $data['PaymentFusion']['standard_num_devices'] + $data['PaymentFusion']['vp2pe_num_devices'] + $data['PaymentFusion']['pfcc_num_devices'] + $data['PaymentFusion']['vp2pe_pfcc_num_devices'],
					'generic_product_mid' => $data['PaymentFusion']['generic_product_mid'],
					'other_features' => $data['PaymentFusion']['other_features'],
				];
			}

			if (!$ProductSetting->saveMany($pfProdSettings)) {
				echo "Error: Failed to save products services data for payment fusion!\n";
				return false;
			}
		}
		return true;
	}
/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$pfData = [];
		$dataSource = ConnectionManager::getDataSource($this->connection);
		$ProductsServicesType = $this->generateModel('ProductsServicesType');
		$PaymentFusion = $this->generateModel('PaymentFusion');
		$ProductFeature = $this->generateModel('ProductFeature');
		$ProductSetting = $this->generateModel('ProductSetting');
		$PaymentFusionsProductFeature = $this->generateModel('PaymentFusionsProductFeature');
		$pfProdId = $ProductsServicesType->field('id', ['products_services_description' => 'Payment Fusion']);
		if ($direction === 'up') {
			$dataSource->begin();
			$pfProdSettings = $ProductSetting->find('all', [
				'conditions' => [
					'products_services_type_id' => $pfProdId
				]
			]);

			$productFeatures = $ProductFeature->find('list', ['fields' => ['id', 'feature_name']]);

			foreach ($pfProdSettings as $pfProducSetting) {
				$newData = [
					'merchant_id' => $pfProducSetting['ProductSetting']['merchant_id'],
					'generic_product_mid' => $pfProducSetting['ProductSetting']['generic_product_mid'],
					'account_fee' => $pfProducSetting['ProductSetting']['monthly_fee'],
					'rate' => $pfProducSetting['ProductSetting']['rate'],
					'monthly_total' => $pfProducSetting['ProductSetting']['monthly_total'],
					'per_item_fee' => $pfProducSetting['ProductSetting']['per_item_fee'],
					'other_features' => $pfProducSetting['ProductSetting']['other_features'],
				];
				$prodFeatureId = $pfProducSetting['ProductSetting']['product_feature_id'];
				if (!empty($prodFeatureId)) {
					if ($productFeatures[$prodFeatureId] === 'Standard') {
						$newData['standard_device_fee'] = $pfProducSetting['ProductSetting']['gral_fee'];
						$newData['standard_num_devices'] = $pfProducSetting['ProductSetting']['gral_fee_multiplier'];
					} elseif ($productFeatures[$prodFeatureId] === 'VP2PE') {
						$newData['vp2pe_device_fee'] = $pfProducSetting['ProductSetting']['gral_fee'];
						$newData['vp2pe_num_devices'] = $pfProducSetting['ProductSetting']['gral_fee_multiplier'];
					} elseif ($productFeatures[$prodFeatureId] === 'PFCC') {
						$newData['pfcc_device_fee'] = $pfProducSetting['ProductSetting']['gral_fee'];
						$newData['pfcc_num_devices'] = $pfProducSetting['ProductSetting']['gral_fee_multiplier'];
					} elseif ($productFeatures[$prodFeatureId] === 'VP2PE & PFCC') {
						$newData['vp2pe_pfcc_device_fee'] = $pfProducSetting['ProductSetting']['gral_fee'];
						$newData['vp2pe_pfcc_num_devices'] = $pfProducSetting['ProductSetting']['gral_fee_multiplier'];
					}
				}

				$PaymentFusion->create();
				if ($PaymentFusion->save($newData)) {
					if (!empty($prodFeatureId)) {
						$PaymentFusionsProductFeature->create();
						$PaymentFusionsProductFeature->save([
							'payment_fusion_id' => $PaymentFusion->id,
							'product_feature_id' => $prodFeatureId,
						]);
					}
				} else {
					$dataSource->rollback();
					echo "Error: Failed to save payment fusion data!\n";
					return false;
				}
			}
			//Update product as a stand alone product with its own class identifier
			$saved = $ProductsServicesType->save([
				'id' => $pfProdId,
				'class_identifier' => 'pst_6',
				'custom_labels' => null,
			]);
			if (!$saved) {
				$dataSource->rollback();
				echo "Error: Failed to save products services data for payment fusion!\n";
				return false;
			}
			//delete payment fusion from product settings
			$ProductSetting->deleteAll(['products_services_type_id' => $pfProdId]);
			$dataSource->commit();
		}
		return true;
	}
}
