<?php
class ReencryptMerchantBanksData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'reencrypt_merchant_banks_data';

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
		$this->EncryptedModel = ClassRegistry::init('MerchantBank');
		if ($direction === 'up') {
			$dataModed = false;
			//Checking for data that was encrypted using deprecated MCRYPT functions
			$data = $this->EncryptedModel->find('all', [
				'fields' => ['id', 'bank_routing_number', 'bank_dda_number', 'fees_routing_number', 'fees_dda_number'],
				'conditions' => [
					'OR' => [
						'length(bank_routing_number) > 0',
						'length(bank_dda_number) > 0',
						'length(fees_routing_number) > 0',
						'length(fees_dda_number) > 0',
					]
				]
			]);

			foreach ($data as $idx => &$record) {
				if ($this->EncryptedModel->deprecated_isEncrypted($record['MerchantBank']['bank_routing_number'])) {
					$record['MerchantBank']['bank_routing_number'] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['MerchantBank']['bank_routing_number']);
					$dataModed = true;
				} else {
					//attept OpenSSL decrypt 
					//if this migration was already ran once before, then data is already OpenSSL encrypted and this will not be false
					if (@$this->EncryptedModel->decrypt($record['MerchantBank']['bank_routing_number'], Configure::read('Security.OpenSSL.key')) === false) {
						//when false then attept to decrypt with MCRYPT and then check whether result is junk (very likely)
						//we expect a non empty string TAX ID number
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['MerchantBank']['bank_routing_number']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check) || empty(str_replace("?", "", $check))) {
							$record['MerchantBank']['bank_routing_number'] = null;
							$dataModed = true;
						}
					}
				}
				if ($this->EncryptedModel->deprecated_isEncrypted($record['MerchantBank']['bank_dda_number'])) {
					$record['MerchantBank']['bank_dda_number'] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['MerchantBank']['bank_dda_number']);
					$dataModed = true;
				} elseif(!empty($record['MerchantBank']['bank_dda_number'])) {// most of these are empty
					if (@$this->EncryptedModel->decrypt($record['MerchantBank']['bank_dda_number'], Configure::read('Security.OpenSSL.key')) === false) {
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['MerchantBank']['bank_dda_number']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check) || str_replace("?", "", $check) === '') {
							$record['MerchantBank']['bank_dda_number'] = null;
							$dataModed = true;
						}
					}
				}
				if ($this->EncryptedModel->deprecated_isEncrypted($record['MerchantBank']['fees_routing_number'])) {
					$record['MerchantBank']['fees_routing_number'] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['MerchantBank']['fees_routing_number']);
					$dataModed = true;
				} elseif(!empty($record['MerchantBank']['fees_routing_number'])) {// most of these are empty
					if (@$this->EncryptedModel->decrypt($record['MerchantBank']['fees_routing_number'], Configure::read('Security.OpenSSL.key')) === false) {
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['MerchantBank']['fees_routing_number']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check) || str_replace("?", "", $check) === '') {
							$record['MerchantBank']['fees_routing_number'] = null;
							$dataModed = true;
						}
					}
				}
				if ($this->EncryptedModel->deprecated_isEncrypted($record['MerchantBank']['fees_dda_number'])) {
					$record['MerchantBank']['fees_dda_number'] = $this->EncryptedModel->mcryptToOpenSSLEnc($record['MerchantBank']['fees_dda_number']);
					$dataModed = true;
				} elseif (!empty($record['MerchantBank']['fees_dda_number'])) {// most of these are empty
					if (@$this->EncryptedModel->decrypt($record['MerchantBank']['fees_dda_number'], Configure::read('Security.OpenSSL.key')) === false) {
						$check = @$this->EncryptedModel->deprecated_decrypt(hex2bin($record['MerchantBank']['fees_dda_number']), Configure::read('Security.axSalt'));
						$check = $this->__cleanUpStr($check);
						if (empty($check) || !is_numeric($check) || str_replace("?", "", $check) === '') {
							$record['MerchantBank']['fees_dda_number'] = null;
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
 * Utility method to remove specific characters from bank account numbers
 *
 */
	private function __cleanUpStr($str) {
		$search = [",", "?", " ", "-", "'"];
		return str_replace($search, "", $str);
	}
}
