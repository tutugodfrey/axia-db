<?php
App::uses('PaginatorComponent', 'Controller/Component');

class AppPaginatorComponent extends PaginatorComponent {

	protected $_controller = null;

/**
 * Constructor
 *
 * @param \Controller $controller controller
 * @return void
 */
	public function initialize(\Controller $controller) {
		parent::initialize($controller);
		$this->_controller = $controller;
	}

/**
 * Paginate, and catch not found exceptions to redirect back to initial page
 *
 * @param type $object object
 * @param type $scope scope
 * @param type $whitelist whitelist
 * @return mixed
 */
	public function paginate($object = null, $scope = array(), $whitelist = array()) {
		try {
			return parent::paginate($object, $scope, $whitelist);
		} catch (NotFoundException $e) {
			if (empty($this->_controller->request->params)) {
				$this->_controller->request->params = array();
			}
			return $this->_controller->redirect($this->_controller->request->action);
		}
	}
}
