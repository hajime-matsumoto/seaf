<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;

use Seaf\Base;
use Seaf\Core\Seaf;
use Seaf\Response\StatusCode;

class Response extends \Seaf\Response\Response
{
    use Base\SeafAccessTrait;

    protected $headers = [];

    /**
     * @return Response
     */
    public function clear( )
    {
        parent::clear( );
        $this->headers = [];
        return $this;
    }

    /**
     * レスポンスヘッダーを追加する
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function header ($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->header($k, $v);
            return $this;
        }
        $this->headers[$name] = $value;
        return $this;
    }


    //---------------------------------------
    // 送信系
    //---------------------------------------

    /**
     * 送信する
     */
    public function send ( ) {
        $this->sendHeaders();
        Seaf::system()->halt($this->body);
    }

    /**
     * ヘッダを送信する
     */
    public function sendHeaders () {
        if (headers_sent()) {
            return false;
        }

        // Send status code header
        Seaf::system()->header(
            sprintf(
                '%s %d %s',
                Seaf::Globals('SERVER.SERVER_PROTOCOL', 'HTTP/1.1'),
                $this->status,
                StatusCode::$codes[$this->status]),
            true,
            $this->status
        );


        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    Seaf::system()->header($field.': '.$v, false);
                }
            } else {
                Seaf::system()->header($field.': '.$value);
            }
        }

        return $this;
    }

    /**
     * Jsonを送信する
     */
    public function sendJson( )
    {
        $json = json_encode($this->toArray( ));
        $this->clear();
        $this->header('Content-Type', 'application/json')->write($json)->send();
    }
}
