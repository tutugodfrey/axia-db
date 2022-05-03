<?php
class UpdateMoreAxiamedAcquiringClientsAsControlscanBoarded extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_more_axiamed_acquiring_clients_as_controlscan_boarded';

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
			$Merchant = $this->generateModel('Merchant');
			$MerchantPci = $this->generateModel('MerchantPci');
			$mUpdates = [];
			$dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
			$folder = new Folder($dir);

			if (is_null($folder->path)) {
				throw new Exception("Directory $dir was not found!");
			}
			$row = 1;
			if (($handle = fopen($dir . "moreBoardedInControlScan.csv", "r")) !== false) {
				while (($data = fgetcsv($handle, 10000, ",")) !== false) {

					//Indexes
					//[0] = MID

					//We don't care about headers
					if ($row === 1 && (strtolower($data[0]) === 'mid')) {
						$row ++;
						continue;
					}
					$mid = trim($data[0]);
					$merch = $Merchant->find('first',[
						'fields' => ['id', 'merchant_mid', 'merchant_id_old'],
						'conditions' => [
							'OR' => [
								['merchant_id_old' => $mid],
								['merchant_mid' => $mid]
							]
						]
					]);
					if (!empty($merch['Merchant']['id'])) {
						$newData = [
							'merchant_id' => $merch['Merchant']['id'],
							'merchant_id_old' => $merch['Merchant']['merchant_mid'],
							'compliance_level' => 4,
							'scanning_company' => 'Control Scan',
							'insurance_fee' => 0,
							'pci_enabled' => false,
							'controlscan_boarded' => true,
							'cancelled_controlscan' => false
						];
						$toUpdate = $MerchantPci->find('first', ['conditions' => ['merchant_id' =>  $merch['Merchant']['id']]]);
						if (empty(Hash::get($toUpdate, 'MerchantPci.id'))) {
							$mUpdates[] = $newData;
						} else {
							if ($toUpdate['MerchantPci']['merchant_id_old'] == $merch['Merchant']['merchant_id_old']) {
								$newData['merchant_id_old'] = $merch['Merchant']['merchant_id_old'];
							}
							$newData = array_merge($toUpdate['MerchantPci'], $newData);
							$mUpdates[] = $newData;
						}
					} else {
						echo "Merchant $mid could not be found and was skipped\n";
					}
				}
				fclose($handle);
			} else {
				throw new Exception("fopen failed to open stream: No such file or directory!");
			}

			echo ">> Updating AxiaMed Acquiring Clients as Boarded in control Scan ...\n";
			//Update all merchants
			$savedAll = $MerchantPci->saveMany($mUpdates);
			if (!$savedAll) {
			 	throw new Exception('Failed to saveAll');
			}
		}
		return true;
	}
}
