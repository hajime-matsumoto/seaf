<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf\Base;

class File
{
    use Base\SeafAccessTrait;

    public function componentHelper ($name = null) 
    {
        if (func_num_args() == 0) return $this;
        return call_user_func_array([$this, '_componentHelper'], func_get_args());
    }

    public function _componentHelper ( )
    {
        return call_user_func_array(
            [$this->sf( )->FileSystem(), 'file'],
            func_get_args()
        );
    }
}
