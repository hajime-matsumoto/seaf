<?php

namespace Seaf\Util;

class ArrayHelper
{
	static public function get($target, $key, $default = null)
	{
		if( !is_array($target) ) return $default;
		return isset($target[$key]) ? $target[$key]: $default;
	}

	/**
	 * Get Var Like a.b.c ad a[b][c]
	 */
	static public function parseGet( array $array, $key, $default = false )
	{
		$data =  self::_parseGet( $key, $array );
		return $data ? $data: $default;
	}

	static private function _parseGet( $key, &$ref )
	{
		if( false === ($pos = strpos($key, '.')) )
		{
			return self::get($ref, $key, false);
		}
		else
		{
			$next_key = substr($key, $pos+1);
			return self::_parseGet( 
				$next_key, $ref[substr($key,0,$pos)]
			);
		}
	}

	/**
	 * Set Var Like a.b.c
	 */
	static public function parseSet( array &$array, $key, $value )
	{
		return self::_parseSet( $key, $value, $array );
	}

	static private function _parseSet( $key, $value, &$ref )
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
					self::_parseSet( $k, $v, $ref[$key] );
				}
			}
		}
		else
		{
			return self::_parseSet(
				substr($key, $pos+1),
				$value ,
				$ref[substr($key,0,$pos)]
			);
		}
	}
}
