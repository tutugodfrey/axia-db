<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('RepProfileHelper', 'View/Helper');
App::uses('AxiaTestCase', 'Test');

/**
 * RepProfileHelper Test Case
 *
 */
class RepProfileHelperTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$settings = array(
			'Type.id' => 'UserParameterType.id',
			'Type.type' => 'UserParameterType.type',
			'Type.name' => 'UserParameterType.name',
			'Type.value_type' => 'UserParameterType.value_type',
			'valueTypeCheckbox' => 0,
			'typeSimple' => 0,
			'typeManualOverride' => 2,
			'valueModelAlias' => 'UserParameter',
		);
		$this->RepProfile = new RepProfileHelper($View, $settings);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->RepProfile);

		parent::tearDown();
	}

/**
 * testDefaultButtons method
 *
 * @return void
 */
	public function testView() {
		$result = $this->RepProfile->view(array(), array());
		$expectedTableTag = array(
			'tag' => 'table',
			'attributes' => array('class' => 'toggle-table'),
			'descendant' => array(
				array(
					'tag' => 'th',
					'content' => 'User Parameters',
				),
			)
		);
		$this->assertTag($expectedTableTag, $result);

		$userParamHeader = array(
			//simple checkbox
			array(
				'UserParameter' => false,
				'UserParameterType' => array(
					'id' => 1,
					'name' => 'Do Not Display',
					'order' => 1,
					'type' => 0,
					'value_type' => 0,
				)
			),
		);
		$productServiceTypes = array(
			0 => array(
				'ProductServiceType' => array(
					'id' => 'uuid-0',
					'products_services_type_old_id' => 'ACHP',
					'products_services_description' => 'ACH',
					'products_services_rppp' => true,
				),
			),
		);
		$result = $this->RepProfile->view($userParamHeader, $productServiceTypes);
		$expectedTableTag = array(
			'tag' => 'table',
			'attributes' => array('class' => 'toggle-table'),
			'descendant' => array(
				// we display an empty checkbox
				array(
					'tag' => 'td',
					'content' => '&#9744;',
				),
			)
		);
		$this->assertTag($expectedTableTag, $result);

	}

}
