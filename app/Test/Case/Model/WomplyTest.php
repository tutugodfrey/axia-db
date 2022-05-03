<?php
App::uses('Womply', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('File', 'Utility');

/**
 * Womply Test Case
 *
 */
class WomplyTest extends AxiaTestCase {

/**
 * Test to run for the test case (e.g array('testFind', 'testView'))
 * If this attribute is not empty only the tests from the list will be executed
 *
 * @var array
 * @access protected
 */
	protected $_testsToRun = [];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Womply = ClassRegistry::init('Womply');
		$this->WomplyMerchantLog = ClassRegistry::init('WomplyMerchantLog');
		$this->Merchant = ClassRegistry::init('Merchant');
		$this->TimelineEntry = ClassRegistry::init('TimelineEntry');
		$this->testParFileName = "test_par_file";
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Womply);
		parent::tearDown();
	}

/**
 * _getExpectedCase
 *
 * @param string $case the case that is expected to be returned by the test function to which this expectation data applies
 * @return void
 */
	protected function _getExpectedCase($case) {
		return [
			[
				'id' => '16b15921-5092-4884-9236-5e185aac336e',
				'merchant_mid' => '1239220055005574',
				'merchant_dba' => 'Zeo Brothers Productions Inc. (Website)',
				'merchant_ownership_type' => 'Corporation',
				'merchant_mail_contact' => 'DANIEL J ZEO',
				'timeline_date_completed' => null,
				'rep_name' => 'Joseph Krueger (New)',
				'corp_address_title' => null,
				'corp_address_street' => null,
				'corp_address_city' => null,
				'corp_address_state' => null,
				'corp_address_zip' => null,
				'corp_address_phone' => null,
				'mail_address_title' => null,
				'mail_address_street' => null,
				'mail_address_city' => null,
				'mail_address_state' => null,
				'mail_address_zip' => null,
				'mail_address_phone' => null,
				'site_street' => null,
				'site_city' => null,
				'site_state' => null,
				'site_zip' => null,
				'site_phone' => null,
				'merchant_contact' => 'Matthew Spiece',
				'merchant_tin' => 'tdGYA+LVUNxE4dHOjlpt6OGwFE1rdeRNOW3hcQC/uoE47uz5FPcxl1xpFUHTd8+oS8oejyPof3Nn6r8okM7+C2hmzRw+1r+MpiXZ8k+0Si5l/bsgpG1wUdHQPEbcg5wv',
				'merchant_sic' => 5732,
				'merchant_email' => 'dan@zeobrothers.com',
				'acquirer' => 'Axia',
				'bin' => null,
				'bank_routing_number' => null,
				'bank_dda_number' => null,
				'ref_name' => null,
				'res_name' => null,
				'network_description' => null,
				'womply_active' => true,
				'case' => $case //this is the only thing that changes
			]
		];
	}

/**
 * testGetUpdates
 *
 * @covers Womply::getUpdates()
 * @return void
 */
	public function testGetUpdates() {
		$expected = $this->_getExpectedCase('UPDATED');
		//first save merchant
		$this->_saveMerchant();
		//Simulate the addition of the merchant to womply and to the log
		$added = $this->Womply->getAdditions();
		$this->Womply->setMarContent($added);
		//created note_date must be greater than log creation time
		$date = date_create(date('Y-m-d'));
		date_add($date, date_interval_create_from_date_string("1 days"));
		//existance of an change-retalted note is required as part of the query
		$changeNote = [
			'user_id' => 'ab912cea-0900-433c-94f0-60d4789150a4',
			'merchant_id' => '16b15921-5092-4884-9236-5e185aac336e',
			'note' => 'Test tseT',
			'note_type_id' => NoteType::CHANGE_REQUEST_ID,
			'general_status' => 'COMP',
			'note_date' => date_format($date, "Y-m-d") . " 00:00:00"
		];
		//detach listener
		$this->Merchant->MerchantNote->detachListener('SystemTransactionListener');
		$this->Merchant->MerchantNote->create();
		//disable callbacks to prevent modification of note_date on beforeSave
		$this->Merchant->MerchantNote->save($changeNote, ['validate' => false, 'callbacks' => false]);

		$actual = $this->Womply->getUpdates();

		// $this->Womply->setMarContent($added);
		$this->assertSame($expected, $actual);
	}

