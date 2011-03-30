<?php

namespace DOGNDS\HL;

use DOGNDS\Store\Source;
use DOGNDS\Store\Flushable;
use DOGNDS\Store\ObjeectAccessor;

/**
 * High-level methods for creating posts and linking to personas.
 */
interface PersonaAccessor extends Source, Flushable
{
	//// Get stuff
	
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
	
	/**
	 * Returns an iterator that will give all the updatable
	 * friend IDs for the specified persona
	 */
	public function getFriendIds( $personaId );
	
	//// Modify stuff
	
	/** Returns a post object or a Ref targetting one */
	public function createPost( $subject, $content, $metadata=array() );
	
	/** Returns a link object */
	public function createLink( $post, $metadata=array() );
	
	/** Attach a link to a persona (i.e. this persona 'shares' the link target) */
	public function attachLink( $personaId, $link );
}
