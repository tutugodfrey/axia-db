<?php
App::uses('AppModel', 'Model');
App::uses('AxiaCalculate', 'Model');
App::uses('UserCostsArchive', 'Model');
/**
 * ProfitProjection Model
 *
 * @property Merchant $Merchant
 * @property ProductsServicesType $ProductsServicesType
 */
class ProfitProjection extends AppModel {


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
		),
		'ProductsServicesType' => array(
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
		)
	);

/**
 * calculate
 * Calculates profit projections basd on a specified merchant id
 * Uses the same calculation formulas for gross profit and profit as those used in Commissions and Residuals
 * 
 * @param string $merchantId a Merchant.id
 * @return Boolean true on success | false on failure
 * @throws Exception
 */
	public function updateAllProjections($merchantId) {
		if (empty($merchantId)) {
			throw new InvalidArgumentException('Merchant id parameter missing! Unable to upate projections.');
		}
		$products = $this->Merchant->MerchantPricingArchive->getArchivableProducts($merchantId);
		if (empty($products)) {
			throw new Exception('Merchant does not have any products with which to calculate profit projections.');
		}
		$UserCostsArchive = new UserCostsArchive(false, null, null, true);
		$AxiaCalculate = ClassRegistry::init('AxiaCalculate');

		$projections = [];
		foreach ($products as $productId => $name) {
			//Only process projections for these products
			if (preg_match("/(Visa|MasterCard|Discover|American Express|ACH|Payment Fusion|Credit Monthly)/i", $name) === 0) {
				continue;
			}
			//we need to compile latest pricing data regardless of any date, so using 9999 for the required year parameter.
			$merchPricingData = $this->Merchant->MerchantPricingArchive->setMpArchiveData($merchantId, $productId, 1, 9999, 'noemail@axiamed.com');
			$rawCostData = $UserCostsArchive->getUserCostArchiveData($merchantId, $productId);
			$rawCostData = $rawCostData[0];

			$userCostData = $UserCostsArchive->formatUserCostArchiveData($rawCostData);

			$pricingData = [];
			$this->Merchant->CommissionReport->setItemsVolAvgTicket($rawCostData, $pricingData, $name);

			$merchantResults['Merchant'] = $rawCostData['Merchant'];
			$merchantResults['MerchantPricingArchive'] = $merchPricingData['MerchantPricingArchive'];
			$merchantResults['UserCostsArchive'] = $userCostData;
			$rdVolume = $pricingData['m_monthly_volume'];
			$rdNumItems = $pricingData['num_items'];

			$productsServicesType = ['ProductsServicesType' => ['id' => $productId, 'products_services_description' => $name]];
			$data = $this->Merchant->ResidualReport->compileNewRecord($merchantResults, $productsServicesType, 1, 9999, $rdVolume, $rdNumItems);
			$repGrossProfit = (empty($rawCostData['Merchant']['partner_id']))? $data['rep_gross_profit'] : $data['partner_rep_gross_profit'];
			$repProfitAmnt = (empty($rawCostData['Merchant']['partner_id']))? $data['r_profit_amount'] : $data['partner_rep_profit_amount'];
			$companyProfitAmount =  $repGrossProfit - $data['partner_profit_amount'] - $data['refer_profit_amount'] - $data['res_profit_amount'] - $repProfitAmnt - $data['manager_profit_amount'] - $data['manager_profit_amount_secondary'];
			$projection = [
				'merchant_id' => $merchantId,
				'products_services_type_id' => $productId,
				'rep_gross_profit' => empty($repGrossProfit)? 0 : $repGrossProfit,
				'rep_profit_amount' => empty($repProfitAmnt)? 0 : $repProfitAmnt,
				'axia_profit_amount' => empty($companyProfitAmount)? 0 : $companyProfitAmount
			];

			$projections[] = $projection;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		if (!$this->deleteAll(['merchant_id' => $merchantId], false) || !$this->saveMany($projections)) {
			$dataSource->rollback();
			throw new Exception('Unable to update projections! Try again.');
		}
		$dataSource->commit();
		return true;
	}

/**
 * getGroupedByProductCategory
 * Returns profit projections data grouped by product category
 * The returned data has the following structure
 * 
 * 	[
 *		'Credit Category' => [
 *			ProfitProjection => [<...data...>],
 *			ProductsServicesType => [<...data...>]
 *		],
 *		'<Other Category>' => [<...data...>]
 * 
 * @param string $merchantId a Merchant.id
 * @return array
 */
	public function getGroupedByProductCategory($merchantId) {
		$data = $this->find('all', [
			'fields' => [
				'ProfitProjection.*',
				'ProductsServicesType.products_services_description',
				'ProductCategory.category_name'
			],
			'conditions' => ["ProfitProjection.merchant_id" => $merchantId],
			'joins' => [
				[
					'table' => 'products_services_types',
					'alias' => 'ProductsServicesType',
					'type' => 'LEFT',
					'conditions' => [
						"ProfitProjection.products_services_type_id = ProductsServicesType.id"
					]
				],
				[
					'table' => 'product_categories',
					'alias' => 'ProductCategory',
					'type' => 'LEFT',
					'conditions' => [
						"ProductsServicesType.product_category_id = ProductCategory.id"
					]
				],
			],
			'order' => ['ProductCategory.category_name ASC', 'ProductsServicesType.products_services_description ASC']
		]);
		$projections = Hash::combine($data, '{n}.ProductsServicesType.products_services_description', '{n}', '{n}.ProductCategory.category_name');
		//The Credit category contains the most products for that reason place it at the first element of the array
		if (!empty($projections['Credit'])) {
			$projections = array_merge(['Credit' => $projections['Credit']], $projections);
		}
		return $projections;
	}
}
