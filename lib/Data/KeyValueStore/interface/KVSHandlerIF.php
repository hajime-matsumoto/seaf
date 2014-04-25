<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

interface KVSHandlerIF 
{
    public function get ($key, &$status = null);
    public function set ($key, $status);
    public function has ($key);
    public function del ($key);
}
