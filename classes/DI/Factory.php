<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DI;

use Seaf\DI;
use Seaf\Container\ArrayContainer;

class Factory extends ArrayContainer
{
    public function register ($name, $define)
    {
        $this->set($name, $define);
    }

    public function create ($name, $cfg = [])
    {
        $define = $this->get($name);
        if (is_string($define)) {
            if (method_exists($this, $method = 'create'.$define)) {
                return call_user_func([$this,$method], $cfg);
            }
            return new $define($cfg);
        }elseif($define instanceof \Closure){
            return call_user_func($define, $cfg);
        }elseif(is_callable($define)) {
            return call_user_func($define, $cfg);
        }
    }
}
