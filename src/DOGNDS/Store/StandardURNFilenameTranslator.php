<?php

namespace DOGNDS\Store;

class StandardURNFilenameTranslator
{
	protected static $instance;
	public static function getInstance() {
		if( self::$instance === null ) self::$instance = new self();
		return self::$instance;
	}
	
	public function stripScheme( $uri ) {
		if( preg_match('/:([^:]+)$/',$uri,$bif) ) {
			return $bif[1];
		} else {
			throw new Exception("Don't see a scheme part in: $uri");
		}
	}
	
	public function dupToDir( $str, $byteCount ) {
		return substr($str,0,$byteCount) . '/' . $str;
	}
	
	public function __invoke( $urn ) {
		return $this->dupToDir( $this->stripScheme($urn), 2 );
	}
}
