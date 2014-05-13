<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * Util
 */
namespace Seaf\Util;

/**
 * コンテナユーザ
 */
trait ContainerUserTrait
{
    private $container;

    public function container ( )
    {
        if (!$this->container) {
            $this->container = Util::Dictionary();
        }
        return $this->container;
    }
}
