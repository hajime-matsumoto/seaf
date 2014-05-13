<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\Base\Proxy;

/**
 * リクエストIF
 */
interface ProxyResultIF
{
    public function set($value);
    public function retrive();
    public function setError($msg);
    public function getError();
    public function isError();
}
