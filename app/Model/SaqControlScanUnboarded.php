<?php

App::uses('AppModel', 'Model');

/**
 * SaqControlScanUnboarded Model
 *
 * @property Merchant $Merchant
 */
class SaqControlScanUnboarded extends AppModel {
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			  'Merchant' => array(
						'className' => 'Merchant',
						'foreignKey' => 'merchant_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			  )
	);

}
