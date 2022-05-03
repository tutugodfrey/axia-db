<?php
App::uses('AppController', 'Controller');
/**
 * BackgroundJobs Controller
 *
 * @property BackgroundJob $BackgroundJob
 * @property PaginatorComponent $Paginator
 */
class BackgroundJobsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * updateList method
 * Handles Ajax Requests only
 *
 * @param string $merchantMID a merchant mid
 * @return void
 */
	public function updateList() {
		/* This function handles only ajax rerquests */
		$this->autoRender = false;

		if ($this->request->is('ajax')) {
			/* Check if session is still active by checking currently logged in user id */
			if ($this->Session->check('Auth.User.id')) {
				$this->set($this->BackgroundJob->getViewData());
				$this->render('/Elements/AjaxElements/Admin/bg_processes_list', 'ajax');
			} else {
				$this->response->statusCode(403);
			}
		}
	}
}
