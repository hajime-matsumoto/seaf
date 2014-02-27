<?php
/**
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;

use Seaf\Util\ArrayHelper;
use ReflectionClass;
use ReflectionMethod;

/**
 * 開発用エクステンション
 */
class DevExtension
{
    private $env;

    public function initializeExtension( $prefix, $env )
    {
        $this->env = $env;
    }

    /**
     * 現在の情報をダンプする
     *
     * @SeafBindPrefix false
     * @SeafBind dump
     */
    public function dump()
    {
        $env = $this->env;

        $dump = $env->dump();
        $dump = $this->stringnize($dump);
        return $dump;
    }

    public function stringnize( $array )
    {
        $ret_array = array();
        foreach( $array as $k=>$v )
        {

            if(is_array($v))
            {
                if( is_callable($v))
                {
                    list($class,$method) = $v;
                    $c = new ReflectionClass($class);
                    $m = new ReflectionMethod($c->getName(), $method);
                    $ret_array[$k] = 
                        $c->getName().'::'.
                        $m->getName().' '.
                        basename($m->getFileName()).' '.
                        $m->getStartLine();
                    continue;
                }

                $ret_array[$k] = $this->stringnize($v);
                continue;
            }


            if(is_object($v))
            {
                $c = new ReflectionClass(get_class($v));
                $ret_array[$k] = $c->getName().' '.basename($c->getFileName()).' '.$c->getStartLine();
            }
        }
        return $ret_array;
    }
}
/* vim: set expandtab ts=4 sw=4 sts=4: et*/
