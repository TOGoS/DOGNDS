<?php

namespace DOGNDS\HL;

class CrappyPersonaAccessor implements PersonaAccessor
{
	protected $storage;
	
	public function __construct( $storage ) {
		$this->storage = $storage;
	}
}
