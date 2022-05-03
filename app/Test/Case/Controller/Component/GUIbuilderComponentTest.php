<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GUIbuilderComponent', 'Controller/Component');
App::uses('User', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * GUIbuilderComponent Test Case
 *
 */
class GUIbuilderComponentTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->GUIbuilder = new GUIbuilderComponent($Collection);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->GUIbuilder);

		parent::tearDown();
	}

/**
 * testGetAppQuantityTypeList
 *
 * @return void
 */
	public function testGetAppQuantityTypeList() {
		$this->assertSame(
		[
			GUIbuilderComponent::NEW_APP => 'New App',
			GUIbuilderComponent::ADDTL_LOC => 'Additional Location App',
		],
			GUIbuilderComponent::getAppQuantityTypeList()
		);
	}

/**
 * testGetStatusLabel
 *
 * @return void
 */
	public function testGetStatusLabel() {
		$list = GUIbuilderComponent::getStatusList();
		foreach ($list as $stat => $expLabel)
		$this->assertSame($expLabel, GUIbuilderComponent::getStatusLabel($stat));
		
	}

/**
 * testGetDatePartOptns
 *
 * @return void
 */
	public function testGetDatePartOptns() {
		
		$actual['days'] = $this->GUIbuilder->getDatePartOptns('d');
		$actual['months'] = $this->GUIbuilder->getDatePartOptns('m');
		$actual['years'] = $this->GUIbuilder->getDatePartOptns('y');
		for ($x = 1; $x<=31; $x++) {
			$this->assertSame(($x<=9)?"0$x":"$x",$actual['days'][ ($x<=9)?"0$x":"$x" ]);
		}
		for ($x = 1; $x<=12; $x++) {
			$this->assertSame(date("M", strtotime("2011-$x-01")),$actual['months'][$x]);
		}
		for ($x = date("Y") + 3; $x>2001; $x--) {
			$this->assertSame($x,$actual['years'][$x]);
		}		
	}

/**
 * testGetDatePartOptnsInvalidArgumentException
 *
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage The given argument junk is not recognized
 * @return void
 */
	public function testGetDatePartOptnsInvalidArgumentException() {
		$this->GUIbuilder->getDatePartOptns('junk');
	}

/**
 * testGetNoteTypesOptns
 *
 * @covers: GUIbuilderComponent::getNoteTypesOptns()
 * @return void
 */
	public function testGetNoteTypesOptns() {
		$actual = $this->GUIbuilder->getNoteTypesOptns();
		$this->assertSame(
			[   'All' => 'All',
				'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2' => 'Change Request',
				'0bfee249-5c37-417c-aec7-83dcd2b2f566' => 'General Note',
				'083e3d46-76f2-42d9-ad7b-6fb3a9775b3c' => 'Programming Note',
				'85b32624-aca2-44f2-9924-49cddc6b2e5a' => 'Installation & Setup Note'
			],
			$actual 
		);
	}

/**
 * testFormatDateFormFieldInvalidArgumentException
 *
 * @covers: GUIbuilderComponent::formatDateFormField()
 * @expectedException InvalidArgumentException
 * @expectedExceptionMessage formatDateFormField function expects array argument containing 3 elements month day and year as its values in that order.
 * @return void
 */
	public function testFormatDateFormFieldInvalidArgumentException() {
		$this->GUIbuilder->formatDateFormField([]);
	}

/**
 * testFormatDateFormField
 * 
 * @covers: GUIbuilderComponent::formatDateFormField()
 * @return void
 */
	public function testFormatDateFormField() {
		$actual = $this->GUIbuilder->formatDateFormField([12,20,2025]);
		$this->assertSame('2025-12-20', $actual);
	}

/**
 * testGetCommissionMonthsOptions
 * 
 * @covers: GUIbuilderComponent::getCommissionMonthsOptions()
 * @return void
 */
	public function testGetCommissionMonthsOptions() {
		$actual = $this->GUIbuilder->getCommissionMonthsOptions([12,20,2025]);

		$m = 1;
		$y = 2004;
		$monthsTillNow = ((date('Y') + 2) - $y) * 12;
		$monthsArray = $this->GUIbuilder->getDatePartOptns('M');
		$result = [];
		for ($x = 1; $x <= $monthsTillNow; $x++) {
			if ($m > 12) {
				$m = 1;
				$y += 1;
			}
			
			$this->assertSame($monthsArray[$m] . ' ' . $y, $actual[$m . ":" . $y]);
			$m += 1;
		}
	}

/**
 * testSetRequesDataNoteData
 * 
 * @covers: GUIbuilderComponent::getCommissionMonthsOptions()
 * @return void
 */
	public function testSetRequesDataNoteData() {
		$merchantId = CakeText::uuid();
		$actual = $this->GUIbuilder->setRequesDataNoteData($merchantId, 'Change Request', 'Test Note Title');
		$expected = [
			[
				'note_type_id' => NoteType::CHANGE_REQUEST_ID,
				'user_id' => null,
				'merchant_id' => $merchantId,
				'note_date' => date("Y-m-d"),
				'note_title' => 'Test Note Title',
				'general_status' => 'COMP'
			]
		];

		$this->assertSame($expected, $actual);
	}

