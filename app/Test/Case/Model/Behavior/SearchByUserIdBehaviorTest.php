<?php
App::uses('SearchByUserIdBehavior', 'Model/Behavior');
App::uses('AxiaTestCase', 'Test');

/**
 * SearchByUserIdBehavior Test Case
 *
 */
class SearchByUserIdBehaviorTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$SearchByUserIdBehavior = $this->getMock('SearchByUserIdBehavior', ['_getLoggedInUser']);
		ClassRegistry::addObject('SearchByUserIdBehavior', $SearchByUserIdBehavior);
		$this->Model = ClassRegistry::init('MerchantReject');
		$this->Model->Behaviors->attach('SearchByUserId');
		$SearchByUserIdBehavior
			->expects($this->any())
			->method('_getLoggedInUser')
			->will($this->returnValue(AxiaTestCase::USER_ADMIN_ID));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Model);

		parent::tearDown();
	}

/**
 * testSearchByUserIdException
 *
 * @expectedException OutOfBoundsException
 * @expectedExceptionMessage Model Model is not associated with User or Entity
 * @return void
 */
	public function testSearchByUserIdException() {
		$this->Model = new Model();
		$this->Model->Behaviors->load('SearchByUserId');
		$this->Model->searchByUserId();
	}

/**
 * testSearchByUserId
 *
 * @return void
 */
	public function testSearchByUserId() {
		$data = array('user_id' => 'something');
		$result = $this->Model->searchByUserId($data);
		$expected = 'SELECT "User"."id" AS "User__id" FROM "public"."users" AS "User"   WHERE "User"."id" = \'something\'';
		$this->assertEquals($expected, $result);

		$data = array('user_id' => 'user_id:something');
		$result = $this->Model->searchByUserId($data);
		$this->assertEquals($expected, $result);

		$data = array('user_id' => 'parent_user_id:someId');
		$expected = 'SELECT "User"."id" AS "User__id" FROM "public"."users" AS "User"   WHERE (("User"."parent_user_id" = \'someId\') OR ("User"."id" = \'someId\'))';
		$result = $this->Model->searchByUserId($data);
		$this->assertEquals($expected, $result);

		$data = array('user_id' => 'entity_id:someId');
		$expected = 'SELECT "Merchant"."id" AS "Merchant__id" FROM "public"."merchants" AS "Merchant"   WHERE "Merchant"."entity_id" = \'someId\'';
		$result = $this->Model->searchByUserId($data);
		$this->assertEquals($expected, $result);
	}
}
