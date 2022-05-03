<?php

App::uses('AppModel', 'Model');

/**
 * MerchantUw Model
 *
 * @property Merchant $Merchant
 * @property FinalStatus $FinalStatus
 * @property FinalApproved $FinalApproved
 */
class MerchantUw extends AppModel {

/**
 * Load Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Search.Searchable',
		'ChangeRequest',
		'SearchByUserId' => [
			'userRelatedModel' => 'Merchant'
		],
	);

/**
 * set filterArgs
 *
 * @var array
 */
	public $filterArgs = [
		'user_id' => [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"Merchant"."user_id"',
			'searchByMerchantEntity' => true,
		],
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MerchantUwFinalStatus' => array(
			'className' => 'MerchantUwFinalStatus',
			'foreignKey' => 'merchant_uw_final_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MerchantUwFinalApproved' => array(
			'className' => 'MerchantUwFinalApproved',
			'foreignKey' => 'merchant_uw_final_approved_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SponsorBank' => array(
			'className' => 'SponsorBank',
			'foreignKey' => 'sponsor_bank_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'MerchantUwVolume' => array(
			'className' => 'MerchantUwVolume',
			'foreignKey' => 'merchant_uw_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		$approvedId = $this->MerchantUwFinalStatus->field('id', array('name' => MerchantUwFinalStatus::APPROVED));
		if (Hash::get($this->data, 'MerchantUw.merchant_uw_final_status_id') === $approvedId) {
			$previousDate = $this->field('final_date', ['id' => Hash::get($this->data, 'MerchantUw.id')]);
			$TimelineEntry = ClassRegistry::init('TimelineEntry');
			$hasApprTimeEntry = $TimelineEntry->hasAny(['timeline_item_id' => TimelineItem::APPROVED, 'merchant_id' => $this->data[$this->alias]['merchant_id']]);
			//Update/Create a timeline entry for approval IFF this is a new record or if the final_date has changed from before
			if (empty(Hash::get($this->data, 'MerchantUw.id')) || $previousDate !== $this->data[$this->alias]['final_date'] || $hasApprTimeEntry === false) {
				$tEntry = array(
					'merchant_id' => $this->data[$this->alias]['merchant_id'],
					'timeline_item_id' => TimelineItem::APPROVED,
					'timeline_date_completed' => (!empty($this->data[$this->alias]['final_date']))? date("Y-m-d", strtotime($this->data[$this->alias]['final_date'])) : null, //enforce consistent format
				);
				$TimelineEntry->saveTimeEntry($tEntry);
			}
		}
	}

/**
 * getUwDataByMerchantId
 *
 * @param string $id merchant's id
 * @return array $data merchant's underwriting and associated data
 */
	public function getUwDataByMerchantId($id) {
		$contain = array(
			'UwInfodocMerchantXref' => array('UwInfodoc', 'UwReceived'),
			'UwStatusMerchantXref' => array('UwStatus'),
			'UwApprovalinfoMerchantXref' => array('UwApprovalinfo', 'UwVerifiedOption'),
			'MerchantUw' => array('MerchantUwFinalApproved', 'MerchantUwFinalStatus', 'SponsorBank'),
			'MerchantUwVolume'
		);
		$data = $this->Merchant->find('first', array('recursive' => -1, 'contain' => $contain,
			'conditions' => array('Merchant.id' => $id)));

		if (!empty($data)) {
			$data['Merchant']['UwInfodocMerchantXref'] = $data['UwInfodocMerchantXref'];
			$data['Merchant']['UwStatusMerchantXref'] = $data['UwStatusMerchantXref'];
			$data['Merchant']['UwApprovalinfoMerchantXref'] = $data['UwApprovalinfoMerchantXref'];

			//add user data to top array dimention
			$data += $this->Merchant->User->find('first', array(
				'recursive' => -1,
				'fields' => array(
					'User.user_first_name', 'User.user_last_name', 'User.user_email'
				),
				'conditions' => array(
					'User.id' => $data['Merchant']['user_id']
				)
			));

			unset($data['UwInfodocMerchantXref']);
			unset($data['UwStatusMerchantXref']);
			unset($data['UwApprovalinfoMerchantXref']);
			//remove user id to prevent user menu from displaying
			unset($data['User']['id']);
		}

		return $data;
	}

/**
 * getAssociatedUwLists method
 *
 * @param array $thisData optional array containing MerchantUw data
 * @return array
 */
	public function getAssociatedUwLists($thisData = array()) {
		$data = array();

		$data['uwSatuses'] = $this->Merchant->UwStatusMerchantXref->UwStatus->find('all');
		$data['approvalInfos'] = $this->Merchant->UwApprovalinfoMerchantXref->UwApprovalinfo->find('all');
		$data['uwRequiredInfoDocs'] = $this->Merchant->UwInfodocMerchantXref->UwInfodoc->find('byRequired', ['required' => true]);
		$data['uwOtherInfoDocs'] = $this->Merchant->UwInfodocMerchantXref->UwInfodoc->find('byRequired');
		$data['finalStatusOptns'] = $this->MerchantUwFinalStatus->getUwFinalStatusesList();
		$data['approversOptns'] = $this->MerchantUwFinalApproved->getActivePlus(Hash::get($thisData, "MerchantUw.merchant_uw_final_approved_id"));

		return $data;
	}

/**
 * reIndexUwMerchantXrefsData method
 *
 * Ensures the indexes from both params match so that uwMerchantXtref data is displayed in the proper order on the view.
 * Re-aranges the model data in $uwMerchantXtref by making its index match its corresponding associated $uwList data by the priority field.
 * For example: if $uwMerchantXtref is data from UwStatusMerchantXref (which Merchant hasMany) then UwStatusMerchantXref data will be
 * re-indexed with index numbers equal to the index position of the corresponding UwStatus.priority.
 *
 * @param array $uwList an array containing data from either UwStatus, UwApprovalinfo or UwInfodoc sorted by priority
 * @param array $uwMerchantXtref containing data from either UwStatusMerchantXref, UwApprovalinfoMerchantXref or UwInfodocMerchantXref
 * @return array
 * @throws \InvalidArgumentException
 */
	public function reIndexUwMerchantXrefsData($uwList, $uwMerchantXtref) {
		if (empty($uwMerchantXtref)) {
			return array();
		}
		$chekASCSort = Hash::extract($uwList, "{n}.{s}.priority");
		$tot = count($chekASCSort);
		for ($i = 1; $i < $tot; $i++) {
			for ($j = 0; $j <= $i; $j++) {
				//Check all previous values with indexes lower than $i
				if ($chekASCSort[$i - $j] > $chekASCSort[$i]) {
					throw new InvalidArgumentException("Argument 1 is not sorted in ascending numerical order by priority field as expected");
				}
			}
		}
		$result = array();
		$cntX = count($uwList);
		//In this case count($uwMerchantXtref) != n in $uwMerchantXtref, therefore
		//we must set count equal to the highest index number + 1  rather than the actual number of elements
		$tmpI = array_keys($uwMerchantXtref);
		$tmpI = array_pop($tmpI);
		$cntI = (is_null($tmpI))? 0 : $tmpI + 1;
		for ($x = 0; $x < $cntX; $x++) {
			//extract the id of the current priority number in incremental order
			$curUwListId = Hash::extract($uwList, "{n}.{s}[priority=" . ($x + 1) . "].id");
			$curUwListId = array_pop($curUwListId);
			for ($i = 0; $i < $cntI; $i++) {
				if (!empty($uwMerchantXtref[$i])) {
					foreach ($uwMerchantXtref[$i] as $val) {
						if ($val === $curUwListId) {
							$result[$x] = $uwMerchantXtref[$i];
						}
					}
				}
			}
		}
		return $result;
	}

/**
 * extractInfodocsXrefsDataByRequirement method
 *
 * @param array $uwMerchantXtref an array containing data from UwInfodoc
 * @param bool $isRequiredData bool value to check if data is valid
 * @return array
 */
	public function extractInfodocsXrefsDataByRequirement($uwMerchantXtref, $isRequiredData) {
		if (empty($uwMerchantXtref)) {
			return $uwMerchantXtref;
		}
		if (empty(Hash::extract($uwMerchantXtref, "{n}.UwInfodoc"))) {
			$infoDocs = $this->Merchant->UwInfodocMerchantXref->UwInfodoc->find('byRequired', ['required' => $isRequiredData]);
		}
		$result = array();
		foreach ($uwMerchantXtref as $key => $val) {
			if (!empty($infoDocs)) {
				//if current uwMerchantXtref uw_infodoc_id is
				//contain within this subset of UwInfodocs whith required = $isRequiredData then add it to the result
				if (!empty(Hash::extract($infoDocs, "{n}.UwInfodoc[id=" . $val["uw_infodoc_id"] . "].id"))) {
					array_push($result, $val);
				}
			} else {
				if ($val['UwInfodoc']['required'] === $isRequiredData) {
					array_push($result, $val);
				}
			}
		}
		return $result;
	}

/**
 * filterEmptyElements recursive method
 * Recursively traverses the entire data structure and removes all empty array elements but only from array dimensions that lack an id key.
 * This is necessary when an existing record's original field value is removed by the user on the client side.
 *
 * @param array $modelData model data
 * @return array
 */
	public function filterEmptyElements($modelData) {
		foreach ($modelData as $key => $data) {
			if (is_array($data)) {
				$modelData[$key] = $this->filterEmptyElements($data);
				if (empty($modelData[$key])) {
					unset($modelData[$key]);
				}
			} else {
				//iff no id is set then we want to remove empty array elements
				if (!isset($modelData['id'])) {
					if (!isset($modelData[$key]) || $modelData[$key] === "") {
						unset($modelData[$key]);
					}
					//If after cleanup the only data remaining is the _id (which is programmatically added on the view) then remove it as well
					$tmpArr = $modelData;
					if (count($tmpArr) === 1 && $this->isValidUUID(array_pop($tmpArr))) {
						$modelData = array();
					}
				}
			}

		}

		return $modelData;
	}

/**
 * setPaginatorSettings method
 *
 * @param array $params values to create the conditions to be used in the query
 * @return array
 */
	public function setPaginatorSettings($params) {
		$signedId = $this->Merchant->TimelineEntry->TimelineItem->field("id", array('timeline_item_description' => 'Signed'));

		$contain = array(
			'TimelineEntry' => array('fields' => 'TimelineEntry.timeline_date_completed',
				'conditions' => array('TimelineEntry.timeline_item_id' => $signedId)),
			'MerchantUw' => array('fields' => array('MerchantUw.expedited', 'MerchantUw.app_quantity_type')),
			'UwStatusMerchantXref' => array('UwStatus', 'order' => "UwStatusMerchantXref.datetime ASC",
				'fields' => array('UwStatusMerchantXref.datetime'))
		);

		$uwStsXrefConditions = array('UwStatusMerchantXref.merchant_id = Merchant.id');
		if (!empty($params['beginY']) && !empty($params['beginM'])) {
			$uwStsXrefConditions['UwStatusMerchantXref.datetime >='] = $params['beginY'] . '-' . $params['beginM'] . '-01';
		}

		if (!empty($params['endY']) && !empty($params['endM'])) {
			$uwStsXrefConditions['UwStatusMerchantXref.datetime <='] = $params['endY'] . '-' . $params['endM'] . '-' . cal_days_in_month(CAL_GREGORIAN, $params['endM'], $params['endY']);
		}

		$joins = array(
			array('table' => 'uw_status_merchant_xrefs',
				'alias' => 'UwStatusMerchantXref',
				'type' => 'INNER',
				'conditions' => $uwStsXrefConditions
			),
			array('table' => 'users',
				'alias' => 'User',
				'type' => 'INNER',
				'conditions' => array(
					'Merchant.user_id = User.id'
				)
			),
			array('table' => 'users',
				'alias' => 'Partner',
				'type' => 'LEFT',
				'conditions' => array(
					'Merchant.partner_id = Partner.id'
				)
			),
			array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.client_id = Client.id'
					)
				),
		);

		$settings = array(
			'limit' => 500,
			'maxLimit' => 500,
			'fields' => array(
				'Merchant.merchant_dba',
				'Merchant.merchant_mid',
				'Merchant.active', 'User.user_first_name', 'User.user_last_name',
				'Client.client_id_global',
				'((' . $this->Merchant->User->getFullNameVirtualField('Partner') . ')) as "Partner__fullname"',
				),
			'contain' => $contain,
			'joins' => $joins,
			'group' => array(
				'Merchant.id',
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Merchant.active',
				'Client.client_id_global',
				'User.user_first_name',
				'User.user_last_name',
				'Partner.user_first_name',
				'Partner.user_last_name',
				'MerchantUw.expedited',
				'MerchantUw.app_quantity_type'
			)
		);

		//Filter by merchant
		$settings['conditions'] = array();
		if (!empty($params['dba_mid'])) {
			$settings['conditions'][] = $this->Merchant->orConditions(array("search" => $params['dba_mid']));
		}
		//Always exclude gateway/payment fusion merchants which have an MID > 6 digits
		$settings['conditions'][] = 'length(Merchant.merchant_mid) > 6';
		$complexUserIdArgs = $this->Merchant->User->extractComplexId(Hash::get($params, 'user_id'));

		//This method will return '"MerchantUw"."user_id"' as the search field but this model's table does not have a user_id field
		$usersFilterArgs = $this->getUserSearchFilterArgs($complexUserIdArgs);
		if (Hash::get($complexUserIdArgs, 'prefix') === User::PREFIX_ENTITY || (Hash::get($complexUserIdArgs, 'prefix') === User::PREFIX_USER && Hash::get($usersFilterArgs, 'field') !== '"MerchantUw"."user_id"')) {
			$this->filterArgs['user_id'] = $usersFilterArgs;
		}

		/*Merge user conditions*/
		$settings['conditions'] = array_merge($settings['conditions'], $this->parseCriteria($params));
		return $settings;
	}

/**
 * makeCsvString method
 *
 * @param array $searchSettings conditions to use for search
 * @return string comma delimited string
 */
	public function makeCsvString($searchSettings) {
		//Initialize RBAC
		$merchantUws = $this->Merchant->find('all', $this->setPaginatorSettings($searchSettings));
		//Padd UwStatusMerchantXref data to achieve correct number of elements
		$uwStatuses = $this->_getUwStatus()->find('all', array(
			'order' => array(
				'UwStatus.priority' => 'asc'
			)
		));
		$statusCount = count($uwStatuses);
		foreach ($merchantUws as $key => $data) {
			$merchantUws[$key]['UwStatusMerchantXref'] = $this->reIndexUwMerchantXrefsData($uwStatuses, $data['UwStatusMerchantXref']);
		}
		$dispDateBegin = date_format(date_create($searchSettings['beginY'] . '-' . $searchSettings['beginM'] . '-01'), 'M Y');
		$dispDateEnd = date_format(date_create($searchSettings['endY'] . '-' . $searchSettings['endM'] . '-' . cal_days_in_month(CAL_GREGORIAN, $searchSettings['endM'], $searchSettings['endY'])), 'M Y');
		$csvStr = "\nUnderwriting Report | " . $dispDateBegin . (isset($dispDateEnd) ? " - " . $dispDateEnd : "") . "\n";
		$csvStr .= "Signed,MID,DBA,Client ID,Rep,Expedited,Received,Illegible,Incomplete,Complete,Submitted to UW,Add'l UW Items Needed,Rec'd Add'l UW Items,Approved,Declined,Status" . "\n";

		foreach ($merchantUws as $merchantUw) {
			if (!empty($merchantUw['TimelineEntry'][0])) {
				$csvStr .= date_format(date_create($merchantUw['TimelineEntry'][0]['timeline_date_completed']), 'm/d/Y');
			}
			$csvStr .= ',="' . $merchantUw['Merchant']['merchant_mid'] . '"';
			$csvStr .= ',"' . trim($merchantUw['Merchant']['merchant_dba']) . '"';
			$csvStr .= ',"' . trim($merchantUw['Client']['client_id_global']) . '"';
			if ($this->rbacIsPermitted('app/actions/MerchantUws/view/module/indexRepCol')) {
				$csvStr .= "," . $merchantUw['User']['user_first_name'] . ' ' . substr($merchantUw['User']['user_last_name'], 0, 1);
			}
			$csvStr .= ($merchantUw['MerchantUw']['expedited']) ? ",Yes" : ",No";
			for ($x = 0; $x < $statusCount; $x++) {
				$csvStr .= ",";
				if (!empty($merchantUw['UwStatusMerchantXref'][$x]['datetime'])) {
					$csvStr .= date_format(date_create($merchantUw['UwStatusMerchantXref'][$x]['datetime']), 'm/d/Y H:i');
				}
			}
			$csvStr .= ($merchantUw['Merchant']['active']) ? ",Active" : ",Inactive";
			$csvStr .= "\n";
		}
		return $csvStr;
	}

/**
 * UwStatus mocker
 *
 * @return UwStatus
 */
	protected function _getUwStatus() {
		return ClassRegistry::init('UwStatus');
	}

/**
 * emailRepUwStatus method
 * Sets up email data and triggers readyForEmail event with that data
 *
 * @param array $merchantUwData MerchantUw Data
 * @param string $statusXrefId Status Data
 * @return bool
 */
	public function emailRepUwStatus($merchantUwData, $statusXrefId) {
		$merchDBA = $merchantUwData['Merchant']['merchant_dba'];
		$repName = $merchantUwData['User']['user_first_name'] . ' ' . $merchantUwData['User']['user_last_name'];
		$notifier = CakeSession::read('Auth.User.fullname');
		foreach ($merchantUwData['Merchant']['UwStatusMerchantXref'] as $uwStatusXrefsData) {
			if ($uwStatusXrefsData['id'] === $statusXrefId) {
				break; //Stop when found so we can use this $uwStatusXrefsData below
			}
		}
		$emailBody = "Hello " . $repName . ",\n";
		$emailBody .= "The Underwriting status of " . $merchDBA . " has been updated to " . $uwStatusXrefsData['UwStatus']['name'] . ".\n";
		if (!empty($uwStatusXrefsData['notes'])) {
			$emailBody .= "Additional Notes:\n";
			$emailBody .= $uwStatusXrefsData['notes'] . "\n";
		}
		$emailBody .= "Sincerely,\nAxia Underwriting";

		if (!empty($notifier)) {
			$emailBody .= "\n";
			$emailBody .= "(This notification was triggered by $notifier, please do not reply to this message.)\n";
		}

		// create a new event
		$event = new CakeEvent('App.Model.readyForEmail', $this, array(
			'from' => 'RM@axiapayments.com',
			'to' => $merchantUwData['User']['user_email'],
			'bcc' => Configure::read('App.underwritingEmail'),
			'subject' => $merchDBA . " Underwriting Status Update",
			'emailBody' => $emailBody
				)
		);
		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
		return $event->result;
	}

/**
 * setFormMenuData
 * Gathers all data required for menus on the editable forms
 *
 * @param array $reqData request data
 * @return array
 */
	public function setFormMenuData($reqData) {
		$uwVerifiedOptns = $this->Merchant->UwApprovalinfoMerchantXref->UwVerifiedOption->getUwVerifiedOptions();
		$uwReceivedOptns = $this->Merchant->UwInfodocMerchantXref->UwReceived->find('prioritized');
		$sponsorBankOptns = $this->SponsorBank->getSponsorBanksList();
		$merchant = $this->Merchant->getSummaryMerchantData(Hash::get($reqData, 'Merchant.id'));
		return compact('merchant', 'uwReceivedOptns', 'uwVerifiedOptns', 'sponsorBankOptns');
	}
/**
 * getDefaultSearchValues
 * Gathers all data required for menus on the editable forms
 *
 * @return array
 */
	public function getDefaultSearchValues() {
		return [
			'beginM' => date('n'),
			'beginY' => date('Y'),
			'endM' => date('n'),
			'endY' => date('Y'),
			'user_id' => $this->Merchant->User->getDefaultUserSearch()
		];
	}

/**
 * setFinalDate
 * Checks if UwStatusMerchantXref.datetime (for Approved UwStatus) is set and sets MerchantUw.final_date equal to it.
 *
 * @param array &$data reference to data submited from a client side form
 * @return array
 */
	public function setFinalDate(&$data) {
		if (!empty(Hash::filter($data['MerchantUw']['final_date'])) || empty(Hash::filter(Hash::extract($data, 'Merchant.UwStatusMerchantXref.{n}.datetime')))) {
			return;
		}
		$approvedId = $this->Merchant->UwStatusMerchantXref->UwStatus->field('id', ['name' => 'Approved']);
		if (!empty($approvedId)) {
			$approvedTime = Hash::extract($data, "Merchant.UwStatusMerchantXref.{n}[uw_status_id=$approvedId].datetime");
			$approvedTime = array_pop($approvedTime);
			if (is_array($approvedTime) && !empty(Hash::filter($approvedTime))) {
				$data['MerchantUw']['final_date'] = $approvedTime;
			}
		}
	}
}
