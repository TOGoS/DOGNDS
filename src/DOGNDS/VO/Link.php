<?php

namespace DOGNDS\VO;

class Link
{
	protected $targetUri;
	protected $metadata;
	
	public function __construct( $targetUri, $metadata ) {
		$this->targetUri = $targetUri;
		$this->metadata = $metadata;
	}
	
	public function getTargetUri() {  return $this->targetUri;  }
	public function getMetadata() {  return $this->metadata;  } 
}
