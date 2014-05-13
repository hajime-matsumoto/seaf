<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\DI\Factory;


/**
 * 
 */
interface FactoryIF
{
    public function setNext(FactoryIF $factory);
    public function getNext( );
    /**
     * クラスを登録する
     *
     * @param string
     * @param array $args = []
     * @param array $option = []
     */
    public function register($name, $args = [], $option = []);

    /**
     * @return bool
     */
    public function canCreate($name);


    /**
     * @param string
     * @return mixed
     */
    public function newInstance($name);

    /**
     * @param string
     * @param array
     * @return mixed
     */
    public function newInstanceArgs($name, $args);
}
