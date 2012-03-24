<?php

namespace DOGNDS\Store;

interface Source
{
	/**
	 * Indicate that you will be requesting the object(s) identified by
	 * $uris soon so that this source can optimize access.
	 * @param array $uris a list of URIs to precache
	 */
	public function precache( $uris );
	
	/**
	 * Returns the object identified by $uri
	 * @param boolean $noWait - if true, will return null instead of waiting
	 *   for 'slow' operations to complete.
	 * @return mixed the object identified by URI, or null if it was not found
	 */
	public function get( $uri, $noWait=false );
}
