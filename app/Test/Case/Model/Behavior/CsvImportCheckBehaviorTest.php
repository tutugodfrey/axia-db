<?php
App::uses('AxiaTestCase', 'Test');

/**
 * Article Test Model
 */
class Article extends CakeTestModel {

	public $useTable = 'articles';
}

/**
 * Import Test Model
 */
class ImportObserver {

/**
 * test method
 *
 * @param array $data Data
 * @return void
 */
	public function onImportRow($data) {
	}

/**
 * test method
 *
 * @param string $action action
 * @param array $data Data
 * @return void
 */
	public function listen($action, $data) {
	}

}

/**
 * ArticleCallbackBeforeImport Test Model
 */
class ArticleCallbackBeforeImport extends CakeTestModel {

	public $useTable = 'articles';

/**
 * test method
 *
 * @param array $data Data
 * @return array
 */
	public function beforeImport($data) {
		$data['ArticleCallbackBeforeImport']['body'] = $data['ArticleCallbackBeforeImport']['body'] . '-modified';
		return $data;
	}

}

/**
 * ArticleCallbackSkipRow Test Model
 */
class ArticleCallbackSkipRow extends CakeTestModel {

	public $useTable = 'articles';
	const SKIP_MESSAGE = 'Row Skipped';

/**
 * test method
 *
 * @param array $data Data
 * @return mixed
 */
	public function skipRowImport($data) {
		if (empty($data['ArticleCallbackSkipRow']['body'])) {
			return self::SKIP_MESSAGE;
		}
	}

}

/**
 * Test case
 */
class CsvImportCheckBehaviorTest extends AxiaTestCase {

/**
 * Creates the model instance
 *
 * @return void
 */
	public function setUp() {
		$this->Article = new Article();
		$this->Article->Behaviors->load('CsvImportCheck');
	}

/**
 * Destroy the model instance
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Article);
		unset($this->Behavior);
	}

/**
 * testImportCSVNoFileException
 *
 * @expectedException RuntimeException
 * @access public
 * @return void
 */
	public function testImportCSVNoFileException() {
		$this->Article->importCSV('/unexistent/file');
	}

/**
 * testImportCSVNoFile
 *
 * @access public
 * @return void
 */
	public function testImportCSVNoFile() {
		$result = $this->Article->importCSV(null);
		$this->assertFalse($result);
	}

/**
 * testImportCSV
 *
 * @access public
 * @return void
 */
	public function testImportCSV() {
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test1.csv');
		$this->assertTrue($result);

		$expected = [
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'title' => 'Title-1',
					'body' => 'Body-1'
				]
			],
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000002',
					'title' => 'Title-2',
					'body' => 'Body-2'
				]
			],
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000003',
					'title' => 'Title-3',
					'body' => 'Body-3'
				]
			],
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000004',
					'title' => 'Title-4',
					'body' => 'Body-4'
				]
			]
		];

		$result = $this->Article->find('all');
		$this->assertEquals($expected, $result);
	}

/**
 * testImportCSV
 *
 * @access public
 * @return void
 */
	public function testImportCSVWithCallback() {
		$this->Article = new ArticleCallbackBeforeImport();
		$this->Article->Behaviors->load('CsvImportCheck');
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test1.csv');
		$this->assertTrue($result);

		$expected = [
			[
				'ArticleCallbackBeforeImport' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'title' => 'Title-1',
					'body' => 'Body-1-modified'
				]
			],
			[
				'ArticleCallbackBeforeImport' => [
					'id' => '00000000-0000-0000-0000-000000000002',
					'title' => 'Title-2',
					'body' => 'Body-2-modified'
				]
			],
			[
				'ArticleCallbackBeforeImport' => [
					'id' => '00000000-0000-0000-0000-000000000003',
					'title' => 'Title-3',
					'body' => 'Body-3-modified'
				]
			],
			[
				'ArticleCallbackBeforeImport' => [
					'id' => '00000000-0000-0000-0000-000000000004',
					'title' => 'Title-4',
					'body' => 'Body-4-modified'
				]
			]
		];

		$result = $this->Article->find('all');
		$this->assertEquals($expected, $result);
	}

/**
 * testImportCSV
 *
 * @access public
 * @return void
 */
	public function testImportCSVWithCallbackSkippingRow() {
		$this->Article = new ArticleCallbackSkipRow();
		$this->Article->Behaviors->load('CsvImportCheck');
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test2.csv');
		$this->assertTrue($result);

		$expected = [
			[
				'ArticleCallbackSkipRow' => [
					'id' => '00000000-0000-0000-0000-000000000002',
					'title' => 'Title-2',
					'body' => 'Body-2'
				]
			],
			[
				'ArticleCallbackSkipRow' => [
					'id' => '00000000-0000-0000-0000-000000000003',
					'title' => 'Title-3',
					'body' => 'Body-3'
				]
			],
			[
				'ArticleCallbackSkipRow' => [
					'id' => '00000000-0000-0000-0000-000000000004',
					'title' => 'Title-4',
					'body' => 'Body-4'
				]
			]
		];

		$result = $this->Article->find('all');
		$this->assertEquals($expected, $result);
	}

/**
 * testImportCSVSaved
 *
 * @access public
 * @return void
 */
	public function testImportCSVWithFixedData() {
		$fixed = [
			'Article' => [
				'title' => 'title-fixed'
			]
		];
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test1.csv', $fixed);
		$this->assertTrue($result);

		$result = $this->Article->find('all');
		$expected = [
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'title' => 'title-fixed',
					'body' => 'Body-1'
				]
			],
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000002',
					'title' => 'title-fixed',
					'body' => 'Body-2'
				]
			],
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000003',
					'title' => 'title-fixed',
					'body' => 'Body-3'
				]
			],
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000004',
					'title' => 'title-fixed',
					'body' => 'Body-4'
				]
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testImportCSVWithSaved
 *
 * @access public
 * @return void
 */
	public function testImportCSVWithSaved() {
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test4.csv', [], true);
		$this->assertEquals([0], $result);

		$result = $this->Article->find('all');
		$expected = [
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'title' => 'Title-1',
					'body' => 'Body-1'
				]
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testImportCSVWithDotInHeader
 *
 * @access public
 * @return void
 */
	public function testImportCSVWithDotInHeader() {
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test5.csv');
		$this->assertTrue($result);

		$result = $this->Article->find('all');
		$expected = [
			[
				'Article' => [
					'id' => '00000000-0000-0000-0000-000000000001',
					'title' => 'Title-1',
					'body' => 'Body-1'
				]
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * testListeners
 *
 * @access public
 * @return void
 */
	public function testListeners() {
		$mock = $this->getMock('ImportObserver');
		$this->Article->attachImportListener($mock);
		$this->Article->attachImportListener([&$mock, 'listen']);

		$mock->expects($this->exactly(2))->method('onImportRow');
		$mock->expects($this->exactly(2))->method('listen');

		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test3.csv');
		$this->assertTrue($result);
	}

/**
 * testImportCSVWithValidation
 *
 * @access public
 * @return void
 */
	public function testImportCSVWithValidation() {
		$this->Article->validate = [
			'body' => [
				'rule' => 'notBlank',
				'message' => 'This field should not be empty'
			]
		];
		$result = $this->Article->importCSV(APP . 'Test' . DS . 'Tmp' . DS . 'test2.csv');
		$this->assertFalse($result);
		$errors = $this->Article->getImportErrors();
		$expected = [
			[
				'validation' => [
					'body' => [
						'This field should not be empty'
					]
				]
			]
		];

		$this->assertEquals($errors, $expected);
	}

}