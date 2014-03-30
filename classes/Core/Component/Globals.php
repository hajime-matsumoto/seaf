<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf\Data\Container;

/**
 * スーパーグローバル変数を取得する
 *
 * このクラスをかませる事で単体テストを可能にする
 */
class Globals extends Container\ArrayContainer
{
    public function __construct ( )
    {
        parent::__construct( );

        /**
         * これで取得できない
        foreach (self::$keys as $k) {
            $this->data[$k] = $$k;
        }
         */
        $argc = isset($GLOBALS['argc']) ? $GLOBALS['argc']: 0;
        $argv = isset($GLOBALS['argv']) ? $GLOBALS['argv']: [];
        $this->data = [
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
    }


    /**
     * ヘルパメソッド
     *
     * @param string $name = null
     * @return mixed
     */
    public function helper ($name = null, $default = null)
    {
        if ($name == null) return $this;
        return $this->get($name, $default);
    }

}
