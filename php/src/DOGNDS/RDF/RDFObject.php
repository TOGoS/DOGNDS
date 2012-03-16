<?php

class DOGNDS_RDF_RDFObject implements DOGNDS\MultiMap
{
	protected $props = array();
	
	const RDF_TYPE_PROPERTY = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';
	
	public function __construct( $className=null, $propMap=array() ) {
		if( $className !== null ) {
			$this->add( self::RDF_TYPE_PROPERTY, new DOGNDS_RDF_URIRef($className) );
		}
		foreach( $propMap as $k=>$v ) {
			$this->add( $k, $v );
		}
	}
	
	public function getKeys() {
		return array_keys($this->props);
	}
	
	public function add( $k, $v ) {
		if( !isset($this->props[$k]) ) {
			$this->props[$k] = array();
		}
		$this->props[$k][] = $v;
	}
	
	public function addAll( $k, $v ) {
		if( !isset($this->props[$k]) ) {
			$this->props[$k] = array();
		}
		foreach( $v as $v ) {
			$this->props[$k][] = $v;
		}
	}
	
	public function setAll( $k, $v ) {
		$this->props[$k] = $v;
	}
	
	public function getAll( $k ) {
		return isset($this->props[$k]) ? $this->props[$k] : array();
	}
	
	public function getRdfTypeName() {
		$ref = $this[self::RDF_TYPE_PROPERTY];
		return $ref === null ? null : $ref->getUri();
	}
	
	//// Array access for singular properties ////
	
	public function offsetExists( $k ) {
		return isset($this->props[$k]) && count($this->props[$k]) > 0;
	}
	
	public function offsetSet( $k, $v ) {
		$this->props[$k] = array($v);
	}
	
	public function offsetGet( $k ) {
		if( isset($this->props[$k]) ) {
			foreach( $this->props[$k] as $v ) return $v;
		}
		return null;
	}
	
	public function offsetUnset( $k ) {
		unset($this->props[$k]);
	}
}
