<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\MongoDB;

use Seaf\Base;
use Seaf\Data;

class FindQuery implements \Iterator
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    public $table;
    public $query;
    public $limit;
    public $offset;
    public $sort;
    private $cur;

    public function __construct ($table, $query)
    {
        $this->table = $table;
        $this->query = $query;
    }

    private function execute ( )
    {
        return $this->table->realFind($this);
    }

    public function sort ($fields)
    {
        $this->sort = $fields;
        return $this;
    }

    public function limit ($num)
    {
        $this->limit = $num;
        return $this;
    }

    public function offset ($num)
    {
        $this->offset = $num;
        return $this;
    }
    // ----------------------------------
    // For Iterator
    // ----------------------------------

    /**
     * \Iterator::current
     */
    public function current ( )
    {
        return $this->cur->current();
    }

    /**
     * \Iterator::key
     */
    public function key ( )
    {
        return $this->cur->key();
    }

    /**
     * \Iterator::next
     */
    public function next ( )
    {
        return $this->cur->next();
    }

    /**
     * \Iterator::rewind
     */
    public function rewind ( )
    {
        if (!$this->cur) {
            $this->cur = $this->execute();
        }
        return $this->cur->rewind();
    }

    /**
     * \Iterator::valid
     */
    public function valid ( )
    {
        return $this->cur->valid();
    }

}
