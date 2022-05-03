<?php
/* MaintenanceDashboard Test cases generated on: 2016-11-02 11:11:40 : 1478112160*/
App::uses('MaintenanceDashboard', 'Model');

App::uses('AppTestCase', 'Templates.Lib');
class MaintenanceDashboardTestCase extends AppTestCase {

/**
 * Autoload entrypoint for fixtures dependecy solver
 *
 * @var string
 * @access public
 */
	public $plugin = 'app';

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = array();

/**
 * Start Test callback
 *
 * @param string $method method
 * @return void
 * @access public
 */
	public function startTest($method) {
		parent::startTest($method);
		$this->MaintenanceDashboard = ClassRegistry::init('MaintenanceDashboard');
	}

/**
 * End Test callback
 *
 * @param string $method method
 * @return void
 * @access public
 */
	public function endTest($method) {
		parent::endTest($method);
		unset($this->MaintenanceDashboard);
		ClassRegistry::flush();
	}

/**
 * testGetEditViewData
 *
 * @covers MaintenanceDashboard::getEditViewData
 * @return void
 * @access public
 */
	public function testGetEditViewData() {
		$actual = $this->MaintenanceDashboard->getEditViewData('ProductsServicesType');

		$this->assertSame(array_keys(ClassRegistry::init('ProductSetting')->getColumnTypes()), $actual['productSettingFields']);
		$this->assertSame('ProductsServicesType', $actual['modelName']);
		$this->assertNotEmpty($actual['productCategories']);

		$actual = $this->MaintenanceDashboard->getEditViewData('MerchantAchReason');
		$this->assertSame(array_keys(ClassRegistry::init('MerchantAchReason')->getColumnTypes()), array_keys($actual['fieldTypes']));
		$this->assertSame('MerchantAchReason', $actual['modelName']);
		$this->assertNotEmpty($actual['nonTaxableReasons']);
		$this->assertNotEmpty($actual['accountingReportColAliases']);
	}

/**
 * testGetModelOptions
 *
 * @covers MaintenanceDashboard::getModelOptions
 * @return void
 * @access public
 */
	public function testGetModelOptions() {
		$expected = [
			'BackEndNetwork' => 'Back End Networks',
			'BetNetwork' => 'Bet Networks',
			'BetTable' => 'Bet Tables',
			'CancellationFee' => 'Cancellation Fees',
			'CardType' => 'Card Types',
			'Client' => 'Clients',
			'DebitAcquirer' => 'Debit Acquirers',
			'Entity' => 'Entities',
			'Gateway' => 'Gateways',
			'Groups' => 'Groups',
			'MerchantAchAppStatus' => 'Merchant Ach App Statuses',
			'MerchantAchBillingOption' => 'Merchant Ach Billing Options',
			'MerchantAchReason' => 'Merchant Ach Reasons',
			'MerchantAcquirer' => 'Merchant Acquirers',
			'MerchantBin' => 'Merchant Bins',
			'MerchantCancellationSubreason' => 'Merchant Cancellation Subreasons',
			'MerchantRejectType' => 'Merchant Reject Types',
			'MerchantType' => 'Merchant Types',
			'MerchantUwFinalApproved' => 'Merchant Uw Final Approveds',
			'MerchantUwFinalStatus' => 'Merchant Uw Final Statuses',
			'Network' => 'Networks',
			'NonTaxableReason' => 'Non Taxable Reasons',
			'Organization' => 'Organizations',
			'OriginalAcquirer' => 'Original Acquirers',
			'ProductCategory' => 'Product Categories',
			'ProductFeature' => 'Product Features',
			'ProductsServicesType' => 'Products Services Types',
			'RateStructure' => 'Rate Structures',
			'Region' => 'Regions',
			'SponsorBank' => 'Sponsor Banks',
			'Subregion' => 'Subregions',
			'TimelineItem' => 'Timeline Items',
			'UwApprovalinfo' => 'Uw Approvalinfos',
			'UwInfodoc' => 'Uw Infodocs',
			'UwStatus' => 'Uw Statuses',
			'Vendor' => 'Vendors',
		];
		asort($expected);
		$result = $this->MaintenanceDashboard->getModelOptions();
		$this->assertSame($expected, $result);
	}

/**
 * testGetContentData
 *
 * @covers MaintenanceDashboard::getContentData
 * @return void
 * @access public
 */
	public function testGetContentData() {
		$modelName = "Network";
		$result = $this->MaintenanceDashboard->getContentData($modelName);
		$this->assertNotEmpty($result['headers']);
		$this->assertNotEmpty($result['modelData']);
		$this->assertNotEmpty($result['modelName']);
	}

/**
 * testGetModelData
 *
 * @covers MaintenanceDashboard::getModelData
 * @return void
 * @access public
 */
	public function testGetModelData() {
		$maintenanceModels = $this->MaintenanceDashboard->modelNames;
		foreach ($maintenanceModels as $modelName) {
			//test get All Records of each maintainable model
			$result = $this->MaintenanceDashboard->getModelData($modelName);
			$this->assertNotNull($result);
		}
			//test get specific record
			$modelName = "Network";
			$id = '666ea487-76cb-4721-bfdb-b12e3c1c0e09';
			$result = $this->MaintenanceDashboard->getModelData($modelName, $id);
			$expected = ClassRegistry::init($modelName)->find("first", ['conditions' => ["$modelName.id" => $id]]);
			$this->assertSame($expected, $result);
	}

/**
 * testSainitizeData
 *
 * @covers MaintenanceDashboard::sainitizeData
 * @return void
 * @access public
 */
	public function testSainitizeData() {
		$data = [
			'ProductsServicesType' => [
				'custom_labels' => [
					'Lorem ipsum' => 'dolor sit amet',
					'consectetur' => 'adipiscing elit',
					'sed do eiusmod' => 'tempor incididunt ut labore'
				]
			]
		];
		$this->MaintenanceDashboard->sainitizeData($data);
		$this->assertFalse(is_array($data['ProductsServicesType']['custom_labels']));
		$this->assertTrue(unserialize($data['ProductsServicesType']['custom_labels']));
	}

/**
 * testDeleteAndLog
 *
 * @covers MaintenanceDashboard::deleteAndLog
 * @return void
 * @access public
 */
	public function testDeleteAndLog() {
		$LogableLog = ClassRegistry::init('LoggableLog');
		$Network = ClassRegistry::init('Network');

		//Test deletion and loging of specific Model record
		$modelName = "Network";
		$id = '666ea487-76cb-4721-bfdb-b12e3c1c0e09';
		$this->MaintenanceDashboard->deleteAndLog($id, $modelName);
		$result = $Network->hasAny(['id' => $id]);
		$this->assertFalse($result);

		$result = $LogableLog->hasAny(['foreign_key' => $id, 'model' => $modelName, 'action' => 'adminMaintenanceDelete']);
		$this->assertTrue($result);
	}

/**
 * testCountAffectedRecords
 *
 * @covers MaintenanceDashboard::countAffectedRecords
 * @return void
 * @access public
 */
	public function testCountAffectedRecords() {
		$modelName = "ProductsServicesType";
		$id = '10000001-1001-1001-1001-100000000001';
		$result = $this->MaintenanceDashboard->countAffectedRecords($id, $modelName);
		$this->assertEqual(0, $result);
		$id = '952e7099-42b7-48d0-909b-6cb09d4d6706'; //Amex product is present in several associated model records
		$result = $this->MaintenanceDashboard->countAffectedRecords($id, $modelName);
		$this->assertGreaterThanOrEqual(4, $result);
	}

/**
 * testGetDeletedFromLog
 *
 * @covers MaintenanceDashboard::getDeletedFromLog
 * @return void
 * @access public
 */
	public function testGetDeletedFromLog() {
		$Network = ClassRegistry::init('Network');

		//Test data is backed up after delete
		$modelName = "Network";
		$id = '666ea487-76cb-4721-bfdb-b12e3c1c0e09';
		$expected = $Network->find('first', ['conditions' => ['id' => $id]]);
		//Delete data create back up
		$this->MaintenanceDashboard->deleteAndLog($id, $modelName);
		//Check back up
		$backedUpData = $this->MaintenanceDashboard->getDeletedFromLog($id, $modelName);
		$this->assertNotEmpty($backedUpData);
		//Original data should be the same as back up
		$this->assertSame($expected, $backedUpData);
	}

/**
 * testUndoDelete
 *
 * @covers MaintenanceDashboard::undoDelete
 * @return void
 * @access public
 */
	public function testUndoDelete() {
		//Test data is backed up after delete
		$Network = ClassRegistry::init('Network');
		$modelName = "Network";
		$id = '666ea487-76cb-4721-bfdb-b12e3c1c0e09';
		//Delete
		$deleted = $this->MaintenanceDashboard->deleteAndLog($id, $modelName);
		$this->assertTrue($deleted);
		$result = $Network->hasAny(['id' => $id]);
		$this->assertFalse($result);

		//Undo
		$this->MaintenanceDashboard->undoDelete($id, $modelName);
		$result = $Network->hasAny(['id' => $id]);
		$this->assertTrue($result);
	}

/**
 * testUndoDeleteException
 *
 * @covers MaintenanceDashboard::undoDelete
 * @return void
 * @access public
 */
	public function testUndoDeleteException() {
		try {
			$this->MaintenanceDashboard->undoDelete('10000001-1001-1001-1001-100000000001', 'LoremIpsumModelName');
		} catch(Exception $e) {
			$this->assertEqual($e->getMessage(), 'Failed to undo: unable to find backed up data from which to restore!');
		}
	}

/**
 * testSaveCustomSortModelDataExceptionThrown
 *
 * @covers MaintenanceDashboard::saveCustomSortModelData
 * @return void
 * @access public
 */
	public function testSaveCustomSortModelDataExceptionThrown() {
		try {
			$this->MaintenanceDashboard->saveCustomSortModelData($this->MaintenanceDashboard, []);
		} catch(Exception $e) {
				$this->assertEqual($e->getMessage(), 'Unable to accurately save record position! Expected member constant MaintenanceDashboard::SORT_FIELD is not defined.');
		}
	}
}
