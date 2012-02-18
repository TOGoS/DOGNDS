<?php

namespace DOGNDS\Expression;

use PHPUnit\Framework\TestCase;
use DOGNDS\VO\MultiMap;

class URIParserTest extends TestCase
{
	protected $uriParser; 
	
	protected function parse($uri) {
		if( $this->uriParser === null ) $this->uriParser = new URIParser();
		return $this->uriParser->parse($uri);
	}
	
	public function testParseDataUris() {
		$this->assertEquals(
			new ValueExpression("Hello, world!", null),
			$this->parse("data:,".urlencode("Hello, world!"))
		);
		$this->assertEquals(
			new ValueExpression("Hello, world!", null),
			$this->parse("data:base64,".base64_encode("Hello, world!"))
		);
		$this->assertEquals(
			new ValueExpression("<p>Hello, world!</p>", "text/html"),
			$this->parse("data:text/html;base64,".base64_encode("<p>Hello, world!</p>"))
		);
		$this->assertEquals(
			new ValueExpression("<p>Hello, world!</p>", "text/html; charset=utf-8"),
			$this->parse("data:text/html;charset=utf-8;base64,".base64_encode("<p>Hello, world!</p>"))
		);
	}
	
	public function testParseUnparseableUris() {
		$this->assertEquals(
			new URIExpression("http://www.example.com/Something"),
			$this->parse("http://www.example.com/Something")
		);
	}
	
	public function testParseActiveUris() {
		$mm1 = new MultiMap();
		$mm1->add("x",new ActiveExpression("xyz", new MultiMap(array("+:%"=>new ValueExpression("hi")))));
		$mm1->add("x",new ValueExpression("+&%"));
		$mm1->add("y",new ValueExpression("hi"));
		
		$this->assertEquals(
			new ActiveExpression("a+c",$mm1),
			$this->parse("active:".urlencode("a+c").
				"+x@".urlencode("active:xyz+".urlencode("+:%")."@data:,hi").
				"+x@".urlencode("data:base64,".base64_encode("+&%")).
				"+y@".urlencode("data:,hi")
			)
		);
	}
}
