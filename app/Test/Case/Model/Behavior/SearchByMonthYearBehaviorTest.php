<?php
App::uses('SearchByMonthYearBehavior', 'Model/Behavior');
App::uses('Model', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Model to test the behavior with fulldate filter
 */
class PostModel extends Model {

	public $useDbConfig = 'test';

	public $name = 'Post';

	public $alias = 'Post';

	public $useTable = 'posts';

}

/**
 * SearchByMonthYearBehavior Test Case
 *
 */
class SearchByMonthYearBehaviorTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Post = ClassRegistry::init('PostModel');
		$this->Post->Behaviors->load('SearchByMonthYear', [
			'yearFieldName' => 'published_year',
			'monthFieldName' => 'published_month'
		]);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Post);

		parent::tearDown();
	}

/**
 * test
 *
 * @covers SearchByMonthYearBehavior::setup()
 * @covers SearchByMonthYearBehavior::_validateSettings()
 * @return void
 */
	public function testSetup() {
		$data = [
			'from_date' => [
				'year' => '2015',
				'month' => '12',
			]
		];

		$this->Post->Behaviors->load('SearchByMonthYear', [
			'yearFieldName' => 'published_year',
			'monthFieldName' => 'published_month'
		]);
		$result = $this->Post->dateStartConditions($data);
		$this->assertSame("make_date(Post.published_year::integer, Post.published_month::integer, 1) >= '2015-12-01'", $result);

		$this->Post->Behaviors->load('SearchByMonthYear', [
			'fulldateFieldName' => 'published_fulldate'
		]);
		$result = $this->Post->dateStartConditions($data);
		$this->assertSame(['Post.published_fulldate >=' => '2015-12-01'], $result);
	}

/**
 * test
 *
 * @param int|string $year Year
 * @param int|string $month Month
 * @param mixed $expected Expected result
 * @return void
 * @covers SearchByMonthYearBehavior::buildStartDate()
 * @covers SearchByMonthYearBehavior::_parseMonth()
 * @covers SearchByMonthYearBehavior::_checkValidDate()
 * @dataProvider provideBuildStartDate
 */
	public function testBuildStartDate($year, $month, $expected) {
		$this->assertEquals($expected, $this->Post->buildStartDate($year, $month));
	}

/**
 * Data provider for testBuildStartDate
 *
 * @return array
 */
	public function provideBuildStartDate() {
		return [
			['2015', 1, '2015-01-01'],
			[2022, '6', '2022-06-01'],
			['1999', '12', '1999-12-01'],
			[2004, '09', '2004-09-01'],
		];
	}

/**
 * test
 *
 * @param int|string $year Year
 * @param int|string $month Month
 * @return void
 * @covers SearchByMonthYearBehavior::buildStartDate()
 * @covers SearchByMonthYearBehavior::_parseMonth()
 * @covers SearchByMonthYearBehavior::_checkValidDate()
 * @dataProvider provideBuildStartDateNotValid
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid date
 */
	public function testBuildStartDateNotValid($year, $month) {
		$this->Post->buildStartDate($year, $month);
	}

/**
 * Data provider for testBuildStartDateNotValid
 *
 * @return array
 */
	public function provideBuildStartDateNotValid() {
		return [
			[null, null],
			[null, 1],
			[1999, -1],
			['1999', 13],
			[-99, 2],
			['year', '10'],
		];
	}
/**
 * test
 *
 * @param int|string $year Year
 * @param int|string $month Month
 * @param mixed $expected Expected result
 * @return void
 * @dataProvider provideBuildEndDate
 * @covers SearchByMonthYearBehavior::buildEndDate()
 */
	public function testBuildEndDate($year, $month, $expected) {
		$this->assertEquals($expected, $this->Post->buildEndDate($year, $month));
	}

/**
 * Data provider for testBuildEndDate
 *
 * @return array
 */
	public function provideBuildEndDate() {
		return [
			['2015', 1, '2015-01-31'],
			[2022, '6', '2022-06-30'],
			['1999', '2', '1999-02-28'],
			[2004, '09', '2004-09-30'],
		];
	}

