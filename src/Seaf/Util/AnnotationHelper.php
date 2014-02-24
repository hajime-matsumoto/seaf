<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Annotation Helper
 */

namespace Seaf\Util;

use ReflectionMethod;
use ReflectionClass;

class AnnotationHelper
{
	public static function getMethodsAnnotation( $class, $filter = ReflectionMethod::IS_PUBLIC )
	{
		// getMethods Fileter
		//
		// ReflectionMethod::IS_STATIC
		// ReflectionMethod::IS_PUBLIC
		// ReflectionMethod::IS_PROTECTED
		// ReflectionMethod::IS_PRIVATE
		// ReflectionMethod::IS_ABSTRACT
		// ReflectionMethod::IS_FINAL

		$class = new ReflectionClass( $class );

		$annotations = array();

		foreach( $class->getMethods($filter) as $method )
		{
			// コメントブロックの取得
			$comment = $method->getDocComment();

			$annotations[$method->getName()]  = array();
			$annotation =& $annotations[$method->getName()];

			// 改行で分割
			$lines = explode("\n", $comment);
			for( $i = 0; $i<count($lines); $i++ )
			{
				// いらない部分を捨てる ( /, *, and \n)
                $line = ltrim( trim($lines[$i],"\n /"), " *");

				// 先頭に@がなければアノテーションじゃない
				$isAnnotation = !empty($line) && $line[0] == "@";

				if( !$isAnnotation ) continue;

				// @key[space]$valueのはず
				list($key, $value) = explode(' ', substr($line,1), 2);

				// 同じkeyのアノテーションがあった時の対応
				if( isset($annotation[$key]) ) {
					if( !is_array($annotation[$key]) ) {
						$annotation[$key] = array($annotation[$key]);
					}
					$annotation[$key][] = $value;
				}else{
					$annotation[$key] = $value;
				}
			}
		}
		return $annotations;
	}
}
