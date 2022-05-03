<?php
class CreateApiVDigestValuesTrackerTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_api_v_digest_values_tracker_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
    public $migration = array(
        'up' => array(
            'create_table' => array(
                'api_auth_value_trackers' => array(
                    'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
                    'nonce_value' => array('type' => 'string', 'length' => 50, 'null' => false),
                    'nonce_value_used' => array('type' => 'boolean', 'null' => false),
                    'created' => array('type' => 'datetime', 'null' => false),
                    'modified' => array('type' => 'datetime', 'null' => false),
                    'indexes' => array(
                        'PRIMARY' => array(
                            'column' => 'id',
                            'unique' => 1
                        ),
                        'API_AUTH_VAL_NONCE_VAL_INDEX' => array(
                            'column' => 'nonce_value',
                        )
                    )
                )
            )
        ),
        'down' => array(
            'drop_table' => array(
                'api_auth_value_trackers'
            )
        ),
    );
}
