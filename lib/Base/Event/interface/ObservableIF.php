<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

/**
 * Eventオブザーブできるオブジェクト 
 */
interface ObservableIF extends ObserverIF
{
    public function addObserver(ObserverIF $observer);
    public function fireEvent($type, $args = []);
    public function once($type, callable $callback);
    public function on($type, callable $callback);
    public function off($type, callable $callback);
}
