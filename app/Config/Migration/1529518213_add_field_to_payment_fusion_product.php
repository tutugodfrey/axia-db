<?php
class AddFieldToPaymentFusionProduct extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_field_to_payment_fusion_product';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = [
		'up' => [
			'create_field' => [
				'payment_fusions' => [
					'is_hw_as_srvc' => [
						'type' => 'boolean',
						'default' => false
					]
				]
			]
		],
		'down' => [
			'drop_field' => [
				'payment_fusions' => [
					'is_hw_as_srvc'
				]
			]
		],
	];

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'up') {
			$PaymentFusion = $this->generateModel('PaymentFusion');
			$PaymentFusion->updateAll(
				[
					'is_hw_as_srvc' => true],
				[
					'OR' => [
						'standard_device_fee > 30',
						'vp2pe_device_fee > 30',
						'pfcc_device_fee > 30',
						'vp2pe_pfcc_device_fee > 30',
					]
				]
			);
		}
		return true;
	}
}
