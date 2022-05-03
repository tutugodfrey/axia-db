<?php
class CreateMMerchantMidTypesTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_m_merchant_mid_types_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
            'create_table' => array(
                'merchant_types' => array(
                    'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
                    'type_description' => array('type' => 'string', 'length' => 100),
                    'indexes' => array(
                        'PRIMARY' => array(
                            'column' => 'id',
                            'unique' => 1
                        ),
                        'MERCHANT_TYPE_UNIQUE' => array(
                            'column' => 'type_description',
                            'unique' => 1
                        ),
                    )
                )
            ),
            'create_field' => array(
                'merchants' => array(
                    'merchant_type_id' => array('type' => 'uuid'),
                )
            )
		),
		'down' => array(
            'drop_table' => array(
                'merchant_types'
            ),
            'drop_field' => array(
                'merchants' => array(
                    'merchant_type_id'
                )
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
            $MerchantType = $this->generateModel('MerchantType');
            $Merchant = $this->generateModel('Merchant');
            $types = array(
                array('type_description' => 'ACH'),
                array('type_description' => 'Acquiring'),
                array('type_description' => 'CareCredit'),
                array('type_description' => 'Gateway'),
                array('type_description' => 'Text&Pay'),
            );
            $MerchantType->saveMany($types);
            $acquiringType = $MerchantType->find('first', array('conditions' => array('type_description' => 'Acquiring')));

            $Merchant->updateAll(
                array('merchant_type_id' => "'{$acquiringType['MerchantType']['id']}'"),
                array("char_length(merchant_mid) = 16")
            );
        }
		return true;
	}
}
