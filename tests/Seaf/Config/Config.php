<?php

namespace Seaf\Config;

use Seaf\Loader\FileSystemLoader;

class Config
{
	private $fileLoader;

	public function setFileLoader( FileSystemLoader $loader )
	{
		$this->fileLoader = $loader;
	}

	public function getFileLoader( )
	{
		if( $this->fileLoader ) return $this->fileLoader;
		$this->fileLloader = new FileSystemLoader('.');
	}

	public function loadPHPFile( $filename )
	{
		$path = $this->getFileLoader( )->getPath( $filename );
		$data = include $path;
		var_dump( $data );
	}
}
