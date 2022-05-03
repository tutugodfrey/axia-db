<?php
App::uses('Gateway1', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Gateway1 Test Case
 *
 * @coversDefaultClass Gateway1
 */
class Gateway1Test extends AxiaTestCase {

/**
 * Class under test
 *
 * @var Gateway1
 */
	public $Gateway1;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Gateway1 = ClassRegistry::init('Gateway1');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Gateway1);
		parent::tearDown();
	}

/**
 * testBeforeSave
 *
 * @covers Gateway1::beforeSave()
 * @return void
 */
	public function testBeforeSave() {
		$tstData = [
			'Gateway1' => ['gw1_mid' => ' trailing spaces are not good ']
		];
		$this->Gateway1->set($tstData);
		$this->Gateway1->beforeSave();

		$actual = $this->Gateway1->data['Gateway1']['gw1_mid'];
		$expected = 'trailing spaces are not good';
		$this->assertSame($expected, $actual);
	}
}
