<?php
App::uses('ResidualParameterType', 'Model');
App::uses('ResidualVolumeTier', 'Model');
App::uses('AxiaTestCase', 'Test');

class ResidualParameterTypeTestCase extends AxiaTestCase {

	const COMPENSATION_ID = '5570e7fe-5a64-4a93-8724-337a34627ad4';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ResidualParameterType = $this->getMockForModel('ResidualParameterType', [
			'_getCurrentUser',
			'RbacIsPermitted',
		]);
		$this->ResidualParameterType->expects($this->any())
			->method('_getCurrentUser')
			->will($this->returnValue(AxiaTestCase::USER_REP_ID));
		$this->ResidualParameterType->expects($this->any())
			->method('RbacIsPermitted')
			->will($this->returnValue(true));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ResidualParameterType);

		parent::tearDown();
	}

/**
 * testGetHeadersNotValid method
 *
 * @return void
 */
	public function testGetHeadersNotValid() {
		$expected = 'var-not-modified';
		try {
			$invalidUserId = '00000000-9999-0000-0000-000000000001';
			$this->ResidualParameterType->getHeaders($invalidUserId, null);
		} catch (Exception $e) {
			$this->assertEquals('NotFoundException', get_class($e));
			$this->assertEquals('User not found for getHeaders method', $e->getMessage());
		}
		$this->assertEquals('var-not-modified', $expected);

		try {
			$this->ResidualParameterType->getHeaders(AxiaTestCase::USER_REP_ID, null);
		} catch (Exception $e) {
			$this->assertEquals('NotFoundException', get_class($e));
			$this->assertEquals('Compensation profile not found for getHeaders method', $e->getMessage());
		}
		$this->assertEquals('var-not-modified', $expected);

		try {
			$this->ResidualParameterType->getHeaders(AxiaTestCase::USER_REP_ID, self::COMPENSATION_ID, 'invalid-model');
		} catch (Exception $e) {
			$this->assertEquals('InvalidArgumentException', get_class($e));
			$this->assertEquals('Invalid model for getHeaders method', $e->getMessage());
		}
		$this->assertEquals('var-not-modified', $expected);
	}

/**
 * testGetHeadersNoAssociatedUsers method
 *
 * @return void
 */
	public function testGetHeadersNoAssociatedUsers() {
		$headers = $this->ResidualParameterType->getHeaders('003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6', '5570e7dc-a4fc-444c-8929-337a34627ad4');
		$this->assertCount(10, $headers);
		// Repeated for each Tier
		$expectedHeaderTypes = [
			'Rep %',
			'Rep Multiple',
			'Rep %',
			'Rep Multiple',
			'Rep %',
			'Rep Multiple',
			'Rep %',
			'Rep Multiple',
			'Rep %',
			'Rep Multiple',
		];
		$this->assertEquals($expectedHeaderTypes, Hash::extract($headers, '{n}.ResidualParameterType.name'));
		$tiers = Hash::extract($headers, '{n}.Tier');
		$this->assertEquals([1, 2, 3, 4, 0], array_values(array_unique($tiers)));

		$firstParameterType = reset($headers);
		$this->assertEquals(1, Hash::get($firstParameterType, 'Tier'));
		$this->assertEmpty(Hash::get($firstParameterType, 'ResidualParameter'));
	}

/**
 * testGetHeadersAssociatedUsers method
 *
 * @return void
 */
	public function testGetHeadersAssociatedUsers() {
		$headers = $this->ResidualParameterType->getHeaders(AxiaTestCase::USER_REP_ID, self::COMPENSATION_ID);
		$this->assertCount(30, $headers);
		$expectedHeaderTypes = [
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple'
		];

		$this->assertEquals($expectedHeaderTypes, Hash::extract($headers, '{n}.ResidualParameterType.name'));
		$tiers = Hash::extract($headers, '{n}.Tier');
		$this->assertEquals([1, 2, 3, 4, 0], array_values(array_unique($tiers)));

		$firstParameterType = reset($headers);
		$this->assertEquals(1, Hash::get($firstParameterType, 'Tier'));
		$this->assertCount(1, Hash::get($firstParameterType, 'ResidualParameter'));
		$this->assertEquals(self::COMPENSATION_ID, Hash::get($firstParameterType, 'ResidualParameter.0.user_compensation_profile_id'));
	}

/**
 * testGetHeadersResidualTimeParameter method
 *
 * @return void
 */
	public function testGetHeadersResidualTimeParameter() {
		$headers = $this->ResidualParameterType->getHeaders(AxiaTestCase::USER_REP_ID, self::COMPENSATION_ID, 'ResidualTimeParameter');
		$this->assertCount(24, $headers);

		$expectedHeaderTypes = [
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple',
			'Rep %',
			'Rep Multiple',
			'Mgr %',
			'Mgr %',
			'Mgr Multiple',
			'Mgr Multiple'
		];

		$this->assertEquals($expectedHeaderTypes, Hash::extract($headers, '{n}.ResidualParameterType.name'));
		$tiers = Hash::extract($headers, '{n}.Tier');
		$this->assertEquals([1, 2, 3, 4], array_values(array_unique($tiers)));
	}
}
