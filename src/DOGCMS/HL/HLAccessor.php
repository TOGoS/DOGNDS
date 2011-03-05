<?php

namespace DOGCMS\HL;

interface HLAccessor
{
	/** Returns a URI identifying the post object */
	public function createPost( $subject, $content, $metadata=array() );
	
	/** Returns a URI identifying the link object */
	public function createLink( $postUri, $metadata=array() );
	
	/**
	 * Returns the updatable (probably public key-based) URI
	 * identifying the named person
	 */
	public function getPersonaId( $nickname );
	
	/**
	 * Returns an object that can be iterated over
	 * to get Link objects
	 * @param mixed $personaIds a single persona URI or an array of them
	 * @param array $tags an array of tags, all of which must be present
	 *   in a link's metadata for the link to be included in the result
	 */
	public function getLinksByTag( $personaIds, $tags );
	
	/** Indicate that you will be requesting the object identified by $uri soon */
	public function precacheObject( $uri );
	/**
	 * Fetches and returns the object identified by $uri
	 * @param boolean $force - if false, may not return the object if it is not
	 *   cached locally
	 */
	public function getObject( $uri, $force=true );
	
	/** Store the given object and returns its URI */
	public function storeObject( $object );
	
	/**
	 * Returns an iterator that will give all the updatable
	 * friend IDs for the specified persona
	 */
	public function getFriendIds( $personaId );
}
