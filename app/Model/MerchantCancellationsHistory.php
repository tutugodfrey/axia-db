<?php

App::uses('AppModel', 'Model');

/**
 * MerchantCancellationsHistory Model
 *
 * @property MerchantCancellation $MerchantCancellation
 * @property Merchant $Merchant
 * @property Subreason $Subreason
 */
class MerchantCancellationsHistory extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_cancellation_id' => array(
			'uuid' => array(
				'rule' => array('notBlank')
			)
		),
		'merchant_id' => array(
			'uuid' => array(
				'rule' => array('notBlank')
			)
		),
		'subreason_id' => array(
			'uuid' => array(
				'rule' => array('notBlank')
			)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant',
		'MerchantCancellationSubreason' => array(
			'className' => 'MerchantCancellationSubreason',
			'foreignKey' => 'merchant_cancellation_subreason_id',
		)
	);

/**
 * archivePreviousCancellation method
 * 
 * When a merchant is re-activated a record of the previous cancellation is created
 * 
 * @param type $id merchant uuid
 * @return bool true/false on save success/fail 
 */
	public function archivePreviousCancellation($id) {
		$data = $this->Merchant->MerchantCancellation->find('first', array(
			'conditions' => array(
				'MerchantCancellation.merchant_id =' => $id
			),
			'fields' => array(
				'merchant_id',
				'merchant_cancellation_subreason_id',
				'merchant_cancellation_subreason', //input field, the previous is a foreing key
				'date_submitted',
				'date_completed',
				'date_inactive',
				'fee_charged',
				'reason',
				'axia_invoice_number'
			)
		));
		$data = Hash::get($data, 'MerchantCancellation');
		/* Did current merchant have any previous cancellation data? */
		if (empty($data)) {
			return true; //nothing to archive
		} else {
			$data['date_reactivated'] = date('Y-m-d');
			$this->create();
			if ($this->save($data, array('validate' => false)) !== false) {
				$this->Merchant->MerchantCancellation->deleteAll(array('MerchantCancellation.merchant_id =' => $id), false);
				return true;
			}

			return false;
		}
	}
}
