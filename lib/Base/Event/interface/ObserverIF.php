<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

/**
 * Eventオブザーブできるオブジェクト 
 */
interface ObserverIF
{
    public function notify(EventIF $event);
}
