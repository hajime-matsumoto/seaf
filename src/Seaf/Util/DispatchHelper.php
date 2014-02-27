<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * ディスパッチクラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Util;

use Seaf\Util\Exception\DispatchException;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;


/**
 * ディスパッチヘルパー
 */
class DispatchHelper
{
    /**
     * 関数をディスパッチする
     *
     * @param mixed  $function
     * @param array  $params params
     * @return mixed
     */
    static public function dispatch($function, $params ) 
    {
        if( is_array($function) )
        {
            list($class, $method) = $function;
            if( !is_callable($function) )
            {
                throw new DispatchException(
                    "%sから%sというメソッドは呼び出せません。",
                    $class,
                    $method
                );
            }
        }
        return call_user_func_array( $function, $params );
    }

    /**
     * メソッドをディスパッチする
     */
    public static function invokeMethodArgs( $class, $method, $params)
    {
        try
        {
            $function = new ReflectionMethod( $class, $method );
        }
        catch( ReflectionException $e )
        {
            throw new DispatchException(
                "%sの%sというメソッドが呼び出せません。\n %s", get_class($class), $method, (string) $e
            );
        }
        return $function->invokeArgs( $class, $params );
    }

    /**
     * 関数をディスパッチする
     */
    public static function invokeArgs( $function, $params)
    {
        if( is_array($function) )
        {
            list( $class, $method ) = $function;
            return self::invokeMethodArgs( $class, $method, $params );
        }

        try
        {
            $function = new ReflectionFunction( $function );
            return $function->invokeArgs(  $params );
        }
        catch( ReflectionException $e )
        {
            throw new DispatchException(
                "%sというメソッドが呼び出せません。", $function
            );
        }
    }
}