/**
 * testGetRemoval
 *
 * @covers Womply::getRemovals()
 * @return void
 */
	public function testGetRemovals() {
		$expected = $this->_getExpectedCase('DELETED');
		//first save merchant
		$this->_saveMerchant();
		//Simulate the addition of the merchant to womply and to the log
		$added = $this->Womply->getAdditions();
		$this->Womply->setMarContent($added);

		//Simulate the removal of the merchant from womply
		$updatedMerchant = ['id' => '16b15921-5092-4884-9236-5e185aac336e', 'womply_merchant_enabled' => false];
		$this->Merchant->create();
		$this->Merchant->save($updatedMerchant, ['validate' => false]);
		$actual = $this->Womply->getRemovals();

		// $this->Womply->setMarContent($added);
		$this->assertSame($expected, $actual);
	}

/**
 * testGetReactivations
 *
 * @covers Womply::getReactivations()
 * @return void
 */
	public function testGetReactivations() {
		$expected = $this->_getExpectedCase('RECREATED');
		//first save merchant
		$this->_saveMerchant();
		//Simulate the addition of the merchant to womply and to the log
		$added = $this->Womply->getAdditions();
		$this->Womply->setMarContent($added);

		//Create a cancellation for the saved merchant
		$mCancel = [
			'merchant_id' => '16b15921-5092-4884-9236-5e185aac336e',
			'status' => 'COMP',
		];
		$this->Merchant->MerchantCancellation->create();
		$this->Merchant->MerchantCancellation->save($mCancel, ['validate' => false]);

		//Simulate the cancellation of the merchant to womply and to the log
		$cancellation = $this->Womply->getCancellations();
		$this->Womply->setMarContent($cancellation);

		//Will have to delete creation log entry since time between creation and cancellation is < 1sec
		//and queried data is sorted by record created time DEC. In a real scenario time will differ by days.
		$this->WomplyMerchantLog->deleteAll(['details like \'%"CREATED"%\'']);

		//Simulate merchant reacitvation
		$this->Merchant->MerchantCancellation->delete($this->Merchant->MerchantCancellation->id);

		//existance of an acticvation-retalted note is required as part of the reactivations query
		$activationNote = [
			'user_id' => 'ab912cea-0900-433c-94f0-60d4789150a4',
			'merchant_id' => '16b15921-5092-4884-9236-5e185aac336e',
			'note' => 'Merchant Reactivated for some reason',
		];
		//detach listener
		$this->Merchant->MerchantNote->detachListener('SystemTransactionListener');
		$this->Merchant->MerchantNote->create();
		$this->Merchant->MerchantNote->save($activationNote, ['validate' => false]);

		$actual = $this->Womply->getReactivations();
		$this->assertSame($expected, $actual);
	}

/**
 * testGetCancellations
 *
 * @covers Womply::getCancellations()
 * @return void
 */
	public function testGetCancellations() {
		$expected = $this->_getExpectedCase('DELETED');

		//first save merchant
		$this->_saveMerchant();
		//Simulate the addition of the merchant to womply and to the log
		$added = $this->Womply->getAdditions();
		$this->Womply->setMarContent($added);

		//Create a cancellation for the saved merchant
		$mCancel = [
			'merchant_id' => '16b15921-5092-4884-9236-5e185aac336e',
			'status' => 'COMP',
		];
		$this->Merchant->MerchantCancellation->save($mCancel, array('validate' => false));
		$actual = $this->Womply->getCancellations();
		$this->assertSame($expected, $actual);
	}
/**
 * testGetAdditions
 *
 * @covers Womply::getAdditions()
 * @return void
 */
	public function testGetAdditions() {
		$expected = $this->_getExpectedCase('CREATED');
		$this->_saveMerchant();
		$actual = $this->Womply->getAdditions();
		$this->assertSame($expected, $actual);
	}

/**
 * testSetMarContent
 *
 * @param array $data data to be converted as pipe delimited
 * @param string $actionOverride a letter A|U|D which will be set to all merchants on $data
 * @param sting $expected the expected pipe delimited string result
 * @dataProvider providerTestSetMarContent
 * @covers Womply::setMarContent()
 * @return void
 */
	public function testSetMarContent($data, $actionOverride, $expected) {
		$actual = $this->Womply->setMarContent($data, $actionOverride);
		$actualLogCount = $this->WomplyMerchantLog->find('count', ['conditions' => ['merchant_id' => Hash::extract($data, '{n}.id')]]);
		$expectedLogCount = count($data);
		$this->assertSame($expectedLogCount, $actualLogCount);
		$this->assertSame($expected, $actual);
	}

