<?php
class ReencryptMerchantUwVolumesData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'reencrypt_merchant_uw_volumes_data';

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
		$this->EncryptedModel = ClassRegistry::init('MerchantUwVolume');
		if ($direction === 'up') {
			$dataModed = false;
			 $fields = [
				'te_amex_number',
				'te_diners_club_number',
				'te_discover_number',
				'te_jcb_number',
			];
			//Checking for data that was encrypted using deprecated MCRYPT functions
			$data = $this->EncryptedModel->find('all', [
				'fields' => array_merge(['id'], $fields),
				'conditions' => [
					'OR' => [
						'length(te_amex_number) > 0',
						'length(te_diners_club_number) > 0',
						'length(te_discover_number) > 0',
						'length(te_jcb_number) > 0',
					]
				]
			]);

			foreach ($data as $idx => &$record) {
				foreach ($fields as $fieldName) {
					if ($this->EncryptedModel->deprecated_isEncrypted($record['MerchantUwVolume'][$fieldName])) {
						$record['MerchantUwVolume'][$fieldName] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['MerchantUwVolume'][$fieldName]);
						$dataModed = true;
					} elseif(!empty($record['MerchantUwVolume'][$fieldName])) {
						//attept OpenSSL decrypt 
						//if this migration was already ran once before, then data is already OpenSSL encrypted and this will not be false
						if (@$this->EncryptedModel->decrypt($record['MerchantUwVolume'][$fieldName], Configure::read('Security.OpenSSL.key')) === false) {
							//when false then attept to decrypt with MCRYPT and then check whether result is junk (very likely)
							//we expect a non empty string banking number
							$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['MerchantUwVolume'][$fieldName]), Configure::read('Security.axSalt'));
							$check = $this->__cleanUpStr($check);
							if (empty($check) || !is_numeric($check)) {
								$record['MerchantUwVolume'][$fieldName] = null;
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
