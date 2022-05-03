<?php

App::uses('AppController', 'Controller');
/**
 * MerchantNotes Controller
 *
 * @property MerchantNote $MerchantNote
 */
class MerchantNotesController extends AppController {

	public $components = array(
		'GUIbuilder',
		'Search.Prg' => array(
			'commonProcess' => array('paramType' => 'querystring'),
			'presetForm' => array('paramType' => 'querystring')
		));

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Prg->commonProcess();
		$parsedParams = $this->Prg->parsedParams();
		$this->_setSearchParams($parsedParams);
		$this->Paginator->settings = $this->MerchantNote->getIndexPaginatorSettings($parsedParams);

		//Set sort order
		$sortField = Hash::get($this->request->params, 'named.sort');
		$order = ($sortField)? [$sortField => Hash::get($this->request->params, 'named.direction')] : ['Merchant.merchant_mid' => 'asc'];
		$this->Paginator->settings['order' ] =  $order;
		
		$noteTypes = $this->MerchantNote->NoteType->getList();
		$actionTypes = array_merge($this->MerchantNote->Merchant->MerchantAch->invTypes, $noteTypes);
		$users = [];
		$statusList = [];
		$this->Paginator->settings['limit' ] = 1000;
		$this->Paginator->settings['maxLimit'] = 1000;
		if (Hash::get($this->request->params, 'ext') === 'csv') {
			//Increase memory, this report can be very big
			ini_set('memory_limit', '10G');
			set_time_limit(0);
			$countSettings = $this->Paginator->settings;
			unset($countSettings['limit']);
			$resultCount = $this->MerchantNote->Merchant->find('count', $countSettings);

			unset($this->request->params['named']['limit']);
			unset($this->request->params['named']['page']);
			$this->Paginator->settings['limit' ] = $resultCount;
			$this->Paginator->settings['maxLimit'] = $resultCount;
			$this->GUIbuilder->setCsvView();
			$this->response->download('merchant-notes-requests.csv');
		} else {
			$users = $this->MerchantNote->User->getEntityManagerUsersList(true);
			$statusList = $this->MerchantNote->getStatusList();
			$countSettings = $this->Paginator->settings;
			unset($countSettings['limit']);
		}
		$merchantNotes = $this->Paginator->paginate('Merchant');

		$this->set(compact('merchantNotes', 'users', 'actionTypes', 'statusList'));
	}

/**
 * _checkSortParam protected method 
 * Checks that the Model.field combination set for sort is correct.
 * This is done based on whether the action_type filter is NoteType UUID or a string matching MerchantAch->invTypes
 * If action_type is UUID then we can only sort by MerchantNote otherwise we can only sort by MerchantAch model data
 *
 * @param array 
 * @return string
 */
	protected function _checkSortParam($requestParams = array()) {
		$sortParams = Hash::get($requestParams, 'named.sort');
		if (!empty($sortParams)) {
			$parts = explode('.', $sortParams);
			//if selected action_type is an UUID then the the sort model must be MerchantNote
			if ($this->MerchantNote->isValidUUID(Hash::get($this->Prg->parsedParams(), 'action_type'))) {
				if (Hash::get($parts, '0') !== $this->MerchantNote->alias) {
					unset($this->request->params['named']['sort']);
					unset($this->request->params['named']['direction']);
				}
			} else {
				if (Hash::get($parts, '0') !== 'MerchantAch') {
					unset($this->request->params['named']['sort']);
					unset($this->request->params['named']['direction']);
				}
			}
		}
	}
/**
 * _setSearchParam protected method 
 *
 * @param array &$parsedParams reference to arguments passed by search
 * @return void
 */
	protected function _setSearchParams(array &$parsedParams) {
		if (empty($parsedParams['user_id'])) {
			$parsedParams['user_id'] = $this->MerchantNote->getCurrentUserComplexId();
		}
		if (empty($parsedParams['from_date'])) {
			$parsedParams['from_date']['year'] = date('Y');
			$parsedParams['from_date']['month'] = date('m');
		}
		if (empty($parsedParams['end_date'])) {
			$parsedParams['end_date']['year'] = date('Y');
			$parsedParams['end_date']['month'] = date('m');
		}

		//Change filterArgs if searching by author (MerchantNote.user_id) instead of Merchant.user_id
		$this->MerchantNote->searchByAuthor = (Hash::get($this->Prg->parsedParams(), 'author_type') === MerchantNote::AUTHOR_TYPE_AUTHOR);
		$authorParam = Hash::get($this->Prg->parsedParams(), 'author_name');
		if ($this->MerchantNote->searchByAuthor) {
			if (!empty($parsedParams['action_type'])) {
				if (!empty($authorParam)) {
					$conditions = [
						'OR' => [
							'User.user_first_name ILIKE' => "%$authorParam%",
							'User.user_last_name ILIKE' => "%$authorParam%",
							'User.username ILIKE' => "%$authorParam%",
							'User.user_email ILIKE' => "%$authorParam%",
							$this->MerchantNote->User->getFullNameVirtualField('User') . ' ILIKE' =>  "%$authorParam%",
						]
					];
					$userIds = $this->MerchantNote->User->find('list', ['conditions' => $conditions, 'fields' => ['id']]);
					$parsedParams['user_id'] = $userIds;
				} else {
					unset($parsedParams['user_id']);
				}
				$this->MerchantNote->filterArgs['user_id']['type'] = 'value';
				if ($this->MerchantNote->isValidUUID($parsedParams['action_type'])) {
					$this->MerchantNote->filterArgs['user_id']['field'] = '"MerchantNote"."user_id"';
				} else {
					$this->MerchantNote->filterArgs['user_id']['field'] = '"MerchantAch"."user_id"';
				}
			} else {
				if (!empty($authorParam)) {
					$parsedParams['user_id'] = $authorParam;
					$this->MerchantNote->filterArgs['user_id'] = array(
						'type' => 'ilike',
						'field' => array(
							'User.user_first_name',
							'User.user_last_name',
							'User.username',
							'User.user_email',
							$this->MerchantNote->User->getFullNameVirtualField('User'),
						)
					);					
				} else {
					$complexUserId = $this->MerchantNote->User->buildComplexId(User::PREFIX_ALL, User::PREFIX_ALL);
					$parsedParams['user_id'] = $complexUserId;
				}
			}
		} else {
			$complexUserIdArgs = $this->MerchantNote->User->extractComplexId(Hash::get($parsedParams, 'user_id'));
			$this->MerchantNote->filterArgs['user_id'] = $this->MerchantNote->getUserSearchFilterArgs($complexUserIdArgs);
			if (Hash::get($complexUserIdArgs, 'prefix') !== User::PREFIX_ENTITY) {
				$this->MerchantNote->filterArgs['user_id']['field'] = '"Merchant"."user_id"';
			}
		}

	}

