<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\BackEnd\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;

/**
 * リクエスト
 */
class ProxyRequest extends Proxy\Request
{
    public function __construct (BackEnd\Module\ModuleMediatorIF $m)
    {
        $this->setHandler($m);
    }

    public function factory ($class)
    {
        $request = Util::ClassName($class)->newInstance($this->handler());
        $request->params = clone $this->params;
        return $request;
    }
}
