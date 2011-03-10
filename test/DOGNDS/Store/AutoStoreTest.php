<?php

namespace DOGNDS\Store;

use DOGNDS\String\Base32;
use PHPUnit\Framework\TestCase;

abstract class AutoStoreTest extends TestCase
{
	protected abstract function createStore();
	
	public function setUp() {
		$this->store = $this->createStore();
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
	
	public function translateKeyToFilename( $key ) {
		return $this->dupToDir( $this->stripScheme($key), 2 );
	}
	
	public function testStoreGetString() {
		$boogerUri = $this->store->store( "Booger" );
		$this->assertEquals( "Booger", $this->store->get($boogerUri) );
	}
}
