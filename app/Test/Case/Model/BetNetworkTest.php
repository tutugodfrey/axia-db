<?php
/* BetNetwork Test cases generated on: 2017-11-13 10:11:00 : 1510598820*/
App::uses('BetNetwork', 'Model');
App::uses('AxiaTestCase', 'Test');
class BetNetworkTestCase extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BetNetwork = ClassRegistry::init('BetNetwork');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BetNetwork);
		parent::tearDown();
	}

/**
 * testGetListExeptionThrown
 * Test getting BetNetwork data
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage Function expects array but string was provided
 * @return void
 * @access public
 */
	public function testGetListExeptionThrown() {
		$this->BetNetwork->getList('bad param');
	}

/**
 * testGetList
 * Test getting BetNetwork data
 *
 * @covers BetNetwork::getList()
 * @return void
 * @access public
 */
	public function testGetList() {
		//test existing data
		$actual = $this->BetNetwork->getList(
				[
				'conditions' => [
					'is_active' => 1,
					'id' => ['55cc6d99-d534-45f1-acc7-184d34627ad4', '56e05859-7d90-4777-9fde-31a834627ad4'
					],
				],
				'order' => ['name ASC'],
				'fields' => ['id', 'name']
				]
			);

		$expected = [
			'55cc6d99-d534-45f1-acc7-184d34627ad4' => 'Bet Network 1',
			'56e05859-7d90-4777-9fde-31a834627ad4' => 'TSYS (pre 10/1/14)'
		];
		$this->assertSame($expected, $actual);
		//Test default behavior when no params passed
		$actual = $this->BetNetwork->getList([]);
		$expected = $this->BetNetwork->find('list', [
			'fields' => ['id', 'name'],
			'conditions' => ['is_active' => 1],
			'order' => ['name ASC']
		]);
		$this->assertSame($expected, $actual);
	}

}
