<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\Base\Proxy;

/**
 * ハンドラIF
 */
interface ProxyHandlerIF
{
    /**
     * プロクシの__getのハンドリング
     */
    public function __proxyRequestGet(ProxyRequestIF $request, $name);

    /**
     * プロクシの__callのハンドリング
     */
    public function __proxyRequestCall(ProxyRequestIF $request, $name, $params);
}
