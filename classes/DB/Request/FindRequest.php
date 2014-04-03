<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\Request;

use Seaf\DB;

class FindRequest extends DB\Request
{
    protected $type = 'FIND';

    private $limit = -1;
    private $offset = -1;
    private $where = [];
    private $orders = [];
    private $fields = [];

    public function getLimit ( )
    {
        return $this->limit;
    }

    public function getOffset ( )
    {
        return $this->offset;
    }

    public function getWhere ( )
    {
        return $this->where;
    }

    public function getOrder ( )
    {
        return $this->orders;
    }

    public function getFields ( )
    {
        return $this->fields;
    }

    public function limit ($limit) 
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset ($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function where ($array)
    {
        $this->where = $array;
        return $this;
    }

    public function order ($key, $desc = false)
    {
        $this->orders[$key] = $desc ? 'desc': 'asc';
        return $this;
    }

    public function field ($field)
    {
        $this->fields[] = $field;
        return $this;
    }
}