/**
 * testSaveMarGzipFile
 *
 * @param array $data data to be converted as pipe delimited
 * @param string $actionOverride a letter A|U|D which will be set to all merchants on $data
 * @param sting $expected the expected pipe delimited string result
 * @dataProvider providerTestSetMarContent
 * @covers Womply::saveMarGzipFile()
 * @return void
 */
	public function testSaveMarGzipFile($data, $actionOverride, $expected) {
		$fileContent = $this->Womply->setMarContent($data, $actionOverride);
		$fileName = "merchant_action." . Womply::WOMPLY_ID . date('.Y.m.d.H.i.s.' . substr(date('u'), 0, 3));
		$this->Womply->saveMarGzipFile($fileContent, $fileName);
		$file = new File(APP . "tmp/$fileName");
		$file->open('r');
		$actual = $file->read();
		$file->close('r');
		$this->assertSame($expected, $actual);
		//Remove files
		unlink(APP . "tmp/$fileName");
		unlink(APP . "tmp/$fileName.gz");
	}

/**
 * testSendDailyMAR()
 *
 * @return array
 */
	public function testSendDailyMAR() {
		$this->_saveMerchant();
		$result = $this->Womply->sendDailyMAR();
		$this->assertTrue($result);
	}

/**
 * Provider for testGetUserNameByComplexId
 *
 * @dataProvider
 * @return void
 */
	public function providerTestSetMarContent() {
		return [
			[
				[//*******Data param
					[
						'id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'merchant_mid' => '7641000030002785',
						'merchant_dba' => 'Zeo Brothers Productions Inc. (Website)',
						'merchant_ownership_type' => 'Corporation',
						'merchant_mail_contact' => 'DANIEL J ZEO',
						'timeline_date_completed' => null,
						'rep_name' => 'Joseph Krueger (New)',
						'corp_address_title' => 'Zeo Brothers Productions Inc.',
						'corp_address_street' => '244 E County Line Rd',
						'corp_address_city' => 'Hatboro',
						'corp_address_state' => 'PA',
						'corp_address_zip' => '19040',
						'corp_address_phone' => '(215)956-0328',
						'mail_address_title' => null,
						'mail_address_street' => null,
						'mail_address_city' => null,
						'mail_address_state' => null,
						'mail_address_zip' => null,
						'mail_address_phone' => null,
						'site_street' => '244 E County Line Rd',
						'site_city' => 'Hatboro',
						'site_state' => 'PA',
						'site_zip' => '19040',
						'site_phone' => '(215)956-0328',
						'merchant_contact' => 'Matthew Spiece',
						'merchant_tin' => '232530240',
						'merchant_sic' => 5732,
						'merchant_email' => 'dan@zeobrothers.com',
						'acquirer' => 'Axia',
						'bin' => '449778',
						'bank_routing_number' => '036001808',
						'bank_dda_number' => '4284245440',
						'ref_name' => 'Unique Business Systems ',
						'res_name' => null,
						'network_description' => 'TSYS',
						'womply_active' => true,
						'case' => 'CREATED',
					]
				],
					//override param
					'',
					//expected pipe delimited string
					"H|8|0063\n" .
					"B|7641000030002785|7641000030002785|232530240|A||Zeo Brothers Productions Inc. (Website)|244 E County Line Rd||Hatboro|PA|US|19040|Matthew Spiece|2159560328||dan@zeobrothers.com|||Owner Name 1|||Owner Name 2|Zeo Brothers Productions Inc.|244 E County Line Rd||Hatboro|PA|US|19040|||DANIEL J ZEO|2159560328||5732|Unique Business Systems |Joseph Krueger (New)|036001808|4284245440|CALL\n" .
					"F|1"
			],
			[
				[//*******Data param
					[
						'id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'merchant_mid' => '7641000030002785',
						'merchant_dba' => 'Zeo Brothers Productions Inc. (Website)',
						'merchant_ownership_type' => 'Corporation',
						'merchant_mail_contact' => 'DANIEL J ZEO',
						'timeline_date_completed' => null,
						'rep_name' => 'Joseph Krueger (New)',
						'corp_address_title' => 'Zeo Brothers Productions Inc.',
						'corp_address_street' => '244 E County Line Rd',
						'corp_address_city' => 'Hatboro',
						'corp_address_state' => 'PA',
						'corp_address_zip' => '19040',
						'corp_address_phone' => '(215)956-0328',
						'mail_address_title' => null,
						'mail_address_street' => null,
						'mail_address_city' => null,
						'mail_address_state' => null,
						'mail_address_zip' => null,
						'mail_address_phone' => null,
						'site_street' => '244 E County Line Rd',
						'site_city' => 'Hatboro',
						'site_state' => 'PA',
						'site_zip' => '19040',
						'site_phone' => '(215)956-0328',
						'merchant_contact' => 'Matthew Spiece',
						'merchant_tin' => '232530240',
						'merchant_sic' => 5732,
						'merchant_email' => 'dan@zeobrothers.com',
						'acquirer' => 'Axia',
						'bin' => '449778',
						'bank_routing_number' => '036001808',
						'bank_dda_number' => '4284245440',
						'ref_name' => 'Unique Business Systems ',
						'res_name' => null,
						'network_description' => 'TSYS',
						'womply_active' => true,
						'case' => 'CREATED',
					],
					[
						'id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040',
						'merchant_mid' => '7641000030002785',
						'merchant_dba' => 'Zeo Brothers Productions Inc. (Website)',
						'merchant_ownership_type' => 'Corporation',
						'merchant_mail_contact' => 'DANIEL J ZEO',
						'timeline_date_completed' => null,
						'rep_name' => 'Joseph Krueger (New)',
						'corp_address_title' => 'Zeo Brothers Productions Inc.',
						'corp_address_street' => '244 E County Line Rd',
						'corp_address_city' => 'Hatboro',
						'corp_address_state' => 'PA',
						'corp_address_zip' => '19040',
						'corp_address_phone' => '(215)956-0328',
						'mail_address_title' => null,
						'mail_address_street' => null,
						'mail_address_city' => null,
						'mail_address_state' => null,
						'mail_address_zip' => null,
						'mail_address_phone' => null,
						'site_street' => '244 E County Line Rd',
						'site_city' => 'Hatboro',
						'site_state' => 'PA',
						'site_zip' => '19040',
						'site_phone' => '(215)956-0328',
						'merchant_contact' => 'Matthew Spiece',
						'merchant_tin' => '232530240',
						'merchant_sic' => 5732,
						'merchant_email' => 'dan@zeobrothers.com',
						'acquirer' => 'Axia',
						'bin' => '449778',
						'bank_routing_number' => '036001808',
						'bank_dda_number' => '4284245440',
						'ref_name' => 'Unique Business Systems ',
						'res_name' => null,
						'network_description' => 'TSYS',
						'womply_active' => true,
						'case' => 'CREATED',
					]
				],
					//override param
					'U',
					//expected pipe delimited string
					"H|8|0063\n" .
					"B|7641000030002785|7641000030002785|232530240|U||Zeo Brothers Productions Inc. (Website)|244 E County Line Rd||Hatboro|PA|US|19040|Matthew Spiece|2159560328||dan@zeobrothers.com|||Owner Name 1|||Owner Name 2|Zeo Brothers Productions Inc.|244 E County Line Rd||Hatboro|PA|US|19040|||DANIEL J ZEO|2159560328||5732|Unique Business Systems |Joseph Krueger (New)|036001808|4284245440|CALL\n" .
					"B|7641000030002785|7641000030002785|232530240|U||Zeo Brothers Productions Inc. (Website)|244 E County Line Rd||Hatboro|PA|US|19040|Matthew Spiece|2159560328||dan@zeobrothers.com|||Owner Name 1|||Owner Name 2|Zeo Brothers Productions Inc.|244 E County Line Rd||Hatboro|PA|US|19040|||DANIEL J ZEO|2159560328||5732|Unique Business Systems |Joseph Krueger (New)|036001808|4284245440|CALL\n" .
					"F|2"
			],
		];
	}

