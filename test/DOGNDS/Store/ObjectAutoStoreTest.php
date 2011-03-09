<?php

namespace DOGNDS\Store;

abstract class ObjectAutoStoreTest extends AutoStoreTest
{
	protected abstract function createStoreableObjects();
	
	public function testStoreGetObject() {
		foreach( $this->createStoreableObjects() as $obj ) {
			$uri = $this->store->store( $obj );
			$this->assertEquals( $obj, $this->store->get($uri) );
			echo "Checked $uri\n";
		}
	}
}
