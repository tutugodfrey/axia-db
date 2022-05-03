<?php

App::uses('AppController', 'Controller');
App::uses('RolePermissions', 'Lib');

/**
 * Dashboards Controller
 *
 */
class DashboardsController extends AppController {

/**
 * Main dashboard home for all users
 *
 * @todo: refactor this method and move logic into models, it was moved here to cleanup pages controller for rbac perms
 * @return void
 */
	public function home() {
		//Load necessary models
		$this->loadModel('Merchant');
		$this->loadModel('SystemTransaction');
		$this->loadModel('User');
		$this->loadModel('MerchantNote');
		$this->loadModel('Order');
		$this->loadModel('MerchantChange');
		$this->loadModel('TransactionType');

		//Define Local vars
		$date = new DateTime(date("Y-m-d"));
		$limit = 10; /* Max number of results per query */

		/* Date modifier calculates a date to be used as the start of a date range
		 * which is then used to determine how many days in the past worth of recent data to retrieve.
		 * today's date() - n days = a date w/format yyyy/mm/dd, n days in the past */
		date_modify($date, '-290 day');

		/* Get conditions to limit results based on currenly logged in user */
		$RolePermissions = new RolePermissions();
		$userConditions = $RolePermissions->getRepFilterConditions();
		$joins = array(
			array('table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => array(
					'Merchant.user_id = User.id'
				)
			),
			array('table' => 'associated_users',
				'alias' => 'AssociatedUser',
				'type' => 'left',
				'conditions' => array(
					'User.id = AssociatedUser.user_id'
				)
			)
		);
		/*		 * **  Get recent activity data          **** */
		$recentlyAdded = $this->Merchant->find('all', array(
			'conditions' => array_merge(
				array(
					'Merchant.active =' => 1,
					'Merchant.active_date >=' => date_format($date, 'Y-m-d')
				),
				$userConditions
			),
			'fields' => array(
				'Merchant.id',
				'Merchant.merchant_dba',
				'Merchant.active_date'
			),
			'order' => array('Merchant.active_date' => 'DESC'),
			'group' => array('Merchant.id', 'Merchant.merchant_dba'),
			'limit' => $limit,
			'joins' => $joins
		));
		$recentLogins = array();
		if ($this->SystemTransaction->RbacIsPermitted('app/actions/Dashboards/view/module/homeAdminModule')) {
			$transactDate = new DateTime(date('Y-m-d'));
			$transactDate->sub(new DateInterval('P7D'));
			$fromDate = $transactDate->format('Y-m-d');
			$this->User->virtualFields['fullname'] = $this->User->getFullNameVirtualField('User');
			$userLogins = $this->SystemTransaction->find('all', array(
				'contain' => array('User'),
				'conditions' => array(
					'SystemTransaction.transaction_type_id' => TransactionType::USER_LOGIN,
					'SystemTransaction.system_transaction_date >' => $fromDate,
					'SystemTransaction.system_transaction_date <=' => date('Y-m-d'),
					'User.active' => true,
				),
				'fields' => array(
					'SystemTransaction.system_transaction_date',
					'SystemTransaction.system_transaction_time',
					'((' . $this->User->getFullNameVirtualField('User') . ')) As "User__fullname"',
					'User.id'
				),
				'order' => array(
					'SystemTransaction.system_transaction_date' => 'DESC',
					//also sort by time to get the latest within a date subset at the top
					'SystemTransaction.system_transaction_time' => 'DESC'
				),
			));

			$recentLogins = array();
			$loginListCount = 1;
			foreach ($userLogins as $logins) {
				//Only want the latest user login per user
				if (empty(Hash::extract($recentLogins, '{n}.User[fullname=' . $logins['User']['fullname'] . '].fullname'))) {
					//zero-based index
					$recentLogins[$loginListCount - 1] = array(
						'SystemTransaction' => $logins['SystemTransaction'],
						'User' => $logins['User']
					);
					//We want a maximum of 10 recent logins in the list
					if ($loginListCount !== 10) {
						$loginListCount++;
					} else {
						break;
					}
				}
			}
		}

		$currentUserLastLGN = $this->SystemTransaction->find('first', array(
			'conditions' => array(
				'SystemTransaction.transaction_type_id =' => TransactionType::USER_LOGIN,
				'SystemTransaction.user_id =' => $this->Auth->user('id')
			),
			'order' => 'SystemTransaction.system_transaction_date DESC, SystemTransaction.system_transaction_time DESC',
			'offset' => 1,
			'fields' => array(
				'SystemTransaction.system_transaction_date',
				'SystemTransaction.system_transaction_time',
			)
		));

