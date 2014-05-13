<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * モジュール
 */
namespace Seaf\Base\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * モジュールファサード
 */
trait ModuleFacadeTrait
    {
        use ModuleTrait;

        public function makeRequest ($name = null)
        {
            $req = new ProxyRequest($this);
            if ($name) {
                return $req->$name;
            }
            return $req;
        }

        public function __proxyRequestGet(Proxy\ProxyRequestIF $req, $name)
        {
            return $this->proxyRequestGet($req, $name);
        }

        private function proxyRequestGet(Proxy\ProxyRequestIF $req, $name)
        {
            throw new \Exception('INVALID REQUEST GET '.$name.'|'.$this->getObjectName());
        }

        public function __proxyRequestCall(Proxy\ProxyRequestIF $req, $name, $params)
        {
            return $this->proxyRequestCall($req, $name, $params);
        }

        final private function proxyRequestCallAll($request, $name, $params)
        {
            // RunAllの実行

            $result = new Proxy\ProxyResult( );

            $values = [];
            $error = [];
            $realList = [];

            if (count($params) == 1) {
                $runList = $params[0];
                foreach($runList as $k=>$v) {
                    if (is_numeric($k)) {
                        $k = $v;
                        $v = [];
                    }
                    if (is_string($v)) {
                        $v = [$v];
                    }
                    $realList[] = [$name,$v];
                }
            }else{
                $runName = $params[0];
                $runList = $params[1];
                foreach ($runList as $arg)
                {
                    $realList[] = [$runName, $arg];
                }
            }


            foreach ($realList as $v)
            {
                $part = $this->__proxyRequestCall(clone $request, $v[0], $v[1]);
                $values[$v[0]] = $part->retrive();
                if ($part->isError()) {
                    $error[$v[0]] = $part->getMessage();
                }
            }

            if (!empty($error)) {
                $result->setError($error);
            }

            $result->set($values);
            return $result;
        }

        protected function proxyRequestCall($req, $name, $params)
        {
            if ($name == 'runAll') {
                return $this->proxyRequestCallAll($req, $name, $params);
            }

            $handler = $this->selectProxyHandler($req, $name);

            if ($name == 'help') {
                $result = new Proxy\ProxyResult();
                if (!isset($params[0]) || $params[0] == false) {
                    echo Util::Help($handler);
                }
                $result->set(Util::Help($handler));
                return $result;
            }

            $this->debug(['Execute >>> %s <<<', $name]);

            $result = new Proxy\ProxyResult();
            if (!is_callable([$handler, $name])) {
                $log = $this->crit(['Cant Execute %s', $name]);
                throw new \Exception((string)$log);
            }

            $return = call_user_func_array([$handler, $name], $params);
            $result->set($return === $handler ? $handler: $return);
            return $result;

        }

        protected function selectProxyHandler($req, $name)
        {
            return $this;
        }
    }
