<?php
namespace Seaf\Environment;

use Seaf\Kernel\Kernel;
use Seaf\Pattern\DynamicMethod;
use Seaf\Data\Container\Base;

/**
 * Environmentクラス
 */
class Environment extends Base
{
    use DynamicMethod;

    /**
     * __construct
     *
     * @param 
     * @return void
     */
    public function __construct ()
    {
        $this->di = new DI($this);
    }

    /**
     * callFallBack
     *
     * @param $name, $params
     * @return void
     */
    public function callFallBack ($name, $params)
    {
        return $this->di->call($name, $params);
    }

    public function call ($name, $params)
    {
        $result = $this->__call($name, $params);
        //var_dump($result);
        return $result;
    }
}
