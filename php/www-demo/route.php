<?php

function getIfSet( &$var, $default=null ) {
	return isset($var) ? $var : $default;
}

$dogndsPhpDir = dirname(dirname(__FILE__));
$dogndsDir = dirname($dogndsPhpDir);

ini_set('include_path',"{$dogndsPhpDir}/src".PATH_SEPARATOR.ini_get('include_path'));
require_once 'DOGNDS/Util/Autoloader.php';
spl_autoload_register( new DOGNDS\Util\Autoloader );

$resourceName = substr($_SERVER['REQUEST_URI'],1);

if( preg_match( '#^uri-res/(.*)$#', $resourceName, $bif ) ) {
	$resourceName = $bif[1];
}

if( preg_match( '#(?:^|/)N2R\?(.*)$#', $resourceName, $bif ) ) {
	$viewType = 'raw';
	$uri = $bif[1];
	$name = 'anonymous-blob';
} else if( preg_match( '#^(raw)/([^/]+)/([^/]+)$#', $resourceName, $bif ) ) {
	$viewType = $bif[1];
	$uri = urldecode($bif[2]);
	$name = $bif[3];
} else {
	$viewType = 'none';
	$uri = 'none';
	$name = 'none';
}

switch( $_SERVER['REQUEST_METHOD'] ) {
case('GET'):
	header( "Content-Type: text/plain" );
	echo "You requested to $viewType $uri as $name\n";
	echo "And PATH_INFO is ", $_SERVER['PATH_INFO'], "\n";
	return;
case('PUT'):
	$postedContent = file_get_contents('php://input');
	$verifier = new DOGNDS_Crypto_SHA1RSASignatureVerifier( null );
	
	DOGNDS_Log::push();
	$hashValid = DOGNDS_Hash_HashUtil::verifyHashUrn( $postedContent, $uri );
	DOGNDS_Log::mergeOrDiscard( !$hashValid, "The hash in the URL did not match the calculated hash of the content." );
		
	DOGNDS_Log::push();
	$sigs = $verifier->processContentSignatures( $postedContent, @$_SERVER['HTTP_CONTENT_SIGNATURE'] );
	DOGNDS_Log::mergeOrDiscard( $sigs === false, "There were invalid signatures" );
	
	if( !$hashValid or $sigs === false ) {
		header( "HTTP/1.1 409 bad hashes and/or signatures" );
		header( "Content-Type: text/plain" );
		echo "409!  Your content has been REJECTED for the following raisins:\n";
		foreach( DOGNDS_Log::getMessages() as $m ) {
			echo "* ", $m, "\n";
		}
		return;
	}
	header( "Content-Type: text/plain" );
	echo "Your content is acceptable.  Thank you.\n";
}

/*
$objectConstructor = new DOGNDS\RDF\RDFObjectConstructor();
$rdfifier = new DOGNDS\RDF\XMLRDFifier( $objectConstructor );
$person = $rdfifier->parse( file_get_contents("{$dogndsDir}/demo-data/TOGoS.rdf") );

if( $img = getIfSet($person['http://xmlns.com/foaf/0.1/depiction']) and $img instanceof DOGNDS\URIRef ) {
	echo "<img src=\"".htmlspecialchars($img->getUri())."\"/>\n";
}
if( $name = getIfSet($person['http://xmlns.com/foaf/0.1/name']) ) {
	echo "<h2>", htmlspecialchars($name), "</h2>\n";
}
echo "<dl>\n";
foreach( $person->getKeys() as $k ) {
	echo "<dt>", htmlspecialchars($k), "</dt>\n";
	foreach( $person->getAll($k) as $v ) {
		if( $v instanceof DOGNDS\URIRef ) {
			$url = $v->getUri();
			echo "<dd>", htmlspecialchars($url), "</dd>\n";
		} else if( is_scalar($v) ) {
			echo "<dd>", htmlspecialchars($v), "</dd>\n";
		}
	}
}
echo "</dl>\n";
*/

echo "include_path = ".ini_get('include_path');

return true;
