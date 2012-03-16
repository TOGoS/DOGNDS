<?php

class DOGNDS_RDF_RDFObjectConstructor
	implements DOGNDS_RDF_ObjectConstructor
{
	public function createObject( $className ) {
		return new DOGNDS_RDF_RDFObject( $className );
	}
	public function addProperty( $subject, $propName, $value ) {
		$subject->add( $propName, $value );
	}
	public function closeObject( $obj ) {
		return $obj;
	}
	public function resolveResource( $uri ) {
		return new DOGNDS_RDF_URIRef($uri);
	}
}
