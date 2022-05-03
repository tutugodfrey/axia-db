<?php

class ReencryptLoggableLogsContentData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'reencrypt_loggable_logs_content_data';

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
		$LoggableLog = ClassRegistry::init('LoggableLog');
		if ($direction === 'up') {
			$testOpenSSLEncryption = $LoggableLog->find('first', [
				'fields' => ['content'],
				'conditions' => [
					'model' => 'Merchant',
					//all data encrypted with OpenSSL contains the substring LVUNxE4dHOj
					"content LIKE '%LVUNxE4dHOj%'"
				]
			]);

			//If data has already been re-encrypted skip running this migration script
			if (!empty($testOpenSSLEncryption)) {
				$testContent = unserialize($testOpenSSLEncryption['LoggableLog']['content']);
				//Test a sample encryption
				$checkEncData = (Hash::get($testContent, 'Merchant.merchant_tin'))?: Hash::get($testContent, 'Merchant.merchant_d_and_b');
				$isOpenSSLEncrypted = $LoggableLog->isEncrypted($checkEncData);
				if ($isOpenSSLEncrypted === true) {
					return true;
				}
			}
			$encryptedFields = array(
				'Merchant' => array('merchant_tin', 'merchant_d_and_b'),
				'MerchantBank' => array('bank_routing_number', 'bank_dda_number', 'fees_routing_number', 'fees_dda_number'),
				'MerchantUwVolume' => array(
					'te_amex_number',
					'te_diners_club_number',
					'te_discover_number',
					'te_jcb_number',
				),
				'Ach' => array(
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
				),
				'MerchantOwner' => array('owner_social_sec_no')
			);
			
			$data = $LoggableLog->find('all', [
				'fields' => ['id', 'content'],
				'conditions' => [
					'model' => ['Merchant', 'MerchantOwner', 'MerchantBank', 'MerchantUwVolume', 'MerchantUw', 'Ach'],
					'OR' => [
						//We want to match a double quote in the serialized content but since
						//double quotes are removed by the CakePHP ORM they cannot be directly searched for in the query conditions
						//By using the Regex pattern [^1] we are marching any character that is not the number one
						//effectively matching the double quote that is only found at that possition.
						"content ~ 'merchant_d_and_b[^1];s:[1-9]'",
						"content ~ 'merchant_tin[^1];s:[1-9]'",
						"content ~ 'owner_social_sec_no[^1];s:[1-9]'",
						"content ~ 'bank_routing_number[^1];s:[1-9]'",
						"content ~ 'bank_dda_number[^1];s:[1-9]'",
						"content ~ 'fees_routing_number[^1];s:[1-9]'",
						"content ~ 'fees_dda_number[^1];s:[1-9]'",
						"content ~ 'te_amex_number[^1];s:[1-9]'",
						"content ~ 'te_diners_club_number[^1];s:[1-9]'",
						"content ~ 'te_discover_number[^1];s:[1-9]'",
						"content ~ 'te_jcb_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_w_dsb_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_w_dsb_account_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_w_fee_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_w_fee_account_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_w_rej_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_w_rej_account_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_nw_dsb_account_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_nw_dsb_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_nw_fee_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_nw_fee_account_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_nw_rej_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_mi_nw_rej_account_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_w_dsb_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_w_dsb_account_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_w_fee_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_w_fee_account_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_w_rej_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_w_rej_account_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_nw_dsb_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_nw_dsb_account_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_nw_fee_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_nw_fee_account_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_nw_rej_routing_number[^1];s:[1-9]'",
						"content ~ 'ach_ci_nw_rej_account_number[^1];s:[1-9]'"
					]
				],
			]);
			if (!empty($data)) {
				foreach ($data as &$record) {
					$content = unserialize($record['LoggableLog']['content']);
					foreach ($encryptedFields as $modelName => $fieldNames) {
						if (!empty(Hash::get($content, $modelName))) {
							//Check for hasMany data structure
							if (!empty(Hash::get($content, "$modelName.0")) && is_array(Hash::get($content, "$modelName.0"))) {
								$tmpContent = [];
								foreach(Hash::get($content, $modelName) as $idx => $hasManyContent) {
									$tmpContent[$idx] = $LoggableLog->deprecated_decryptFields($hasManyContent);
									$tmpContent[$idx] = $LoggableLog->encryptFields($fieldNames, $tmpContent[$idx]);
								}
								$content[$modelName] = $tmpContent;
							} else {
								foreach ($fieldNames as $fieldName) {
									$mcryptData = Hash::get($content, "$modelName.$fieldName");
									if ($LoggableLog->deprecated_isEncrypted($mcryptData)) {
										$decrypted = $LoggableLog->deprecated_decrypt(hex2bin($mcryptData), Configure::read('Security.axSalt'));
										$check = $this->__cleanUpStr($decrypted);

										if (empty($check) || !is_numeric($check)) {
											$content[$modelName][$fieldName] = null;
										} else {
											$content[$modelName][$fieldName] = $LoggableLog->encrypt($decrypted, Configure::read('Security.OpenSSL.key'));
										}
									}
								}
							}
							
						}
					}
					//re-serialize data here
					$record['LoggableLog']['content'] = serialize($content);
				}
				$LoggableLog->saveMany($data, ['validate' => false, 'callbacks' => false]);
			}
		}

		return true;
	}

/**
 * __cleanUpStr
 * Utility method to remove specific characters from banking/SSN/TIN numbers
 *
 */
	private function __cleanUpStr($str) {
		$search = [",", "?", " ", "-", "'", "/"];
		return str_replace($search, "", $str);
	}
}
