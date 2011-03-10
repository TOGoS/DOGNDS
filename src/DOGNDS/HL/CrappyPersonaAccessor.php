<?php

namespace DOGNDS\HL;

use DOGNDS\RDFNames;
use DOGNDS\String\Base32;
use DOGNDS\VO\Link;
use DOGNDS\VO\MultiMap;
use DOGNDS\VO\Profile;
use DOGNDS\VO\TextPost;

class CrappyPersonaAccessor implements PersonaAccessor
{
	protected $storage;
	protected $headStore;
	
	public function __construct( $storage, $headStore ) {
		$this->storage = $storage;
		$this->headStore = $headStore;
	}
	
	//// Get stuff
	
	public function precache( $uris ) {
		$this->storage->precache($uris);
	}
	
	public function get( $uri, $force=true ) {
		return $this->storage->get($uri,$force);
	}
	
	public function getPersonaId( $nickname ) {
		return "person:".Base32::encode(mhash(MHASH_SHA1,(string)$nickname));
	}
	
	protected function getPersonaProfileUri( $personaId ) {
		return $this->headStore->get("$personaId.profile-uri");
	}
	
	protected function getPersonaProfile( $personaId ) {
		return $this->storage->get( $this->getPersonaProfileUri($personaId) );
	}
	
	public function getLinksByTag( $personaIds, $tags ) {
		$leenx = array();
		foreach( $personaIds as $personaId ) {
			$prof = $this->getPersonaProfile( $personaId );
			if( $prof === null ) continue;
			
			foreach( $prof->linkUris as $linkUri ) {
				$link = $this->get($linkUri);
				
				$ltags = $link->getLinkMetadata()->getAll(RDFNames::TAG);
				$ttags = $link->getTargetMetadata()->getAll(RDFNames::TAG);
				foreach( $tags as $t ) {
					if( !in_array($t,$ltags) && !in_array($t,$ttags) ) {
						continue 2;
					} 
				}
				$leenx[] = $link;
			}
		}
		return $leenx;
	}
	
	public function getFriendIds( $personaId ) {
		$prof = $this->getPersonaProfile( $personaId );
		return $prof->friendIds;
	}
	
	
	
	//// Modify stuff
	
	protected $updatablePersonaProfiles = array();
	protected function getUpdatablePersonaProfile( $personaUri ) {
		if( !isset($this->updatablePersonaProfiles[$personaUri]) ) {
			$profile = $this->getPersonaProfile($personaUri);
			if( $profile === null ) {
				$profile = new Profile();
			}
			$this->updatablePersonaProfiles[$personaUri] = $profile;
		}
		return $this->updatablePersonaProfiles[$personaUri];
	}
	
	protected function setPersonaProfileUri( $personaId, $profileUri ) {
		$this->headStore->put("$personaId.profile-uri", $profileUri);
	}
	
	protected function setPersonaProfile( $personaId, $profile ) {
		$this->headStore->put("$personaId.profile-uri", $this->storage->store($profile));
	}
	
	
	
	public function createPost( $subject, $content, $metadata=array() ) {
		return $this->storage->store( new TextPost($subject,$content,$metadata) );
	}
	
	public function createLink( $postUri, $linkMetadata=array(), $targetMetadata=array() ) {
		if( @$linkMetadata[RDFNames::CREATED] === null ) {
			$linkMetadata = new MultiMap($linkMetadata);
			$linkMetadata[RDFNames::CREATED] = gmdate('Y-m-d H:i:s GMT');
		}
		$link = new Link( $postUri, $linkMetadata, $targetMetadata );
		return $this->storage->store( $link );
	}
	
	public function attachLink( $personaId, $linkUri ) {
		$profile = $this->getUpdatablePersonaProfile($personaId);
		$profile->linkUris[$linkUri] = $linkUri;
	}
	
	public function flush() {
		foreach( $this->updatablePersonaProfiles as $personaId=>$profile ) {
			$this->setPersonaProfile($personaId,$profile);
		}
		$this->updatablePersonaProfiles = array();
	}
}
