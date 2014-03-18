<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\DI;

use Seaf\Helper\ArrayHelper;

class Definition extends ArrayHelper
{
    private $definition;
    private $opts = array();
    private $callback;

    public static function factory ($config)
    {
        $def = new self();

        foreach($config as $k=>$v)
        {
            $def->$k = $v;
        }

        return $def;
    }

    public function create ( )
    {
        if (is_callable($this->definition)) {

            $instance = call_user_func_array($this->definition, (array)$this->opts);
        } elseif (is_string($this->definition)) {

            $class = $this->definition;
            $rc = new \ReflectionClass($class);
            $instance = $rc->newInstanceArgs((array)$this->opts);
        }

        if (is_callable($this->callback)) {
            $result = call_user_func_array($this->callback, $instance);
            if (is_object($result)) {
                $instance = $result;
            }
        }
        return $instance;
    }
}
