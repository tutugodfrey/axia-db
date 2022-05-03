<?php
App::uses('Adjustment', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Adjustment Test Case
 *
 */
class AdjustmentTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Adjustment = ClassRegistry::init('Adjustment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Adjustment);
		parent::tearDown();
	}

/**
 * testConstructorAndFilterArgs
 *
 * @covers Adjustment::__construct
 * @covers Adjustment::filterArgs property
 * @return void
 */
	public function testConstructorAndFilterArgs() {
		$AdjustmentModel = $this->_mockModel('Adjustment');
		$expected = [
			'from_date' => [
				'type' => 'query',
				'method' => 'dateStartConditions',
				'empty' => true,
				'defaultValue' => [
					'year' => '2015',
					'month' => '01',
				],
			],
			'end_date' => [
				'type' => 'query',
				'method' => 'dateEndConditions',
				'empty' => true,
				'defaultValue' => [
					'year' => '2015',
					'month' => '01',
				],
			],
			'user_id' => [
				'type' => 'subquery',
				'method' => 'searchByUserId',
				'field' => '"Adjustment"."user_id"',
				'defaultValue' => 'user_id:' . AxiaTestCase::USER_REP_ID,
			],
		];
		$this->assertEquals($expected, $AdjustmentModel->filterArgs);
		unset($AdjustmentModel);
	}

/**
 * testFindIndex
 *
 * @return void
 */
	public function testFindIndex() {
		$data = array();
		$data['user_id'] = '96d9a821-2838-4457-a491-d8d25c51c779';
		$data['adj_date'] = '2016-06-22';
		$data['adj_description'] = 'Test Description';
		$data['adj_amount'] = '1.99';

		$this->Adjustment->create($data);
		$this->Adjustment->save();

		$expected = array(
			'Adjustment' => array(
				'user_id' => '96d9a821-2838-4457-a491-d8d25c51c779',
				'adj_date' => '2016-06-22',
				'adj_description' => 'Test Description',
				'adj_amount' => '1.9900'
			)
		);

		$response = $this->Adjustment->find('index');

		$adjustment = array();

		foreach ($response as $r) {
			if ($r['Adjustment']['adj_description'] == 'Test Description') {
				$adjustment = $r;
				$expected['Adjustment']['id'] = $r['Adjustment']['id'];
				break;
			}
		}

		$this->assertEquals($expected, $adjustment);
	}

/**
 * testDelete
 *
 * @return void
 */
	public function testDelete() {
		$data = array();
		$data['user_id'] = '96d9a821-2838-4457-a491-d8d25c51c779';
		$data['adj_date'] = '2016-06-22';
		$data['adj_description'] = 'Test Description';
		$data['adj_amount'] = '1.99';

		$this->Adjustment->create($data);
		$this->Adjustment->save();

		$response = $this->Adjustment->find('first',
			array(
				'conditions' => array(
					'adj_description' => 'Test Description'
				)
			)
		);

		$id = $response['Adjustment']['id'];
		$this->Adjustment->delete($id);

		$response = $this->Adjustment->find('first',
			array(
				'conditions' => array(
					'adj_description' => 'Test Description'
				)
			)
		);

		$this->assertEquals(array(), $response);
	}

/**
 * testEdit
 *
 * @return void
 */
	public function testEdit() {
		$data = array();
		$data['user_id'] = '96d9a821-2838-4457-a491-d8d25c51c779';
		$data['adj_date'] = '2016-06-22';
		$data['adj_description'] = 'Test Description';
		$data['adj_amount'] = '1.99';

		$this->Adjustment->create($data);
		$this->Adjustment->save();

		$response = $this->Adjustment->find('first',
			array(
				'conditions' => array(
					'adj_description' => 'Test Description'
				)
			)
		);

		$id = $response['Adjustment']['id'];

		$this->Adjustment->id = $id;
		$this->Adjustment->saveField('adj_description', 'Updated Test Description');

		$response = $this->Adjustment->find('first',
			array(
				'conditions' => array(
					'adj_description' => 'Test Description'
				)
			)
		);

		$this->assertEquals(array(), $response);

		$expected = array(
			'Adjustment' => array(
				'adj_date' => '2016-06-22',
				'adj_description' => 'Updated Test Description',
				'adj_amount' => '1.9900',
				'adj_seq_number_old' => null,
				'user_id' => '96d9a821-2838-4457-a491-d8d25c51c779',
				'user_id_old' => null
			)
		);

		$response = $this->Adjustment->find('first',
			array(
				'conditions' => array(
					'adj_description' => 'Updated Test Description'
				)
			)
		);

		$expected['Adjustment']['id'] = $response['Adjustment']['id'];

		$this->assertEquals($expected, $response);
	}

	/**
	 * testFindAdjustment
	 *
	 * @covers Adjustment::_findAdjustments
	 * @return void
	 */
		public function testFindAdjustments() {
			$actual = $this->Adjustment->find('adjustments', [
				'conditions' => ['adj_description' => 'Adjustment 1']
				]
			);

			$expected = [
				['Adjustment' => [
							'adj_amount' => '1.0000',
							'adj_description' => 'Adjustment 1'
					]
				]
			];
			$this->assertEquals($expected, $actual);
		}
}
