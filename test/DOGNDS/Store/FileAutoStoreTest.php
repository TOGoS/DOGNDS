<?php

namespace DOGNDS\Store;

class FileAutoStoreTest extends AutoStoreTest
{
	protected $tempDir;
	
	protected function createStore() {
		return new AutoStoreAdapter( array($this,'identify'),
			new FileStore(
				$this->tempDir,
				array($this,'translateKeyToFilename')
			)
		);
	}
	
	public function setUp() {
		$this->tempDir = 'temp'.rand(0,9999);
		parent::setUp();
	}
	
	protected function rmdir_r( $d ) {
		if( is_dir($d) ) {
			$dh = opendir($d);
			while( $fe = readdir($dh) ) {
				if( $fe != '.' and $fe != '..' ) {
					$this->rmdir_r("$d/$fe");
				}
			}
			closedir($dh);
			rmdir($d);
		} else {
			unlink($d);
		}
	}
	
	public function tearDown() {
		if( strlen($this->tempDir) > 4 ) {
			$this->rmdir_r($this->tempDir);
		}
		parent::tearDown();
	}
}
