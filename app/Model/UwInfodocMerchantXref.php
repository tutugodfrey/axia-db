<?php

App::uses('AppModel', 'Model');

/**
 * UwInfodocMerchantXref Model
 *
 * @property Merchant $Merchant
 * @property Infodoc $Infodoc
 * @property Received $Received
 */
class UwInfodocMerchantXref extends AppModel {

	public $useTable = "uw_infodoc_merchant_xrefs";

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'merchant_id' => array(
					'notBlank' => array(
						'rule' => 'notBlank',
						'allowEmpty' => false,
						'message' => 'Please select a Rep'
					)
			),
	);

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
			),
			'UwInfodoc' => array(
						'className' => 'UwInfodoc',
						'foreignKey' => 'uw_infodoc_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'UwReceived' => array(
						'className' => 'UwReceived',
						'foreignKey' => 'uw_received_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			)
	);
/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['notes'])) {
            $this->data[$this->alias]['notes'] = $this->removeAnyMarkUp($this->data[$this->alias]['notes']);
        }
	}
}
