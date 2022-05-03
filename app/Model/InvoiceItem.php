<?php
App::uses('AppModel', 'Model');
/**
 * InvoiceItem Model
 *
 * @property MerchantAch $MerchantAch
 * @property MerchantAchReason $MerchantAchReason
 */
class InvoiceItem extends AppModel {


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'MerchantAch' => array(
			'className' => 'MerchantAch',
			'foreignKey' => 'merchant_ach_id',
		),
		'MerchantAchReason' => array(
			'className' => 'MerchantAchReason',
			'foreignKey' => 'merchant_ach_reason_id',
		),
		'NonTaxableReason' => array(
			'className' => 'NonTaxableReason',
			'foreignKey' => 'non_taxable_reason_id',
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['reason_other'])) {
            $this->data[$this->alias]['reason_other'] = $this->removeAnyMarkUp($this->data[$this->alias]['reason_other']);
        }
	}

/**
 * removeEmpty method
 * Removes empty data from array.
 * An InvoiceItem entry is considered to be empty when all of the following fields are empty
 *
 * id
 * merchant_ach_reason_id
 * reason_other
 * non_taxable_reason_id
 * amount
 * 
 * @param array &$data this array reference should contain InvoiceItem data in a HasMany data structure i.e.: ['InvoiceItem' => [n => [< .. data ...>]]]
 * @return void
 */
	public function removeEmpty(&$data) {
		$dataMod = $data;
		if (!empty($dataMod[$this->alias])) {
			$dataMod = $data[$this->alias];
		}
		foreach($dataMod as $idx => $item){
			if (empty($item['id']) && empty($item['merchant_ach_reason_id']) && empty($item['reason_other']) && empty($item['non_taxable_reason_id']) && empty($item['amount'])) {
				unset($data[$this->alias][$idx], $data[$idx]);//do both
			}
		}

	}
}
