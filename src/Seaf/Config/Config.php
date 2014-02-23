<?php

namespace Seaf\Config;

use Seaf\Loader\FileSystemLoader;
use Seaf\Util\ArrayHelper;

class Config
{
	private $configs = array();
	private $fileLoader;

	public function setFileLoader( FileSystemLoader $loader )
	{
		$this->fileLoader = $loader;
		return $this;
	}

	public function getFileLoader( )
	{
		if( $this->fileLoader ) return $this->fileLoader;
		$this->fileLloader = new FileSystemLoader('.');
	}

	public function loadPHPFile( $filename )
	{
		$data = $this->getFileLoader( )->getPath( $filename );
		$data = include $data;
		$this->setConfigArray( $data );
	}

	public function setConfigArray( $data )
	{
		foreach( $data as $key=>$value )
		{
			$this->setConfig( $key, $value );
		}
	}
	public function setConfig( $key, $value )
	{
		return ArrayHelper::parseSet( $this->configs, $key, $value );
	}

	public function getConfig( $key, $default = null )
	{
		return ArrayHelper::parseGet( $this->configs, $key, $default );
	}
}
