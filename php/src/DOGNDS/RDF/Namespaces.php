<?php

class DOGNDS_RDF_Namespaces
{
	const RDF_NS            = 'http://www.w3.org/1999/02/';
	const RDF_TYPE_PROPERTY = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';
	
	const MDS_NS            = 'http://ns.earthit.com/MDS/';
	
	/*
	public static function stripNs( $name, $ns='' ) {
		if( substr($name,0,strlen($ns)) == $ns ) {
			return substr($name,strlen($ns));
		} else {
			return $name;
		}
	}
	
	public static function stripMdsNs( $name ) {
		return self::stripNs( $name, DOGNDS_RDF_Namespaces::MDS_NS );
	}
	*/
	
	public static function stripAnyNs( $name ) {
		if( preg_match( '<([^#/]+)$>', $name, $bif ) ) {
			return $bif[1];
		}
	}
}
