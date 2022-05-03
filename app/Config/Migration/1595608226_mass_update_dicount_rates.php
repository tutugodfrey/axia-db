<?php
App::uses('NoteType', 'Model');
class MassUpdateDicountRates extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'mass_update_dicount_rates';

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
		//This migration updates merchant's discount processing rate using CSV data
		if ($direction === 'up') {
			$Merchant = ClassRegistry::init('Merchant');
			$MerchantPricing = $this->generateModel('MerchantPricing');
			$MerchantNote = $this->generateModel('MerchantNote');
			$User = $this->generateModel('User');
			$userId = $User->field('id', ['username' => 'omartinez']);
			$MerchantNote->detachListener('SystemTransactionListener');
			$mUpdates = [];
			$dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
			$folder = new Folder($dir);

			if (is_null($folder->path)) {
				throw new Exception("Directory $dir was not found!");
			}
			$row = 1;
			if (($handle = fopen($dir . "DBRatesUpdate.csv", "r")) !== false) {
				while (($data = fgetcsv($handle, 10000, ",")) !== false) {

					//Indexes
					//[0] = MID
					//[1] = V/MC rate
					//[2] = Discover rate
					//[3] = Amex rate

					//We don't care about headers
					if ($row === 1 && (strtolower($data[0]) === 'mid')) {
						$row ++;
						continue;
					}
					$data = array_map('trim', $data);

					if (!empty($data[1])) {
						$mUpdates["$data[0]"]['processing_rate'] = $data[1];
					}
					if (!empty($data[2])) {
						$mUpdates["$data[0]"]['ds_processing_rate'] = $data[2];
					}
					if (!empty($data[3])) {
						$mUpdates["$data[0]"]['amex_processing_rate'] = $data[3];
					}
				}
				fclose($handle);
			} else {
				throw new Exception("fopen failed to open stream: No such file or directory!");
			}

			echo "Updating AxiaMed Clients Discount Processing Rates ...\n";
			//Update all merchants within each subset
			$merchantsData = [];
			foreach ($mUpdates as $mid => $updates) {
				$merchantId = $Merchant->field('id', ['merchant_mid' => "$mid"]);
				if ($merchantId !== false) {
					$noteStr = "Updated \n";

					$curPricing = $MerchantPricing->find('first', [
						'fields' => ['id','processing_rate', 'amex_processing_rate', 'ds_processing_rate'],
						'conditions' => ['merchant_id' => $merchantId]
					]);

					if (empty($curPricing['MerchantPricing']['processing_rate'])) {
						unset($updates['processing_rate']);
					} else {
						$noteStr .= '- V/MC rate updated from: "' . $curPricing['MerchantPricing']['processing_rate'] . '" to ' . '"' . Hash::get($updates, 'processing_rate') . '" .' . "\n";
					}

					if (empty($curPricing['MerchantPricing']['amex_processing_rate'])) {
						unset($updates['amex_processing_rate']);
					} else {
						$noteStr .= '- Amex rate updated from: "' . $curPricing['MerchantPricing']['amex_processing_rate'] . '" to ' . '"' . Hash::get($updates, 'amex_processing_rate') . '" .' . "\n";
					}

					if (empty($curPricing['MerchantPricing']['ds_processing_rate'])) {
						unset($updates['ds_processing_rate']);
					} else {
						$noteStr .= '- Discover rate updated from: "' . $curPricing['MerchantPricing']['ds_processing_rate'] . '" to ' . '"' . Hash::get($updates, 'ds_processing_rate') . '" .' . "\n";
					}
					if (!empty($updates)) {
						$updates['id'] = $curPricing['MerchantPricing']['id'];
						$updates['merchant_id'] = $merchantId;
						$merchantsData[] = $updates;
						$merchantNotes[] = [
							'merchant_id' => $merchantId,
							'user_id' => $userId,
							'note_type_id' => NoteType::GENERAL_NOTE_ID,
							'note_date' => date('Y-m-d H:i:s'),
							'note' => $noteStr,
							'note_title' => 'Discount Processing rate updated via Migration Script',
							'general_status' => 'COMP',
							'resolved_date' => date('Y-m-d'),
							'resolved_time'	 => date('H:i:s')
						];
					}
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
}
