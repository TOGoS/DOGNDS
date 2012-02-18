<?php

namespace DOGNDS\Store;

use DOGNDS\Hash\SHA1Identifier;

class MemAutoStoreTest extends AutoStoreTest
{
	protected function createStore() {
		return new AutoStoreAdapter( SHA1Identifier::getInstance(), new MemStore() );
	}
}
