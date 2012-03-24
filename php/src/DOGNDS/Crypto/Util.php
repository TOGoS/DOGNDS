<?php

class DOGNDS_Crypto_Util
{
	public static function derToPem( $data ) {
		$pem = chunk_split(base64_encode($data), 64, "\n");
		$pem = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";
		return $pem;
	}
}