/**
 * _saveMerchant
 *
 * @param boolean $womplyEnabled Sets Metchant.womply_merchant_enabled
 * @param boolean $womplyCustomer Sets Merchant.womply_customer
 * @return array
 */
	protected function _saveMerchant($womplyEnabled = true, $womplyCustomer = false) {
		$newMerchant = [
			'Merchant' => [
				'id' => '16b15921-5092-4884-9236-5e185aac336e',
				'merchant_id_old' => '1239220055005574',
				'user_id' => 'ab912cea-0900-433c-94f0-60d4789150a4',
				'user_id_old' => '239',
				'merchant_mid' => '1239220055005574',
				'merchant_dba' => 'Zeo Brothers Productions Inc. (Website)',
				'merchant_contact' => 'Matthew Spiece',
				'merchant_email' => 'dan@zeobrothers.com',
				'merchant_ownership_type' => 'Corporation',
				'merchant_tin' => 'tdGYA+LVUNxE4dHOjlpt6OGwFE1rdeRNOW3hcQC/uoE47uz5FPcxl1xpFUHTd8+oS8oejyPof3Nn6r8okM7+C2hmzRw+1r+MpiXZ8k+0Si5l/bsgpG1wUdHQPEbcg5wv',
				'active_date' => '2016-12-20',
				'ref_seq_number_old' => 232,
				'network_id' => '83dd7dff-2aff-4317-9c08-b1a4525cfb30',
				'network_id_old' => '5',
				'merchant_buslevel' => 'Retail',
				'merchant_sic' => 5732,
				'entity_old' => 'AX',
				'entity_id' => '2843e82e-c32f-4aa8-a8ee-a6edbc418f0a',
				'merchant_bustype' => 'ELECTRONIC SALES',
				'merchant_url' => 'www.zeobrothers.com',
				'merchant_tin_disp' => '0240',
				'active' => 1,
				'cancellation_fee_id' => 'b319c855-dc97-49f5-b9e0-38fe4e1f4fed',
				'cancellation_fee_id_old' => '2',
				'merchant_contact_position' => 'Operations Mgr',
				'merchant_mail_contact' => 'DANIEL J ZEO',
				'merchant_bin_id' => 'f2facc46-52b3-4591-81e0-d71b562cbfcf',
				'merchant_bin_id_old' => '1',
				'merchant_acquirer_id' => '192f8406-836d-4341-a1ee-d9d568cdb53d',
				'merchant_acquirer_id_old' => '1',
				'merchant_ps_sold' => 'Audio, Lighting, Visual Rentals',
				'ref_p_type' => 'percentage-grossprofit',
				'ref_p_value' => '40.0000',
				'ref_p_pct' => 100,
				'partner_id' => '58b914e7-f340-4f7c-8e75-13e934627ad4',
				'partner_id_old' => '37',
				'cobranded_application_id' => 10455,
				'womply_merchant_enabled' => $womplyEnabled,
				'womply_customer' => $womplyCustomer,
			],
			'User' => [
				'password' => '*****',
				'id' => 'ab912cea-0900-433c-94f0-60d4789150a4',
				'user_id_old' => '239',
				'user_type_id' => 'e1adcd96-0611-4b17-8508-4dc41d468a25',
				'user_type_old' => 'REP',
				'user_title' => 'Inside Sales Rep',
				'user_first_name' => 'Joseph',
				'user_last_name' => 'Krueger (New)',
				'username' => 'jkruegernew',
				'user_email' => 'jkrueger@axiapayments.com',
				'parent_user_id' => '151e6c14-f930-4414-9e07-6156e6a1a4db',
				'parent_user_id_old' => '190',
				'entity_id' => '2843e82e-c32f-4aa8-a8ee-a6edbc418f0a',
				'entity_old' => 'AX',
				'active' => 1,
				'initials' => 'JKN',
				'manager_percentage' => '5.0000',
				'date_started' => '2014-03-15',
				'split_commissions' => false,
				'bet_extra_pct' => false,
				'discover_bet_extra_pct' => false,
				'womply_user_enabled' => true,
				'womply_active' => true,
				'pci_user_enabled' => true,
				'is_blocked' => false,
				'fullname' => 'Joseph Krueger (New)'
			],
		];
		//Save new merchant, validation is irrelevant for this test
		$this->Merchant->saveAll($newMerchant, array('validate' => false));
	}
