<?php
namespace Seaf\Loader;

use Seaf\Loader\Exception\FileDoseNotExist;

class FileSystemLoader
{
	private $paths = array();

	/**
	 * @param mixed $dir null or string or array
	 */
	public function __construct( $paths = array() ) 
	{
		if( is_string($paths) ) $paths = array($paths);

		foreach( $paths as $path )
		{
			$this->appendPath( $path );
		}
	}

	/**
	 * @param string $path
	 */
	public function appendPath( $path )
	{
		array_push($this->paths, $path);
		return $this;
	}

	public function getPath( $filename )
	{
		foreach( $this->paths as $path )
		{
			if(file_exists($path.'/'.$filename))
			{
				return $path.'/'.$filename;
			}
		}
		return false;
	}

	public function open( $filename, $option = 'r')
	{
		if( false !== ($path = $this->getPath( $filename )) )
		{
			return fopen( $path, $option );
		}
		throw new FileDoseNotExist(
			"file $filename dose not exist"
		);
	}

	public function read( $filename )
	{
		$fp = $this->open($filename, 'r');
		$text =  fread($fp, filesize($this->getPath($filename)));
		fclose($fp);
		return $text;
	}
}
