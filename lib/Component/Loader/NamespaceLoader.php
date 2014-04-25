<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Component\Loader;

use Seaf\Wrapper;

class NamespaceLoader implements LoaderIF
{
    private $ns, $prefix, $suffix;

    public function __construct ($ns, $prefix = '', $suffix = '')
    {
        $this->ns = $ns;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function create ($name, $params = [])
    {
        if (class_exists($class = $this->ns.'\\'.$name.$this->suffix)) {
            return Wrapper\ReflectionClass::factory($class)->newInstanceArgs($params);
        }
        return false;
    }
}
