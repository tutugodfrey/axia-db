<?php

App::uses('AppModel', 'Model');
App::uses('NoteType', 'Model');
App::uses('AddressType', 'Model');
App::uses('TimelineItem', 'Model');
App::uses('Folder', 'Utility');
/**
 * Womply Model
 *
 */
class Womply extends AppModel {

/**
 * Use Table
 *
 * @var bool $useTable
 * @access public
 */
	public $useTable = false;

/**
 * WOMPLY_ID
 *
 * @var string $queryFiedls
 * @access public
 */
	const WOMPLY_ID = '0063';

/**
 * WOMPLY_CREATION
 *
 * @var string WOMPLY_CREATION
 * @access public
 */
	const WOMPLY_CREATION = 'A';

/**
 * WOMPLY_UPDATE
 *
 * @var string WOMPLY_UPDATE
 * @access public
 */
	const WOMPLY_UPDATE = 'U';
/**
 * WOMPLY_DEL
 *
 * @var string WOMPLY_DEL
 * @access public
 */
	const WOMPLY_DEL = 'D';

/**
 * QUERY_FIELDS
 *
 * @var string $queryFiedls
 * @access public
 */
	const QUERY_FIELDS = "
						m.id,
						m.merchant_mid,
						m.merchant_dba,
						m.merchant_ownership_type,
						m.merchant_mail_contact,
						te.timeline_date_completed,
						u.user_first_name || ' ' || u.user_last_name AS rep_name,
						ac.address_title AS corp_address_title,
						ac.address_street AS corp_address_street,
						ac.address_city AS corp_address_city,
						ac.address_state AS corp_address_state,
						ac.address_zip AS corp_address_zip,
						ac.address_phone AS corp_address_phone,
						am.address_title AS mail_address_title,
						am.address_street AS mail_address_street,
						am.address_city AS mail_address_city,
						am.address_state AS mail_address_state,
						am.address_zip AS mail_address_zip,
						am.address_phone AS mail_address_phone,
						ab.address_street AS site_street,
						ab.address_city AS site_city,
						ab.address_state AS site_state,
						ab.address_zip AS site_zip,
						ab.address_phone AS site_phone,
						m.merchant_contact,
						m.merchant_tin,
						m.merchant_sic,
						m.merchant_email,
						ma.acquirer,
						mb.bin,
						mba.bank_routing_number,
						mba.bank_dda_number,
						r.user_first_name || ' ' || r.user_last_name AS ref_name,
						res.user_first_name || ' ' || res.user_last_name AS res_name,
						n.network_description,
						u.womply_active,";

/**
 * getCommonJoins
 *
 * @return string
 * @access public
 */
	public function setCommonJoins() {
		return "LEFT JOIN public.merchant_acquirers ma ON m.merchant_acquirer_id = ma.id
			LEFT JOIN public.merchant_bins mb ON m.merchant_bin_id = mb.id
			LEFT JOIN public.users r ON m.referer_id = r.id
			LEFT JOIN public.users res ON m.reseller_id = res.id
			LEFT JOIN public.addresses ac ON m.id = ac.merchant_id AND ac.address_type_id = '" . AddressType::CORP_ADDRESS . "'
			LEFT JOIN public.addresses am ON m.id = am.merchant_id AND am.address_type_id = '" . AddressType::MAIL_ADDRESS . "'
			LEFT JOIN public.addresses ab ON m.id = ab.merchant_id AND ab.address_type_id = '" . AddressType::BUSINESS_ADDRESS . "'
			LEFT JOIN public.merchant_banks mba ON m.id = mba.merchant_id
			LEFT JOIN public.timeline_entries te ON m.id = te.merchant_id AND te.timeline_item_id = '" . TimelineItem::GO_LIVE_DATE . "'
			LEFT JOIN public.networks n ON m.network_id = n.id";
	}

/**
 * buildQueryStr
 *
 * @param string $caseFieldSql containing a CASE clause which sets a conditional field
 * @param string $specificSqlPart ending part of the SQL query code specific to the type of womply data being retrieved
 * @access public
 * @return array
 */
	public function buildQueryStr($caseFieldSql, $specificSqlPart) {
		$query = 'SELECT ' . self::QUERY_FIELDS;
		$query .= ' ' . $caseFieldSql;
		$query .= ' FROM public.merchants m';
		$query .= ' ' . $this->setCommonJoins();
		$query .= ' ' . $specificSqlPart;
		return $query;
	}

/**
 * getCancellations
 *
 * @access public
 * @return array
 */
	public function getCancellations() {
		$caseField = 'CASE WHEN m.merchant_mid IS NOT NULL THEN \'DELETED\' ELSE NULL END AS "case"';
		$cancellationsSql = 'JOIN public.users u ON m.user_id = u.id AND NOT (u.id IN ( SELECT users.id FROM public.users WHERE users.womply_user_enabled = false))
			WHERE (m.id IN ( SELECT deleted.merchant_id
			FROM ( SELECT DISTINCT ON (wml.merchant_id) wml.merchant_id, wa.action, wml.created, mc.status AS delete,
						CASE
							WHEN wa.action = \'CREATED\' THEN \'delete\'
							WHEN wa.action = \'UPDATED\' THEN \'delete\'
							WHEN wa.action = \'DELETED\' THEN \'ignore\'
							WHEN wa.action = \'RECREATED\' THEN \'delete\'
							ELSE NULL
						END AS "case"
					FROM public.womply_merchant_logs wml
					LEFT JOIN public.womply_actions wa ON wml.womply_action_id = wa.id
					JOIN public.merchant_cancellations mc ON wml.merchant_id = mc.merchant_id AND mc.status = \'COMP\'
				ORDER BY wml.merchant_id, wml.created DESC) deleted
			WHERE deleted."case" <> \'ignore\'))
		ORDER BY m.merchant_mid;';
		$queryStr = $this->buildQueryStr($caseField, $cancellationsSql);
		$result = $this->query($queryStr);
		return Hash::extract($result, '{n}.{n}');
	}

/**
 * getReactivations
 *
 * @access public
 * @return array
 */
	public function getReactivations() {
		$caseField = 'CASE WHEN m.merchant_mid IS NOT NULL THEN \'RECREATED\' ELSE NULL END AS "case"';
		$reactivationsSql = 'JOIN public.users u ON m.user_id = u.id AND NOT (u.id IN ( SELECT users.id FROM public.users WHERE users.womply_user_enabled = false))
				LEFT JOIN public.merchant_cancellations mc ON m.id = mc.merchant_id AND mc.status = \'COMP\'
				WHERE (m.id IN ( SELECT reactivated.merchant_id
						FROM ( SELECT DISTINCT ON (wml.merchant_id) wml.merchant_id, wa.action, wml.created,
								CASE
									WHEN wa.action = \'CREATED\' THEN \'ignore\'
									WHEN wa.action = \'UPDATED\' THEN \'ignore\'
									WHEN wa.action = \'DELETED\' THEN \'reactivate\'
									WHEN wa.action = \'RECREATED\' THEN \'ignore\'
									ELSE NULL
								END AS "case"
							FROM public.womply_merchant_logs wml
							LEFT JOIN public.womply_actions wa ON wml.womply_action_id = wa.id
							LEFT JOIN public.merchant_notes mn ON wml.merchant_id = mn.merchant_id AND mn.note_title ~~* \'%reactivated%\'
							ORDER BY wml.merchant_id, wml.created DESC) reactivated
					WHERE reactivated."case" = \'reactivate\')) AND mc.merchant_id IS NULL AND m.womply_merchant_enabled = true
			ORDER BY m.merchant_mid;';
		$queryStr = $this->buildQueryStr($caseField, $reactivationsSql);
		$result = $this->query($queryStr);
		return Hash::extract($result, '{n}.{n}');
	}

/**
 * getAdditions
 *
 * @access public
 * @return array
 */
	public function getAdditions() {
		$caseField = 'CASE WHEN m.merchant_mid IS NOT NULL THEN \'CREATED\' ELSE NULL END AS "case"';
		$additionsSql = 'JOIN public.users u ON m.user_id = u.id AND NOT (u.id IN ( SELECT users.id FROM public.users WHERE users.womply_user_enabled = false))
						LEFT JOIN public.merchant_cancellations mc ON m.id = mc.merchant_id AND mc.status = \'COMP\'
						LEFT JOIN public.womply_merchant_logs wml ON m.id = wml.merchant_id
						WHERE m.active = 1 AND mc.merchant_id IS NULL AND wml.merchant_id IS NULL AND m.womply_merchant_enabled = true AND m.merchant_acquirer_id = (SELECT id from public.merchant_acquirers where acquirer=\'Axia\')
						ORDER BY m.merchant_mid;';
		$queryStr = $this->buildQueryStr($caseField, $additionsSql);
		$result = $this->query($queryStr);
		return Hash::extract($result, '{n}.{n}');
	}

/**
 * getRemovals
 *
 * @access public
 * @return array
 */
	public function getRemovals() {
		$caseField = 'CASE WHEN m.merchant_mid IS NOT NULL THEN \'DELETED\' ELSE NULL END AS "case"';
		$removalsSql = 'JOIN public.users u ON m.user_id = u.id 
				WHERE (m.id IN ( SELECT deleted.merchant_id FROM ( SELECT DISTINCT ON (wml.merchant_id) wml.merchant_id, wa.action, wml.created,
						CASE
							WHEN wa.action = \'CREATED\' THEN \'delete\'
							WHEN wa.action = \'UPDATED\' THEN \'delete\'
							WHEN wa.action = \'DELETED\' THEN \'ignore\'
							WHEN wa.action = \'RECREATED\' THEN \'delete\'
							ELSE NULL
						END AS "case"
				FROM public.womply_merchant_logs wml
					LEFT JOIN public.womply_actions wa ON wml.womply_action_id = wa.id
					JOIN public.merchants m_1 ON wml.merchant_id = m_1.id AND m_1.womply_merchant_enabled = false
					ORDER BY wml.merchant_id, wml.created DESC) deleted
				WHERE deleted."case" <> \'ignore\'))
			ORDER BY m.merchant_mid;';
		$queryStr = $this->buildQueryStr($caseField, $removalsSql);
		$result = $this->query($queryStr);
		return Hash::extract($result, '{n}.{n}');
	}

/**
 * getUpdates
 *
 * @access public
 * @return array
 */
	public function getUpdates() {
		$caseField = 'CASE WHEN m.merchant_mid IS NOT NULL THEN \'UPDATED\' ELSE NULL END AS "case"';
		$updatesSql = 'JOIN public.users u ON m.user_id = u.id AND NOT (u.id IN ( SELECT users.id FROM public.users WHERE users.womply_user_enabled = false))
			LEFT JOIN public.merchant_cancellations mc ON m.id = mc.merchant_id AND mc.status = \'COMP\'
			WHERE (m.id IN ( SELECT updateable.merchant_id
			FROM ( SELECT DISTINCT ON (wml.merchant_id) wml.merchant_id, wa.action, wml.created, mcn.note_date,
						CASE
							WHEN wa.action = \'CREATED\' THEN \'update\'
							WHEN wa.action = \'UPDATED\' THEN \'update\'
							WHEN wa.action = \'DELETED\' THEN \'ignore\'
							WHEN wa.action = \'RECREATED\' THEN \'update\'
							ELSE NULL
						END AS "case"
					FROM public.womply_merchant_logs wml
						LEFT JOIN public.womply_actions wa ON wml.womply_action_id = wa.id
						LEFT JOIN public.merchant_notes mcn ON wml.merchant_id = mcn.merchant_id AND mcn.general_status = \'COMP\' AND mcn.note_type_id = \'' . NoteType::CHANGE_REQUEST_ID . '\'
					ORDER BY wml.merchant_id, wml.created DESC) updateable
				WHERE updateable."case" <> \'ignore\' AND updateable.note_date > updateable.created)) AND m.active = 1 AND mc.merchant_id IS NULL AND m.merchant_acquirer_id = (SELECT id from public.merchant_acquirers where acquirer=\'Axia\') AND m.womply_merchant_enabled = true
			ORDER BY m.merchant_mid;';
		$queryStr = $this->buildQueryStr($caseField, $updatesSql);
		$result = $this->query($queryStr);
		return Hash::extract($result, '{n}.{n}');
	}

/**
 * getMarData
 *
 * @access public
 * @return array
 */
	public function getMarDailyData() {
		$additions = $this->getAdditions();
		$cancellations = $this->getCancellations();
		$reactivations = $this->getReactivations();
		$removals = $this->getRemovals();
		$updates = $this->getUpdates();

		//Decrypt any ecrypted data
		$this->_decryptAll($additions, $cancellations, $reactivations, $removals, $updates);

		return array_merge($additions, $cancellations, $reactivations, $removals, $updates);
	}

/**
 * _sanitizeData
 *
 * @param array &$additions reference to data built by self::getAdditions() or related functions
 * @param array &$cancellations reference to data built by self::getCancellations() or related functions
 * @param array &$reactivations reference to data built by self::getReactivations() or related functions
 * @param array &$removals reference to data built by self::getRemovals() or related functions
 * @param array &$updates reference to data built by self::getUpdates() or related functions
 * @return void
 */
	protected function _decryptAll(&$additions, &$cancellations, &$reactivations, &$removals, &$updates) {
		if (!empty($additions)) {
			$additions = array_map(array('self', 'decryptFields'), $additions);
		} else {
			$additions = [];
		}
		if (!empty($cancellations)) {
			$cancellations = array_map(array('self', 'decryptFields'), $cancellations);
		} else {
			$cancellations = [];
		}
		if (!empty($reactivations)) {
			$reactivations = array_map(array('self', 'decryptFields'), $reactivations);
		} else {
			$reactivations = [];
		}
		if (!empty($removals)) {
			$removals = array_map(array('self', 'decryptFields'), $removals);
		} else {
			$removals = [];
		}
		if (!empty($updates)) {
			$updates = array_map(array('self', 'decryptFields'), $updates);
		} else {
			$updates = [];
		}
	}

/**
 * setMarContent
 * Fields in $data should not be encrypted
 * 
 * Required Womply Fields:
 * SettlementMID, AuthorizationMID, Chain (EIN), Action, MIDOpenDate, DBAName,
 * DBAAddressLine1, DBAAddressLine2, DBACity, DBAState, DBACountry, 
 * DBAPostalCode, DBAAttention, DBAPhone1, DBAPhone2, DBAEmail,
 * OwnerFirstName1, OwnerLastName1, OwnerFullName1, OwnerFirstName2, 
 * OwnerLastName2, OwnerFullName2, BillingCompanyName, BillingAddressLine1,
 * BillingAddressLine2, BillingCity, BillingState, BillingCountryCode, 
 * BillingPostalCode, BillingAttentionFirstName, BillingAttentionLastName,
 * BillingAttentionFullName, BillingPhone, BillingEmail, MCC, MerchantSource,
 * MerchantSalesAgent, ACHRoutingNumber, ACHAccountNumber,MerchantCallStatus
 *
 * @param array $data data built by self::getMarDailyData() or related functions(fields should not be encrypted)
 * @param array $actionOverride if set MAR action will be set to the value overrrides the action 
 * @return void
 */
	public function setMarContent($data, $actionOverride = '') {
		$MerchantOwner = ClassRegistry::init('MerchantOwner');
		$WomplyAction = ClassRegistry::init('WomplyAction');
		$WomplyMerchantLog = ClassRegistry::init('WomplyMerchantLog');
		//Initialize Header
		$csvContent = "H|8|" . self::WOMPLY_ID . "\n";
		foreach ($data as $arr) {
			if (!empty($actionOverride)) {
				$action = $actionOverride;
			} else {
				switch ($arr['case']) {
					case 'CREATED':
						$action = self::WOMPLY_CREATION;
						break;
					case 'DELETED':
						$action = self::WOMPLY_DEL;
						break;
					case 'RECREATED':
						$action = self::WOMPLY_CREATION;
						break;
					case 'UPDATED':
						$action = self::WOMPLY_UPDATE;
						break;
				}
			}
			//grab multiple owners if they exist
			$owners = $MerchantOwner->find('all', [
				'conditions' => ['MerchantOwner.merchant_id' => $arr['id']],
				'order' => 'MerchantOwner.owner_equity DESC',
				'limit' => 2
			]);

			//build the csv
			//RowType
			$str = "B|";
			//SettlementMID
			$str .= substr_replace($arr['merchant_mid'], '7641', 0, 4) . "|";
			//AuthorizationMID
			$str .= substr_replace($arr['merchant_mid'], '7641', 0, 4) . "|";
			//Chain (EIN)
			$str .= $arr['merchant_tin'] . "|";
			//Action
			$str .= "$action|";
			//MIDOpenDate
			$str .= str_replace("-", "", $arr['timeline_date_completed']) . "|";
			//DBAName
			$str .= str_replace(",", "", $arr['merchant_dba']) . "|";
			//DBAAddressLine1
			$str .= $arr['site_street'] . "|";
			//DBAAddressLine2
			$str .= "|";
			//DBACity
			$str .= $arr['site_city'] . "|";
			//DBAState
			$str .= $arr['site_state'] . "|";
			//DBACountry
			$str .= "US|";
			//DBAPostalCode
			$str .= substr($arr['site_zip'], 0, 6) . "|"; //USA zip len=5/CANADA zip len=6
			//DBAAttention
			$str .= $arr['merchant_contact'] . "|";
			//DBAPhone1
			$str .= preg_replace("/\D/", "", $arr['site_phone']) . "|";
			//DBAPhone2
			$str .= "|";
			//DBAEmail
			$str .= $arr['merchant_email'] . "|";
			//OwnerFirstName1
			$str .= "|";
			//OwnerLastName1
			$str .= "|";
			//OwnerFullName1
			$str .= Hash::get($owners, '0.MerchantOwner.owner_name') . "|";
			//OwnerFirstName2
			$str .= "|";
			//OwnerLastName2
			$str .= "|";
			//OwnerFullName2
			$str .= Hash::get($owners, '1.MerchantOwner.owner_name') . "|";
			// BillingCompanyName
			$str .= $arr['corp_address_title'] . "|";
			// BillingAddressLine1
			$str .= $arr['corp_address_street'] . "|";
			//BillingAddressLine2
			$str .= "|";
			//BillingCity
			$str .= $arr['corp_address_city'] . "|";
			//BillingState
			$str .= $arr['corp_address_state'] . "|";
			//BillingCountryCode
			$str .= "US|";
			//BillingPostalCode
			$str .= substr($arr['corp_address_zip'], 0, 6) . "|"; //USA zip len=5/CANADA zip len=6
			//BillingAttentionFirstName
			$str .= "|";
			//BillingAttentionLastName
			$str .= "|";
			//BillingAttentionFullName
			$str .= $arr['merchant_mail_contact'] . "|";
			//BillingPhone
			$str .= preg_replace("/\D/", "", $arr['corp_address_phone']) . "|";
			//BillingEmail
			$str .= "|";
			// MCC
			$str .= $arr['merchant_sic'] . "|";
			// MerchantSource
			if ($arr['ref_name']) {
				$str .= $arr['ref_name'] . "|";
			} elseif ($arr['res_name']) {
				$str .= $arr['res_name'] . "|";
			} else {
				$str .= "Direct Sale" . "|";
			}
			//MerchantSalesAgent
			$str .= ($arr['womply_active'] == true ? $arr['rep_name'] . "|" : "|");
			// ACHRoutingNumber
			$str .= $arr['bank_routing_number'] . "|";
			// ACHAccountNumber
			$str .= $arr['bank_dda_number'] . "|";
			//MerchantCallStatus
			$str .= "CALL";
			$str .= "\n";
			$csvContent .= $str;

			$womplyLog[] = array(
				'merchant_id' => $arr['id'],
				'womply_action_id' => $WomplyAction->field('id', ['action' => $arr['case']]),
				'details' => json_encode($arr)
			);
		}
		if (isset($womplyLog)) {
			$WomplyMerchantLog->saveMany($womplyLog);
		}
		$recordCount = count($data);
		$csvContent .= "F|$recordCount";
		return $csvContent;
	}

/**
 * saveMarGzipFile
 *
 * @param string $fileContent contents of the MAR
 * @param string $fileName name of the MAR file
 * @return void
 * @throws Exception
 */
	public function saveMarGzipFile($fileContent, $fileName) {
		$savePath = $this->_setLocalMarFilePath($fileName);
		$fp = @fopen($savePath, 'w');

		try {
			if (!$fp) {
				throw new Exception("Could not create local file $savePath.");
			}
			if (@fwrite($fp, $fileContent) === false) {
				throw new Exception("Could not write to local file: $savePath.");
			}
		} catch (Exception $e) {
			error_log('Exception: ' . $e->getMessage());
		}

		fclose($fp);

		$gzOut = $savePath . ".gz";
		file_put_contents("compress.zlib://$gzOut", file_get_contents($savePath));
	}

/**
 * _getMarFileName
 *
 * @return string
 */
	protected function _getMarFileName() {
		return "merchant_action." . self::WOMPLY_ID . date('.Y.m.d.H.i.s.' . substr(date('u'), 0, 3));
	}

/**
 * _setLocalMarFilePath
 *
 * @param string $fileName MAR file name
 * @return string the local directory path where the MAR file is
 */
	protected function _setLocalMarFilePath($fileName = '') {
		$folder = APP . "tmp/";
		$path = $folder . $fileName;
		return $path;
	}

/**
 * __getSSH2_SFTPResource
 * Creates an SFTP conection resource
 *
 * @return SSH2 SFTP resource for use with all other ssh2_sftp_*() methods and the ssh2.sftp:// fopen wrapper. 
 * @throws Exception if public Key Authentication fails
 */
	private function __getSSH2_SFTPResource() {
		$connection = ssh2_connect('sftp.womply.com', 22, array('hostkey' => 'ssh-rsa'));
		$dir = Configure::read('WomplySSHKey.path');

		if (!$connection) {
			throw new Exception('Connection failed');
		}

		if (!ssh2_auth_pubkey_file($connection, 'client-' . self::WOMPLY_ID, $dir . DS . 'womply.pub', $dir . DS . 'womply')) {
			throw new Exception('Public Key Authentication Failed' . 'client-' . self::WOMPLY_ID . ' - AND -' . $dir . DS . 'womply.pub' . '- AND -' . $dir . DS . 'womply');
		}

		$sftp = ssh2_sftp($connection);
		return $sftp;
	}

/**
 * sendDailyMar
 *
 * @return boolean
 * @throws Exception
 */
	public function sendDailyMAR() {
		//Get data
		$data = $this->getMarDailyData();

		//Set it as Pipe Delimited data
		$psvData = $this->setMarContent($data);

		//get file name
		$marFileName = $this->_getMarFileName();

		//save it locally
		$this->saveMarGzipFile($psvData, $marFileName);

		//get SSH2 SFTP resource
		$sftp = $this->__getSSH2_SFTPResource();

		if (Configure::read('debug') === 0) {
			$sftpPath = '/to_womply/merchant_action_report/';
		} elseif (Configure::read('debug') >= 1) {
			$sftpPath = '/to_womply_test/merchant_action_report/';
		}
		$sftpStream = @fopen('ssh2.sftp://' . intval($sftp) . $sftpPath . $marFileName . '.gz', 'w');
		$gzOut = $this->_setLocalMarFilePath($marFileName) . '.gz';
		try {

			if (!$sftpStream) {
				throw new Exception("Could not open remote file: $sftpPath{$marFileName}.gz");
			}

			$dataToSend = @file_get_contents($gzOut);

			if ($dataToSend === false) {
				throw new Exception("Could not open local file: $gzOut.");
			}

			if (@fwrite($sftpStream, $dataToSend) === false) {
				throw new Exception("Could not send data from file: $gzOut.");
			}

			fclose($sftpStream);
		} catch (Exception $e) {
			error_log('Exception: ' . $e->getMessage());
			fclose($sftpStream);
		}

		// We are done with the Files so delete them
		unlink($this->_setLocalMarFilePath($marFileName));
		unlink($this->_setLocalMarFilePath($marFileName) . ".gz");
		return true;
	}

/**
 * getEmailReadyPARdata
 * Gets all Product Action Report Data (PAR) and calls $this->updateMerchants method which updates merchants based on the retrieved PAR data.
 * Data is returned in an array which can be passed to CakeEmail object and has the following structure:
 * 
 * [
 * 	'body' => <email body: contains errors or other process messages.>
 * 	'attachment' => [
 *			"$fileName.txt" => ['data' => $dataToSend] //this is the full downloaded PAR csv data as string
 *		];
 * 	'email' => <will be set to webmaster if any serious errors occur otherwise Configure::read('App.axia360Email')>
 * ]
 * 
 * @return array with all the contents for the email to send to Configure::read('App.axia360Email')
 * @throws Exception
 */
	public function getEmailReadyPARdata() {
		$emailContent['body'] = "Requests,\n";
		$emailContent['attachment'] = null;
		$localFolder = APP . "tmp/";

		//get SSH2 SFTP resource
		$sftp = $this->__getSSH2_SFTPResource();

		if (Configure::read('debug') === 0) {
			$sftpPath = '/from_womply/product_action_report/';
		} elseif (Configure::read('debug') >= 1) {
			$sftpPath = '/from_womply_test/product_action_report/';
		}

		$dirHandle = opendir('ssh2.sftp://' . intval($sftp) . $sftpPath);

		while (false !== ($file = readdir($dirHandle))) {
			if ($file != '.' && $file != '..' && substr($file, -3) === '.gz') {
				$files[] = $file;
			}
		}
		if (isset($files) && count($files) === 1) {
			$fileName = $files[0];
			$emailContent['body'] .= "This month's file is $fileName \n";

			try {
				// Remote stream
				$sftpRemote = @fopen("ssh2.sftp://" . intval($sftp) . $sftpPath . $fileName, 'r');
				if (!$sftpRemote) {
					throw new Exception("Unable to open remote file: sftp://sftp.womply.com/$sftpPath$fileName \n");
				}

				// Local stream
				$localStream = @fopen($localFolder . $fileName, 'w');
				if (!$localStream) {
					throw new Exception("Unable to open local file for writing: " . $localFolder . $fileName . "\n");
				}

				// Write from our remote stream to our local stream
				$read = 0;
				$fileStats = fstat($sftpRemote);
				$fileSize = $fileStats['size'];
				while ($read < $fileSize && ($buffer = fread($sftpRemote, $fileSize - $read))) {
					// Increase our bytes read
					$read += strlen($buffer);

					// Write to our local file
					if (fwrite($localStream, $buffer) === false) {
						throw new Exception("Unable to write to local file: " . $localFolder . $fileName . "\n");
					} else {
						$emailContent['body'] .= "We have retrieved $fileName from Womply\n";
					}
				}

				if (ssh2_sftp_rename($sftp, $sftpPath . $fileName, $sftpPath . $fileName . ".downloaded") === false) {
					$emailContent['body'] .= "Unable to rename remote file: $sftpPath$fileName to $sftpPath$fileName.downloaded\n";
					$falure = true;
				} else {
					$emailContent['body'] .= "Successfully renamed remote file \n";
				}

				//Open compresed file
				$gzIn = $localFolder . $fileName;
				$gzPointer = gzopen($gzIn, "r");

				if ($gzPointer === false) {
					throw new Exception("Could could not open file for extraction of file: " . $gzIn);
				}
				//Get uncompressed file size
				$unzippedFileSize = $this->gzfilesize($gzIn);
				//read decompressed file contents into string
				$dataToSend = gzread($gzPointer, $unzippedFileSize);
				gzclose($gzPointer);
				$emailContent['attachment'] = [
					substr($fileName, 0, -3) . ".txt" => ['data' => $dataToSend]
				];

				$result = $this->updateMerchants($gzIn);
				$emailContent['body'] .= $result['result_msg'];

				// Close our streams
				fclose($localStream);
				fclose($sftpRemote);

				// We are done with the Files so delete them
				unlink($gzIn);

			} catch (Exception $e) {
				$emailContent['body'] .= 'Exception: ' . $e->getMessage() . "\n";
				$falure = true;
				// Close our streams
				if (!is_bool($localStream)) {
					fclose($localStream);
				}
				if (!is_bool($sftpRemote)) {
					fclose($sftpRemote);
				}
			}
		} elseif (!isset($files)) {
			$emailContent['body'] .= "No new files were found\n";
		} else {
			$emailContent['body'] .= "Multiple files were located manual intervention is required\n";
			$falure = true;
		}

		if (isset($falure) || Configure::read('debug') >= 1) {
			$emailKey = array_keys(Configure::read('App.defaultSender'));
			$emailContent['email'] = array_pop($emailKey);
		} else {
			$emailContent['email'] = Configure::read('App.axia360Email');
		}

		return $emailContent;
	}

/**
 * updateMerchants
 *
 * @param string $localFilePath the path where the gz compresed PAR file is saved
 * @return array containing ids of merchant
 * @throws Exception
 */
	public function updateMerchants($localFilePath) {
		$Merchant = ClassRegistry::init('Merchant');
		$TimelineEntry = ClassRegistry::init('TimelineEntry');
		$gz = gzopen($localFilePath, "r");
		$i = 0;
		$result['result_msg'] = '';
		$mids = [];
		while (($parLineItems = fgetcsv($gz, 0, "|")) !== false) {
			switch ($parLineItems[0])
			{
				case "H":
					//This is the header row.  Ignore it.
					break;
				case "B":
					if ($parLineItems[4] == 'Add') {
						$mId = $Merchant->field('id', [
							'OR' => [
								"Merchant.merchant_mid like '%" . substr($parLineItems[1], -12) . "'",
								"Merchant.merchant_id_old" => $parLineItems[1] //temporary fix, womply does not have the new axiatech MIDs
							]
						]);
						$hasEntry = $TimelineEntry->hasAny(
							[
								'merchant_id' => $mId,
								'timeline_item_id' => TimelineItem::SOLD_WOMPLY,
								'timeline_date_completed IS NOT NULL'
							]
						);
						if ($hasEntry) {
							//just increase the counter
							$i++;
						} else {
							$data = [
								[
									'Merchant' => [
										'id' => $mId,
										'womply_customer' => true
									],
									'TimelineEntry' => [
										[
											'merchant_id' => $mId,
											'timeline_item_id' => TimelineItem::SOLD_WOMPLY,
											'timeline_date_completed' => date('Y-m-d'),
											'action_flag' => false
										]
									]
								]
							];
							if ($Merchant->saveMany($data, ['deep' => true, 'validate' => false])) {
								$i++;
							}
							$mids[] = $mId;
						}
					}
					break;
				case "F":
					if ($i == $parLineItems[1]) {
						$result['result_msg'] .= $i . ' Merchants Updated' . "\n";
					} else {
						$result['result_msg'] .= "There were $i records updated, but there should have been $parLineItems[1]\n";
						$result['result_msg'] .= "Please have development review this PAR\n";
					}
					break;
			}
		}
		gzclose($gz);
		if (!empty($mids)) {
			$cancelledMerch = $Merchant->find('list', [
				'fields' => ['id', 'merchant_mid'],
				'conditions' => [
					'womply_customer' => true,
					'id NOT IN' => $mids,
					]
				]);

			if (!empty($cancelledMerch)) {
				//mark cancelled merchants are no longer womply customers
				$Merchant->updateAll(['womply_customer' => 'FALSE'], ['id' => array_keys($cancelledMerch)]);
				$tEntries = [];
				foreach ($cancelledMerch as $id => $mid) {
					$hasEntry = $TimelineEntry->hasAny(
						[
							'merchant_id' => $id,
							'timeline_item_id' => TimelineItem::CANCELLED_WOMPLY,
							'timeline_date_completed IS NOT NULL'
						]
					);
					if (!$hasEntry) {
						$tEntries[] = [
							'merchant_id' => $id,
							'timeline_item_id' => TimelineItem::CANCELLED_WOMPLY,
							'timeline_date_completed' => date('Y-m-d'),
							'action_flag' => false
						];
						$result['result_msg'] .= "$mid has cancelled Axia Insights\n";
					}
				}
				if (!$TimelineEntry->saveMany($tEntries)) {
					$result['result_msg'] .= " **Fatal Error: Failed saving Timeline Entries for merchants that Axia Insights!** \n";
				}
			}
		}
		return $result;
	}

/**
 * sendWomplyEmailAlert method
 *
 * Triggers an email event
 * To add contents of a file as attachment intead of passing the path to the file to attach should be done using this array structure:
 *
 *	[
 *		'fileName.ext' => [
 *			'data' => '<file contents as string here>',
 *		]
 *	]
 *
 * @param string $emailBody the message for the email. New line characters "\n" are acceptable and will be properly parsed.
 * @param String $emailSubject email subject
 * @param String $email an email address. If nothing is passed the Configured default email sender will be used as recipient.
 * @param mixed $attachment could be a string with the local path to the file or an array with the structure specified above.
 * @return void
 */
	public function sendWomplyEmailAlert($emailBody, $emailSubject = '', $email = '', $attachment = null) {
		if (empty($emailSubject)) {
			$emailSubject = 'Axia Database - Automated Womply Reporting Process Alert';
		}

		if (empty($email)) {
			$emailKey = array_keys(Configure::read('App.defaultSender'));
			$email = array_pop($emailKey);
		}

		$event = new CakeEvent('App.Model.womplyAlert', $this, [
				'template' => Configure::read('App.defaultTemplate'),
				'to' => $email,
				'subject' => $emailSubject,
				'emailBody' => $emailBody,
				'attachment' => $attachment
			]
		);
		// dispatch event to the local event manager
		$this->getEventManager()->dispatch($event);
	}

/**
 * gzfilesize
 * Returnes the true uncompressed filesize of a gzip file.
 * The filesize is stored as a 32-bit integer in the end of the compressed file. 
 * This function checks to see if it's a real gzip compressed file before doing the work; if not, then just like other gz- functions, 
 * it'll treat it just like a regular file and perform a normal filesize operation
 *
 * @param string $filename the full path and file name
 * @return integer size of the file
 */
	public function gzfilesize($filename) {
		$gzfs = FALSE;
		if (($zp = fopen($filename, 'r'))!==FALSE) {
			if (@fread($zp, 2) == "\x1F\x8B") { // this is a gzip'd file
				fseek($zp, -4, SEEK_END);
				if(strlen($datum = @fread($zp, 4)) == 4)
				extract(unpack('Vgzfs', $datum));
			}
			else {// not a gzip'd file, revert to regular filesize function
				$gzfs = filesize($filename);
			}
			fclose($zp);
		}
		return($gzfs);
	}
}
