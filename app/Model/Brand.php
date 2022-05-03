<?php

App::uses('AppModel', 'Model');

class Brand extends AppModel {
/**
 * Display field
 *
 * @var string
 */
        public $displayField = 'name';

        public $hasMany = array(
                'Merchant' => array(
                        'className' => 'Merchant',
                        'foreignKey' => 'brand_id'
                )
        );
}
