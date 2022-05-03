<?php

App::uses('AppController', 'Controller');

/**
 * Adjustments Controller
 *
 * @property Adjustment $Adjustment
 */
class AdjustmentsController extends AppController {

	public $components = ['GUIbuilder'];

/**
 * index method
 *
 * @param string $id user_id
 *
 * @return bool
 */
	public function index($id) {
		if (empty($id)) {
			$this->Session->setFlash(__('user id is not valid'));
			return false;
		}

		$beginYear = $this->params->query('begin_year');
		$beginMonth = $this->params->query('begin_month');
		$endYear = $this->params->query('end_year');
		$endMonth = $this->params->query('end_month');

		$from = null;
		$to = null;

		if (!is_null($beginYear) && is_null($beginMonth)) {
			$from = date("Y-m-d", mktime(0, 0, 0, $beginMonth, 1, $beginYear));
			$this->set('beginYearSelected', $beginYear);
			$this->set('beginMonthSelected', $beginMonth);
		}

		if ($endYear != '' && $endMonth != '') {
			$to = date("Y-m-d", mktime(0, 0, 0, $endMonth + 1, 1, $endYear));
			$this->set('endYearSelected', $endYear);
			$this->set('endMonthSelected', $endMonth);
		}

		$conditions = array();
		$conditions['Adjustment.user_id'] = $id;

		if (!empty($from)) {
			$conditions['Adjustment.adj_date >='] = $from;
		}

		if (!empty($to)) {
			$conditions['Adjustment.adj_date <'] = $to;
		}

		// grab results from the custom finder _findIndex and pass them to the paginator
		$this->Paginator->settings = array('findType' => 'index');
		$this->Paginator->settings['conditions'] = $conditions;

		$this->set('adjustmentList', $this->Paginator->paginate());

		$this->set('userId', $id);
		$this->set('years', $this->GUIbuilder->getDatePartOptns('y'));
		$this->set('months', $this->GUIbuilder->getDatePartOptns('m'));
		$this->set('days', $this->GUIbuilder->getDatePartOptns('d'));

		return true;
	}

/**
 * add method
 *
 * @param string $id user_id
 *
 * @return bool
 */

	public function add($id) {
		if (empty($id)) {
			$this->Session->setFlash(__('user id is not valid'));
			return false;
		}

		if ($this->request->is('post')) {
			$adjMonth = $this->request->data('Adjustment.adjustment_month');
			$adjDay = $this->request->data('Adjustment.adjustment_day');
			$adjYear = $this->request->data('Adjustment.adjustment_year');

			$adjDate = $adjYear . "-" . $adjMonth . "-" . $adjDay;

			$adjustment = array();
			$adjustment['Adjustment']['adj_date'] = $adjDate;
			$adjustment['Adjustment']['adj_amount'] = $this->request->data('Adjustment.adjustment_amount');
			$adjustment['Adjustment']['adj_description'] = $this->request->data('Adjustment.adjustment_description');
			$adjustment['Adjustment']['user_id'] = $id;

			$this->Adjustment->create($adjustment);

			if (!$this->Adjustment->save()) {
				$this->Session->setFlash(__('could not save adjustment'));
			}

			$this->redirect(
				array(
					'controller' => 'Adjustments',
					'action' => 'index/' . $id
				)
			);
		}

		return true;
	}

/**
 * edit method
 *
 * @param string $id adjustment_id
 *
 * @return bool
 */
	public function edit($id) {
		$this->Adjustment->id = $id;
		if (!$this->Adjustment->exists()) {
			$this->Session->setFlash(__('Adjustment does not exist'));
			$this->redirect(['controller' => 'Users', 'action' => 'index']);
		}

		$adjustment = $this->Adjustment->find('first',
			array(
				'conditions' => array(
					'Adjustment.id' => $id
				)
			)
		);

		if ($this->request->is('post')) {
			$adjMonth = $this->request->data('Adjustment.adjustment_month');
			$adjDay = $this->request->data('Adjustment.adjustment_day');
			$adjYear = $this->request->data('Adjustment.adjustment_year');

			$adjDate = $adjYear . "-" . $adjMonth . "-" . $adjDay;

			$adjustment['Adjustment']['adj_date'] = $adjDate;
			$adjustment['Adjustment']['adj_amount'] = $this->request->data('Adjustment.adjustment_amount');
			$adjustment['Adjustment']['adj_description'] = $this->request->data('Adjustment.adjustment_description');

			if (!$this->Adjustment->save($adjustment)) {
				$this->Session->setFlash(__('could not save adjustment'));
			} else {
				$this->redirect(
					array(
						'controller' => 'Adjustments',
						'action' => 'index/' . $adjustment['Adjustment']['user_id']
					)
				);
			}
		}

		$this->set('adjustmentId', $id);
		$this->set('adjustmentAmount', $adjustment['Adjustment']['adj_amount']);
		$this->set('adjustmentDescription', $adjustment['Adjustment']['adj_description']);

		$adjDate = $adjustment['Adjustment']['adj_date'];

		$day = null;
		$month = null;
		$year = null;

		if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $adjDate, $matches)) {
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
		}

		$this->set('adjustmentMonth', $month);
		$this->set('adjustmentDay', $day);
		$this->set('adjustmentYear', $year);

		$this->set('years', $this->GUIbuilder->getDatePartOptns('y'));
		$this->set('months', $this->GUIbuilder->getDatePartOptns('m'));
		$this->set('days', $this->GUIbuilder->getDatePartOptns('d'));

		return true;
	}

/**
 * delete method
 *
 * @param string $id Adjustment.id
 * @param string $userId Adjustment.user_id
 * @return void
 */
	public function delete($id, $userId) {
		$this->Adjustment->id = $id;
		$url = ['action' => 'index', $userId];
		if (!$this->Adjustment->exists()) {
			$this->_failure(__('Adjustment does not exist'), $url);
		}
		if (!$this->Adjustment->delete()) {
			$this->_failure(__('Could not delete adjustment record'), $url);
		} else {
			$this->_success(__('Adjustment deleted!'), $url);
		}
	}
}