		$recentNotesCond = $userConditions;
		if ($this->User->isAdmin($this->Auth->user('id'))) {
			$recentNotesCond[] = 'MerchantNote.id in (SELECT id FROM merchant_notes ORDER BY note_date DESC LIMIT 10)';
		} else {
			$recentNotesCond['MerchantNote.note_date >='] = date_format($date, 'Y-m-d');
		}
		$recentNotes = $this->MerchantNote->find('all', array(
			'contain' => array('Merchant'),
			'conditions' => $recentNotesCond,
			'order' => array('MerchantNote.note_date' => 'DESC'),
			'fields' => array(
				'MerchantNote.note_title',
				'MerchantNote.note_date',
				'Merchant.merchant_dba',
				'Merchant.id',
			),
			'group' => array('Merchant.id', 'Merchant.merchant_dba', 'MerchantNote.id'),
			'joins' => $joins,
			'limit' => $limit
		));
		$recentOrders = array();
		if ($this->Order->RbacIsPermitted('app/actions/Dashboards/view/module/homeAdminModule')) {
			$recentOrders = $this->Order->find('all', array(
				'contain' => array('User'),
				'conditions' => array(
					'Order.date_ordered >=' => date_format($date, 'Y-m-d')),
				'fields' => array(
					'User.id',
					'User.user_first_name',
					'User.user_last_name',
					'Order.id',
					'Order.user_id',
					'Order.date_ordered',
					'Order.invoice_number'
				),
				'order' => array('Order.date_ordered' => 'DESC'),
				'limit' => $limit
			));
		}

		$approvedChngsCond = $userConditions;
		$changeRequestTypeId = NoteType::CHANGE_REQUEST_ID;
		if ($this->User->isAdmin($this->Auth->user('id'))) {
			$approvedChngsCond[] = "MerchantNote.id in (SELECT id FROM merchant_notes WHERE note_type_id = '$changeRequestTypeId' AND general_status = '" . MerchantNote::STATUS_COMPLETE . "' ORDER BY resolved_date DESC NULLS LAST, resolved_time DESC LIMIT 10)";
		} else {
			$approvedChngsCond['MerchantNote.resolved_date >='] = date_format($date, 'Y-m-d H:i:s');
			$approvedChngsCond['MerchantNote.note_type_id'] = $changeRequestTypeId;
			$approvedChngsCond['MerchantNote.general_status'] = MerchantNote::STATUS_COMPLETE;
		}

		$approvedChngs = $this->MerchantNote->find('all', array(
			'contain' => array('Merchant'),
			'conditions' => $approvedChngsCond,
			'order' => 'MerchantNote.resolved_date DESC NULLS FIRST',
			'fields' => array(
				'MerchantNote.id',
				'MerchantNote.note_title',
				'MerchantNote.resolved_date',
				'Merchant.merchant_dba',
				'Merchant.id',
			),
			'group' => array('Merchant.id', 'Merchant.merchant_dba', 'MerchantNote.id'),
			'joins' => $joins,
			'limit' => $limit
		));

		$recentPENDRequest = $this->MerchantNote->find('all', array(
			'contain' => array('Merchant'),
			'conditions' => array_merge(
				array(
					'MerchantNote.note_date >=' => date_format($date, 'Y-m-d'),
					'MerchantNote.note_type_id' => $changeRequestTypeId,
					'MerchantNote.general_status' => MerchantNote::STATUS_PENDING,
				),
				$userConditions
			),
			'order' => array('MerchantNote.note_date' => 'DESC'),
			'fields' => array(
				'MerchantNote.id',
				'MerchantNote.note_title',
				'MerchantNote.note_date',
				'Merchant.merchant_dba',
				'Merchant.id',
			),
			'group' => array('Merchant.id', 'Merchant.merchant_dba', 'MerchantNote.id'),
			'joins' => $joins,
			'limit' => $limit
		));

		//Prepare data for dashboard charts/graphs
		$acqMerchCount = $merchant = $this->Dashboard->getAcquiringMerch();
		$mActivityStats = $this->Dashboard->getPiChartData();
		$acquiringMerchCount = json_encode($acqMerchCount);
		$mActivityStats = json_encode($mActivityStats);
		$isLogin = (strpos($this->referer(), 'login') !== false);

		//Pass view-variables to the home page
		$this->set(compact(
			'isLogin',
			'currentUserLastLGN',
			'recentlyAdded',
			'recentLogins',
			'recentNotes',
			'recentOrders',
			'approvedChngs',
			'recentPENDRequest',
			'changeRequestTypeId',
			'acquiringMerchCount',
			'mActivityStats'
		));
	}

/**
 * getChartData
 *
 * @param string $funcName a callable function to use to retrieve the chart data
 * @param string $param the parameter to pass to the callable function. Data must be compatible with called function.
 * @return void
 */
	public function getChartData($funcName, $param) {
		$this->autoRender = false;
		if ($this->Session->check('Auth.User.id')) {
			$data = call_user_func(array($this->Dashboard, $funcName), $param);
			$data = json_encode($data);
			return $data;
		} else {
			//Session expired status 403
			$this->response->statusCode(403);
		}
	}
}
