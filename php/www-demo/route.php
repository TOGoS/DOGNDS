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

if( preg_match( '#(?:^|/)N2R\?(.*)$#', $resourceName, $bif ) ) {
	$viewType = 'raw';
	$uri = $bif[1];
	$name = 'anonymous-blob';
} else if( preg_match( '#^([^/]+)/([^/]+)/([^/]+)$#', $resourceName, $bif ) ) {
	$viewType = $bif[1];
	$uri = urldecode($bif[2]);
	$name = $bif[3];
} else {
	$viewType = 'none';
	$uri = 'none';
	$name = 'none';
}

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

//print_r( $person );
//echo "include_path = ".ini_get('include_path');
//echo "You requested to $viewType $uri as $name";

return true;