/**
 * testGetEmailReadyPARdata
 *
 * @covers Womply::getEmailReadyPARdata()
 * @return void
 */
	public function testGetEmailReadyPARdata() {
		$expectedPARAttachmentData = "H|7|63\n" .
				"B|3948000030002785|DBA1|20151101|Add|Axia Insights|29.99|\n" .
				"B|3948000030003045|DBA2|20151101|Add|Axia Insights|29.99|\n" .
				"B|4541619030000153|DBA3|20151101|Add|Axia Insights|59.98|\n" .
				"B|3948906204000946|DBA4|20151101|Add|Axia Insights|59.98|\n" .
				"F|4\n";

		//Save a file in the remote womply test folder
		$this->_saveRemoteTestPARFile();
		$result = $this->Womply->getEmailReadyPARdata();

		$this->assertSame($expectedPARAttachmentData, $result['attachment']['test_par_file.txt']['data']);
		$this->assertContains("This month's file is test_par_file.gz", $result['body'] );
		$this->assertContains("We have retrieved test_par_file.gz from Womply", $result['body'] );
		$this->assertContains("4 Merchants Updated", $result['body'] );

		//**At this time deleting remote test files is failing, perhaps because of restricted remote directory permissions
		$this->_deleteRemoteTestPARFile();
	}

