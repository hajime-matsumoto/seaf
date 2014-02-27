<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Annotation Helper
 */

namespace Seaf\Util;

use Seaf\Annotation\AnnotationClass;

use ReflectionMethod;
use ReflectionClass;

/**
 * アノテーションヘルパー
 */
class AnnotationHelper
{
    static $annotation_cache = array();

    public static function get( $class )
    {
        if(is_object($class))
        {
            $class = get_class($class);
        }
        if(isset($annotation_cacne[$class]))
        {
            return $annotation_cacne[$class];
        }
        return $annotation_cacne[$class] = new AnnotationClass( $class );
    }


    public static function getClassAnnotation( $class )
    {
        $class = new ReflectionClass( $class );
        $comment = $class->getDocComment();
        return(self::getAnnotation($comment));
    }

    /**
     * Doc形式のコメントからアノテーションを返す
     *
     * @param string 
     */
    public static function getAnnotation( $comment )
    {
        $annotation = array();

        // 改行で分割
        $lines = explode("\n", $comment);
        for( $i = 0; $i<count($lines); $i++ )
        {
            // いらない部分を捨てる ( /, *, and \n)
            $line = ltrim( trim($lines[$i],"\n "), " *");

            // 先頭に@がなければアノテーションじゃない
            $isAnnotation = !empty($line) && $line[0] == "@";

            if( !$isAnnotation ) continue;

            // @key[space]$valueのはず
            list($key, $value) = preg_split('/[\s]+/', substr($line,1), 2);
            if( !$value ) var_dump($line);

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
        return $annotation;
    }

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

            $annotation = self::getAnnotation($comment);
            if(!empty($annotation))
            {
                $annotations[$method->getName()]  = $annotation;
            }
        }
        return $annotations;
    }
}
