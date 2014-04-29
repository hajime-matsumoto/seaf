<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\Mysql;

class Cursor implements \Iterator
{
    private $handler;
    private $sql;
    private $result;

    public function __construct (MysqlHandler $handler, $sql)
    {
        $this->handler = $handler;
        $this->sql = $sql;
    }

    public function execute ( )
    {
        if ($this->result) {
            $this->result->data_seek(0);
        }else{
            $this->result = $this->handler->query($this->sql);
        }
        return $this->result;
    }

    // ----------------------------------
    // For Iterator
    // ----------------------------------

    /**
     * \Iterator::current
     */
    public function current ( )
    {
        return current($this->result);
    }

    /**
     * \Iterator::key
     */
    public function key ( )
    {
        return key($this->result);
    }

    /**
     * \Iterator::next
     */
    public function next ( )
    {
        return next($this->result);
    }

    /**
     * \Iterator::rewind
     */
    public function rewind ( )
    {
        $this->result = $this->execute();
    }

    /**
     * \Iterator::valid
     */
    public function valid ( )
    {
        return current($this->result);
    }

}
