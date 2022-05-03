<?php
class AssignOrgsAndRegions extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'assign_orgs_and_regions';

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
			$Organization = $this->generateModel('Organization');
			$Region = $this->generateModel('Region');

			$dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
			$folder = new Folder($dir);

			if (is_null($folder->path)) {
				throw new Exception("Directory $dir was not found!");
			}
			$row = 1;
			if (($handle = fopen($dir . "MerchantOrgsRegions.csv", "r")) !== false) {
				while (($data = fgetcsv($handle, 10000, ",")) !== false) {

					//Indexes
					//[0] = MID
					//[1] = Organization
					//[2] = Region

					$num = count($data);// update count
					if ($num > 3) {
						fclose($handle);
						throw new OutOfBoundsException("CSV data contains more columns than the expected max of 3!");
					}
					//We don't care about headers
					if ($row === 1 && (strtolower($data[0]) === 'mid' || strtolower($data[1]) === 'dba')) {
						$row++;
						continue;
					}
					$data = array_map('trim', $data);

					//begin compiling data.
					$mUpdates["$data[0]"] = [
						'organization_id' => $Organization->field('id', ['name' => $data[1]])?: null,
						'region_id' => $Region->field('id', ['name' => $data[2]])?: null
					];
				}
				fclose($handle);
			} else {
				throw new Exception("fopen failed to open stream: No such file or directory!");
			}

			echo "Updating mechants orgs/regions rates ...\n";
			//Update all merchants within each subset
			$merchantsData = [];
			foreach ($mUpdates as $mid => $updates) {
				$merchantId = $Merchant->field('id', ['merchant_mid' => "$mid"]);
				if ($merchantId !== false) {
					$updates['id'] = $merchantId;
					$merchantsData[] = $updates;
				} else {
					echo "Merchant $mid not found and could not be updated! Skipping...\n";
				}
			}

			$updated = $Merchant->saveMany($merchantsData, ['validate' => false]);

			if ($updated === false) {
				throw new Exception("Updating merchants failed!");
			}
			return true;
		}
		return true;
	}
}
