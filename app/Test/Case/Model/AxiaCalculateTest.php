<?php
App::uses('AxiaCalculate', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AxiaCalculate Test Case
 *
 */
class AxiaCalculateTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AxiaCalculate = ClassRegistry::init('AxiaCalculate');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AxiaCalculate);

		parent::tearDown();
	}

/**
 * testSumKeyValPairs method
 *
 * @return void
 */
	public function testSumKeyValPairs() {
		//Test data two dimensions
		$tstData = [
			'ModelName' => [
				'bet_extra_pct' => .10,
				'm_rate_pct' => .10
			]
		];

		$this->AxiaCalculate->sumKeyValPairs($tstData, 'm_rate_pct', 'bet_extra_pct', 'm_rate_pct');
		$this->assertEquals($tstData['ModelName']['m_rate_pct'], .20);

		//Test data one dimension
		$tstData = [
				'bet_extra_pct' => .10,
				'm_rate_pct' => .10
			];
		$this->AxiaCalculate->sumKeyValPairs($tstData, 'm_rate_pct', 'bet_extra_pct', 'm_rate_pct');
		$this->assertEquals($tstData['m_rate_pct'], .20);
	}

/**
 * testSumKeyValPairs3Keys method
 *
 * @return void
 */
	public function testSumKeyValPairs3Keys() {
		//Test data two dimensions
		$tstData = [
			'ModelName' => [
				'u_pi_cost' => .10,
				'm_wireless_pi_cost' => .20,
				'u_gateway_pi_cost' => .30
			]
		];
		// In this case we want the sum to be u_pi_cost = u_pi_cost + m_wireless_pi_cost + u_gateway_pi_cost
		$this->AxiaCalculate->sumKeyValPairs($tstData, 'u_pi_cost', 'm_wireless_pi_cost', 'u_pi_cost', 'u_gateway_pi_cost');
		$this->assertEquals($tstData['ModelName']['u_pi_cost'], .60);

		//Test data one dimension
		$tstData = [
				'u_pi_cost' => .10,
				'm_wireless_pi_cost' => .20,
				'u_gateway_pi_cost' => .30
			];
		$this->AxiaCalculate->sumKeyValPairs($tstData, 'u_pi_cost', 'm_wireless_pi_cost', 'u_pi_cost', 'u_gateway_pi_cost');
		$this->assertEquals($tstData['u_pi_cost'], .60);
	}

/**
 * testSumKeyValPairsKeyExcluded
 * When excluding a required key in the $tstData that data should not be touched/modified
 *
 * @return void
 */
	public function testSumKeyValPairsKeyExcluded() {
		//Test data two dimensions
		$tstData = [
			'ModelName' => [
				'bet_extra_pct' => .10
			]
		];

		$expected = $tstData;

		$this->AxiaCalculate->sumKeyValPairs($tstData, 'm_rate_pct', 'bet_extra_pct', 'm_rate_pct');
		$this->assertEquals($expected, $tstData);

		//Test data one dimension
		$tstData = [
				'bet_extra_pct' => .10
			];
		$expected = $tstData;
		$this->AxiaCalculate->sumKeyValPairs($tstData, 'm_rate_pct', 'bet_extra_pct', 'm_rate_pct');
		$this->assertEquals($expected, $tstData);
	}

/**
 * testSumKeyValPairsDimExceptionThrown
 * test exception thrown when arrays with dimmensions are greater than two are passed
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Array of values must be maximun two dimensions
 * @return void
 */
	public function testSumKeyValPairsDimExceptionThrown() {
		$tstData = [
			'ModelName' => [
				'AnotherModelName' => ['stuff'],
				'bet_extra_pct' => .10,
				'm_rate_pct' => .10
			]
		];
		$this->AxiaCalculate->sumKeyValPairs($tstData, 'm_rate_pct', 'bet_extra_pct', 'm_rate_pct');
	}
