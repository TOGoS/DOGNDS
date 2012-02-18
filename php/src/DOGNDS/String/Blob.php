<?php

namespace DOGNDS\String;

use IteratorAggregate;

/**
 * Gives a sequence of strings when iterated over
 */
interface Blob extends IteratorAggregate
{
	public function subBlob( $offset, $length );
}
