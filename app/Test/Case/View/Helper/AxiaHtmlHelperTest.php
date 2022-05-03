<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('AxiaHtmlHelper', 'View/Helper');
App::uses('AxiaTestCase', 'Test');

/**
 * AxiaHtmlHelper Test Case
 *
 */
class AxiaHtmlHelperTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->AxiaHtml = new AxiaHtmlHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AxiaHtml);

		parent::tearDown();
	}

/**
 * testCheckboxValue method
 *
 * @return void
 */
	public function testCheckboxValue() {
		$result = $this->AxiaHtml->checkboxValue(true, 'label', 'checked', 'notChecked');
		$expected = '<b>label</b>: &#9745; checked';
		$this->assertEquals($expected, $result);

		$result = $this->AxiaHtml->checkboxValue(false, 'label', 'checked', 'notChecked');
		$expected = '<b>label</b>: &#9744; notChecked';
		$this->assertEquals($expected, $result);
	}

/**
 *  testToggleTable method
 *
 * @return void
 */
	public function testToggleTable() {
		$result = $this->AxiaHtml->toggleTable();
		$expected = '';
		$this->assertEquals($expected, $result);

		$headers = array('one', 'two (collapsed)', 'three', 'four (collapsed)');
		$cells = array(
			array(11, 12, 13, 14),
			array(21, array(22, array('class' => 'myclass')), 23, 24),
			array(31, 32, 33, array(34, array('class' => 'myclass'))),
			array(41, 42, 43, 44),
		);
		// for each toggle, a toggle button will be displayed
		$toggles = array(
			array(
				//NOTE: column index starts on 0
				'column' => 1,
				'label' => __('Second Col Enable/Disable'),
			),
			array(
				'column' => 2,
				'label' => __('Third Col Enable/Disable'),
			),
		);
		$result = $this->AxiaHtml->toggleTable($headers, $cells, $toggles, array());
		$this->assertRegExp('/(<button.*){4}/', $result);
		$this->assertRegExp('/(<table.*){1}/', $result);
		$this->assertRegExp('/(<th.*){4}/', $result);
		$this->assertRegExp('/<div(.*)(<button.*){2}(<label.*)(<button.*){2}(<label.*)(<table.*)/', $result);
		//more tests should be added to check the classes are correctly added
	}

/**
 * test
 *
 * @return void
 */
	public function testEditImageLink() {
		$result = $this->AxiaHtml->editImageLink('/someurl', array(
			'someoption' => 'somevalue',
		));
		$expected = '<a href="/someurl" someoption="somevalue"><img src="/img/editPencil.gif" title="Edit" alt="Edit" class="icon"/></a>';

		$this->assertEquals($expected, $result);
	}
/**
 * test
 *
 * @return void
 */
	public function testModalDecryptIcon() {
		$result = $this->AxiaHtml->modalDecryptIcon('/someurl');
		$expected = '<a href="javascript:void(0)" title="Show full number" class="glyphicon glyphicon-eye-open" data-toggle="modal" data-target="#myModal" onClick="renderContentAJAX(&#039;&#039;, &#039;&#039;, &#039;&#039;, &#039;ModalContainer&#039;, &#039;/someurl&#039;)"></a>';
		$this->assertEquals($expected, $result);
	}
}
