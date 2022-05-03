<?php
App::uses('UserParameter', 'Model');
App::uses('AxiaTestCase', 'Test');

class UserParameterTestCase extends AxiaTestCase {

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array();

/**
 * Start Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function startTest($method) {
		parent::startTest($method);
		$this->UserParameter = ClassRegistry::init('UserParameter');
	}

/**
 * End Test callback
 *
 * @param string $method
 * @return void
 * @access public
 */
	public function endTest($method) {
		parent::endTest($method);
		unset($this->UserParameter);
		ClassRegistry::flush();
	}

	public function testValidValue() {
		$this->assertFalse($this->UserParameter->validValue(array()));
		$this->UserParameter->set(array(
			'user_parameter_type_id' => UserParameterType::ENABLED_FOR_REP,
		));
		$this->assertTrue($this->UserParameter->validValue(array(1)));
		$this->assertFalse($this->UserParameter->validValue(array(2)));
		$this->assertFalse($this->UserParameter->validValue(array('string')));
		$this->UserParameter->set(array(
			'user_parameter_type_id' => UserParameterType::PCT_OF_GROSS,
		));
		$expected = 'Must be a valid percentage value with a maximum precision of 3';
		$this->assertEquals($expected, $this->UserParameter->validValue(array('string')));
		$this->assertEquals($expected, $this->UserParameter->validValue(array(101)));
		$this->assertEquals($expected, $this->UserParameter->validValue(array(-5)));
		$this->assertTrue($this->UserParameter->validValue(array(100)));
	}


/**
 * testGetEnabledProductsList
 * 
 * @return void
 */
	public function testGetEnabledProductsList() {
		
		$this->assertEmpty($this->UserParameter->getEnabledProductsList('6570e7dc-a4fc-444c-8929-337a34627ad4'));
		$this->UserParameter->updateAll(
			['user_parameter_type_id' => "'a8268c33-2e9f-4bc0-b482-82fdbcc0cd69'", 'value' => 1],
			['user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4']);
		$expected = ['e6d8f040-9963-4539-ab75-3e19f679de16' => 'Visa Sales'];
		$actual = $this->UserParameter->getEnabledProductsList('6570e7dc-a4fc-444c-8929-337a34627ad4');
		$this->assertSame($expected, $actual);
	}
}
