<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\Base\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;

/**
 * リクエスト
 */
class ProxyRequest extends Proxy\Request
{
    public function __construct (Proxy\ProxyHandlerIF $m = null)
    {
        if ($m) {
            $this->setHandler($m);
        }
    }

    public function factory ($class)
    {
        $request = Util::ClassName($class)->newInstance($this->handler());
        $request->params = clone $this->params;
        return $request;
    }

    public function showParams ( )
    {
        var_dump($this->params);
    }

}
