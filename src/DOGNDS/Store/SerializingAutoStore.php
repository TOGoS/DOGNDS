<?php

namespace DOGNDS\Store;

abstract class SerializingAutoStore implements Source, AutoStore
{
	protected abstract function serialize( $obj );
	protected abstract function unserialize( $str );
	
	/** Return a URI indicating the result of unserializing $uri */
	protected abstract function wrapUri( $uri );
	/** Return null if the URI does not indicate a serialized object */
	protected abstract function unwrapUri( $uri );
	
	protected $next;
	
	public function __construct( $next ) {
		$this->next = $next;
	}
	
	public function precache( $uris ) {
		if( is_scalar($uris) ) $uris = array($uris);
		foreach( $uris as $uri ) {
			$wrappedUri = $this->unwrapUri($uri);
			if( $wrappedUri === null ) {
				$this->next->precache( $uri );
			} else {
				$this->next->precache( $wrappedUri );
			}
		}
	}
	
	public function get( $uri, $noWait=false ) {
		$wrappedUri = $this->unwrapUri($uri);
		if( $wrappedUri === null ) {
			return $this->next->get( $uri );
		} else {
			$data = $this->next->get( $wrappedUri, $noWait );
			if( $data === null ) return null;
			return $this->unserialize( $data );
		}
	}
	
	public function store( $obj ) {
		if( is_string($obj) ) {
			return $this->next->store($obj);
		} else {
			return $this->wrapUri( $this->next->store( $this->serialize($obj) ) );
		}
	}
	
	public function flush() {
		$this->next->flush();
	}
}
