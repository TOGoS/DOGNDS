<?php

namespace DOGNDS\Expression;

use DOGNDS\VO\MultiMap;

class ActiveExpression
{
	public $functionName;
	public $argExpressions;
	
	/**
	 * @param string $functionName
	 * @param MultiMap $argExpressions
	 */
	public function __construct( $functionName, MultiMap $argExpressions ) {
		$this->functionName = $functionName;
		$this->argExpressions = $argExpressions;
	}
}
