<?php

App::uses('AppModel', 'Model');
App::uses('MerchantRejectStatus', 'Model');

/**
 * MerchantRejectLine Model
 *
 */
class MerchantRejectLine extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_reject_id' => array(
			'uuid' => array(
				'rule' => array('uuid')
			),
		),
		'merchant_reject_status_id' => array(
			'uuid' => array(
				'rule' => array('uuid')
			),
		),
	);

/**
 * BelongsTo association
 *
 * @var array
 */
	public $belongsTo = array(
		'MerchantReject',
		'MerchantRejectStatus'
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

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 */
	public function afterFind($results, $primary = false) {
		// Calculate the submitted amount of the reject lines on primary finds
		if ($primary) {
			foreach ($results as &$result) {
				$amount = Hash::get($result, 'MerchantReject.amount');
				if (isset($result[$this->alias])) {
					$result[$this->alias]['submitted_amount'] = $this->getSubmittedAmount($amount, $result[$this->alias]);
				}
			}
		}
		return $results;
	}

/**
 * Calculate the reject line submitted amount
 *
 * @param decimal $amount The merchant reject amount
 * @param array $rejectLine the reject line used to calculate the submitted amount
 *
 * @return decimal with the calculated amount. Null if the reject line dont have submitted amount
 */
	public function getSubmittedAmount($amount, $rejectLine) {
		if (empty($rejectLine['status_date'])) {
			return null;
		} else {
			return $amount + Hash::get($rejectLine, 'fee');
		}
	}

/**
 * Attach default merchant reject
 *
 * @return array
 */
	public function defaultMerchantRejectLine() {
		$rejectLine = array(
			'merchant_reject_status_id' => MerchantRejectStatus::STATUS_RECEIVED,
			'status_date' => $this->_getNow()->format('Y-m-d'),
		);

		return $rejectLine;
	}

/**
 * Contain for MerchantRejectLine row
 *
 * @return array
 */
	public function rowContain() {
		return array(
			'MerchantReject.MerchantRejectRecurrance',
			'MerchantRejectStatus'
		);
	}

/**
 * Reformat the array structure for the rejectLien to match the one at MerchantRejects/cancelRow
 *
 * @param array $merchantReject Array with the MerchantReject data
 * 
 * @return array
 */
	public function formatRejectLine($merchantReject) {
		$rejectLine = Hash::get($merchantReject, 'MerchantRejectLine');
		$rejectLine['MerchantRejectStatus'] = Hash::get($merchantReject, 'MerchantRejectStatus');
		return $rejectLine;
	}
}
