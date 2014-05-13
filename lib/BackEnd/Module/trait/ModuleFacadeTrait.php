<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * モジュール
 */
namespace Seaf\BackEnd\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\BackEnd;
use Seaf\Logging;

/**
 * リクエスト
 */
trait ModuleFacadeTrait
{
    use Logging\LoggableTrait;
    use Event\ObservableTrait;

    private $parent;

    public function setParent(ModuleFacadeIF $facade)
    {
        $this->parent = $facade;
    }

    public function closestMediator ( )
    {
        if($parent = $this->getParent())
        {
            return $parent->closestMediator();
        }
        return false;
    }

    public function root ( )
    {
        if ($this->parent) {
            return $this->parent->root();
        }else{
            return $this;
        }
    }

    public function getParent( )
    {
        return $this->parent;
    }

    public function hasParent( )
    {
        return empty($this->parent) ? false: true;
    }

    public function initFacade( )
    {
    }

    public function __proxyRequestGet(Proxy\ProxyRequestIF $request, $name)
    {
        return $this->proxyRequestGet($request, $name);
    }

    final protected function proxyRequestGet(Proxy\ProxyRequestIF $request, $name)
    {
        $new_request = clone $request;
        $this->debug("FACADE|REQUEST",["Add Section %s [%s]", $name, get_class($this)]);
        $new_request->addParam('section', $name);
        return $new_request;
    }

    public function __proxyRequestCall(Proxy\ProxyRequestIF $request, $name, $params)
    {
        return $this->proxyRequestCall($request, $name, $params);
    }

    final private function proxyRequestCallAll($request, $name, $params)
    {
        // RunAllの実行
        $runList = $params[0];
        $this->debug('FACADE',[
            'Run %s %s',
            implode(',', array_keys($runList)),
            get_class($this)
        ]);

        $result = new Proxy\ProxyResult( );

        $values = [];
        $error = [];
        foreach($runList as $k=>$v) {
            if (is_numeric($k)) {
                $k = $v;
                $v = [];
            }
            if (is_string($v)) {
                $v = [$v];
            }
            $part = $this->__proxyRequestCall(clone $request, $k, $v);
            $values[$k] = $part->retrive();
            if ($part->isError()) {
                $error[$k] = $part->getMessage();
            }
        }

        if (!empty($error)) {
            $result->setError($error);
        }

        $result->set($values);
        return $result;
    }

    final private function proxyRequestHelp($handler)
    {
        $this->debug('FACADE','HELP...');
        $result = new Proxy\ProxyResult( );
        $text = Util::Help($handler);
        $this->debug("HELP", $text);
        $result->set($text);
        return $result;
    }

    final protected function proxyRequestCall(Proxy\ProxyRequestIF $request, $name, $params)
    {
        if ($name == 'runAll') {
            return $this->proxyRequestCallAll($request, $name, $params);
        }

        $result = new Proxy\ProxyResult( );
        $handler = $this->selectProxyHandler($request, $name, $params);

        // Help
        if ($name == 'help') {
            return $this->proxyRequestHelp($handler);
        }

        if (!is_callable([$handler, $name])) {
            $log = $this->crit('FACADE', [
                'Not Callable >>>> %02$s->%01$s <<<<', $name, get_class($this)
            ]);
            return $result->setError($log);
        }

        $this->debug("FACADE",[
            'Execute >>> %02$s->%01$s <<<<',
            $name, get_class($this)
        ]);

        $return = call_user_func_array([$handler, $name], $params);
        if ($return === $handler) {
            // モジュールを抜かれてしまった可能性がある
            $request->restore();

            // 自分を返す系のメソッドをオーバーライド
            $return = clone $request;
            $return->restore();
        }
        $result->set($return);
        return $result;
    }

    protected function selectProxyHandler($request, $name, $params)
    {
        return $this;
    }
}
