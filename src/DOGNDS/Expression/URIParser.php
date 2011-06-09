<?php

namespace DOGNDS\Expression;

use DOGNDS\Expression\ActiveExpression;
use DOGNDS\Expression\ValueExpression;
use DOGNDS\Expression\URIExpression;
use DOGNDS\VO\MultiMap;

/**
 * Parses URIs into Expression objects
 */
class URIParser
{
	public function parse( $uri ) {
		if( preg_match('/^data:([^,]*),(.*)$/', $uri, $bif ) ) {
			$opts = explode(';',$bif[1]);
			$dataString = $bif[2];
			$base64 = false;
			$charset = null;
			$format = null;
			foreach( $opts as $opt ) {
				if( $opt == 'base64' ) {
					$base64 = true;
				} else if( preg_match('/^charset=(.*)$/',$opt,$bif) ) {
					$charset = $bif[1];
				} else if( preg_match('/^[^=]+$/',$opt) ) {
					$format = $opt;
				}
			}
			if( $charset ) {
				if( !$format ) $format = 'text/plain';
				$format = "$format; charset=$charset";
			}
			if( $base64 ) {
				$data = base64_decode($dataString);
			} else {
				$data = urldecode($dataString);
			}
			return new ValueExpression( $data, $format );
		} else if( preg_match('/^active:(.*)/', $uri, $bif ) ) {
			$parts = explode('+', $bif[1]);
			$args = new MultiMap();
			for( $i=1; $i<count($parts); ++$i ) {
				list($k,$v) = explode('@',$parts[$i]);
				$args->add( urldecode($k), $this->parse(urldecode($v)) );
			}
			return new ActiveExpression( urldecode($parts[0]), $args );
		} else {
			return new URIExpression( $uri );
		}
	}
}
