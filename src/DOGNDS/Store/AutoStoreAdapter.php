<?php

namespace DOGNDS\Store;

class AutoStoreAdapter implements AutoStore, Source
{
	protected $idFunction;
	protected $store;
	
	public function __construct( $idFunction, $store ) {
		$this->idFunction = $idFunction;
		$this->store = $store;
	}
	
	public function precache( $keys ) {
		$this->store->precache( $keys );
	}
	
	public function get( $key, $noWait=false ) {
		return $this->store->get( $key, $noWait );
	}
	
	public function store( $object ) {
		$key = call_user_func($this->idFunction,$object);
		$this->store->put( $key, $object );
		return $key;
	}
	
	public function flush() {
		$this->store->flush();
	}
}
