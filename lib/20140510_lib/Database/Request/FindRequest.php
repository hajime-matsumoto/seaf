<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\Request;

use Seaf\Base\Command;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * テーブル定義リクエスト
 */
class FindRequest extends Command\Request implements \IteratorAggregate
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    private $args;

    public function __construct ( )
    {
        parent::__construct();

        $this->args = $this->data->dict('args');
    }

    public function field ($field)
    {
        $this->args->add('fields', $field);
        return $this;
    }

    public function query ($query)
    {
        $this->args->set('query', $query);
        return $this;
    }

    public function sort ($fields)
    {
        $this->args->set('sort', $fields);
        return $this;
    }

    public function limit ($num)
    {
        $this->args->set('limit', intval($num));
        return $this;
    }

    public function offset ($num)
    {
        $this->args->set('offset', intval($num));
        return $this;
    }

    public function __call($name, $params)
    {
        throw new \Exception("InvalidMethod($name)");
    }

    public function fetch( )
    {
        return $this->getCursor()->fetch();
        $result = $this->_execute('createTable', [$this->data->get('args')]);
        if ($result->isError()) return $result;
        return $result->pop('returnValue');
    }

    public function getCursor( )
    {
        if (!isset($this->cur)) {
            $result = $this->_execute('findTable', [$this->data->get('args')]);
            $this->cur = $result->pop('returnValue');
        }
        return $this->cur;
    }

    public function getIterator()
    {
        return $this->getCursor();
    }

}
