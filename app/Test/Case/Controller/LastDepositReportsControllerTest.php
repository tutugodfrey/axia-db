<?php
App::uses('BaseAuthControllerTestCase', 'Test');

/**
 * LastDepositReportsController Test Case
 *
 */
class LastDepositReportsControllerTest extends BaseAuthControllerTestCase {

	public function startTest($method) {
		Configure::write('Rbac.enabled', false);
		parent::startTest($method);
		$this->_startAuth('LastDepositReports');
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
		$this->testAction('/lastDepositReports/upload', array(
			'method' => 'get',
			'return' => 'vars'
		));
		$this->assertEquals('upload', $this->controller->view);
	}

/**
 * Index default
 */
	public function testIndexGet() {
		$LastDepositReport = $this->getMockBuilder('LastDepositReport')
				->disableOriginalConstructor()
				->getMock();
		$User = $this->getMockBuilder('User')
				->disableOriginalConstructor()
				->getMock();
		$Merchant = $this->getMockBuilder('Merchant')
				->disableOriginalConstructor()
				->getMock();
		$Organization = $this->getMockForModel('Organization', array('find'));
		$Region = $this->getMockForModel('Region', array('find'));
		$Subregion = $this->getMockForModel('Subregion', array('find'));

		$this->LastDepositReports->LastDepositReport = $LastDepositReport;
		$this->LastDepositReports->LastDepositReport->User = $User;
		$this->LastDepositReports->LastDepositReport->Merchant = $Merchant;
		$this->LastDepositReports->LastDepositReport->Merchant->Organization = $Organization;
		$this->LastDepositReports->LastDepositReport->Merchant->Region = $Region;
		$this->LastDepositReports->LastDepositReport->Merchant->Subregion = $Subregion;

		$User->expects($this->once())
				->method('getEntityManagerUsersList')
				->will($this->returnValue(array(
					'someUser' => 'someName'
				)));
		$User->expects($this->once())
				->method('extractComplexId')
				->will($this->returnValue(array(
					'prefix' => User::PREFIX_USER,
					'id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1',
				)));
		$Organization->expects($this->once())
				->method('find')
				->will($this->returnValue(array(
					CakeText::uuid() => 'someOrg'
				)));
		$Region->expects($this->once())
				->method('find')
				->will($this->returnValue(array(
					CakeText::uuid() => 'someRegion'
				)));
		$Subregion->expects($this->once())
				->method('find')
				->will($this->returnValue(array(
					CakeText::uuid() => 'someSubregion'
				)));
		$viewVars = $this->testAction(
			'/last_deposit_reports/index?user_id=select_all%3Aselect_all&merchant=Hands&active=1&organization_id=&region_id=&subregion_id=&location_description=', array(
				'method' => 'get',
				'return' => 'vars'
			)
		);
		$this->assertEquals('lastDepositReports', $this->LastDepositReports->Paginator->settings['findType']);
	}

}
