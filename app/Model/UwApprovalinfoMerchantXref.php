<?php

App::uses('AppModel', 'Model');

/**
 * UwApprovalinfoMerchantXref Model
 *
 * @property Merchant $Merchant
 * @property Approvalinfo $Approvalinfo
 * @property VerifiedOption $VerifiedOption
 */
class UwApprovalinfoMerchantXref extends AppModel {

	public $useTable = "uw_approvalinfo_merchant_xrefs";

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
			'UwApprovalinfo' => array(
						'className' => 'UwApprovalinfo',
						'foreignKey' => 'uw_approvalinfo_id',
						'conditions' => '',
						'fields' => '',
						'order' => ''
			),
			'UwVerifiedOption' => array(
						'className' => 'UwVerifiedOption',
						'foreignKey' => 'uw_verified_option_id',
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
