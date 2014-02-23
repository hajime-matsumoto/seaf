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
		if( $data ) {
			$data = include $data;
			$this->setConfigArray( $data );
		}
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
		$data =  ArrayHelper::parseGet( $this->configs, $key, $default );
		if(!is_string($data)) return $data;
		$self = $this;
		return preg_replace_callback('/\{\{(.*)\}\}/', function($m) use($self){
			return $self->getConfig($m[1]);
		}, $data);
	}
}