<?php
namespace Seaf\Kernel\Module;

use Seaf\Core\Pattern\ExtendableMethods;

class ReflectionClass
{
    public function __construct ( )
    {
    }

    public function __invoke( $class )
    {
        return new \ReflectionClass($class);
    }
}
