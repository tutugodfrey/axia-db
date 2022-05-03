<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('AxiaCakeFormHelper', 'View/Helper');

/**
 * AxiaCakeFormHelper Test Case
 *
 */
class AxiaCakeFormHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->AxiaCakeForm = new AxiaCakeFormHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AxiaCakeForm);

		parent::tearDown();
	}

/**
 * testDefaultButtons method
 *
 * @return void
 */
	public function testDefaultButtons() {
		$expected = '<div class="form-group"><div class="col col-md-9 col-md-offset-3"><a href="javascript:void(0);" class="btn" id="newId" onclick="history.go(-1);">Cancel</a><input class="btn btn-primary" type="submit" value="Submit"/></div></div>';
		$result = $this->AxiaCakeForm->defaultButtons('newId');

		$this->assertEquals($expected, $result);
	}

/**
 * testDateTime
 *
 * @return void
 */
	public function testDateTime() {
		$fieldName = 'fieldName';

		$result = $this->AxiaCakeForm->dateTime($fieldName);
		$this->assertContains('Jan', $result);
		$this->assertContains('Dec', $result);
	}

/**
 * testCreate
 *
 * @return void
 */
	public function testCreate() {
		$result = $this->AxiaCakeForm->create('theForm');
		$expected = '<form action="/" class="form-horizontal" id="theFormForm" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>';
		$this->assertEquals($expected, $result);
	}

/**
 * testInput
 *
 * @return void
 */
	public function testInput() {
		$result = $this->AxiaCakeForm->input('field', array(
			'type' => 'checkbox'
		));
		$expected = '<div><div class="col col-md-7 col-md-offset-5"><div class="checkbox"><input type="hidden" name="data[field]" id="field_" value="0"/><label for="field"><input type="checkbox" name="data[field]" value="1" id="field"/> Field</label></div></div></div>';
		$this->assertEquals($expected, $result);

		$result = $this->AxiaCakeForm->input('field', array(
			'type' => 'password'
		));
		$expected = '<div><label for="field">Field</label><div class="input password"><input name="data[field]" value="" type="password" id="field"/></div></div>';
		$this->assertEquals($expected, $result);
	}

/**
 * test
 *
 * @return void
 */
	public function testCompactDate() {
		$this->markTestIncomplete("Check the diference between the 2 strings");
		$result = $this->AxiaCakeForm->compactDate('fieldName', array('test' => true));
		$expected = '<div><select name="data[fieldName][month]" test="1" class=" input-compact" id="fieldNameMonth">
<option value=""></option>
<option value="01">Jan</option>
<option value="02">Feb</option>
<option value="03">Mar</option>
<option value="04">Apr</option>
<option value="05">May</option>
<option value="06">Jun</option>
<option value="07">Jul</option>
<option value="08">Aug</option>
<option value="09">Sep</option>
<option value="10">Oct</option>
<option value="11">Nov</option>
<option value="12">Dec</option>
</select><select name="data[fieldName][day]" test="1" class=" input-compact" id="fieldNameDay">
<option value=""></option>
<option value="01">1</option>
<option value="02">2</option>
<option value="03">3</option>
<option value="04">4</option>
<option value="05">5</option>
<option value="06">6</option>
<option value="07">7</option>
<option value="08">8</option>
<option value="09">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
</select><select name="data[fieldName][year]" test="1" class=" input-compact" id="fieldNameYear">
<option value=""></option>
<option value="2036">2036</option>
<option value="2035">2035</option>
<option value="2034">2034</option>
<option value="2033">2033</option>
<option value="2032">2032</option>
<option value="2031">2031</option>
<option value="2030">2030</option>
<option value="2029">2029</option>
<option value="2028">2028</option>
<option value="2027">2027</option>
<option value="2026">2026</option>
<option value="2025">2025</option>
<option value="2024">2024</option>
<option value="2023">2023</option>
<option value="2022">2022</option>
<option value="2021">2021</option>
<option value="2020">2020</option>
<option value="2019">2019</option>
<option value="2018">2018</option>
<option value="2017">2017</option>
<option value="2016">2016</option>
<option value="2015">2015</option>
<option value="2014">2014</option>
<option value="2013">2013</option>
<option value="2012">2012</option>
<option value="2011">2011</option>
<option value="2010">2010</option>
<option value="2009">2009</option>
<option value="2008">2008</option>
<option value="2007">2007</option>
<option value="2006">2006</option>
<option value="2005">2005</option>
<option value="2004">2004</option>
<option value="2003">2003</option>
<option value="2002">2002</option>
<option value="2001">2001</option>
<option value="2000">2000</option>
<option value="1999">1999</option>
<option value="1998">1998</option>
<option value="1997">1997</option>
<option value="1996">1996</option>
</select></div>';

		$this->assertEquals($expected, $result);
	}
}
