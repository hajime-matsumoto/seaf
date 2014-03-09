<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

/**
 * コマンダー
 */
namespace Seaf\Commander;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use Exception;
use Seaf\Helper\ArrayHelper;

/**
 * コマンド
 */
class Command
{
    private $type, $content, $params;

    public function __construct($type, $content,$params)
    {
        $this->type = $type;
        $this->content = $content;
        $this->params = $params;
    }

    public function help ( )
    {
        return self::funcToString($this->content);
    }

    public function execute ( )
    {
        $args = array_merge($this->params ,func_get_args());
        return self::invokeArgs($this->content, $args);
    }

    public function __toString ( )
    {
        return self::funcToString($this->content);
    }

    public static function factory ($config) 
    {
        $type = ArrayHelper::get($config,'type','closure');
        $content = ArrayHelper::get($config,'content');
        $params = ArrayHelper::get($config,'params', array());
        return new Command($type, $content, $params);
    }

    /** Utility系 **/

    public static function newInstanceArgs ($class, $args) 
    {
        if (!class_exists($class)) {
            throw new Exception("$classは存在しません");
        }

        $rc = new ReflectionClass($class);
        return $rc->newInstanceArgs($args);
    }

    public static function invokeArgs ($func, $args) {
        if (!is_callable($func)) {
            throw new Exception(self::funcToString($func)."はよびだせません");
        }
        return call_user_func_array($func, $args);
    }

    public static function invoke ($func) 
    {
        return self::invokeArgs($func, array_slice(func_get_args(),1));
    }

    public static function funcToString ($func) 
    {
        if (is_array($func)) {
            list($class,$method) = $func;
            $type = is_object($class) ? "->": "::";
            $class_name = $type == "->" ? get_class($class): $class;
            return $class_name.$type.$method;
        } elseif ($func instanceof Closure) {
            $rf = new ReflectionFunction($func);
            return sprintf("{Closure} %s", $rf->getFileName()." line ".$rf->getStartLine());
        } elseif (is_object($func)) {
            return get_class($func);
        }
        return "unknown";
    }
}
