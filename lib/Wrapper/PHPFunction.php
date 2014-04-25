<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

use Seaf\Container;

/**
 * PHP関数のラッパー
 */
class PHPFunction extends Container\MethodContainer
{

    public function __construct ( )
    {
    }

    public function __invoke($name)
    {
        $params = array_slice(func_get_args(), 1);

        // オーバーライドされていたら、オーバライドされているクロージャをコールする
        if ($this->hasMethod($name)) {
            return $this->callMethodArray($name, $params);
        }

        // 何も設定されていなければ素のPHP関数をコールする
        if (function_exists($name)) {
            return call_user_func_array($name, $params);
        }
    }
}
