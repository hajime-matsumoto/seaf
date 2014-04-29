<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

use Seaf\Container;
use Seaf\Base;

/**
 * PHP関数のラッパー
 */
class PHPFunction extends Container\MethodContainer
{
    use Base\SingletonTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct ( )
    {
    }

    public function __call ($name, $params)
    {
        $this->callFunction($name, $params);
    }

    public static function __callStatic ($name, $params)
    {
        return static::getSingleton()->callFunction($name, $params);
    }

    public function __invoke($name)
    {
        $params = array_slice(func_get_args(), 1);

        return $this->callFunction($name, $params);

    }

    public function callFunction($name, $params)
    {
        // オーバーライドされていたら、オーバライドされているクロージャをコールする
        if ($this->hasMethod($name)) {
            return $this->callMethodArray($name, $params);
        }

        // exitって関数じゃなくて構文なので追加処理
        if ($name == 'exit') {
            exit($params[0]);
        }elseif (function_exists($name)) { // 何も設定されていなければ素のPHP関数をコールする
            return call_user_func_array($name, $params);
        }
    }
}
