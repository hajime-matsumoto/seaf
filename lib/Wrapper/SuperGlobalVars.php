<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

use Seaf\Container;
use Seaf\Base;

/**
 * スーパーグローバル変数用のラッパー
 */
class SuperGlobalVars extends Container\ArrayContainer
{

    use Base\SingletonTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct ( )
    {
        $argc = isset($GLOBALS['argc']) ? $GLOBALS['argc']: 0;
        $argv = isset($GLOBALS['argv']) ? $GLOBALS['argv']: [];

        $data = [
            '_SERVER'  => $_SERVER,
            '_GET'     => $_GET,
            '_POST'    => $_POST,
            '_FILES'   => $_FILES,
            '_REQUEST' => $_REQUEST,
            '_ENV'     => $_ENV,
            '_COOKIE'  => $_COOKIE,
            'argc'     => $argc,
            'argv'     => $argv
        ];

        // コンテナのイニシャルデータとして登録する
        parent::__construct($data);
    }
}
