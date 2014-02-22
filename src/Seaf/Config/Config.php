<?php

namespace Seaf\Config;

use Seaf\Loader\FileSystemLoader;

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
			$this->_setConfig( $key, $value, $this->configs );
		}
	}
	public function setConfig( $key, $value )
	{
		return $this->_setConfig($key, $value, $this->configs );
	}

	public function _setConfig( $key, $value, &$ref )
	{
		if( false === ($pos = strpos($key, '.')) )
		{
			if( !is_array( $value ) )
			{
				$ref[$key] = $value;
			}
			else
			{
				if( 
					!isset($ref[$key]) 
					|| 
					!is_array($ref[$key]) 
				) $ref[$key] = array();

				foreach( $value as $k => $v )
				{
					$this->_setConfig( $k, $v, $ref[$key] );
				}
			}
		}
		else
		{
			return $this->_setConfig(
				substr($key, $pos+1),
				$value ,
				$ref[substr($key,0,$pos)]
			);
		}
	}

	public function getConfig( $key )
	{
		return $this->_getConfig( $key, $this->configs );
	}

	public function _getConfig( $key, &$ref )
	{
		if( false === ($pos = strpos($key, '.')) )
		{
			return $ref[$key];
		}
		else
		{
			return $this->_getConfig(
				substr($key, $pos+1),
				$ref[substr($key,0,$pos)]
			);
		}
	}
}
