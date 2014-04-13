<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DI\Factory;

use Seaf\DI;

class MethodBaseFactory extends DI\Factory
{
    /**
     * コンストラクタ
     */
    public function __construct ($object)
    {
        foreach (get_class_methods($object) as $method)
        {
            if (strpos($method, 'init') === 0) {
                $this->register(substr($method,4), [$object, $method]);
            }
        }
    }
}
