<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\App\Component;

use Seaf\Core\Environment;
use Seaf\Core\Kernel;

/**
 * アプリケーションレスポンス
 * ===========================
 */
class ResponseComponent
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * @var array
     */
    private $headers = array();

    /**
     * @var array
     */
    private $status = 200;

    /**
     * @var Environment
     */
    private $env;

    public function initComponent (Environment $env) 
    {
        $this->env = $env;
    }

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


    /** 初期処理 **/

    public function __construct( )
    {
        $this->init( );
    }

    /**
     * 初期化
     */
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

    public function getParams()
    {
        return $this->params;
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


    /**
     * ヘッダを送信する
     */
    public function sendHeaders () {
        $SERVER = Kernel::rg()->get('SERVER_PROTOCOL');

        // Send status code header
        if (strpos(php_sapi_name(), 'cgi') !== false) {
            Kernel::header(
                sprintf(
                    'Status: %d %s',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true
            );
        } else {
            Kernel::header(
                sprintf(
                    '%s %d %s',
                    (isset($SERVER['SERVER_PROTOCOL']) ? $SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                    $this->status,
                    self::$codes[$this->status]),
                true,
                $this->status
            );
        }


        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    Kernel::header($field.': '.$v, false);
                }
            } else {
                Kernel::header($field.': '.$value);
            }
        }

        return $this;
    }

    /**
     * Sets caching headers for the response.
     *
     * @param int|string $expires Expiration time
     * @return object Self reference
     */
    public function cache($expires) {
        if ($expires === false) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            $this->headers['Pragma'] = 'no-cache';
            $this->headers['X-Accel-Expires'] = 0;
        }
        else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->headers['Cache-Control'] = 'max-age='.($expires - time());
            $this->headers['Pragma'] = 'cache';
            $this->headers['X-Accel-Expires'] = $expires;
        }

        return $this;
    }

    public function send ( )
    {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            $this->sendHeaders();
        }

        Kernel::system()->halt($this->body);
    }

    public function sendJson( )
    {
        $json = $this->toJson( );

        $this->init();
        $this->header('Content-Type', 'application/json')->write($json)->send();
    }

}
