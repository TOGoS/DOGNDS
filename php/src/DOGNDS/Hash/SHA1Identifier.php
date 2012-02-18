<?php

namespace DOGNDS\Hash;

use DOGNDS\String\Base32;

class SHA1Identifier
{
	protected static $instance;
	public static function getInstance() {
		if( self::$instance === null ) self::$instance = new self();
		return self::$instance;
	}
	
	public function __invoke( $obj ) {
		return "urn:sha1:".Base32::encode(mhash(MHASH_SHA1,(string)$obj));
	}
}
