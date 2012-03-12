<?php

/*
 * Simple, low-level XML parser.
 * Breaks souce into a list of text and tags with no structure.
 * Does NOT validate in any way (behavior when parsing malformed XML
 * is undefined), structure nested tags, or translate prefixes to namespaces.
 * DOES decode entities in text.
 */
class DOGNDS_XML_XMLParser
{
	public $trimText = true;
	protected $xmlConsumer;
	
	protected function decodeText( $text ) {
		return html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	}
	
	public function __construct( $xmlConsumer ) {
		$this->xmlConsumer = $xmlConsumer;
	}
	
	/**
	 * @param string $xml XML source to parse
	 * @param XMLConsumer $c
	 */
	public function parse( $xml ) {
		$c = $this->xmlConsumer;
		
		$tokens = preg_match_all('#<!--.*?-->|<[^>]+>|[^<]+#s',$xml,$matches,PREG_SET_ORDER);
		foreach( $matches as $m ) {
			if( preg_match('#^<!--#',$m[0]) ) {
				// Comment; ignore!
			} else if( preg_match('#^<(/?)([^\s/>]+)\s?(.*?)(/?)>$#s',$m[0],$bif) ) {
				$name = $bif[2];
				$isOpening = $bif[1] == '';
				$isClosing = ($bif[1] == '/' || $bif[4] == '/');
				$attrList = array();
				preg_match_all('#([^\s="]+)="([^"]*)"#',$bif[3],$attrMatches,PREG_SET_ORDER);
				foreach( $attrMatches as $am ) {
					$attrList[] = new DOGNDS_XML_XMLAttribute($am[1],$this->decodeText($am[2]));
				}
				if( $isOpening ) {
					$c->openTag( $name, $attrList );
				}
				if( $isClosing ) {
					$c->closeTag( $name );
				}
			} else {
				$rawText = $m[0];
				if( $this->trimText ) $rawText = trim($rawText);
				if( $rawText != '' ) {
					$c->text( $this->decodeText($rawText) );
				}
			}
		}
	}
}
