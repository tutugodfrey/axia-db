<?php

App::uses('AppController', 'Controller');
App::uses('Component', 'Controller');
App::uses('MerchantChange', 'Model');
App::uses('MerchantNote', 'Model');

class GUIbuilderComponent extends Component {

/**
 * View output types
 *
 * @var string
 */
	const OUTPUT_CSV = 'csv';
	const OUTPUT_PRINT = 'print';

/**
 * No limit pagination value
 *
 * @var string
 */
	const NO_LIMIT_PAGINATION = 9999999;

/**
 * Posible Status
 *
 * @var string
 */
	const STATUS_COMPLETED = 'COMP';
	const STATUS_PENDING = 'PEND';
	const STATUS_PAID = 'PAID';
	const STATUS_INVOICED = 'INV';
	const STATUS_DELETED = 'DEL';
	const STATUS_REJECTED = 'REJEC';

	const GRP_PREFIX = "G-";

/**
 * Posible Underwriting application quantity types
 * Used to indicate whether application is "New App" or "Additional Location App"
 *
 * @var string
 */
	const NEW_APP = 'NEW_APP';
	const ADDTL_LOC = 'ADDTL_APP';

/**
 * Constructor
 *
 * @param ComponentCollection $collection Collection
 */
	public function __construct(ComponentCollection $collection) {
		$this->controller = $collection->getController();
	}

/**
 * Return the status list
 *
 * @return array
 */
	public static function getAppQuantityTypeList() {
		return [
			self::NEW_APP => __('New App'),
			self::ADDTL_LOC => __('Additional Location App'),
		];
	}

/**
 * Return the status list
 *
 * @return array
 */
	public static function getStatusList() {
		return [
			self::STATUS_COMPLETED => __('Completed'),
			self::STATUS_PENDING => __('Pending'),
			self::STATUS_PAID => __('Paid'),
			self::STATUS_INVOICED => __('Invoiced'),
			self::STATUS_DELETED => __('Deleted'),
			self::STATUS_REJECTED => __('Rejected'),
		];
	}

/**
 * Return the label of a status
 *
 * @param string $status Status to be displayed
 * @return array
 */
	public static function getStatusLabel($status) {
		$statusList = self::getStatusList();
		return Hash::get($statusList, $status);
	}

/**
 * getDatePartOptns method
 *
 * @param string $arg Accepts the arguments listed below;
 * @param integer $minYear an optional four digit year to set as the start year;
 * @throws InvalidArgumentException
 * d -> Returns array with two digit dates
 * M-> Returns array with Full months names
 * m -> Returns array with three letter abreviation months
 * y -> Returns array with four digit years starting from 1999 to current + 3 years *
 *
 * @return array $OPTNS
 */
	public function getDatePartOptns($arg = null, $minYear = 0) {
		switch ($arg) {
			case 'd':
				$OPTNS = ["" => "--", '01' => '01', '02' => '02', '03' => '03', '04' => '04',
					'05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10',
					'11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16',
					'17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22',
					'23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28',
					'29' => '29', '30' => '30', '31' => '31'
				];
				break;
			case 'm':
				$OPTNS = ["" => "--", "1" => "Jan", "2" => "Feb", "3" => "Mar", "4" => "Apr",
					"5" => "May", "6" => "Jun", "7" => "Jul", "8" => "Aug", "9" => "Sep", "10" => "Oct",
					"11" => "Nov", "12" => "Dec"
				];
				break;
			case 'M':
				$OPTNS = ["" => "--", "1" => "January", "2" => "February", "3" => "March",
					"4" => "April", "5" => "May", "6" => "June", "7" => "July", "8" => "August",
					"9" => "September", "10" => "October", "11" => "November", "12" => "December"
				];
				break;
			case 'y':
				$OPTNS[''] = '--';
				if (empty($minYear)) {
					$minYear = 2001;
				}
				$maxDate = date("Y") + 3;
				for ($i = $maxDate; $i > $minYear; $i--) {
					$OPTNS[$i] = $i;
				}
				break;
			default:
				throw new InvalidArgumentException('The given argument ' . $arg . ' is not recognized');
		}

		return $OPTNS;
	}

/**
 * Function Uses NoteTypes Model retrieves note_type and note_type_description
 *
 * @return array ( note_type => note_type_description);
 *
 */
	public function getNoteTypesOptns() {
		/* Initialize the NoteType model */
		$noteTypeModel = ClassRegistry::init('NoteType'); //$this->loadModel('..') cannot be used from withing a component

		$noteTypesData = array_merge(["All" => "All"], $noteTypeModel->find('list', [
			'recursive' => -1,
			'fields' => ['id', 'note_type_description']
		]));

		return $noteTypesData;
	}

/**
 *  formatDateFormField method
 *
 * @param array $dateField containing 3 elements month day and year as its values is that specific order,
 * 						which is the same order standard cakephp date forms fields generate date arrays.
 * @throws InvalidArgumentException
 *
 * @return string the date formatted yyyy-mm-dd
 */
	public function formatDateFormField($dateField = array()) {
		if (empty($dateField) || count($dateField) < 3) {
			throw new InvalidArgumentException('formatDateFormField function expects array argument containing 3 elements month day and year as its values in that order.');
		}

		$values = array_values($dateField);
		$date = date_create($values[2] . '-' . $values[0] . '-' . $values[1]);
		$date = date_format($date, 'Y-m-d');

		return $date;
	}

/**
 * getCommissionMonthsOptions method
 *
 * Uses full month names array of all months which is used to build return array
 *
 * @return array containing m:year as keys and Month Year as values i.e: 1:2015=>January 2015
 */
	public function getCommissionMonthsOptions() {
		$monthsArray = $this->getDatePartOptns('M');

		$m = 1;
		$y = 2004;
		$monthsTillNow = ((date('Y') + 2) - $y) * 12;

		$result = [];
		for ($x = 1; $x <= $monthsTillNow; $x++) {
			if ($m > 12) {
				$m = 1;
				$y += 1;
			}
			$result[$m . ":" . $y] = $monthsArray[$m] . ' ' . $y;
			$m += 1;
		}
		return $result;
	}

/**
 * getUSAStatesOptions method
 *
 * Associative array of state abbreviations and US state names
 *
 * @return array
 */
	public function getUSAStates() {
		$states = [
			'AL' => "Alabama", 'AK' => "Alaska", 'AZ' => "Arizona", 'AR' => "Arkansas",
			'CA' => "California", 'CO' => "Colorado", 'CT' => "Connecticut", 'DE' => "Delaware",
			'DC' => "District Of Columbia", 'FL' => "Florida",
			'GA' => "Georgia", 'HI' => "Hawaii", 'ID' => "Idaho", 'IL' => "Illinois", 'IN' => "Indiana",
			'IA' => "Iowa", 'KS' => "Kansas", 'KY' => "Kentucky", 'LA' => "Louisiana", 'ME' => "Maine",
			'MD' => "Maryland",
			'MA' => "Massachusetts", 'MI' => "Michigan", 'MN' => "Minnesota", 'MS' => "Mississippi",
			'MO' => "Missouri", 'MT' => "Montana", 'NE' => "Nebraska", 'NV' => "Nevada", 'NH' => "New Hampshire",
			'NJ' => "New Jersey",
			'NM' => "New Mexico", 'NY' => "New York", 'NC' => "North Carolina", 'ND' => "North Dakota",
			'OH' => "Ohio", 'OK' => "Oklahoma", 'OR' => "Oregon", 'PA' => "Pennsylvania",
			'RI' => "Rhode Island", 'SC' => "South Carolina",
			'SD' => "South Dakota", 'TN' => "Tennessee", 'TX' => "Texas", 'UT' => "Utah",
			'VT' => "Vermont", 'VA' => "Virginia", 'WA' => "Washington", 'WV' => "West Virginia",
			'WI' => "Wisconsin", 'WY' => "Wyoming"
		];
		return $states;
	}

/**
 * setRequesDataNoteData method
 *
 * Builds an array containing data to be used for $this->request->data from the controller which could be used in a form to create a completed/approved note
 *
 * @param string $id Merchant.id
 * @param string $noteDescription note Type description
 * @param string $noteTitle title for the note
 * @param string $generalStatus Status of the note
 *
 * @return array
 */
	public function setRequesDataNoteData($id, $noteDescription, $noteTitle = null, $generalStatus = MerchantNote::STATUS_COMPLETE) {
		$noteType = ClassRegistry::init('NoteType');
		return array(
			array(
				'note_type_id' => $noteType->findByNoteTypeDescription($noteDescription),
				'user_id' => CakeSession::read('Auth.User.id'),
				'merchant_id' => $id,
				'note_date' => date("Y-m-d"),
				'note_title' => $noteTitle,
				'general_status' => $generalStatus
			)
		);
	}

/**
 * buildSelectRepOptns method
 *
 * Builds the list of options to be used in the rep dropdown filter.
 * <br />The options are filtered based on RBAC roles and current user associations.
 * <br />Requires app_config.php for role name definitions.
 *
 * @return array options for Rep Dropdown filter
 */
	public function buildRepFilterOptns() {
		$User = ClassRegistry::init('User');
		$curUserId = CakeSession::read('Auth.User.id');
		$curUser = $User->find('first', array('recursive' => -1, 'contain' => array('Role' => array(
					'fields' => 'Role.name')), 'conditions' => array('User.id' => $curUserId)));
		//The assumption is that current user needs a role in order to do anything
		$curUserRoles = Hash::extract(Hash::get($curUser, 'Role', []), '{n}.name');

		$curRoleName = Hash::get($curUser, 'Role.0.name', '');
		$ddOptns = array();
		//Get current list of configured role names
		$rolesConfig = $User->userRoles['roles'];
		$srchOptions = array('recursive' => -1,
			'conditions' => array(/* vary depending on user role */),
			'contain' => array(
				'User' => array('fields' => array('id', 'fullname')),
			),
			'order' => array('User.user_first_name' => 'asc'),
		);
		if ((bool)array_intersect($curUserRoles, array_merge($rolesConfig['Rep']['roles'], $rolesConfig['PartnerRep']['roles']))) {
			//reps can only see themselves
			return array($curUserId => '¤ ' . $curUser['User']['fullname']);
		} elseif ((bool)array_intersect($curUserRoles, array_merge($rolesConfig['SalesManager']['roles'], $rolesConfig['Installer']['roles']))) {
			//Managers can see themselves and whom they manage. Likewise, Installers see whome they are installers for.
			//Get all associated users
			$srchOptions['conditions']['AssociatedUser.associated_user_id'] = $curUserId;
			$assocUsers = $User->AssociatedUser->find('all', $srchOptions);

			$ddOptns[self::GRP_PREFIX . $curUserId] = " -" . $curUser['User']['fullname'] . ' Group';
			$ddOptns[$curUserId] = str_repeat('-', 4) . ' ' . $curUser['User']['fullname'];
			foreach ($assocUsers as $usr) {
				$ddOptns[$usr['User']['id']] = str_repeat('-', 4) . ' ' . $usr['User']['fullname'];
			}
			return $ddOptns;
		} elseif ((bool)array_intersect($curUserRoles, $rolesConfig['Admin']['roles'])) {
			//Admins see all
			//First get all managers
			$managers = $User->getByRole(Hash::get($rolesConfig, 'SalesManager.label'));

			//Get all associated users (associated with managers)
			$srchOptions['conditions']['AssociatedUser.associated_user_id'] = array_keys($managers);
			$assocUsers = $User->AssociatedUser->find('all', $srchOptions);

			//Now Find a list all active users except those already associated
			$assocUserIds = Hash::extract($assocUsers, '{n}.User.id');
			$unassocUsers = $User->find('list', array('recursive' => -1, 'fields' => array(
					'id', 'fullname'),
				'conditions' => array('active' => 1, 'User.id !=' => $assocUserIds)));

			//format unassociated users values
			array_walk($unassocUsers, function (&$userName) {
				$userName = str_repeat('-', 4) . ' ' . $userName;
			});

			foreach ($managers as $mgrId => $mgrName) {
				$ddOptns[self::GRP_PREFIX . $mgrId] = "--" . " " . trim($mgrName) . ' Group';
				$ddOptns[$mgrId] = str_repeat('-', 4) . ' ' . $mgrName;
				foreach ($assocUsers as $usr) {
					if ($usr['AssociatedUser']['associated_user_id'] === $mgrId) {
						$ddOptns[$usr['User']['id']] = str_repeat('-', 4) . ' ' . $usr['User']['fullname'];
					}
				}
			}
			//Puting all together
			$unassocUsers = ["G-0" => "--" . " " . "Unassociated Users Group"] + $unassocUsers;
			$ddOptns += $unassocUsers;
			return $ddOptns;
		}
		return false;
	}

/**
 *	checkNoteBtnClicked
 *	Checks the submitted form data and returns the button that was pressed to save the form.
 * 	To be used only with forms that have Change Request and Pending/Approved notes implemented
 *
 *	@param array $formRequestData submitted view form request data from a form with change request functionality
 *	@return string from MerchantChange edit type
 */
	public function checkNoteBtnClicked($formRequestData) {
		// Check the submit button used
		$editType = MerchantChange::EDIT_PENDING;
		if (!empty($formRequestData[MerchantChange::EDIT_APPROVED])) {
			$editType = MerchantChange::EDIT_APPROVED;
		} elseif (!empty($formRequestData[MerchantChange::EDIT_LOG])) {
			$editType = MerchantChange::EDIT_LOG;
		}
		return $editType;
	}

/**
 * Set an action to export data to csv using a view file instead of serialize the data
 *
 * @param array $searchParams Search params
 * @return void
 */
	public function setCsvView($searchParams = []) {
		$this->controller->set('appliedFilters', $this->formatSearchParams($searchParams));
		$this->controller->set('output', self::OUTPUT_CSV);

		$this->controller->viewClass = 'CsvView.Csv';
	}

/**
 * setDecimalsNoRounding 
 * Changes the number of decimals in a float/double to the supplied precicion without rounding
 * Example for two decimals: 0.01 * (int)($value*100) 
 * 
 * @param float $number a numbe with arbitrary number of decimals
 * @param int $precision defaults to 2 decimals
 * @return float
 * @throws InvalidArgumentException
 */
	public static function setDecimalsNoRounding($number, $precision = 2) {
		if ($precision < 0 || !is_numeric($precision)) {
			throw new InvalidArgumentException('Invalid argument supplied for precision!');
		}

		$decimalToRight = '1' . str_repeat('0', $precision);
		if ($precision == 0) {
			$decimalToLeft = '1.0';
		} elseif ($precision > 0) {
			$decimalToLeft = '0.'. str_repeat('0', $precision - 1) . '1';
		}
		
		return  $decimalToLeft * (int)($number*$decimalToRight);
	}

/**
 * Format search parameters to user friendly values to display in csv files
 *
 * @param array $searchParams Search parameters
 * @return array
 */
	public function formatSearchParams($searchParams) {
		$UserModel = ClassRegistry::init('User');

		$formatedData = [];
		if (!empty($searchParams['user_id'])) {
			$formatedData['user_id'] = $UserModel->getUserNameByComplexId($searchParams['user_id']);
		}
		if (!empty($searchParams['merchant_dba'])) {
			$formatedData['merchant_dba'] = $searchParams['merchant_dba'];
		}
		if (!empty($searchParams['from_date'])) {
			$year = Hash::get($searchParams, 'from_date.year');
			$month = Hash::get($searchParams, 'from_date.month');
			$formatedData['from_date'] = (new DateTime())->setdate($year, $month, 1)->format('Y-m-d');
		}
		if (!isset($searchParams['res_months']) && !empty($searchParams['end_date'])) {
			$year = Hash::get($searchParams, 'end_date.year');
			$month = Hash::get($searchParams, 'end_date.month');
			$formatedData['end_date'] = (new DateTime())->setdate($year, $month, 1)->format('Y-m-t');
		} elseif (isset($searchParams['res_months'])) {
			$formatedData['res_months'] = "{$searchParams['res_months']} month(s)" ;
		}

		return $formatedData;
	}
}
