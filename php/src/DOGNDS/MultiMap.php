<?php

interface DOGNDS_MultiMap extends ArrayAccess
{
	/** Adds $v to the list of values at $k */
	public function add( $k, $v );
	/** Adds the list of values $v to the set at $k */
	public function addAll( $k, $v );
	/** Replaces the list of values at $k with the array $v */
	public function setAll( $k, $v );
	/** Returns the list of values at $k */
	public function getAll( $k );
}
