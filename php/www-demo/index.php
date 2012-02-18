<?php

//// Configuration stuff, move to site-specific file someday ////

ini_set('include_path',dirname(__FILE__).'/../src');
require_once 'DOGNDS/Util/Autoloader.php';
spl_autoload_register( new DOGNDS\Util\Autoloader() );

function handle_except_chin( $e ) {
	echo "<p>Error: ";
	echo htmlspecialchars($e->getMessage());
	echo "</p>";
	echo "<ul>\n";
	foreach( $e->getTrace() as $tl ) {
		echo "<li>", htmlspecialchars(@$tl['file']), ":", @$tl['line'], "</li>\n";
	}
	echo "</ul>\n";
}

set_exception_handler( 'handle_except_chin' );




use DOGNDS\Hash\SHA1Identifier;
use DOGNDS\HL\CrappyPersonaAccessor;
use DOGNDS\Store\AutoStoreAdapter;
use DOGNDS\Store\FileStore;
use DOGNDS\Store\PHPSerializingAutoStore;
use DOGNDS\Store\StandardURNFilenameTranslator;
use DOGNDS\RDFNames;

$dataDir = 'data';
$headDir = 'heads';

function xltStuff( $x ) {
	return str_replace( ':','/',$x );
}

$DS = new PHPSerializingAutoStore(
	new AutoStoreAdapter(
		SHA1Identifier::getInstance(),
		new FileStore($dataDir,StandardURNFilenameTranslator::getInstance())
	)
);
$HS = new FileStore($headDir,'xltStuff');
$PA = new CrappyPersonaAccessor( $DS, $HS );

$fred = $PA->getPersonaId("Fred");
if( $fred === null ) {
	throw new Exception("Fred's persona ID is null!");
}
echo "<p>Fred's persona ID: $fred</p>\n";

$linkCount = 0;
foreach( $PA->getLinksByTag(array($fred),array('test')) as $link ) {
	$linkCount = 1; break;
}
if( $linkCount == 0 ) {
	$post = $PA->createPost( "Blah", "Blah blah blah foo bar" );
	$link = $PA->createLink( $post, array(RDFNames::TAG=>'test') );
	$PA->attachLink( $fred, $link );
	$PA->flush();
}

echo "<p>Links:</p><ul>";
foreach( $PA->getLinksByTag(array($fred),array('test')) as $link ) {
	echo "<li>Leeenk</li>\n";
}
echo "</ul>\n";