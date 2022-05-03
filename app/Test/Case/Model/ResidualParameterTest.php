<?php
App::uses('ResidualParameter', 'Model');
App::uses('AxiaTestCase', 'Test');

class ResidualParameterTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ResidualParameter = ClassRegistry::init('ResidualParameter');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ResidualParameter);

		parent::tearDown();
	}

/**
 * testValidValue method
 *
 * @return void
 */
	public function testValidValue() {
		$data = [
			'residual_parameter_type_id' => '8586ddc9-6409-4801-a997-525baa039c2b', //rep %
			'value' => 'string'
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['value must be a number']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an invalid percentage
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '8586ddc9-6409-4801-a997-525baa039c2b', //rep %
			'value' => 101
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['Must be a valid percentage value with a maximum precision of 3']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an invalid percentage
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '8586ddc9-6409-4801-a997-525baa039c2b', //rep %
			'value' => .1011
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['Must be a valid percentage value with a maximum precision of 3']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an invalid percentage
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '8586ddc9-6409-4801-a997-525baa039c2b', //rep %
			'value' => -5
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['Must be a valid percentage value with a maximum precision of 3']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an invalid multiple
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '75adbe8c-7a0c-4ad3-92aa-faa3b234a003', //rep multiple
			'value' => 'invalid multiple'
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['value must be a number']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an invalid multiple
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '75adbe8c-7a0c-4ad3-92aa-faa3b234a003', //rep multiple
			'value' => -5
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['Invalid value!']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an invalid multiple
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '75adbe8c-7a0c-4ad3-92aa-faa3b234a003', //rep multiple
			'value' => 0.12345
		];
		$this->ResidualParameter->set($data);
		$this->assertFalse($this->ResidualParameter->validates());
		$expected = [
			'value' => ['Invalid value!']
		];
		$actual = $this->ResidualParameter->validationErrors;
		$this->assertSame($expected, $actual);

		//Test with an valid percentage
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '8586ddc9-6409-4801-a997-525baa039c2b', //rep %
			'value' => 100
		];
		$this->ResidualParameter->set($data);
		$this->assertTrue($this->ResidualParameter->validates());

		//Test with an valid percentage
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '8586ddc9-6409-4801-a997-525baa039c2b', //rep %
			'value' => 100
		];
		$this->ResidualParameter->set($data);
		$this->assertTrue($this->ResidualParameter->validates());

		//Test with an valid multiple
		$this->ResidualParameter->clear();
		$data = [
			'residual_parameter_type_id' => '75adbe8c-7a0c-4ad3-92aa-faa3b234a003', //rep %
			'value' => 2
		];
		$this->ResidualParameter->set($data);
		$this->assertTrue($this->ResidualParameter->validates());
	}
}
