<?php
class CustomSortSequencesUniquenesCorrections extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'custom_sort_sequences_uniquenes_corrections';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'rename_field' => array(
				'gateways' => array(
					'order' => 'position'
				),
				'merchant_ach_reasons' => array(
					'order' => 'position'
				)
			)
		),
		'down' => array(
			'rename_field' => array(
				'gateways' => array(
					'position' => 'order'
				),
				'merchant_ach_reasons' => array(
					'position' => 'order'
				)
			)
		),
	);

/**
 *	Tables with ducplicate sequence sort numbers
 *		gateways                    | position
 *		merchant_ach_app_statuses   | rank
 *		merchant_ach_reasons        | position
 *		merchant_reject_statuses    | priority
 *		networks                    | position
 *		uw_approvalinfos            | priority
 *		uw_infodocs                 | priority
 *		uw_statuses                 | priority
 *		vendors                     | rank
 */
	public $modelFieldRel = [
		'gateways' => 'position',
		'merchant_ach_app_statuses' => 'rank',
		'merchant_ach_reasons' => 'position',
		'merchant_reject_statuses' => 'priority',
		'networks' => 'position',
		'uw_approvalinfos' => 'priority',
		'uw_infodocs' => 'priority',
		'uw_statuses' => 'priority',
		'vendors' => 'rank',
	];

/**
 * after migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'up') {
			foreach ($this->modelFieldRel as $tableName => $sortField) {
				$modelName = Inflector::classify($tableName);
				$Model = $this->generateModel($modelName);
				$sortValues = $Model->find('all', [
					'fields' => ['id', $sortField],
					'order' => ["\"$modelName\".\"$sortField\" ASC NULLS LAST"]
				]);
				$firstVal = $sortValues[0][$modelName][$sortField];
				if (is_numeric($firstVal)) {
					$totalRecords = count($sortValues);
					for ($i = 0; $i < $totalRecords; $i++) {
						//Some sortfield values are intentionally set to a number large enough
						//that the total count of records will never reach it, skip those
						if ($sortValues[$i][$modelName][$sortField] > $totalRecords) {
							continue;
						} else {
							$sortValues[$i][$modelName][$sortField] = $firstVal;
							$firstVal++;
						}
					}

					if (!$Model->saveMany($sortValues)) {
						echo "Failed to save $modelName updates " . implode(',', Hash::flatten($Model->validationErrors));
						return false;
					}
				} else {
					echo "First sample value in the sequence is not a number: $firstVal. Records in $tableName were note updated\n";
				}
			}
		}
		return true;
	}
}
