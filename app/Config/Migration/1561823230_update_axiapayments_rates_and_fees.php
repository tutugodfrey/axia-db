<?php
App::uses('NoteType', 'Model');
class UpdateAxiapaymentsRatesAndFees extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_axiapayments_rates_and_fees';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		//This migration updates merchant's amex rate using CSV data
		if ($direction === 'up') {
			$Merchant = ClassRegistry::init('Merchant');
			$MerchantPricing = $this->generateModel('MerchantPricing');
			$MerchantNote = $this->generateModel('MerchantNote');
			$MerchantPricing = $this->generateModel('MerchantPricing');
			$User = $this->generateModel('User');
			$userId = $User->field('id', ['username' => 'omartinez']);
			$MerchantNote->detachListener('SystemTransactionListener');

			$dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
			$folder = new Folder($dir);

			if (is_null($folder->path)) {
				throw new Exception("Directory $dir was not found!");
			}
			$row = 1;
			if (($handle = fopen($dir . "newCardRatesFees.csv", "r")) !== false) {
				while (($data = fgetcsv($handle, 10000, ",")) !== false) {

					//Indexes
					//[0] = MID
					//[1] = DBA
					//[2] = V/MC rate
					//[3] = Amex rate
					//[4] = Discover rate
					//[5] = Debit rate
					//[6] = EBT rate
					//[7] = <blank separator>
					//[8] = V/MC Auth fee
					//[9] = Amex Auth fee
					//[10] = Discover Auth fee
					//[11] = Debit Auth fee
					//[12] = EBT auth fee

					$num = count($data);// update count
					if ($num > 13) {
						fclose($handle);
						throw new OutOfBoundsException("CSV data contains more columns than the expected max of 13!");
					}
					//We don't care about headers
					if ($row === 1 && (strtolower($data[0]) === 'mid' || strtolower($data[1]) === 'dba')) {
						$row++;
						continue;
					}
					$data = array_map('trim', $data);

					if (!empty($data[2])) {
						$mUpdates["$data[0]"]['processing_rate'] = $data[2];
					}
					if (!empty($data[3])) {
						$mUpdates["$data[0]"]['amex_processing_rate'] = $data[3];
					}
					if (!empty($data[4])) {
						$mUpdates["$data[0]"]['ds_processing_rate'] = $data[4];
					}
					if (!empty($data[5])) {
						$mUpdates["$data[0]"]['debit_processing_rate'] = $data[5];
					}
					if (!empty($data[6])) {
						$mUpdates["$data[0]"]['ebt_processing_rate'] = $data[6];
					}
					if (!empty($data[8])) {
						$mUpdates["$data[0]"]['mc_vi_auth'] = $data[8];
						$mUpdates["$data[0]"]['billing_mc_vi_auth'] = $data[8];
					}
					if (!empty($data[9])) {
						$mUpdates["$data[0]"]['amex_auth_fee'] = $data[9];
						$mUpdates["$data[0]"]['billing_amex_auth'] = $data[9];
					}
					if (!empty($data[10])) {
						$mUpdates["$data[0]"]['ds_auth_fee'] = $data[10];
						$mUpdates["$data[0]"]['billing_discover_auth'] = $data[10];
					}
					if (!empty($data[11])) {
						$mUpdates["$data[0]"]['debit_auth_fee'] = $data[11];
						$mUpdates["$data[0]"]['billing_debit_auth'] = $data[11];
					}
					if (!empty($data[12])) {
						$mUpdates["$data[0]"]['ebt_auth_fee'] = $data[12];
						$mUpdates["$data[0]"]['billing_ebt_auth'] = $data[12];
					}
					
				}
				fclose($handle);
			} else {
				throw new Exception("fopen failed to open stream: No such file or directory!");
			}

			echo "Updating Axia Payments mechants rates and auth fees ...\n";
			//Update all merchants within each subset
			$merchantsData = [];
			foreach ($mUpdates as $mid => $updates) {
				$merchantId = $Merchant->field('id', ['merchant_mid' => "$mid"]);
				if ($merchantId !== false) {
					$noteStr = "Increased auth fee by $0.03 and discount rate by 0.01%, effective May 1, 2019.\n";

					$curPricing = $MerchantPricing->find('first', [
						'fields' => ['id','processing_rate', 'mc_vi_auth', 'billing_mc_vi_auth', 'amex_processing_rate', 'amex_auth_fee', 'billing_amex_auth', 'ds_processing_rate', 'ds_auth_fee', 'billing_discover_auth', 'debit_processing_rate', 'debit_auth_fee', 'billing_debit_auth', 'ebt_processing_rate', 'ebt_auth_fee', 'billing_ebt_auth'],
						'conditions' => ['merchant_id' => $merchantId]
					]);

					if (!empty($updates['processing_rate']) || !empty($updates['mc_vi_auth']) || !empty($updates['billing_mc_vi_auth'])) {
						if ($this->_hasVisaMc($merchantId) == false) {
							unset($updates['processing_rate'],$updates['mc_vi_auth'],$updates['billing_mc_vi_auth']);
						} else {
							$noteStr .= 'V/MC rate updated from: "' . $curPricing['MerchantPricing']['processing_rate'] . '" to ' . '"' . Hash::get($updates, 'processing_rate') . '" .' . "\n";
							$noteStr .= 'V/MC Auth updated from: "' . $curPricing['MerchantPricing']['mc_vi_auth'] . '" to ' . '"' . Hash::get($updates, 'mc_vi_auth') . '" .' . "\n";
							$noteStr .= 'V/MC Billing Auth updated from: "' . $curPricing['MerchantPricing']['billing_mc_vi_auth'] . '" to ' . '"' . Hash::get($updates, 'billing_mc_vi_auth') . '" .' . "\n"; 
						}
					}
					if (!empty($updates['amex_processing_rate']) || !empty($updates['amex_auth_fee']) || !empty($updates['billing_amex_auth'])) {
						if ($this->_hasAmex($merchantId) == false) {
							unset($updates['amex_processing_rate'],$updates['amex_auth_fee'],$updates['billing_amex_auth']);
						} else {
							$noteStr .= 'Amex rate updated from: "' . $curPricing['MerchantPricing']['amex_processing_rate'] . '" to ' . '"' . Hash::get($updates, 'amex_processing_rate') . '" .' . "\n";
							$noteStr .= 'Amex Auth updated from: "' . $curPricing['MerchantPricing']['amex_auth_fee'] . '" to ' . '"' . Hash::get($updates, 'amex_auth_fee') . '" .' . "\n";
							$noteStr .= 'Amex Billing Auth updated from: "' . $curPricing['MerchantPricing']['billing_amex_auth'] . '" to ' . '"' . Hash::get($updates, 'billing_amex_auth') . '" .' . "\n";
						}
					}
					if (!empty($updates['ds_processing_rate']) || !empty($updates['ds_auth_fee']) || !empty($updates['billing_discover_auth'])) {
						if ($this->_hasDiscover($merchantId) == false) {
							unset($updates['ds_processing_rate'],$updates['ds_auth_fee'],$updates['billing_discover_auth']);
						} else {
							$noteStr .= 'Discover rate updated from: "' . $curPricing['MerchantPricing']['ds_processing_rate'] . '" to ' . '"' . Hash::get($updates, 'ds_processing_rate') . '" .' . "\n";
							$noteStr .= 'Discover Auth updated from: "' . $curPricing['MerchantPricing']['ds_auth_fee'] . '" to ' . '"' . Hash::get($updates, 'ds_auth_fee') . '" .' . "\n";
							$noteStr .= 'Discover Billing Auth updated from: "' . $curPricing['MerchantPricing']['billing_discover_auth'] . '" to ' . '"' . Hash::get($updates, 'billing_discover_auth') . '" .' . "\n";
						}
					}
					if (!empty($updates['debit_processing_rate']) || !empty($updates['debit_auth_fee']) || !empty($updates['billing_debit_auth'])) {
						if ($this->_hasDebit($merchantId) == false) {
							unset($updates['debit_processing_rate'],$updates['debit_auth_fee'],$updates['billing_debit_auth']);
						} else {
							$noteStr .= 'Debit rate updated from: "' . $curPricing['MerchantPricing']['debit_processing_rate'] . '" to ' . '"' . Hash::get($updates, 'debit_processing_rate') . '" .' . "\n";
							$noteStr .= 'Debit Auth updated from: "' . $curPricing['MerchantPricing']['debit_auth_fee'] . '" to ' . '"' . Hash::get($updates, 'debit_auth_fee') . '" .' . "\n";
							$noteStr .= 'Debit Billing Auth updated from: "' . $curPricing['MerchantPricing']['billing_debit_auth'] . '" to ' . '"' . Hash::get($updates, 'billing_debit_auth') . '" .' . "\n";
						}
					}
					if (!empty($updates['ebt_processing_rate']) || !empty($updates['ebt_auth_fee']) || !empty($updates['billing_ebt_auth'])) {
						if ($this->_hasEBT($merchantId) == false) {
							unset($updates['ebt_processing_rate'],$updates['ebt_auth_fee'],$updates['billing_ebt_auth']);
						} else {
							$noteStr .= 'EBT rate updated from: "' . $curPricing['MerchantPricing']['ebt_processing_rate'] . '" to ' . '"' . Hash::get($updates, 'ebt_processing_rate') . '" .' . "\n";
							$noteStr .= 'EBT Auth updated from: "' . $curPricing['MerchantPricing']['ebt_auth_fee'] . '" to ' . '"' . Hash::get($updates, 'ebt_auth_fee') . '" .' . "\n";
							$noteStr .= 'EBT Billing Auth updated from: "' . $curPricing['MerchantPricing']['billing_ebt_auth'] . '" to ' . '"' . Hash::get($updates, 'billing_ebt_auth') . '" .' . "\n";
						}
					}
					$updates['id'] = $curPricing['MerchantPricing']['id'];
					$updates['merchant_id'] = $merchantId;
					$merchantsData[] = $updates;
					$merchantNotes[] = [
						'merchant_id' => $merchantId,
						'user_id' => $userId,
						'note_type_id' => NoteType::GENERAL_NOTE_ID,
						'note_date' => date('Y-m-d H:i:s'),
						'note' => $noteStr,
						'note_title' => 'Rate and Auth pricing updated via Migration',
						'general_status' => 'COMP',
						'resolved_date' => date('Y-m-d'),
						'resolved_time'	 => date('H:i:s')
					];
				} else {
					echo "Merchant $mid not found and could not be updated! Skipping...\n";
				}

			}


			$updated = $MerchantPricing->saveMany($merchantsData, ['validate' => false]);
			$updatedNotes = $MerchantNote->saveMany($merchantNotes, ['validate' => false]);

			if ($updated == false || $updatedNotes == false) {
				throw new Exception("Updating merchants failed!");
			}
			return true;
		}
		return true;
	}

/**
 * Utitlity functions to check whether merchant has underlying products
 *
 * @param string Merchant.id uuid
 * @return boolean
 */
	protected function _hasVisaMc($merchantId){
		return $this->_hasProduct($merchantId, '(Visa|MasterCard)');
	}
	protected function _hasAmex($merchantId){
		return $this->_hasProduct($merchantId, '(American)');
	}
	protected function _hasDiscover($merchantId){
		return $this->_hasProduct($merchantId, '(Discover)');
	}
	protected function _hasDebit($merchantId){
		return $this->_hasProduct($merchantId, '(Debit)');
	}
	protected function _hasEBT($merchantId){
		return $this->_hasProduct($merchantId, '(EBT)');
	}
	protected function _hasProduct($merchantId, $searchPattern) {
		$ProductsAndService = $this->generateModel('ProductsAndService');
		return $ProductsAndService->hasAny(
			[
				'merchant_id' => $merchantId,
				"products_services_type_id in (SELECT id from products_services_types where products_services_description ~* '$searchPattern')"
			]
		);
	}
}
