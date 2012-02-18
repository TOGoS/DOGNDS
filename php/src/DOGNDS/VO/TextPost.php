<?php

namespace DOGNDS\VO;

class TextPost
{
	protected $title;
	protected $text;
	
	public function __construct( $title, $text ) {
		$this->title = $title;
		$this->text = $text;
	}
	
	public function getTitle() {  return $this->title;  }
	public function getText() {  return $this->text;  }
}