/**
 * _deleteRemoteTestPARFile()
 * 
 * @throws Exception
 * @return void
 */
	protected function _deleteRemoteTestPARFile() {
		$fileName = $this->testParFileName;
		$connection = ssh2_connect('sftp.womply.com', 22, array('hostkey' => 'ssh-rsa'));
		$dir = new Folder(Configure::read('WomplySSHKey.path'));
		if (!$connection) {
			throw new Exception('Connection failed');
		}
		if (!ssh2_auth_pubkey_file($connection, 'client-' . Womply::WOMPLY_ID, $dir->path . DS . 'womply.pub', $dir->path . DS . 'womply')) {
			throw new Exception('Public Key Authentication Failed');
		}

		$sftp = ssh2_sftp($connection);
		$sftpPath = '/from_womply_test/product_action_report/';

		//**At this time deleting remote files apears to be failing, probably because directory permissions?
		//file is renamed with an added subfix '.downloaded' at run time
		ssh2_sftp_unlink($sftp, 'ssh2.sftp://' . intval($sftp) . $sftpPath . $fileName . '.gz.downloaded');
		//Delete original
		ssh2_sftp_unlink($sftp, 'ssh2.sftp://' . intval($sftp) . $sftpPath . $fileName . '.gz');
	}
/**
 * _saveRemoteTestPARFile()
 *
 * @throws Exception
 * @return void
 */
	protected function _saveRemoteTestPARFile() {
		$tstData = "H|7|63\n" .
				"B|3948000030002785|DBA1|20151101|Add|Axia Insights|29.99|\n" .
				"B|3948000030003045|DBA2|20151101|Add|Axia Insights|29.99|\n" .
				"B|4541619030000153|DBA3|20151101|Add|Axia Insights|59.98|\n" .
				"B|3948906204000946|DBA4|20151101|Add|Axia Insights|59.98|\n" .
				"F|4\n";
		$fileName = $this->testParFileName;

		$savePath = APP . "tmp/" . $fileName;

		$connection = ssh2_connect('sftp.womply.com', 22, array('hostkey' => 'ssh-rsa'));
		$dir = new Folder(Configure::read('WomplySSHKey.path'));
		if (!$connection) {
			throw new Exception('Connection failed');
		}
		if (!ssh2_auth_pubkey_file($connection, 'client-' . Womply::WOMPLY_ID, $dir->path . DS . 'womply.pub', $dir->path . DS . 'womply')) {
			throw new Exception('Public Key Authentication Failed');
		}

		$sftp = ssh2_sftp($connection);
		$sftpPath = '/from_womply_test/product_action_report/';
		$sftpStream = @fopen('ssh2.sftp://' . intval($sftp) . $sftpPath . $fileName . '.gz', 'w');
		if (@fwrite($sftpStream, $tstData) === false) {
			throw new Exception("Could not send data from file: $gzOut.");
		}
		$dirHandle = opendir('ssh2.sftp://' . intval($sftp) . $sftpPath);
		fclose($sftpStream);
	}
