<?php

namespace DOGNDS\Expression;

class ValueExpression
{
	public $value;
	public $dataFormat;
	
	public function __construct( $value, $dataFormat=null ) {
		$this->value = $value;
		$this->dataFormat = $dataFormat;
	}	
}
