<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

interface KVSComponentIF 
{
    public function get ($table, $key, &$status = null);
    public function set ($table, $key, $status);
    public function has ($table, $key);
    public function del ($table, $key);
}
