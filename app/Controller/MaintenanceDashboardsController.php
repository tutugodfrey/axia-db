<?php

App::uses('AppController', 'Controller');

class MaintenanceDashboardsController extends AppController {

	public $layout = 'adminLayout';

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'MaintenanceDashboards';

/**
 * edit method
 *
 * @param string $modelName from which to retrieve content data
 * @return void
 */
	public function main_menu() {}

/**
 * edit method
 *
 * @param string $modelName from which to retrieve content data
 * @return void
 */
	public function content($modelName = '') {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Check if session is still active by checking currently logged in user id */
			if (!$this->Session->check('Auth.User.id')) {
				$this->response->statusCode(403);
			}
			$this->set($this->MaintenanceDashboard->getContentData($modelName));
			$this->render('/Elements/AjaxElements/Admin/dbTableContent', 'ajax');
		} else {
			$modelNames = $this->MaintenanceDashboard->getModelOptions();
			$this->set(compact('modelNames'));
		}
	}

/**
 * add method
 *
 * @param string $recordId an id that correspond to the model in de the second param
 * @param string $modelName the name of the model to where the record id exists
 * @return void
 */
	public function edit($recordId = '', $modelName = '') {
		if (empty($modelName)) {
			$modelName = $this->request->data('MaintenanceDashboard.modelName');
		}
		$Model = ClassRegistry::init($modelName);

		if (!empty($recordId)) {
			$Model->id = $recordId;
			if (!$Model->exists()) {
				$this->_failure(__("Error: Record does not exist in $modelName"), ['controller' => 'MaintenanceDashboards', 'action' => 'content']);
			}
		}

		if (($this->request->is('put') || $this->request->is('post')) && !empty($this->request->data($modelName))) {
			$this->MaintenanceDashboard->sainitizeData($this->request->data);
			//Remove any possible trailing spaces in the data
			$this->request->data[$modelName] = array_map('trim', $this->request->data[$modelName]);
			$validated = $Model->saveAll($this->request->data, array('validate' => 'only'));
			if ($validated) {
				$Model->create();
				$successMsg = __('%s successfully saved', $modelName);
				//Some models have their own custom sorting position field. Those models behave as lists and
				//their save methods are handled differently by the behavior
				if ($Model->Behaviors->loaded('Utils.List')) {
					try {
						if ($this->MaintenanceDashboard->saveCustomSortModelData($Model, $this->request->data)) {
							$this->_success($successMsg, ['controller' => 'MaintenanceDashboards', 'action' => 'content']);
						} else {
							$this->_failure($Model->validationErrors);
						}
					} catch (Exception $e) {
						$this->_failure($e->getMessage());
					}
				} elseif ($Model->save($this->request->data($modelName))) {
					$this->_success($successMsg, ['controller' => 'MaintenanceDashboards', 'action' => 'content']);
				} 
			}
			$errors = Hash::extract($Model->validationErrors, '{s}.{n}');
			$this->Session->setFlash($errors, 'Flash/listErrors', array('class' => 'alert alert-danger'));
		}

		if (!empty($recordId)) {
			//get view data
			$this->request->data = $this->MaintenanceDashboard->getModelData($modelName, $recordId);
		}

		$this->request->data['MaintenanceDashboard']['modelName'] = $modelName;
		$this->set($this->MaintenanceDashboard->getEditViewData($modelName));
	}

/**
 * delete method
 *
 * @param string $recordId and id that belongs to modelName
 * @param string $modelName name of a model that contains recordId
 * @return void
 * @throws MethodNotAllowedException
 */
	public function delete($recordId, $modelName) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$affected = $this->MaintenanceDashboard->countAffectedRecords($recordId, $modelName);
		if ($affected > 0) {
			$this->Session->setFlash(__("This record may not be deleted as $affected associated records across multiple tables will become orphaned!"), 'Flash/alert', ['class' => 'alert alert-danger']);
			$this->redirect(['controller' => 'MaintenanceDashboards', 'action' => 'content']);
		}

		if ($this->MaintenanceDashboard->deleteAndLog($recordId, $modelName)) {
			$this->Session->setFlash(__('Record Deleted from table ' . Inflector::tableize($modelName)), 'Flash/adminUndoDelete',
				['class' => 'alert alert-danger', 'controller' => $this->name, 'action' => 'undoDelete', 'id' => $recordId, "model" => $modelName]);
			$this->redirect(['controller' => 'MaintenanceDashboards', 'action' => 'content']);
		}
		$this->_failure(__("Error: Record could not be deleted"), ['controller' => 'MaintenanceDashboards', 'action' => 'content']);
	}

/**
 * undoDelete method
 *
 * @param string $recordId and id that belongs to modelName
 * @param string $modelName name of a model that contains recordId
 * @return void
 * @throws MethodNotAllowedException
 */
	public function undoDelete($recordId, $modelName) {
		try {
			$this->MaintenanceDashboard->undoDelete($recordId, $modelName);
		} catch (OutOfBoundsException $e) {
			$this->_failure(__($e->getMessage()), ['controller' => 'MaintenanceDashboards', 'action' => 'content']);
		} catch (Exception $e) {
			$this->Session->setFlash(__($e->getMessage()), 'Flash/adminUndoDelete',
				['class' => 'alert alert-danger', 'controller' => $this->name, 'action' => 'undoDelete', 'id' => $recordId, "model" => $modelName]);
			$this->redirect(['controller' => 'MaintenanceDashboards', 'action' => 'content']);
		}
		$this->_success(__("$modelName record has been restored."), ['controller' => 'MaintenanceDashboards', 'action' => 'content']);
	}

/**
 * generateApiDoc method
 *
 * @return void
 */
	public function generateApiDoc() {
		try {
			$this->MaintenanceDashboard->refreshAxiaApiJsonForSwagger();
			$this->_success('API documentation web page has been successfully regenerated, to review go to: ' . Router::url('/AxiaApiDocs/', true), $this->referer(), ['class' => 'alert alert-success strong']);
		} catch(Exception $e){
			$err = $e->getMessage();
			$this->_failure($err, $this->referer(), ['class' => 'alert alert-danger strong']);
		}
	}
}