/**
 * testSumKeyValPairsMultipleEntiesExceptionThrown
 * test exception thrown when arrays with dimmensions are greater than two are passed
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Array of values must be maximun two dimensions
 * @return void
 */
	public function testSumKeyValPairsMultipleEntiesExceptionThrown() {
		//Test data 2 dimensions but multiple enties
		$tstData = [
			'ModelName' => [
				'bet_extra_pct' => .10,
				'm_rate_pct' => .10
			],
			'AnotherModelName' => [
				'stuff'
			],
		];
		$this->AxiaCalculate->sumKeyValPairs($tstData, 'm_rate_pct', 'bet_extra_pct', 'm_rate_pct');
	}

/**
 * testSetMerchStmtFee method
 *
 * @return void
 */
	public function testSetMerchStmtFee() {
		$actual = $this->AxiaCalculate->setMerchStmtFee('3bc3ac07-fa2d-4ddc-a7e5-680035ec1040');
		$this->assertNotEquals($actual, 0);
		$this->assertEquals($actual, 25);
		$this->assertGreaterThan(0, $actual);
	}

/**
 * testSetMerchStmtFeeExceptionThrown
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Argument 1 cannot be empty
 * @return void
 */
	public function testSetMerchStmtFeeExceptionThrown() {
				$this->AxiaCalculate->setMerchStmtFee('');
	}

/**
 * testUGrossPrftExceptionThrown
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage param elements must exactly match hinted structure
 * @return void
 */
	public function testUGrossPrftExceptionThrown() {
		$amounts = array(
			"invalid data" => 0,
			"m_rate" => 0.00,
			"user_rate" => 0.00,
			"user_risk_pct" => 0.00,
			"num_items" => 0,
			"m_item_fee" => null,
			"user_pi" => 0.00
		);
		$this->AxiaCalculate->uGrossPrft($amounts);
	}
/**
 * testUGrossPrft
 *
 * @return void
 */
	public function testUGrossPrft() {
		$amounts = array(
				"volume" => 100,
				"m_rate" => 10,
				"user_rate" => 50,
				"user_risk_pct" => 10,
				"num_items" => 10,
				"m_item_fee" => 0.50,
				"user_pi" => 0.10
			);

		$actual = $this->AxiaCalculate->uGrossPrft($amounts);
		$expected = (100 * (.1 - (.5 + .1))) + (10 * (.5 - .1 ));
		$this->assertEquals($actual, $expected);

		$amounts = array(
				"volume" => 0,
				"m_rate" => 0,
				"user_rate" => 0,
				"user_risk_pct" => 0,
				"num_items" => 10,
				"m_item_fee" => 0.50,
				"user_pi" => 0.10
			);

		$actual = $this->AxiaCalculate->uGrossPrft($amounts);
		$expected = (10 * (.5 - .1 ));
		$this->assertEquals($actual, $expected);

		$amounts = array(
			"volume" => 100,
			"m_rate" => 10,
			"user_rate" => 50,
			"user_risk_pct" => 10,
			"num_items" => 0,
			"m_item_fee" => 0,
			"user_pi" => 0
		);

		$actual = $this->AxiaCalculate->uGrossPrft($amounts);
		$expected = (100 * (.1 - (.5 + .1)));
		$this->assertEquals($actual, $expected);
	}
/**
 * testMultipleAmntExceptionThrown
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage param elements must exactly match hinted structure
 * @return void
 */
	public function testMultipleAmntExceptionThrown() {
		$terms = array(
			"rep_gross_prft" => 0,
			"p_profit_amount" => 0.00,
			"ref_profit_amount" => 0.00,
			"res_profit_amount" => 0.00,
			"rep_pct_of_gross" => 0,
			"invalid_key" => 0.00
		);
		$this->AxiaCalculate->multipleAmnt($terms);
	}
