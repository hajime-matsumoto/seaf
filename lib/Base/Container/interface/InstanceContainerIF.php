<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

use Seaf\Base\Factory;

/**
 * インスタンスコンテナIF
 */
interface InstanceContainerIF extends ContainerIF
{
    /**
     * ファクトリを追加する
     *
     * @param Factory\FactoryIF
     */
    public function addFactory(Factory\FactoryIF $factory);

    /**
     * インスタンスを登録する
     *
     * @param string
     * @param object
     */
    public function setInstance($name, $instance);


    /**
     * インスタンスを取得する
     *
     * @param Factory\FactoryIF
     */
    public function getInstance($name);

    /**
     * インスタンスを取得する(実体)
     *
     * @param Factory\FactoryIF
     * @param array
     */
    public function getInstanceArgs($name, array $args);

    /**
     * インスタンスを作成する(実体)
     *
     * @param Factory\FactoryIF
     * @param array
     */
    public function newInstanceArgs($name, array $args);

    /**
     * インスタンスが存在するか？
     *
     * @param string
     * @param bool $useFactory=true ファクトリも検索対象にする
     * @param Factory\Factory 見つかったファクトリを返却する
     */
    public function hasInstance($name, $useFactory = true, &$factory = null);
}
