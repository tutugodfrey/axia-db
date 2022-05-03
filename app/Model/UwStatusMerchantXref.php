<?php

App::uses('AppModel', 'Model');
App::uses('ControlScan', 'Model');

/**
 * UwStatusMerchantXref Model
 *
 * @property Merchant $Merchant
 * @property Status $Status
 */
class UwStatusMerchantXref extends AppModel {

	public $useTable = "uw_status_merchant_xrefs";

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'merchant_id' => array(
						'notBlank' => array(
								'rule' => array('notBlank'),
						),
			),
			'uw_status_id' => array(
						'notBlank' => array(
								'rule' => array('notBlank'),
						),
			),
			'datetime' => array(
						'datetime' => array(
								'rule' => array('datetime'),
								//'message' => 'Your custom message here',
								'allowEmpty' => true,
								'required' => false,
						),
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
			'UwStatus' => array(
						'className' => 'UwStatus',
						'foreignKey' => 'uw_status_id',
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
		if (!empty($this->data[$this->alias]['datetime'])) {
			$approvedId = $this->UwStatus->field('id', array('name' => 'Approved'));
			$receivedId = $this->UwStatus->field('id', array('name' => 'Received'));
			$dateCompleted = date("Y-m-d", strtotime($this->data[$this->alias]['datetime']));//enforce consistent format
			$merchantId = $this->data[$this->alias]['merchant_id'];
			$tEntry = array(
				'merchant_id' => $merchantId,
				'timeline_date_completed' => $dateCompleted,
			);
			$TimelineEntry = ClassRegistry::init('TimelineEntry');
			if (Hash::get($this->data, 'UwStatusMerchantXref.uw_status_id') === $approvedId) {
				$previousDate = $this->field('datetime', ['id' => Hash::get($this->data, "{$this->alias}.id")]);
				//Update/Create a timeline entry for approval IFF this is a new record or if the datetime has changed from before
				if (empty(Hash::get($this->data, 'UwStatusMerchantXref.id')) || $previousDate !== $this->data[$this->alias]['datetime']) {
					$tEntry['timeline_item_id'] = TimelineItem::APPROVED;
					$TimelineEntry->saveTimeEntry($tEntry);
				}
				//Newly approved acquiring AxiaMed merchants need to be Boarded to ControlScan
				if (empty(Hash::get($this->data, 'UwStatusMerchantXref.id')) && $this->Merchant->isAcquiringAxiaMed($merchantId)) {
					ClassRegistry::init('ControlScan')->boardMerchant($merchantId);
				}
			}
			if (Hash::get($this->data, 'UwStatusMerchantXref.uw_status_id') === $receivedId) {
				$tEntry['timeline_item_id'] = TimelineItem::SUBMITTED;
				$TimelineEntry->saveTimeEntry($tEntry);
			}
		}

		if (!empty($this->data[$this->alias]['notes'])) {
            $this->data[$this->alias]['notes'] = $this->removeAnyMarkUp($this->data[$this->alias]['notes']);
        }
	}
}