/**
 * testMultipleAmnt
 *
 * @return void
 */
	public function testMultipleAmnt() {
		$terms = array(
				"rep_gross_prft" => 21.50,
				"p_profit_amount" => 10.50,
				"ref_profit_amount" => 11.00,
				"res_profit_amount" => 12.00,
				"rep_pct_of_gross" => 10,
				"multiple" => 2
			);

		$actual = $this->AxiaCalculate->multipleAmnt($terms);
		$expected = (21.5 - 10.5 - 11 - 12) * .10 * .02; // < 0
		$this->assertLessThan(0, $actual);
		$this->assertEquals($actual, $expected);

		$terms = array(
				"rep_gross_prft" => 50.5,
				"p_profit_amount" => 10.00,
				"ref_profit_amount" => 10.00,
				"res_profit_amount" => 10.50,
				"rep_pct_of_gross" => 50,
				"multiple" => 1
			);

		$actual = $this->AxiaCalculate->multipleAmnt($terms);
		$expected = (50.5 - 10 - 10 - 10.5) * .5 * .01; // > 0
		$this->assertGreaterThan(0, $actual);
		$this->assertEquals($actual, $expected);

		$terms = array(
				"rep_gross_prft" => 10.5,
				"p_profit_amount" => 0.00,
				"ref_profit_amount" => 1.00,
				"res_profit_amount" => 1.00,
				"rep_pct_of_gross" => 50,
				"multiple" => 2
			);

		$actual = $this->AxiaCalculate->multipleAmnt($terms);
		$expected = (10.5 - 1 - 1) * .5 * .02; // > 0
		$this->assertGreaterThan(0, $actual);
		$this->assertEquals($actual, $expected);
	}


/**
 * testThirdPartyProfitAmntException1Thrown
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage All elements in array param must be numbers
 * @return void
 */
	public function testThirdPartyProfitAmntException1Thrown() {
		$terms = array(
			"u_gross_profit" => "not a number",
			"u_pct_of_gross" => 0,
			"u_residual_pct" => 0,
		);
		$this->AxiaCalculate->thirdPartyProfitAmnt($terms);
	}
/**
 * testThirdPartyProfitAmntException2Thrown
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage must contain 3 elements
 * @return void
 */
	public function testThirdPartyProfitAmntException2Thrown() {
		$terms = array(
			"u_pct_of_gross" => 0,
			"u_residual_pct" => 0,
		);
		$this->AxiaCalculate->thirdPartyProfitAmnt($terms);
	}

/**
 * testMultSumEqType1($data, $keyA, $keyB, $keyC)
 *
 * @param array $data data provided
 * @param double $expected expected calculation
 * @dataProvider dataForTestMultSumEqType1
 * @return void
 */
	public function testMultSumEqType1($data, $expected) {
		$actual = $this->AxiaCalculate->multSumEqType1($data, "A", "B", "C");
		$this->assertEquals($expected, $actual);
	}

/**
 * Provider for testMultSumEqType1
 *
 * @return array
 */
	public function dataForTestMultSumEqType1() {
		return [
			[["A" => "5", "B" => "5", "C" => "5"], (5 * 5) + 5],
			[["A" => ".25", "B" => ".25", "C" => ".99"], (.25 * .25) + .99],
			[["A" => ".095", "B" => ".099", "C" => ".099"], (.095 * .099) + .099],
		];
	}

/**
 * testThirdPartyProfitAmnt
 *
 * @return void
 */
	public function testThirdPartyProfitAmnt() {
		$amounts = array(
			"u_gross_profit" => 2,
			"u_pct_of_gross" => 2,
			"u_residual_pct" => 2,
		);
		$actual = $this->AxiaCalculate->thirdPartyProfitAmnt($amounts);
		$this->assertEquals($actual, 2 * .02 * .02);

		$amounts = array(
			"u_gross_profit" => -2,
			"u_pct_of_gross" => 2,
			"u_residual_pct" => 2,
		);
		$actual = $this->AxiaCalculate->thirdPartyProfitAmnt($amounts);
		$this->assertEquals($actual, -2 * .02 *.02);
	}

}
