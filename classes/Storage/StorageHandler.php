<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Storage;

use Seaf\Base;

class StorageHandler
{
    use Base\SeafAccessTrait;

    private $storage;
    private $table;
    private $type;

    public function __construct (Storage $storage, $table = '', $type = '')
    {
        $this->storage = $storage;
        $this->table  = $table;
        $this->type    = $type;
    }

    public function getType( )
    {
        return $this->type;
    }

    public function has ($key)
    {
        return $this->storage->has($key, $this->table, $this->type);
    }

    public function put ($key, $value, $status = [])
    {
        $this->storage->put($key, $value, $status, $this->table, $this->type);
    }

    public function stat ($key)
    {
        return $this->storage->stat($key, $this->table, $this->type);
    }

    public function get ($key, &$stat = null)
    {
        return $this->storage->get($key, $stat, $this->table, $this->type);
    }
}
