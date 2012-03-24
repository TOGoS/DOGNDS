<?php

class DOGNDS_Crypto_SignatureInfo
{
	public $algorithmName;
	public $signingKeyUri;
	public $signedContent;
	public $signatureData;
	
	public function __construct( $algName, $keyUri, $content, $sigDat ) {
		$this->algorithmName = $algName;
		$this->signingKeyUri = $keyUri;
		$this->signedContent = $content;
		$this->signatureData = $sigDat;
	}
	
	public function getAlgorithmName() {  return $this->algorithmName;  }
	public function getSigningKeyUri() {  return $this->signingKeyUri;  }
	public function getSignedContent() {  return $this->signedContent;  }
	public function getSignatureData() {  return $this->signatureData;  }
	
	//// Burke Content Signature functions ////
	// See http://tools.ietf.org/html/draft-burke-content-signature-00
	
	public static function fromBurkeContentSignature( $csig, $content ) {
		if( is_scalar($csig) ) {
			$parts = explode(";",$csig);
			$csig = array();
			foreach( $parts as $p ) {
				list($k,$v) = explode('=',$p,2);
				$k = trim($k); $v = trim($v);
				$csig[$k] = $v;
			}
		}
		return new self( $csig['algorithm'], $csig['signer'], $content, pack('H*',$csig['signature']) );
	}
	
	public static function fromBurkeContentSignatures( $csigs, $content ) {
		if( $csigs == null ) return array();
		if( is_scalar($csigs) ) {
			if( trim($csigs) == '' ) return array();
			// Note: Quoted strings could also contain commas,
			// and this won't handle those correctly.
			// But I don't use quoted strings.
			$csigs = explode(',', $csigs);
		}
		foreach( $csigs as $k=>$csig ) {
			$csigs[$k] = self::fromBurkeContentSignature( $csig, $content );
		}
		return $csigs;
	}
	
	public static function toBurkeContentSignature( $sign ) {
		list(,$sigHex) = unpack('H*',$sign->getSignatureData());
		$algName = $sign->getAlgorithmName();
		$keyUri  = $sign->getSigningKeyUri();
		return "signature=$sigHex; signer=$keyUri; algorithm=$algName";
	}
}
