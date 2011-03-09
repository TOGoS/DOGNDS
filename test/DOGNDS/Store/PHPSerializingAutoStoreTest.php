<?php

namespace DOGNDS\Store;

use DOGNDS\VO\TextPost;

class PHPSerizalizingAutoStoreTest extends ObjectAutoStoreTest
{
	protected function createStore() {
		return new PHPSerializingAutoStore( new AutoStoreAdapter( array($this,'identify'), new MemStore() ) );
	}
	
	protected function createStoreableObjects() {
		return array(
			1, 3.5, array("foo","bar"), "Blah", new TextPost("Something","Oh look text")
		);
	}
}
