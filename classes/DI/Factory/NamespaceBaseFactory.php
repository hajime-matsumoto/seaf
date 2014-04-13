<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DI\Factory;

use Seaf\DI;

class NamespaceBaseFactory extends DI\Factory
{
    private $ns;

    /**
     * コンストラクタ
     */
    public function __construct ($ns)
    {
        $this->ns = $ns;
    }

    public function has ($name)
    {
        $class = $this->ns.'\\'.ucfirst($name);
        if (class_exists($class)) {
            $this->set($name, $class);
            return true;
        }
        return false;
    }
}
