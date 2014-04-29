<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\Web;
use Seaf\Com\Result\Result as Base;
use Seaf\Com\Result\StatusCode;
use Seaf\Wrapper;

class Result extends Base implements ComponentIF
{
    /**
     * @var Web\WebController
     */
    private $Controller;

    /**
     * ヘッダー
     */
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
     * WebControllerをセットアップする
     */
    public function setupWebComponent(Web\WebController $Ctrl)
    {
        $this->Controller = $Ctrl;
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
        Wrapper\PHPFunction::getSingleton( )->exit($this->body);
    }

    /**
     * ヘッダを送信する
     */
    public function sendHeaders () {
        if (headers_sent()) {
            return false;
        }
        $php = Wrapper\PHPFunction::getSingleton( );
        $g   = Wrapper\SuperGlobalVars::getSingleton();

        // Send status code header
        $php->header(
            sprintf(
                '%s %d %s',
                $g('_SERVER.SERVER_PROTOCOL', 'HTTP/1.1'),
                $this->status,
                StatusCode::$codes[$this->status]),
            true,
            $this->status
        );


        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $php->header($field.': '.$v, false);
                }
            } else {
                $php->header($field.': '.$value);
            }
        }

        return $this;
    }

    /**
     * Jsonを送信する
     */
    public function sendJson( )
    {
        $php = Wrapper\PHPFunction::getSingleton( );
        $json = $php->json_encode($this->toArray( ));

        $this->clear();
        $this->header('Content-Type', 'application/json')->write($json)->send();
    }
}
