<?php
class CreateNonTaxableReasonsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_non_taxable_reasons_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'non_taxable_reasons' => array(
					'id' => array('type' => 'uuid', 'null' => false, 'key' => 'primary'),
					'reason' => array('type' => 'string', 'length' => 200),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						),
					)
				)
			),
		),
		'down' => array(
			'drop_table' => array(
				'non_taxable_reasons'
			)
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
			$NonTaxableReason = $this->generateModel('NonTaxableReason');
			$newData = [
				['reason'=> 'Service/Non Equipment'],
				['reason'=> 'Reseller'],
				['reason'=> 'Out of State'],
				['reason'=> 'Exact Shipping'],
			];
			$NonTaxableReason->saveMany($newData);
		}
		return true;
	}
}