/**
 * view method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function view($id = null) {
		$this->MerchantNote->id = $id;
		if (!$this->MerchantNote->exists()) {
			throw new NotFoundException(__('Invalid merchant note'));
		}
		$this->set('merchantNote', $this->MerchantNote->read(null, $id));
	}

/**
 * add method
 *
 * @param string $id id
 * @param string $noteType Note description
 * @throws Exception
 * @return void
 */
	public function add($id = null, $noteType = null) {
		/* This action handles Ajax request */
		if (empty($noteType)) {
			throw new Exception('Error: ' . $this->name . ' ' . $this->action . ' action expects argument $noteType. None was provided');
		} else {

			$noteID = $this->MerchantNote->NoteType->findByNoteTypeDescription($noteType);
			if (empty($noteID)) {
				throw new Exception('Error: The' . $noteType . ' is an invalid type');
			}
		}

		$this->autoRender = false;
		/* Check if session is still active by checking currently logged in user id */
		if ($this->Auth->user('id')) {
			$this->set(compact('noteType'));
			if (!empty($this->request->data)) {
				$this->MerchantNote->create();
				if ($this->MerchantNote->save($this->request->data)) {
					if (!empty($this->request->data['MerchantNote']['note_sent']) && $this->request->data['MerchantNote']['note_sent'] === '1') {
							$this->MerchantNote->emailNoteToRep($this->MerchantNote->getNoteById($this->MerchantNote->id));
					}
					$this->response->statusCode(200);
				} else {
					$this->response->statusCode(400);

					$errors = array();
					foreach ($this->MerchantNote->validationErrors as $field) {
						foreach ($field as $rule) {
							array_push($errors, $rule);
						}
					}
					// Set the message to display in our element
					$this->set('message', __('The merchant note could not be saved due to these errors:'));
					$this->set(compact('errors'));
					// Render the error_dialog element
					$this->render('/Elements/error_dialog', 'ajax');
				}
			} else {
				$this->request->data['MerchantNote'] = array('note_type_id' => $this->MerchantNote->NoteType->findByNoteTypeDescription($noteType),
					'user_id' => $this->Session->read('Auth.User.id'),
					'merchant_id' => $id,
					'general_status' => MerchantNote::STATUS_COMPLETE
				);
				$this->set('gnrOptions', $this->MerchantNote->getStatusGnrOptions());
				$this->render('/MerchantNotes/add', 'ajax');
			}
		} else {
			/* Upon session expiration, we cannot use $this->redirect(...) dutring an ajax requests in process.
			 * Therefore return a Forbidden status 403 responce and handle this responce on the client side with ajax. */
			$this->response->statusCode(403);
		}
	}

/**
 * edit method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null) {
		$this->MerchantNote->id = $id;
		if (!$this->MerchantNote->exists()) {
			throw new NotFoundException(__('Invalid merchant note'));
		}

		$userId = $this->Auth->user('id');
		if (!$this->MerchantNote->userHasAccess($id, $userId)) {
			$this->_failure(__('You dont have access to this Merchant Note'), $this->referer());
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
			try {
				$result = $this->MerchantNote->edit($this->request->data);
			} catch (Exception $e) {
				$result = false;
			}
			if ($result === true) {
				$this->_success(__('The merchant note has been saved'), $this->referer());
			} else {
				if ($this->request->data('MerchantNote.note_type_id') === NoteType::CHANGE_REQUEST_ID) {
					$this->Session->setFlash('Failed to apply changes due to validation errors! To review and fix validation errors go to "View change" and hit save. Validation errors will be displayed on the form.', 'default', ['class' => 'alert alert-danger strong']);
					$this->redirect($this->referer());
				} else {
					$this->_failure($result);					
				}
			}
		} else {
			$this->request->data = $this->MerchantNote->getNoteById($id);
		}

		$this->set($this->MerchantNote->getEditFormRelatedData($this->request->data));
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
		$this->MerchantNote->id = $id;
		if (!$this->MerchantNote->exists()) {
			throw new NotFoundException(__('Invalid merchant note'));
		}
		$merchantId = $this->MerchantNote->field('merchant_id', ['id' => $id]);
		$redirectUrl = ['controller' => 'Merchants', 'action' => 'notes', $merchantId];
		if ($this->MerchantNote->delete()) {
			$this->_success(__('Merchant note deleted'), $redirectUrl);
		}
		$this->_failure(__('Merchant note was not deleted, try again.'), $redirectUrl);
	}

}
