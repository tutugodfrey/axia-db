<?php
class CreateAddressesOwnerFkConstraint extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_addresses_owner_fk_constraint';

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
		$Address = $this->generateModel('Address');
		$dataSource = ConnectionManager::getDataSource($this->connection);
		$dataSource->begin();
		if ($direction === 'up') {
			$Address->query("ALTER TABLE addresses ADD CONSTRAINT fk_merchant_owner_id FOREIGN KEY (merchant_owner_id) REFERENCES merchant_owners (id) on DELETE CASCADE");
			$result = $Address->query("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'addresses' AND constraint_name = 'fk_merchant_owner_id'");
			if (empty($result)) {
				$dataSource->rollback();
				throw new Exception('ERROR: Faled to create FOREIGN KEY CONSTRAINT!');
			}
		} else {
			$Address->query("ALTER TABLE addresses DROP CONSTRAINT IF EXISTS fk_merchant_owner_id;");
		}
		$dataSource->commit();
		return true;
	}
}
