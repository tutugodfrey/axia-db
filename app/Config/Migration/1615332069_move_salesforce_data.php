<?php
App::uses('SalesForce', 'Model');
class MoveSalesforceData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'move_salesforce_data';

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
		if ($direction === 'up') {
			$AssocExtRecord = $this->generateModel('AssociatedExternalRecord');
			//Since this migration drops the "AssociatedExternalRecord.external_id" field any attept to run "up" more than
			//once will cause problems so by checking if field exists we can tell whether this migration
			//has already been ran upwards before
			$tmpSample = $AssocExtRecord->find('first');			
			if (array_key_exists('external_id', $tmpSample['AssociatedExternalRecord'])) {
				$extRecords = $AssocExtRecord->find('all');
				$SalesForce = ClassRegistry::init('SalesForce');
				$preCount = count($extRecords);
				$recordFields = [];
				foreach ($extRecords as $extRecord) {
					$recordFields[] = [
						'merchant_id' => $extRecord['AssociatedExternalRecord']['merchant_id'],
						'associated_external_record_id' => $extRecord['AssociatedExternalRecord']['id'],
						'field_name' => SalesForce::ACCOUNT_ID,
						'api_field_name' => $SalesForce->fieldNames[SalesForce::ACCOUNT_ID]['field_name'],
						'value' => Hash::get($extRecord, 'AssociatedExternalRecord.external_id'),
					];
				}
				$ExtRecordField = $this->generateModel('ExternalRecordField');
				$ExtRecordField->saveMany($recordFields);
				$postCount = $ExtRecordField->find('count');

				if ($preCount === $postCount) {
					$AssocExtRecord->query('ALTER TABLE associated_external_records DROP COLUMN IF EXISTS external_id');
				} else {
					throw new Exception("Failed to save all external records as fields $preCount !== $postCount");
				}
			}
		}
		return true;
	}
}
