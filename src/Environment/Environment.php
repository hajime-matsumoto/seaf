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

    protected $di;

    /**
     * __construct
     *
     * @param 
     * @return void
     */
    public function __construct ()
    {
        $this->di = new DI($this);

        $this->di->addComponentNamespace(__CLASS__);
    }

    /**
     * DIを取得
     */
    public function di ( )
    {
        return $this->di;
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
