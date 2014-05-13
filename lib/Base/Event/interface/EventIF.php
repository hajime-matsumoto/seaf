<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

/**
 * Eventオブザーブできるオブジェクト 
 */
interface EventIF
{
    public function stop();
    public function isStoped();
    public function getType();
    public function getSource();
}
