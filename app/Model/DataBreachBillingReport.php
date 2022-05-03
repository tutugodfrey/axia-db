<?php

App::uses('AppModel', 'Model');


/**
 * DataBreachBillingReport Model
 */
class DataBreachBillingReport extends AppModel {

	public $useTable = false;

	const REPORT_AXIA_TECH = 'axiatech';
	const REPORT_AXIA_PAYMENTS = 'axiapayments';

/**
 * getAxiaTechReport
 *
 * @return array $results
 */
	public function getAxiaTechReport() {
		if (date("m") == '01') {
			$teAppDate = date("Y", strtotime("-1 year", time())) . "-" . date("m", strtotime("-1 month", time())) . "-01";
		} else {
			$teAppDate = date("Y") . "-" . date("m", strtotime("-1 month", time())) . "-01";
		}

		$conditions = array(
			"MerchantPci.insurance_fee is NOT NULL",
			"MerchantPci.insurance_fee != 0",
			"Merchant.active" => 1,
			"TimelineEntry.timeline_date_completed < " . "'$teAppDate'",
			"MerchantCancellation.date_completed IS NULL",
			"Entity.entity_name = 'AxiaMed'"
		);

		$results = $this->getReport($conditions);
		return $results;
	}

/**
 * getAxiaPaymentsReport
 *
 * @return array $results
 */
	public function getAxiaPaymentsReport() {
		if (date("m") == '01') {
			$teAppDate = date("Y", strtotime("-1 year", time())) . "-" . date("m", strtotime("-1 month", time())) . "-01";
		} else {
			$teAppDate = date("Y") . "-" . date("m", strtotime("-1 month", time())) . "-01";
		}

		$conditions = array(
			"MerchantPci.insurance_fee is NOT NULL",
			"MerchantPci.insurance_fee != 0",
			"Merchant.active" => 1,
			"TimelineEntry.timeline_date_completed < " . "'$teAppDate'",
			"MerchantCancellation.date_completed IS NULL",
			"Entity.entity_name = 'Axia Payments'"
		);

		$results = $this->getReport($conditions);
		return $results;
	}

/**
 * getReport
 *
 * @param array $conditions query conditions
 *
 * @return array $results
 */
	public function getReport($conditions = null) {
		$this->MerchantBank = ClassRegistry::init('MerchantBank');
		$this->TimelineItem = ClassRegistry::init('TimelineItem');

		$results = $this->MerchantBank->find('all',
			array(
				'fields' => array(
					'Merchant.merchant_mid',
					'Merchant.merchant_dba',
					'MerchantBank.fees_routing_number',
					'MerchantBank.fees_dda_number',
					'MerchantPci.insurance_fee',
					'TimelineEntry.timeline_item_id',
					'TimelineEntry.timeline_date_completed',
					'MerchantCancellation.date_completed'
				),
				'joins' => array(
					array(
						'alias' => 'TimelineEntry',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							"MerchantBank.merchant_id = TimelineEntry.merchant_id",
							"TimelineEntry.timeline_item_id = " . "'" . TimelineItem::APPROVED . "'"
						)
					),
					array(
						'alias' => 'MerchantPci',
						'table' => 'merchant_pcis',
						'type' => 'LEFT',
						'conditions' => '"MerchantBank"."merchant_id" = "MerchantPci"."merchant_id"'
					),
					array(
						'alias' => 'Merchant',
						'table' => 'merchants',
						'type' => 'LEFT',
						'conditions' => '"MerchantBank"."merchant_id" = "Merchant"."id"'
					),
					array(
						'alias' => 'MerchantCancellation',
						'table' => 'merchant_cancellations',
						'type' => 'LEFT',
						'conditions' => '"MerchantBank"."merchant_id" = "MerchantCancellation"."merchant_id"'
					),
					array(
						'alias' => 'Entity',
						'table' => 'entities',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."entity_id" = "Entity"."id"'
					)
				),
				'conditions' => $conditions,
				'order' => array(
					'MerchantBank.merchant_id DESC'
				),
				'recursive' => -1
			)
		);

		foreach ($results as &$result) {
			if (!empty($result['MerchantBank']['fees_routing_number'])) {
				$result['MerchantBank']['fees_routing_number'] = $this->decrypt($result['MerchantBank']['fees_routing_number'], Configure::read('Security.OpenSSL.key'));
			}

			if (!empty($result['MerchantBank']['fees_dda_number'])) {
				$result['MerchantBank']['fees_dda_number'] = $this->decrypt($result['MerchantBank']['fees_dda_number'], Configure::read('Security.OpenSSL.key'));
			}
		}

		return $results;
	}
}
