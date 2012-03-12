<?php

class DOGNDS_RDF_ObjectStack {
	public $parent;
	public $subject;
	public $propName;
	
	public function __construct( $parent, $subject, $propName=null ) {
		$this->parent = $parent;
		$this->subject = $subject;
		$this->propName = $propName;
	}
}

class DOGNDS_RDF_XMLRDFifier
	implements DOGNDS_XML_XMLConsumer
{
	const MODE_VALUE = 1; // ready to read a value!
	const MODE_PROP  = 2; // inside an object; ready to read a property!
	
	public $objectConstructor;
	protected $objectStack;
	protected $mode = self::MODE_VALUE;
	protected $value;
	protected $propName;
	protected $internalNamedNodes = array(); // nodeID things
	
	public function __construct( $objectConstructor ) {
		$this->objectConstructor = $objectConstructor;
	}
	
	public function getValue() {
		return $this->value;
	}
		
	protected function save() {
		$this->objectStack = new DOGNDS_RDF_ObjectStack( $this->objectStack, $this->value, $this->propName );
		$this->value = null;
		$this->propName = null;
		$this->mode = self::MODE_VALUE;
	}
	protected function restore() {
		$this->value = $this->objectStack->subject;
		$this->propName = $this->objectStack->propName;
		$this->objectStack = $this->objectStack->parent;
		$this->mode = self::MODE_PROP;
	}
	
	public function text( $text ) {
		if( $text == '' ) return;
		if( $this->mode == self::MODE_VALUE ) {
			$this->value = $text;
		}
	}
	public function openTag( $name, $attrList ) {
		$nodeId = null;
		$resourceUri = null;
		foreach( $attrList as $attr ) {
			if( $attr->name == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nodeID' ) {
				$nodeId = $attr->value;
			} else if( $attr->name == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#resource' ) {
				$resourceUri = $attr->value;
			}
		}
		
		if( $this->mode == self::MODE_VALUE ) {
			$this->value = $this->objectConstructor->createObject( $name );
			if( $nodeId ) $this->namedObjects[$nodeId] = $this->value;
			$this->mode = self::MODE_PROP;
		} else {
			// Property!
			$this->propName = $name;
			$this->save();
			foreach( $attrList as $attr ) {
				if( $resourceUri ) {
					$this->value = $this->objectConstructor->resolveResource( $resourceUri );
				} else if( $nodeId ) {
					$this->value = $this->namedObjects[$nodeId];
				}
			}
		}
	}
	public function closeTag( $name ) {
		if( $this->mode == self::MODE_PROP ) {
			// Done reading properties of an object
			$this->value = $this->objectConstructor->closeObject( $this->value );
			$this->mode = self::MODE_VALUE;
		} else {
			// Done reading some value!
			$v = $this->value;
			$this->restore();
			if( $this->propName != $name ) {
				throw new Exception("Property tag mismatch; opened as {$this->propName}, closing as {$name}");
			}
			$this->objectConstructor->addProperty( $this->value, $this->propName, $v );
		}
	}
	
	/**
	 * Convenience method so you don't have to make your own XMLParsers, etc
	 */
	public function parse( $xml ) {
		$namespacifier = new DOGNDS_XML_XMLNamespacifier( $this );
		$xmlparser = new DOGNDS_XML_XMLParser( $namespacifier );
		$xmlparser->parse( $xml );
		return $this->getValue();
	}
}
