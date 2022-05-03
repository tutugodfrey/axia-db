<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('UsersController', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('AppPaginatorComponent', 'Controller/Component');
App::uses('AxiaTestCase', 'Test');

/**
 * AppPaginatorComponent Test Case
 *
 */
class AppPaginatorComponentTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->AppPaginator = new AppPaginatorComponent($Collection);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AppPaginator);

		parent::tearDown();
	}

/**
 * testPaginateManagesNotFoundWithRedirect
 *
 * @return void
 */
	public function testPaginateManagesNotFoundWithRedirect() {
		$controller = $this->getMockBuilder('UsersController')
				->disableOriginalConstructor()
				->setMethods(array(
					'redirect',
				))
				->getMock();
		$controller->modelClass = 'User';
		$controller->User = $this->getMockForModel('User', array('_getCurrentUser'));
		$controller->request = $this->getMock('CakeRequest');
		$controller->request->action = 'currentAction';
		$controller->request->params = array(
			'named' => array(
				'page' => 2,
			),
		);
		$controller->expects($this->once())
				->method('redirect')
				->with('currentAction', null, true);
		$this->AppPaginator->initialize($controller);
		$this->AppPaginator->Controller = $controller;
		$this->AppPaginator->paginate();
	}
}
