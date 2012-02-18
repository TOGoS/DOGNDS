<?php

namespace DOGNDS\Store;

interface Source
{
	/**
	 * Indicate that you will be requesting the object(s) identified by $uris soon
	 * @param mixed $uris a single URI or an array of URIs of objects to pre-cache
	 */
	public function precache( $uris );
	
	/**
	 * Fetches and returns the object identified by $uri
	 * @param boolean $noWait - if true, will return null instead of waiting
	 *   for 'slow' operations to complete.
	 */
	public function get( $uri, $noWait=false );
}
