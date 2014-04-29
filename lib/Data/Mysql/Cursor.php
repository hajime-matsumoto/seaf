<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\Mysql;

class Cursor implements \Iterator
{
    private $handler;
    private $sql;
    private $result;

    private $datas;
    private $idx;

    public function __construct (MysqlHandler $handler, $sql)
    {
        $this->handler = $handler;
        $this->sql = $sql;
        $this->result = $handler->query($sql);
    }


    public function fetch ( )
    {
        return $this->result->fetch_assoc();
    }

    public function getNext ( )
    {
        return $this->fetch();
    }

    // ----------------------------------
    // For Iterator
    // ----------------------------------

    /**
     * \Iterator::current
     */
    public function current ( )
    {
        return $this->current;
    }

    /**
     * \Iterator::key
     */
    public function key ( )
    {
        return $this->idx = 0;
    }

    /**
     * \Iterator::next
     */
    public function next ( )
    {
        $this->idx++;
    }

    /**
     * \Iterator::rewind
     */
    public function rewind ( )
    {
        $this->idx = 0;
    }

    /**
     * \Iterator::valid
     */
    public function valid ( )
    {
        $this->current = $this->fetch( );
        return $this->current ? true: false;
    }

}
