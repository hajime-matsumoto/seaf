<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

/**
 * 
 */
use Seaf\Base\Factory;
use Seaf\Base\Event;

/**
 * インスタンスコンテナ
 */
class InstanceContainer implements Event\ObservableIF, InstanceContainerIF
{
    use InstanceContainerTrait;
    use Event\ObservableTrait;

    /**
     * コンストラクタ
     */
    public function __construct ($owner = null)
    {
        $this->initInstanceContainer();
    }

}
