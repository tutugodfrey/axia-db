<?php
class AddProductCategories extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_product_categories';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'product_categories' => array(
					'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
					'category_name' => array('type' => 'string', 'null' => false, 'length' => '100'),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
					)
				)
			),
			'create_field' => array(
				'products_services_types' => array(
					'product_category_id' => array(
						'type' => 'uuid',
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'products_services_types' => array(
					'product_category_id'
				)
			),
			'drop_table' => array(
				'product_categories'
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
			$ProductCategory = $this->generateModel('ProductCategory');
			$ProductsServicesType = $this->generateModel('ProductsServicesType');
			$ProductCategory->saveMany(array(
				array('category_name' => 'Uncategorized Product'),
				array('category_name' => 'ACH'),
				array('category_name' => 'Credit'),
				array('category_name' => 'Debit/EBT'),
				array('category_name' => 'Gateway'),
			));
			
			$ProductsServicesType->updateAll(
				["product_category_id" => "(SELECT id from product_categories where category_name = 'Credit')"],
				["products_services_description ~* '(Visa|MasterCard|Discover|American|Annual Fee|Credit Monthly)'"]
			);
			$ProductsServicesType->updateAll(
				["product_category_id" => "(SELECT id from product_categories where category_name = 'Debit/EBT')"],
				["products_services_description ~* '(Debit|EBT)'"]
			);
			$ProductsServicesType->updateAll(
				["product_category_id" => "(SELECT id from product_categories where category_name = 'ACH')"],
				["products_services_description ILIKE 'ACH%'"]
			);
			$ProductsServicesType->updateAll(
				["product_category_id" => "(SELECT id from product_categories where category_name = 'Gateway')"],
				["products_services_description ILIKE 'Gateway%'"]
			);
			$ProductsServicesType->updateAll(
				["product_category_id" => "(SELECT id from product_categories where category_name = 'Uncategorized Product')"],
				["product_category_id IS NULL"]
			);
			$ProductsServicesType->query('ALTER TABLE products_services_types ALTER COLUMN product_category_id SET NOT NULL');
		}
		return true;
	}
}