/**
 * testUpdateMerchants
 *
 * @param string $data data to be saved as pipe delimited gz file. This emulates data downloaded from WOMPLY 
 * @param sting $expectedResult the expected result
 * @param sting $expectedUpdated the expected updated merchants list
 * @param sting $expectedCancelledCount the expected count of cancelled merchants
 * @dataProvider providerTestUpdateMerchants
 * @covers Womply::updateMerchants()
 * @return void
 */
	public function testUpdateMerchants($data, $expectedResult, $expectedUpdated, $expectedCancelledCount) {
		$savePath = APP . "tmp/tests/par_test";
		$fp = fopen($savePath, 'w');
		fwrite($fp, $data);
		fclose($fp);
		$gzOut = $savePath . ".gz";
		file_put_contents("compress.zlib://$gzOut", file_get_contents($savePath));

		$actualResult = $this->Womply->updateMerchants($gzOut);

		$actualUpdated = $this->Merchant->find('list',
		[
			'fields' => ['merchant_mid'],
			'conditions' => ['womply_customer' => true],
			'order' => 'merchant_mid DESC'
		]);

		//One timeline entry per updated merchant
		$expectedAddedCount = count($actualUpdated);
		$actualAddedCount = $this->TimelineEntry->find('count',
		[
			'conditions' => [
				'timeline_date_completed' => date('Y-m-d'),
				'timeline_item_id' => TimelineItem::SOLD_WOMPLY,
			],
		]);
		$actualCancelledCount = $this->TimelineEntry->find('count',
		[
			'conditions' => [
				'timeline_date_completed' => date('Y-m-d'),
				'timeline_item_id' => TimelineItem::CANCELLED_WOMPLY,
				],
		]);
		unlink($savePath);
		unlink("$savePath.gz");

		$this->assertSame($expectedAddedCount, $actualAddedCount);
		$this->assertSame($expectedCancelledCount, $actualCancelledCount);
		$this->assertSame($expectedUpdated, $actualUpdated);
		$this->assertSame($expectedResult, $actualResult['result_msg']);
	}

/**
 * Provider for testUpdateMerchants
 *
 * @dataProvider
 * @return void
 */
	public function providerTestUpdateMerchants() {
		return [
			[
				//*******Data param
				"H|7|63\n" .
				"B|3948000030002785|DBA1|20151101|Add|Axia Insights|29.99|\n" .
				"B|3948000030003045|DBA2|20151101|Add|Axia Insights|29.99|\n" .
				"B|4541619030000153|DBA3|20151101|Add|Axia Insights|59.98|\n" .
				"B|3948906204000946|DBA4|20151101|Add|Axia Insights|59.98|\n" .
				"F|4\n",
				//expected result
				"4 Merchants Updated\n",
				// expected updated data
				[
					'00000000-0000-0000-0000-000000000005' => '4541619030000153',
					'00000000-0000-0000-0000-000000000004' => '3948906204000946',
					'4e3587be-aafb-48c4-9b6b-8dd26b8e94aa' => '3948000030003045',
					'3bc3ac07-fa2d-4ddc-a7e5-680035ec1040' => '3948000030002785'
				],
				//expected timeline entries created for cancelled womply merchant
				0
			],
			[
				//*******Data param
				"H|7|63\n" .
				"B|3948000030002785|DBA1|20151101|Refund|Axia Insights|29.99|\n" .
				"B|3948000030003045|DBA2|20151101|Refund|Axia Insights|29.99|\n" .
				"B|4541619030000153|DBA3|20151101|Refund|Axia Insights|59.98|\n" .
				"B|3948906204000946|DBA4|20151101|Refund|Axia Insights|59.98|\n" .
				"F|4\n",
				//expected result
				"There were 0 records updated, but there should have been 4\n" .
				"Please have development review this PAR\n",
				// expected updated data
				[/*no updates w/this dataset*/],

				//expected timeline entries created for cancelled womply merchant
				0
			],
			[
				//*******Data param
				"H|7|63\n" .
				"B|3948000030002785|DBA1|20151101|Add|Axia Insights|29.99|\n" .
				"B|3948000030003045|DBA2|20151101|Add|Axia Insights|29.99|\n" .
				"B|4541619030000153|DBA3|20151101|Refund|Axia Insights|59.98|\n" .
				"B|3948906204000946|DBA4|20151101|Refund|Axia Insights|59.98|\n" .
				"F|4\n",
				//expected result
				"There were 2 records updated, but there should have been 4\n" .
				"Please have development review this PAR\n",
				// expected updated data
				[
					'4e3587be-aafb-48c4-9b6b-8dd26b8e94aa' => '3948000030003045',
					'3bc3ac07-fa2d-4ddc-a7e5-680035ec1040' => '3948000030002785'
				],
				//expected timeline entries created for cancelled womply merchant
				0
			],
		];
	}
}
