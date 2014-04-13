<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

class CacheHandler
{
    private $cache;
    private $name;

    public function __construct ($name, Cache $cache)
    {
        $this->cache = $cache;
        $this->name = $name;
    }

    public function has ($name, $unless = 0)
    {
        return $this->cache->has($this->getName($name), $unless);
    }

    public function create ($name, $data, $expires = 0)
    {
        return $this->cache->create($this->getName($name), $data, $expires);
    }

    public function get ($name, &$stat = null)
    {
        return $this->cache->get($this->getName($name), $stat);
    }

    protected function getName($name)
    {
        return $this->name.'_'.$name;
    }
}
