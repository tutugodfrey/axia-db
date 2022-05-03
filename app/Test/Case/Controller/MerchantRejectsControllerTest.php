<?php
App::uses('BaseAuthControllerTestCase', 'Test');

/**
 * MerchantRejectsController Test Case
 *
 */
class MerchantRejectsControllerTest extends BaseAuthControllerTestCase {

/**
 * {@inheritDoc}
 */
	public function startTest($method) {
		Configure::write('Rbac.enabled', false);
		parent::startTest($method);
		$this->_startAuth('MerchantRejects');
	}

/**
 * {@inheritDoc}
 */
	public function endTest($method) {
		Configure::write('Rbac.enabled', true);
	}

/**
 * Check the get will render the upload view
 */
	public function testUploadGet() {
		$this->testAction('/merchantRejects/import', array(
			'method' => 'get',
			'return' => 'vars'
		));
		$this->assertEquals('import', $this->controller->view);
	}

/**
 * Uploading an invalid file will display error message
 */
	public function testUploadPostInvalidFile() {
		$data = array('posted data');
		$MerchantReject = $this->getMockForModel('MerchantReject', array(
			'importFromCsvUpload',
			'getImportErrors',
			'getImportSkippedRows'
		));
		$MerchantReject->expects($this->at(0))
			->method('importFromCsvUpload')
			->with($data);
		$MerchantReject->expects($this->at(1))
			->method('getImportErrors')
			->will($this->returnValue(array('there are errors')));
		$MerchantReject->expects($this->at(2))
			->method('getImportSkippedRows');
		$this->MerchantRejects->MerchantReject = $MerchantReject;

		$this->MerchantRejects->Session->expects($this->once())
			->method('setFlash')
			->with('The file was not imported, there were validation errors.')
			->will($this->returnValue(true));

		$viewVars = $this->testAction(
			'/merchantRejects/import', array(
				'data' => $data,
				'method' => 'post',
				'return' => 'vars'
			)
		);
	}

/**
 * Uploading an invalid file will display error message
 */
	public function testUploadPostValidationException() {
		$data = array('posted data');
		$message = 'exception message';
		$MerchantReject = $this->getMockForModel('MerchantReject', array(
			'importFromCsvUpload',
			'getImportErrors',
			'getImportSkippedRows'
		));
		$MerchantReject->expects($this->at(0))
			->method('importFromCsvUpload')
			->with($data)
			->will($this->throwException(new UploadValidationException($message)));
		$this->MerchantRejects->MerchantReject = $MerchantReject;

		$this->MerchantRejects->Session->expects($this->once())
			->method('setFlash')
			->with($message)
			->will($this->returnValue(true));

		$this->testAction(
			'/merchant_rejects/import', array(
				'data' => $data,
				'method' => 'post',
				'return' => 'vars'
			)
		);
	}

/**
 * Upload happy path
 */
	public function testUploadPostHappy() {
		$data = array('posted data');
		$results = array('some results');
		$MerchantReject = $this->getMockForModel('MerchantReject', array(
			'importFromCsvUpload',
			'getImportErrors',
			'getImportSkippedRows'
		));
		$MerchantReject->expects($this->at(0))
			->method('importFromCsvUpload')
			->with($data)
			->will($this->returnValue($results));
		$MerchantReject->expects($this->at(1))
			->method('getImportErrors')
			->will($this->returnValue(array()));
		$MerchantReject->expects($this->at(2))
			->method('getImportSkippedRows')
			->will($this->returnValue(array()));
		$this->MerchantRejects->MerchantReject = $MerchantReject;

		$this->MerchantRejects->Session->expects($this->once())
			->method('setFlash')
			->with('File imported successfully')
			->will($this->returnValue(true));

		$viewVars = $this->testAction(
			'/merchant_rejects/import', array(
				'data' => $data,
				'method' => 'post',
				'return' => 'vars'
			)
		);
		$this->assertEquals($results, $viewVars['importResult']);
		$this->assertEmpty($viewVars['importErrors']);
		$this->assertEmpty($viewVars['importSkippedRows']);
	}

/**
 * Index default
 */
	public function testIndexGet() {
		$MerchantReject = $this->getMockBuilder('MerchantReject')
				->disableOriginalConstructor()
				->getMock();
		$Merchant = $this->getMockBuilder('Merchant')
				->disableOriginalConstructor()
				->getMock();

		$this->MerchantRejects->MerchantReject = $MerchantReject;
		$this->MerchantRejects->MerchantReject->Merchant = $Merchant;

		$MerchantReject->expects($this->once())
				->method('getItemsToSet')
				->will($this->returnValue(array(
					'merchantRejectStatuses' => array(),
					'users' => array(),
					'merchantRejectRecurrances' => array(),
					'merchantRejectTypes' => array(),
					'merchantRejectCodes' => array()
				)));
		$viewVars = $this->testAction(
			'/merchant_rejects/index/reject-1', array(
				'method' => 'get',
				'return' => 'vars'
			)
		);
		$this->assertEquals('merchantRejects', $this->MerchantRejects->Paginator->settings['findType']);
	}

}
