<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\Request;

use Seaf\BackEnd\Module;

/**
 * テーブル検索リクエスト
 */
class FindRequest extends Module\ProxyRequest implements \IteratorAggregate
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    private $args;
    private $cur;

    public function args() 
    {
        if (!$this->args) {
            return $this->params()->dict('args');
        }
    }

    public function field ($field)
    {
        $this->args()->add('fields', $field);
        return $this;
    }

    public function query ($query)
    {
        $this->args()->set('query', $query);
        return $this;
    }

    public function sort ($fields)
    {
        $this->args()->set('sort', $fields);
        return $this;
    }

    public function limit ($num)
    {
        $this->args()->set('limit', intval($num));
        return $this;
    }

    public function offset ($num)
    {
        $this->args()->set('offset', intval($num));
        return $this;
    }

    public function __call($name, $params)
    {
        $cursor = $this->getCursor();
        if (is_callable([$cursor,$name])) {
            return call_user_func_array([$cursor, $name], $params);
        }
        throw new \Exception('Invalid '.$name);
    }

    public function getCursor( )
    {
        if (!isset($this->cur)) {
            $result = $this->executeProxyRequestCall(
                'findTable',
                [$this->params()->get('args')]
            );
            $this->cur = $result->retrive();
        }
        return $this->cur;
    }

    public function getIterator()
    {
        return $this->getCursor();
    }

}
