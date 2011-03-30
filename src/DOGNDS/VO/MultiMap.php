<?php

namespace DOGNDS\VO;

use ArrayAccess;
use Exception;

class MultiMap implements ArrayAccess
{
	public static function from( $from ) {
		if( $from instanceof MultiMap ) return $from;
		else return new MultiMap($from);
	}
	
	public function __construct( $from=array() ) {
		if( $from instanceof MultiMap ) {
			$this->data = $from->data;
		} else if( is_array($from) ) {
			foreach( $from as $k=>$v ) {
				$this->put($k,$v);
			}
		} else {
			throw new Exception("Don't know how to create MultiMap from ".gettype($from));
		}
	}
	
	protected $data = array();
	
	public function add( $k, $v ) {
		if( isset($this->data[$k]) ) $this->data[$k] = array();
		$this->data[$k][] = $v;
	}
	
	public function get( $k ) {
		if( isset($this->data[$k]) ) {
			foreach( $this->data[$k] as $v ) return $v;
		} else {
			return null;
		}
	}
	
	public function put( $k, $v ) {
		$this->data[$k] = array($v);
	}
	
	/**
	 * @param string $k attribute name
	 * @param mixed $v an array of values OR an object that somehow represents the set of all values
	 */
	public function putAll( $k, $v ) {
		$this->data[$k] = $v;
	}
	
	public function getAll( $k ) {
		if( isset($this->data[$k]) ) {
			return $this->data[$k];
		} else {
			return array();
		}
	}
	
	// ArrayAccess
	
	public function offsetExists( $k ) {
		return true;
	}
	
	public function offsetGet( $k ) {
		return $this->get($k);
	}
	
	public function offsetSet( $k, $v ) {
		$this->put( $k, $v );
	}
	
	public function offsetUnset( $k ) {
		unset($this->data[$k]);
	}
}
