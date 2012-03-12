<?php

class DOGNDS_XML_XMLAttribute {
	public $name;
	public $value;
	public function __construct($n,$v) {
		$this->name = $n;
		$this->value = $v;
	}
}
