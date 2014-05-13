<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\View;

use Seaf\Util\Util;
use Seaf\Base\Types;
use Seaf\Util\ContainerUserTrait;
use Seaf\Util\MethodContainer;
use Seaf\Base\Proxy;
use Seaf\BackEnd;

use Seaf\Base\ExtendableMethodTrait;

/**
 * ViewModel型
 */
class ViewModel extends Types\Dictionary
{
    use ExtendableMethodTrait;

    protected function __callWhenMethodNotExists($name, $params)
    {
        return new \Exception(['Invalid call %s', $name]);
    }
}
