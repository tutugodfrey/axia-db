<?php
class LibCrypt {

	const SALT = '8xv;K,He1<9X=_is73~r';

	public static function crypt($input, $encode = true) {
		$td = mcrypt_module_open(MCRYPT_TRIPLEDES, '', MCRYPT_MODE_ECB, '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, self::SALT, $iv);
		if ($encode) {
			$result = base64_encode(mcrypt_generic($td, $input));
		} else {
			$result = mdecrypt_generic($td, base64_decode($input));
		}
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		return trim($result);
	}

}