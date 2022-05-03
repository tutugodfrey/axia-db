<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('MerchantRejectHelper', 'View/Helper');
App::uses('AxiaTestCase', 'Test');

/**
 * MerchantRejectHelper Test Case
 *
 */
class MerchantRejectHelperTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->MerchantReject = new MerchantRejectHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantReject);

		parent::tearDown();
	}

/**
 * testShowOpenStatus method
 *
 * @return void
 */
	public function testShowOpenStatus() {
		$result = $this->MerchantReject->showOpenStatus(true);
		$expected = '<span class="label label-primary">open</span>';
		$this->assertEquals($expected, $result);
		$result = $this->MerchantReject->showOpenStatus(false);
		$expected = '<span class="label label-danger">closed</span>';
		$this->assertEquals($expected, $result);
	}

/**
 * testOpenStatusInput method
 *
 * @return void
 */
	public function testOpenStatusInput() {
		$result = $this->MerchantReject->openStatusInput('fieldName', array('test' => 'test'));
		$expected = '<div class="open-status-input-block"><div class="input radio"><input type="hidden" name="data[fieldName]" id="fieldName_" value=""/><input type="radio" name="data[fieldName]" id="fieldName1" value="1" test="test" /><label for="fieldName1">Open<br/></label><input type="radio" name="data[fieldName]" id="fieldName0" value="0" test="test" /><label for="fieldName0">Closed</label></div></div>';
		$this->assertEquals($expected, $result);
	}

/**
 * testAjaxDelete method
 *
 * @return void
 */
	public function testAjaxDelete() {
		$result = $this->MerchantReject->ajaxDelete('/url', array('test' => 'test'));
		$expected = '<a href="#" class="delete-merchant-rejects" data-target="/url" test="test" onclick="if (confirm(&quot;Are you sure you want to delete the reject line?&quot;)) { return true; } return false;"><img src="/img/icon_trash.gif" title="Delete" class="icon" alt=""/></a>';
		$this->assertEquals($expected, $result);
	}

}
