<?php
class ReencryptMerchantOwnersData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'reencrypt_merchant_owners_data';

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
		$this->EncryptedModel = ClassRegistry::init('MerchantOwner');
		if ($direction === 'up') {
			$dataModed = false;
			//Checking for data that was encrypted using deprecated MCRYPT functions
			$data = $this->EncryptedModel->find('all', [
					'fields' => ['id', 'owner_social_sec_no'],
					'conditions' => ['length(owner_social_sec_no) > 0',
				]
			]);

			foreach ($data as $idx => &$record) {
				if ($this->EncryptedModel->deprecated_isEncrypted($record['MerchantOwner']['owner_social_sec_no'])) {
					$record['MerchantOwner']['owner_social_sec_no'] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['MerchantOwner']['owner_social_sec_no']);
					$dataModed = true;
				} else {
					//attept OpenSSL decrypt 
					//if this migration was already ran once before, then data is already OpenSSL encrypted and this will not be false
					if (@$this->EncryptedModel->decrypt($record['MerchantOwner']['owner_social_sec_no'], Configure::read('Security.OpenSSL.key')) === false) {
						//when false then attept to decrypt with MCRYPT and then check whether result is junk (very likely)
						//we expect a non empty string SSN number
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['MerchantOwner']['owner_social_sec_no']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check)) {
							$record['MerchantOwner']['owner_social_sec_no'] = null;
							$dataModed = true;
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
 * Utility method to remove specific characters from SSN numbers
 *
 */
	private function __cleanUpStr($str) {
		$pattern = '/\D+/i';
		$replacement = '';
		$str = preg_replace($pattern, $replacement, $str);
		//SSN is 9 digits long but sometimes there is two SSN's in the same field for some reason
		if (strlen($str) > 18) {
			return null;
		}
		return $str;
	}
}