/**
 * test
 *
 * @param int|string $year Year
 * @param int|string $month Month
 * @return void
 * @dataProvider provideBuildEndDateNotValid
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid date
 * @covers SearchByMonthYearBehavior::buildEndDate()
 */
	public function testBuildEndDateNotValid($year, $month) {
		$this->Post->buildEndDate($year, $month);
	}

/**
 * Data provider for testBuildEndDateNotValid
 *
 * @return array
 */
	public function provideBuildEndDateNotValid() {
		return [
			[null, null],
			[null, 1],
			[1999, -1],
			['1999', 13],
			[-99, 2],
			['year', '10'],
		];
	}

/**
 * test
 *
 * @param array $config Behavior configuration
 * @param string $expectedMessage Expected exception message
 * @return void
 * @dataProvider provideSetupOutOfBoundsException
 * @covers SearchByMonthYearBehavior::_validateSettings()
 */
	public function testValidateSettings($config, $expectedMessage) {
		$this->Post->Behaviors->load('SearchByMonthYear', $config);
		try {
			$this->Post->dateStartConditions([]);
			$this->fail('Expected OutOfBoundsException for missing table field');
		} catch (OutOfBoundsException $ex) {
			$this->assertSame($expectedMessage, $ex->getMessage());
		}
	}

/**
 * Data provider for testSetupOutOfBoundsException
 *
 * @return array
 */
	public function provideSetupOutOfBoundsException() {
		return [
			[
				['fulldateFieldName' => 'random_field_name'],
				'The fulldateFieldName \'random_field_name\' does not exists in model Post'
			],
			[
				[
					'fulldateFieldName' => false,
					'yearFieldName' => 'random_field_name',
				],
				'The yearFieldName \'random_field_name\' does not exists in model Post'
			],
			[
				[
					'fulldateFieldName' => false,
					'yearFieldName' => 'published_year',
					'monthFieldName' => 'random_field_name',
				],
				'The monthFieldName \'random_field_name\' does not exists in model Post'
			],
		];
	}

/**
 * test
 *
 * @param array $data Data submitted for search args
 * @return void
 * @dataProvider provideDateStartConditionsNotValid
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid date
 * @covers SearchByMonthYearBehavior::dateStartConditions
 */
	public function testDateStartConditionsNotValid($data) {
		$this->Post->dateStartConditions($data);
	}

/**
 * Data provider for testDateStartConditionsNotValid
 *
 * @return array
 */
	public function provideDateStartConditionsNotValid() {
		return [
			[[]],
			[['year' => '2017']],
			[['from_date' => ['month' => '12']]],
			[['from_date' => ['year' => '2000', 'month' => '13']]],
			[['from_date' => ['year' => 'year', 'month' => '8']]],
		];
	}

/**
 * test
 *
 * @param array $data Data submitted for search args
 * @return void
 * @dataProvider provideDateEndConditionsNotValid
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid date
 * @covers SearchByMonthYearBehavior::dateEndConditions
 * @covers SearchByMonthYearBehavior::_monthYearEndConditions
 */
	public function testDateEndConditionsNotValid($data) {
		$this->Post->dateEndConditions($data);
	}

/**
 * Data provider for testDateEndConditionsNotValid
 *
 * @return array
 */
	public function provideDateEndConditionsNotValid() {
		return [
			[[]],
			[['year' => '2017']],
			[['end_date' => ['month' => '12']]],
			[['end_date' => ['year' => '2000', 'month' => '13']]],
			[['end_date' => ['year' => 'year', 'month' => '8']]],
		];
	}

/**
 * test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @return void
 * @dataProvider provideDateStartConditionsMonthYear
 * @covers SearchByMonthYearBehavior::dateStartConditions
 * @covers SearchByMonthYearBehavior::_monthYearStartConditions
 */
	public function testDateStartConditionsMonthYear($data, $expected) {
		$this->assertEquals($expected, $this->Post->dateStartConditions($data));
	}

