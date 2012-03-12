<?php

class DOGNDS_XML_NSStackEntry
{
	public $parent;
	public $namespaces;
	
	public function __construct( $parent ) {
		$this->parent = $parent;
		$this->namespaces = $parent === null ? array() : $parent->namespaces;
	}
}

class DOGNDS_XML_XMLNamespacifier
{
	protected $nsStack;
	public $next;
	
	public function __construct( $next ) {
		$this->nsStack = new DOGNDS_XML_NSStackEntry( null );
		$this->next = $next;
	}
	
	public function text( $n ) {
		$this->next->text( $n );
	}
	
	protected function namespacify( $shortName ) {
		if( preg_match('#^([^:]+):(.+)$#',$shortName,$bif) ) {
			$nsAbbrev = $bif[1];
			$shortName = $bif[2];
			$ns = @$this->nsStack->namespaces[$nsAbbrev];
			if( $ns === null ) {
				throw new Exception("Could not resolve $nsAbbrev namespace (used in $nsAbbrev:$shortName)");
			}
		} else {
			$ns = @$this->nsStack->namespaces[0];
		}
		return $ns.$shortName;
	}
	
	public function openTag( $n, $attrList ) {
		$this->nsStack = new DOGNDS_XML_NSStackEntry( $this->nsStack );
		foreach( $attrList as $attr ) {
			if( $attr->name == 'xmlns' ) {
				$this->nsStack->namespaces[0] = $attr->value;
			} else if( preg_match( '#^xmlns:(.+)$#', $attr->name, $bif ) ) {
				$this->nsStack->namespaces[$bif[1]] = $attr->value;
			}
		}
		
		$nsName = $this->namespacify($n);
		$nsAttrs = array();
		foreach( $attrList as $attr ) {
			if( !preg_match('#^xmlns(:|$)#',$attr->name) ) {
				$nsAttrs[] = new DOGNDS_XML_XMLAttribute( $this->namespacify($attr->name), $attr->value );
			}
		}
		
		$this->next->openTag( $nsName, $nsAttrs );
	}
	
	public function closeTag( $n ) {
		$this->next->closeTag( $this->namespacify($n) );
		$this->nsStack = $this->nsStack->parent;
	}
}
