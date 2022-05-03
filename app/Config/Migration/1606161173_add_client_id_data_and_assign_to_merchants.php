<?php
class AddClientIdDataAndAssignToMerchants extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_client_id_data_and_assign_to_merchants';

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
		$Merchant = $this->generateModel('Merchant');
		$Client = $this->generateModel('Client');
		if ($direction === 'up') {
			$mUpdates = [];
			$dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
			$folder = new Folder($dir);
			$addedClientData = [];

			if (is_null($folder->path)) {
				throw new Exception("Directory $dir was not found!");
			}
			$row = 1;
			if (($handle = fopen($dir . "clientMetaData.csv", "r")) !== false) {
				while (($data = fgetcsv($handle, 10000, ",")) !== false) {

					//Indexes
					//[0] = Client ID
					//[1] = Client Name
					//[2] = MID

					//We don't care about headers
					if ($row === 1 && (strtolower($data[2]) === 'mid')) {
						$row ++;
						continue;
					}
					$clientId = trim($data[0]);
					$clientName = trim($data[1]);
					$mid = trim($data[2]);
					$merch = $Merchant->find('first',[
						'fields' => ['id'],
						'conditions' => ['merchant_mid' => $mid]
					]);

					$clientUuid = Hash::get($addedClientData, $clientId);
					if (empty($clientUuid)) {
						$clientUuid = CakeText::uuid();
						$newClients[] = [
							'id' => $clientUuid,
							'client_id_global' => $clientId,
							'client_name_global' => $clientName,
						];
						$addedClientData[$clientId] = $clientUuid;
					}
					
					if (!empty($merch['Merchant']['id'])) {
						$mUpdates[] = [
							'id' => $merch['Merchant']['id'],
							'client_id' => $addedClientData[$clientId]
						];
					} else {
						echo "Merchant $mid could not be found and was skipped\n";
					}
				}
				fclose($handle);
			} else {
				throw new Exception("fopen failed to open stream: No such file or directory!");
			}

			echo ">> Adding AxiaMed Clients and updating merchants ...\n";
			//Update all merchants
			$savedAll = $Merchant->saveMany($mUpdates);
			if (!$savedAll) {
			 	throw new Exception('Failed to saveAll Merchant Updates');
			}//Update all merchants
			$savedAll = $Client->saveMany($newClients);
			if (!$savedAll) {
			 	throw new Exception('Failed to saveAll Clients');
			}
		} elseif ($direction === 'down') {
			$Merchant->updateAll(
			['client_id' => null], ['client_id IS NOT NULL']);
			$Client->query('TRUNCATE table clients;');
		}
		return true;
	}

}
