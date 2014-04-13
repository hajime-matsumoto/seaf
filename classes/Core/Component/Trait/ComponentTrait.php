<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Core\Component;

trait ComponentTrait
    {
        public function componentHelper ($name = null) 
        {
            if (func_num_args() == 0 || !method_exists($this, '_componentHelper')) {
                return $this;
            }
            return call_user_func_array([$this, '_componentHelper'], func_get_args());
        }
    }
