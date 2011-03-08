<?php

namespace DOGNDS\Store;

use Exception;
use DOGNDS\String\Blob;

class FileStore implements Source, Store
{
	protected $dir;
	protected $keyTranslator;
	
	public function __construct( $dir, $keyTranslator ) {
		$this->dir = $dir;
		$this->keyTranslator = $keyTranslator;
	}
	
	protected function validate( $path ) {
		if( preg_match('#(^|\/|\\\\)\.\.($|\/|\\\\)|^\/|^\\\\|[a-zA-Z]\:[\/\\\\]#',$path) ) {
			throw new Exception("Path component contains unsafe sequences: ".$path);
		}
		return $path;
	}
	
	protected function keyToPath( $key ) {
		return $this->dir . '/' . $this->validate( call_user_func($this->keyTranslator, $key ) );
	}
	
	public function precache( $uris ) {}
	
	public function get( $key, $force=true ) {
		$fn = $this->keyToPath($key);
		if( file_exists($fn) ) {
			return file_get_contents($fn);
		} else {
			return null;
		}
	}
	
	public function put( $key, $value ) {
		$fn = $this->keyToPath($key);
		$dir = dirname($fn);
		if( !is_dir($dir) ) mkdir($dir,0777,true);
		$fh = fopen($fn,'wb');
		if( $value instanceof Blob ) {
			foreach( $value as $str ) {
				fwrite( $fh, $str );
			}
		} else {
			fwrite( $fh, $value );
		}
		fclose( $fh );
	}
	
	public function flush() {}
}
