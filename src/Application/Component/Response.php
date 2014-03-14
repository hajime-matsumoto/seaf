<?php
namespace Seaf\Application\Component;

use Seaf\Application\StatusCode;
use Seaf\Kernel\Kernel;

/**
 * Response クラス
 */
class Response
{
    public $status  = 200;
    public $params  = array();
    public $body    = '';
    public $headers = array();

    /**
     * __invoke
     *
     * @param 
     */
    public function __invoke ()
    {
        return $this;
    }

    public function write ($body) 
    {
        $this->body.=$body;
        return $this;
    }

    public function param ($name, $value)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->param($k, $v);
            return $this;
        }
        $this->param[$name] = $value;
        return $this;
    }

    public function getParam($name)
    {
        return $this->param[$name];
    }

    public function header ($name, $value)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->header($k, $v);
            return $this;
        }
        $this->headers[$name] = $value;
        return $this;
    }

    public function status ($code)
    {
        $this->status = $code;
        return $this;
    }

    public function init()
    {
        $this->status = 200;
        $this->body = '';
        $this->headers = array();
        $this->params = array();

        return $this;
    }

    public function __toString( )
    {
        $array = array(
            'statuc'  => $this->status,
            'headers' => $this->headers,
            'params'  => $this->params,
            'body'    => $this->body
        );

        return json_encode($array);
    }

    /** **/

    public function send ( ) {
        $this->sendHeaders();
        Kernel::System()->halt($this->body);
    }

    /**
     * ヘッダを送信する
     */
    public function sendHeaders () {
        $SERVER = Kernel::globals()->get('SERVER_PROTOCOL');

        if (headers_sent()) {
            return false;
        }

        // Send status code header
        if (strpos(php_sapi_name(), 'cgi') !== false) {
            Kernel::system()->header(
                sprintf(
                    'Status: %d %s',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true
            );
        } else {
            Kernel::system()->header(
                sprintf(
                    '%s %d %s',
                    (isset($SERVER['SERVER_PROTOCOL']) ? $SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                    $this->status,
                    StatusCode::$codes[$this->status]),
                true,
                $this->status
            );
        }


        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    Kernel::system()->header($field.': '.$v, false);
                }
            } else {
                Kernel::system()->header($field.': '.$value);
            }
        }

        return $this;
    }
}
