<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Config;

use Seaf\Util\Util;
use Seaf\Base\Types;

/**
 * 
 */
class ConfigContainer extends Types\Dictionary
{
    public function __construct ($config = [])
    {
        parent::__construct($config);
    }

    public function has ($name)
    {
        if (false === strpos($name, '.')) {
            return parent::has($name);
        }
        $token = strtok($name, '.');
        $head = $this->data;
        do {
            if (!isset($head[$token])) return false;
            $head =& $head[$token];

        } while($token = strtok('.'));
        return true;
    }

    public function get($name, $default = null)
    {
        if (false === strpos($name, '.')) {
            return parent::get($name);
        }
        $token = strtok($name, '.');
        $head = $this->data;
        do {
            if (!isset($head[$token])) return $default;
            $head =& $head[$token];

        } while($token = strtok('.'));
        return $head;
    }
}
