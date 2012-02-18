<?php

namespace DOGNDS\Util;

class Autoloader
{
	function leDie( $message ) {
		echo $message,"\n";
		foreach( debug_backtrace() as $r ) {
			$file = @$r['file'];
			$line = @$r['line'];
			echo "  $file:$line\n";
		}
		exit(1);
	}
	
	function classDefined($className) {
		return class_exists($className,false) || interface_exists($className,false);
	}
	
	function __invoke($className) {
		$idirs = explode(PATH_SEPARATOR,ini_get('include_path'));
		$path = str_replace(array('\\','_'),DIRECTORY_SEPARATOR,$className).'.php';
		foreach( $idirs as $idir ) {
			if( file_exists($f = $idir.DIRECTORY_SEPARATOR.$path) ) {
				require_once $f;
				$usClassName = str_replace('\\','_',$className);
				$bsClassName = str_replace('_','\\',$className);
				if( !$this->classDefined($className,false) && $this->classDefined($usClassName,false) ) {
					class_alias($usClassName,$className);
				}
				if( !$this->classDefined($className,false) && $this->classDefined($bsClassName,false) ) {
					class_alias($bsClassName,$className);
				}
				if( !$this->classDefined($className,false) ) {
					$this->leDie("$f did not define class '$className'");
				}
				return;
			}
		}
		$this->leDie("Could not find $path on include path: ".ini_get('include_path'));
	}
}
