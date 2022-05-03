<?php
App::uses('ResidualProductControl', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Residual Product Control Case
 *
 * @coversDefaultClass ResidualProductControl
 */
class ResidualProductControlTest extends AxiaTestCase {

/**
 * Class under test.
 *
 * @var ResidualProductControl
 */
	public $ResidualProductControl;

/**
 * Runs before each test.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->ResidualProductControl = ClassRegistry::init('ResidualProductControl');
	}

/**
 * Test `validate` property.
 *
 * @param string $field The field to check valdation for.
 * @param string $value The value to try saving.
 * @param array $validationError A list of validation errors.
 * @return void
 * @covers ::validate property
 * @dataProvider provideTestValidate
 */
	public function testValidate($field, $value, array $validationError = null) {
		$this->ResidualProductControl->id = '00000000-0000-0000-0000-000000000001';

		$returnValue = $this->ResidualProductControl->saveField($field, $value, true);

		$validationErrors = $this->ResidualProductControl->validationErrors;

		if ($validationError === null) {
			self::assertArrayNotHasKey($field, $validationErrors);
			self::assertTrue($returnValue);
		} else {
			self::assertArrayHasKey($field, $validationErrors);
			self::assertEquals($validationError, $validationErrors[$field]);
			self::assertFalse($returnValue);
		}
	}

/**
 * Data provider for `testValidate` test.
 *
 * @return array[]
 */
	public function provideTestValidate() {
		$percentageErrorMessage = 'Must be a valid percentage value with a maximum precision of 3';

		return [
			/* valid values */
			['user_id', '7d5e1c78-3b74-47c3-afa8-06d241025d62'],
			['product_service_type_id', '7d5e1c78-3b74-47c3-afa8-06d241025d62'],
			['do_no_display', true],
			['tier_product', true],
			['enabled_for_rep', true],
			['rep_params_gross_profit_tsys', 99.99],
			['rep_params_gross_profit_sage', 99.99],
			['rep_params_gross_profit_dc', 99.99],
			['manager1_params_gross_profit_tsys', 99.99],
			['manager1_params_gross_profit_sage', 99.99],
			['manager1_params_gross_profit_dc', 99.99],
			['manager2_params_gross_profit_tsys', 99.99],
			['manager2_params_gross_profit_sage', 99.99],
			['manager2_params_gross_profit_dc', 99.99],
			['override_rep_percentage', 99.99],
			['override_multiple', 99.99],
			['override_manager1', 99.99],
			['override_manager2', 99.99],
			/* invalid values */
			['user_id', null, ['notBlank']],
			['product_service_type_id', null, ['notBlank']],
			['do_no_display', 'invalid_value', ['boolean']],
			['tier_product', 'invalid_value', ['boolean']],
			['enabled_for_rep', 'invalid_value', ['boolean']],
			['rep_params_gross_profit_tsys', 'invalid_value', [$percentageErrorMessage]],
			['rep_params_gross_profit_sage', 'invalid_value', [$percentageErrorMessage]],
			['rep_params_gross_profit_dc', 'invalid_value', [$percentageErrorMessage]],
			['manager1_params_gross_profit_tsys', 'invalid_value', [$percentageErrorMessage]],
			['manager1_params_gross_profit_sage', 'invalid_value', [$percentageErrorMessage]],
			['manager1_params_gross_profit_dc', 'invalid_value', [$percentageErrorMessage]],
			['manager2_params_gross_profit_tsys', 'invalid_value', [$percentageErrorMessage]],
			['manager2_params_gross_profit_sage', 'invalid_value', [$percentageErrorMessage]],
			['manager2_params_gross_profit_dc', 'invalid_value', [$percentageErrorMessage]],
			['override_rep_percentage', 'invalid_value', [$percentageErrorMessage]],
			['override_multiple', 'invalid_value', [$percentageErrorMessage]],
			['override_manager1', 'invalid_value', [$percentageErrorMessage]],
			['override_manager2', 'invalid_value', [$percentageErrorMessage]],
		];
	}

/**
 * Test `beforeValidate` method.
 *
 * @covers ::beforeValidate
 * @return void
 */
	public function testBeforeValidate() {
		$mockModel = $this->_mockModel('ResidualProductControl', array('stripPercentageFromFields'));
		$mockModel
			->expects(self::once())
			->method('stripPercentageFromFields');

		$returnValue = $mockModel->beforeValidate();

		self::assertTrue($returnValue);
	}

/**
 * Test `stripPercentageFromFields` method.
 *
 * @covers ::stripPercentageFromFields
 * @return void
 */
	public function testStripPercentageFromFields() {
		foreach ($this->ResidualProductControl->stripPercentageFromFields as $field) {
			$this->ResidualProductControl->data['ResidualProductControl'][$field] = '99.99%';
		}

		$this->ResidualProductControl->stripPercentageFromFields();

		foreach ($this->ResidualProductControl->stripPercentageFromFields as $field) {
			self::assertEquals(
				$this->ResidualProductControl->data['ResidualProductControl'][$field],
				(float)99.99
			);
		}
	}

/**
 * Test `synchronizeProductServiceTypes` method.
 *
 * @covers ::synchronizeProductServiceTypes
 * @return void
 */
	public function testSynchronizeProductServiceTypes() {
		$userId = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
		$commonFindOptions = [
			'contain' => [],
			'fields' => ['ResidualProductControl.product_service_type_id'],
			'conditions' => ['ResidualProductControl.user_id' => $userId],
		];

		$types = $this->ResidualProductControl->ProductServiceType->find('all', [
			'fields' => ['ProductServiceType.id'],
			'order' => false,
		]);
		$userRecords = $this->ResidualProductControl->find('all', $commonFindOptions);

		self::assertLessThan(
			count($types),
			count($userRecords),
			'This test should be performed on a record that has missing product service types.'
		);

		$this->ResidualProductControl->synchronizeProductServiceTypes($userId);

		$userRecords = $this->ResidualProductControl->find('all', $commonFindOptions);

		self::assertGreaterThanOrEqual(
			count($types),
			count($userRecords),
			'Not enough product service types after synchronizing.'
		);

		$typeIds = Hash::extract($types, '{n}.ProductServiceType.id');
		$userTypeIds = Hash::extract($userRecords, '{n}.ResidualProductControl.product_service_type_id');

		$missingTypeIds = array_diff($typeIds, $userTypeIds);

		self::assertEmpty($missingTypeIds, 'All product service types were not synced.');

		$newTypeId = current(array_intersect($typeIds, $userTypeIds));
		$userType = $this->ResidualProductControl->find('first', array_merge($commonFindOptions, [
			'fields' => null,
			'conditions' => ['ResidualProductControl.product_service_type_id' => $newTypeId],
		]));

		$expectedValues = [
			'user_id' => $userId,
			'rep_params_gross_profit_tsys' => '80',
			'rep_params_gross_profit_sage' => '80',
			'rep_params_gross_profit_dc' => '65',
			'manager1_params_gross_profit_tsys' => '80',
			'manager1_params_gross_profit_sage' => '80',
			'manager1_params_gross_profit_dc' => '65',
			'manager2_params_gross_profit_tsys' => '80',
			'manager2_params_gross_profit_sage' => '80',
			'manager2_params_gross_profit_dc' => '65',
			'override_rep_percentage' => '25',
			'override_multiple' => '3',
			'override_manager1' => '25',
			'override_manager2' => '10',
		];

		$actualValues = array_intersect_key(
			$userType['ResidualProductControl'],
			$expectedValues
		);
		self::assertEquals($expectedValues, $actualValues, 'A synchronized record did not have the expected values');

		self::assertArrayContainsArray(
			$expectedValues,
			$userType['ResidualProductControl'],
			'A synchronized record did not have the expected values'
		);
	}

/**
 * Test `addDataByUserId` method.
 *
 * @covers ::allDataByUserId
 * @return void
 */
	public function testAllDataByUserId() {
		$userId = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
		$records = $this->ResidualProductControl->allDataByUserId($userId);

		self::assertCount(92, $records, 'The test user did not have the expect amount of products.');
		self::assertArrayHasPaths([
			'ResidualProductControl.id',
			'ResidualProductControl.user_id',
			'ResidualProductControl.product_service_type_id',
			'ResidualProductControl.do_no_display',
			'ResidualProductControl.tier_product',
			'ResidualProductControl.enabled_for_rep',
			'ResidualProductControl.rep_params_gross_profit_tsys',
			'ResidualProductControl.rep_params_gross_profit_sage',
			'ResidualProductControl.rep_params_gross_profit_dc',
			'ResidualProductControl.manager1_params_gross_profit_tsys',
			'ResidualProductControl.manager1_params_gross_profit_sage',
			'ResidualProductControl.manager1_params_gross_profit_dc',
			'ResidualProductControl.manager2_params_gross_profit_tsys',
			'ResidualProductControl.manager2_params_gross_profit_sage',
			'ResidualProductControl.manager2_params_gross_profit_dc',
			'ResidualProductControl.override_rep_percentage',
			'ResidualProductControl.override_multiple',
			'ResidualProductControl.override_manager1',
			'ResidualProductControl.override_manager2',
			'ProductServiceType.id',
			'ProductServiceType.products_services_type_id_old',
			'ProductServiceType.products_services_description',
			'ProductServiceType.products_services_rppp',
			'ProductServiceType.is_active',
		], $records[0], 'The data returned is missing expected keys.');
	}

/**
 * Test `cloneDataToUser` method.
 *
 * @covers ::cloneDataToUser
 * @return void
 */
	public function testCloneDataToUser() {
		$userFrom = 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda';
		$userTo = '00000000-9999-0000-0000-000000000001';

		$expected = $this->ResidualProductControl->find('all', [
			'conditions' => ['ResidualProductControl.user_id' => $userFrom],
		]);

		self::assertCount(2, $expected);

		$this->ResidualProductControl->cloneDataToUser($userFrom, $userTo);

		$actual = $this->ResidualProductControl->find('all', [
			'conditions' => ['ResidualProductControl.user_id' => $userTo],
		]);

		foreach (range(0, 1) as $index) {
			self::assertNotEquals(
				$expected[$index]['ResidualProductControl']['id'],
				$actual[$index]['ResidualProductControl']['id']
			);
			self::assertEquals(
				$userTo,
				$actual[$index]['ResidualProductControl']['user_id']
			);
			unset(
				$actual[$index]['ResidualProductControl']['id'],
				$actual[$index]['ResidualProductControl']['user_id'],
				$expected[$index]['ResidualProductControl']['id'],
				$expected[$index]['ResidualProductControl']['user_id']
			);
		}

		self::assertEquals($expected, $actual);
	}

/**
 * Test `cloneDataToUser` method when user does not exist.
 *
 * @covers ::cloneDataToUser
 * @expectedException RuntimeException
 * @expectedExceptionMessage The user does not have any settings yet or does not exist!
 * @return void
 */
	public function testCloneDataToUserWhenUserDoesNotExist() {
		$this->ResidualProductControl->cloneDataToUser('00000000-9999-0000-0000-000000000001', '00000000-8888-0000-0000-000000000001');
	}

/**
 * Test `cloneDataToUser` method when target user has settings.
 *
 * @covers ::cloneDataToUser
 * @expectedException RuntimeException
 * @expectedExceptionMessage The user you are trying to clone to already has settings!
 * @return void
 */
	public function testCloneDataToUserWhenTargetUserHasSettings() {
		$this->ResidualProductControl->cloneDataToUser('00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000001');
	}
}