/**
 * testCheckNoteBtnClicked
 * 
 * @covers: GUIbuilderComponent::checkNoteBtnClicked()
 * @return void
 */
	public function testCheckNoteBtnClicked() {
		$merchantId = CakeText::uuid();
		$actual = $this->GUIbuilder->checkNoteBtnClicked(['nothing']);
		$expected = MerchantChange::EDIT_PENDING;
		$this->assertSame($expected, $actual);

		$actual = $this->GUIbuilder->checkNoteBtnClicked([MerchantChange::EDIT_APPROVED => MerchantChange::EDIT_APPROVED]);
		$expected = MerchantChange::EDIT_APPROVED;
		$this->assertSame($expected, $actual);

		$actual = $this->GUIbuilder->checkNoteBtnClicked([MerchantChange::EDIT_LOG => MerchantChange::EDIT_LOG]);
		$expected = MerchantChange::EDIT_LOG;
		$this->assertSame($expected, $actual);
	}

/**
 * testSetCsvView
 * 
 * @covers: GUIbuilderComponent::checkNoteBtnClicked()
 * @return void
 */
	public function testSetCsvView() {
		
		$controller = $this->getMockBuilder('UsersController')
			->disableOriginalConstructor()
			->setMethods(array(
				'redirect',
				'set',
			))
			->getMock();
		$controller->name = 'UsersController';
		//set viewClass implicit property to something arbitrarty
		$controller->viewClass = 'someView';

		$this->GUIbuilder->controller = $controller;

		$this->GUIbuilder->setCsvView();
		//Check that the view was changed to csv for exporting data
		$this->assertSame('CsvView.Csv',$controller->viewClass);
	}

/**
 * testSetDecimalsNoRoundingException
 *
 * @return void
 */
	public function testSetDecimalsNoRoundingException() {
		try {
			$this->GUIbuilder->setDecimalsNoRounding(1.99999, -1);
		} catch (Exception $e) {
			$this->assertSame('Invalid argument supplied for precision!', $e->getMessage());
		}
	}
/**
 * testSetDecimalsNoRounding
 *
 * @covers GUIbuilderComponent::setDecimalsNoRounding()
 * @return void
 */
	public function testSetDecimalsNoRounding() {
		$this->assertSame(1.0, $this->GUIbuilder->setDecimalsNoRounding(1.99999, 0));
		$this->assertSame(1.99, $this->GUIbuilder->setDecimalsNoRounding(1.99999));
		$this->assertSame(1.955, $this->GUIbuilder->setDecimalsNoRounding(1.95599, 3));
		$this->assertSame(1.9876, $this->GUIbuilder->setDecimalsNoRounding(1.987699, 4));
	}

/**
 * testFormatSearchParams
 *
 * @covers GUIbuilderComponent::formatSearchParams()
 * @return void
 */
	public function testFormatSearchParams() {
		$param = ['user_id' => User::PREFIX_USER . ':00ccf87a-4564-4b95-96e5-e90df32c46c1'];
		$actual = $this->GUIbuilder->formatSearchParams($param);
		$this->assertSame(['user_id' => 'Mark Weatherford'], $actual);

		$param = ['merchant_dba' => 'The Fake Merchant'];
		$actual = $this->GUIbuilder->formatSearchParams($param);
		$this->assertSame($param, $actual);

		$param = ['from_date' => ['year' => '2030', 'month' => '12']];
		$actual = $this->GUIbuilder->formatSearchParams($param);
		$this->assertSame(['from_date' => '2030-12-01' ], $actual);

		$param = ['end_date' => ['year' => '2030', 'month' => '12']];
		$actual = $this->GUIbuilder->formatSearchParams($param);
		$this->assertSame(['end_date' => '2030-12-31' ], $actual);

		$param = ['res_months' => 2];
		$actual = $this->GUIbuilder->formatSearchParams($param);
		$this->assertSame(['res_months' => '2 month(s)'], $actual);
	}

/**
 * testGetUSAStates
 *
 * @covers GUIbuilderComponent::getUSAStates()
 * @return void
 */
	public function testGetUSAStates() {
		$actual = $this->GUIbuilder->getUSAStates();
		$this->assertNotEmpty($actual);
		$this->assertCount(51, $actual);
	}

/**
 * testBuildRepFilterOptns
 *
 * @covers GUIbuilderComponent::buildRepFilterOptns()
 * @return void
 */
	public function testBuildRepFilterOptns() {
		$actual = $this->GUIbuilder->buildRepFilterOptns();
		$this->assertFalse($actual);
	}
}
