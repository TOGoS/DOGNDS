<?php

interface DOGNDS_XML_XMLConsumer
{
	public function text( $text );
	public function openTag( $name, $attrList );
	public function closeTag( $name );
}