/**
 * Data provider for testDateStartConditions
 *
 * @return array
 */
	public function provideDateStartConditionsMonthYear() {
		return [
			[
				['from_date' => ['year' => '2015', 'month' => '01']],
				"make_date(Post.published_year::integer, Post.published_month::integer, 1) >= '2015-01-01'",
			],
			[
				['from_date' => ['year' => '2020', 'month' => '06']],
				"make_date(Post.published_year::integer, Post.published_month::integer, 1) >= '2020-06-01'",
			],
		];
	}

/**
 * test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @return void
 * @dataProvider provideDateEndConditionsMonthYear
 * @covers SearchByMonthYearBehavior::dateEndConditions()
 * @covers SearchByMonthYearBehavior::_monthYearEndConditions
 */
	public function testDateEndConditionsMonthYear($data, $expected) {
		$this->assertEquals($expected, $this->Post->dateEndConditions($data));
	}

/**
 * Data provider for testDateEndConditions
 *
 * @return array
 */
	public function provideDateEndConditionsMonthYear() {
		return [
			[
				['end_date' => ['year' => '2015', 'month' => '01']],
				"make_date(Post.published_year::integer, Post.published_month::integer, 28) <= '2015-01-31'",
			],
			[
				['end_date' => ['year' => '2020', 'month' => '06']],
				"make_date(Post.published_year::integer, Post.published_month::integer, 28) <= '2020-06-30'",
			],
		];
	}

/**
 * test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @return void
 * @dataProvider provideDateStartConditionsFulldate
 * @covers SearchByMonthYearBehavior::dateStartConditions
 * @covers SearchByMonthYearBehavior::_fulldateStartConditions
 */
	public function testDateStartConditionsFulldate($data, $expected) {
		$this->Post->Behaviors->load('SearchByMonthYear', [
			'fulldateFieldName' => 'published_fulldate',
		]);
		$this->assertEquals($expected, $this->Post->dateStartConditions($data));
	}

/**
 * Data provider for testDateStartConditionsFulldate
 *
 * @return array
 */
	public function provideDateStartConditionsFulldate() {
		return [
			[
				['from_date' => ['year' => '2015', 'month' => '01']],
				['Post.published_fulldate >=' => '2015-01-01'],
			],
			[
				['from_date' => ['year' => '2020', 'month' => '06']],
				['Post.published_fulldate >=' => '2020-06-01'],
			],
		];
	}

/**
 * test
 *
 * @param array $data Data submitted for search args
 * @param mixed $expected Expected result
 * @return void
 * @dataProvider provideDateEndConditionsFulldate
 * @covers SearchByMonthYearBehavior::dateEndConditions
 * @covers SearchByMonthYearBehavior::_fulldateEndConditions
 */
	public function testDateEndConditionsFulldate($data, $expected) {
		$this->Post->Behaviors->load('SearchByMonthYear', [
			'fulldateFieldName' => 'published_fulldate',
		]);
		$this->assertEquals($expected, $this->Post->dateEndConditions($data));
	}

/**
 * Data provider for testDateEndConditionsFulldate
 *
 * @return array
 */
	public function provideDateEndConditionsFulldate() {
		return [
			[
				['end_date' => ['year' => '2015', 'month' => '01']],
				['Post.published_fulldate <=' => '2015-01-31'],
			],
			[
				['end_date' => ['year' => '2020', 'month' => '06']],
				['Post.published_fulldate <=' => '2020-06-30'],
			],
		];
	}

/**
 * test
 *
 * @covers SearchByMonthYearBehavior::_getQueryAlias()
 * @return void
 */
	public function testGetQueryAlias() {
		$data = [
			'from_date' => [
				'year' => '2015',
				'month' => '07',
			]
		];

		$result = $this->Post->dateStartConditions($data);
		$this->assertSame("make_date(Post.published_year::integer, Post.published_month::integer, 1) >= '2015-07-01'", $result);

		$result = $this->Post->dateStartConditions($data, 'CustomModelAlias');
		$this->assertSame("make_date(CustomModelAlias.published_year::integer, CustomModelAlias.published_month::integer, 1) >= '2015-07-01'", $result);
	}
}
