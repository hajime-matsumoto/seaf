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
class DeclearRequest extends Command\Request
{
    private $args;

    public function __construct ( )
    {
        parent::__construct();
        $this->args = $this->data->dict('args');
    }

    public function data() 
    {
        return $this->data;
    }

    public function name($name)
    {
        $this->args->set('name',$name);
        return $this;
    }

    public function field($name, $type, $size = -1)
    {
        $this->args->dict('fields')->set($name,
            [
                'name' => $name,
                'type' => $type,
                'size' => $size
            ]
        );

        return $this;
    }

    public function index ($name)
    {
        $this->args->add(
            'indexes', [
                'name' => $name
            ]
        );
        return $this;
    }

    public function primary_index ($name)
    {
        $this->args->set('primary_index', $name);
        return $this;
    }

    public function option ($name, $value)
    {
        $this->args->dict('options')->set($name, $value);
        return $this;
    }

    public function __call($name, $params)
    {
        throw new \Exception("InvalidMethod($name)");
    }

    public function create( )
    {
        $result = $this->_execute('createTable', [$this->data->get('args')]);
        if ($result->isError()) return $result;
        return $result->pop('returnValue');
    }

}
