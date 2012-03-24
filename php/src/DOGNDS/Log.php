<?php

class DOGNDS_Log__Frame
{
	public function __construct( $parent ) {
		$this->parent = $parent;
		$this->messages = array();
	}
	
	public $parent;
	public $messages;
}

class DOGNDS_Log
{
	protected static $frame;
	
	public static function getFrame() {
		if( self::$frame === null ) self::$frame = new DOGNDS_Log__Frame(null);
		return self::$frame;
	}
	
	public static function push() {
		self::$frame = new DOGNDS_Log__Frame( self::getFrame() );
	}
	
	public static function merge( $prefix='  ' ) {
		$oldFrame = self::$frame;
		self::discard();
		foreach( $oldFrame->messages as $m ) {
			self::$frame->messages[] = $prefix.$m;
		}
	}
	
	public static function discard() {
		self::$frame = self::$frame->parent;
	}
	
	/**
	 * Removes the current frame by either merging its messages into the parent frame
	 * (if cond is true) or discarding it (otherwise).  If the condition is true and
	 * $extraMessage is given, it will be added to the parent frame before the merged
	 * messages.
	 */
	public static function mergeOrDiscard( $cond, $extraMessage=null, $prefix='  ' ) {
		if( $cond ) {
			if( $extraMessage ) self::$frame->parent->messages[] = $extraMessage;
			self::merge( $prefix );
		} else {
			self::discard();
		}
	}
	
	public static function log( $message ) {
		self::getFrame()->messages[] = $message;
	}
	
	public static function getMessages() {
		return self::getFrame()->messages;
	}
}
