<?php
class ReencryptAchesData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'reencrypt_aches_data';

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
		$this->EncryptedModel = ClassRegistry::init('Ach');
		if ($direction === 'up') {
			$dataModed = false;
			 $fields = [
				'ach_mi_w_dsb_routing_number',
				'ach_mi_w_dsb_account_number',
				'ach_mi_w_fee_routing_number',
				'ach_mi_w_fee_account_number',
				'ach_mi_w_rej_routing_number',
				'ach_mi_w_rej_account_number',
				'ach_mi_nw_dsb_account_number',
				'ach_mi_nw_dsb_routing_number',
				'ach_mi_nw_fee_routing_number',
				'ach_mi_nw_fee_account_number',
				'ach_mi_nw_rej_routing_number',
				'ach_mi_nw_rej_account_number',
				'ach_ci_w_dsb_routing_number',
				'ach_ci_w_dsb_account_number',
				'ach_ci_w_fee_routing_number',
				'ach_ci_w_fee_account_number',
				'ach_ci_w_rej_routing_number',
				'ach_ci_w_rej_account_number',
				'ach_ci_nw_dsb_routing_number',
				'ach_ci_nw_dsb_account_number',
				'ach_ci_nw_fee_routing_number',
				'ach_ci_nw_fee_account_number',
				'ach_ci_nw_rej_routing_number',
				'ach_ci_nw_rej_account_number',
			];
			//Checking for data that was encrypted using deprecated MCRYPT functions
			$data = $this->EncryptedModel->find('all', [
				'fields' => array_merge(['id'], $fields),
				'conditions' => [
					'OR' => [
						'length(ach_mi_w_dsb_routing_number) > 0',
						'length(ach_mi_w_dsb_account_number) > 0',
						'length(ach_mi_w_fee_routing_number) > 0',
						'length(ach_mi_w_fee_account_number) > 0',
						'length(ach_mi_w_rej_routing_number) > 0',
						'length(ach_mi_w_rej_account_number) > 0',
						'length(ach_mi_nw_dsb_account_number) > 0',
						'length(ach_mi_nw_dsb_routing_number) > 0',
						'length(ach_mi_nw_fee_routing_number) > 0',
						'length(ach_mi_nw_fee_account_number) > 0',
						'length(ach_mi_nw_rej_routing_number) > 0',
						'length(ach_mi_nw_rej_account_number) > 0',
						'length(ach_ci_w_dsb_routing_number) > 0',
						'length(ach_ci_w_dsb_account_number) > 0',
						'length(ach_ci_w_fee_routing_number) > 0',
						'length(ach_ci_w_fee_account_number) > 0',
						'length(ach_ci_w_rej_routing_number) > 0',
						'length(ach_ci_w_rej_account_number) > 0',
						'length(ach_ci_nw_dsb_routing_number) > 0',
						'length(ach_ci_nw_dsb_account_number) > 0',
						'length(ach_ci_nw_fee_routing_number) > 0',
						'length(ach_ci_nw_fee_account_number) > 0',
						'length(ach_ci_nw_rej_routing_number) > 0',
						'length(ach_ci_nw_rej_account_number) > 0',
					]
				]
			]);

			foreach ($data as $idx => &$record) {
				foreach ($fields as $fieldName) {
					if ($this->EncryptedModel->deprecated_isEncrypted($record['Ach'][$fieldName])) {
						$record['Ach'][$fieldName] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['Ach'][$fieldName]);
						$dataModed = true;
					} else {
						//attept OpenSSL decrypt 
						//if this migration was already ran once before, then data is already OpenSSL encrypted and this will not be false
						if (@$this->EncryptedModel->decrypt($record['Ach'][$fieldName], Configure::read('Security.OpenSSL.key')) === false) {
							//when false then attept to decrypt with MCRYPT and then check whether result is junk (very likely)
							//we expect a non empty string banking number
							$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['Ach'][$fieldName]), Configure::read('Security.axSalt'));
							$check = $this->__cleanUpStr($check);
							if (empty($check) || !is_numeric($check)) {
								$record['Ach'][$fieldName] = null;
								$dataModed = true;
							}
						}
					}
				}
			}

			//if migration already ran before data will not be modified so no need perform a giant saveMany
			if ($dataModed) {
				//turn off callbacks, otherwise encryption will happen again if beforeSave
				$isSaved = $this->EncryptedModel->saveMany($data, array('validate' => false, 'callbacks' => false));
				if (!$isSaved) {
					throw new Exception('Save Failed!');
				}
			}
		}
		return true;
	}

/**
 * __cleanUpStr
 * Utility method to remove specific characters from bank account numbers
 *
 */
	private function __cleanUpStr($str) {
		$search = [",", "?", " ", "-", "'"];
		return str_replace($search, "", $str);
	}
}
