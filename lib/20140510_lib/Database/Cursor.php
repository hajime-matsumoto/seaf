<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Util\Util;

/**
 * Mysql:Curor
 */
abstract class Cursor implements ResultIF,\Iterator
{
    private $result;
    private $fetch_mode = 'assoc';

    public function __construct ($result)
    {
        $this->result = $result;
    }

    /**
     * エラー判定
     */
    public function isError( )
    {
        return $this->result === false;
    }

    /**
     *
     */
    abstract public function fetchAssoc ( );
    abstract public function fetchNum ( );

    /**
     * フェッチモードの指定
     */
    public function setFetchMode($mode)
    {
        if (!in_array($mode, ['assoc','num','both'])) {
            throw new \Exception(sprintf("Invalid FetchMode %s",$mode));
        }
        $this->fetch_mode = $mode;
    }

    /**
     * フェッチ
     */
    public function fetch ( )
    {
        switch ($this->fetch_mode) {
        case 'num':
            return $this->fetchNum();
            break;
        case 'assoc':
        default:
            return $this->fetchAssoc();
            break;
        }
    }

    /**
     * フェッチ
     */
    public function fetchAll ( )
    {
        $rows = array();
        while($row = $this->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function dump ( )
    {
        Util::dump(iterator_to_array($this));
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
