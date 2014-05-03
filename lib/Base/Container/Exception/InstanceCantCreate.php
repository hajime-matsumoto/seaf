<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container\Exception;

use Seaf\Base;

/**
 * 配列コンテナ
 */
class InstanceCantCreate extends Base\Exception\Exception
{
    public function __construct($name, $from)
    {
        $message = sprintf('%s From %s', $name, get_class($from));
        parent::__construct($message);
    }
}
