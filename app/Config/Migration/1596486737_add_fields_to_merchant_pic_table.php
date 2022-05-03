<?php
class AddFieldsToMerchantPicTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_fields_to_merchant_pic_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'merchant_pcis' => array(
					'cs_board_request_guid' => array(
						'type' => 'uuid',
						'default' => null,
						'null' => true
					),
					'cs_cancel_request_guid' => array(
						'type' => 'uuid',
						'default' => null,
						'null' => true
					),
					'cancelled_controlscan' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'cancelled_controlscan_date' => array(
						'type' => 'date',
						'default' => null,
					)
				)
			)
		),
		'down' => array(
			'drop_field' => array(
				'merchant_pcis' => array(
					'cs_board_request_guid',
					'cs_cancel_request_guid',
					'cancelled_controlscan',
					'cancelled_controlscan_date',
				)
			)
		),
	);
}
