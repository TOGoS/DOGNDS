<?php

class DOGNDS_RDF_URIRef implements DOGNDS_URIRef
{
	protected $uri;
	public function __construct( $uri ) {  $this->uri = $uri;  }
	public function getUri() {  return $this->uri;  }
}
