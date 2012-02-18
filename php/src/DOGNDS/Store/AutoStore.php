<?php

namespace DOGNDS\Store;

interface AutoStore extends Flushable
{
	/**
	 * Store the given object and returns its key
	 */
	public function store( $object );
}
