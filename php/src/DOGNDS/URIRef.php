<?php

/**
 * May be implemented by any object representing a resource
 * that has a URI.
 */
interface DOGNDS_URIRef
{
	/**
	 * @return a URI that this resource corresponds to; may be null.
	 */
	public function getUri();
}

class_alias('DOGNDS_URIRef','DOGNDS\\URIRef');