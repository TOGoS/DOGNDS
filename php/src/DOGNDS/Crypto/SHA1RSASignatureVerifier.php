<?php

class DOGNDS_Crypto_SHA1RSASignatureVerifier
{
	public $pubKeySource;
	
	public function __construct( $pubKeySource ) {
		$this->pubKeySource = $pubKeySource;
	}
	
	public function verify( $sig ) {
		if( ($algName = $sig->getAlgorithmName()) != 'SHA1withRSA' ) {
			DOGNDS_Log::log("Algorithm '$algName' is not supported (only 'SHA1withRSA' is).");
			return false;
		}
		$pubData = $this->pubKeySource->get( $keyUri = $sig->getSigningKeyUri() );
		if( $pubData == null ) {
			DOGNDS_Log::log("Couldn't find public key '$keyUri'.");
			return false;
		}
		$pubKeyPem = DOGNDS_Crypto_Util::derToPem( $pubData );
		$pubKey = openssl_get_publickey($pubKeyPem);
		if( $pubKey === false ) {
			DOGNDS_Log::log("Failed to parse public key $keyUri: ".openssl_error_string()."; PEM data = $pubKeyPem");
			return false;
		}
		$verified = openssl_verify( $sig->getSignedContent(), $sig->getSignatureData(), $pubKey );
		openssl_free_key( $pubKey );
		if( !$verified ) {
			 DOGNDS_Log::log("Verification failed!");
			 return false;
		}
		return true;
	}
	
	/**
	 * If there is a valid signature for the request, returns the
	 * request content (a string).  Otherwise, returns false.
	 * @param string $content the request content
	 * @param string $cSigHeader the value of the Content-Signature header;
	 *   if there are multiple signatures, this will be a comma-separated list
	 *   (this is the standard way to consolidate multiple headers into one -
	 *   see RFC 2616)
	 * @param array &$invalidSigs invalid signatures will be pushed onto this array
	 * @return list of valid signatures if all went well, or false if there were invalid ones
	 */
	public function processContentSignatures( $content, $cSigHeader, &$invalidSigs=array() ) {
		$signatures = DOGNDS_Crypto_SignatureInfo::fromBurkeContentSignatures($cSigHeader, $content);
		$validSigs = array();
		
		if( count($signatures) == 0 ) {
			DOGNDS_Log::log("No signature headers found.");
			return array();
		}
		
		$anySigsInvalid = false;
		foreach( $signatures as $sign ) {
			if( $this->verify( $sign ) ) {
				$validSigs[] = $sign;
			} else {
				// But keep going so we can rack up lots of error messages
				$invalidSigs[] = $sign;
			}
		}
		if( count($invalidSigs) > 0 ) {
			return false;
		}
		return $validSigs;
	}
}
