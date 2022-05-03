<?php
class ReencryptMerchantsData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'reencrypt_merchants_data';

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
		$this->EncryptedModel = ClassRegistry::init('Merchant');
		if ($direction === 'up') {
			$dataModed = false;
			//Checking for data that was encrypted using deprecated MCRYPT functions
			$data = $this->EncryptedModel->find('all', [
				'fields' => ['id', 'merchant_tin', 'merchant_d_and_b'],
				'conditions' => [
					'OR' => [
						'length(merchant_tin) !=0',
						'length(merchant_d_and_b) !=0'
					]
				]
			]);

			foreach ($data as $idx => &$merchant) {
				if ($this->EncryptedModel->deprecated_isEncrypted($merchant['Merchant']['merchant_tin'])) {
					$merchant['Merchant']['merchant_tin'] = $this->EncryptedModel->mcryptToOpenSSLEnc($merchant['Merchant']['merchant_tin']);
					$dataModed = true;
				} else {
					//attept OpenSSL decrypt 
					//if this migration was already ran once before, then data is already OpenSSL encrypted and this will not be false
					if (@$this->EncryptedModel->decrypt($merchant['Merchant']['merchant_tin'], Configure::read('Security.OpenSSL.key')) === false) {
						//when false then attept to decrypt with MCRYPT and then check whether result is junk (very likely)
						//we expect a non empty string TAX ID number
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($merchant['Merchant']['merchant_tin']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check)) {
							$merchant['Merchant']['merchant_tin'] = null;
							$dataModed = true;
						}
					}
				}
				if ($this->EncryptedModel->deprecated_isEncrypted($merchant['Merchant']['merchant_d_and_b'])) {
					$merchant['Merchant']['merchant_d_and_b'] = $this->EncryptedModel->mcryptToOpenSSLEnc($merchant['Merchant']['merchant_d_and_b']);
					$dataModed = true;
				} elseif(!empty($merchant['Merchant']['merchant_d_and_b'])) {// most of these are empty
					if (@$this->EncryptedModel->decrypt($merchant['Merchant']['merchant_d_and_b'], Configure::read('Security.OpenSSL.key')) === false) {
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($merchant['Merchant']['merchant_d_and_b']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check)) {
							$merchant['Merchant']['merchant_d_and_b'] = null;
							$dataModed = true;
						}
					}
				}
			}

			//if migration already ran before data will not be modified so no need perform a giant saveMany
			if ($dataModed) {
				//turn off callbacks, encryption will happen again if before save
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
 * Utility method to remove specific characters from merchant TIN and D&B numbers
 *
 */
	private function __cleanUpStr($str) {
		$search = [",", "?", " ", "-", "'"];
		return str_replace($search, "", $str);
	}
}
