<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\BackEnd;
use Seaf\Util\Util;
use Seaf\Web;
use Seaf\Base\DI;
use Seaf\Base\Event;


/**
 * WEBレスポンス管理
 */
class WebResponse implements Event\ObservableIF
{
    use Event\ObservableTrait;

    public $params = [];
    public $options = [];
    protected $status = StatusCode::OK;
    protected $body;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->clear();
    }

    public function clear( )
    {
        $this->body   = '';
        $this->params = [];
        $this->headers = [];
        $this->options = Util::Dictionary();
        return $this;
    }

    /**
     * ステータスコードをセットする
     *
     * @param string
     * @return Result
     */
    public function status($code)
    {
        $this->status = $code;
        return $this;
    }
    /**
     * 結果本文を書き込む
     *
     * @param string
     * @return Result
     */
    public function write($body)
    {
        $this->body .= $body;
        return $this;
    }

    /**
     * パラメタをセットする
     *
     * @param string
     * @param mixed
     * @return Request
     */
    public function param ($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->param($k, $v);
            return $this;
        }

        $this->params[$name] = $value;
        return $this;
    }

    public function option($name, $value)
    {
        $this->options->set($name, $value);
    }

    public function getOptions( )
    {
        return $this->options;
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
    // 取得系
    //---------------------------------------

    /**
     * レスポンスパラメタを取得する
     *
     * @return array()
     */
    public function getParams( )
    {
        return $this->params;
    }


    /**
     * レスポンスbodyを取得する
     *
     * @return string
     */
    public function getBody( )
    {
        return $this->body;
    }

    /**
     * レスポンスコードを取得する
     *
     * @return int
     */
    public function getStatus( )
    {
        return $this->status;
    }

    //---------------------------------------
    // 変換系
    //---------------------------------------

    /**
     * 文字列にする
     */
    public function toString( )
    {
        $array = $this->toArray();
        return json_encode($array);
    }

    /**
     * 配列にする
     */
    public function toArray( )
    {
        $array = array(
            'status'  => $this->status,
            'params'  => $this->getParams(),
            'body'    => $this->body
        );
        return $array;
    }

    /**
     * Jsonにする
     */
    public function toJson( )
    {
        $array = $this->toArray();
        return json_encode($array);
    }

    //---------------------------------------
    // 送信系
    //---------------------------------------

    /**
     * 送信する
     */
    public function send ( ) {
        $this->sendHeaders();
        BackEnd( )->phpFunction->exit($this->body);
    }

    /**
     * ヘッダを送信する
     */
    public function sendHeaders () {
        if (headers_sent()) {
            return false;
        }
        $php = BackEnd( )->phpFunction;
        $g   = BackEnd( )->system->SuperGlobals;

        // Send status code header
        $php->header(
            sprintf(
                '%s %d %s',
                $g->get('_SERVER.SERVER_PROTOCOL', 'HTTP/1.1'),
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
        $json = BackEnd( )->phpFunction->json_encode($this->toArray( ));

        $this->clear();
        $this->header('Content-Type', 'application/json')->write($json)->send();
    }
}
