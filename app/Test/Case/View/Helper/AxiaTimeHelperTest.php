<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('AxiaTimeHelper', 'View/Helper');
App::uses('AxiaTestCase', 'Test');

/**
 * AxiaTimeHelper Test Case
 *
 */
class AxiaTimeHelperTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->AxiaTime = new AxiaTimeHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AxiaTime);

		parent::tearDown();
	}

/**
 * test
 *
 * @covers AxiaTimeHelper::date()
 * @return void
 */
	public function testDate() {
		$this->assertSame('May 4, 2015', $this->AxiaTime->date('2015-05-04'));
		$this->assertSame('Sep 28, 2011', $this->AxiaTime->date('2011-09-28'));
		$this->assertSame('Dec 12, 2016', $this->AxiaTime->date('2016-12-12'));
	}

/**
 * test
 *
 * @covers AxiaTimeHelper::time()
 * @return void
 */
	public function testTime() {
		$this->assertSame('9:45 am', $this->AxiaTime->time('09:45:51'));
		$this->assertSame('2:45 pm', $this->AxiaTime->time('14:45:51'));
		$this->assertSame('12:00 am', $this->AxiaTime->time('00:00:00'));
	}

/**
 * test
 *
 * @covers AxiaTimeHelper::datetime()
 * @return void
 */
	public function testDatetime() {
		$this->assertSame('May 4, 2015 9:45 am', $this->AxiaTime->datetime('2015-05-04 09:45:51'));
		$this->assertSame('Sep 28, 2011 2:45 pm', $this->AxiaTime->datetime('2011-09-28 14:45:51'));
		$this->assertSame('Dec 12, 2016 12:00 am', $this->AxiaTime->datetime('2016-12-12 00:00:00'));
	}
}
