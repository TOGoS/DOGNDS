<?php

namespace DOGNDS\Store;

class PHPSerializingAutoStore extends SerializingAutoStore
{
	protected function serialize( $obj ) {
		return serialize($obj);
	}
	
	protected function unserialize( $str ) {
		return unserialize($str);
	}
	
	protected function wrapUri( $uri ) {
		return "x-struct:".$uri;
	}
	
	protected function unwrapUri( $uri ) {
		if( preg_match('/^x-struct:(.*)/',$uri,$bif) ) {
			return $bif[1];
		} else {
			return null;
		}
	}
}
