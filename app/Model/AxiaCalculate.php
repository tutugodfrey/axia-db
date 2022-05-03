<?php

App::uses('AppModel', 'Model');

class AxiaCalculate extends AppModel {

	const STRUCT_ERR1 = "Function param elements must exactly match hinted structure in function definition";

/**
 * Use Table
 *
 * @var bool $useTable
 * @access public
 */
	public $useTable = false;

/**
 * sumKeyValPairs - sums the numeric values of two or three given key strings and stores the result in the given holder key string.
 * This function modifies the passed-by-reference data unless
 * 
 * @param array &$data reference containing keys 1 and 2
 * @param string $key1 name of the key that holds value to add 
 * @param string $key2 name of the key that holds value to add
 * @param string $holderKey name of the key where result should be stored in original data param
 * @param string $Key3 optional name of key that holds an additional value to add
 * @return void
 */
	public static function sumKeyValPairs(&$data, $key1, $key2, $holderKey, $Key3 = '') {
		if (empty($data)) {
			return;
		}

		$tmpArr = self::_setSingleDimAssoc($data);
		$key = Hash::get($tmpArr, 'top_dim_key');

		if (!array_key_exists($key1, $tmpArr) || !array_key_exists($key2, $tmpArr)) {
			return;
		}

		if (!empty($key)) {
			unset($tmpArr['top_dim_key']);
			$tmpArr[$holderKey] = $tmpArr[$key1] + $tmpArr[$key2] + Hash::get($tmpArr, $Key3);
			$data[$key] = $tmpArr;
		} else {
			$data[$holderKey] = $data[$key1] + $data[$key2] + Hash::get($data, $Key3);
		}
	}

/**
 * setMerchStmtFee - merchant statement fee
 *
 * From Merchant Pricings, Products & Services + Gateway Access Fee + Wireless Access Fee
 * 
 * @param array $mId a merchant id
 * @return float equals Merchant Pricing's statement_fee + gateway_access_fee + wireless_access_fee
 * @throws \InvalidArgumentException
 */
	public static function setMerchStmtFee($mId) {
		if (empty($mId)) {
			throw new InvalidArgumentException('Argument 1 cannot be empty');
		}
		$MerchantPricing = ClassRegistry::init('MerchantPricing');

		$data = $MerchantPricing->find('first', [
					'conditions' => ['MerchantPricing.merchant_id' => $mId],
					'fields' => ['statement_fee', 'gateway_access_fee', 'wireless_access_fee']
					]
				);
		return array_sum(Hash::extract($data, '{s}.{s}'));
	}

/**
 * _checkParams
 * 
 * @param array &$data to check that meets required structure
 *		-Max dimensions = 2
 * 		-Two dimensinal structures must have a single-entry at the top dimemsion
 * @return void
 * @throws \InvalidArgumentException
 */
	protected static function _checkParams(&$data) {
		if (Hash::maxDimensions($data) >= 3 || (Hash::maxDimensions($data) === 2 && count($data) > 1)) {
			throw new InvalidArgumentException('Array of values must be maximun two dimensions with one entry as the top dimenssion');
		}
	}

/**
 * _setSingleDimAssoc()
 * Sigle-entry two dimensional associative array params are reduced to a single dimenssion. 
 * Additionally the name of the string key that was removed from the top dimension its appended to the result
 * 
 * @param array &$data Sigle-entry two dimensional associative array
 * @return array the original array without the top dimension plus the name of top_dim_key that was removed appended to it.
 *				Or the original array if 
 */
	protected static function _setSingleDimAssoc(&$data) {
		//Check param
		self::_checkParams($data);

		if ((Hash::maxDimensions($data) === 2)) {
			// Make it single dimentional
			$key = array_keys($data)[0];
			$result = Hash::extract($data, '{s}');
			$result = array_pop($result);
			$result['top_dim_key'] = $key;
			return $result;
		}
		return $data;
	}

/**
 * userGrossProfit()
 * User Gross Proffit Calculation
 * 
 * @param array $amounts specified astructure is required. Percentages should not be in decimal form.
 * @return number Negative or positive values are allowed in this calculation
 * @throws \InvalidArgumentException is array argument structure does not match hinted structure
 */
	public function uGrossPrft(
			array $amounts = array(
				"volume" => 0,
				"m_rate" => 0.00,
				"user_rate" => 0.00,
				"user_risk_pct" => 0.00,
				"num_items" => 0,
				"m_item_fee" => 0.00,
				"user_pi" => 0.00
			)
		) {
		if (count($amounts) != 7 || !array_key_exists('volume', $amounts) || !array_key_exists('m_rate', $amounts) || !array_key_exists('user_rate', $amounts) || !array_key_exists('user_risk_pct', $amounts) ||
					!array_key_exists('num_items', $amounts) || !array_key_exists('m_item_fee', $amounts) || !array_key_exists('user_pi', $amounts)) {
			throw new InvalidArgumentException(self::STRUCT_ERR1);
		}
		/*Set decimal form percentage*/
		$amounts['m_rate'] /= 100;
		$amounts['user_rate'] /= 100;
		$amounts['user_risk_pct'] /= 100;
		return ($amounts['volume'] * ($amounts['m_rate'] - ($amounts['user_rate'] + $amounts['user_risk_pct']))) +
				($amounts['num_items'] * ($amounts['m_item_fee'] - $amounts['user_pi']));
	}

/**
 * multipleAmnt()
 * Rep/PartnerRep Multiple amount Calculation = (<Rep or PartnerRep> Gross Profit - Partner Profit - Referrer Profit - Reseller Profit) * <Rep or PartnerRep> Percent of Gross * Multiple
 * 
 * @param array $terms specified astructure is required. Percentages should not be in decimal form.
 * @return number Negative or positive values are allowed in this calculation
 * @throws \InvalidArgumentException if array argument structure does not match hinted structure
 */
	public function multipleAmnt(
			array $terms = array(
				"rep_gross_prft" => 0,
				"p_profit_amount" => 0.00,
				"ref_profit_amount" => 0.00,
				"res_profit_amount" => 0.00,
				"rep_pct_of_gross" => 0,
				"multiple" => 0.00
			)
		) {
		if (count($terms) != 6) {
			throw new InvalidArgumentException(self::STRUCT_ERR1);
		}

		if (count($terms) != 6 || !array_key_exists('rep_gross_prft', $terms) || !array_key_exists('p_profit_amount', $terms) || !array_key_exists('ref_profit_amount', $terms) ||
			!array_key_exists('res_profit_amount', $terms) || !array_key_exists('rep_pct_of_gross', $terms) || !array_key_exists('multiple', $terms)) {
			throw new InvalidArgumentException(self::STRUCT_ERR1);
		}
		/*Set decimal form percentage*/
		$terms['rep_pct_of_gross'] /= 100;
		$terms['multiple'] /= 100;

		return ($terms['rep_gross_prft'] - $terms['p_profit_amount'] - $terms['ref_profit_amount'] - $terms['res_profit_amount']) * $terms['rep_pct_of_gross'] * $terms['multiple'];
	}

/**
 * thirdPartyProfitAmnt
 * Calculates profit amount for Partner Referer, reseler. 
 * thirdPartyProfitAmnt = User Gross Profit * Percent of Gross * Residual % 
 * 
 * @param array $amounts specified astructure is required. Percentages should not be in decimal form.
 * @return number Negative or positive values are allowed in this calculation
 * @throws \InvalidArgumentException if array argument structure does not match hinted structure
 */
	public function thirdPartyProfitAmnt(
		array $amounts = array(
			"u_gross_profit" => 0,
			"u_pct_of_gross" => 0,
			"u_residual_pct" => 0,
			)) {
		if (!$this->arrayIsNumeric($amounts) || count($amounts) != 3) {
			throw new InvalidArgumentException("All elements in array param must be numbers and must contain 3 elements");
		}
		/*Set decimal form of percentages*/
		$amounts['u_pct_of_gross'] /= 100;
		$amounts['u_residual_pct'] /= 100;

		return array_product($amounts);
	}

/**
 * multSumEqType1
 * Calculates y in y = (a * b) + c and returns y
 * 
 * @param array $data associative array containing $keys that correspond to each $key parameter
 * @param string $keyA array key containing a value to multiply with keyB
 * @param string $keyB array key containing a value to multiply with keyA
 * @param string $keyC array key containing a value to add to the product of A and B
 * @return integer|double (a * b) + c
 * @throws OutOfBoundsException
 */
	public function multSumEqType1($data, $keyA, $keyB, $keyC) {
		if (!array_key_exists($keyA, $data) || !array_key_exists($keyB, $data) || !array_key_exists($keyC, $data)) {
			throw new OutOfBoundsException("Key not found in data array");
		}
		return ($data[$keyA] * $data[$keyB]) + $data[$keyC];
	}

/**
 * multSumEqType2
 * Calculates y in y = a * (b - c) and returns y
 * 
 * @param array $data associative array containing $keys that correspond to each $key parameter
 * @param string $keyA array key containing a value to multiply by
 * @param string $keyB array key containing a value for b in term b - c
 * @param string $keyC array key containing a value for c in term b - c
 * @return integer|double a * (b - c)
 */
	public function multSumEqType2($data, $keyA, $keyB, $keyC) {
		return Hash::get($data, $keyA) * (Hash::get($data, $keyB) - Hash::get($data, $keyC));
	}

/**
 * pmntFusionDevicesMonthly
 * Calculates:
 *		(# of Standard Devices * (Merchant Standard Device Fee - User Standard Device Cost))
 *		+ (# of VP2PE Devices * (Merchant VP2PE Device Fee - User VP2PE Device Cost))
 *		+ (# of PFCC Devices * (Merchant PFCC Device Fee - User PFCC Device Cost))
 *		+ (# of VP2PE & PFCC Devices * (Merchant VP2PE & PFCC Device Fee - User VP2PE & PFCC Device Cost))
 * 
 * @param array $pFusionData Merchant's payment fusion data in a one dimention array
 * @param array $pFusionUserCosts User's payment_fusion_rep_cost data in a one dimention array
 * @return integer|double a * (b - c)
 */
	public function pmntFusionDevicesMonthly($pFusionData, $pFusionUserCosts) {
		if (array_key_exists('PaymentFusion', $pFusionData)) {
			$pFusionData = $pFusionData['PaymentFusion'];
		}
		if (array_key_exists('PaymentFusionRepCost', $pFusionUserCosts)) {
			$pFusionUserCosts = $pFusionUserCosts['PaymentFusionRepCost'];
		}
		//All the leading multiplier terms are in this var, therefore when empty the calculation will allways be zero
		if (empty($pFusionData)) {
			return 0;
		}
		$allData = array_merge($pFusionData, $pFusionUserCosts);
		$result = $this->multSumEqType2($allData, 'standard_num_devices', 'standard_device_fee', 'standard_device_cost')
					+ $this->multSumEqType2($allData, 'vp2pe_num_devices', 'vp2pe_device_fee', 'vp2pe_device_cost')
					+ $this->multSumEqType2($allData, 'pfcc_num_devices', 'pfcc_device_fee', 'pfcc_device_cost')
					+ $this->multSumEqType2($allData, 'vp2pe_pfcc_num_devices', 'vp2pe_pfcc_device_fee', 'vp2pe_pfcc_device_cost');
		return $result;
	}

/**
 * uGrossPrftType2
 * Calculates a user Gros Profit using the following formula
 *
 *  (Volume * (Merchant Rate  - (User Cost % + User Risk Assessment %))) + (Items * User Per Item Cost) + (Merchant Monthly Fee - User Monthly Cost)
 * 
 * @param array $terms array containing each of the terms or amounts in the formula above each assigned to the array keys as follows:
 * [
 *	'vol' => float,
 *	'm_rate' => float, percent should be in decimal form
 *	'u_cost_pct' => float, percent should be in decimal form
 *	'u_risk_pct' => float, percent should be in decimal form
 *	'm_items' => int,
 *	'u_pi_cost' => float,
 *	'm_montly' => float,
 *	'u_monthly'
 * ]
 *
 * @return integer|double 
 * @throws OutOfBoundsException
 */
	public function uGrossPrftType2(array $terms = array(
			'vol' => 0.00,
			'm_rate' => 0.00,
			'u_cost_pct' => 0.00,
			'u_risk_pct' => 0.00,
			'm_items' => 0,
			'u_pi_cost' => 0.00,
			'm_monthly' => 0.00,
			'u_monthly' => 0.00
		)) {
		$checkKeys = ['vol', 'm_rate', 'u_cost_pct', 'u_risk_pct', 'm_items', 'u_pi_cost', 'm_monthly', 'u_monthly'];
		foreach ($checkKeys as $key) {
			if (!array_key_exists($key, $terms)) {
				throw new OutOfBoundsException("Expected key not found in array parameter");
			}
		}

		return ($terms['vol'] * ($terms['m_rate'] - ($terms['u_cost_pct'] + $terms['u_risk_pct']))) + ($terms['m_items'] * $terms['u_pi_cost']) + ($terms['m_monthly'] - $terms['u_monthly']);
	}

/**
 * uGrossPrftType3()
 * User Gross Proffit Calculation involving merchant discount item fee
 * 
 * @param array $amounts specified astructure is required, percentages should not be in decimal form.
 * @return number Negative or positive values are allowed in this calculation
 * @throws \InvalidArgumentException is array argument structure does not match hinted structure
 */
	public function uGrossPrftType3(
			array $amounts = array(
				"volume" => 0,
				"m_rate" => 0.00,
				"user_rate" => 0.00,
				"user_risk_pct" => 0.00,
				"num_items" => 0,
				"m_disc_item_fee" => 0.00,
			)
		) {
		if (count($amounts) != 6 || !isset($amounts['volume']) || !isset($amounts['m_rate']) || !isset($amounts['user_rate']) || !isset($amounts['user_risk_pct']) ||
					!isset($amounts['num_items']) || !isset($amounts['m_disc_item_fee'])) {
			throw new InvalidArgumentException(self::STRUCT_ERR1);
		}
		/*Set decimal form percentage*/
		$amounts['m_rate'] /= 100;
		$amounts['user_rate'] /= 100;
		$amounts['user_risk_pct'] /= 100;
		return ($amounts['volume'] * ($amounts['m_rate'] - ($amounts['user_rate'] + $amounts['user_risk_pct']))) + ($amounts['num_items'] * $amounts['m_disc_item_fee']);
	}

/**
 * uGrossPrftType4()
 * User Gross Proffit Calculation
 * 
 * @param array $amounts specified astructure is required. Percentages should not be in decimal form.
 * @return number Negative or positive values are allowed in this calculation
 * @throws \InvalidArgumentException is array argument structure does not match hinted structure
 */
	public function uGrossPrftType4(
			array $amounts = array(
				"num_items" => 0,
				"m_item_fee" => 0.00,
				"user_pi" => 0.00,
				"volume" => 0,
				"m_rate" => 0.00,
				"m_monthly" => 0.00,
				"u_monthly" => 0.00,
			)
		) {
		if (count($amounts) != 7 || !array_key_exists('num_items', $amounts) || !array_key_exists('m_item_fee', $amounts) || !array_key_exists('user_pi', $amounts) || !array_key_exists('volume', $amounts) ||
					!array_key_exists('m_rate', $amounts) || !array_key_exists('m_monthly', $amounts) || !array_key_exists('u_monthly', $amounts)) {
			throw new InvalidArgumentException(self::STRUCT_ERR1);
		}
		/*Set decimal form percentage*/
		$amounts['m_rate'] /= 100;
		return ($amounts['num_items'] * ($amounts['m_item_fee'] - $amounts['user_pi'])) + ($amounts['volume'] * $amounts['m_rate']) + ($amounts['m_monthly'] - $amounts['u_monthly']);
	}
}
