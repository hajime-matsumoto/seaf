<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\FrameWork\Component;

use Seaf;

/**
 * レスポンスクラス
 */
class Response
{
    /**
     * @var array HTTP status codes 
     */
    public static $codes = array(  // {{{
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ); // }}}


    protected $status  = 200;
    protected $headers = array();
    protected $body    = '';
    protected $params  = array();

    /** 初期処理 **/

    public function __construct( )
    {
        $this->init( );
    }

    public function init()
    {
        $this->status  = 200;
        $this->headers = array();
        $this->body    = '';
        $this->params  = array();
    }

    /** データ設定 **/

    public function _status( $code )
    {
        $this->status = $code;
    }

    public function _write ($body) {
        $this->body .= $body;
    }

    public function _header ($name, $value=null) {

        if ( is_array($name) ) {
            foreach ($name as $k => $v) $this->_header($k,$v);
            return;
        }
        $this->headers[$name] = $value;
    }

    public function _param ($name, $value = false)
    {
        if ( is_array($name) ) {
            foreach ($name as $k => $v) $this->_param($k,$v);
            return;
        }
        $this->params[$name] = $value;
    }

    /** 変換系 **/

    public function toArray ( )
    {
        if (ob_get_length() > 0) {
            $this->_write(ob_get_clean());
        }

        return array(
            'status' => $this->status,
            'headers' => $this->headers,
            'params' => $this->params,
            'body' => $this->body
        );
    }

    public function toJson ( ) {
        $array = $this->toArray();
        return json_encode ($array);
    }

    public function getBodyClean( )
    {
        $body = $this->body;
        $this->body = '';
        return $body;
    }

    /** 出力系  **/

    /** システム  **/
    public function __call ( $name, $params )
    {
        if ( method_exists($this,'_'.$name) ) {
            call_user_func_array( array($this,'_'.$name), $params );
            return $this;
        }

        throw new \Exception(get_class($this).'::'.$name.'メソッドが存在しません。');
    }
}
