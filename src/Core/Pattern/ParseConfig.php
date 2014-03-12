<?php
namespace Seaf\Core\Pattern;

/**
 * Config Parse Pattern
 */
trait ParseConfig
{
    /**
     * parseConfig
     *
     * @param $config
     * @return void
     */
    protected function parseConfig ($config)
    {
        foreach ($config as $k=>$v) {
            if (method_exists($this, $method = 'set'.ucfirst($k))) {
                $this->$method($v);
            }else{
                $this->$k = $v;
            }
        }
    }

}
