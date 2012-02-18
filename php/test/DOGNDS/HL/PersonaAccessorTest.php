<?php

namespace DOGNDS\HL;

use DOGNDS\Hash\SHA1Identifier;
use DOGNDS\RDFNames;
use DOGNDS\Store\AutoStoreAdapter;
use DOGNDS\Store\MemStore;
use DOGNDS\Store\PHPSerializingAutoStore;
use PHPUnit\Framework\TestCase;

class PersonaAccessorTest extends TestCase
{
	protected function createPersonaAccessor() {
		return new CrappyPersonaAccessor(
			new PHPSerializingAutoStore( new AutoStoreAdapter(
				SHA1Identifier::getInstance(), new MemStore()
			)),
			new MemStore()
		);
	}
	
	protected function setUp() {
		$this->PA = $this->createPersonaAccessor();
	}
	
	public function testCreatePost() {
		$postUri = $this->PA->createPost( "Test", "This post is a test" );
		$post = $this->PA->get( $postUri );
		$this->assertEquals( "Test", $post->getTitle() );
		$this->assertEquals( "This post is a test", $post->getText() );
	}

	public function testCreatePostLink() {
		$postUri = $this->PA->createPost( "Test", "This post is a test" );
		$post = $this->PA->get( $postUri );
		
		$linkUri = $this->PA->createLink( $postUri,
			array(RDFNames::DESCRIPTION=>"A cool link"),
			array(RDFNames::DESCRIPTION=>"A cool post")
		);
		$link = $this->PA->get( $linkUri );
		$this->assertEquals( $postUri, $link->getTargetUri() );
		$this->assertEquals( "A cool post", $link->getTargetMetadata()->get(RDFNames::DESCRIPTION) );
		$this->assertEquals( "A cool link", $link->getLinkMetadata()->get(RDFNames::DESCRIPTION) );
	}
	
	public function testCreatePersona() {
		$nickName = "Nick";
		$nickId = $this->PA->getPersonaId( $nickName );
		
		$linkCount = 0;
		foreach( $this->PA->getLinksByTag(array($nickId),array('rad')) as $link ) {
			++$linkCount;
		}
		
		$this->assertEquals( 0, $linkCount );

		// Create a new post/link...
		$postUri = $this->PA->createPost( "Test", "This post is a test" );
		$post = $this->PA->get( $postUri );
		$linkUri = $this->PA->createLink( $postUri,
			array(RDFNames::DESCRIPTION=>"A test link", RDFNames::TAG=>"test"),
			array(RDFNames::DESCRIPTION=>"A test post")
		);
		// Now attach...
		$this->PA->attachLink( $nickId, $linkUri );
		$this->PA->flush();
		
		// Look for non-existing tag 'rest':
		foreach( $this->PA->getLinksByTag(array($nickId),array('rest')) as $link ) {
			++$linkCount;
		}
		$this->assertEquals( 0, $linkCount );
		
		// Look for existing tag 'test':
		foreach( $this->PA->getLinksByTag(array($nickId),array('test')) as $link ) {
			$this->assertEquals("A test link", $link->getLinkMetadata()->get(RDFNames::DESCRIPTION));
			++$linkCount;
		}
		$this->assertEquals( 1, $linkCount );
	}
}
