<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * モジュール
 */
namespace Seaf\Base\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;

/**
 * モジュールメディエータ
 */
trait ModuleMediatorTrait
    {
        use ModuleContainerTrait;
        use ModuleFacadeTrait;

        public function __proxyRequestGet(Proxy\ProxyRequestIF $req, $name)
        {
            $key = $this->getObjectName();

            if ($req->hasParam($key)) {
                if($module = $this->loadModule($req->getParam($key))) {
                    return $module->__proxyRequestGet($req, $name);
                }
                throw new \Exception('INVALID REQUEST GET'.$name.'|'.$this->getObjectName());
            }

            $newReq = clone $req;
            $newReq->setParam($key, $name);
            return $newReq;
        }

        public function __proxyRequestCall(Proxy\ProxyRequestIF $req, $name, $params)
        {
            // $this->debug("Recive $name");

            $key = $this->getObjectName();

            if ($req->hasParam($key)) {
                if($module = $this->loadModule($req->getParam($key))) {
                    return $module->__proxyRequestCall($req, $name, $params);
                }
                throw new \Exception('INVALID REQUEST CALL'.$name.'|'.$this->getObjectName());
            }

            return $this->proxyRequestCall($req, $name, $params);
        }

    }
