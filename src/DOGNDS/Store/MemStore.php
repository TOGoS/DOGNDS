<?php

namespace DOGNDS\Store;

class MemStore implements Source, Store
{
	protected $store = array();
	
	public function precache( $uris ) {}
	
	public function get( $key, $force=true ) {
		return @$this->store[$key];
	}
	
	public function put( $key, $value ) {
		$this->store[$key] = $value;
	}
	
	public function flush() {}
}
