<?php
App::uses('AppStatus', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AppStatus Test Case
 *
 */
class AppStatusTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AppStatus = ClassRegistry::init('AppStatus');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AppStatus);
		parent::tearDown();
	}

/**
 * testConstructorAndValidateDefinition
 *
 * @covers AppStatus::__construct
 * @covers AppStatus::validate property
 * @return void
 */
	public function testConstructorAndValidateDefinition() {
		$AppStatusModel = $this->_mockModel('AppStatus');

		$expected = [
			'merchant_ach_app_status_id',
			'user_compensation_profile_id',
			'rep_cost',
			'axia_cost',
			'rep_expedite_cost',
			'axia_expedite_cost_tsys',
			'axia_expedite_cost_sage'
		];
		$this->assertEquals($expected, array_keys($AppStatusModel->validate));
		unset($AppStatusModel);
	}

/**
 * testAdd
 *
 * @return void
 */
	public function testAdd() {
		$this->assertCount(2, $this->AppStatus->find('list'));

		$data = [];
		$this->assertFalse($this->AppStatus->add($data));
		$this->assertCount(2, $this->AppStatus->find('list'));

		$data = [
			'AppStatus' => [
				'merchant_ach_app_status_id' => '00000000-0000-0000-0000-000000000001',
				'user_compensation_profile_id' => '00000000-0000-0000-0000-000000000002',
			],
		];
		$this->assertTrue($this->AppStatus->add($data));
		$this->assertCount(3, $this->AppStatus->find('list'));
		$newRecord = $this->AppStatus->read(array_keys($data['AppStatus']), $this->AppStatus->id);
		$this->assertEquals($data, $newRecord);
	}

/**
 * testAddValidationError
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Could not save the appStatus, please check your inputs
 * @return void
 */
	public function testAddValidationError() {
		$data = [
			'AppStatus' => [
				'merchant_ach_app_status_id' => 'lorem ipsum',
				'user_compensation_profile_id' => 'dolor sit amet',
				'rep_cost' => 'not-a-valid-number'
			],
		];
		$this->AppStatus->add($data);
	}

/**
 * testEdit
 *
 * @return void
 */
	public function testEdit() {
		$firstRecord = $this->AppStatus->find('first');
		$this->assertNotEmpty($firstRecord);

		$recordId = $firstRecord['AppStatus']['id'];

		$this->assertEquals($firstRecord, $this->AppStatus->edit($recordId, []));

		$data = [
			'AppStatus' => [
				'id' => $recordId,
				'user_compensation_profile_id' => $firstRecord['AppStatus']['user_compensation_profile_id'],
				'rep_cost' => $firstRecord['AppStatus']['rep_cost'] + 0.01,
				'axia_cost' => $firstRecord['AppStatus']['axia_cost'] + 7,
			]
		];
		$this->assertCount(2, $this->AppStatus->find('list'));
		$this->assertTrue($this->AppStatus->edit($recordId, $data));
		$this->assertCount(2, $this->AppStatus->find('list'));
		$editedRecord = $this->AppStatus->read(array_keys($data['AppStatus']), $recordId);
		$this->assertEquals($data, $editedRecord);
	}

/**
 * testEditValidationError
 *
 * @return void
 */
	public function testEditValidationError() {
		$firstRecord = $this->AppStatus->find('first');
		$this->assertNotEmpty($firstRecord);

		$recordId = $firstRecord['AppStatus']['id'];
		$data = [
			'AppStatus' => [
				'id' => $recordId,
				'user_compensation_profile_id' => 'lorem ipsum',
				'rep_cost' => 'not-a-valid-number'
			],
		];
		$result = $this->AppStatus->edit($recordId, $data);
		$this->assertEquals($data, $result);
		$expected = [
			'rep_cost' => [
				'Please enter a numeric Rep Cost'
			]
		];
		$this->assertEquals($expected, $this->AppStatus->validationErrors);
	}

/**
 * testAddValidationError
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid App Status
 * @return void
 */
	public function testAddNotValid() {
		$this->AppStatus->edit('00000000-9999-0000-0000-000000000001', []);
	}

/**
 * testView
 *
 * @return void
 */
	public function testView() {
		$expected = [
			'AppStatus' => [
				'id' => '53763c81-7d58-4341-bc12-385a34627ad4',
				'merchant_ach_app_status_id' => 'ec4a7409-0ad7-48be-9d83-08356f3b16fe',
				'rep_cost' => '20',
				'axia_cost' => '0',
				'rep_expedite_cost' => '50',
				'axia_expedite_cost_tsys' => '0',
				'axia_expedite_cost_sage' => '0',
				'user_compensation_profile_id' => '6570e7dc-a4fc-444c-8929-337a34627ad4',
			],
		];
		$result = $this->AppStatus->view('53763c81-7d58-4341-bc12-385a34627ad4');
		$this->assertEquals($expected, $result);
	}

/**
 * testViewNotValid
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid App Status
 * @return void
 */
	public function testViewNotValid() {
		$this->AppStatus->view('00000000-9999-0000-0000-000000000001');
	}

/**
 * testValidateAndDeleteNotValid
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Invalid App Status
 * @return void
 */
	public function testValidateAndDeleteNotValid() {
		$this->AppStatus->validateAndDelete('00000000-9999-0000-0000-000000000001', []);
	}

/**
 * testValidateAndDeleteNotValid
 *
 * @expectedException Exception
 * @expectedExceptionMessage You need to confirm to delete this App Status
 * @return void
 */
	public function testValidateAndDeleteNotConfirm() {
		$this->AppStatus->validateAndDelete('53763c81-7d58-4341-bc12-385a34627ad4', ['AppStatus' => ['confirm' => 0]]);
	}

/**
 * testValidateAndDelete
 *
 * @return void
 */
	public function testValidateAndDelete() {
		$appStatusId = '53763c81-7d58-4341-bc12-385a34627ad4';
		$result = $this->AppStatus->validateAndDelete($appStatusId, []);
		$this->assertFalse($result);

		$data = ['AppStatus' => ['confirm' => 1]];
		$this->assertCount(2, $this->AppStatus->find('list'));
		$result = $this->AppStatus->validateAndDelete($appStatusId, $data);
		$this->assertTrue($result);
		$this->assertCount(1, $this->AppStatus->find('list'));
		$this->assertFalse($this->AppStatus->read(null, $appStatusId));
	}
}
