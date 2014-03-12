<?php
namespace Seaf\Core\Pattern;;

use Seaf\Kernel\Kernel;
use Seaf\Core\Exception;

trait ExtendableMethods {

    protected $maps = array();

    public function map ($name, $action = false)
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) {
                $this->map($k, $v);
            }
            return $this;
        }

        if (is_string($action) && !is_callable($action)) {
            $action = array($this,$action);
        }

        $this->maps[$name] = $action;
        return $this;
    }

    public function isMaped ($name)
    {
        return isset($this->maps[$name]);
    }

    public function call ($name, $params)
    {
        if ($this->isMaped($name)) {
            $action = $this->maps[$name];
            return Kernel::dispatch($action, $params, $this);
        }
        throw new Exception(array('%sの%sは呼べません',get_class($this),$name));
    }
}
