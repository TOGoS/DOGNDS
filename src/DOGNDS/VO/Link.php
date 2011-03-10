<?php

namespace DOGNDS\VO;

use DOGNDS\VO\MultiMap;

class Link
{
	protected $targetUri;
	protected $linkMetadata;
	protected $targetMetadata;
	
	public function __construct( $targetUri, $linkMetadata=array(), $targetMetadata=array() ) {
		$this->targetUri = $targetUri;
		$this->linkMetadata = MultiMap::from($linkMetadata);
		$this->targetMetadata = MultiMap::from($targetMetadata);
	}
	
	public function getTargetUri() {  return $this->targetUri;  }
	public function getLinkMetadata() {  return $this->linkMetadata;  }
	public function getTargetMetadata() {  return $this->targetMetadata;  } 
}
