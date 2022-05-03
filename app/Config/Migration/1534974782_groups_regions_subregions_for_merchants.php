<?php
class GroupsRegionsSubregionsForMerchants extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'groups_regions_subregions_for_merchants';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchants' => array(
					'region_id' => array('type' => 'uuid'),
					'subregion_id' => array('type' => 'uuid'),
					'organization_id' => array('type' => 'uuid'),
					'indexes' => array(
						'MERCHANT_REGION_ID_INDEX' => array(
							'column' => 'region_id',
							'unique' => false
						),
						'MERCHANT_SUBREGION_ID_INDEX' => array(
							'column' => 'subregion_id',
							'unique' => false
						),
						'MERCHANT_ORGANIZATION_ID_INDEX' => array(
							'column' => 'organization_id',
							'unique' => false
						)
					)
				),
				'groups' => array(
					'indexes' => array(
						'UNIQUE_GROUP_DESC_INDEX' => array(
							'column' => 'group_description',
							'unique' => true
						)
					)
				)	
			),
			'alter_field' => array(
				'groups' => array(
					'group_description' => array(
						'null' => false
					)
				)
			),
			'create_table' => array(
				'organizations' => array(
					'id' => array('type' => uuid, 'null' => false, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => false, 'length' => 100),
					'active' => array('type' => 'boolean', 'null' => false, 'default' => true),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'UNIQUE_ORGANIZATION_NAME_INDEX' => array(
							'column' => 'name',
							'unique' => true
						)
					)
				),
				'regions' => array(
					'id' => array('type' => uuid,'null' => false,'key' => 'primary'),
					'organization_id' => array('type' => uuid, 'null' => false),
					'name' => array('type' => 'string', 'null' => false, 'length' => 100),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'UNIQUE_REGION_NAME_INDEX' => array(
							'column' => 'name',
							'unique' => true
						)
					)
				),
				'subregions' => array(
					'id' => array('type' => uuid,'null' => false,'key' => 'primary'),
					'region_id' => array('type' => uuid, 'null' => false),
					'organization_id' => array('type' => uuid, 'null' => false),
					'name' => array('type' => 'string', 'null' => false, 'length' => 100),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
						'UNIQUE_SUBREGION_NAME_INDEX' => array(
							'column' => 'name',
							'unique' => true
						)
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchants' => array(
					'region_id',
					'subregion_id',
					'organization_id',
					'indexes' => array(
						'MERCHANT_REGION_ID_INDEX',
						'MERCHANT_SUBREGION_ID_INDEX',
						'MERCHANT_ORGANIZATION_ID_INDEX'
					)
				),
				'groups' => array(
					'indexes' => array(
						'UNIQUE_GROUP_DESC_INDEX'
					)
				)	
			),
			'drop_table' => array(
				'regions',
				'subregions',
				'organizations'
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
		return true;
	}
}
