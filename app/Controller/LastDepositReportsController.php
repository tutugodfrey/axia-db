<?php

App::uses('AppController', 'Controller');
App::uses('UploadValidationException', 'Lib/Error');

/**
 * LastDepositReports Controller
 *
 * @property LastDepositReport $LastDepositReport
 */
class LastDepositReportsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'GUIbuilder',
		'RequestHandler' => [
			'viewClassMap' => ['csv' => 'CsvView.Csv']
		],
		'Search.Prg' => array(
			'commonProcess' => array('paramType' => 'querystring'),
			'presetForm' => array('paramType' => 'querystring')
		)
	);

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$filterParams = $this->_getFilterParams();
		$this->paginate = array(
			'findType' => 'lastDepositReports',
			'conditions' => $this->LastDepositReport->parseCriteria($filterParams),
		);
		//Persist selected organizational filers
		$this->request->data['Merchant']['organization_id'] = $this->request->data('LastDepositReport.organization_id');
		$this->request->data['Merchant']['region_id'] = $this->request->data('LastDepositReport.region_id');
		$this->request->data['Merchant']['subregion_id'] = $this->request->data('LastDepositReport.subregion_id');
		$users = $this->LastDepositReport->User->getEntityManagerUsersList(true);
		$lastDepositReports = $this->paginate();
		$this->set('organizations', $this->LastDepositReport->Merchant->Organization->find('list'));
		$this->set('regions', $this->LastDepositReport->Merchant->Region->find('list'));
		$this->set('subregions', $this->LastDepositReport->Merchant->Subregion->find('list'));
		$this->set(compact('lastDepositReports', 'users'));
	}

/**
 * Export the report to csv
 *
 * @return void
 */
	public function exportToCsv() {
		$filterParams = $this->_getFilterParams();
		$maxCount = $this->LastDepositReport->find('count');
		$lastDepositReports = $this->LastDepositReport->find('lastDepositReports', array(
				'conditions' => $this->LastDepositReport->parseCriteria($filterParams),
				'limit' => $maxCount,
				'maxLimit' => $maxCount,
			));

		$this->response->download('last-activity.csv');
		$this->GUIbuilder->setCsvView($filterParams);
		$this->set('lastDepositReports', $lastDepositReports);
	}

/**
 * Return the filter parameters from Searchable behavior
 *
 * @return array
 */
	protected function _getFilterParams() {
		$this->Prg->commonProcess();
		$filterParams = $this->Prg->parsedParams();
		if (empty($this->request->query['user_id'])) {
			$filterParams['user_id'] = $this->LastDepositReport->User->getDefaultUserSearch();
		} else {
			$complexUserIdArgs = $this->LastDepositReport->User->extractComplexId(Hash::get($filterParams, 'user_id'));
			if (Hash::get($complexUserIdArgs, 'prefix') === User::PREFIX_ENTITY) {
				$this->LastDepositReport->filterArgs['user_id']['field'] = '"Merchant"."id"';
			} else {
				$this->LastDepositReport->filterArgs['user_id'] = $this->LastDepositReport->getUserSearchFilterArgs($complexUserIdArgs);
				$this->LastDepositReport->filterArgs['user_id']['field'] = '"Merchant"."user_id"';
			}

		}
		return $filterParams;
	}
/**
 * delete method
 *
 * @param string $id id
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->LastDepositReport->id = $id;
		if (!$this->LastDepositReport->exists()) {
			throw new NotFoundException(__('Invalid last deposit report'));
		}
		if ($this->LastDepositReport->delete()) {
			$this->Session->setFlash(__('Last deposit report deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Last deposit report was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * Upload new LastDepositReports
 *
 * This is a candidate to place into a possible csvimport behavior
 *
 * @return void
 */
	public function upload() {
		if ($this->request->is('post') && !empty($this->request->data)) {
			//move file so is available for backgound processing after request is complete
			 $this->LastDepositReport->moveUploadedFile(
				Hash::get($this->request->data, 'LastDepositReport.file.tmp_name'),
				APP . 'tmp' . DS,
				basename(Hash::get($this->request->data, 'LastDepositReport.file.tmp_name'))
			); 

			// this can be a very long process, so queue it and notify the user when done
			CakeResque::enqueue(
				'genericAxiaDbQueue',
				'BackgroundJobShell',
				['lastDepositCsvDataImport', $this->request->data, $this->Auth->user('id')]
			);

			return $this->_success(__('Upload will continue on the server, you will receive an email once it is finished.'), null, ['class' => 'alert alert-info']);
		}
	}
}
