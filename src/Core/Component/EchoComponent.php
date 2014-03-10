<?php

namespace Seaf\Core\Component;

/**
 * テスト用
 */
class EchoComponent 
{
    /**
     * __construct
     *
     * @param 
     */
    public function __construct ()
    {
    }

    /**
     * say
     *
     * @param 
     * @return void
     */
    public function say ()
    {
        var_dump(func_get_args());
    }
}
