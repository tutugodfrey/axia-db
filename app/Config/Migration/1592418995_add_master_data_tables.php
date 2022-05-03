<?php
class AddMasterDataTables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_master_data_tables';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchants' => array(
					'source_of_sale' => array('type' => 'string'), // : enum('reseller', 'direct')
					'is_acquiring_only' => array('type' => 'boolean'), // boolean
					'is_pf_only' => array('type' => 'boolean'), // : boolean
					'general_practice_type' => array('type' => 'string'), //
					'specific_practice_type' => array('type' => 'string'), //
				)
			),
			'create_table' => array(
				'imported_data_collections' => array(
					'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
					'merchant_id' => array('type' => 'uuid', 'null' => false),
					'month' => array('type' => 'integer', 'null' => false), // : int
					'year' => array('type' => 'integer', 'null' => false), // : int
					'gw_n1_id' => array('type' => 'uuid'), // : UUID (set based on First Gateway Col)
					'gw_n1_id_seq' => array('type' => 'string', 'length' => 30), // : varchar(30)
					'gw_n1_item_count' => array('type' => 'integer'), // : int
					'gw_n1_vol' => array('type' => 'number'), // : numeric
					'gw_n2_id' => array('type' => 'uuid'), // : UUID (set based on Second Gateway Col)
					'gw_n2_id_seq' => array('type' => 'string', 'length' => 30), // : varchar(30)
					'gw_n2_item_count' => array('type' => 'integer'), // : int
					'gw_n2_vol' => array('type' => 'number'), // : numeric
					'pf_total_gw_item_count' => array('type' => 'integer'), // : int
					'pf_total_gw_vol' => array('type' => 'number'), // : numeric
					'devices_billed_count' => array('type' => 'integer'), // : numeric
					'pf_recurring_rev' => array('type' => 'number'), // : numeric
					'pf_recurring_item_rev' => array('type' => 'number'), // : numeric
					'pf_recurring_device_lic_rev' => array('type' => 'number'), // : numeric
					'pf_recurring_gw_rev' => array('type' => 'number'), // : numeric
					'pf_recurring_acct_rev' => array('type' => 'number'), // : numeric
					'pf_one_time_rev' => array('type' => 'number'), // : numeric
					'pf_per_item_fee' => array('type' => 'number'), // : numeric
					'pf_item_fee_total' => array('type' => 'number'), // : numeric
					'pf_monthly_fees' => array('type' => 'number'), // : numeric
					'pf_one_time_cost' => array('type' => 'number'), // : numeric
					'pf_rev_share' => array('type' => 'number'), // : numeric
					'pf_actual_mo_vol' => array('type' => 'number'), // : numeric
					'acquiring_one_time_rev' => array('type' => 'number'), // : numeric
					'acquiring_one_time_cost' => array('type' => 'number'), // : numeric
					'ach_recurring_rev' => array('type' => 'number'), // : numeric
					'ach_recurring_gp' => array('type' => 'number'), // : numeric
					'is_usa_epay' => array('type' => 'boolean'), // : boolean
					'is_pf_gw' => array('type' => 'boolean'), // : boolean
					'mo_gateway_cost' => array('type' => 'number'), // : numeric
					'profit_loss_amount' => array('type' => 'number'), // : numeric
					'net_sales_count' => array('type' => 'integer'), // : integer
					'net_sales' => array('type' => 'number'), // : numeric
					'gross_sales_count' => array('type' => 'integer'), // : integer
					'gross_sales' => array('type' => 'number'), // : numeric
					'discount' => array('type' => 'number'), // : numeric
					'interchng_income' => array('type' => 'number'), // : numeric
					'interchng_expense' => array('type' => 'number'), // : numeric 
					'other_income' => array('type' => 'number'), // : numeric
					'other_expense' => array('type' => 'number'), // : numeric
					'total_income' => array('type' => 'number'), // : numeric
					'total_expense' => array('type' => 'number'), // : numeric
					'gross_profit' => array('type' => 'number'), // : numeric
					'revised_gp' => array('type' => 'number'), // : numeric
					'total_income_minus_pf' => array('type' => 'number'), // : numeric
					'card_brand_expenses' => array('type' => 'number'), // : numeric
					'processor_expenses' => array('type' => 'number'), // : numeric
					'sponsor_cogs' => array('type' => 'number'), // : numeric
					'ithree_monthly_cogs' => array('type' => 'number'), // : numeric
					'sf_closed_date' => array('type' => 'date'), // : date
					'sf_opportunity_won' => array('type' => 'boolean'), // : boolean
					'sf_projected_accounts' => array('type' => 'integer'), // : int
					'sf_projected_devices' => array('type' => 'integer'), // : int
					'sf_projected_acq_tans' => array('type' => 'integer'), // : int
					'sf_projected_acq_vol' => array('type' => 'number'), // : numeric
					'sf_projected_pf_trans' => array('type' => 'integer'), // : int
					'sf_projected_pf_revenue' => array('type' => 'number'), // : numeric
					'sf_projected_pf_recurring_ach_revenue' => array('type' => 'number'), // : numeric
					'sf_projected_pf_recurring_ach_gp' => array('type' => 'number'), // : numeric
					'sf_support_cases_count' => array('type' => 'integer'), // : int
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'IMPORTED_DATA_COLLECTIONS_MONTH' => array(
							'column' => 'month',
						),
						'IMPORTED_DATA_COLLECTIONS_YEAR' => array(
							'column' => 'year',
						),
						'IMPORTED_DATA_COLLECTIONS_MERCHANT_ID' => array(
							'column' => 'merchant_id',
						)
					)
				),
				'external_record_fields' => array(
					'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
					'merchant_id' => array('type' => 'uuid', 'null' => false),
					'associated_external_record_id' => array('type' => 'uuid'),
					'field_name' => array('type' => 'string', 'null' => false),
					'api_field_name' => array('type' => 'string', 'null' => false),
					'value' => array('type' => 'string', 'default' => null),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'EXTERNAL_RECORD_FIELDS_ASSOC_EXTERNAL_RECORD_ID' => array(
							'column' => 'associated_external_record_id',
						),
						'EXTERNAL_RECORD_FIELDS_MERCHANT_ID_INDEX' => array(
							'column' => 'merchant_id',
						)
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'imported_data_collections',
				'external_record_fields'
			)
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'up') {
			$ImportedDataCollection = $this->generateModel('ImportedDataCollection');
			$AssociatedExternalRecord = $this->generateModel('AssociatedExternalRecord');
			$ExternalRecordFields = $this->generateModel('ExternalRecordFields');
			$dataSource = ConnectionManager::getDataSource($this->connection);
			$dataSource->begin();
			$ImportedDataCollection->query("ALTER TABLE imported_data_collections ADD CONSTRAINT fk_merchant_id FOREIGN KEY (merchant_id) REFERENCES merchants (id) on DELETE CASCADE");
			$result = $ImportedDataCollection->query("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'imported_data_collections' AND constraint_name = 'fk_merchant_id'");
			if (empty($result)) {
				$dataSource->rollback();
				throw new Exception('ERROR: Faled to create FOREIGN KEY CONSTRAINT "imported_data_collections.fk_merchant_id"!');
			}
			$AssociatedExternalRecord->query("ALTER TABLE associated_external_records ADD CONSTRAINT fk_merchant_id FOREIGN KEY (merchant_id) REFERENCES merchants (id) on DELETE CASCADE");
			$result = $AssociatedExternalRecord->query("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'associated_external_records' AND constraint_name = 'fk_merchant_id'");
			if (empty($result)) {
				$dataSource->rollback();
				throw new Exception('ERROR: Faled to create FOREIGN KEY CONSTRAINT "associated_external_records.fk_merchant_id"!');
			}
			$ExternalRecordFields->query("ALTER TABLE external_record_fields ADD CONSTRAINT fk_associated_external_record_id FOREIGN KEY (associated_external_record_id) REFERENCES associated_external_records (id) on DELETE CASCADE");
			$result = $ExternalRecordFields->query("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'external_record_fields' AND constraint_name = 'fk_associated_external_record_id'");
			if (empty($result)) {
				$dataSource->rollback();
				throw new Exception('ERROR: Faled to create FOREIGN KEY CONSTRAINT "external_record_fields.fk_associated_external_record_id"!');
			}
			$dataSource->commit();
		} else {
			$Model = $this->generateModel('Merchant');
			$Model->query("ALTER TABLE IF EXISTS imported_data_collections DROP CONSTRAINT IF EXISTS fk_merchant_id;");
			$Model->query("ALTER TABLE IF EXISTS associated_external_records DROP CONSTRAINT IF EXISTS fk_merchant_id;");
			$Model->query("ALTER TABLE IF EXISTS external_record_fields DROP CONSTRAINT IF EXISTS fk_associated_external_record_id;");
		}
		
		return true;
	}
}
