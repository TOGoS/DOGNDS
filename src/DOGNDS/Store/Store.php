<?php

namespace DOGNDS\Store;

interface Store extends Flushable
{
	/**
	 * Store the given object at the given key
	 */
	public function put( $key, $object );
}
