<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Types;

/**
 * ディクショナリ型のインターフェイス
 */
interface DictionaryIF
{
    // 通常の挙動
    public function set($name, $value = null);
    public function get($name, $default = null);
    public function clear();
    public function has($name);
    public function isEmpty();

    // キュー的な処理
    public function append($name);
    public function prepend($name);
    public function pop( );
    public function shift( );
}
