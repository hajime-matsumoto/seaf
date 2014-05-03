<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

use Seaf\Base\Singleton;

interface ObserverIF
{
    public function notify (Event $e);
}
