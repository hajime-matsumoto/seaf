<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\Request;

use Seaf\BackEnd\Module;

/**
 * テーブル定義リクエスト
 */
class DeclearRequest extends Module\ProxyRequest
{
    private $args;

    public function args() 
    {
        if (!$this->args) {
            return $this->params()->dict('args');
        }
    }

    public function name($name)
    {
        $this->args()->set('name',$name);
        return $this;
    }

    public function field($name, $type, $size = -1)
    {
        $this->args()->dict('fields')->set($name,
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
        $this->args()->append(
            'indexes', [
                'name' => $name
            ]
        );
        return $this;
    }

    public function primary_index ($name)
    {
        $this->args()->set('primary_index', $name);
        return $this;
    }

    public function option ($name, $value)
    {
        $this->args()->dict('options')->set($name, $value);
        return $this;
    }

    public function create ( )
    {
        $result = $this->executeProxyRequestCall(
            'createTable',
            [$this->args()->__toArray()]
        );

        return $result->retrive();
    }

    public function __call($name, $params)
    {
        throw new \Exception("InvalidMethod($name)");
    }
}
