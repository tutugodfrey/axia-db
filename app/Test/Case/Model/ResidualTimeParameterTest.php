<?php
App::uses('ResidualTimeParameter', 'Model');
App::uses('AxiaTestCase', 'Test');

class ResidualTimeParameterTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ResidualTimeParameter = ClassRegistry::init('ResidualTimeParameter');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ResidualTimeParameter);

		parent::tearDown();
	}

/**
 * testValidValue method
 *
 * @return void
 */
	public function testValidValue() {
		$this->assertFalse($this->ResidualTimeParameter->validValue([]));

		$this->ResidualTimeParameter->set(['residual_parameter_type_id' => 1]);
		$expectedError = 'Must be a valid percentage value with a maximum precision of 3';
		$this->assertEquals($expectedError, $this->ResidualTimeParameter->validValue(['string']));
		$this->assertEquals($expectedError, $this->ResidualTimeParameter->validValue([101]));
		$this->assertEquals($expectedError, $this->ResidualTimeParameter->validValue([-5]));
		$this->assertTrue($this->ResidualTimeParameter->validValue([100]));
		$this->assertTrue($this->ResidualTimeParameter->validValue([0.5]));
		$this->assertTrue($this->ResidualTimeParameter->validValue([15]));
	}
}
