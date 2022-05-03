<?php
App::uses('AppModel', 'Model');

class Dashboard extends AppModel {

/**
 * This Class does not have a table 
 *
 * @var useTable
 */
	public $useTable = false;

/**
 * getAcquiringMerch
 * Counts the total number of merchants that have visa or mastercard discount products for a given year
 *
 * @param integer $year (optional) 4 digit year defaults to current year if nothing is passed.
 * @return mixed array containing [month => aggregate count of acquiring merchants in that month] for all 12 months
 *				 will reutrn false if fails to find an aquiring product id to use for the aggregate month search
 */
	public function getAcquiringMerch($year = null) {
		if (empty($year) || strlen($year) != 4) {
			$year = date('Y');
		}

		$ResidualReport = ClassRegistry::init('ResidualReport');
		$ProductsServicesType = ClassRegistry::init('ProductsServicesType');
		$acquiringProductId = $ProductsServicesType->field('id', [
			'products_services_description' => 'Visa Discount'
		]);
		//If "acquiring product" wasn't found try another
		if (empty($acquiringProductId)) {
			$acquiringProductId = $ProductsServicesType->field('id', [
				'products_services_description' => 'MasterCard Discount'
			]);
		}
		//Still not found return false
		if (empty($acquiringProductId)) {
			return false;
		}
		$data = $ResidualReport->find('all', [
			'fields' => [
				'r_month',
				'((count(ResidualReport.r_month))) AS "ResidualReport__count"'
			],
			'conditions' => [
				'r_year' => $year,
				'products_services_type_id' => $acquiringProductId
			],
			'group' => 'r_month',
			'order' => "r_month ASC"
		]);
		$data = array_combine(Hash::extract($data, '{n}.ResidualReport.r_month'), Hash::extract($data, '{n}.ResidualReport.count'));

		//fill missing months with zeros
		for ($x = 1; $x <= 12; $x++) {
			if (!isset($data[$x])) {
				$result[$x] = 0;
			} else {
				$result[$x] = $data[$x];
			}
		}

		return Hash::extract($result, '{n}');
	}

/**
 * getPiChartData()
 * Returns data for Charts JS piChart as JSON entires in the array are as follows:
 * [ <cancelled>, <approved>, <installed>, <in underwriting>, <no data> ]
 * 
 * When no data is available yet for the configured timeframe the last entry in the array will be 1
 *
 * @param integer $month the 2 digit month for which to get statistical data
 * @return integer
 */
	public function getPiChartData($month = null) {
		$data[] = $this->getCancelledCount($month);
		$data[] = $this->getApprovedCount($month);
		$data[] = $this->getInstalledCount($month);
		$data[] = $this->getInUwCount($month);
		if (array_sum($data) === 0) {
			$data[] = 1;
		} else {
			$data[] = 0;
		}
		return $data;
	}

/**
 * getCancelledCount()
 * Counts the total number of merchants cancelled current month or the month in param
 *
 * @param integer $month a two digit month. If omitted will use current month.
 * @return integer
 */
	public function getCancelledCount($month = null) {
		if (empty($month)) {
			$month = date('m');
		}
		$monthStart = date("Y-$month-01");
		$monthEnd = date("Y-m-t 23:59:00", strtotime($monthStart)); //get last day of any month
		$count = ClassRegistry::init('MerchantCancellation')->find('count', [
				'conditions' => [
					'date_completed >=' => $monthStart,
					'date_completed <=' => $monthEnd,
				]
			]);
		return $count;
	}

/**
 * getApprovedCount()
 * Counts the total number of merchants approved current month or the month in param
 *
 * @param integer $month a two digit month. If omitted will use current month.
 * @return integer
 */
	public function getApprovedCount($month = null) {
		if (empty($month)) {
			$month = date('m');
		}
		$monthStart = date("Y-$month-01");
		$monthEnd = date("Y-m-t 23:59:00", strtotime($monthStart)); //get last day of any month
		$approvedId = ClassRegistry::init('UwStatus')->field('id', ['name' => 'Approved']);
		$count = ClassRegistry::init('UwStatusMerchantXref')->find('count', [
				'conditions' => [
					'uw_status_id' => $approvedId,
					'datetime >=' => $monthStart,
					'datetime <=' => $monthEnd,
				]
			]);
		return $count;
	}

/**
 * getInstalledCount()
 * Counts the total number of merchants installed current month or the month in param
 *
 * @param integer $month a two digit month. If omitted will use current month.
 * @return integer
 */
	public function getInstalledCount($month = null) {
		if (empty($month)) {
			$month = date('m');
		}
		$monthStart = date("Y-$month-01");
		$monthEnd = date("Y-m-t 23:59:00", strtotime($monthStart)); //get last day of any month;
		$count = ClassRegistry::init('TimelineEntry')->find('count', [
				'conditions' => [
					'timeline_item_id' => TimelineItem::GO_LIVE_DATE,
					'timeline_date_completed >=' => $monthStart,
					'timeline_date_completed <=' => $monthEnd,
				]
			]);
		return $count;
	}

/**
 * getInUwCount()
 * Counts the total number of merchants waiting in underwriting current month. or the month in param
 *
 * @param integer $month a two digit month. If omitted will use current month.
 * @return integer
 */
	public function getInUwCount($month = null) {
		if (empty($month)) {
			$month = date('m');
		}
		$Merchant = ClassRegistry::init('Merchant');
		$UwStatus = ClassRegistry::init('UwStatus');
		$receivedStatusId = $UwStatus->field('id', ['name' => 'Received']);
		$approvedStatusId = $UwStatus->field('id', ['name' => 'Approved']);
		$monthStart = date("Y-$month-01");
		$monthEnd = date("Y-m-t 23:59:00", strtotime($monthStart)); //get last day of any month
		$count = $Merchant->find('count', [
				'conditions' => [
					'UwStsMerchX.uw_status_id' => $receivedStatusId,
					'UwStsMerchX.datetime >=' => $monthStart,
					'UwStsMerchX.datetime <=' => $monthEnd,
					"Merchant.id NOT IN (SELECT merchant_id FROM uw_status_merchant_xrefs where uw_status_id = '$approvedStatusId' AND datetime >='$monthStart' and datetime <= '$monthEnd')"
				],
				'joins' => [
					[
						'table' => 'uw_status_merchant_xrefs',
						'alias' => 'UwStsMerchX',
						'type' => 'INNER',
						'conditions' => [
							'Merchant.id = UwStsMerchX.merchant_id',
						]
					]
				]
			]);
		return $count;
	}
}