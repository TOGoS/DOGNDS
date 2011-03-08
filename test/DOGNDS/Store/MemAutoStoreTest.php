<?php

namespace DOGNDS\Store;

class MemAutoStoreTest extends AutoStoreTest
{
	protected function createStore() {
		return new AutoStoreAdapter( array($this,'identify'), new MemStore() );
	}
}
