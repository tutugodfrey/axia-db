<?php
App::uses('UserParameterType', 'Model');
App::uses('AxiaTestCase', 'Test');

class UserParameterTypeTestCase extends AxiaTestCase {

	const USER_ID = '00ccf87a-4564-4b95-96e5-e90df32c46c1';

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
		$this->UserParameterType = ClassRegistry::init('UserParameterType');
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
		unset($this->UserParameterType);
		ClassRegistry::flush();
	}

/**
 * Test validation rules
 *
 * @return void
 * @access public
 */
	public function testValidation() {
		$data = array(
			'id' => '81dbd2e6-21a2-4958-bd85-815c70f7d90a',
			'name' => 'Do Not Display',
			'order' => 1,
			'type' => 0,
			'value_type' => 0
		);

		$this->assertValid($this->UserParameterType, $data);
		// Test mandatory fields
		$data = array('UserParameterType' => array('id' => 'new-id'));
		$expectedErrors = array(); // TODO Update me with mandatory fields
		$this->assertValidationErrors($this->UserParameterType, $data, $expectedErrors);

		$this->assertValidationErrors($this->UserParameterType, $data, $expectedErrors);
	}

	public function testGetHeaders() {
		$this->UserParameterType = $this->_mockModel(
			'UserParameterType',
			['_getAssociatedUsers'],
			[
				'_getAssociatedUsers' => [
					'matcher' => $this->once(),
					'value' => [
						'AssociatedUser' => [
							'00ccf87a-4564-4b95-96e5-e90df32c46c1', //1 for each merchant acquirer for myself
							'00ccf87a-4564-4b95-96e5-e90df32c46c1',
							'00ccf87a-4564-4b95-96e5-e90df32c46c1',
							'00ccf87a-4564-4b95-96e5-e90df32c46c1',
							'00ccf87a-4564-4b95-96e5-e90df32c46c1',
							'00ccf87a-4564-4b95-96e5-e90df32c46c1',
						],
					],
				]
			]
		);
		$headers = $this->UserParameterType->getHeaders(self::USER_ID, '00000000-0000-0000-0000-000000000001');
		$this->assertCount(10, $headers);
		$expectedHeaderTypes = [
			'Do Not Display',
			'Tier Products',
			'Enabled for Rep',
			//for each merchant acquirer
			'% of Gross Profit',
			'% of Gross Profit',
			'% of Gross Profit',
			'% of Gross Profit',
			'% of Gross Profit',
			'% of Gross Profit',
			'% of Gross Profit',
		];
		$this->assertEquals($expectedHeaderTypes, Hash::extract($headers, '{n}.UserParameterType.name'));
	}
}
