<?php
App::uses('MerchantAchBillingOption', 'Model');
class CreateInvoiceItemsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_invoice_items_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'invoice_items' => array(
					'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
					'merchant_ach_id' => array('type' => 'uuid', 'null' => false),
					'merchant_ach_reason_id' => array('type' => 'uuid'),
					'non_taxable_reason_id' => array('type' => 'uuid'),
					'reason_other' => array('type' => 'string', 'length' => 150),
					'commissionable' => array('type' => 'boolean', 'default' => false),
					'taxable' => array('type' => 'boolean', 'default' => false),
					'amount' => array('type' => 'number'),
					'tax_amount' => array('type' => 'number'),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'INVOICE_ITEM_MERCHANT_ACH_ID_INDEX' => array(
							'column' => 'merchant_ach_id',
						)
					)
				)
			),
			'create_field' => array(
				'merchant_aches' => array(
					'non_taxable_ach_amount' => array('type' => 'number'),
					'tax_state_name' => array('type' => 'string', 'length' => 2),
					'tax_city_name' => array('type' => 'string', 'length' => 50),
					'tax_county_name' => array('type' => 'string', 'length' => 100),
					'tax_rate_state' => array('type' => 'number'),
					'tax_rate_county' => array('type' => 'number'),
					'tax_rate_city' => array('type' => 'number'),
					'tax_rate_district' => array('type' => 'number'),
					'tax_amount_state' => array('type' => 'number'),
					'tax_amount_county' => array('type' => 'number'),
					'tax_amount_city' => array('type' => 'number'),
					'tax_amount_district' => array('type' => 'number'),
					'ship_to_street' => array('type' => 'string', 'length' => 100),
					'ship_to_city' => array('type' => 'string', 'length' => 50),
					'ship_to_state' => array('type' => 'string', 'length' => 2),
					'ship_to_zip' => array('type' => 'string', 'length' => 20),
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'invoice_items'
			),
			'drop_field' => array(
				'merchant_aches' => array(
					'non_taxable_ach_amount',
					'tax_state_name',
					'tax_city_name',
					'tax_county_name',
					'tax_rate_state',
					'tax_rate_county',
					'tax_rate_city',
					'tax_rate_district',
					'tax_amount_state',
					'tax_amount_county',
					'tax_amount_city',
					'tax_amount_district',
					'ship_to_street',
					'ship_to_city',
					'ship_to_state',
					'ship_to_zip'
				)
			)
		),
	);

/**
 * before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		if ($direction === 'down') {
			$MerchantAch = $this->generateModel('MerchantAch');
			//restore data back to original state
			$allUpdated = $MerchantAch->updateAll(
				['ach_amount' => 'non_taxable_ach_amount'],
				['non_taxable_ach_amount > 0']
			);
			if (!$allUpdated) {
				throw new Exception ('Failed to updateAll');
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
		$InvoiceItem = $this->generateModel('InvoiceItem');
		$MerchantAch = $this->generateModel('MerchantAch');
		if ($direction === 'up') {
			$dataSource = ConnectionManager::getDataSource($this->connection);
			$dataSource->begin();
			$InvoiceItem->query('ALTER TABLE invoice_items ADD CONSTRAINT fk_merchant_ach_id FOREIGN KEY (merchant_ach_id) REFERENCES merchant_aches (id) on DELETE CASCADE');
			$result = $InvoiceItem->query("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'invoice_items' AND constraint_name = 'fk_merchant_ach_id'");
			if (empty($result)) {
				$dataSource->rollback();
				throw new Exception('ERROR: Faled to create FOREIGN KEY CONSTRAINT!');
			}
			$dataSource->commit();
			$allUpdated = $MerchantAch->updateAll(
				[
					'non_taxable_ach_amount' => 'ach_amount',
					'ach_amount' => "0"
				],
				['(tax IS NULL OR tax = 0)']
			);
			if (!$allUpdated) {
				throw new Exception ('Failed to update non_taxable_ach_amount');
			}
			$allUpdated = $MerchantAch->updateAll(
				[
					'ach_amount' => 'ach_amount * -1',
					'non_taxable_ach_amount' => 'non_taxable_ach_amount * -1',
					'total_ach' => "total_ach * -1",
					'tax' => "tax * -1",
				],
				['is_debit' => false]
			);
			if (!$allUpdated) {
				throw new Exception ('Failed to update credit amounts into negative amounts');
			}
			$MerchAchBillOptn = $this->generateModel('MerchantAchBillingOption');
			$billRepId = $MerchAchBillOptn->field('id', ['billing_option_description' => MerchantAchBillingOption::BILL_REP]);
			$merchantAches = $MerchantAch->find('all');
			$items = [];
			foreach($merchantAches as $achData) {
				$items[] = [
					'merchant_ach_id' => $achData['MerchantAch']['id'],
					'merchant_ach_reason_id' => $achData['MerchantAch']['merchant_ach_reason_id'],
					'reason_other' => $achData['MerchantAch']['reason_other'],
					'commissionable' => ($achData['MerchantAch']['merchant_ach_billing_option_id'] === $billRepId),
					'taxable' => (abs($achData['MerchantAch']['tax']) > 0),
					'amount' => ($achData['MerchantAch']['ach_amount'] != 0)? $achData['MerchantAch']['ach_amount'] : $achData['MerchantAch']['non_taxable_ach_amount'],
					'tax_amount' => $achData['MerchantAch']['tax'],
				];
			}
			$allSaved = $InvoiceItem->saveMany($items);
			if (!$allSaved) {
				throw new Exception ('Failed to saveMany');
			}
		} else {
			//negate amounts again to return them to positive
			$allUpdated = $MerchantAch->updateAll(
				[
					'ach_amount' => 'ach_amount * -1',
					'total_ach' => "total_ach * -1",
					'tax' => "tax * -1",
				],
				[
					'is_debit' => false,
					'OR' => [
						'ach_amount < 0',
						'total_ach < 0'
						]
				]
			);
			if (!$allUpdated) {
				throw new Exception ('Failed to update credit amounts into negative amounts');
			}
		}
		return true;
	}
}
