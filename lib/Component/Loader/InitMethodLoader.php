<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Component\Loader;

use Seaf\Wrapper;

class InitMethodLoader implements LoaderIF
{
    private $object;

    public function __construct ($object)
    {
        $this->object = $object;
    }

    public function create ($name, $params = [])
    {
        if (method_exists($this->object, $method = 'init'.$name)) {
            return call_user_func_array([$this->object, $method], $params);
        }
        return false;
    }
}
