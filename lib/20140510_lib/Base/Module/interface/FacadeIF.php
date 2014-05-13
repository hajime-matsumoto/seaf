<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Module;

use Seaf\Base\Command;
/**
 * 
 */
interface FacadeIF
{
    public function execute (Command\RequestIF $request, $from = null);
}
