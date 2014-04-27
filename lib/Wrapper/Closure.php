<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

class Closure
{
    public static function create ($data)
    {
        return new Closure($data);
    }

    public function __invoke( )
    {
        return call_user_func_array($data,array_slice(func_get_args(),1));
    }
}
